<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ItineraryGeneratorService;

class TestMultiDayProgression extends Command
{
    protected $signature = 'test:multiday-progression';
    protected $description = 'Test multi-day distance and time progression';

    protected $itineraryService;

    public function __construct(ItineraryGeneratorService $itineraryService)
    {
        parent::__construct();
        $this->itineraryService = $itineraryService;
    }

    public function handle()
    {
        $this->info('🔍 MULTI-DAY PROGRESSION TEST');
        $this->info('============================');
        $this->newLine();

        // Create trail data - total 8.5km distance
        $trail = [
            'id' => 1,
            'name' => 'Ambangeg Trail',
            'difficulty' => 'moderate',
            'elevation_gain' => 1200,
            'distance_km' => 8.5, // Total distance to summit
            'estimated_duration_hours' => 24,
            'package' => [
                'duration' => '36 hours', // 2 days, 1 night
                'id' => 1,
                'name' => 'Weekend Adventure Package'
            ]
        ];

        $routeData = [
            'distance_km' => 8.5,
            'estimated_duration_minutes' => 480
        ];

        try {
            $itinerary = [];
            $build = $routeData;
            $result = $this->itineraryService->generateItinerary($itinerary, $trail, $build, []);

            $this->info("📏 DISTANCE & TIME ANALYSIS:");
            $this->info("Total Trail Distance: {$trail['distance_km']} km");
            $this->info("Duration: {$result['dateInfo']['duration_days']} days");
            $this->newLine();

            foreach ($result['dayActivities'] as $dayIndex => $dayActivities) {
                $this->info("=== DAY {$dayIndex} ===");
                
                // Find distance progression
                $distances = [];
                $times = [];
                foreach ($dayActivities as $activity) {
                    if (isset($activity['cum_distance_km']) && $activity['cum_distance_km'] > 0) {
                        $distances[] = $activity['cum_distance_km'];
                    }
                    if (isset($activity['minutes'])) {
                        $times[] = $activity['minutes'];
                    }
                }
                
                $maxDistance = !empty($distances) ? max($distances) : 0;
                $maxTime = !empty($times) ? max($times) : 0;
                $maxTimeStr = sprintf('%02d:%02d', intval($maxTime / 60), $maxTime % 60);
                
                $this->info("Max Distance Reached: {$maxDistance} km");
                $this->info("Max Time: {$maxTimeStr}");
                
                // Show key activities
                $this->info("Key Activities:");
                foreach ($dayActivities as $activity) {
                    if (str_contains($activity['title'], 'Start') || 
                        str_contains($activity['title'], 'Break Camp') ||
                        str_contains($activity['title'], 'Summit') ||
                        str_contains($activity['title'], 'Arrive') ||
                        str_contains($activity['title'], 'Final')) {
                        
                        $timeStr = sprintf('%02d:%02d', 
                            intval($activity['minutes'] / 60), 
                            $activity['minutes'] % 60
                        );
                        $distance = $activity['cum_distance_km'] ?? 0;
                        $this->info("  {$timeStr} - {$activity['title']} ({$distance} km)");
                    }
                }
                $this->newLine();
            }

            $this->info("🔍 ANALYSIS:");
            
            // Check if Day 2 starts where Day 1 ended
            $day1LastDistance = 0;
            $day1LastTime = 0;
            if (isset($result['dayActivities'][1])) {
                foreach ($result['dayActivities'][1] as $activity) {
                    if (isset($activity['cum_distance_km'])) {
                        $day1LastDistance = max($day1LastDistance, $activity['cum_distance_km']);
                    }
                    if (isset($activity['minutes'])) {
                        $day1LastTime = max($day1LastTime, $activity['minutes']);
                    }
                }
            }
            
            $day2StartDistance = 0;
            $day2StartTime = 0;
            if (isset($result['dayActivities'][2])) {
                $firstActivity = $result['dayActivities'][2][0] ?? null;
                if ($firstActivity) {
                    $day2StartDistance = $firstActivity['cum_distance_km'] ?? 0;
                    $day2StartTime = $firstActivity['minutes'] ?? 0;
                }
            }
            
            $this->info("Day 1 ends at: {$day1LastDistance} km, " . sprintf('%02d:%02d', intval($day1LastTime / 60), $day1LastTime % 60));
            $this->info("Day 2 starts at: {$day2StartDistance} km, " . sprintf('%02d:%02d', intval($day2StartTime / 60), $day2StartTime % 60));
            
            $continuityOK = ($day2StartDistance > 0 && $day2StartDistance >= $day1LastDistance * 0.8); // Allow some variance
            $this->info("✓ Day 2 continues from Day 1 progress: " . ($continuityOK ? "PASS" : "FAIL"));
            
            $this->newLine();
            $this->info('✨ Test complete!');

        } catch (\Exception $e) {
            $this->error("❌ Error: " . $e->getMessage());
        }
    }
}
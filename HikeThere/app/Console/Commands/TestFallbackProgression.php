<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ItineraryGeneratorService;

class TestFallbackProgression extends Command
{
    protected $signature = 'test:fallback-progression';
    protected $description = 'Test multi-day distance with fallback generation';

    protected $itineraryService;

    public function __construct(ItineraryGeneratorService $itineraryService)
    {
        parent::__construct();
        $this->itineraryService = $itineraryService;
    }

    public function handle()
    {
        $this->info('🔍 FALLBACK GENERATION TEST');
        $this->info('==========================');
        $this->newLine();

        // Create trail data
        $trail = [
            'id' => 1,
            'name' => 'Ambangeg Trail',
            'difficulty' => 'moderate',
            'distance_km' => 8.5,
            'package' => [
                'duration' => '36 hours'
            ]
        ];

        $routeData = [
            'distance_km' => 8.5,
            'estimated_duration_minutes' => 480
        ];

        try {
            // Use reflection to access protected method
            $reflectionClass = new \ReflectionClass(get_class($this->itineraryService));
            
            // Test date info calculation
            $dateInfoMethod = $reflectionClass->getMethod('calculateDateInfo');
            $dateInfoMethod->setAccessible(true);
            $dateInfo = $dateInfoMethod->invoke($this->itineraryService, [], $trail, $routeData);
            
            $this->info("Total Trail Distance: {$trail['distance_km']} km");
            $this->info("Duration: {$dateInfo['duration_days']} days");
            $this->newLine();

            // Test fallback generation directly
            $generateDayPlanMethod = $reflectionClass->getMethod('generateDayPlan');
            $generateDayPlanMethod->setAccessible(true);
            
            for ($day = 1; $day <= $dateInfo['duration_days']; $day++) {
                $this->info("=== DAY {$day} (Fallback Generation) ===");
                
                $dayActivities = $generateDayPlanMethod->invoke(
                    $this->itineraryService, $day, $trail, $dateInfo, $routeData
                );
                
                // Show all activities with distances
                foreach ($dayActivities as $activity) {
                    $timeStr = sprintf('%02d:%02d', 
                        intval($activity['minutes'] / 60), 
                        $activity['minutes'] % 60
                    );
                    $distance = $activity['cum_distance_km'] ?? 0;
                    $this->info("  {$timeStr} - {$activity['title']} ({$distance} km)");
                }
                $this->newLine();
            }

            $this->info('✅ Test complete!');

        } catch (\Exception $e) {
            $this->error("❌ Error: " . $e->getMessage());
            $this->error("Stack trace: " . $e->getTraceAsString());
        }
    }
}
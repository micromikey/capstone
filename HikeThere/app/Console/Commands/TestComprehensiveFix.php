<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ItineraryGeneratorService;

class TestComprehensiveFix extends Command
{
    protected $signature = 'test:comprehensive-fix';
    protected $description = 'Test all itinerary generation fixes comprehensively';

    protected $itineraryService;

    public function __construct(ItineraryGeneratorService $itineraryService)
    {
        parent::__construct();
        $this->itineraryService = $itineraryService;
    }

    public function handle()
    {
        $this->info('🚀 COMPREHENSIVE ITINERARY TEST');
        $this->info('==============================');
        $this->newLine();

        // Create trail data with package duration (like from real trail)
        $trail = [
            'id' => 1,
            'name' => 'Ambangeg Trail',
            'difficulty' => 'moderate',
            'elevation_gain' => 1200,
            'distance_km' => 8.5,
            'estimated_duration_hours' => 24, // This should be overridden by package duration
            'package' => [
                'duration' => '36 hours', // 2 days, 1 night - this should take precedence
                'id' => 1,
                'name' => 'Weekend Adventure Package'
            ]
        ];

        $routeData = [
            'distance_km' => 8.5,
            'estimated_duration_minutes' => 480
        ];

        $userActivities = [];

        try {
            // Call with correct parameter order: generateItinerary($itinerary, $trail, $build, $weatherData)
            $itinerary = []; // Empty itinerary, let trail package drive the duration
            $build = $routeData; // Route data goes into build parameter
            $result = $this->itineraryService->generateItinerary($itinerary, $trail, $build, []);

            $this->info('📅 DURATION & DATES:');
            $this->info("Duration: {$result['dateInfo']['duration_days']} days, {$result['dateInfo']['nights']} nights");
            $this->info("Start Date: {$result['dateInfo']['start_date']}");
            $this->info("End Date: {$result['dateInfo']['end_date']}");
            $this->newLine();

            $this->info('🏔️ DAY ACTIVITIES:');
            foreach ($result['dayActivities'] as $dayIndex => $dayActivities) {
                $this->info("Day {$dayIndex}:");
                foreach ($dayActivities as $index => $activity) {
                    $timeStr = sprintf('%02d:%02d', 
                        intval($activity['minutes'] / 60), 
                        $activity['minutes'] % 60
                    );
                    $this->info("  {$timeStr} - {$activity['title']} ({$activity['location']})");
                    if ($index === 0) {
                        $this->info("    ➡️ Starting activity: {$activity['title']}");
                    }
                    if ($index >= 2) break; // Show first 3 activities
                }
                $this->newLine();
            }

            $this->info('🌙 NIGHT ACTIVITIES:');
            foreach ($result['nightActivities'] as $nightIndex => $nightActivities) {
                $this->info("Night {$nightIndex}:");
                foreach ($nightActivities as $index => $activity) {
                    $timeStr = sprintf('%02d:%02d', 
                        intval($activity['minutes'] / 60), 
                        $activity['minutes'] % 60
                    );
                    $this->info("  {$timeStr} - {$activity['title']} ({$activity['location']})");
                    if ($index >= 2) break; // Show first 3 activities
                }
                $this->newLine();
            }

            $this->info('✅ VALIDATION CHECKS:');
            
            // Check 1: Duration parsing from package
            $expectedDays = 2;
            $expectedNights = 1;
            $actualDays = intval($result['dateInfo']['duration_days']);
            $actualNights = intval($result['dateInfo']['nights']);
            $durationOK = ($actualDays === $expectedDays && $actualNights === $expectedNights);

            $this->info("✓ Duration from package (36 hours = 2 days/1 night): " . ($durationOK ? "PASS" : "FAIL"));
            
            // Check 2: Day 1 starts at trailhead
            $day1FirstActivity = $result['dayActivities'][1][0] ?? null;
            $day1OK = $day1FirstActivity && str_contains($day1FirstActivity['title'], 'Start') && str_contains($day1FirstActivity['title'], 'Trail');
            $this->info("✓ Day 1 starts at trailhead: " . ($day1OK ? "PASS" : "FAIL"));
            
            // Check 3: Day 2 starts at campsite  
            $day2FirstActivity = $result['dayActivities'][2][0] ?? null;
            $day2OK = $day2FirstActivity && str_contains($day2FirstActivity['title'], 'Break Camp');
            $this->info("✓ Day 2 starts at campsite: " . ($day2OK ? "PASS" : "FAIL"));
            
            // Check 4: Night activities start at reasonable evening time (18:00 or later)
            $night1FirstActivity = $result['nightActivities'][1][0] ?? null;
            $actualMinutes = $night1FirstActivity['minutes'] ?? 0;
            $actualTimeStr = sprintf('%02d:%02d', intval($actualMinutes / 60), $actualMinutes % 60);
            $nightTimeOK = $night1FirstActivity && $actualMinutes >= 18 * 60; // 18:00 = 1080 minutes

            $this->info("✓ Night activities start at evening time (18:00+): " . ($nightTimeOK ? "PASS" : "FAIL"));
            
            // Check 5: Timezone should be Philippines (Asia/Manila)
            $currentTz = config('app.timezone');
            $timezoneOK = ($currentTz === 'Asia/Manila');
            $this->info("✓ Philippines timezone (Asia/Manila): " . ($timezoneOK ? "PASS" : "FAIL"));
            
            $this->newLine();
            $this->info('🎯 OVERALL RESULT: ' . ($durationOK && $day1OK && $day2OK && $nightTimeOK && $timezoneOK ? "ALL TESTS PASSED! ✅" : "Some tests failed ❌"));

        } catch (\Exception $e) {
            $this->error("❌ Error generating itinerary: " . $e->getMessage());
            $this->error("Stack trace:");
            $this->error($e->getTraceAsString());
        }

        $this->newLine();
        $this->info('✨ Test complete!');
    }
}
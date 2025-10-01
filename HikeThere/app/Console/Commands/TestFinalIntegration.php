<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ItineraryGeneratorService;
use ReflectionClass;

class TestFinalIntegration extends Command
{
    protected $signature = 'test:final-integration';
    protected $description = 'Final test to ensure Google Maps is working in the complete itinerary generation';

    public function handle(ItineraryGeneratorService $service)
    {
        $this->info('=== Final Google Maps Integration Test ===');

        // Use the exact same parameters as our working debug test
        $trail = [
            'name' => 'Mt. Pulag via Ambangeg Trail',
            'location' => 'Benguet, Philippines',
            'latitude' => 16.5966,
            'longitude' => 120.9060,
            'transport_included' => 1, // This is crucial!
            'transport_details' => 'Van transportation from Shaw Boulevard Manila to Ambangeg Trail',
            'departure_point' => 'Shaw Boulevard Manila'
        ];

        // Test with array format (like the debug test)
        $build = [
            'user_lat' => 14.6417,
            'user_lng' => 120.4736,
            'user_location' => 'Bataan, Philippines',
            'start_date' => now()->addDays(7)->format('Y-m-d')
        ];

        // Test with object format (like the complete flow test)
        $buildObject = (object) [
            'user_lat' => 14.6417,
            'user_lng' => 120.4736,
            'user_location' => 'Bataan, Philippines',
            'start_date' => now()->addDays(7)->format('Y-m-d')
        ];

        $trailPackages = [
            (object) [
                'duration_days' => 2,
                'duration_nights' => 1
            ]
        ];

        try {
            $this->line("🔄 Testing with array format build data...");
            $itinerary1 = $service->generateItinerary($trailPackages, $trail, $build);
            
            if (isset($itinerary1['preHikeActivities'])) {
                $count1 = count($itinerary1['preHikeActivities']);
                $maxTime1 = 0;
                foreach ($itinerary1['preHikeActivities'] as $activity) {
                    $maxTime1 = max($maxTime1, $activity['minutes'] ?? 0);
                }
                $hours1 = round($maxTime1 / 60, 1);
                $this->line("✅ Array format: $count1 activities, $hours1 hours total");
            }
            
            $this->line("\n🔄 Testing with object format build data...");
            $itinerary2 = $service->generateItinerary($trailPackages, $trail, $buildObject);
            
            if (isset($itinerary2['preHikeActivities'])) {
                $count2 = count($itinerary2['preHikeActivities']);
                $maxTime2 = 0;
                foreach ($itinerary2['preHikeActivities'] as $activity) {
                    $maxTime2 = max($maxTime2, $activity['minutes'] ?? 0);
                }
                $hours2 = round($maxTime2 / 60, 1);
                $this->line("✅ Object format: $count2 activities, $hours2 hours total");
            }
            
            $this->line("\n📋 Sample Pre-hike Activities (Array Format):");
            if (isset($itinerary1['preHikeActivities'])) {
                foreach ($itinerary1['preHikeActivities'] as $index => $activity) {
                    $minutes = $activity['minutes'] ?? 0;
                    $timeDisplay = sprintf('%02d:%02d', intval($minutes / 60), $minutes % 60);
                    $this->line(sprintf(
                        "%d. %s | %s",
                        $index + 1,
                        $timeDisplay,
                        $activity['title'] ?? 'Unknown Activity'
                    ));
                }
            }
            
        } catch (\Exception $e) {
            $this->error("❌ Test failed: " . $e->getMessage());
            $this->line("Stack trace: " . $e->getTraceAsString());
        }
        
        return 0;
    }
}
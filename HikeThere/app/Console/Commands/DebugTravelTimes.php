<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ItineraryGeneratorService;
use ReflectionClass;

class DebugTravelTimes extends Command
{
    protected $signature = 'debug:travel-times';
    protected $description = 'Debug travel time calculations to see why Google Maps times aren\'t being used';

    public function handle(ItineraryGeneratorService $service)
    {
        $this->info('=== Debugging Travel Time Calculations ===');

        // Test the generatePreHikeActivities method directly
        $trail = [
            'name' => 'Mt. Pulag via Ambangeg Trail',
            'location' => 'Benguet, Philippines',
            'latitude' => 16.5966,
            'longitude' => 120.9060,
            'transport_included' => 1,
            'transport_details' => 'Van transportation from Shaw Boulevard Manila to Ambangeg Trail',
            'departure_point' => 'Shaw Boulevard Manila'
        ];

        $build = [
            'user_lat' => 14.6417,    // Bataan
            'user_lng' => 120.4736,
            'user_location' => 'Bataan, Philippines',
            'user_address' => 'Bataan, Philippines'
        ];

        try {
            $reflection = new ReflectionClass($service);
            
            // Test getUserLocationData method
            $getUserMethod = $reflection->getMethod('getUserLocationData');
            $getUserMethod->setAccessible(true);
            $userLocation = $getUserMethod->invoke($service, $build);
            
            $this->line("📍 User Location Data:");
            $this->line("   Address: " . $userLocation['address']);
            $this->line("   Lat: " . $userLocation['lat']);
            $this->line("   Lng: " . $userLocation['lng']);
            
            // Test generatePreHikeActivities
            $preHikeMethod = $reflection->getMethod('generatePreHikeActivities');
            $preHikeMethod->setAccessible(true);
            $activities = $preHikeMethod->invoke($service, $trail, $build);
            
            $this->line("\n🚌 Generated Pre-hike Activities:");
            foreach ($activities as $index => $activity) {
                $minutes = $activity['minutes'] ?? 0;
                $timeDisplay = sprintf('%02d:%02d', intval($minutes / 60), $minutes % 60);
                $this->line(sprintf(
                    "%d. %s | %s | %s",
                    $index + 1,
                    $timeDisplay,
                    $activity['title'] ?? 'Unknown',
                    $activity['location'] ?? 'Unknown'
                ));
                $this->line("   Description: " . ($activity['description'] ?? 'No description'));
            }
            
            // Test the calculateTravelTime method directly
            $this->line("\n🔍 Direct Travel Time Tests:");
            
            $travelMethod = $reflection->getMethod('calculateTravelTime');
            $travelMethod->setAccessible(true);
            
            // Bataan to Shaw Boulevard
            $time1 = $travelMethod->invoke(
                $service,
                14.6417, 120.4736,  // Bataan
                14.5868, 121.0584,  // Shaw Boulevard
                'bus',
                'bataan_to_manila_shaw'
            );
            $this->line("🚌 Bataan → Shaw Boulevard (Bus): " . round($time1/60, 1) . " hours ($time1 minutes)");
            
            // Shaw Boulevard to Mt. Pulag
            $time2 = $travelMethod->invoke(
                $service,
                14.5868, 121.0584,  // Shaw Boulevard
                16.5966, 120.9060,  // Mt. Pulag
                'van',
                'manila_to_baguio_trail'
            );
            $this->line("🚐 Shaw → Mt. Pulag (Van): " . round($time2/60, 1) . " hours ($time2 minutes)");
            
            $totalDirectTime = $time1 + $time2;
            $this->line("📊 Total Direct Travel Time: " . round($totalDirectTime/60, 1) . " hours");
            
            // Compare with what's in the activities
            $totalActivityTime = 0;
            foreach ($activities as $activity) {
                $totalActivityTime = max($totalActivityTime, $activity['minutes'] ?? 0);
            }
            $this->line("📊 Total Activity Schedule Time: " . round($totalActivityTime/60, 1) . " hours");
            
            if ($totalActivityTime < $totalDirectTime) {
                $this->error("⚠️  The activity schedule time is less than direct travel time!");
                $this->line("This suggests the Google Maps times aren't being used properly in activity generation.");
            }
            
        } catch (\Exception $e) {
            $this->error("❌ Debug failed: " . $e->getMessage());
            $this->line("Stack trace: " . $e->getTraceAsString());
        }
        
        return 0;
    }
}
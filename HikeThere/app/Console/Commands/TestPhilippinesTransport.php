<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ItineraryGeneratorService;
use ReflectionClass;

class TestPhilippinesTransport extends Command
{
    protected $signature = 'test:philippines-transport';
    protected $description = 'Test Philippines transportation system for Bataan → Shaw Boulevard → Mt. Pulag scenario';

    public function handle(ItineraryGeneratorService $service)
    {
        $this->info('=== Testing Bataan → Shaw Boulevard → Mt. Pulag Scenario ===');

        try {
            // Use reflection to access protected methods for testing
            $reflection = new ReflectionClass($service);
            
            // Test calculatePhilippinesTravelTime
            $travelMethod = $reflection->getMethod('calculatePhilippinesTravelTime');
            $travelMethod->setAccessible(true);
            
            // Test getLocationCoordinates
            $locationMethod = $reflection->getMethod('getLocationCoordinates');
            $locationMethod->setAccessible(true);
            
            // Test coordinates lookup
            $this->info("\n=== Location Coordinates ===");
            $locations = ['Shaw Boulevard Manila', 'Bataan', 'Ambangeg Trail'];
            foreach ($locations as $location) {
                $coords = $locationMethod->invoke($service, $location);
                $this->line("📍 $location: {$coords['lat']}, {$coords['lng']}");
            }
            
            // Test travel times
            $this->info("\n=== Travel Time Calculations ===");
            
            // Bataan to Shaw Boulevard
            $bataan_coords = $locationMethod->invoke($service, 'Bataan');
            $shaw_coords = $locationMethod->invoke($service, 'Shaw Boulevard Manila');
            
            $time1 = $travelMethod->invoke(
                $service, 
                $bataan_coords['lat'], $bataan_coords['lng'],
                $shaw_coords['lat'], $shaw_coords['lng'],
                'bus'
            );
            
            $this->line("🚌 Bataan → Shaw Boulevard: " . $time1 . " minutes (" . round($time1/60, 1) . " hours)");
            
            // Shaw Boulevard to Mt. Pulag
            $pulag_coords = $locationMethod->invoke($service, 'Ambangeg Trail');
            
            $time2 = $travelMethod->invoke(
                $service,
                $shaw_coords['lat'], $shaw_coords['lng'],
                $pulag_coords['lat'], $pulag_coords['lng'],
                'van'
            );
            
            $this->line("🚐 Shaw Boulevard → Mt. Pulag: " . $time2 . " minutes (" . round($time2/60, 1) . " hours)");
            
            $totalHours = round(($time1 + $time2)/60, 1);
            $this->info("\n⏱️  Total travel time: $totalHours hours");
            
            // Test route context handling
            $this->info("\n=== Testing Route Context ===");
            
            $contextMethod = $reflection->getMethod('getPhilippinesFallbackTime');
            $contextMethod->setAccessible(true);
            
            $routeTests = [
                ['transport' => 'bus', 'context' => 'bataan_to_manila_shaw'],
                ['transport' => 'van', 'context' => 'manila_to_baguio_trail'],
                ['transport' => 'bus', 'context' => 'province_to_manila'],
                ['transport' => 'van', 'context' => 'manila_to_baguio']
            ];
            
            foreach ($routeTests as $test) {
                $fallbackTime = $contextMethod->invoke($service, $test['transport'], $test['context']);
                $this->line("🛣️  {$test['transport']} via '{$test['context']}': " . round($fallbackTime/60, 1) . " hours fallback");
            }
            
            $this->info("\n✅ Philippines transportation system test completed!");
            
            // Test full pre-hike activity generation
            $this->info("\n=== Testing Full Pre-Hike Activity Generation ===");
            
            $trail = [
                'name' => 'Mt. Pulag via Ambangeg Trail',
                'location' => 'Benguet, Philippines',
                'latitude' => 16.5966,
                'longitude' => 120.9060,
                'transport_included' => 1,
                'transport_details' => 'Van transportation from Shaw Boulevard Manila to Ambangeg Trail',
                'departure_point' => 'Shaw Boulevard Manila'
            ];
            
            $userLocation = [
                'lat' => 14.6417,
                'lng' => 120.4736,
                'address' => 'Bataan, Philippines'
            ];
            
            $trailPackages = [
                ['duration_days' => 2, 'duration_nights' => 1]
            ];
            
            $preHikeMethod = $reflection->getMethod('generatePreHikeActivities');
            $preHikeMethod->setAccessible(true);
            
            $activities = $preHikeMethod->invoke($service, $trail, $trailPackages, $userLocation);
            
            $this->line("Generated " . count($activities) . " pre-hike activities:");
            
            foreach ($activities as $index => $activity) {
                // Use correct keys from actual structure
                $minutes = $activity['minutes'] ?? 0;
                $timeDisplay = sprintf('%02d:%02d', intval($minutes / 60), $minutes % 60);
                
                $activityName = $activity['title'] ?? 'Unknown Activity';
                $location = $activity['location'] ?? 'Unknown Location';
                $description = $activity['description'] ?? 'No description';
                
                $this->line(sprintf(
                    "%d. %s | %s | %s",
                    $index + 1,
                    $timeDisplay,
                    $activityName,
                    $location
                ));
                $this->line("   📋 $description");
            }
            
        } catch (\Exception $e) {
            $this->error("❌ Error: " . $e->getMessage());
            $this->line("Stack trace: " . $e->getTraceAsString());
        }
        
        return 0;
    }
}
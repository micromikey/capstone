<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\GoogleMapsService;
use App\Services\ItineraryGeneratorService;
use ReflectionClass;

class TestGoogleMapsIntegration extends Command
{
    protected $signature = 'test:google-maps-integration';
    protected $description = 'Test Google Maps API integration for accurate Philippines travel times';

    public function handle(GoogleMapsService $googleMaps, ItineraryGeneratorService $itineraryService)
    {
        $this->info('=== Testing Google Maps API Integration ===');

        // Test 1: Direct Google Maps API
        $this->info("\n=== Test 1: Direct Google Maps Distance Matrix API ===");
        
        // Bataan to Shaw Boulevard Manila
        $bataan = ['lat' => 14.6417, 'lng' => 120.4736];
        $shaw = ['lat' => 14.5868, 'lng' => 121.0584];
        $pulag = ['lat' => 16.5966, 'lng' => 120.9060];
        
        $this->line("📍 Testing Route: Bataan → Shaw Boulevard Manila");
        $result1 = $googleMaps->getDistanceMatrix(
            $bataan['lat'], $bataan['lng'],
            $shaw['lat'], $shaw['lng'],
            'driving'
        );
        
        if ($result1) {
            $this->line("✅ Google API Result:");
            $this->line("   Distance: {$result1['distance_text']} ({$result1['distance_km']} km)");
            $this->line("   Duration: {$result1['duration_text']} ({$result1['duration_minutes']} minutes)");
            if (isset($result1['duration_in_traffic_minutes'])) {
                $this->line("   With Traffic: {$result1['duration_in_traffic_text']} ({$result1['duration_in_traffic_minutes']} minutes)");
            }
        } else {
            $this->error("❌ Google Maps API failed for Bataan → Shaw Boulevard");
        }
        
        $this->line("\n📍 Testing Route: Shaw Boulevard → Mt. Pulag");
        $result2 = $googleMaps->getDistanceMatrix(
            $shaw['lat'], $shaw['lng'],
            $pulag['lat'], $pulag['lng'],
            'driving'
        );
        
        if ($result2) {
            $this->line("✅ Google API Result:");
            $this->line("   Distance: {$result2['distance_text']} ({$result2['distance_km']} km)");
            $this->line("   Duration: {$result2['duration_text']} ({$result2['duration_minutes']} minutes)");
            if (isset($result2['duration_in_traffic_minutes'])) {
                $this->line("   With Traffic: {$result2['duration_in_traffic_text']} ({$result2['duration_in_traffic_minutes']} minutes)");
            }
        } else {
            $this->error("❌ Google Maps API failed for Shaw Boulevard → Mt. Pulag");
        }

        // Test 2: Enhanced Philippines Travel Time
        $this->info("\n=== Test 2: Enhanced Philippines Travel Time Calculation ===");
        
        $this->line("🚌 Bus travel: Bataan → Shaw Boulevard");
        $busTime = $googleMaps->calculatePhilippinesTravelTime(
            $bataan['lat'], $bataan['lng'],
            $shaw['lat'], $shaw['lng'],
            'bus'
        );
        $this->line("   Result: " . round($busTime/60, 1) . " hours ($busTime minutes)");
        
        $this->line("🚐 Van travel: Shaw Boulevard → Mt. Pulag");
        $vanTime = $googleMaps->calculatePhilippinesTravelTime(
            $shaw['lat'], $shaw['lng'],
            $pulag['lat'], $pulag['lng'],
            'van'
        );
        $this->line("   Result: " . round($vanTime/60, 1) . " hours ($vanTime minutes)");

        // Test 3: Integrated Itinerary Service
        $this->info("\n=== Test 3: Integrated Itinerary Service ===");
        
        try {
            $reflection = new ReflectionClass($itineraryService);
            $method = $reflection->getMethod('calculateTravelTime');
            $method->setAccessible(true);
            
            $this->line("🔄 Testing integrated calculateTravelTime method:");
            
            // Test Bataan → Shaw Boulevard with bus
            $integratedTime1 = $method->invoke(
                $itineraryService,
                $bataan['lat'], $bataan['lng'],
                $shaw['lat'], $shaw['lng'],
                'bus',
                'bataan_to_manila_shaw'
            );
            $this->line("   Bataan → Shaw (Bus): " . round($integratedTime1/60, 1) . " hours");
            
            // Test Shaw Boulevard → Mt. Pulag with van
            $integratedTime2 = $method->invoke(
                $itineraryService,
                $shaw['lat'], $shaw['lng'],
                $pulag['lat'], $pulag['lng'],
                'van',
                'manila_to_baguio_trail'
            );
            $this->line("   Shaw → Mt. Pulag (Van): " . round($integratedTime2/60, 1) . " hours");
            
        } catch (\Exception $e) {
            $this->error("❌ Integrated service test failed: " . $e->getMessage());
        }

        // Test 4: Traffic Conditions
        $this->info("\n=== Test 4: Traffic Conditions Analysis ===");
        
        $traffic = $googleMaps->getTrafficConditions(
            $bataan['lat'], $bataan['lng'],
            $shaw['lat'], $shaw['lng']
        );
        $this->line("🚦 Current traffic (Bataan → Shaw): $traffic");

        // Test 5: Full Pre-hike Activity Generation
        $this->info("\n=== Test 5: Full Pre-hike Activity Generation ===");
        
        $trail = [
            'name' => 'Mt. Pulag via Ambangeg Trail',
            'location' => 'Benguet, Philippines',
            'latitude' => $pulag['lat'],
            'longitude' => $pulag['lng'],
            'transport_included' => 1,
            'transport_details' => 'Van transportation from Shaw Boulevard Manila to Ambangeg Trail',
            'departure_point' => 'Shaw Boulevard Manila'
        ];
        
        $userLocation = [
            'lat' => $bataan['lat'],
            'lng' => $bataan['lng'],
            'address' => 'Bataan, Philippines'
        ];
        
        $trailPackages = [
            ['duration_days' => 2, 'duration_nights' => 1]
        ];
        
        try {
            $preHikeMethod = $reflection->getMethod('generatePreHikeActivities');
            $preHikeMethod->setAccessible(true);
            
            $activities = $preHikeMethod->invoke($itineraryService, $trail, $trailPackages, $userLocation);
            
            $this->line("Generated " . count($activities) . " activities with Google Maps accuracy:");
            
            foreach ($activities as $index => $activity) {
                $minutes = $activity['minutes'] ?? 0;
                $timeDisplay = sprintf('%02d:%02d', intval($minutes / 60), $minutes % 60);
                
                $this->line(sprintf(
                    "%d. %s | %s | %s",
                    $index + 1,
                    $timeDisplay,
                    $activity['title'] ?? 'Unknown Activity',
                    $activity['location'] ?? 'Unknown Location'
                ));
            }
            
        } catch (\Exception $e) {
            $this->error("❌ Pre-hike activity generation failed: " . $e->getMessage());
        }

        // Summary
        $this->info("\n=== Summary ===");
        $totalGoogleTime = ($result1 ? $result1['duration_minutes'] : 0) + ($result2 ? $result2['duration_minutes'] : 0);
        $totalIntegratedTime = ($integratedTime1 ?? 0) + ($integratedTime2 ?? 0);
        
        if ($totalGoogleTime > 0) {
            $this->line("✅ Google Maps API: Working");
            $this->line("📊 Total Google Travel Time: " . round($totalGoogleTime/60, 1) . " hours");
        } else {
            $this->line("❌ Google Maps API: Not working");
        }
        
        if ($totalIntegratedTime > 0) {
            $this->line("📊 Total Integrated Time: " . round($totalIntegratedTime/60, 1) . " hours");
            $this->line("✅ Integration: Successful - Using " . ($totalGoogleTime > 0 ? "Google API + Philippines adjustments" : "Fallback calculations"));
        }
        
        return 0;
    }
}
<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\GoogleMapsService;
use App\Services\ItineraryGeneratorService;

class DebugManilaToMountPulag extends Command
{
    protected $signature = 'debug:manila-to-pulag';
    protected $description = 'Debug Google Maps API for Manila to Mount Pulag route';

    public function handle(GoogleMapsService $googleMaps, ItineraryGeneratorService $itinerary)
    {
        $this->info('=== Testing Manila to Mount Pulag Route ===');

        // Shaw Boulevard Manila coordinates
        $manilaLat = 14.5906;
        $manilaLng = 121.0570;
        
        // Mount Pulag Ambangeg Trail coordinates
        $pulagLat = 16.5966;
        $pulagLng = 120.9060;

        $this->line("📍 From: Shaw Boulevard, Manila ({$manilaLat}, {$manilaLng})");
        $this->line("📍 To: Mount Pulag Ambangeg Trail ({$pulagLat}, {$pulagLng})");
        $this->line("");

        // Test 1: Direct Google Maps Distance Matrix API call
        $this->line("🔄 Testing Direct Google Maps Distance Matrix API...");
        try {
            $distanceMatrix = $googleMaps->getDistanceMatrix(
                $manilaLat, $manilaLng,
                $pulagLat, $pulagLng,
                'driving'
            );
            
            if ($distanceMatrix && isset($distanceMatrix['rows'][0]['elements'][0])) {
                $element = $distanceMatrix['rows'][0]['elements'][0];
                if ($element['status'] === 'OK') {
                    $duration = $element['duration_in_traffic']['value'] ?? $element['duration']['value'];
                    $distance = $element['distance']['value'];
                    
                    $hours = round($duration / 3600, 1);
                    $km = round($distance / 1000, 1);
                    
                    $this->info("✅ Google Maps Direct: {$hours} hours, {$km} km");
                    
                    if ($hours < 5) {
                        $this->warn("⚠️ This seems too short for Manila to Mount Pulag!");
                    } else {
                        $this->info("✅ Duration seems realistic for Philippines mountain route");
                    }
                } else {
                    $this->error("❌ API Error: " . $element['status']);
                }
            } else {
                $this->error("❌ Invalid response structure");
            }
        } catch (\Exception $e) {
            $this->error("❌ Exception: " . $e->getMessage());
        }

        // Test 2: Through ItineraryGeneratorService calculateTravelTime method
        $this->line("\n🔄 Testing ItineraryGeneratorService calculateTravelTime...");
        try {
            $reflection = new \ReflectionClass($itinerary);
            $calculateMethod = $reflection->getMethod('calculateTravelTime');
            $calculateMethod->setAccessible(true);
            
            $travelTime = $calculateMethod->invoke(
                $itinerary,
                $manilaLat, $manilaLng,
                $pulagLat, $pulagLng,
                'van',
                'manila_to_baguio_trail'
            );
            
            if ($travelTime) {
                $hours = round($travelTime / 60, 1);
                $this->info("✅ ItineraryService: {$travelTime} minutes ({$hours} hours)");
                
                if ($hours < 5) {
                    $this->warn("⚠️ This is definitely too short for Manila to Mount Pulag!");
                } else {
                    $this->info("✅ Duration seems realistic");
                }
            } else {
                $this->warn("⚠️ calculateTravelTime returned empty/null");
            }
        } catch (\Exception $e) {
            $this->error("❌ Exception: " . $e->getMessage());
        }

        // Test 3: Check coordinates being used in trail data
        $this->line("\n🔄 Testing coordinates from trail data methods...");
        try {
            $trailData = [
                'name' => 'Mt. Pulag via Ambangeg Trail',
                'location' => 'Benguet, Philippines',
                'latitude' => 16.5966,
                'longitude' => 120.9060,
                'departure_point' => 'Shaw Boulevard Manila'
            ];
            
            $getDepartureMethod = $reflection->getMethod('getDeparturePointCoordinates');
            $getDepartureMethod->setAccessible(true);
            $departureCoords = $getDepartureMethod->invoke($itinerary, 'Shaw Boulevard Manila');
            
            $getTrailMethod = $reflection->getMethod('getTrailCoordinates');
            $getTrailMethod->setAccessible(true);
            $trailCoords = $getTrailMethod->invoke($itinerary, $trailData);
            
            $this->line("📍 Departure Coordinates: {$departureCoords['lat']}, {$departureCoords['lng']}");
            $this->line("📍 Trail Coordinates: {$trailCoords['lat']}, {$trailCoords['lng']}");
            
            // Verify if coordinates match what we expect
            if (abs($departureCoords['lat'] - $manilaLat) < 0.01 && abs($departureCoords['lng'] - $manilaLng) < 0.01) {
                $this->info("✅ Departure coordinates match Shaw Boulevard Manila");
            } else {
                $this->warn("⚠️ Departure coordinates don't match expected Shaw Boulevard location");
            }
            
            if (abs($trailCoords['lat'] - $pulagLat) < 0.01 && abs($trailCoords['lng'] - $pulagLng) < 0.01) {
                $this->info("✅ Trail coordinates match Mount Pulag");
            } else {
                $this->warn("⚠️ Trail coordinates don't match expected Mount Pulag location");
            }
            
        } catch (\Exception $e) {
            $this->error("❌ Exception testing coordinates: " . $e->getMessage());
        }

        // Test 4: Check fallback mechanism
        $this->line("\n🔄 Testing Philippines fallback times...");
        try {
            $getFallbackMethod = $reflection->getMethod('getPhilippinesFallbackTime');
            $getFallbackMethod->setAccessible(true);
            
            $fallbackTime = $getFallbackMethod->invoke($itinerary, 'van', 'manila_to_baguio_trail');
            $fallbackHours = round($fallbackTime / 60, 1);
            
            $this->line("📋 Philippines fallback time: {$fallbackTime} minutes ({$fallbackHours} hours)");
            
            if ($fallbackHours >= 8) {
                $this->info("✅ Fallback time seems realistic for Manila to Baguio/Mt. Pulag");
            } else {
                $this->warn("⚠️ Even fallback time seems too short");
            }
        } catch (\Exception $e) {
            $this->error("❌ Exception testing fallback: " . $e->getMessage());
        }

        return 0;
    }
}
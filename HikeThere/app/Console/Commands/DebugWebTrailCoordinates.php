<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ItineraryGeneratorService;

class DebugWebTrailCoordinates extends Command
{
    protected $signature = 'debug:web-coordinates';
    protected $description = 'Debug what coordinates the web interface is actually using';

    public function handle(ItineraryGeneratorService $service)
    {
        $this->info('=== Debugging Web Interface Trail Coordinates ===');

        // Test different variations of how trail data might come from web
        $testCases = [
            'Direct coordinates' => [
                'name' => 'Mt. Pulag via Ambangeg Trail',
                'latitude' => 16.5966,
                'longitude' => 120.9060,
                'departure_point' => 'Shaw Boulevard Manila'
            ],
            'String coordinates' => [
                'name' => 'Mt. Pulag via Ambangeg Trail', 
                'latitude' => '16.5966',
                'longitude' => '120.9060',
                'departure_point' => 'Shaw Boulevard Manila'
            ],
            'No coordinates' => [
                'name' => 'Mt. Pulag via Ambangeg Trail',
                'location' => 'Benguet, Philippines',
                'departure_point' => 'Shaw Boulevard Manila'
            ],
            'Different name format' => [
                'mountain_name' => 'Mount Pulag',
                'trail_name' => 'Ambangeg Trail',
                'latitude' => 16.5966,
                'longitude' => 120.9060,
                'departure_point' => 'Shaw Boulevard Manila'
            ]
        ];

        $reflection = new \ReflectionClass($service);
        $getDepartureMethod = $reflection->getMethod('getDeparturePointCoordinates');
        $getDepartureMethod->setAccessible(true);
        $getTrailMethod = $reflection->getMethod('getTrailCoordinates');
        $getTrailMethod->setAccessible(true);
        $calculateMethod = $reflection->getMethod('calculateTravelTime');
        $calculateMethod->setAccessible(true);

        foreach ($testCases as $testName => $trailData) {
            $this->line("\n🔍 Testing: $testName");
            
            try {
                // Get coordinates
                $departureCoords = $getDepartureMethod->invoke($service, 'Shaw Boulevard Manila');
                $trailCoords = $getTrailMethod->invoke($service, $trailData);
                
                $this->line("📍 Departure: {$departureCoords['lat']}, {$departureCoords['lng']}");
                $this->line("📍 Trail: {$trailCoords['lat']}, {$trailCoords['lng']}");
                
                // Check if coordinates are valid
                if (!$trailCoords['lat'] || !$trailCoords['lng']) {
                    $this->warn("⚠️ Invalid trail coordinates - this could cause issues");
                    continue;
                }
                
                // Calculate travel time
                $travelTime = $calculateMethod->invoke(
                    $service,
                    $departureCoords['lat'], $departureCoords['lng'],
                    $trailCoords['lat'], $trailCoords['lng'],
                    'van',
                    'manila_to_baguio_trail'
                );
                
                if ($travelTime) {
                    $hours = round($travelTime / 60, 1);
                    $this->info("✅ Travel time: {$travelTime} minutes ({$hours} hours)");
                    
                    if ($hours < 5) {
                        $this->error("❌ This is too short for Manila to Mount Pulag!");
                    } else {
                        $this->info("✅ Duration seems realistic");
                    }
                } else {
                    $this->error("❌ No travel time returned - would use fallback");
                }
                
            } catch (\Exception $e) {
                $this->error("❌ Error: " . $e->getMessage());
            }
        }

        // Test potential nearby locations that might be confused with Mount Pulag
        $this->line("\n🔍 Testing potential location mix-ups...");
        
        $nearbyLocations = [
            'Baguio City' => ['lat' => 16.4023, 'lng' => 120.5960],
            'La Trinidad, Benguet' => ['lat' => 16.4593, 'lng' => 120.5908],
            'Tublay, Benguet' => ['lat' => 16.5000, 'lng' => 120.6167],
            'Actual Mt. Pulag' => ['lat' => 16.5966, 'lng' => 120.9060]
        ];
        
        $manilaLat = 14.5906;
        $manilaLng = 121.0570;
        
        foreach ($nearbyLocations as $locationName => $coords) {
            $travelTime = $calculateMethod->invoke(
                $service,
                $manilaLat, $manilaLng,
                $coords['lat'], $coords['lng'],
                'van',
                'manila_to_baguio_trail'
            );
            
            $hours = $travelTime ? round($travelTime / 60, 1) : 0;
            $this->line("📍 {$locationName}: {$hours} hours");
            
            if ($hours > 0 && $hours < 3) {
                $this->warn("⚠️ This could be the source of the 1.5h error!");
            }
        }

        return 0;
    }
}
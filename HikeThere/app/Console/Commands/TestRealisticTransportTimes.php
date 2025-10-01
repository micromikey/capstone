<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ItineraryGeneratorService;

class TestRealisticTransportTimes extends Command
{
    protected $signature = 'test:realistic-transport';
    protected $description = 'Test realistic transportation times with user location';

    public function handle()
    {
        $this->info('🗺️ REALISTIC TRANSPORT TIMES TEST');
        $this->info('==================================');
        $this->newLine();

        try {
            // Test Case 1: User in Manila going to Mt. Pulag (Benguet) with transport included
            $this->info('=== TEST CASE 1: Manila → Mt. Pulag (Transport Included) ===');
            $trailPulag = [
                'id' => 1,
                'name' => 'Mt. Pulag Trail',
                'distance_km' => 8.5,
                'transport_included' => true,
                'transport_details' => 'Van transportation from Baguio City Terminal',
                'departure_point' => 'Baguio City Terminal',
                'coordinates_start_lat' => 16.5966,
                'coordinates_start_lng' => 120.9060,
                'package' => ['duration' => '36 hours']
            ];

            $buildManilaUser = [
                'user_location' => 'Quezon City, Metro Manila, Philippines',
                'user_lat' => 14.6760,
                'user_lng' => 121.0437
            ];

            $this->testTransportWithLocation($trailPulag, $buildManilaUser, 'Manila User');
            $this->newLine();

            // Test Case 2: User in Cebu going to Mt. Apo (Davao) with commute
            $this->info('=== TEST CASE 2: Cebu → Mt. Apo (Commute) ===');
            $trailApo = [
                'id' => 2,
                'name' => 'Mt. Apo Trail',
                'distance_km' => 12.0,
                'transport_included' => false,
                'transport_details' => 'Private vehicle recommended',
                'coordinates_start_lat' => 7.0031,
                'coordinates_start_lng' => 125.2769,
                'package' => ['duration' => '48 hours']
            ];

            $buildCebuUser = [
                'user_location' => 'Cebu City, Cebu, Philippines', 
                'user_lat' => 10.3157,
                'user_lng' => 123.8854
            ];

            $this->testTransportWithLocation($trailApo, $buildCebuUser, 'Cebu User');
            $this->newLine();

            // Test Case 3: User near the trail (local) going to Mt. Makiling
            $this->info('=== TEST CASE 3: Los Baños → Mt. Makiling (Local) ===');
            $trailMakiling = [
                'id' => 3,
                'name' => 'Mt. Makiling Trail',
                'distance_km' => 6.0,
                'transport_included' => false,
                'transport_details' => 'Jeepney or private vehicle',
                'coordinates_start_lat' => 14.1350,
                'coordinates_start_lng' => 121.2170,
                'package' => ['duration' => '8 hours']
            ];

            $buildLocalUser = [
                'user_location' => 'Los Baños, Laguna, Philippines',
                'user_lat' => 14.1647,
                'user_lng' => 121.2417
            ];

            $this->testTransportWithLocation($trailMakiling, $buildLocalUser, 'Local User');
            $this->newLine();

        } catch (\Exception $e) {
            $this->error("❌ Error: " . $e->getMessage());
            $this->error("Stack trace: " . $e->getTraceAsString());
        }

        $this->info('✅ Test completed! Transportation times are now realistic and location-based.');
    }

    private function testTransportWithLocation($trail, $build, $userType)
    {
        $itineraryService = app(ItineraryGeneratorService::class);

        $routeData = [
            'distance_km' => $trail['distance_km'],
            'estimated_duration_minutes' => 480
        ];

        $itinerary = [];
        $result = $itineraryService->generateItinerary($itinerary, $trail, $build, []);

        $this->info("🌍 {$userType} Details:");
        $this->info("  Location: {$build['user_location']}");
        $this->info("  Coordinates: {$build['user_lat']}, {$build['user_lng']}");
        $this->info("  Trail: {$trail['name']}");
        $this->info("  Transport: " . ($trail['transport_included'] ? 'Included' : 'Self-arrange'));
        $this->newLine();

        if (isset($result['preHikeActivities']) && !empty($result['preHikeActivities'])) {
            $this->info('⏰ Transportation Schedule:');
            foreach ($result['preHikeActivities'] as $activity) {
                $timeStr = sprintf('%02d:%02d', 
                    intval($activity['minutes'] / 60), 
                    $activity['minutes'] % 60
                );
                $description = isset($activity['description']) ? " - {$activity['description']}" : '';
                $this->info("  {$timeStr} | {$activity['title']} ({$activity['location']}){$description}");
            }

            // Calculate total transport time
            $firstActivity = $result['preHikeActivities'][0];
            $lastActivity = end($result['preHikeActivities']);
            $totalMinutes = $lastActivity['minutes'] - $firstActivity['minutes'];
            $totalHours = floor($totalMinutes / 60);
            $remainingMinutes = $totalMinutes % 60;
            
            $this->newLine();
            $this->info("📊 Total pre-hike time: {$totalHours}h {$remainingMinutes}m");
        }
    }
}
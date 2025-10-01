<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ItineraryGeneratorService;

class TestPreHikeTransport extends Command
{
    protected $signature = 'test:pre-hike-transport';
    protected $description = 'Test pre-hike transportation activities generation';

    protected $itineraryService;

    public function __construct(ItineraryGeneratorService $itineraryService)
    {
        parent::__construct();
        $this->itineraryService = $itineraryService;
    }

    public function handle()
    {
        $this->info('🚌 PRE-HIKE TRANSPORT TEST');
        $this->info('==========================');
        $this->newLine();

        // Test Case 1: Transportation Included
        $this->info('=== TEST CASE 1: Transportation Included ===');
        $trailWithTransport = [
            'id' => 1,
            'name' => 'Mt. Pulag Trail',
            'distance_km' => 8.5,
            'transport_included' => true,
            'transport_details' => 'Van transportation from Baguio City',
            'departure_point' => 'Baguio City Terminal',
            'pickup_time' => '08:30', // Test with specific pickup time
            'package' => [
                'duration' => '36 hours'
            ]
        ];

        $this->testTransportGeneration($trailWithTransport, 'Transport Included');
        $this->newLine();

        // Test Case 2: Commute/Self-Transportation
        $this->info('=== TEST CASE 2: Commute/Self-Transportation ===');
        $trailCommute = [
            'id' => 2,
            'name' => 'Mt. Apo Trail',
            'distance_km' => 12.0,
            'transport_included' => false,
            'departure_time' => '09:00', // Test with specific departure time
            'transport_details' => 'Private vehicle recommended',
            'departure_point' => 'Kidapawan City',
            'package' => [
                'duration' => '48 hours'
            ]
        ];

        $this->testTransportGeneration($trailCommute, 'Commute');
        $this->newLine();

        // Test Case 3: Same-day Short Journey
        $this->info('=== TEST CASE 3: Same-day Short Journey ===');
        $trailShortJourney = [
            'id' => 3,
            'name' => 'Mt. Makiling Trail',
            'distance_km' => 6.0,
            'transport_included' => true,
            'transport_details' => 'Van transportation from Los Baños',
            'departure_point' => 'Los Baños Terminal',
            'pickup_time' => '05:30', // Early pickup for same-day hike
            'latitude' => 14.1665,
            'longitude' => 121.2237,
            'package' => [
                'duration' => '12 hours'
            ]
        ];

        $this->testTransportGeneration($trailShortJourney, 'Same-day Short Journey');
        $this->newLine();

        $this->info('✅ Test complete!');
    }

    private function testTransportGeneration($trail, $testType)
    {
        try {
            $routeData = [
                'distance_km' => $trail['distance_km'],
                'estimated_duration_minutes' => 480
            ];

            $itinerary = [];
            $build = $routeData;
            $result = $this->itineraryService->generateItinerary($itinerary, $trail, $build, []);

            $this->info("Trail: {$trail['name']}");
            $this->info("Transport Type: {$testType}");
            $this->info("Transport Included: " . ($trail['transport_included'] ? 'Yes' : 'No'));
            $this->newLine();

            if (isset($result['preHikeActivities']) && !empty($result['preHikeActivities'])) {
                $this->info('🚌 PRE-HIKE TRANSPORTATION ACTIVITIES:');
                foreach ($result['preHikeActivities'] as $activity) {
                    $timeStr = sprintf('%02d:%02d', 
                        intval($activity['minutes'] / 60), 
                        $activity['minutes'] % 60
                    );
                    $location = $activity['location'] ?? 'Unknown';
                    $description = isset($activity['description']) ? " - {$activity['description']}" : '';
                    $this->info("  {$timeStr} - {$activity['title']} ({$location}){$description}");
                }
            } else {
                $this->warn('No pre-hike activities generated');
            }

            $this->newLine();
            $this->info('🏔️ FIRST DAY ACTIVITY:');
            if (isset($result['dayActivities'][1]) && !empty($result['dayActivities'][1])) {
                $firstDayActivity = $result['dayActivities'][1][0];
                $timeStr = sprintf('%02d:%02d', 
                    intval($firstDayActivity['minutes'] / 60), 
                    $firstDayActivity['minutes'] % 60
                );
                $this->info("  {$timeStr} - {$firstDayActivity['title']} ({$firstDayActivity['location']})");
            }

        } catch (\Exception $e) {
            $this->error("❌ Error: " . $e->getMessage());
            $this->error("Stack trace: " . $e->getTraceAsString());
        }
    }
}
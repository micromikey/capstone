<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ItineraryGeneratorService;

class DebugWebTrailData extends Command
{
    protected $signature = 'debug:web-trail-data';
    protected $description = 'Debug what trail data is being passed to the service from web';

    public function handle(ItineraryGeneratorService $service)
    {
        $this->info('=== Debugging Web Trail Data ===');

        // Test different trail data formats that might come from web
        $testCases = [
            'Array with transport_included' => [
                'name' => 'Mt. Pulag via Ambangeg Trail',
                'location' => 'Benguet, Philippines',
                'latitude' => 16.5966,
                'longitude' => 120.9060,
                'transport_included' => 1,
                'transport_details' => 'Van transportation from Shaw Boulevard Manila to Ambangeg Trail',
                'departure_point' => 'Shaw Boulevard Manila'
            ],
            'Array WITHOUT transport_included' => [
                'name' => 'Mt. Pulag via Ambangeg Trail',
                'location' => 'Benguet, Philippines',
                'latitude' => 16.5966,
                'longitude' => 120.9060,
                'transport_details' => 'Van transportation from Shaw Boulevard Manila to Ambangeg Trail',
                'departure_point' => 'Shaw Boulevard Manila'
            ],
            'Array with transport_included = 0' => [
                'name' => 'Mt. Pulag via Ambangeg Trail',
                'location' => 'Benguet, Philippines',
                'latitude' => 16.5966,
                'longitude' => 120.9060,
                'transport_included' => 0,
                'transport_details' => 'Van transportation from Shaw Boulevard Manila to Ambangeg Trail',
                'departure_point' => 'Shaw Boulevard Manila'
            ]
        ];

        $itineraryData = [
            (object) [
                'duration_days' => 2,
                'duration_nights' => 1
            ]
        ];

        $buildData = [
            'user_lat' => 14.6417,
            'user_lng' => 120.4736,
            'user_location' => 'Bataan, Philippines',
            'start_date' => now()->addDays(7)->format('Y-m-d')
        ];

        foreach ($testCases as $testName => $trail) {
            $this->line("\n🔍 Testing: $testName");
            $this->line('Trail data: ' . json_encode($trail, JSON_PRETTY_PRINT));
            
            try {
                $generatedData = $service->generateItinerary($itineraryData, $trail, $buildData);
                
                if (isset($generatedData['preHikeActivities'])) {
                    $activities = $generatedData['preHikeActivities'];
                    $count = count($activities);
                    $totalHours = round(end($activities)['minutes'] / 60, 1);
                    
                    $this->info("✅ Generated $count activities, $totalHours hours total");
                    
                    // Show first few activity titles to identify which method was used
                    $this->line('📋 Activity titles:');
                    foreach (array_slice($activities, 0, 3) as $index => $activity) {
                        $this->line("  " . ($index + 1) . ". " . ($activity['title'] ?? 'Unknown'));
                    }
                    
                    // Determine which method was used based on activity titles
                    $firstTitle = $activities[0]['title'] ?? '';
                    if (str_contains($firstTitle, 'Long Journey')) {
                        $this->info('🎯 Used: generateIncludedTransportActivities (Google Maps, updated)');
                    } elseif (str_contains($firstTitle, 'Group Adventure')) {
                        $this->warn('⚠️ Used: generateCommuteActivities (hardcoded, old)');
                    } else {
                        $this->line('❓ Used: Unknown method');
                    }
                } else {
                    $this->error('❌ No pre-hike activities generated');
                }
                
            } catch (\Exception $e) {
                $this->error("❌ Error: " . $e->getMessage());
            }
        }

        return 0;
    }
}
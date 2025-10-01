<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\Hiker\RefactoredItineraryController;
use App\Services\ItineraryGeneratorService;
use Illuminate\Http\Request;

class TestWebItineraryGeneration extends Command
{
    protected $signature = 'test:web-itinerary';
    protected $description = 'Test the web itinerary generation exactly as it happens in browser';

    public function handle(ItineraryGeneratorService $service)
    {
        $this->info('=== Testing Web Itinerary Generation ===');

        // Simulate the exact data structure from web interface
        $itineraryData = [
            (object) [
                'duration_days' => 2,
                'duration_nights' => 1
            ]
        ];

        $trail = [
            'name' => 'Mt. Pulag via Ambangeg Trail',
            'location' => 'Benguet, Philippines',
            'latitude' => 16.5966,
            'longitude' => 120.9060,
            'transport_included' => 1,
            'transport_details' => 'Van transportation from Shaw Boulevard Manila to Ambangeg Trail',
            'departure_point' => 'Shaw Boulevard Manila'
        ];

        $buildData = [
            'user_lat' => 14.6417,
            'user_lng' => 120.4736,
            'user_location' => 'Bataan, Philippines',
            'start_date' => now()->addDays(7)->format('Y-m-d')
        ];

        try {
            $this->line('🔄 Calling ItineraryGeneratorService directly...');
            $generatedData = $service->generateItinerary($itineraryData, $trail, $buildData);
            
            if (isset($generatedData['preHikeActivities'])) {
                $this->info('✅ Got pre-hike activities: ' . count($generatedData['preHikeActivities']));
                
                $this->line('📋 Activities breakdown:');
                foreach ($generatedData['preHikeActivities'] as $index => $activity) {
                    $minutes = $activity['minutes'] ?? 0;
                    $timeDisplay = sprintf('%02d:%02d', intval($minutes / 60), $minutes % 60);
                    $this->line(sprintf(
                        "%d. %s | %s | %s",
                        $index + 1,
                        $timeDisplay,
                        $activity['title'] ?? 'Unknown',
                        $activity['description'] ?? 'No description'
                    ));
                }
                
                // Check the total duration
                $lastActivity = end($generatedData['preHikeActivities']);
                $totalHours = round(($lastActivity['minutes'] ?? 0) / 60, 1);
                
                $this->line('⏰ Total pre-hike duration: ' . $totalHours . ' hours');
                
                if ($totalHours > 10) {
                    $this->info('✅ Google Maps integration working - realistic times!');
                } else {
                    $this->warn('⚠️ Still showing short times - may be fallback or hardcoded data');
                }
            } else {
                $this->error('❌ No pre-hike activities generated');
            }

            // Now test the RefactoredItineraryController
            $this->line('\n🌐 Testing RefactoredItineraryController...');
            $controller = new RefactoredItineraryController($service);
            
            // Create a fake request
            $request = new Request();
            $request->merge([
                'itinerary' => $itineraryData,
                'trail' => $trail,
                'build' => $buildData
            ]);
            
            // This might show us where the disconnect is
            $this->line('📝 Controller test would require actual HTTP request simulation');
            
        } catch (\Exception $e) {
            $this->error('❌ Test failed: ' . $e->getMessage());
            $this->line('Stack trace: ' . $e->getTraceAsString());
        }
        
        return 0;
    }
}
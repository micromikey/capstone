<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\ItineraryController;
use Illuminate\Http\Request;

class TestItineraryWebGeneration extends Command
{
    protected $signature = 'test:web-itinerary';
    protected $description = 'Test web itinerary generation to see pre-hike activities';

    public function handle()
    {
        $this->info('🌐 WEB ITINERARY GENERATION TEST');
        $this->info('=================================');
        $this->newLine();

        try {
            // Create a mock request with itinerary data
            $itineraryData = [
                'duration_days' => 3,
                'nights' => 2,
                'start_date' => '2025-09-29',
                'selected_trail' => 'Mt. Pulag Multi-Day Trek'
            ];

            $trailData = [
                'id' => 1,
                'name' => 'Mt. Pulag Multi-Day Trek',
                'distance_km' => 15.5,
                'transport_included' => true,
                'transport_details' => 'Van transportation from Baguio City',
                'departure_point' => 'Baguio City Terminal',
                'pickup_time' => '07:00', // Test with 7:00 AM pickup time
                'package' => [
                    'duration' => '60 hours'
                ]
            ];

            $this->info("Testing itinerary generation with:");
            $this->info("  Trail: {$trailData['name']}");
            $this->info("  Duration: 3 days, 2 nights");
            $this->info("  Transport: " . ($trailData['transport_included'] ? 'Included' : 'Self-arrange'));
            $this->newLine();

            // Test the service directly
            $itineraryService = app(\App\Services\ItineraryGeneratorService::class);
            
            $buildData = [
                'user_lat' => 14.6417,    // Bataan
                'user_lng' => 120.4736,   // Bataan  
                'user_location' => 'Bataan, Philippines',
                'start_date' => now()->addDays(7)->format('Y-m-d')
            ];

            $result = $itineraryService->generateItinerary($itineraryData, $trailData, $buildData, []);

            $this->info('✅ Service generates pre-hike activities: ' . (isset($result['preHikeActivities']) && !empty($result['preHikeActivities']) ? 'YES' : 'NO'));
            
            if (isset($result['preHikeActivities'])) {
                $this->info('📋 Pre-hike activities count: ' . count($result['preHikeActivities']));
                
                foreach ($result['preHikeActivities'] as $activity) {
                    $timeStr = sprintf('%02d:%02d', 
                        intval($activity['minutes'] / 60), 
                        $activity['minutes'] % 60
                    );
                    $this->info("  {$timeStr} - {$activity['title']} ({$activity['location']})");
                }
            }

            $this->newLine();
            $this->info('💡 To see the pre-hike activities in the web interface:');
            $this->info('  1. Go to /hiker/itinerary/build');
            $this->info('  2. Select "Mt. Pulag Multi-Day Trek"');
            $this->info('  3. Generate itinerary');
            $this->info('  4. Look for the "Pre-hike Transportation" section above Day 1');

        } catch (\Exception $e) {
            $this->error("❌ Error: " . $e->getMessage());
            $this->error("Stack trace: " . $e->getTraceAsString());
        }
    }
}
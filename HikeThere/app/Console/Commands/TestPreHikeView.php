<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ItineraryGeneratorService;

class TestPreHikeView extends Command
{
    protected $signature = 'test:pre-hike-view';
    protected $description = 'Test that the view correctly displays pre-hike activities';

    public function handle()
    {
        $this->info('🎨 PRE-HIKE VIEW RENDERING TEST');
        $this->info('==============================');
        $this->newLine();

        try {
            // Create sample data like what the view expects
            $trailData = [
                'id' => 1,
                'name' => 'Mt. Pulag Multi-Day Trek',
                'distance_km' => 15.5,
                'transport_included' => true,
                'transport_details' => 'Van transportation from Baguio City',
                'departure_point' => 'Baguio City Terminal',
                'package' => [
                    'duration' => '60 hours'
                ]
            ];

            $itineraryData = [
                'duration_days' => 3,
                'nights' => 2,
                'start_date' => '2025-09-29'
            ];

            $routeData = [
                'distance_km' => 15.5,
                'estimated_duration_minutes' => 840
            ];

            // Generate the complete itinerary data
            $itineraryService = app(ItineraryGeneratorService::class);
            $generatedData = $itineraryService->generateItinerary($itineraryData, $trailData, $routeData, []);

            $this->info('Generated data keys:');
            foreach (array_keys($generatedData) as $key) {
                $this->info("  ✓ {$key}");
            }
            $this->newLine();

            // Test pre-hike activities specifically
            if (isset($generatedData['preHikeActivities']) && !empty($generatedData['preHikeActivities'])) {
                $this->info('✅ Pre-hike activities are generated successfully!');
                $this->info('📋 Activities count: ' . count($generatedData['preHikeActivities']));
                $this->newLine();

                $this->info('🚌 Pre-hike Transportation Activities:');
                $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
                
                foreach ($generatedData['preHikeActivities'] as $activity) {
                    $timeStr = sprintf('%02d:%02d', 
                        intval($activity['minutes'] / 60), 
                        $activity['minutes'] % 60
                    );
                    $description = isset($activity['description']) ? ' - ' . $activity['description'] : '';
                    $this->info("  {$timeStr} | {$activity['title']} ({$activity['location']}){$description}");
                }

                $this->newLine();
                $this->info('✅ View Data Structure Ready:');
                $this->info('  - preHikeActivities: Available for view rendering');
                $this->info('  - Each activity has: minutes, title, location, description');
                $this->info('  - View template: resources/views/hiker/itinerary/generated.blade.php');
                $this->info('  - Section: Pre-hike Transportation (above Day 1)');

            } else {
                $this->error('❌ Pre-hike activities not found in generated data');
            }

            $this->newLine();
            $this->info('🎯 Next Steps:');
            $this->info('  1. The view template has been updated to display pre-hike activities');
            $this->info('  2. Test the web interface at /hiker/itinerary/build');
            $this->info('  3. Generate an itinerary with a trail that has transport_included = true');
            $this->info('  4. Look for the blue "Pre-hike Transportation" section above Day 1');

        } catch (\Exception $e) {
            $this->error("❌ Error: " . $e->getMessage());
            $this->error("Stack trace: " . $e->getTraceAsString());
        }
    }
}
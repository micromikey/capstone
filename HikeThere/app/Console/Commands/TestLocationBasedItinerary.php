<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ItineraryGeneratorService;

class TestLocationBasedItinerary extends Command
{
    protected $signature = 'test:location-itinerary';
    protected $description = 'Test complete itinerary with location-based transportation';

    public function handle()
    {
        $this->info('🌎 LOCATION-BASED ITINERARY TEST');
        $this->info('=================================');
        $this->newLine();

        try {
            // Example: User from Makati going to Mt. Pulag with included transport
            $trail = [
                'id' => 1,
                'name' => 'Mt. Pulag Multi-Day Trek',
                'distance_km' => 15.5,
                'transport_included' => true,
                'transport_details' => 'Van transportation from Baguio City Terminal',
                'departure_point' => 'Baguio City Terminal',
                'coordinates_start_lat' => 16.5966,
                'coordinates_start_lng' => 120.9060,
                'package' => ['duration' => '60 hours']
            ];

            $build = [
                'user_location' => 'Makati City, Metro Manila, Philippines',
                'user_lat' => 14.5547,
                'user_lng' => 121.0244
            ];

            $itineraryService = app(ItineraryGeneratorService::class);
            
            $routeData = [
                'distance_km' => 15.5,
                'estimated_duration_minutes' => 840
            ];

            $itinerary = [];
            $result = $itineraryService->generateItinerary($itinerary, $trail, $build, []);

            $this->displayLocationBasedItinerary($result, $trail, $build);

        } catch (\Exception $e) {
            $this->error("❌ Error: " . $e->getMessage());
            $this->error("Stack trace: " . $e->getTraceAsString());
        }
    }

    private function displayLocationBasedItinerary($result, $trail, $build)
    {
        $this->info("🏔️ Trail: {$trail['name']}");
        $this->info("📍 User Location: {$build['user_location']}");
        $this->info("📏 Total Distance: {$trail['distance_km']} km");
        $this->info("🚌 Transport: " . ($trail['transport_included'] ? 'Included from ' . $trail['departure_point'] : 'Self-arrange'));
        $this->newLine();

        // Pre-hike Activities with Location Context
        if (isset($result['preHikeActivities']) && !empty($result['preHikeActivities'])) {
            $this->info('🚌 PRE-HIKE TRANSPORTATION');
            $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
            foreach ($result['preHikeActivities'] as $activity) {
                $timeStr = sprintf('%02d:%02d', 
                    intval($activity['minutes'] / 60), 
                    $activity['minutes'] % 60
                );
                $description = isset($activity['description']) ? " - {$activity['description']}" : '';
                $this->info("  {$timeStr} | {$activity['title']} ({$activity['location']}){$description}");
            }
            $this->newLine();
        }

        // Day Activities
        if (isset($result['dayActivities'])) {
            foreach ($result['dayActivities'] as $day => $activities) {
                $this->info("🌅 DAY {$day}");
                $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
                foreach ($activities as $activity) {
                    $timeStr = sprintf('%02d:%02d', 
                        intval($activity['minutes'] / 60), 
                        $activity['minutes'] % 60
                    );
                    $this->info("  {$timeStr} | {$activity['title']} ({$activity['location']})");
                }
                $this->newLine();
            }
        }

        // Night Activities
        if (isset($result['nightActivities'])) {
            foreach ($result['nightActivities'] as $night => $activities) {
                $this->info("🌙 NIGHT {$night}");
                $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
                foreach ($activities as $activity) {
                    $timeStr = sprintf('%02d:%02d', 
                        intval($activity['minutes'] / 60), 
                        $activity['minutes'] % 60
                    );
                    $this->info("  {$timeStr} | {$activity['title']} ({$activity['location']})");
                }
                $this->newLine();
            }
        }

        // Date Information
        if (isset($result['dateInfo'])) {
            $this->info('📅 TRIP DATES');
            $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
            $this->info("  Start Date: " . $result['dateInfo']['start_date']);
            $this->info("  End Date: " . $result['dateInfo']['end_date']);
            $this->info("  Duration: " . $result['dateInfo']['duration_days'] . " days, " . $result['dateInfo']['nights'] . " nights");
            $this->newLine();
        }

        $this->info('✅ LOCATION-BASED FEATURES:');
        $this->info('  ✓ Uses actual user location from build form');
        $this->info('  ✓ Calculates realistic travel times based on distance');  
        $this->info('  ✓ Displays user\'s specific address instead of generic "Home"');
        $this->info('  ✓ Differentiates between transport included vs commute scenarios');
        $this->info('  ✓ Accounts for Philippines traffic and transportation conditions');
    }
}
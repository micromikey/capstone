<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ItineraryGeneratorService;
use App\Models\Trail;

class TestDatabaseTrailTransport extends Command
{
    protected $signature = 'test:database-trail-transport';
    protected $description = 'Test pre-hike transportation using real database trail data';

    protected $itineraryService;

    public function __construct(ItineraryGeneratorService $itineraryService)
    {
        parent::__construct();
        $this->itineraryService = $itineraryService;
    }

    public function handle()
    {
        $this->info('🏔️ DATABASE TRAIL TRANSPORT TEST');
        $this->info('==================================');
        $this->newLine();

        // Fetch trails from database with package data
        $trails = Trail::with('package')->whereHas('package')->get();
        
        if ($trails->isEmpty()) {
            $this->error('No trails with packages found in database');
            return;
        }

        foreach ($trails as $trail) {
            $this->info("=== TESTING TRAIL ID {$trail->id} ===");
            $this->info("Trail Name: " . ($trail->name ?: 'Unnamed'));
            
            if (!$trail->package) {
                $this->warn("No package data found for this trail");
                continue;
            }
            
            $this->info("Package ID: {$trail->package->id}");
            $this->info("Pickup Time: " . ($trail->package->pickup_time ?: 'Not set'));
            $this->info("Departure Time: " . ($trail->package->departure_time ?: 'Not set'));
            $this->info("Transport Included: " . ($trail->package->transport_included ? 'Yes' : 'No'));
            
            $this->testTrailTransportGeneration($trail);
            $this->newLine();
        }

        $this->info('✅ Database trail tests complete!');
    }

    private function testTrailTransportGeneration($trail)
    {
        try {
            // Debug trail transport settings
            $this->info("DEBUG - Trail transport_included: " . ($trail->transport_included ? 'true' : 'false'));
            $this->info("DEBUG - Package transport_included: " . ($trail->package->transport_included ? 'true' : 'false'));
            
            // Create route data for the test with user location
            $routeData = [
                'distance_km' => 8.5, // Default distance
                'estimated_duration_minutes' => 480,
                'start_location' => 'Manila',
                'user_location' => 'Manila'
            ];

            $itinerary = [];
            $build = $routeData;
            $result = $this->itineraryService->generateItinerary($itinerary, $trail, $build, []);

            $this->info("DEBUG - Result keys: " . implode(', ', array_keys($result)));

            if (isset($result['preHikeActivities']) && !empty($result['preHikeActivities'])) {
                $this->info("📋 PRE-HIKE TRANSPORT ACTIVITIES:");
                
                foreach ($result['preHikeActivities'] as $index => $activity) {
                    $this->info("DEBUG Activity {$index}: " . json_encode($activity, JSON_PRETTY_PRINT));
                    
                    $timeDisplay = $activity['time_display'] ?? 'No time';
                    $activityName = $activity['activity'] ?? 'Unknown activity';
                    $location = $activity['location'] ?? 'Unknown location';
                    
                    $this->line("  • {$timeDisplay} - {$activityName} ({$location})");
                }
            } else {
                $this->warn("No pre-hike transport activities generated");
                $this->info("DEBUG - preHikeActivities result: " . var_export($result['preHikeActivities'] ?? 'not set', true));
            }

        } catch (\Exception $e) {
            $this->error("Error generating itinerary: " . $e->getMessage());
            $this->error("Stack trace: " . $e->getTraceAsString());
        }
    }
}
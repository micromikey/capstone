<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ItineraryGeneratorService;
use App\Models\Trail;

class TestSpecificTrail extends Command
{
    protected $signature = 'test:specific-trail {trail_id}';
    protected $description = 'Test a specific trail transport generation with detailed debugging';

    protected $itineraryService;

    public function __construct(ItineraryGeneratorService $itineraryService)
    {
        parent::__construct();
        $this->itineraryService = $itineraryService;
    }

    public function handle()
    {
        $trailId = $this->argument('trail_id');
        
        $this->info("🔍 TESTING SPECIFIC TRAIL ID {$trailId}");
        $this->info('=====================================');
        $this->newLine();

        $trail = Trail::with('package')->find($trailId);
        
        if (!$trail) {
            $this->error("Trail ID {$trailId} not found");
            return;
        }

        $this->info("Trail Name: " . ($trail->name ?: 'Unnamed'));
        
        if (!$trail->package) {
            $this->error("No package data found for this trail");
            return;
        }
        
        $this->info("Package ID: {$trail->package->id}");
        $this->info("Pickup Time: " . ($trail->package->pickup_time ?: 'Not set'));
        $this->info("Departure Time: " . ($trail->package->departure_time ?: 'Not set'));
        $this->info("Transport Included: " . ($trail->package->transport_included ? 'Yes' : 'No'));
        $this->info("Trail Accessor transport_included: " . ($trail->transport_included ? 'Yes' : 'No'));
        
        $this->newLine();
        
        // Test getTrailTimes method directly
        $this->info("=== TESTING getTrailTimes METHOD ===");
        
        $service = $this->itineraryService;
        $reflection = new \ReflectionClass($service);
        $method = $reflection->getMethod('getTrailTimes');
        $method->setAccessible(true);
        
        $times = $method->invoke($service, $trail);
        $this->info("Extracted pickup_time: " . ($times['pickup_time'] ?: 'null'));
        $this->info("Extracted departure_time: " . ($times['departure_time'] ?: 'null'));
        
        // Test time conversion
        $convertMethod = $reflection->getMethod('convertTimeToMinutes');
        $convertMethod->setAccessible(true);
        
        if ($times['pickup_time']) {
            $pickupMinutes = $convertMethod->invoke($service, $times['pickup_time']);
            $this->info("Pickup time in minutes: {$pickupMinutes}");
        }
        
        if ($times['departure_time']) {
            $departureMinutes = $convertMethod->invoke($service, $times['departure_time']);
            $this->info("Departure time in minutes: {$departureMinutes}");
        }
        
        $this->newLine();
        
        // Test full itinerary generation
        $this->info("=== TESTING FULL ITINERARY GENERATION ===");
        
        // Let's also manually test the specific method call
        $this->info("=== MANUAL METHOD TEST ===");
        $service = $this->itineraryService;
        $reflection = new \ReflectionClass($service);
        
        // Mock user location data
        $userLocation = ['name' => 'Manila', 'lat' => 14.5995, 'lng' => 120.9842];
        $departurePoint = 'Pickup Location';
        
        // Test generateIncludedTransportActivities directly
        try {
            $method = $reflection->getMethod('generateIncludedTransportActivities');
            $method->setAccessible(true);
            
            $this->info("Calling generateIncludedTransportActivities with trail ID {$trail->id}...");
            $activities = $method->invoke($service, $trail, $departurePoint, $userLocation);
            
            $this->info("Returned " . count($activities) . " activities");
            
            // Show all activities from direct method call
            $this->info("🔍 ALL ACTIVITIES FROM DIRECT METHOD:");
            foreach ($activities as $index => $activity) {
                $this->info("   Activity {$index}: type={$activity['type']}, minutes={$activity['minutes']}, title={$activity['title']}");
            }
            
            // Find the meetup activity
            $foundMeetup = false;
            foreach ($activities as $activity) {
                if ($activity['type'] === 'meetup') {
                    $this->info("🎯 DIRECT METHOD MEETUP RESULT:");
                    $this->info("   Minutes: {$activity['minutes']}");
                    $this->info("   Title: {$activity['title']}");
                    $foundMeetup = true;
                    break;
                }
            }
            
            if (!$foundMeetup) {
                $this->warn("No meetup activity found in direct method call");
            }
            
        } catch (\Exception $e) {
            $this->error("Error calling generateIncludedTransportActivities: " . $e->getMessage());
            $this->error("Stack trace: " . $e->getTraceAsString());
        }
        
        $this->testTrailTransportGeneration($trail);
    }

    private function testTrailTransportGeneration($trail)
    {
        try {
            $routeData = [
                'distance_km' => 8.5,
                'estimated_duration_minutes' => 480,
                'start_location' => 'Manila',
                'user_location' => 'Manila'
            ];

            $itinerary = [];
            $build = $routeData;
            $result = $this->itineraryService->generateItinerary($itinerary, $trail, $build, []);

            if (isset($result['preHikeActivities']) && !empty($result['preHikeActivities'])) {
                $this->info("📋 PRE-HIKE TRANSPORT ACTIVITIES:");
                
                foreach ($result['preHikeActivities'] as $index => $activity) {
                    if ($activity['type'] === 'meetup') {
                        $this->info("🎯 MEETUP ACTIVITY FOUND:");
                        $this->info("   Minutes: {$activity['minutes']}");
                        $this->info("   Title: {$activity['title']}");
                        $this->info("   Description: {$activity['description']}");
                        
                        // Convert minutes to time display
                        $hours = floor($activity['minutes'] / 60);
                        $mins = $activity['minutes'] % 60;
                        $timeDisplay = sprintf('%02d:%02d', $hours % 24, $mins);
                        $dayOffset = floor($hours / 24);
                        
                        $this->info("   Time Display: {$timeDisplay}" . ($dayOffset > 0 ? " (Day {$dayOffset})" : ""));
                        break;
                    }
                }
            } else {
                $this->warn("No pre-hike transport activities generated");
            }

        } catch (\Exception $e) {
            $this->error("Error generating itinerary: " . $e->getMessage());
        }
    }
}
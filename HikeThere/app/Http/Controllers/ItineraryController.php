<?php

namespace App\Http\Controllers;

use App\Models\Itinerary;
use App\Models\Trail;
use App\Services\HybridRoutingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ItineraryController extends Controller
{
    protected $routingService;

    public function __construct(HybridRoutingService $routingService)
    {
        $this->routingService = $routingService;
    }

    public function build()
    {
        // Check if user has completed assessment
        $hasAssessment = Auth::user()->latestAssessmentResult()->exists();

        if (! $hasAssessment) {
            return redirect()->route('assessment.instruction')
                ->with('warning', 'Please complete the Pre-Hike Self-Assessment first to generate a personalized itinerary.');
        }

        // Get available trails for suggestions
        $trails = Trail::with('location')->active()->get();

        // Get user's latest assessment for personalized recommendations
        $assessment = Auth::user()->latestAssessmentResult;

        return view('hiker.itinerary.build', compact('trails', 'assessment'));
    }

    public function generate(Request $request)
    {
        $user = Auth::user();
        $assessment = $user->latestAssessmentResult;

        if (! $assessment) {
            return redirect()->route('assessment.instruction')
                ->with('warning', 'Please complete the Pre-Hike Self-Assessment first.');
        }

        // Validate the request
        $validator = Validator::make($request->all(), [
            'trail_name' => 'required|string|max:255',
            'user_location' => 'nullable|string|max:500',
            'time' => 'required|date_format:H:i',
            'date' => 'required|date|after:today',
            'transportation' => 'required|string',
            'trail' => 'nullable|string',
            'stopovers.*' => 'nullable|string|max:255',
            'sidetrips.*' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            // Generate itinerary based on assessment results and user preferences
            $itinerary = $this->generatePersonalizedItinerary($assessment, $request);

            // Save the itinerary
            $savedItinerary = Itinerary::create([
                'user_id' => $user->id,
                'title' => $itinerary['title'],
                'trail_name' => $itinerary['trail_name'],
                'user_location' => $itinerary['user_location'],
                'difficulty_level' => $itinerary['difficulty_level'],
                'estimated_duration' => $itinerary['estimated_duration'],
                'distance' => $itinerary['distance'],
                'elevation_gain' => $itinerary['elevation_gain'],
                'best_time_to_hike' => $itinerary['best_time_to_hike'],
                'weather_conditions' => $itinerary['weather_conditions'],
                'gear_recommendations' => $itinerary['gear_recommendations'],
                'safety_tips' => $itinerary['safety_tips'],
                'route_description' => $itinerary['route_description'],
                'waypoints' => $itinerary['waypoints'],
                'emergency_contacts' => $itinerary['emergency_contacts'],
                'schedule' => $itinerary['schedule'],
                'stopovers' => $itinerary['stopovers'],
                'sidetrips' => $itinerary['sidetrips'],
                'transportation' => $itinerary['transportation'],
                'route_coordinates' => $itinerary['route_coordinates'],
                'daily_schedule' => $itinerary['daily_schedule'],
                'transport_details' => $itinerary['transport_details'],
                'departure_info' => $itinerary['departure_info'],
                'arrival_info' => $itinerary['arrival_info'],
                'route_data' => $itinerary['route_data'] ?? [],
                'route_summary' => $itinerary['route_summary'] ?? [],
            ]);

            return redirect()->route('itinerary.show', $savedItinerary)
                ->with('success', 'Your personalized itinerary has been generated successfully!');

        } catch (\Exception $e) {
            Log::error('Itinerary generation failed', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->back()
                ->withErrors(['error' => 'Failed to generate itinerary. Please try again.'])
                ->withInput();
        }
    }

    public function show(Itinerary $itinerary)
    {
        // Check if user owns this itinerary
        if ($itinerary->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to itinerary.');
        }

        return view('hiker.itinerary.generated', compact('itinerary'));
    }

    public function pdf(Itinerary $itinerary)
    {
        // Check if user owns this itinerary
        if ($itinerary->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to itinerary.');
        }

        return view('hiker.itinerary.pdf', compact('itinerary'));
    }

    private function generatePersonalizedItinerary($assessment, $request)
    {
        // Get user preferences
        $user = Auth::user();
        $preferences = $user->preferences;

        // Determine difficulty level based on assessment scores
        $difficultyLevel = $this->determineDifficultyLevel($assessment);

        // Get trail preferences from request - FIX: Use trail field if trail_name is empty
        $trailName = $request->input('trail_name') ?: $request->input('trail');
        $userLocation = $request->input('user_location') ?: Auth::user()->location ?: 'Location not specified';
        $time = $request->input('time');
        $date = $request->input('date');
        $transportation = $request->input('transportation');
        $selectedTrail = $request->input('trail');
        $stopovers = $request->input('stopovers', []);
        $sidetrips = $request->input('sidetrips', []);

        // Validate that we have a trail name
        if (empty($trailName)) {
            throw new \Exception('Trail name is required to generate itinerary');
        }

        // Get trail details for accurate information
        $trail = $this->getTrailDetails($trailName, $selectedTrail);

        // Generate route using Google Directions API - FIXED: Pass transportation parameter
        $routeData = $this->generateRouteData($userLocation, $trail, $stopovers, $sidetrips, $transportation);

        // Generate gear recommendations based on assessment
        $gearRecommendations = $this->generateGearRecommendations($assessment);

        // Generate safety tips based on assessment
        $safetyTips = $this->generateSafetyTips($assessment);

        // Generate detailed schedule
        $schedule = $this->generateDetailedSchedule($time, $date, $routeData, $stopovers, $sidetrips);

        // Generate daily schedule with weather integration - FIXED: Pass transportation parameter
        $dailySchedule = $this->generateDailySchedule($date, $time, $routeData, $trail, $transportation);

        // Store route data in the daily schedule for access in the model
        if (! empty($dailySchedule) && ! empty($routeData)) {
            $dailySchedule[0]['route_data'] = $routeData;
        }

        // Debug logging to see what's being generated
        Log::info('Itinerary generation debug', [
            'routeData' => $routeData,
            'dailySchedule' => $dailySchedule,
            'stopovers' => $stopovers,
            'sidetrips' => $sidetrips,
            'transportation' => $transportation,
        ]);

        // Ensure all required fields are present
        if (! empty($dailySchedule)) {
            foreach ($dailySchedule as &$day) {
                if (! isset($day['day_label'])) {
                    $day['day_label'] = 'Day 1';
                }
                if (! isset($day['day_number'])) {
                    $day['day_number'] = 1;
                }
            }
        }

        // Calculate separate durations
        $travelDuration = $this->calculateTravelDuration($routeData);
        $hikingDuration = $this->calculateHikingDuration($trail);
        $totalTripDuration = $this->calculateTotalTripDuration($travelDuration, $hikingDuration);

        return [
            'title' => "Personalized {$trailName} Itinerary",
            'trail_name' => $trailName,
            'user_location' => $userLocation,
            'difficulty_level' => $difficultyLevel,
            'estimated_duration' => $this->formatDuration($hikingDuration), // Only hiking duration
            'travel_duration' => $this->formatDuration($travelDuration), // Separate travel duration
            'total_trip_duration' => $this->formatDuration($totalTripDuration), // Combined duration
            'distance' => $routeData['total_distance'] ?? 'Based on route planning',
            'elevation_gain' => $trail['elevation_gain'] ?? $this->getElevationForDifficulty($difficultyLevel),
            'best_time_to_hike' => $time,
            'weather_conditions' => $this->getWeatherConditions($date),
            'gear_recommendations' => $gearRecommendations,
            'safety_tips' => $safetyTips,
            'route_description' => $this->generateRouteDescription($difficultyLevel, $trailName, $routeData),
            'waypoints' => $this->generateWaypoints($difficultyLevel, $stopovers, $sidetrips, $routeData),
            'static_map_url' => $this->generateStaticMapUrl($routeData),
            'emergency_contacts' => $this->getEmergencyContacts($user),
            'schedule' => $schedule,
            'stopovers' => $stopovers,
            'sidetrips' => $sidetrips,
            'transportation' => $transportation,
            'route_coordinates' => $routeData['route_coordinates'] ?? [],
            'daily_schedule' => $dailySchedule,
            'transport_details' => $routeData['transport_details'] ?? [],
            'route_summary' => [
                'departure' => $userLocation,
                'destination' => $trail['location'] ?? 'Trail destination',
                'transportation' => $transportation,
                'total_distance' => $routeData['total_distance'] ?? 'Based on route planning',
                'travel_duration' => $this->formatDuration($travelDuration),
                'hiking_duration' => $this->formatDuration($hikingDuration),
            ],
            'departure_info' => [
                'date' => $date,
                'time' => $time,
                'location' => $userLocation,
                'coordinates' => $routeData['origin_coordinates'] ?? null,
            ],
            'arrival_info' => [
                'trail_name' => $trailName,
                'location' => $trail['location'] ?? 'Trail destination',
                'coordinates' => $routeData['destination_coordinates'] ?? null,
                'difficulty' => $difficultyLevel,
            ],
            'route_data' => $routeData,
        ];
    }

    private function getTrailDetails($trailName, $selectedTrail)
    {
        // Try to find trail in database
        $trail = Trail::where('trail_name', $trailName)
            ->orWhere('trail_name', 'like', '%'.$trailName.'%')
            ->with('location')
            ->first();

        if ($trail) {
            // Build comprehensive location string
            $locationString = 'Location N/A';
            if ($trail->location) {
                $locationParts = [];
                if ($trail->location->name) {
                    $locationParts[] = $trail->location->name;
                }
                if ($trail->location->province) {
                    $locationParts[] = $trail->location->province;
                }
                if ($trail->location->region) {
                    $locationParts[] = $trail->location->region;
                }
                if ($trail->location->country) {
                    $locationParts[] = $trail->location->country;
                }

                if (! empty($locationParts)) {
                    $locationString = implode(', ', $locationParts);
                }
            } elseif ($trail->mountain_name) {
                $locationString = $trail->mountain_name;
            }

            return [
                'id' => $trail->id,
                'name' => $trail->trail_name,
                'difficulty' => $trail->difficulty,
                'length' => $trail->length,
                'elevation_gain' => $trail->elevation_gain,
                'estimated_time' => $trail->estimated_time,
                'location' => $locationString,
                'coordinates' => $trail->coordinates,
                'features' => $trail->features,
                'summary' => $trail->summary,
                'best_season' => $trail->best_season,
                'terrain_notes' => $trail->terrain_notes,
                'permit_required' => $trail->permit_required,
                'departure_point' => $trail->departure_point,
            ];
        }

        // Fallback for custom trails with more realistic data
        return [
            'name' => $trailName,
            'difficulty' => 'beginner',
            'length' => '5-10 km',
            'elevation_gain' => '200-500m',
            'estimated_time' => '3-5 hours',
            'location' => 'Custom trail location',
            'coordinates' => null,
            'features' => ['scenic views', 'nature trail'],
            'summary' => 'Custom hiking trail - please verify details before departure',
            'best_season' => 'Year-round',
            'terrain_notes' => 'Mixed terrain',
            'permit_required' => false,
            'departure_point' => 'To be determined',
        ];
    }

    private function generateRouteData($userLocation, $trail, $stopovers, $sidetrips, $transportation)
    {
        try {
            $origin = $userLocation;
            $destination = $trail['location'] ?? 'Trail destination';
            $trailName = $trail['name'] ?? null;

            // Combine all waypoints
            $allWaypoints = array_merge($stopovers, $sidetrips);

            // Use your hybrid routing service for all transportation modes
            $routeData = $this->routingService->getBestRoute(
                $origin,
                $destination,
                $allWaypoints,
                $transportation,
                $trailName
            );

            if ($routeData) {
                // Add stopovers and sidetrips to the route data
                $routeData['stopovers'] = $stopovers;
                $routeData['sidetrips'] = $sidetrips;
                $routeData['transportation'] = $transportation;

                Log::info('Route data generated successfully using hybrid routing', [
                    'transportation' => $transportation,
                    'provider' => $routeData['primary_provider'] ?? 'unknown',
                    'strategy' => $routeData['routing_strategy'] ?? 'unknown',
                    'hasLegs' => isset($routeData['legs']),
                    'legsCount' => isset($routeData['legs']) ? count($routeData['legs']) : 0,
                ]);

                return $routeData;
            }

        } catch (\Exception $e) {
            Log::error('Route generation failed', [
                'error' => $e->getMessage(),
                'origin' => $userLocation,
                'destination' => $trail['location'] ?? 'Unknown',
                'transportation' => $transportation,
            ]);
        }

        // If routing service fails, return minimal data
        Log::warning('Routing service failed, returning minimal route data');

        return [
            'total_distance' => 'Route calculation failed',
            'total_duration' => 'Route calculation failed',
            'legs' => [],
            'stopovers' => $stopovers,
            'sidetrips' => $sidetrips,
            'transportation' => $transportation,
            'primary_provider' => 'none',
            'routing_strategy' => 'fallback',
        ];
    }

    // Removed hardcoded fallback route generation - now using your APIs

    // Removed all hardcoded transit generation methods - now using your APIs

    // Removed estimateTransitCost - now using your APIs

    private function generateDetailedSchedule($time, $date, $routeData, $stopovers, $sidetrips)
    {
        $departureTime = \Carbon\Carbon::parse("$date $time");
        $totalDuration = $routeData['total_duration_seconds'] ?? 7200; // Default 2 hours

        $estimatedArrival = $departureTime->copy()->addSeconds($totalDuration);

        return [
            'date' => $date,
            'start_time' => $time,
            'departure_time' => $departureTime->format('H:i'),
            'estimated_arrival' => $estimatedArrival->format('H:i'),
            'total_duration' => $routeData['total_duration'] ?? '2 hours',
            'total_distance' => $routeData['total_distance'] ?? 'Distance TBD',
            'stopovers' => $stopovers,
            'sidetrips' => $sidetrips,
        ];
    }

    // Enhanced comprehensive daily schedule generation
    private function generateDailySchedule($date, $startTime, $routeData, $trail, $transportation)
    {
        $scheduleDate = \Carbon\Carbon::parse($date);
        $currentTime = \Carbon\Carbon::parse("$date $startTime");
        $userLocation = Auth::user()->location ?? 'Your current location';

        // Fetch weather data for the planned date
        $weatherData = $this->fetchWeatherForDate($date, $trail);

        Log::info('Enhanced daily schedule generation started', [
            'startTime' => $startTime,
            'transportation' => $transportation,
            'hasRouteData' => ! empty($routeData),
            'hasLegs' => isset($routeData['legs']) && ! empty($routeData['legs']),
            'legsCount' => isset($routeData['legs']) ? count($routeData['legs']) : 0,
        ]);

        $dailySchedule = [
            [
                'date' => $scheduleDate->format('Y-m-d'),
                'day_label' => 'Day 1',
                'day_number' => 1,
                'activities' => [],
                'total_duration' => $routeData['total_duration'] ?? 'Calculating...',
                'total_distance' => $routeData['total_distance'] ?? 'Calculating...',
                'travel_duration' => $this->formatDuration($this->calculateTravelDuration($routeData)),
                'hiking_duration' => $this->formatDuration($this->calculateHikingDuration($trail)),
            ],
        ];

        // Phase 1: Pre-departure preparation
        $prepTime = $currentTime->copy()->subMinutes(30);
        $dailySchedule[0]['activities'][] = [
            'time' => $prepTime->format('H:i'),
            'location' => $userLocation,
            'description' => 'Pre-departure preparation',
            'condition' => $this->getWeatherCondition($weatherData['departure_temp'] ?? null),
            'temperature' => $weatherData['departure_temp'] ?? '25°C',
            'note' => 'Final gear check, weather update, and preparation',
            'guidelines' => [
                'Check weather conditions and forecasts',
                'Verify all hiking gear is packed',
                'Ensure phone is fully charged',
                'Check trail conditions and updates',
                'Inform emergency contact of your plans',
                'Pack extra water and snacks',
            ],
            'transport_mode' => 'Preparation',
            'duration' => '30 min',
            'activity_type' => 'preparation',
            'coordinates' => $routeData['origin_coordinates'] ?? null,
        ];

        // Phase 2: Departure
        $dailySchedule[0]['activities'][] = [
            'time' => $currentTime->format('H:i'),
            'location' => $userLocation,
            'description' => 'Begin journey to trail',
            'condition' => $this->getWeatherCondition($weatherData['departure_temp'] ?? null),
            'temperature' => $weatherData['departure_temp'] ?? '25°C',
            'note' => 'Start your journey to '.($trail['name'] ?? 'the trail'),
            'guidelines' => [
                'Double-check you have your essentials',
                'Take a photo of your departure time',
                'Start GPS tracking if available',
                'Keep emergency contacts handy',
            ],
            'transport_mode' => 'Departure',
            'duration' => '5 min',
            'activity_type' => 'departure',
            'coordinates' => $routeData['origin_coordinates'] ?? null,
        ];

        // Phase 3: Journey to trail - Enhanced with comprehensive routing
        if (isset($routeData['legs']) && ! empty($routeData['legs'])) {
            $currentTime = $currentTime->copy()->addMinutes(5); // Account for departure prep

            $transportMode = strtolower($transportation) === 'commute' ? 'transit' : 'driving';

            Log::info('Processing route legs', [
                'transportMode' => $transportMode,
                'legsCount' => count($routeData['legs']),
            ]);

            if ($transportMode === 'transit') {
                $this->addEnhancedCommuteActivities($dailySchedule[0]['activities'], $routeData, $currentTime, $weatherData, $userLocation);
            } else {
                $this->addEnhancedDrivingActivities($dailySchedule[0]['activities'], $routeData, $currentTime, $weatherData);
            }
        } else {
            // Fallback when no route data is available
            $this->addFallbackJourneyActivities($dailySchedule[0]['activities'], $currentTime, $weatherData, $trail, $transportation);
        }

        // Phase 4: Trail arrival and preparation
        $trailArrivalTime = $this->calculateArrivalTime($currentTime, $routeData);
        $dailySchedule[0]['activities'][] = [
            'time' => $trailArrivalTime->format('H:i'),
            'location' => $trail['location'] ?? $trail['name'] ?? 'Trail destination',
            'description' => 'Arrived at trailhead',
            'condition' => $this->getWeatherCondition($weatherData['trail_temp'] ?? null),
            'temperature' => $weatherData['trail_temp'] ?? '23°C',
            'note' => 'Final preparations before starting the hike',
            'guidelines' => [
                'Use restroom facilities if available',
                'Fill water bottles from safe sources',
                'Apply sunscreen and insect repellent',
                'Check trail map and conditions',
                'Take a group photo at the trailhead',
                'Register at trail registry if required',
                'Double-check gear one final time',
            ],
            'transport_mode' => 'Arrival',
            'duration' => '15 min',
            'activity_type' => 'arrival',
            'coordinates' => $routeData['destination_coordinates'] ?? null,
        ];

        // Phase 5: Hiking activities - Enhanced with trail-specific activities
        $hikingStartTime = $trailArrivalTime->copy()->addMinutes(15);
        $this->addHikingActivities($dailySchedule[0]['activities'], $hikingStartTime, $trail, $weatherData);

        // Phase 6: Return journey
        $hikingDuration = $this->calculateHikingDuration($trail);
        $returnStartTime = $hikingStartTime->copy()->addSeconds($hikingDuration);

        $dailySchedule[0]['activities'][] = [
            'time' => $returnStartTime->format('H:i'),
            'location' => $trail['location'] ?? $trail['name'] ?? 'Trail destination',
            'description' => 'Prepare for return journey',
            'condition' => $this->getWeatherCondition($weatherData['trail_temp'] ?? null),
            'temperature' => $weatherData['trail_temp'] ?? '23°C',
            'note' => 'Rest and prepare for the journey home',
            'guidelines' => [
                'Rest and hydrate before leaving',
                'Check all gear is packed',
                'Clean up any trash (Leave No Trace)',
                'Take final photos of the trail',
                'Check transportation schedules',
                'Notify contacts of safe completion',
            ],
            'transport_mode' => 'Rest',
            'duration' => '20 min',
            'activity_type' => 'return_preparation',
            'coordinates' => $routeData['destination_coordinates'] ?? null,
        ];

        // Phase 7: Return journey activities
        $returnJourneyTime = $returnStartTime->copy()->addMinutes(20);
        $this->addReturnJourneyActivities($dailySchedule[0]['activities'], $returnJourneyTime, $routeData, $weatherData, $transportation);

        // Phase 8: Arrival home
        $homeArrivalTime = $this->calculateReturnArrivalTime($returnJourneyTime, $routeData);
        $dailySchedule[0]['activities'][] = [
            'time' => $homeArrivalTime->format('H:i'),
            'location' => $userLocation,
            'description' => 'Arrived home safely',
            'condition' => $this->getWeatherCondition($weatherData['departure_temp'] ?? null),
            'temperature' => $weatherData['departure_temp'] ?? '25°C',
            'note' => 'Journey completed successfully!',
            'guidelines' => [
                'Share your hiking experience with friends',
                'Log your hike in a journal or app',
                'Clean and maintain your gear',
                'Rest and recover',
                'Plan your next adventure!',
            ],
            'transport_mode' => 'Home',
            'duration' => '0 min',
            'activity_type' => 'journey_complete',
            'coordinates' => $routeData['origin_coordinates'] ?? null,
        ];

        Log::info('Enhanced daily schedule generation completed', [
            'totalActivities' => count($dailySchedule[0]['activities']),
            'journeyDuration' => $prepTime->diffInHours($homeArrivalTime).' hours',
        ]);

        return $dailySchedule;
    }

    /**
     * Enhanced commute activities with comprehensive transit information and guidelines
     */
    private function addEnhancedCommuteActivities(&$activities, $routeData, &$currentTime, $weatherData, $userLocation)
    {
        if (! isset($routeData['legs']) || empty($routeData['legs'])) {
            return;
        }

        Log::info('Adding enhanced commute activities', ['legs' => count($routeData['legs'])]);

        foreach ($routeData['legs'] as $legIndex => $leg) {
            if (empty($leg['steps'])) {
                continue;
            }

            foreach ($leg['steps'] as $stepIndex => $step) {
                $travelMode = $step['travel_mode'] ?? 'TRANSIT';

                if ($travelMode === 'TRANSIT' && isset($step['transit_details'])) {
                    $transit = $step['transit_details'];
                    $vehicleType = $transit['line']['vehicle']['name'] ?? 'Public Transport';
                    $lineName = $transit['line']['name'] ?? 'Transit Line';
                    $shortName = $transit['line']['short_name'] ?? '';
                    $departureStop = $transit['departure_stop']['name'] ?? 'Transit Stop';
                    $arrivalStop = $transit['arrival_stop']['name'] ?? 'Destination Stop';
                    $numStops = $transit['num_stops'] ?? 0;
                    $fare = $transit['fare'] ?? null;

                    // Get step duration for accurate timing
                    $stepDuration = $this->extractStepDuration($step, $leg);

                    // Board transit activity
                    $activities[] = [
                        'time' => $currentTime->format('H:i'),
                        'location' => $departureStop,
                        'description' => "Board {$vehicleType} - {$lineName}",
                        'condition' => $this->getWeatherCondition($weatherData['route_temp'] ?? null),
                        'temperature' => $weatherData['route_temp'] ?? '25°C',
                        'transport_mode' => $vehicleType,
                        'note' => "Take {$vehicleType} {$lineName}".($shortName ? " ({$shortName})" : '')." to {$arrivalStop}",
                        'guidelines' => [
                            "Wait at {$departureStop}",
                            $fare ? "Prepare fare: {$fare['text']}" : 'Have exact change ready',
                            'Check vehicle number/route before boarding',
                            'Keep ticket/receipt safe during journey',
                            "Monitor stops - {$numStops} stops to destination",
                            'Have backup payment method ready',
                        ],
                        'activity_type' => 'transit_board',
                        'duration' => '2 min',
                        'coordinates' => $transit['departure_stop']['location'] ?? null,
                        'transit_details' => [
                            'vehicle_type' => $vehicleType,
                            'line_name' => $lineName,
                            'short_name' => $shortName,
                            'departure_stop' => $departureStop,
                            'arrival_stop' => $arrivalStop,
                            'num_stops' => $numStops,
                            'departure_time' => $transit['departure_time']['text'] ?? 'Now',
                            'arrival_time' => $transit['arrival_time']['text'] ?? 'Check schedule',
                            'fare' => $fare,
                            'headsign' => $transit['headsign'] ?? 'Check destination sign',
                        ],
                    ];

                    // Add boarding time
                    $currentTime->addMinutes(2);

                    // Transit journey activity
                    $journeyEndTime = $currentTime->copy()->addSeconds($stepDuration);
                    $activities[] = [
                        'time' => $currentTime->format('H:i'),
                        'location' => "En route: {$lineName}",
                        'description' => "Traveling on {$vehicleType}",
                        'condition' => $this->getWeatherCondition($weatherData['route_temp'] ?? null),
                        'temperature' => $weatherData['route_temp'] ?? '25°C',
                        'transport_mode' => $vehicleType,
                        'note' => 'Journey time: approximately '.gmdate('H:i', $stepDuration),
                        'guidelines' => [
                            'Stay seated and keep belongings secure',
                            'Count stops to avoid missing your destination',
                            'Use travel time to rest or enjoy scenery',
                            'Keep emergency contacts accessible',
                            'Prepare to disembark 1-2 stops before arrival',
                        ],
                        'activity_type' => 'transit_journey',
                        'duration' => gmdate('H:i', $stepDuration),
                        'transit_details' => [
                            'vehicle_type' => $vehicleType,
                            'line_name' => $lineName,
                            'journey_duration' => gmdate('H:i', $stepDuration),
                            'remaining_stops' => $numStops,
                        ],
                    ];

                    // Update current time to journey end
                    $currentTime = $journeyEndTime;

                    // Arrival at stop activity
                    $activities[] = [
                        'time' => $currentTime->format('H:i'),
                        'location' => $arrivalStop,
                        'description' => "Arrive at {$arrivalStop}",
                        'condition' => $this->getWeatherCondition($weatherData['route_temp'] ?? null),
                        'temperature' => $weatherData['route_temp'] ?? '25°C',
                        'transport_mode' => $vehicleType,
                        'note' => "Exit {$vehicleType} at {$arrivalStop}",
                        'guidelines' => [
                            'Prepare to exit before the stop',
                            'Check you have all belongings',
                            'Exit promptly to avoid delays',
                            'Orient yourself at the new location',
                            'Check next connection if applicable',
                        ],
                        'activity_type' => 'transit_arrive',
                        'duration' => '2 min',
                        'coordinates' => $transit['arrival_stop']['location'] ?? null,
                        'transit_details' => [
                            'vehicle_type' => $vehicleType,
                            'line_name' => $lineName,
                            'arrival_stop' => $arrivalStop,
                            'arrival_time' => $transit['arrival_time']['text'] ?? $currentTime->format('H:i'),
                        ],
                    ];

                    $currentTime->addMinutes(2);

                } elseif ($travelMode === 'WALKING') {
                    $distance = $step['distance']['text'] ?? 'Short walk';
                    $duration = $step['duration']['text'] ?? '5 min';
                    $instructions = $step['html_instructions'] ?? '';
                    $startAddress = $step['start_address'] ?? 'Current location';
                    $endAddress = $step['end_address'] ?? 'Destination';

                    $stepDuration = $this->extractStepDuration($step, $leg);

                    $activities[] = [
                        'time' => $currentTime->format('H:i'),
                        'location' => $startAddress,
                        'description' => "Walk to {$endAddress}",
                        'condition' => $this->getWeatherCondition($weatherData['route_temp'] ?? null),
                        'temperature' => $weatherData['route_temp'] ?? '25°C',
                        'transport_mode' => 'Walking',
                        'note' => "Walk {$distance} (approximately {$duration})",
                        'guidelines' => [
                            'Follow walking directions carefully',
                            'Stay on designated walkways',
                            'Be aware of traffic and surroundings',
                            'Use pedestrian crossings where available',
                            'Keep hydrated during the walk',
                            'Take breaks if needed',
                        ],
                        'activity_type' => 'walking',
                        'duration' => $duration,
                        'coordinates' => $step['start_location'] ?? null,
                        'walking_details' => [
                            'distance' => $distance,
                            'duration' => $duration,
                            'instructions' => $this->sanitizeInstruction($instructions),
                            'start_address' => $startAddress,
                            'end_address' => $endAddress,
                        ],
                    ];

                    $currentTime->addSeconds($stepDuration);
                }
            }
        }

        // Add connection time if there are multiple transit legs
        if (count($routeData['legs']) > 1) {
            $activities[] = [
                'time' => $currentTime->format('H:i'),
                'location' => 'Transit connection point',
                'description' => 'Connection between transit routes',
                'condition' => $this->getWeatherCondition($weatherData['route_temp'] ?? null),
                'temperature' => $weatherData['route_temp'] ?? '25°C',
                'transport_mode' => 'Connection',
                'note' => 'Transfer between different transit routes',
                'guidelines' => [
                    'Check departure times for next connection',
                    'Locate the correct platform or stop',
                    'Use this time for restroom break if needed',
                    'Verify route information',
                ],
                'activity_type' => 'transit_connection',
                'duration' => '5 min',
            ];

            $currentTime->addMinutes(5);
        }
    }

    /**
     * Enhanced driving activities with comprehensive route information and guidelines
     */
    private function addEnhancedDrivingActivities(&$activities, $routeData, &$currentTime, $weatherData)
    {
        if (! isset($routeData['legs']) || empty($routeData['legs'])) {
            return;
        }

        Log::info('Adding enhanced driving activities', ['legs' => count($routeData['legs'])]);

        foreach ($routeData['legs'] as $legIndex => $leg) {
            if (empty($leg['steps'])) {
                continue;
            }

            $legStartTime = $currentTime->copy();
            $legDistance = $leg['distance']['text'] ?? '';
            $legDuration = $leg['duration']['text'] ?? '';

            // Add leg start activity
            $activities[] = [
                'time' => $currentTime->format('H:i'),
                'location' => $leg['start_address'] ?? 'Starting point',
                'description' => 'Begin driving segment',
                'condition' => $this->getWeatherCondition($weatherData['route_temp'] ?? null),
                'temperature' => $weatherData['route_temp'] ?? '25°C',
                'transport_mode' => 'Driving',
                'note' => "Drive {$legDistance} (approximately {$legDuration})",
                'guidelines' => [
                    'Check vehicle fuel level and condition',
                    'Ensure GPS/navigation is working',
                    'Adjust mirrors and seat position',
                    'Plan rest stops for long journeys',
                    'Keep emergency kit accessible',
                    'Monitor weather and road conditions',
                ],
                'activity_type' => 'driving_start',
                'duration' => $legDuration,
                'coordinates' => $leg['start_location'] ?? null,
                'driving_details' => [
                    'total_distance' => $legDistance,
                    'estimated_duration' => $legDuration,
                    'route_summary' => $leg['summary'] ?? 'Main route',
                ],
            ];

            // Process major waypoints within the leg
            $significantSteps = $this->filterSignificantDrivingSteps($leg['steps']);

            foreach ($significantSteps as $stepIndex => $step) {
                $distance = $step['distance']['text'] ?? '';
                $duration = $step['duration']['text'] ?? '';
                $instructions = $step['html_instructions'] ?? '';
                $maneuver = $step['maneuver'] ?? '';

                $stepDuration = $this->extractStepDuration($step, $leg);
                $currentTime->addSeconds($stepDuration);

                // Add significant waypoint
                $activities[] = [
                    'time' => $currentTime->format('H:i'),
                    'location' => $this->extractLocationFromInstructions($instructions, $step),
                    'description' => $this->sanitizeInstruction($instructions) ?: 'Continue route',
                    'condition' => $this->getWeatherCondition($weatherData['route_temp'] ?? null),
                    'temperature' => $weatherData['route_temp'] ?? '25°C',
                    'transport_mode' => 'Driving',
                    'note' => "Continue for {$distance} (approximately {$duration})",
                    'guidelines' => [
                        'Follow navigation directions carefully',
                        'Watch for road signs and landmarks',
                        'Maintain safe following distance',
                        'Take breaks if feeling tired',
                        'Monitor fuel consumption',
                    ],
                    'activity_type' => 'driving_waypoint',
                    'duration' => $duration,
                    'coordinates' => $step['end_location'] ?? null,
                    'driving_details' => [
                        'distance' => $distance,
                        'duration' => $duration,
                        'instructions' => $this->sanitizeInstruction($instructions),
                        'maneuver' => $maneuver,
                    ],
                ];
            }

            // Add rest stop if long journey
            $legDurationSeconds = $this->extractDurationSeconds($leg['duration'] ?? []);
            if ($legDurationSeconds > 7200) { // More than 2 hours
                $restTime = $legStartTime->copy()->addSeconds($legDurationSeconds / 2);
                $activities[] = [
                    'time' => $restTime->format('H:i'),
                    'location' => 'Rest area',
                    'description' => 'Recommended rest stop',
                    'condition' => $this->getWeatherCondition($weatherData['route_temp'] ?? null),
                    'temperature' => $weatherData['route_temp'] ?? '25°C',
                    'transport_mode' => 'Rest',
                    'note' => 'Take a break for safety and refreshment',
                    'guidelines' => [
                        'Stretch and walk around',
                        'Use restroom facilities',
                        'Check vehicle if needed',
                        'Stay hydrated',
                        'Rest for 10-15 minutes',
                        'Check GPS and route updates',
                    ],
                    'activity_type' => 'driving_rest',
                    'duration' => '15 min',
                ];
            }
        }
    }

    /**
     * Add fallback journey activities when no route data is available
     */
    private function addFallbackJourneyActivities(&$activities, &$currentTime, $weatherData, $trail, $transportation)
    {
        // Estimate journey time based on transportation type
        $estimatedDuration = $transportation === 'Commute' ? 7200 : 5400; // 2 hours for commute, 1.5 for private

        $activities[] = [
            'time' => $currentTime->format('H:i'),
            'location' => 'En route to trail',
            'description' => "Traveling by {$transportation}",
            'condition' => $this->getWeatherCondition($weatherData['route_temp'] ?? null),
            'temperature' => $weatherData['route_temp'] ?? '25°C',
            'transport_mode' => $transportation,
            'note' => 'Route details will be calculated automatically',
            'guidelines' => [
                'Follow your preferred navigation app',
                'Allow extra time for unexpected delays',
                'Keep emergency contacts handy',
                'Monitor weather conditions',
                'Take breaks as needed',
            ],
            'activity_type' => $transportation === 'Commute' ? 'fallback_transit' : 'fallback_driving',
            'duration' => gmdate('H:i', $estimatedDuration),
        ];

        $currentTime->addSeconds($estimatedDuration);
    }

    /**
     * Calculate arrival time at trail based on route data
     */
    private function calculateArrivalTime($departureTime, $routeData)
    {
        if (isset($routeData['total_duration_seconds'])) {
            return $departureTime->copy()->addSeconds($routeData['total_duration_seconds']);
        }

        if (isset($routeData['legs'])) {
            $totalSeconds = 0;
            foreach ($routeData['legs'] as $leg) {
                if (isset($leg['duration']['value'])) {
                    $totalSeconds += $leg['duration']['value'];
                }
            }

            if ($totalSeconds > 0) {
                return $departureTime->copy()->addSeconds($totalSeconds);
            }
        }

        // Fallback: assume 2 hours
        return $departureTime->copy()->addHours(2);
    }

    /**
     * Add comprehensive hiking activities based on trail information
     */
    private function addHikingActivities(&$activities, &$currentTime, $trail, $weatherData)
    {
        // Hiking start
        $activities[] = [
            'time' => $currentTime->format('H:i'),
            'location' => $trail['location'] ?? $trail['name'] ?? 'Trail start',
            'description' => 'Begin hiking',
            'condition' => $this->getWeatherCondition($weatherData['trail_temp'] ?? null),
            'temperature' => $weatherData['trail_temp'] ?? '23°C',
            'note' => 'Start your hiking adventure',
            'guidelines' => [
                'Follow marked trails only',
                'Stay with your hiking group',
                'Pace yourself appropriately',
                'Take photos but leave no trace',
                'Monitor weather conditions',
                'Stay hydrated and take breaks',
            ],
            'transport_mode' => 'Hiking',
            'duration' => '5 min',
            'activity_type' => 'hiking_start',
            'coordinates' => $trail['coordinates'] ?? null,
        ];

        $currentTime->addMinutes(5);

        // Calculate hiking phases based on trail difficulty and length
        $hikingPhases = $this->calculateHikingPhases($trail);

        foreach ($hikingPhases as $phase) {
            $activities[] = [
                'time' => $currentTime->format('H:i'),
                'location' => $phase['location'],
                'description' => $phase['description'],
                'condition' => $this->getWeatherCondition($weatherData['trail_temp'] ?? null),
                'temperature' => $weatherData['trail_temp'] ?? '23°C',
                'note' => $phase['note'],
                'guidelines' => $phase['guidelines'],
                'transport_mode' => 'Hiking',
                'duration' => $phase['duration'],
                'activity_type' => 'hiking_phase',
                'coordinates' => $trail['coordinates'] ?? null,
            ];

            $currentTime->addMinutes($phase['duration_minutes']);
        }
    }

    /**
     * Calculate travel duration based on route data
     */
    private function calculateTravelDuration($routeData)
    {
        // Try to get duration from route data
        if (isset($routeData['total_duration_seconds'])) {
            return intval($routeData['total_duration_seconds']);
        }

        if (isset($routeData['legs'])) {
            $totalSeconds = 0;
            foreach ($routeData['legs'] as $leg) {
                if (isset($leg['duration']['value'])) {
                    $totalSeconds += intval($leg['duration']['value']);
                }
            }

            if ($totalSeconds > 0) {
                return $totalSeconds;
            }
        }

        // Try parsing from formatted duration string
        if (isset($routeData['total_duration']) && is_string($routeData['total_duration'])) {
            return $this->parseDurationString($routeData['total_duration']);
        }

        // Fallback: estimate based on transportation type
        return 7200; // 2 hours default for travel
    }

    /**
     * Calculate hiking duration based on trail characteristics
     */
    private function calculateHikingDuration($trail)
    {
        // Base duration from trail data or estimate
        if (isset($trail['estimated_time'])) {
            // Convert minutes to seconds
            return intval($trail['estimated_time']) * 60;
        }

        // Calculate based on trail length and difficulty
        $baseTime = 14400; // 4 hours default

        if (isset($trail['length'])) {
            $length = floatval($trail['length']);
            $baseTime = $length * 1800; // 30 minutes per km
        }

        // Adjust for difficulty
        $difficulty = strtolower($trail['difficulty'] ?? 'moderate');
        $difficultyMultiplier = match ($difficulty) {
            'easy', 'beginner' => 0.8,
            'hard', 'difficult', 'advanced' => 1.4,
            'expert', 'extreme' => 1.8,
            default => 1.0
        };

        return intval($baseTime * $difficultyMultiplier);
    }

    /**
     * Calculate total trip duration (travel + hiking + return)
     */
    private function calculateTotalTripDuration($travelDuration, $hikingDuration)
    {
        // Travel time (round trip) + hiking time + breaks/preparation
        $roundTripTravel = $travelDuration * 2; // To and from
        $preparationTime = 3600; // 1 hour for preparation and breaks

        return $roundTripTravel + $hikingDuration + $preparationTime;
    }

    /**
     * Format duration in seconds to user-friendly string
     */
    private function formatDuration($seconds)
    {
        if ($seconds < 60) {
            return $seconds.' sec';
        }

        if ($seconds < 3600) {
            $minutes = floor($seconds / 60);

            return $minutes.' min';
        }

        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);

        if ($minutes == 0) {
            return $hours.'h';
        }

        return $hours.'h '.$minutes.'m';
    }

    /**
     * Parse duration string to seconds
     */
    private function parseDurationString($durationString)
    {
        $totalSeconds = 0;

        // Parse hours
        if (preg_match('/(\d+)\s*(?:hour|hr|h)/i', $durationString, $matches)) {
            $totalSeconds += intval($matches[1]) * 3600;
        }

        // Parse minutes
        if (preg_match('/(\d+)\s*(?:minute|min|m)/i', $durationString, $matches)) {
            $totalSeconds += intval($matches[1]) * 60;
        }

        // If no time units found, try to extract just numbers
        if ($totalSeconds == 0) {
            if (preg_match('/(\d+)/', $durationString, $matches)) {
                $number = intval($matches[1]);
                // If it's a large number, assume it's already in seconds
                if ($number > 1000) {
                    $totalSeconds = $number;
                } else {
                    // Assume it's hours
                    $totalSeconds = $number * 3600;
                }
            }
        }

        return $totalSeconds > 0 ? $totalSeconds : 7200; // Default 2 hours if parsing fails
    }

    /**
     * Add return journey activities
     */
    private function addReturnJourneyActivities(&$activities, &$currentTime, $routeData, $weatherData, $transportation)
    {
        if (strtolower($transportation) === 'commute') {
            $this->addReturnCommuteActivities($activities, $routeData, $currentTime, $weatherData);
        } else {
            $this->addReturnDrivingActivities($activities, $routeData, $currentTime, $weatherData);
        }
    }

    /**
     * Calculate return arrival time
     */
    private function calculateReturnArrivalTime($returnStartTime, $routeData)
    {
        // Same duration as outbound journey
        if (isset($routeData['total_duration_seconds'])) {
            return $returnStartTime->copy()->addSeconds($routeData['total_duration_seconds']);
        }

        // Fallback: assume same time as outbound
        return $returnStartTime->copy()->addHours(2);
    }

    /**
     * Extract step duration from various formats
     */
    private function extractStepDuration($step, $leg)
    {
        $stepDuration = 0;

        // Check multiple possible duration formats from Google API
        if (isset($step['duration']['value']) && is_numeric($step['duration']['value'])) {
            $stepDuration = (int) $step['duration']['value'];
        } elseif (isset($step['duration_seconds']) && is_numeric($step['duration_seconds'])) {
            $stepDuration = (int) $step['duration_seconds'];
        } elseif (isset($step['duration']) && is_array($step['duration']) && isset($step['duration']['value'])) {
            $stepDuration = (int) $step['duration']['value'];
        } elseif (isset($step['duration']) && is_string($step['duration'])) {
            // Parse duration string like "02:00" to seconds
            $durationParts = explode(':', $step['duration']);
            if (count($durationParts) === 2) {
                $stepDuration = (int) $durationParts[0] * 60 + (int) $durationParts[1];
            } elseif (count($durationParts) === 3) {
                $stepDuration = (int) $durationParts[0] * 3600 + (int) $durationParts[1] * 60 + (int) $durationParts[2];
            }
        }

        // If still no duration, try to get it from the leg level
        if ($stepDuration === 0 && isset($leg['duration']['value']) && is_numeric($leg['duration']['value'])) {
            $stepDuration = (int) $leg['duration']['value'];
        }

        return $stepDuration;
    }

    /**
     * Create return journey activity
     */
    private function createReturnActivity($step, $currentTime, $weatherData, $legIndex, $stepIndex)
    {
        // For return journey, we'll create activities in reverse
        $startAddress = is_array($step['start_address'] ?? null) ? json_encode($step['start_address']) : ($step['start_address'] ?? 'Route waypoint');

        // Use actual addresses from Google API when available
        if (isset($step['start_address']) && ! empty($step['start_address'])) {
            $startAddress = $step['start_address'];
        } elseif (isset($step['end_address']) && ! empty($step['end_address'])) {
            $startAddress = $step['end_address'];
        }

        // Extract duration from Google API data for return journey
        $duration = 'Duration TBD';
        if (isset($step['duration']['text'])) {
            $duration = $step['duration']['text'];
        } elseif (isset($step['duration']['value']) && is_numeric($step['duration']['value'])) {
            $durationSeconds = (int) $step['duration']['value'];
            $duration = gmdate('H:i', $durationSeconds);
        }

        $activity = [
            'time' => $currentTime->format('H:i'),
            'location' => $startAddress,
            'description' => 'Return journey: Travel to destination',
            'condition' => $this->getWeatherCondition($weatherData['route_temp'] ?? null),
            'temperature' => $weatherData['route_temp'] ?? 'Route temperature',
            'note' => 'Return journey: Travel to destination',
            'coordinates' => null,
            'transport_mode' => $step['travel_mode'] ?? 'TRANSIT',
            'duration' => $duration,
            'activity_type' => 'return_route',
        ];

        // Add detailed transit information for return journey if available
        if (isset($step['transit_details'])) {
            $transit = $step['transit_details'];
            $vehicleType = $transit['line']['vehicle']['name'] ?? 'Public Transport';
            $lineName = $transit['line']['name'] ?? 'Unknown Line';
            $departureStop = $transit['arrival_stop']['name'] ?? 'Unknown';
            $arrivalStop = $transit['departure_stop']['name'] ?? 'Unknown';

            $activity['note'] = "Return: Take {$vehicleType} {$lineName} from {$departureStop} to {$arrivalStop}";
            $activity['activity_type'] = 'return_transit_board';

            // Add detailed transit information
            $activity['transit_details'] = [
                'vehicle_type' => $vehicleType,
                'line_name' => $lineName,
                'departure_stop' => $departureStop,
                'arrival_stop' => $arrivalStop,
                'num_stops' => $transit['num_stops'] ?? 0,
                'departure_time' => $transit['arrival_time']['text'] ?? 'Unknown',
                'arrival_time' => $transit['departure_time']['text'] ?? 'Unknown',
            ];
        }

        // Add driving details for return journey if available
        if (isset($step['driving_details']) || ($step['travel_mode'] ?? '') === 'DRIVING') {
            $duration = $step['duration']['text'] ?? 'Unknown';
            $distance = $step['distance']['text'] ?? 'Unknown';

            $activity['note'] = "Return: Drive {$distance} ({$duration})";
            $activity['activity_type'] = 'return_driving';

            $activity['driving_details'] = [
                'duration' => $duration,
                'distance' => $distance,
                'destination' => $startAddress,
            ];
        }

        return $activity;
    }

    /**
     * Generate estimated activities when no detailed route data is available
     */
    private function generateEstimatedActivities($routeData, $currentTime, $weatherData)
    {
        $activities = [];
        $estimatedLegTime = 1800; // 30 minutes per leg

        // Get stopovers and sidetrips from route data or use empty arrays
        $stopovers = $routeData['stopovers'] ?? [];
        $sidetrips = $routeData['sidetrips'] ?? [];

        // Add estimated stopover activities
        if (! empty($stopovers)) {
            foreach ($stopovers as $stopover) {
                $currentTime->addSeconds($estimatedLegTime);
                $activities[] = [
                    'time' => $currentTime->format('H:i'),
                    'location' => $stopover,
                    'description' => 'Rest stop and refreshment',
                    'condition' => $this->getWeatherCondition($weatherData['route_temp'] ?? null),
                    'temperature' => $weatherData['route_temp'] ?? 'Route temperature',
                    'note' => 'Rest stop and refreshment',
                    'coordinates' => null,
                    'transport_mode' => 'Travel',
                    'duration' => '30 min',
                    'activity_type' => 'stopover',
                ];
            }
        }

        // Add estimated side trip activities
        if (! empty($sidetrips)) {
            foreach ($sidetrips as $sidetrip) {
                $currentTime->addSeconds($estimatedLegTime);
                $activities[] = [
                    'time' => $currentTime->format('H:i'),
                    'location' => $sidetrip,
                    'description' => 'Optional side trip destination',
                    'condition' => $this->getWeatherCondition($weatherData['route_temp'] ?? null),
                    'temperature' => $weatherData['route_temp'] ?? 'Route temperature',
                    'note' => 'Optional side trip destination',
                    'coordinates' => null,
                    'transport_mode' => 'Travel',
                    'duration' => '30 min',
                    'activity_type' => 'sidetrip',
                ];
            }
        }

        // If no waypoints, add a generic travel activity
        if (empty($stopovers) && empty($sidetrips)) {
            $currentTime->addSeconds(3600); // Add 1 hour for travel
            $activities[] = [
                'time' => $currentTime->format('H:i'),
                'location' => 'En route to trail',
                'description' => 'Traveling to trail destination',
                'condition' => $this->getWeatherCondition($weatherData['route_temp'] ?? null),
                'temperature' => $weatherData['route_temp'] ?? 'Route temperature',
                'note' => 'Traveling to trail destination',
                'coordinates' => null,
                'transport_mode' => 'Travel',
                'duration' => '60 min',
                'activity_type' => 'travel',
            ];
        }

        return $activities;
    }

    /**
     * Get weather condition icon/description
     */
    private function getWeatherCondition($temperature)
    {
        if (! $temperature) {
            return 'Unknown';
        }

        // Extract numeric temperature if possible
        $temp = (int) preg_replace('/[^0-9-]/', '', $temperature);

        if ($temp >= 30) {
            return 'Hot';
        }
        if ($temp >= 25) {
            return 'Warm';
        }
        if ($temp >= 20) {
            return 'Mild';
        }
        if ($temp >= 15) {
            return 'Cool';
        }
        if ($temp >= 10) {
            return 'Cold';
        }

        return 'Very Cold';
    }

    private function fetchWeatherForDate($date, $trail)
    {
        try {
            // Get trail coordinates for weather API
            $coordinates = $trail['coordinates'] ?? null;
            $userLocation = Auth::user()->location ?? 'Quezon City, Philippines';
            $hikingDate = \Carbon\Carbon::parse($date);
            $isToday = $hikingDate->isToday();
            $isTomorrow = $hikingDate->isTomorrow();
            $daysFromNow = $hikingDate->diffInDays(now());

            Log::info('Enhanced weather fetching started', [
                'date' => $date,
                'isToday' => $isToday,
                'isTomorrow' => $isTomorrow,
                'daysFromNow' => $daysFromNow,
                'trail' => $trail['name'] ?? 'Unknown trail',
                'coordinates' => $coordinates,
                'userLocation' => $userLocation,
            ]);

            // Get weather forecast for departure location (use forecast if not today)
            $departureWeather = $this->getWeatherForSpecificDate($userLocation, $date);
            Log::info('Departure weather fetched', ['departureWeather' => $departureWeather]);

            // Get weather forecast for trail destination
            $trailWeather = null;
            if ($coordinates) {
                $trailWeather = $this->getWeatherForSpecificDate($coordinates, $date);
            } else {
                // Fallback: use trail location name
                $trailLocation = $trail['location'] ?? 'Trail destination';
                $trailWeather = $this->getWeatherForSpecificDate($trailLocation, $date);

                // If trail location fails, try using a nearby major city
                if (! $trailWeather || $trailWeather['temperature'] === '25°C') {
                    $nearbyCities = [
                        'Mt. Daraitan' => 'Tanay, Rizal, Philippines',
                        'Mt. Pico de Loro' => 'Ternate, Cavite, Philippines',
                        'Mt. Batulao' => 'Nasugbu, Batangas, Philippines',
                        'Mt. Maculot' => 'Cuenca, Batangas, Philippines',
                        'Mt. Pulag' => 'Bokod, Benguet, Philippines',
                        'Mt. Apo' => 'Kidapawan, Cotabato, Philippines',
                    ];

                    foreach ($nearbyCities as $trailName => $cityName) {
                        if (stripos($trailLocation, $trailName) !== false) {
                            $trailWeather = $this->getWeatherForSpecificDate($cityName, $date);
                            if ($trailWeather && $trailWeather['temperature'] !== '25°C') {
                                Log::info('Using nearby city weather for trail', [
                                    'trail' => $trailLocation,
                                    'city' => $cityName,
                                    'weather' => $trailWeather,
                                ]);
                                break;
                            }
                        }
                    }
                }
            }
            Log::info('Trail weather fetched', ['trailWeather' => $trailWeather]);

            // Get route weather (use departure location weather for simplicity)
            $routeWeather = $departureWeather;

            // Generate dynamic weather conditions based on date and season
            $seasonalConditions = $this->getSeasonalWeatherConditions($date);

            // Ensure consistent weather data format with dynamic conditions
            $result = [
                'departure_temp' => $departureWeather['temperature'] ?? $this->getSeasonalTemperature($date, 'urban'),
                'departure_condition' => $departureWeather['condition'] ?? $seasonalConditions['condition'],
                'trail_temp' => $trailWeather['temperature'] ?? $this->getSeasonalTemperature($date, 'mountain'),
                'trail_condition' => $trailWeather['condition'] ?? $seasonalConditions['condition'],
                'route_temp' => $routeWeather['temperature'] ?? $this->getSeasonalTemperature($date, 'route'),
                'route_condition' => $routeWeather['condition'] ?? $seasonalConditions['condition'],
                'departure_weather' => $departureWeather,
                'trail_weather' => $trailWeather,
                'route_weather' => $routeWeather,
                'seasonal_info' => $seasonalConditions,
                'date_info' => [
                    'hiking_date' => $hikingDate->format('Y-m-d'),
                    'is_today' => $isToday,
                    'is_tomorrow' => $isTomorrow,
                    'days_from_now' => $daysFromNow,
                ],
            ];

            Log::info('Enhanced weather data result', ['result' => $result]);

            return $result;

        } catch (\Exception $e) {
            Log::error('Enhanced weather fetching failed', ['error' => $e->getMessage(), 'date' => $date]);

            // Return fallback with seasonal awareness
            $seasonalConditions = $this->getSeasonalWeatherConditions($date);

            return [
                'departure_temp' => $this->getSeasonalTemperature($date, 'urban'),
                'departure_condition' => $seasonalConditions['condition'],
                'trail_temp' => $this->getSeasonalTemperature($date, 'mountain'),
                'trail_condition' => $seasonalConditions['condition'],
                'route_temp' => $this->getSeasonalTemperature($date, 'route'),
                'route_condition' => $seasonalConditions['condition'],
                'seasonal_info' => $seasonalConditions,
            ];
        }
    }

    /**
     * Get weather forecast for a specific date
     */
    private function getWeatherForSpecificDate($location, $date)
    {
        try {
            $hikingDate = \Carbon\Carbon::parse($date);
            $isToday = $hikingDate->isToday();

            if ($isToday) {
                // For today, get current weather
                return $this->getCurrentWeather($location);
            } else {
                // For future dates, try to get forecast
                return $this->getForecastWeather($location, $date);
            }
        } catch (\Exception $e) {
            Log::error('Weather for specific date failed', [
                'location' => $location,
                'date' => $date,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Get forecast weather for future dates
     */
    private function getForecastWeather($location, $date)
    {
        try {
            $apiKey = config('services.google.maps_api_key');
            if (! $apiKey) {
                return null;
            }

            // First, geocode the location
            $geocodeUrl = 'https://maps.googleapis.com/maps/api/geocode/json';
            $geocodeParams = [
                'address' => $location,
                'key' => $apiKey,
            ];

            $geocodeResponse = Http::get($geocodeUrl, $geocodeParams);

            if ($geocodeResponse->successful()) {
                $geocodeData = $geocodeResponse->json();

                if ($geocodeData['status'] === 'OK' && ! empty($geocodeData['results'])) {
                    $result = $geocodeData['results'][0];
                    $lat = $result['geometry']['location']['lat'];
                    $lng = $result['geometry']['location']['lng'];

                    // Try OpenWeatherMap API for forecast (if you have it configured)
                    $openWeatherKey = config('services.openweathermap.api_key');
                    if ($openWeatherKey) {
                        $forecastUrl = 'https://api.openweathermap.org/data/2.5/forecast';
                        $forecastParams = [
                            'lat' => $lat,
                            'lon' => $lng,
                            'appid' => $openWeatherKey,
                            'units' => 'metric',
                        ];

                        $forecastResponse = Http::get($forecastUrl, $forecastParams);

                        if ($forecastResponse->successful()) {
                            $forecastData = $forecastResponse->json();

                            // Find forecast closest to the hiking date
                            $targetDate = \Carbon\Carbon::parse($date)->startOfDay();
                            $closestForecast = null;
                            $smallestDiff = PHP_INT_MAX;

                            foreach ($forecastData['list'] as $forecast) {
                                $forecastDate = \Carbon\Carbon::createFromTimestamp($forecast['dt']);
                                $diff = abs($forecastDate->diffInHours($targetDate));

                                if ($diff < $smallestDiff) {
                                    $smallestDiff = $diff;
                                    $closestForecast = $forecast;
                                }
                            }

                            if ($closestForecast) {
                                return [
                                    'temperature' => round($closestForecast['main']['temp']).'°C',
                                    'condition' => $this->getWeatherCondition($closestForecast['main']['temp']),
                                    'description' => ucfirst($closestForecast['weather'][0]['description']),
                                    'humidity' => $closestForecast['main']['humidity'],
                                    'feels_like' => round($closestForecast['main']['feels_like']).'°C',
                                    'source' => 'OpenWeatherMap Forecast',
                                ];
                            }
                        }
                    }

                    // Fallback to seasonal estimation
                    return $this->getSeasonalWeatherEstimate($location, $date);
                }
            }
        } catch (\Exception $e) {
            Log::error('Forecast weather API exception', [
                'message' => $e->getMessage(),
                'location' => $location,
                'date' => $date,
            ]);
        }

        return null;
    }

    /**
     * Get seasonal weather conditions based on date
     */
    private function getSeasonalWeatherConditions($date)
    {
        $carbon = \Carbon\Carbon::parse($date);
        $month = $carbon->month;
        $dayOfYear = $carbon->dayOfYear;

        // Philippine seasons
        if ($month >= 6 && $month <= 10) {
            // Rainy season (June-October)
            return [
                'season' => 'rainy',
                'condition' => 'Rainy',
                'description' => 'Expect frequent rain showers. Bring waterproof gear and check weather updates.',
                'recommendations' => [
                    'Pack waterproof clothing and gear',
                    'Check real-time weather before departure',
                    'Consider postponing if severe weather is predicted',
                    'Bring extra dry clothes',
                ],
            ];
        } elseif ($month >= 11 || $month <= 2) {
            // Cool/dry season (November-February)
            return [
                'season' => 'cool_dry',
                'condition' => 'Cool',
                'description' => 'Cool and mostly dry weather. Ideal for hiking with comfortable temperatures.',
                'recommendations' => [
                    'Bring layers for temperature changes',
                    'Morning temperatures may be chilly',
                    'Great visibility for scenic views',
                    'Perfect hiking conditions',
                ],
            ];
        } else {
            // Hot/dry season (March-May)
            return [
                'season' => 'hot_dry',
                'condition' => 'Hot',
                'description' => 'Hot and dry weather. Start early to avoid peak heat.',
                'recommendations' => [
                    'Start hiking very early (before sunrise)',
                    'Bring extra water and electrolytes',
                    'Wear sun protection (hat, sunglasses, sunscreen)',
                    'Take frequent shade breaks',
                ],
            ];
        }
    }

    /**
     * Get seasonal temperature estimate based on location type and date
     */
    private function getSeasonalTemperature($date, $locationType = 'urban')
    {
        $carbon = \Carbon\Carbon::parse($date);
        $month = $carbon->month;

        // Base temperatures by season for Metro Manila/urban areas
        $baseTemp = 27; // Default

        if ($month >= 6 && $month <= 10) {
            // Rainy season - cooler
            $baseTemp = 25;
        } elseif ($month >= 11 || $month <= 2) {
            // Cool season
            $baseTemp = 23;
        } else {
            // Hot season
            $baseTemp = 30;
        }

        // Adjust based on location type
        switch ($locationType) {
            case 'mountain':
                $baseTemp -= 5; // Mountains are typically 5°C cooler
                break;
            case 'route':
                $baseTemp -= 1; // Slightly cooler during travel
                break;
            case 'urban':
            default:
                // Keep base temperature
                break;
        }

        // Add some randomness for realism (±2°C)
        $baseTemp += rand(-2, 2);

        return max(15, min(40, $baseTemp)).'°C'; // Clamp between realistic ranges
    }

    /**
     * Get seasonal weather estimate when APIs fail
     */
    private function getSeasonalWeatherEstimate($location, $date)
    {
        $seasonalConditions = $this->getSeasonalWeatherConditions($date);
        $locationType = $this->determineLocationType($location);

        return [
            'temperature' => $this->getSeasonalTemperature($date, $locationType),
            'condition' => $seasonalConditions['condition'],
            'description' => $seasonalConditions['description'],
            'source' => 'Seasonal Estimate',
        ];
    }

    /**
     * Determine location type for temperature adjustment
     */
    private function determineLocationType($location)
    {
        $location = strtolower($location);

        if (strpos($location, 'mt.') !== false ||
            strpos($location, 'mount') !== false ||
            strpos($location, 'peak') !== false ||
            strpos($location, 'mountain') !== false) {
            return 'mountain';
        }

        if (strpos($location, 'trail') !== false) {
            return 'mountain';
        }

        return 'urban';
    }

    /**
     * Get current weather from Google Weather API
     */
    private function getCurrentWeather($location)
    {
        try {
            $apiKey = config('services.google.maps_api_key');
            Log::info('Google Weather API call started', [
                'location' => $location,
                'hasApiKey' => ! empty($apiKey),
                'apiKeyLength' => $apiKey ? strlen($apiKey) : 0,
            ]);

            if (! $apiKey) {
                Log::warning('Google Maps API key not configured');

                return null;
            }

            // First, geocode the location to get coordinates
            $geocodeUrl = 'https://maps.googleapis.com/maps/api/geocode/json';
            $geocodeParams = [
                'address' => $location,
                'key' => $apiKey,
            ];

            Log::info('Google Geocoding API request', ['url' => $geocodeUrl, 'params' => $geocodeParams]);

            $geocodeResponse = Http::get($geocodeUrl, $geocodeParams);

            if ($geocodeResponse->successful()) {
                $geocodeData = $geocodeResponse->json();

                if ($geocodeData['status'] === 'OK' && ! empty($geocodeData['results'])) {
                    $result = $geocodeData['results'][0];
                    $lat = $result['geometry']['location']['lat'];
                    $lng = $result['geometry']['location']['lng'];

                    Log::info('Location geocoded successfully', ['lat' => $lat, 'lng' => $lng]);

                    // Now get weather data using Google Weather API
                    $weatherUrl = 'https://maps.googleapis.com/maps/api/weather/json';
                    $weatherParams = [
                        'location' => "{$lat},{$lng}",
                        'key' => $apiKey,
                        'units' => 'metric',
                    ];

                    Log::info('Google Weather API request', ['url' => $weatherUrl, 'params' => $weatherParams]);

                    $weatherResponse = Http::get($weatherUrl, $weatherParams);

                    if ($weatherResponse->successful()) {
                        $weatherData = $weatherResponse->json();

                        if ($weatherData['status'] === 'OK') {
                            $result = [
                                'temperature' => round($weatherData['temperature']).'°C',
                                'condition' => $this->getWeatherCondition($weatherData['temperature']),
                                'description' => $weatherData['description'] ?? '',
                                'humidity' => $weatherData['humidity'] ?? 0,
                                'wind_speed' => $weatherData['wind_speed'] ?? 0,
                                'feels_like' => round($weatherData['feels_like'] ?? $weatherData['temperature']).'°C',
                                'raw_data' => $weatherData,
                            ];

                            Log::info('Google Weather API success', ['result' => $result]);

                            return $result;
                        } else {
                            Log::error('Google Weather API error', [
                                'status' => $weatherData['status'],
                                'error_message' => $weatherData['error_message'] ?? 'Unknown error',
                            ]);
                        }
                    } else {
                        Log::error('Google Weather API request failed', [
                            'status_code' => $weatherResponse->status(),
                            'response' => $weatherResponse->body(),
                        ]);
                    }
                } else {
                    Log::error('Geocoding failed', [
                        'status' => $geocodeData['status'],
                        'error_message' => $geocodeData['error_message'] ?? 'Unknown error',
                    ]);
                }
            } else {
                Log::error('Geocoding request failed', [
                    'status_code' => $geocodeResponse->status(),
                    'response' => $geocodeResponse->body(),
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Google Weather API exception', [
                'message' => $e->getMessage(),
                'location' => $location,
            ]);
        }

        // Fallback: return basic weather data
        return [
            'temperature' => '25°C',
            'condition' => 'Warm',
            'description' => 'Weather data unavailable',
            'humidity' => 0,
            'wind_speed' => 0,
            'feels_like' => '25°C',
        ];
    }

    private function getRouteCondition($travelMode)
    {
        return match ($travelMode) {
            'TRANSIT' => 'Public transport',
            'WALKING' => 'Walking',
            'DRIVING' => 'Driving',
            'BICYCLING' => 'Cycling',
            default => 'Traveling'
        };
    }

    private function determineDifficultyLevel($assessment)
    {
        $overallScore = $assessment->overall_score;

        if ($overallScore >= 80) {
            return 'Hard';
        } elseif ($overallScore >= 60) {
            return 'Moderate';
        } elseif ($overallScore >= 40) {
            return 'Easy';
        } else {
            return 'Easy';
        }
    }

    private function getElevationForDifficulty($difficulty)
    {
        return match ($difficulty) {
            'Easy' => '100-300 meters',
            'Moderate' => '300-600 meters',
            'Hard' => '600-1000 meters',
            'Expert' => '1000+ meters',
            default => '300-500 meters'
        };
    }

    private function generateGearRecommendations($assessment)
    {
        $recommendations = ['Essential Items'];

        if ($assessment->gear_score < 70) {
            $recommendations[] = 'Navigation tools (compass, map)';
            $recommendations[] = 'First aid kit';
            $recommendations[] = 'Emergency shelter';
        }

        if ($assessment->weather_score < 70) {
            $recommendations[] = 'Weather-appropriate clothing';
            $recommendations[] = 'Rain gear';
            $recommendations[] = 'Extra layers';
        }

        if ($assessment->emergency_score < 70) {
            $recommendations[] = 'Emergency whistle';
            $recommendations[] = 'Signal mirror';
            $recommendations[] = 'Emergency blanket';
        }

        return $recommendations;
    }

    private function generateSafetyTips($assessment)
    {
        $tips = ['Always hike with a buddy or inform someone of your plans'];

        if ($assessment->health_score < 70) {
            $tips[] = 'Consult with your doctor before hiking';
            $tips[] = 'Carry necessary medications';
        }

        if ($assessment->fitness_score < 70) {
            $tips[] = 'Start with shorter trails and gradually increase difficulty';
            $tips[] = 'Take frequent breaks and stay hydrated';
        }

        if ($assessment->environment_score < 70) {
            $tips[] = 'Check trail conditions before departure';
            $tips[] = 'Be aware of wildlife in the area';
        }

        return $tips;
    }

    private function generateRouteDescription($difficultyLevel, $trailName, $routeData)
    {
        $description = "Your journey to {$trailName} begins from your current location. ";

        if (isset($routeData['legs']) && count($routeData['legs']) > 0) {
            $totalDistance = $routeData['total_distance'] ?? 'unknown distance';
            $totalDuration = $routeData['total_duration'] ?? 'unknown duration';

            $description .= "The route covers approximately {$totalDistance} and takes about {$totalDuration}. ";

            if (count($routeData['legs']) > 1) {
                $description .= 'The journey includes multiple segments with various transportation modes. ';
            }

            // Add information about waypoints if available
            if (isset($routeData['stopovers']) && count($routeData['stopovers']) > 0) {
                $description .= "You'll make stops at: ".implode(', ', $routeData['stopovers']).'. ';
            }

            if (isset($routeData['sidetrips']) && count($routeData['sidetrips']) > 0) {
                $description .= 'Optional side trips include: '.implode(', ', $routeData['sidetrips']).'. ';
            }
        } else {
            $description .= 'The route details are being calculated. Please check the itinerary for the most up-to-date information. ';
        }

        $description .= "This trail is rated as {$difficultyLevel} difficulty, so ensure you're prepared for the challenge level. ";
        $description .= 'Remember to check weather conditions and bring appropriate gear for your journey.';

        return $description;
    }

    private function generateWaypoints($difficulty, $stopovers, $sidetrips, $routeData)
    {
        $waypoints = [
            [
                'name' => 'Departure Point',
                'description' => 'Starting location for your journey',
                'distance' => '0 km',
                'elevation' => 'Starting elevation',
                'time' => 'Departure time',
                'coordinates' => $routeData['origin_coordinates'] ?? null,
            ],
        ];

        // Add stopovers as waypoints with more accurate information
        foreach ($stopovers as $index => $stopover) {
            $waypoints[] = [
                'name' => $stopover,
                'description' => 'Rest stop and refreshment point',
                'distance' => $this->calculateWaypointDistance($index + 1, $routeData),
                'elevation' => 'Stopover elevation',
                'time' => $this->calculateWaypointTime($index + 1, $routeData),
                'coordinates' => null, // Will be filled by Google Directions if available
            ];
        }

        // Add side trips as waypoints with more accurate information
        foreach ($sidetrips as $index => $sidetrip) {
            $waypoints[] = [
                'name' => $sidetrip,
                'description' => 'Optional side trip destination',
                'distance' => $this->calculateWaypointDistance(count($stopovers) + $index + 1, $routeData),
                'elevation' => 'Side trip elevation',
                'time' => $this->calculateWaypointTime(count($stopovers) + $index + 1, $routeData),
                'coordinates' => null, // Will be filled by Google Directions if available
            ];
        }

        $waypoints[] = [
            'name' => 'Trail Destination',
            'description' => 'Final destination - ready to hike',
            'distance' => $routeData['total_distance'] ?? 'Total route distance',
            'elevation' => 'Trail elevation',
            'time' => 'Estimated arrival time',
            'coordinates' => $routeData['destination_coordinates'] ?? null,
        ];

        return $waypoints;
    }

    private function calculateWaypointDistance($waypointIndex, $routeData)
    {
        if (isset($routeData['legs']) && count($routeData['legs']) > 0) {
            // Calculate cumulative distance based on legs
            $totalDistance = 0;
            foreach ($routeData['legs'] as $index => $leg) {
                if ($index < $waypointIndex) {
                    $totalDistance += $leg['distance_meters'] ?? 0;
                }
            }

            return round($totalDistance / 1000, 1).' km';
        }

        // Fallback calculation
        $baseDistance = 50; // Base distance
        $waypointDistance = $waypointIndex * 10; // 10km per waypoint

        return ($baseDistance + $waypointDistance).' km';
    }

    private function calculateWaypointTime($waypointIndex, $routeData)
    {
        if (isset($routeData['legs']) && count($routeData['legs']) > 0) {
            // Calculate cumulative time based on legs
            $totalTime = 0;
            foreach ($routeData['legs'] as $index => $leg) {
                if ($index < $waypointIndex) {
                    $totalTime += $leg['duration_seconds'] ?? 0;
                }
            }

            return gmdate('H:i', $totalTime);
        }

        // Fallback calculation
        $baseTime = 3600; // Base 1 hour
        $waypointTime = $waypointIndex * 900; // 15 minutes per waypoint

        return gmdate('H:i', $baseTime + $waypointTime);
    }

    private function getWeatherConditions($date)
    {
        try {
            $carbonDate = \Carbon\Carbon::parse($date);
            $month = $carbonDate->month;
            $dayName = $carbonDate->format('l');
            $dateFormatted = $carbonDate->format('M d, Y');

            // Get seasonal conditions for more dynamic weather
            $seasonalConditions = $this->getSeasonalWeatherConditions($date);

            $baseCondition = '';
            if ($month >= 6 && $month <= 10) {
                $baseCondition = "Rainy season hiking on {$dayName}, {$dateFormatted}. {$seasonalConditions['description']}";
            } elseif ($month >= 11 && $month <= 2) {
                $baseCondition = "Cool, dry season hiking on {$dayName}, {$dateFormatted}. {$seasonalConditions['description']}";
            } elseif ($month >= 3 && $month <= 5) {
                $baseCondition = "Hot, dry season hiking on {$dayName}, {$dateFormatted}. {$seasonalConditions['description']}";
            } else {
                $baseCondition = "Hiking planned for {$dayName}, {$dateFormatted}. {$seasonalConditions['description']}";
            }

            return $baseCondition;

        } catch (\Exception $e) {
            Log::error('Weather conditions generation failed', [
                'date' => $date,
                'error' => $e->getMessage(),
            ]);

            return 'Weather conditions for your hiking date. Check local forecasts before departure.';
        }
    }

    private function getEmergencyContacts($user)
    {
        return [
            'local_emergency' => '911',
            'park_ranger' => '+63-XXX-XXX-XXXX',
            'emergency_contact' => $user->emergency_contact_phone ?? 'Not provided',
            'user_emergency_contact' => $user->emergency_contact_name ?? 'Not provided',
        ];
    }

    private function generateStaticMapUrl($routeData)
    {
        if (empty($routeData['route_coordinates'])) {
            return null;
        }

        $apiKey = config('services.google.maps_api_key');
        if (! $apiKey) {
            return null;
        }

        // Get departure and arrival coordinates
        $departure = $routeData['origin_coordinates'] ?? $routeData['route_coordinates'][0] ?? null;
        $arrival = $routeData['destination_coordinates'] ?? end($routeData['route_coordinates']) ?? null;

        if (! $departure || ! $arrival) {
            return null;
        }

        // Create path for the route
        $path = '';
        foreach ($routeData['route_coordinates'] as $coord) {
            $path .= $coord['lat'].','.$coord['lng'].'|';
        }
        $path = rtrim($path, '|');

        // Build static map URL
        $url = 'https://maps.googleapis.com/maps/api/staticmap?';
        $url .= 'size=600x400';
        $url .= '&scale=2';
        $url .= '&maptype=terrain';
        $url .= '&markers=color:green|label:D|'.$departure['lat'].','.$departure['lng'];
        $url .= '&markers=color:red|label:A|'.$arrival['lat'].','.$arrival['lng'];
        $url .= '&path=color:0x10B981|weight:4|'.$path;
        $url .= '&key='.$apiKey;

        return $url;
    }

    // Removed getRandomTransitMode - now using your APIs

    /**
     * Sanitize instruction text by removing HTML tags and converting to plain text
     */
    private function sanitizeInstruction($instruction)
    {
        if (empty($instruction)) {
            return 'Travel to destination';
        }

        // Remove HTML tags and convert common HTML entities
        $cleanInstruction = strip_tags($instruction);

        // Convert common HTML entities to plain text
        $cleanInstruction = str_replace(
            ['<b>', '</b>', '<wbr/>', '<div>', '</div>', '<br>', '<br/>'],
            ['', '', ' ', ' ', ' ', ' ', ' '],
            $cleanInstruction
        );

        // Clean up extra whitespace
        $cleanInstruction = preg_replace('/\s+/', ' ', $cleanInstruction);
        $cleanInstruction = trim($cleanInstruction);

        // If the instruction is still empty after cleaning, provide a default
        if (empty($cleanInstruction)) {
            return 'Travel to destination';
        }

        return $cleanInstruction;
    }

    /**
     * Get weather for a specific time
     */
    private function getWeatherForTime($location, $time)
    {
        try {
            $weather = $this->getCurrentWeather($location);

            return [
                'condition' => $weather['condition'] ?? 'Unknown',
                'temperature' => $weather['temperature'] ?? '25°C',
            ];
        } catch (\Exception $e) {
            return [
                'condition' => 'Unknown',
                'temperature' => '25°C',
            ];
        }
    }

    /**
     * Filter significant driving steps to avoid too many minor waypoints
     */
    private function filterSignificantDrivingSteps($steps)
    {
        $significantSteps = [];

        foreach ($steps as $step) {
            $distance = $step['distance']['value'] ?? 0;
            $duration = $step['duration']['value'] ?? 0;

            // Include steps that are:
            // - Longer than 5km
            // - Take more than 10 minutes
            // - Have important maneuvers
            $hasImportantManeuver = isset($step['maneuver']) &&
                in_array($step['maneuver'], ['turn-left', 'turn-right', 'merge', 'exit', 'fork']);

            if ($distance > 5000 || $duration > 600 || $hasImportantManeuver) {
                $significantSteps[] = $step;
            }
        }

        // Ensure we don't have too many steps
        return array_slice($significantSteps, 0, 10);
    }

    /**
     * Extract location information from HTML instructions
     */
    private function extractLocationFromInstructions($instructions, $step)
    {
        if (empty($instructions)) {
            return $step['end_address'] ?? 'Route waypoint';
        }

        // Remove HTML tags and extract key location info
        $cleanInstructions = $this->sanitizeInstruction($instructions);

        // Look for common patterns like "Turn right onto Main St"
        if (preg_match('/onto\s+(.+?)(?:\s|$)/i', $cleanInstructions, $matches)) {
            return 'Route via '.trim($matches[1]);
        }

        // Look for "toward" destinations
        if (preg_match('/toward\s+(.+?)(?:\s|$)/i', $cleanInstructions, $matches)) {
            return 'Toward '.trim($matches[1]);
        }

        // Fallback to sanitized instructions
        return substr($cleanInstructions, 0, 50).(strlen($cleanInstructions) > 50 ? '...' : '');
    }

    /**
     * Extract duration in seconds from various Google API formats
     */
    private function extractDurationSeconds($duration)
    {
        if (is_array($duration) && isset($duration['value'])) {
            return intval($duration['value']);
        }

        if (is_numeric($duration)) {
            return intval($duration);
        }

        return 0;
    }

    /**
     * Calculate hiking phases based on trail characteristics
     */
    private function calculateHikingPhases($trail)
    {
        $phases = [];
        $difficulty = strtolower($trail['difficulty'] ?? 'moderate');
        $length = floatval($trail['length'] ?? 5);

        // Create phases based on trail length and difficulty
        $totalHikingTime = $this->calculateHikingDuration($trail) / 60; // Convert to minutes

        if ($length > 8) {
            // Long trail - multiple phases
            $phases = [
                [
                    'location' => 'Early trail section',
                    'description' => 'Initial hiking phase',
                    'note' => 'Warm up and find your hiking rhythm',
                    'duration' => '45 min',
                    'duration_minutes' => 45,
                    'guidelines' => [
                        'Start at a comfortable pace',
                        'Warm up properly before steep sections',
                        'Take photos of interesting views',
                        'Monitor your energy levels',
                    ],
                ],
                [
                    'location' => 'Midway checkpoint',
                    'description' => 'Mid-trail rest and assessment',
                    'note' => 'Rest, hydrate, and assess conditions',
                    'duration' => '15 min',
                    'duration_minutes' => 15,
                    'guidelines' => [
                        'Take a proper rest break',
                        'Hydrate and have a snack',
                        'Check weather conditions',
                        'Assess group energy levels',
                        'Adjust pace if needed',
                    ],
                ],
                [
                    'location' => 'Final ascent/descent',
                    'description' => 'Final phase to destination',
                    'note' => 'Complete the final section safely',
                    'duration' => ($totalHikingTime - 60).' min',
                    'duration_minutes' => $totalHikingTime - 60,
                    'guidelines' => [
                        'Maintain steady pace',
                        'Use trekking poles if available',
                        'Watch footing on steep sections',
                        'Celebrate your achievement!',
                    ],
                ],
            ];
        } else {
            // Shorter trail - fewer phases
            $phases = [
                [
                    'location' => 'Trail midpoint',
                    'description' => 'Midway rest and scenic viewing',
                    'note' => 'Enjoy the scenery and take a break',
                    'duration' => '20 min',
                    'duration_minutes' => 20,
                    'guidelines' => [
                        'Take photos of scenic views',
                        'Rest and hydrate',
                        'Check your gear',
                        'Enjoy the natural environment',
                    ],
                ],
                [
                    'location' => 'Trail destination/turnaround',
                    'description' => 'Reach trail destination',
                    'note' => 'Congratulations on reaching your destination!',
                    'duration' => ($totalHikingTime - 25).' min',
                    'duration_minutes' => $totalHikingTime - 25,
                    'guidelines' => [
                        'Take celebratory photos',
                        'Rest before return journey',
                        'Pack out all trash',
                        'Prepare for return hike',
                    ],
                ],
            ];
        }

        return $phases;
    }

    /**
     * Add return commute activities (simplified version)
     */
    private function addReturnCommuteActivities(&$activities, $routeData, &$currentTime, $weatherData)
    {
        if (! isset($routeData['legs']) || empty($routeData['legs'])) {
            // Fallback return commute
            $activities[] = [
                'time' => $currentTime->format('H:i'),
                'location' => 'Return commute',
                'description' => 'Return journey by public transport',
                'condition' => $this->getWeatherCondition($weatherData['route_temp'] ?? null),
                'temperature' => $weatherData['route_temp'] ?? '25°C',
                'transport_mode' => 'Transit',
                'note' => 'Follow same route back in reverse',
                'guidelines' => [
                    'Check return schedules',
                    'Keep transportation fare ready',
                    'Plan for potential delays',
                    'Stay alert during journey',
                ],
                'activity_type' => 'return_transit',
                'duration' => '2 hours',
            ];

            $currentTime->addHours(2);

            return;
        }

        // Reverse the route legs for return journey
        $returnLegs = array_reverse($routeData['legs']);

        foreach ($returnLegs as $legIndex => $leg) {
            $activities[] = [
                'time' => $currentTime->format('H:i'),
                'location' => $leg['end_address'] ?? 'Return transit point',
                'description' => 'Return journey segment',
                'condition' => $this->getWeatherCondition($weatherData['route_temp'] ?? null),
                'temperature' => $weatherData['route_temp'] ?? '25°C',
                'transport_mode' => 'Transit',
                'note' => 'Follow return route',
                'guidelines' => [
                    'Check departure schedules',
                    'Board correct return vehicle',
                    'Stay alert for your stop',
                    'Keep belongings secure',
                ],
                'activity_type' => 'return_transit_segment',
                'duration' => $leg['duration']['text'] ?? '30 min',
            ];

            $legDuration = $this->extractDurationSeconds($leg['duration'] ?? []);
            $currentTime->addSeconds($legDuration);
        }
    }

    /**
     * Add return driving activities (simplified version)
     */
    private function addReturnDrivingActivities(&$activities, $routeData, &$currentTime, $weatherData)
    {
        if (! isset($routeData['legs']) || empty($routeData['legs'])) {
            // Fallback return drive
            $activities[] = [
                'time' => $currentTime->format('H:i'),
                'location' => 'Return drive',
                'description' => 'Return journey by private vehicle',
                'condition' => $this->getWeatherCondition($weatherData['route_temp'] ?? null),
                'temperature' => $weatherData['route_temp'] ?? '25°C',
                'transport_mode' => 'Driving',
                'note' => 'Follow same route back in reverse',
                'guidelines' => [
                    'Check fuel levels',
                    'Monitor for fatigue',
                    'Take breaks as needed',
                    'Follow traffic rules',
                    'Be extra cautious if tired',
                ],
                'activity_type' => 'return_driving',
                'duration' => '1.5 hours',
            ];

            $currentTime->addMinutes(90);

            return;
        }

        // Reverse the route for return journey
        $totalReturnTime = 0;
        foreach ($routeData['legs'] as $leg) {
            $totalReturnTime += $this->extractDurationSeconds($leg['duration'] ?? []);
        }

        $activities[] = [
            'time' => $currentTime->format('H:i'),
            'location' => 'Begin return drive',
            'description' => 'Start return journey home',
            'condition' => $this->getWeatherCondition($weatherData['route_temp'] ?? null),
            'temperature' => $weatherData['route_temp'] ?? '25°C',
            'transport_mode' => 'Driving',
            'note' => 'Reverse route back to starting point',
            'guidelines' => [
                'Check vehicle condition before leaving',
                'Plan fuel stops if needed',
                'Monitor fatigue levels',
                'Take breaks every 2 hours',
                'Use GPS navigation for best route',
                'Drive safely and defensively',
            ],
            'activity_type' => 'return_driving_start',
            'duration' => gmdate('H:i', $totalReturnTime),
        ];

        $currentTime->addSeconds($totalReturnTime);
    }
}

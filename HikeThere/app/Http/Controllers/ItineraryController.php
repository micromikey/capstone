<?php

namespace App\Http\Controllers;

use App\Models\Itinerary;
use App\Models\Trail;
use App\Services\GoogleDirectionsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ItineraryController extends Controller
{
    protected $directionsService;

    public function __construct(GoogleDirectionsService $directionsService)
    {
        $this->directionsService = $directionsService;
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
                'route_data' => $itinerary['route_data'] ?? [], // Get route_data from itinerary array
                'route_summary' => $itinerary['route_summary'] ?? [], // Get route_summary from itinerary array
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

        // Generate route using Google Directions API
        $routeData = $this->generateRouteData($userLocation, $trail, $stopovers, $sidetrips, $transportation);

        // Generate gear recommendations based on assessment
        $gearRecommendations = $this->generateGearRecommendations($assessment);

        // Generate safety tips based on assessment
        $safetyTips = $this->generateSafetyTips($assessment);

        // Generate detailed schedule
        $schedule = $this->generateDetailedSchedule($time, $date, $routeData, $stopovers, $sidetrips);

        // Generate daily schedule with weather integration
        $dailySchedule = $this->generateDailySchedule($date, $time, $routeData, $trail);

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

        return [
            'title' => "Personalized {$trailName} Itinerary",
            'trail_name' => $trailName,
            'user_location' => $userLocation,
            'difficulty_level' => $difficultyLevel,
            'estimated_duration' => $routeData['total_duration'] ?? 'Based on route planning',
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
            'route_data' => $routeData, // Include route_data in the returned array
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

            // Combine all waypoints
            $allWaypoints = array_merge($stopovers, $sidetrips);

            // Use Google Directions API for all transportation modes
            $directions = null;

            if ($transportation === 'Commute') {
                // Try transit mode first
                $directions = $this->directionsService->getDirections($origin, $destination, $allWaypoints, 'transit');

                // If transit fails, fall back to driving mode
                if (! $directions) {
                    Log::info('Transit directions failed, falling back to driving mode');
                    $directions = $this->directionsService->getDirections($origin, $destination, $allWaypoints, 'driving');
                }
            } else {
                // Use driving mode for non-commute
                $directions = $this->directionsService->getDirections($origin, $destination, $allWaypoints, 'driving');
            }

            if ($directions) {
                // Add transport details for commute mode
                if ($transportation === 'Commute') {
                    $directions['transport_details'] = $this->directionsService->generateCommuteDetails($directions);

                    // Add enhanced transit summary
                    if (isset($directions['transit_summary'])) {
                        $directions['transit_summary']['total_cost_estimate'] = $this->estimateTransitCost($directions['transit_summary']['transit_modes']);
                    }
                }

                // Add origin and destination coordinates
                $directions['origin_coordinates'] = $directions['legs'][0]['start_location'] ?? null;
                $directions['destination_coordinates'] = $directions['legs'][count($directions['legs']) - 1]['end_location'] ?? null;

                // Ensure we have the stopovers and sidetrips in the route data
                $directions['stopovers'] = $stopovers;
                $directions['sidetrips'] = $sidetrips;

                Log::info('Route data generated successfully using Google Directions API', [
                    'transportation' => $transportation,
                    'hasLegs' => isset($directions['legs']),
                    'legsCount' => isset($directions['legs']) ? count($directions['legs']) : 0,
                ]);

                return $directions;
            }

        } catch (\Exception $e) {
            Log::error('Route generation failed', [
                'error' => $e->getMessage(),
                'origin' => $userLocation,
                'destination' => $trail['location'] ?? 'Unknown',
                'transportation' => $transportation,
            ]);
        }

        // Fallback if Google Directions API fails completely
        Log::info('Using fallback route generation');

        return $this->generateFallbackRouteData($userLocation, $trail, $stopovers, $sidetrips, $transportation);
    }

    /**
     * Generate fallback route data when Google Directions API fails
     */
    private function generateFallbackRouteData($userLocation, $trail, $stopovers, $sidetrips, $transportation)
    {
        $totalWaypoints = count($stopovers) + count($sidetrips);
        $baseDistance = 50; // Base 50km for typical trail journey
        $baseDuration = 3600; // Base 1 hour

        // Add time for each waypoint
        $waypointTime = $totalWaypoints * 900; // 15 minutes per waypoint

        // Adjust for commute mode
        if ($transportation === 'Commute') {
            $baseDuration = 7200; // Base 2 hours for commute (includes waiting times)
            $waypointTime = $totalWaypoints * 1200; // 20 minutes per waypoint for transit
        }

        // Create a proper legs structure for the daily schedule generator
        $fallbackLegs = [];
        $currentTime = 0;

        // Add departure leg
        $fallbackLegs[] = [
            'start_address' => $userLocation,
            'end_address' => $userLocation,
            'distance' => ['text' => '0 km', 'value' => 0],
            'duration' => ['text' => '0 min', 'value' => 0],
            'steps' => [
                [
                    'instruction' => 'Depart from current location',
                    'distance' => ['text' => '0 km'],
                    'duration' => ['text' => '0 min'],
                    'travel_mode' => 'DEPARTURE',
                    'start_location' => ['lat' => 0, 'lng' => 0],
                    'end_location' => ['lat' => 0, 'lng' => 0],
                ],
            ],
        ];

        // Add waypoint legs if any
        foreach ($stopovers as $stopover) {
            $currentTime += 900; // 15 minutes
            $fallbackLegs[] = [
                'start_address' => $userLocation,
                'end_address' => $stopover,
                'distance' => ['text' => '10 km', 'value' => 10000],
                'duration' => ['text' => gmdate('H:i', $currentTime), 'value' => $currentTime],
                'steps' => [
                    [
                        'instruction' => "Travel to {$stopover}",
                        'distance' => ['text' => '10 km'],
                        'duration' => ['text' => gmdate('H:i', $currentTime)],
                        'travel_mode' => $transportation === 'Commute' ? 'TRANSIT' : 'DRIVING',
                        'start_location' => ['lat' => 0, 'lng' => 0],
                        'end_location' => ['lat' => 0, 'lng' => 0],
                        'end_address' => $stopover,
                        'start_address' => $userLocation,
                        // Add realistic transit details for commute mode
                        'transit_details' => $transportation === 'Commute' ? [
                            'line' => [
                                'name' => 'Local Transport',
                                'vehicle' => ['name' => $this->getRandomTransitMode()],
                            ],
                            'departure_stop' => ['name' => $userLocation],
                            'arrival_stop' => ['name' => $stopover],
                            'num_stops' => rand(3, 8),
                            'departure_time' => ['text' => 'Now'],
                            'arrival_time' => ['text' => gmdate('H:i', $currentTime)],
                        ] : null,
                    ],
                ],
            ];
        }

        // Add final leg to destination
        $currentTime += 1800; // 30 minutes to final destination
        $finalDestination = $trail['location'] ?? 'Trail destination';
        $fallbackLegs[] = [
            'start_address' => $userLocation,
            'end_address' => $finalDestination,
            'distance' => ['text' => ($baseDistance + ($totalWaypoints * 10)).' km', 'value' => ($baseDistance + ($totalWaypoints * 10)) * 1000],
            'duration' => ['text' => gmdate('H:i', $baseDuration + $waypointTime), 'value' => $baseDuration + $waypointTime],
            'steps' => [
                [
                    'instruction' => 'Travel to trail destination',
                    'distance' => ['text' => ($baseDistance + ($totalWaypoints * 10)).' km'],
                    'duration' => ['text' => gmdate('H:i', $baseDuration + $waypointTime)],
                    'travel_mode' => $transportation === 'Commute' ? 'TRANSIT' : 'DRIVING',
                    'start_location' => ['lat' => 0, 'lng' => 0],
                    'end_location' => ['lat' => 0, 'lng' => 0],
                    'end_address' => $finalDestination,
                    'start_address' => $userLocation,
                    // Add realistic transit details for commute mode
                    'transit_details' => $transportation === 'Commute' ? [
                        'line' => [
                            'name' => 'Intercity Transport',
                            'vehicle' => ['name' => $this->getRandomTransitMode()],
                        ],
                        'departure_stop' => ['name' => $userLocation],
                        'arrival_stop' => ['name' => $finalDestination],
                        'num_stops' => rand(5, 15),
                        'departure_time' => ['text' => 'Now'],
                        'arrival_time' => ['text' => gmdate('H:i', $baseDuration + $waypointTime)],
                    ] : null,
                ],
            ],
        ];

        return [
            'total_distance' => ($baseDistance + ($totalWaypoints * 10)).' km',
            'total_duration' => gmdate('H:i', $baseDuration + $waypointTime),
            'total_duration_seconds' => $baseDuration + $waypointTime,
            'route_coordinates' => [],
            'transport_details' => [],
            'legs' => $fallbackLegs,
            'origin_coordinates' => null,
            'destination_coordinates' => null,
            'stopovers' => $stopovers,
            'sidetrips' => $sidetrips,
        ];
    }

    /**
     * Estimate transit cost based on modes used
     */
    private function estimateTransitCost($transitModes)
    {
        $costs = [
            'Bus' => 15, // PHP
            'Jeepney' => 12,
            'Train' => 25,
            'Subway' => 30,
            'Tram' => 20,
        ];

        $totalCost = 0;
        foreach ($transitModes as $mode) {
            $totalCost += $costs[$mode] ?? 15; // Default to bus fare
        }

        return [
            'total' => $totalCost,
            'currency' => 'PHP',
            'breakdown' => array_count_values($transitModes),
        ];
    }

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

    private function generateDailySchedule($date, $startTime, $routeData, $trail)
    {
        $scheduleDate = \Carbon\Carbon::parse($date);
        $currentTime = \Carbon\Carbon::parse("$date $startTime");

        // Fetch weather data for the planned date
        $weatherData = $this->fetchWeatherForDate($date, $trail);

        // Debug logging to see what we're working with
        Log::info('Daily schedule generation - initial data', [
            'startTime' => $startTime,
            'currentTime' => $currentTime->format('H:i'),
            'routeDataKeys' => array_keys($routeData),
            'hasLegs' => isset($routeData['legs']),
            'legsCount' => isset($routeData['legs']) ? count($routeData['legs']) : 0,
            'weatherData' => $weatherData,
        ]);

        $dailySchedule = [
            [
                'date' => $scheduleDate->format('Y-m-d'),
                'day_label' => 'Day 1',
                'day_number' => 1,
                'activities' => [],
            ],
        ];

        // Add departure activity
        $dailySchedule[0]['activities'][] = [
            'time' => $currentTime->format('H:i'),
            'location' => 'Departure from current location',
            'condition' => $this->getWeatherCondition($weatherData['departure_temp'] ?? null),
            'temperature' => $weatherData['departure_temp'] ?? 'Current temperature',
            'note' => 'Begin your journey to the trail',
            'coordinates' => $routeData['origin_coordinates'] ?? null,
            'transport_mode' => 'Departure',
            'duration' => '0 min',
            'activity_type' => 'departure',
        ];

        // Generate detailed route activities based on actual route data
        if (isset($routeData['legs']) && ! empty($routeData['legs'])) {
            $currentTime = $currentTime->copy();

            foreach ($routeData['legs'] as $legIndex => $leg) {
                Log::info("Processing leg {$legIndex}", [
                    'leg' => $leg,
                    'stepsCount' => isset($leg['steps']) ? count($leg['steps']) : 0,
                ]);

                // Skip first leg if it's just departure
                if ($legIndex === 0 && empty($leg['steps'])) {
                    continue;
                }

                foreach ($leg['steps'] as $stepIndex => $step) {
                    Log::info("Processing step {$stepIndex} in leg {$legIndex}", [
                        'step' => $step,
                        'currentTime' => $currentTime->format('H:i'),
                    ]);

                    // Skip first step if it's the same as departure
                    if ($legIndex === 0 && $stepIndex === 0) {
                        continue;
                    }

                    // Get actual step duration from Google API data - FIXED LOGIC
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

                    Log::info('Step duration calculated', [
                        'stepDuration' => $stepDuration,
                        'stepDurationFormatted' => gmdate('H:i', $stepDuration),
                        'currentTimeBefore' => $currentTime->format('H:i'),
                        'stepDurationRaw' => $step['duration'] ?? 'not set',
                        'legDuration' => $leg['duration'] ?? 'not set',
                        'stepKeys' => array_keys($step),
                        'durationTypes' => [
                            'duration_value' => isset($step['duration']['value']) ? (is_numeric($step['duration']['value']) ? 'numeric' : 'non-numeric') : 'not set',
                            'duration_seconds' => isset($step['duration_seconds']) ? (is_numeric($step['duration_seconds']) ? 'numeric' : 'non-numeric') : 'not set',
                            'duration_string' => isset($step['duration']) && is_string($step['duration']) ? 'string' : 'not string',
                            'leg_duration_value' => isset($leg['duration']['value']) ? (is_numeric($leg['duration']['value']) ? 'numeric' : 'non-numeric') : 'not set',
                        ],
                    ]);

                    // Add duration to current time for next activity
                    if ($stepDuration > 0) {
                        $currentTime->addSeconds($stepDuration);
                        Log::info('Time updated', [
                            'currentTimeAfter' => $currentTime->format('H:i'),
                        ]);
                    }

                    // Create detailed activity based on travel mode
                    $activity = $this->createDetailedActivity($step, $currentTime, $weatherData, $legIndex, $stepIndex);

                    if ($activity) {
                        $dailySchedule[0]['activities'][] = $activity;
                        Log::info('Activity added', [
                            'activity' => $activity,
                        ]);
                    }
                }
            }
        } else {
            Log::info('No legs found, using estimated activities');
            // Generate estimated schedule when no detailed route data is available
            $dailySchedule[0]['activities'] = array_merge(
                $dailySchedule[0]['activities'],
                $this->generateEstimatedActivities($routeData, $currentTime, $weatherData)
            );
        }

        // Add arrival at trail
        $dailySchedule[0]['activities'][] = [
            'time' => $currentTime->format('H:i'),
            'location' => $trail['location'] ?? 'Trail destination',
            'condition' => $this->getWeatherCondition($weatherData['trail_temp'] ?? null),
            'temperature' => $weatherData['trail_temp'] ?? 'Trail temperature',
            'note' => 'Arrived at trail - ready to start hiking',
            'coordinates' => $routeData['destination_coordinates'] ?? null,
            'transport_mode' => 'Arrival',
            'duration' => '0 min',
            'activity_type' => 'arrival',
        ];

        // Calculate return journey timing (assuming 4 hours for hiking)
        $hikingDuration = 4 * 3600; // 4 hours in seconds
        $returnTime = $currentTime->copy()->addSeconds($hikingDuration);

        $dailySchedule[0]['activities'][] = [
            'time' => $returnTime->format('H:i'),
            'location' => 'Trail destination',
            'condition' => $this->getWeatherCondition($weatherData['trail_temp'] ?? null),
            'temperature' => $weatherData['trail_temp'] ?? 'Trail temperature',
            'note' => 'Start return journey',
            'coordinates' => $routeData['destination_coordinates'] ?? null,
            'transport_mode' => 'Return',
            'duration' => '0 min',
            'activity_type' => 'return_start',
        ];

        // Add return journey activities with proper timing
        if (isset($routeData['legs']) && ! empty($routeData['legs'])) {
            $returnLegs = array_reverse($routeData['legs']);
            foreach ($returnLegs as $legIndex => $leg) {
                foreach ($leg['steps'] as $stepIndex => $step) {
                    // Get actual step duration for return journey - FIXED LOGIC
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

                    // Add duration to return time for next activity
                    if ($stepDuration > 0) {
                        $returnTime->addSeconds($stepDuration);
                    }

                    $returnActivity = $this->createReturnActivity($step, $returnTime, $weatherData, $legIndex, $stepIndex);
                    if ($returnActivity) {
                        $dailySchedule[0]['activities'][] = $returnActivity;
                    }
                }
            }
        }

        // Add final arrival back home
        $dailySchedule[0]['activities'][] = [
            'time' => $returnTime->format('H:i'),
            'location' => 'Arrived back home',
            'condition' => $this->getWeatherCondition($weatherData['departure_temp'] ?? null),
            'temperature' => $weatherData['departure_temp'] ?? 'Current temperature',
            'note' => 'Journey completed - safely back home',
            'coordinates' => $routeData['origin_coordinates'] ?? null,
            'transport_mode' => 'Home',
            'duration' => '0 min',
            'activity_type' => 'return_home',
        ];

        Log::info('Daily schedule generation completed', [
            'finalSchedule' => $dailySchedule,
            'activitiesCount' => count($dailySchedule[0]['activities']),
        ]);

        return $dailySchedule;
    }

    /**
     * Create detailed activity for daily schedule
     */
    private function createDetailedActivity($step, $currentTime, $weatherData, $legIndex, $stepIndex)
    {
        // Ensure all values are properly converted to strings
        $endAddress = is_array($step['end_address'] ?? null) ? json_encode($step['end_address']) : ($step['end_address'] ?? 'Route waypoint');

        // Use actual addresses from Google API when available
        if (isset($step['end_address']) && ! empty($step['end_address'])) {
            $endAddress = $step['end_address'];
        } elseif (isset($step['start_address']) && ! empty($step['start_address'])) {
            $endAddress = $step['start_address'];
        } else {
            // Try to get address from leg level
            $endAddress = $this->getAddressFromStep($step, $legIndex, $stepIndex);
        }

        // If still generic, try to extract from instruction
        if ($endAddress === 'Route waypoint' || $endAddress === 'Transit Station' || $endAddress === 'Walking Route' || $endAddress === 'Road Route') {
            if (isset($step['instruction']) && ! empty($step['instruction'])) {
                // Extract location from instruction like "Travel to Mt. Daraitan"
                if (preg_match('/Travel to (.+)/', $step['instruction'], $matches)) {
                    $endAddress = $matches[1];
                } elseif (preg_match('/Walk to (.+)/', $step['instruction'], $matches)) {
                    $endAddress = $matches[1];
                } elseif (preg_match('/Drive to (.+)/', $step['instruction'], $matches)) {
                    $endAddress = $matches[1];
                }
            }
        }

        // Extract duration from Google API data
        $duration = 'Duration TBD';
        if (isset($step['duration']['text'])) {
            $duration = $step['duration']['text'];
        } elseif (isset($step['duration']['value']) && is_numeric($step['duration']['value'])) {
            $durationSeconds = (int) $step['duration']['value'];
            $duration = gmdate('H:i', $durationSeconds);
        } elseif (isset($step['duration_seconds']) && is_numeric($step['duration_seconds'])) {
            $durationSeconds = (int) $step['duration_seconds'];
            $duration = gmdate('H:i', $durationSeconds);
        } elseif (isset($step['duration']) && is_string($step['duration'])) {
            // Parse duration string like "02:00" to formatted time
            $durationParts = explode(':', $step['duration']);
            if (count($durationParts) === 2) {
                $durationSeconds = (int) $durationParts[0] * 60 + (int) $durationParts[1];
                $duration = gmdate('H:i', $durationSeconds);
            } elseif (count($durationParts) === 3) {
                $durationSeconds = (int) $durationParts[0] * 3600 + (int) $durationParts[1] * 60 + (int) $durationParts[2];
                $duration = gmdate('H:i', $durationSeconds);
            } else {
                $duration = $step['duration'];
            }
        }

        $endLocation = is_array($step['end_location'] ?? null) ? json_encode($step['end_location']) : ($step['end_location'] ?? null);

        $activity = [
            'time' => $currentTime->format('H:i'),
            'location' => $endAddress,
            'condition' => $this->getWeatherCondition($weatherData['route_temp'] ?? null),
            'temperature' => $weatherData['route_temp'] ?? 'Route temperature',
            'note' => $step['instruction'] ?? 'Travel to destination',
            'coordinates' => $endLocation,
            'transport_mode' => $step['travel_mode'] ?? 'TRANSIT',
            'duration' => $duration,
            'activity_type' => 'route',
        ];

        // Add detailed transit information if available
        if (isset($step['transit_details'])) {
            $transit = $step['transit_details'];
            $vehicleType = $transit['line']['vehicle']['name'] ?? 'Public Transport';
            $lineName = $transit['line']['name'] ?? 'Unknown Line';
            $departureStop = $transit['departure_stop']['name'] ?? 'Unknown';
            $arrivalStop = $transit['arrival_stop']['name'] ?? 'Unknown';
            $numStops = $transit['num_stops'] ?? 0;

            // Enhanced note with specific transport details
            $activity['note'] = "Take {$vehicleType} {$lineName} from {$departureStop} to {$arrivalStop} ({$numStops} stops)";
            $activity['activity_type'] = 'transit';

            // Add detailed transit information
            $activity['transit_details'] = [
                'vehicle_type' => $vehicleType,
                'line_name' => $lineName,
                'departure_stop' => $departureStop,
                'arrival_stop' => $arrivalStop,
                'num_stops' => $numStops,
                'departure_time' => $transit['departure_time']['text'] ?? 'Unknown',
                'arrival_time' => $transit['arrival_time']['text'] ?? 'Unknown',
            ];
        }

        // Add walking details if available
        if (isset($step['walking_details'])) {
            $walking = $step['walking_details'];
            $duration = $step['duration']['text'] ?? 'Unknown';
            $instruction = $step['html_instructions'] ?? 'Walk to destination';

            $activity['note'] = "Walk {$duration} - {$instruction}";
            $activity['activity_type'] = 'walking';

            $activity['walking_details'] = [
                'duration_minutes' => $duration,
                'distance' => $step['distance']['text'] ?? 'Unknown',
                'instruction' => strip_tags($instruction),
            ];
        }

        return $activity;
    }

    /**
     * Get address from step or leg data
     */
    private function getAddressFromStep($step, $legIndex, $stepIndex)
    {
        // Try to get address from step level
        if (isset($step['end_address']) && ! empty($step['end_address'])) {
            return $step['end_address'];
        }

        if (isset($step['start_address']) && ! empty($step['start_address'])) {
            return $step['start_address'];
        }

        // Try to get address from instruction text
        if (isset($step['instruction']) && ! empty($step['instruction'])) {
            // Extract location from instruction like "Travel to Mt. Daraitan"
            if (preg_match('/Travel to (.+)/', $step['instruction'], $matches)) {
                return $matches[1];
            }
            if (preg_match('/Walk to (.+)/', $step['instruction'], $matches)) {
                return $matches[1];
            }
        }

        // Try to get address from HTML instructions
        if (isset($step['html_instructions']) && ! empty($step['html_instructions'])) {
            $cleanInstructions = strip_tags($step['html_instructions']);
            if (preg_match('/Travel to (.+)/', $cleanInstructions, $matches)) {
                return $matches[1];
            }
            if (preg_match('/Walk to (.+)/', $cleanInstructions, $matches)) {
                return $matches[1];
            }
        }

        // Try to get address from leg level (this would need to be passed from the calling method)
        // For now, return a descriptive location based on travel mode
        if (isset($step['travel_mode'])) {
            switch ($step['travel_mode']) {
                case 'TRANSIT':
                    return 'Transit Station';
                case 'WALKING':
                    return 'Walking Route';
                case 'DRIVING':
                    return 'Road Route';
                default:
                    return 'Route waypoint';
            }
        }

        return 'Route waypoint';
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
        } else {
            $startAddress = $this->getAddressFromStep($step, $legIndex, $stepIndex);
        }

        // If still generic, try to extract from instruction
        if ($startAddress === 'Route waypoint' || $startAddress === 'Transit Station' || $startAddress === 'Walking Route' || $startAddress === 'Road Route') {
            if (isset($step['instruction']) && ! empty($step['instruction'])) {
                // Extract location from instruction like "Travel to Mt. Daraitan"
                if (preg_match('/Travel to (.+)/', $step['instruction'], $matches)) {
                    $startAddress = $matches[1];
                } elseif (preg_match('/Walk to (.+)/', $step['instruction'], $matches)) {
                    $startAddress = $matches[1];
                } elseif (preg_match('/Drive to (.+)/', $step['instruction'], $matches)) {
                    $startAddress = $matches[1];
                }
            }
        }

        // Extract duration from Google API data for return journey
        $duration = 'Duration TBD';
        if (isset($step['duration']['text'])) {
            $duration = $step['duration']['text'];
        } elseif (isset($step['duration']['value']) && is_numeric($step['duration']['value'])) {
            $durationSeconds = (int) $step['duration']['value'];
            $duration = gmdate('H:i', $durationSeconds);
        } elseif (isset($step['duration_seconds']) && is_numeric($step['duration_seconds'])) {
            $durationSeconds = (int) $step['duration_seconds'];
            $duration = gmdate('H:i', $durationSeconds);
        } elseif (isset($step['duration']) && is_string($step['duration'])) {
            // Parse duration string like "02:00" to formatted time
            $durationParts = explode(':', $step['duration']);
            if (count($durationParts) === 2) {
                $durationSeconds = (int) $durationParts[0] * 60 + (int) $durationParts[1];
                $duration = gmdate('H:i', $durationSeconds);
            } elseif (count($durationParts) === 3) {
                $durationSeconds = (int) $durationParts[0] * 3600 + (int) $durationParts[1] * 60 + (int) $durationParts[2];
                $duration = gmdate('H:i', $durationSeconds);
            } else {
                $duration = $step['duration'];
            }
        }

        $activity = [
            'time' => $currentTime->format('H:i'),
            'location' => $startAddress,
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
            $activity['activity_type'] = 'return_transit';

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

        // Add walking details for return journey if available
        if (isset($step['walking_details'])) {
            $duration = $step['duration']['text'] ?? 'Unknown';
            $instruction = $step['html_instructions'] ?? 'Walk to destination';

            $activity['note'] = "Return: Walk {$duration} - {$instruction}";
            $activity['activity_type'] = 'return_walking';

            $activity['walking_details'] = [
                'duration_minutes' => $duration,
                'distance' => $step['distance']['text'] ?? 'Unknown',
                'instruction' => strip_tags($instruction),
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
            return '☀️ Hot';
        }
        if ($temp >= 25) {
            return '🌤️ Warm';
        }
        if ($temp >= 20) {
            return '⛅ Mild';
        }
        if ($temp >= 15) {
            return '🌥️ Cool';
        }
        if ($temp >= 10) {
            return '🌦️ Cold';
        }

        return '❄️ Very Cold';
    }

    private function fetchWeatherForDate($date, $trail)
    {
        try {
            // Get trail coordinates for weather API
            $coordinates = $trail['coordinates'] ?? null;
            $userLocation = Auth::user()->location ?? 'Quezon City, Philippines';

            Log::info('Weather fetching started', [
                'date' => $date,
                'trail' => $trail,
                'coordinates' => $coordinates,
                'userLocation' => $userLocation,
            ]);

            // Get current weather for departure location
            $departureWeather = $this->getCurrentWeather($userLocation);
            Log::info('Departure weather fetched', ['departureWeather' => $departureWeather]);

            // Get weather for trail destination if coordinates available
            $trailWeather = null;
            if ($coordinates) {
                $trailWeather = $this->getCurrentWeather($coordinates);
            } else {
                // Fallback: use trail location name
                $trailLocation = $trail['location'] ?? 'Trail destination';
                $trailWeather = $this->getCurrentWeather($trailLocation);

                // If trail location fails, try using a nearby major city
                if (! $trailWeather) {
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
                            $trailWeather = $this->getCurrentWeather($cityName);
                            if ($trailWeather) {
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

            // Get route weather (midpoint between departure and destination)
            $routeWeather = $this->getCurrentWeather($userLocation); // Simplified for now
            Log::info('Route weather fetched', ['routeWeather' => $routeWeather]);

            $result = [
                'departure_temp' => $departureWeather['temperature'] ?? 'Current temperature',
                'departure_condition' => $departureWeather['condition'] ?? 'Unknown',
                'trail_temp' => $trailWeather['temperature'] ?? 'Trail temperature',
                'trail_condition' => $trailWeather['condition'] ?? 'Unknown',
                'route_temp' => $routeWeather['temperature'] ?? 'Route temperature',
                'route_condition' => $routeWeather['condition'] ?? 'Unknown',
                'departure_weather' => $departureWeather,
                'trail_weather' => $trailWeather,
                'route_weather' => $routeWeather,
            ];

            Log::info('Weather data result', ['result' => $result]);

            return $result;

        } catch (\Exception $e) {
            Log::error('Weather fetching failed', ['error' => $e->getMessage()]);

            return [
                'departure_temp' => 'Current temperature',
                'departure_condition' => 'Unknown',
                'trail_temp' => 'Trail temperature',
                'trail_condition' => 'Unknown',
                'route_temp' => 'Route temperature',
                'route_condition' => 'Unknown',
            ];
        }
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
        // This could be enhanced with actual weather API integration
        $month = \Carbon\Carbon::parse($date)->month;

        if ($month >= 6 && $month <= 10) {
            return 'Rainy season - expect wet conditions. Bring rain gear and check weather forecasts.';
        } elseif ($month >= 11 && $month <= 2) {
            return 'Cool and dry season - ideal hiking conditions. Mornings may be chilly.';
        } else {
            return 'Transitional season - variable weather. Check local forecasts before departure.';
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

    private function getRandomTransitMode()
    {
        $modes = ['Bus', 'Jeepney', 'Train', 'Subway', 'Tram'];

        return $modes[array_rand($modes)];
    }
}

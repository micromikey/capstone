<?php

namespace App\Http\Controllers;

use App\Models\Itinerary;
use App\Models\Trail;
use App\Models\Location;
use App\Services\HybridRoutingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class ItineraryController extends Controller
{
    protected $routingService;

    public function __construct(HybridRoutingService $routingService)
    {
        $this->routingService = $routingService;
    }

    public function index()
    {
        // Get all itineraries for the authenticated user, ordered by most recent
        $itineraries = Itinerary::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        return view('hiker.itinerary.index', compact('itineraries'));
    }

    public function build()
    {
        // Check if user has completed assessment
        $hasAssessment = Auth::user()->latestAssessmentResult()->exists();

        if (! $hasAssessment) {
            return redirect()->route('assessment.instruction')
                ->with('warning', 'Please complete the Pre-Hike Self-Assessment first to generate a personalized itinerary.');
        }

    // Get available trails for suggestions (eager-load package to access package-side fields)
    $trails = Trail::with(['location', 'package'])->active()->get();

    // Debug: log first few trails and their package data to help diagnose missing package fields
    try {
        \Illuminate\Support\Facades\Log::debug('Itinerary::build loaded trails (sample)', [
            'count' => $trails->count(),
            'sample' => $trails->take(10)->map(function($t){
                return [
                    'id' => $t->id,
                    'trail_name' => $t->trail_name ?? $t->name ?? null,
                    'opening_time' => $t->opening_time ?? null,
                    'closing_time' => $t->closing_time ?? null,
                    'package' => $t->package ? [
                        'id' => $t->package->id ?? null,
                        'opening_time' => $t->package->opening_time ?? null,
                        'closing_time' => $t->package->closing_time ?? null,
                        'pickup_time' => $t->package->pickup_time ?? null,
                        'departure_time' => $t->package->departure_time ?? null,
                        'hours' => $t->package->hours ?? null,
                    ] : null,
                ];
            })->values()->all(),
        ]);
    } catch (\Throwable $e) { /* non-fatal */ }

        // Organization-provided side trips: aggregate from existing trails' package side_trips or legacy trail side_trips
        // Trails may store side_trips on the related `trail_packages` table; aggregate by reading the relation
        $sideTripStrings = $trails->map(function($t){
            return optional($t->package)->side_trips ?? $t->side_trips;
        })->filter()->all();

        $orgSideTrips = collect($sideTripStrings)
            ->flatMap(function ($s) {
                return array_values(array_filter(array_map('trim', explode(',', $s))));
            })
            ->unique()
            ->values()
            ->sort()
            ->map(function ($name) {
                return (object)['name' => $name];
            });

        // Get user's latest assessment for personalized recommendations
        $assessment = Auth::user()->latestAssessmentResult;

        return view('hiker.itinerary.build', compact('trails', 'assessment', 'orgSideTrips'));
    }

    public function buildWithTrail(Trail $trail)
    {
        // Check if user has completed assessment
        $hasAssessment = Auth::user()->latestAssessmentResult()->exists();

        if (! $hasAssessment) {
            return redirect()->route('assessment.instruction')
                ->with('warning', 'Please complete the Pre-Hike Self-Assessment first to generate a personalized itinerary.');
        }

        // Get available trails for suggestions (eager-load package to access package-side fields)
        $trails = Trail::with(['location', 'package'])->active()->get();

        // Organization-provided side trips: aggregate from existing trails' package side_trips or legacy trail side_trips
        $sideTripStrings = $trails->map(function($t){
            return optional($t->package)->side_trips ?? $t->side_trips;
        })->filter()->all();

        $orgSideTrips = collect($sideTripStrings)
            ->flatMap(function ($s) {
                return array_values(array_filter(array_map('trim', explode(',', $s))));
            })
            ->unique()
            ->values()
            ->sort()
            ->map(function ($name) {
                return (object)['name' => $name];
            });

        // Get user's latest assessment for personalized recommendations
        $assessment = Auth::user()->latestAssessmentResult;

        // Pass the selected trail as preselectedTrail to avoid naming conflicts in the view
        $preselectedTrail = $trail;

        return view('hiker.itinerary.build', compact('trails', 'assessment', 'orgSideTrips', 'preselectedTrail'));
    }

    public function generate(Request $request)
    {
        // Itinerary generation has been disabled per project configuration.
        return redirect()->back()->withErrors(['error' => 'Itinerary generation is disabled. Please use the itinerary builder.']);
    }

    public function show(Itinerary $itinerary)
    {
        // Check if user owns this itinerary
        if ($itinerary->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to itinerary.');
        }

        // Extract stored data from the itinerary
        $days = $itinerary->daily_schedule ?? [];
        $pacing = 1.0;
        $presenter = null;

        // Extract stored data
        $weatherData = $itinerary->weather_conditions ?? [];
        $trail = null;
        $build = $itinerary->transport_details ?? [];

        // If we have a trail_id, try to load the trail and fetch fresh weather
        if ($itinerary->trail_id) {
            $trail = \App\Models\Trail::find($itinerary->trail_id);
            
            // Fetch fresh weather data if trail has coordinates
            if ($trail && $trail->latitude && $trail->longitude) {
                try {
                    $weatherController = new \App\Http\Controllers\Api\WeatherController();
                    $weatherRequest = new \Illuminate\Http\Request([
                        'lat' => $trail->latitude,
                        'lng' => $trail->longitude
                    ]);
                    
                    $weatherResponse = $weatherController->getForecast($weatherRequest);
                    $freshWeatherData = $weatherResponse->getData(true);
                    
                    if (!isset($freshWeatherData['error'])) {
                        // Keep the original API data for dynamic weather AND add formatted data for backward compatibility
                        $formattedWeatherData = $this->formatWeatherDataForItinerary($freshWeatherData, $itinerary->start_date, $itinerary->duration_days ?? 1);
                        
                        // Start with original API data, then add formatted day data
                        $weatherData = $freshWeatherData;
                        foreach ($formattedWeatherData as $dayKey => $dayData) {
                            $weatherData[$dayKey] = $dayData;
                        }
                    }
                } catch (\Exception $e) {
                    Log::warning('Failed to fetch fresh weather data for trail: ' . $e->getMessage());
                    // Keep existing weather data as fallback
                }
            }
        }

        // Return the main generated itinerary view with all necessary data
        return view('hiker.itinerary.generated', compact('itinerary', 'days', 'pacing', 'presenter', 'weatherData', 'trail', 'build'));
    }

    /**
     * Show print-optimized view of the itinerary
     */
    public function printView(Itinerary $itinerary)
    {
        // Check if user owns this itinerary
        if ($itinerary->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to itinerary.');
        }

        // Extract stored data from the itinerary
        $weatherData = $itinerary->weather_conditions ?? [];
        $trail = null;
        $build = $itinerary->transport_details ?? [];

        // If we have a trail_id, try to load the trail and fetch fresh weather
        if ($itinerary->trail_id) {
            $trail = \App\Models\Trail::with('location')->find($itinerary->trail_id);
            
            // Fetch fresh weather data if trail has coordinates
            if ($trail && $trail->latitude && $trail->longitude) {
                try {
                    $weatherController = new \App\Http\Controllers\Api\WeatherController();
                    $weatherRequest = new \Illuminate\Http\Request([
                        'lat' => $trail->latitude,
                        'lng' => $trail->longitude
                    ]);
                    
                    $weatherResponse = $weatherController->getForecast($weatherRequest);
                    $freshWeatherData = $weatherResponse->getData(true);
                    
                    if (!isset($freshWeatherData['error'])) {
                        // Keep the original API data for dynamic weather AND add formatted data for backward compatibility
                        $formattedWeatherData = $this->formatWeatherDataForItinerary($freshWeatherData, $itinerary->start_date, $itinerary->duration_days ?? 1);
                        
                        // Start with original API data, then add formatted day data
                        $weatherData = $freshWeatherData;
                        foreach ($formattedWeatherData as $dayKey => $dayData) {
                            $weatherData[$dayKey] = $dayData;
                        }
                    }
                } catch (\Exception $e) {
                    Log::warning('Failed to fetch fresh weather data for trail: ' . $e->getMessage());
                    // Keep existing weather data as fallback
                }
            }
        }

        // Return the print-optimized view
        return view('hiker.itinerary.print', compact('itinerary', 'weatherData', 'trail', 'build'));
    }

    public function pdf(Itinerary $itinerary)
    {
        // Check if user owns this itinerary
        if ($itinerary->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to itinerary.');
        }

        return view('hiker.itinerary.pdf', compact('itinerary'));
    }

    /**
     * Persist a generated itinerary payload into the database.
     * Expected payload: 'itinerary' => array with keys matching Itinerary fields
     */
    public function store(Request $request)
    {
        $payload = $request->input('itinerary');

        // Expect a nested form array submitted as `itinerary[...]` inputs.
        if (! $payload || ! is_array($payload)) {
            return redirect()->back()->withErrors(['itinerary' => 'Invalid itinerary payload: expected structured itinerary data.']);
        }

        $validator = Validator::make($payload, [
            'title' => 'nullable|string|max:255',
            'start_date' => 'nullable|date',
            'start_time' => 'nullable',
            'duration_days' => 'nullable|integer|min:1',
            'nights' => 'nullable|integer|min:0',
            // allow flexible JSON for daily_schedule
            'daily_schedule' => 'nullable',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Build itinerary record
        $it = new Itinerary();
        $it->user_id = Auth::id();
        $it->title = $payload['title'] ?? ($payload['route_description'] ?? 'Generated Itinerary');
        $it->trail_name = $payload['trail_name'] ?? $payload['trail'] ?? null;
        $it->duration_days = intval($payload['duration_days'] ?? 1);
        $it->nights = intval($payload['nights'] ?? max(0, $it->duration_days - 1));
        $it->start_date = $payload['start_date'] ?? null;
        $it->start_time = $payload['start_time'] ?? null;

        // Store JSON fields used by the view/model
        $it->daily_schedule = $payload['daily_schedule'] ?? $payload['schedule'] ?? $payload['days'] ?? [];
        $it->transport_details = $payload['transport_details'] ?? $payload['build'] ?? $payload['transport'] ?? [];
        $it->departure_info = $payload['departure_info'] ?? $payload['departure'] ?? null;
        $it->arrival_info = $payload['arrival_info'] ?? $payload['arrival'] ?? null;
        $it->route_data = $payload['route_data'] ?? $payload['route'] ?? null;
        $it->route_summary = $payload['route_summary'] ?? null;
        $it->weather_conditions = $payload['weather_data'] ?? $payload['weather'] ?? null;
        $it->route_description = $payload['route_description'] ?? null;
        $it->stopovers = $payload['stopovers'] ?? $payload['stop_overs'] ?? [];
        $it->sidetrips = $payload['sidetrips'] ?? $payload['side_trips'] ?? [];

        // Additional meta
        $it->route_coordinates = $payload['route_coordinates'] ?? null;

    // Map commonly-provided builder metadata into Itinerary model fields so the generated
    // view can display distance, elevation, difficulty, and estimated durations.
    $it->trail_id = $payload['trail_id'] ?? $payload['trail'] ?? $it->trail_id ?? null;
    $it->distance = $payload['distance_km'] ?? $payload['distance'] ?? $payload['length'] ?? $it->distance ?? null;
    $it->elevation_gain = $payload['elevation_m'] ?? $payload['elevation_gain'] ?? $payload['elevation'] ?? $it->elevation_gain ?? null;
    $it->difficulty_level = $payload['difficulty'] ?? $payload['difficulty_level'] ?? $it->difficulty_level ?? null;
    $it->estimated_duration = $payload['estimated_time'] ?? $payload['estimated_duration'] ?? $it->estimated_duration ?? null;
    $it->best_time_to_hike = $payload['best_season'] ?? $payload['best_time_to_hike'] ?? $it->best_time_to_hike ?? null;

    // Ensure we don't lose scalar metadata if table lacks dedicated columns: merge into route_data/meta JSON
    $routeData = $it->route_data ?? [];
    if (!is_array($routeData)) $routeData = (array) $routeData;
    $routeData['total_distance_km'] = $routeData['total_distance_km'] ?? ($payload['distance_km'] ?? $payload['distance'] ?? $payload['length'] ?? null);
    $routeData['elevation_gain_m'] = $routeData['elevation_gain_m'] ?? ($payload['elevation_m'] ?? $payload['elevation_gain'] ?? $payload['elevation'] ?? null);
    $routeData['estimated_duration_hours'] = $routeData['estimated_duration_hours'] ?? ($payload['estimated_time'] ?? $payload['estimated_duration'] ?? null);
    $routeData['difficulty'] = $routeData['difficulty'] ?? ($payload['difficulty'] ?? $payload['difficulty_level'] ?? null);
    $it->route_data = $routeData;

    // Wrap creation in a DB transaction and use Eloquent models for days/activities
    DB::transaction(function () use ($it, $payload) {
            $it->save();

            $dailySchedule = $it->daily_schedule ?? [];
            if (is_array($dailySchedule) && count($dailySchedule) > 0) {
                foreach ($dailySchedule as $dayIndex => $day) {
                    $dayModel = \App\Models\ItineraryDay::create([
                        'itinerary_id' => $it->id,
                        'day_index' => intval($dayIndex) + 1,
                        'date' => $day['date'] ?? null,
                        'meta' => $day['meta'] ?? null,
                    ]);

                    $activities = $day['activities'] ?? [];
                    if (is_array($activities)) {
                        foreach ($activities as $order => $act) {
                            \App\Models\ItineraryActivity::create([
                                'itinerary_day_id' => $dayModel->id,
                                'order' => intval($order),
                                'minutes_offset' => intval($act['minutes'] ?? 0),
                                'title' => $act['title'] ?? $act['description'] ?? null,
                                'description' => $act['description'] ?? null,
                                'location' => $act['location'] ?? null,
                                'type' => $act['type'] ?? $act['activity_type'] ?? null,
                                'transport' => $act['transport'] ?? $act['transit_details'] ?? null,
                                'weather' => $act['condition'] ?? $act['weather'] ?? null,
                                'notes' => $act['note'] ?? $act['notes'] ?? null,
                                'meta' => $act['meta'] ?? null,
                            ]);
                        }
                    }
                }
            }
        });

        return redirect()->route('itinerary.show', ['itinerary' => $it->id])->with('success', 'Itinerary saved successfully.');
    }

    // Itinerary generation helpers removed as feature is disabled.
    // If you need to restore generation, check previous commits for the full implementations.

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

    // generateRouteData removed — routing is handled elsewhere. Kept as placeholder.

    // Removed hardcoded fallback route generation - now using your APIs

    // Removed all hardcoded transit generation methods - now using your APIs

    // Removed estimateTransitCost - now using your APIs

    // generateDetailedSchedule removed — detailed scheduling disabled when feature is off.

    // generateDailySchedule removed — daily schedule generation disabled.

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

    // Itinerary generation helper removed: generateEstimatedActivities
    // Reason: Feature removed per user request. Original implementation is available in VCS if needed.

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

    // Itinerary generation helper removed: generateGearRecommendations
    // Reason: Feature removed per user request. Original implementation is available in VCS if needed.

    // Itinerary generation helper removed: generateSafetyTips
    // Reason: Feature removed per user request. Original implementation is available in VCS if needed.

    // Itinerary generation helper removed: generateRouteDescription
    // Reason: Feature removed per user request. Original implementation is available in VCS if needed.

    // Itinerary generation helper removed: generateWaypoints
    // Reason: Feature removed per user request. Original implementation is available in VCS if needed.

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

    // Itinerary generation helper removed: generateStaticMapUrl
    // Reason: Feature removed per user request. Original implementation is available in VCS if needed.

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

    /**
     * Format API weather data for itinerary system compatibility
     */
    private function formatWeatherDataForItinerary($apiWeatherData, $startDate, $durationDays)
    {
        if (!isset($apiWeatherData['forecast']) || !is_array($apiWeatherData['forecast'])) {
            return [];
        }

        $formattedData = [];
        $startDateTime = \Carbon\Carbon::parse($startDate);

        foreach ($apiWeatherData['forecast'] as $dayIndex => $dayData) {
            if ($dayIndex >= $durationDays) break;

            $dayNumber = $dayIndex + 1;
            
            // Extract weather info for different times of day from hourly forecasts
            $formattedData[$dayNumber] = [];

            // If we have hourly data for this day, use it
            if (isset($dayData['hourly_forecasts']) && is_array($dayData['hourly_forecasts'])) {
                foreach ($dayData['hourly_forecasts'] as $hourly) {
                    $time = $hourly['time'] ?? '';
                    if ($time) {
                        $formattedData[$dayNumber][$time] = $this->formatWeatherStringFromHourly($hourly);
                    }
                }
            }
            
            // Always provide default times with daily summary
            $defaultWeather = $this->formatWeatherStringFromDaily($dayData);
            
            if (!isset($formattedData[$dayNumber]['08:00'])) {
                $formattedData[$dayNumber]['08:00'] = $defaultWeather;
            }
            if (!isset($formattedData[$dayNumber]['12:00'])) {
                $formattedData[$dayNumber]['12:00'] = $defaultWeather;
            }
            if (!isset($formattedData[$dayNumber]['16:00'])) {
                $formattedData[$dayNumber]['16:00'] = $defaultWeather;
            }
            if (!isset($formattedData[$dayNumber]['20:00'])) {
                $formattedData[$dayNumber]['20:00'] = $defaultWeather;
            }
        }

        return $formattedData;
    }

    /**
     * Format hourly weather data into display string
     */
    private function formatWeatherStringFromHourly($hourlyData)
    {
        $condition = $hourlyData['condition'] ?? 'Fair';
        $temp = $hourlyData['temp'] ?? 25;
        
        return $condition . ' / ' . round($temp) . '°C';
    }

    /**
     * Format daily weather data into display string
     */
    private function formatWeatherStringFromDaily($dailyData)
    {
        $condition = $dailyData['condition'] ?? 'Fair';
        $temp = $dailyData['temp_midday'] ?? $dailyData['temp_max'] ?? 25;
        
        return $condition . ' / ' . round($temp) . '°C';
    }

    /**
     * Generate PDF from captured PNG image (base64)
     */
    public function generatePdf(Request $request)
    {
        try {
            // Log the incoming request details
            Log::info('PDF generation request received', [
                'has_base64' => $request->has('image_base64'),
                'base64_length' => $request->has('image_base64') ? strlen($request->input('image_base64')) : 0,
                'all_keys' => array_keys($request->all()),
            ]);

            $validator = Validator::make($request->all(), [
                'image_base64' => 'required|string', // Base64 encoded image
                'trail_id' => 'nullable|string',
                'trail_slug' => 'required|string'
            ]);

            if ($validator->fails()) {
                Log::error('PDF generation validation failed', [
                    'errors' => $validator->errors()->toArray(),
                    'input_keys' => array_keys($request->all())
                ]);
                return response()->json([
                    'error' => 'Invalid request', 
                    'details' => $validator->errors()
                ], 422);
            }

            $imageBase64 = $request->input('image_base64');
            $trailSlug = $request->input('trail_slug', 'itinerary');

            // Decode base64 image
            // Remove data:image/png;base64, prefix if present
            if (preg_match('/^data:image\/(\w+);base64,/', $imageBase64, $matches)) {
                $imageBase64 = substr($imageBase64, strpos($imageBase64, ',') + 1);
            }

            $imageData = base64_decode($imageBase64);
            
            if ($imageData === false) {
                Log::error('Failed to decode base64 image');
                return response()->json([
                    'error' => 'Invalid image data'
                ], 400);
            }

            // Save to temporary file
            $tempFile = tempnam(sys_get_temp_dir(), 'itinerary_');
            file_put_contents($tempFile, $imageData);

            Log::info('PDF generation started', [
                'trail_slug' => $trailSlug,
                'image_size' => strlen($imageData),
                'temp_file' => $tempFile
            ]);

            // Create PDF using TCPDF
            $pdf = new \TCPDF('P', 'mm', 'A4', true, 'UTF-8');
            
            // Set document information
            $pdf->SetCreator('HikeThere');
            $pdf->SetAuthor('HikeThere');
            $pdf->SetTitle('Hiking Itinerary - ' . $trailSlug);
            $pdf->SetSubject('Hiking Itinerary');

            // Remove default header/footer
            $pdf->setPrintHeader(false);
            $pdf->setPrintFooter(false);

            // Set margins to 0 for full-page image
            $pdf->SetMargins(0, 0, 0);
            $pdf->SetAutoPageBreak(false, 0);

            // Add a page
            $pdf->AddPage();

            // Use the temporary file path
            $imagePath = $tempFile;
            
            if (!file_exists($imagePath)) {
                throw new \Exception('Temporary image file not found');
            }
            
            $imageSize = @getimagesize($imagePath);
            
            if ($imageSize === false) {
                throw new \Exception('Unable to read image dimensions');
            }
            
            $imageWidth = $imageSize[0];
            $imageHeight = $imageSize[1];

            Log::info('Image dimensions', [
                'width' => $imageWidth,
                'height' => $imageHeight
            ]);

            // Calculate how many pages we need based on image height
            // A4 dimensions at 72 DPI: 595px x 842px
            // At scale 2 (from html2canvas): 1190px x 1684px per page
            $pageHeightPx = 1684;
            $numPages = ceil($imageHeight / $pageHeightPx);

            Log::info('PDF pages calculated', ['pages' => $numPages]);

            // For simplicity, let's just fit the entire image on pages
            // TCPDF will handle scaling automatically
            if ($numPages > 1) {
                // Multiple pages needed
                for ($i = 0; $i < $numPages; $i++) {
                    if ($i > 0) {
                        $pdf->AddPage();
                    }
                    
                    // Add the full image - TCPDF will scale it
                    $pdf->Image(
                        $imagePath,
                        0, 0, // x, y position
                        210, // width (A4 width in mm)
                        0, // height (0 = auto calculate to maintain aspect ratio)
                        '', // image type (auto-detect)
                        '', // link
                        '', // align
                        false, // resize
                        300, // dpi
                        '', // palign
                        false, // ismask
                        false, // imgmask
                        0, // border
                        false, // fitbox
                        false, // hidden
                        true // fitonpage
                    );
                }
            } else {
                // Single page - fit the entire image
                $pdf->Image(
                    $imagePath,
                    0, 0, // x, y position
                    210, // width (A4 width in mm)  
                    0, // height (0 = auto, maintains aspect ratio)
                    '', // image type
                    '', // link
                    '', // align
                    false, // resize
                    300, // dpi
                    '', // palign
                    false, // ismask
                    false, // imgmask
                    0, // border
                    false, // fitbox
                    false, // hidden
                    true // fitonpage
                );
            }

            // Output PDF as download
            $pdfContent = $pdf->Output('', 'S'); // Get as string

            Log::info('PDF generated successfully', ['size' => strlen($pdfContent)]);

            // Clean up temporary file
            if (isset($tempFile) && file_exists($tempFile)) {
                @unlink($tempFile);
            }

            return response($pdfContent)
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'attachment; filename="' . $trailSlug . '-itinerary.pdf"');

        } catch (\Exception $e) {
            Log::error('PDF generation error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            // Clean up temporary file on error
            if (isset($tempFile) && file_exists($tempFile)) {
                @unlink($tempFile);
            }
            
            return response()->json([
                'error' => 'Failed to generate PDF',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}

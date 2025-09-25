<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Models\Trail;
use App\Services\TrailImageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class MapController extends Controller
{
    protected $imageService;

    public function __construct(TrailImageService $imageService)
    {
        $this->imageService = $imageService;
    }

    public function index()
    {
        return view('map.index');
    }

    public function demo()
    {
        return view('map.demo');
    }

    public function getTrails()
    {
    Log::info('MapController::getTrails called');

        try {
            $trails = Cache::remember('enhanced_map_trails', 1800, function () {
                Log::info('Fetching enhanced trails from database');

                $trails = Trail::with(['location', 'images', 'user', 'package'])
                    ->where('is_active', true)
                    ->get();

                Log::info("Found {$trails->count()} trails in database");

                return $trails->map(function ($trail) {
                    // Check if trail has location data
                    if (! $trail->location) {
                        Log::warning("Trail {$trail->id} ({$trail->trail_name}) has no location data");

                        return null;
                    }

                    Log::info("Processing trail: {$trail->trail_name} at location: {$trail->location->name}");

                    // Get enhanced primary image using the image service
                    try {
                        $primaryImageData = $this->imageService->getPrimaryTrailImage($trail);
                    } catch (\Exception $e) {
                        Log::error("Error getting primary image for trail {$trail->id}: ".$e->getMessage());
                        $primaryImageData = [
                            'url' => '/img/default-trail.jpg',
                            'source' => 'default',
                            'caption' => $trail->trail_name,
                        ];
                    }

                    return [
                        'id' => $trail->id,
                        'slug' => $trail->slug,
                        'name' => $trail->trail_name,
                        'mountain_name' => $trail->mountain_name,
                        'difficulty' => $trail->difficulty,
                        'length' => $trail->length,
                        'elevation_gain' => $trail->elevation_gain,
                        'elevation_high' => $trail->elevation_high,
                        'elevation_low' => $trail->elevation_low,
                        'estimated_time' => $trail->estimated_time_formatted,
                        'duration' => optional($trail->package)->duration ?? $trail->duration,
                        'best_season' => $trail->best_season,
                        'coordinates' => [
                            'lat' => (float) $trail->location->latitude,
                            'lng' => (float) $trail->location->longitude,
                        ],
                        'location_name' => $trail->location->name.', '.$trail->location->province,
                        'location' => [
                            'name' => $trail->location->name,
                            'province' => $trail->location->province,
                            'region' => $trail->location->region,
                            'country' => $trail->location->country,
                            'full_address' => $trail->location->name.', '.$trail->location->province.', '.$trail->location->region.', '.$trail->location->country,
                        ],
                        'image_url' => $primaryImageData['url'],
                        'image_source' => $primaryImageData['source'],
                        'image_caption' => $primaryImageData['caption'],
                        'description' => $trail->description,
                        'summary' => $trail->summary,
                        'features' => $trail->features ?? [],
                        'organization' => $trail->user->display_name ?? 'Unknown',
                        'organization_id' => $trail->user_id,
                        'price' => optional($trail->package)->price ?? $trail->price,
                        'permit_required' => $trail->permit_required,
                        'average_rating' => number_format($trail->average_rating, 1),
                        'total_reviews' => $trail->total_reviews,
                        // package schedule (if any)
                        'package' => $trail->package ? (function($pkg) {
                            $opening = $pkg->opening_time ?? null;
                            $closing = $pkg->closing_time ?? null;
                            $pickup = $pkg->pickup_time ?? null;
                            $departure = $pkg->departure_time ?? null;
                            $openingShort = null;
                            $closingShort = null;
                            $pickupShort = null;
                            $departureShort = null;
                            try {
                                if ($opening) $openingShort = Carbon::parse($opening)->format('H:i');
                                if ($closing) $closingShort = Carbon::parse($closing)->format('H:i');
                                if ($pickup) $pickupShort = Carbon::parse($pickup)->format('H:i');
                                if ($departure) $departureShort = Carbon::parse($departure)->format('H:i');
                            } catch (\Exception $e) {
                                // If parsing fails, fall back to raw values (safe)
                                Log::warning('Could not parse package times for package id '.($pkg->id ?? 'unknown').': '.$e->getMessage());
                            }

                            return [
                                'id' => $pkg->id ?? null,
                                'opening_time' => $opening,
                                'closing_time' => $closing,
                                'pickup_time' => $pickup,
                                'departure_time' => $departure,
                                // side trips stored on package (may be string, array or JSON)
                                'side_trips' => $pkg->side_trips ?? null,
                                'side_trips_meta' => $pkg->side_trips_meta ?? null,
                                'opening_time_short' => $openingShort,
                                'closing_time_short' => $closingShort,
                                'pickup_time_short' => $pickupShort,
                                'departure_time_short' => $departureShort,
                                'hours' => $pkg->hours ?? null,
                            ];
                        })($trail->package) : null,

                        // Enhanced hiking data
                        'trail_conditions' => $this->getTrailConditions($trail),
                        'cell_coverage' => $this->getCellCoverage($trail),
                        'water_sources' => $this->getWaterSources($trail),
                        'camping_allowed' => $trail->camping_allowed ?? false,
                        'last_updated' => $trail->updated_at->toISOString(),
                    ];
                })
                    ->filter() // Remove null entries
                    ->values(); // Re-index array
            });

            Log::info('Returning '.count($trails).' processed trails');

            return response()->json($trails);

        } catch (\Exception $e) {
            Log::error('Error in MapController::getTrails: '.$e->getMessage());
            Log::error('Stack trace: '.$e->getTraceAsString());

            return response()->json(['error' => 'Failed to load trails'], 500);
        }
    }

    public function getTrailDetails($id)
    {
        $trail = Trail::with(['location', 'images', 'reviews', 'user', 'package'])
            ->findOrFail($id);

        // Get enhanced images using the image service
        $images = $this->imageService->getTrailImages($trail, 8);

        return response()->json([
            'id' => $trail->id,
            'slug' => $trail->slug,
            'name' => $trail->trail_name,
            'difficulty' => $trail->difficulty,
            'length' => $trail->length,
            'elevation_gain' => $trail->elevation_gain,
            'coordinates' => [
                'lat' => (float) $trail->location->latitude,
                'lng' => (float) $trail->location->longitude,
            ],
            'location_name' => $trail->location->name,
            'images' => $images,
            'description' => $trail->description,
            // include package schedule if present
            'package' => $trail->package ? (function($pkg) {
                $opening = $pkg->opening_time ?? null;
                $closing = $pkg->closing_time ?? null;
                $pickup = $pkg->pickup_time ?? null;
                $departure = $pkg->departure_time ?? null;
                $openingShort = null;
                $closingShort = null;
                $pickupShort = null;
                $departureShort = null;
                try {
                    if ($opening) $openingShort = Carbon::parse($opening)->format('H:i');
                    if ($closing) $closingShort = Carbon::parse($closing)->format('H:i');
                    if ($pickup) $pickupShort = Carbon::parse($pickup)->format('H:i');
                    if ($departure) $departureShort = Carbon::parse($departure)->format('H:i');
                } catch (\Exception $e) {
                    Log::warning('Could not parse package times for package id '.($pkg->id ?? 'unknown').': '.$e->getMessage());
                }

                return [
                    'id' => $pkg->id ?? null,
                    'opening_time' => $opening,
                    'closing_time' => $closing,
                    'pickup_time' => $pickup,
                    'departure_time' => $departure,
                    'side_trips' => $pkg->side_trips ?? null,
                    'side_trips_meta' => $pkg->side_trips_meta ?? null,
                    'opening_time_short' => $openingShort,
                    'closing_time_short' => $closingShort,
                    'pickup_time_short' => $pickupShort,
                    'departure_time_short' => $departureShort,
                    'hours' => $pkg->hours ?? null,
                ];
            })($trail->package) : null,
            'estimated_time' => $trail->estimated_time,
            'reviews' => $trail->reviews->take(5)->map(function ($review) {
                return [
                    'rating' => $review->rating,
                    'comment' => $review->review,
                    'user_name' => $review->user->name,
                    'created_at' => $review->created_at->format('M d, Y'),
                ];
            }),
        ]);
    }

    /**
     * Get trail images with priority to organization images
     */
    public function getTrailImages($id)
    {
        $trail = Trail::with(['location', 'images', 'user'])
            ->findOrFail($id);

        $images = $this->imageService->getTrailImages($trail, 10);

        return response()->json([
            'trail_id' => $trail->id,
            'trail_name' => $trail->trail_name,
            'images' => $images,
        ]);
    }

    public function searchNearby(Request $request)
    {
        $request->validate([
            'lat' => 'required|numeric|between:-90,90',
            'lng' => 'required|numeric|between:-180,180',
            'radius' => 'numeric|min:1|max:100',
        ]);

        $lat = $request->lat;
        $lng = $request->lng;
        $radius = $request->radius ?? 25; // Default 25km radius

        $trails = Trail::with(['location', 'images'])
            ->where('is_active', true)
            ->get()
            ->filter(function ($trail) use ($lat, $lng, $radius) {
                // Check if trail has location data
                if (! $trail->location) {
                    return false;
                }

                $distance = $this->calculateDistance(
                    $lat, $lng,
                    $trail->location->latitude,
                    $trail->location->longitude
                );

                return $distance <= $radius;
            })
            ->map(function ($trail) {
                return [
                    'id' => $trail->id,
                    'slug' => $trail->slug,
                    'name' => $trail->trail_name,
                    'difficulty' => $trail->difficulty,
                    'length' => $trail->length,
                    'elevation_gain' => $trail->elevation_gain,
                    'coordinates' => [
                        'lat' => (float) $trail->location->latitude,
                        'lng' => (float) $trail->location->longitude,
                    ],
                    'location_name' => $trail->location->name,
                    'image_url' => $trail->images->first()?->url ?? '/img/default-trail.jpg',
                    'description' => $trail->description,
                    'estimated_time' => $trail->estimated_time,
                ];
            })
            ->values();

        return response()->json($trails);
    }

    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $theta = $lon1 - $lon2;
        $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +
                cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
        $dist = acos($dist);
        $dist = rad2deg($dist);
        $miles = $dist * 60 * 1.1515;

        return $miles * 1.609344; // Convert to kilometers
    }

    /**
     * Get trail conditions with enhanced data
     */
    private function getTrailConditions($trail)
    {
        // Mock trail conditions - replace with actual data source
        $conditions = [
            'Good - Dry and clear',
            'Fair - Some muddy sections',
            'Poor - Heavy rainfall, slippery',
            'Excellent - Perfect conditions',
            'Caution - Steep and rocky areas',
        ];

        return $conditions[array_rand($conditions)];
    }

    /**
     * Get cell coverage information
     */
    private function getCellCoverage($trail)
    {
        // Mock cell coverage data - replace with actual data
        $coverage = [
            'Full coverage',
            'Partial coverage',
            'Limited coverage at summit',
            'No coverage - emergency radio recommended',
            'Good coverage on main trail',
        ];

        return $coverage[array_rand($coverage)];
    }

    /**
     * Get water sources information
     */
    private function getWaterSources($trail)
    {
        // Mock water sources data - replace with actual data
        $sources = [
            'Natural springs available',
            'Bring your own water',
            'Stream crossings - treat water',
            'Water available at basecamp',
            'Limited water sources - bring extra',
        ];

        return $sources[array_rand($sources)];
    }

    /**
     * Get enhanced trail data for map display
     */
    public function getEnhancedTrails(Request $request)
    {
        $query = Trail::with(['location', 'images', 'user', 'reviews'])
            ->where('is_active', true);

        // Apply filters
        if ($request->has('difficulty')) {
            $query->where('difficulty', $request->difficulty);
        }

        if ($request->has('location_id')) {
            $query->where('location_id', $request->location_id);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('trail_name', 'like', "%{$search}%")
                    ->orWhere('mountain_name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $trails = $query->get()->map(function ($trail) {
            if (! $trail->location) {
                return null;
            }

            return [
                'id' => $trail->id,
                'name' => $trail->trail_name,
                'mountain_name' => $trail->mountain_name,
                'difficulty' => $trail->difficulty,
                'coordinates' => [
                    'lat' => (float) $trail->location->latitude,
                    'lng' => (float) $trail->location->longitude,
                ],
                'location_name' => $trail->location->name.', '.$trail->location->province,
                'image_url' => $trail->images->first()?->url ?? '/img/default-trail.jpg',
                'elevation_gain' => $trail->elevation_gain,
                'length' => $trail->length,
                'estimated_time' => $trail->estimated_time_formatted,
                'average_rating' => $trail->average_rating,
                'total_reviews' => $trail->total_reviews,
                'description' => $trail->description,
                'features' => $trail->features ?? [],
                'last_updated' => $trail->updated_at->toISOString(),
            ];
        })->filter()->values();

        return response()->json($trails);
    }

    /**
     * Get trail path coordinates for visualization
     */
    public function getTrailPaths()
    {
        $trails = Trail::with(['location'])
            ->where('is_active', true)
            ->whereNotNull('coordinates')
            ->get();

        $paths = $trails->map(function ($trail) {
            // Generate sample path coordinates - replace with actual GPX data
            $centerLat = $trail->coordinates['lat'] ?? $trail->location->latitude;
            $centerLng = $trail->coordinates['lng'] ?? $trail->location->longitude;

            $pathCoordinates = [];
            $numPoints = 10;

            for ($i = 0; $i < $numPoints; $i++) {
                $pathCoordinates[] = [
                    'lat' => $centerLat + (rand(-50, 50) / 10000),
                    'lng' => $centerLng + (rand(-50, 50) / 10000),
                ];
            }

            return [
                'id' => $trail->id,
                'name' => $trail->trail_name,
                'difficulty' => $trail->difficulty,
                'path_coordinates' => $pathCoordinates,
                'length' => $trail->length,
                'elevation_gain' => $trail->elevation_gain,
            ];
        });

        return response()->json($paths);
    }

    /**
     * Get elevation profile for a specific trail
     */
    public function getTrailElevation($id)
    {
        $trail = Trail::findOrFail($id);

        // Generate sample elevation data - replace with actual elevation API
        $elevationData = [
            'trail_id' => $trail->id,
            'total_gain' => $trail->elevation_gain,
            'max_elevation' => $trail->elevation_high,
            'min_elevation' => $trail->elevation_low,
            'points' => [],
        ];

        $numPoints = 20;
        $currentElevation = $trail->elevation_low ?? 500;
        $elevationStep = ($trail->elevation_high - $trail->elevation_low) / $numPoints;

        for ($i = 0; $i <= $numPoints; $i++) {
            $elevationData['points'][] = [
                'distance' => ($trail->length / $numPoints) * $i,
                'elevation' => round($currentElevation + ($elevationStep * $i)),
                'grade' => $elevationStep > 0 ? round(($elevationStep / ($trail->length / $numPoints)) * 100, 1) : 0,
            ];
        }

        return response()->json($elevationData);
    }

    /**
     * Get weather data for a location
     */
    public function getWeatherData(Request $request)
    {
        $lat = $request->query('lat');
        $lng = $request->query('lng');

        // Mock weather data - replace with actual weather API integration
        return response()->json([
            'temperature' => rand(15, 30),
            'conditions' => ['Sunny', 'Partly Cloudy', 'Cloudy', 'Light Rain', 'Clear'][rand(0, 4)],
            'wind_speed' => rand(5, 25),
            'wind_direction' => ['N', 'NE', 'E', 'SE', 'S', 'SW', 'W', 'NW'][rand(0, 7)],
            'visibility' => rand(5, 20),
            'humidity' => rand(40, 80),
            'pressure' => rand(1000, 1020),
            'uv_index' => rand(1, 10),
            'sunrise' => '06:00',
            'sunset' => '18:00',
            'forecast' => [
                ['day' => 'Today', 'high' => rand(25, 32), 'low' => rand(18, 24), 'conditions' => 'Sunny'],
                ['day' => 'Tomorrow', 'high' => rand(25, 32), 'low' => rand(18, 24), 'conditions' => 'Partly Cloudy'],
                ['day' => 'Day 3', 'high' => rand(25, 32), 'low' => rand(18, 24), 'conditions' => 'Cloudy'],
            ],
        ]);
    }
}

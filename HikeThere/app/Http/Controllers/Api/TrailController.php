<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Trail;
use App\Services\TrailImageService;
use Illuminate\Http\Request;

class TrailController extends Controller
{
    protected $imageService;

    public function __construct(TrailImageService $imageService)
    {
        $this->imageService = $imageService;
    }

    public function index(Request $request)
    {
        $query = Trail::active()
            ->with(['location', 'primaryImage', 'mapImage', 'user']);

        // Filter trails based on user authentication and following relationships
        if (auth()->check() && auth()->user()->user_type === 'hiker') {
            // For hikers, only show trails from organizations they follow
            $followingIds = auth()->user()->following()->pluck('users.id')->toArray();
            if (!empty($followingIds)) {
                $query->whereIn('user_id', $followingIds);
            } else {
                // If not following any organizations, return empty result
                return response()->json([]);
            }
        } elseif (auth()->check() && auth()->user()->user_type === 'organization') {
            // For organizations, only show their own trails
            $query->where('user_id', auth()->id());
        } else {
            // For unauthenticated users, return empty result
            return response()->json([]);
        }

        // Filter by location
        if ($request->has('location')) {
            $query->whereHas('location', function ($q) use ($request) {
                $q->where('slug', $request->location);
            });
        }

        // Filter by difficulty
        if ($request->has('difficulty')) {
            $query->where('difficulty', $request->difficulty);
        }

        // Search by name
        if ($request->has('search')) {
            $query->where(function($q) use ($request) {
                $q->where('trail_name', 'like', '%' . $request->search . '%')
                  ->orWhere('mountain_name', 'like', '%' . $request->search . '%');
            });
        }

        $trails = $query->get()->map(function ($trail) {
            return [
                'id' => $trail->id,
                'name' => $trail->trail_name,
                'mountain_name' => $trail->mountain_name,
                'slug' => $trail->slug,
                'difficulty' => $trail->difficulty,
                'length' => $trail->length,
                'elevation_gain' => $trail->elevation_gain,
                'elevation_high' => $trail->elevation_high,
                'elevation_low' => $trail->elevation_low,
                'estimated_time' => $trail->estimated_time_formatted,
                'summary' => $trail->summary,
                'description' => $trail->description,
                'average_rating' => number_format($trail->average_rating, 1),
                'total_reviews' => $trail->total_reviews,
                'location' => $trail->location->name . ', ' . $trail->location->province,
                'location_id' => $trail->location->id,
                'location_slug' => $trail->location->slug,
                'primary_image' => $trail->primaryImage?->url ?? $this->imageService->getTrailImage($trail, 'primary', 'medium'),
                'map_image' => $trail->mapImage?->url ?? $this->imageService->getTrailImage($trail, 'map', 'medium'),
                'features' => $trail->features,
                'organization' => $trail->user->display_name,
                'organization_id' => $trail->user->id,
                'price' => $trail->price,
                'duration' => $trail->duration,
                'best_season' => $trail->best_season,
                'coordinates' => $trail->coordinates,
                'gpx_file' => $trail->gpx_file ? \Storage::url($trail->gpx_file) : null,
            ];
        });

        return response()->json($trails);
    }

    public function show(Trail $trail)
    {
        $trail->load(['location', 'images', 'reviews.user', 'user']);

        return response()->json([
            'id' => $trail->id,
            'name' => $trail->trail_name,
            'mountain_name' => $trail->mountain_name,
            'slug' => $trail->slug,
            'difficulty' => $trail->difficulty_label,
            'length' => $trail->length,
            'elevation_gain' => $trail->elevation_gain,
            'elevation_high' => $trail->elevation_high,
            'elevation_low' => $trail->elevation_low,
            'estimated_time' => $trail->estimated_time_formatted,
            'summary' => $trail->summary,
            'description' => $trail->description,
            'average_rating' => $trail->average_rating,
            'total_reviews' => $trail->total_reviews,
            'location' => [
                'name' => $trail->location->name,
                'full_name' => $trail->location->name . ', ' . $trail->location->province,
                'coordinates' => [
                    'lat' => $trail->location->latitude,
                    'lng' => $trail->location->longitude,
                ]
            ],
            'organization' => [
                'name' => $trail->user->display_name,
                'id' => $trail->user->id,
            ],
            'images' => $trail->images->map(fn($img) => [
                'url' => $img->url,
                'type' => $img->image_type,
                'caption' => $img->caption,
            ]),
            'features' => $trail->features,
            'coordinates' => $trail->coordinates,
            'gpx_file' => $trail->gpx_file ? \Storage::url($trail->gpx_file) : null,
            'price' => $trail->price,
            'duration' => $trail->duration,
            'best_season' => $trail->best_season,
            'package_inclusions' => $trail->package_inclusions,
            // Hiking-specific fields
            'trail_conditions' => $trail->trail_conditions ?? 'Good - Dry and clear',
            'cell_coverage' => $trail->cell_coverage ?? 'Partial coverage',
            'water_sources' => $trail->water_sources ?? 'Bring your own water',
            'camping_allowed' => $trail->camping_allowed ?? false,
            'permit_required' => $trail->permit_required ?? false,
        ]);
    }

    /**
     * Get elevation data for a trail
     */
    public function getElevation(Trail $trail)
    {
        // Mock elevation data - replace with actual elevation API or GPX parsing
        $elevationData = [
            'trail_id' => $trail->id,
            'total_gain' => $trail->elevation_gain,
            'max_elevation' => $trail->elevation_high,
            'min_elevation' => $trail->elevation_low,
            'points' => []
        ];

        // Generate sample elevation points along the trail
        $numPoints = 20;
        $currentElevation = $trail->elevation_low;
        $elevationStep = ($trail->elevation_high - $trail->elevation_low) / $numPoints;

        for ($i = 0; $i < $numPoints; $i++) {
            $elevationData['points'][] = [
                'distance' => ($trail->length / $numPoints) * $i,
                'elevation' => round($currentElevation + ($elevationStep * $i)),
                'grade' => round(($elevationStep / ($trail->length / $numPoints)) * 100, 1)
            ];
        }

        return response()->json($elevationData);
    }

    /**
     * Get trail path coordinates for map visualization
     */
    public function getTrailPaths()
    {
        $trails = Trail::active()
            ->with(['location'])
            ->whereNotNull('coordinates')
            ->get();

        return $trails->map(function ($trail) {
            // Generate sample path coordinates if GPX file is not available
            $pathCoordinates = [];
            
            if ($trail->gpx_file) {
                // TODO: Parse GPX file to get actual coordinates
                // For now, generate sample coordinates around the trail center
                $centerLat = $trail->coordinates['lat'] ?? $trail->location->latitude;
                $centerLng = $trail->coordinates['lng'] ?? $trail->location->longitude;
                
                $numPoints = 10;
                for ($i = 0; $i < $numPoints; $i++) {
                    $pathCoordinates[] = [
                        'lat' => $centerLat + (rand(-50, 50) / 10000),
                        'lng' => $centerLng + (rand(-50, 50) / 10000)
                    ];
                }
            } else {
                // Generate a simple path from start to end
                $startLat = $trail->coordinates['lat'] ?? $trail->location->latitude;
                $startLng = $trail->coordinates['lng'] ?? $trail->location->longitude;
                
                $pathCoordinates = [
                    ['lat' => $startLat, 'lng' => $startLng],
                    ['lat' => $startLat + 0.001, 'lng' => $startLng + 0.001],
                    ['lat' => $startLat + 0.002, 'lng' => $startLng + 0.002]
                ];
            }

            return [
                'id' => $trail->id,
                'name' => $trail->trail_name,
                'difficulty' => $trail->difficulty,
                'path_coordinates' => $pathCoordinates,
                'length' => $trail->length,
                'elevation_gain' => $trail->elevation_gain
            ];
        });
    }

    /**
     * Search for trails near a specific location
     */
    public function searchNearby(Request $request)
    {
        $request->validate([
            'lat' => 'required|numeric|between:-90,90',
            'lng' => 'required|numeric|between:-180,180',
            'radius' => 'required|numeric|min:1|max:100'
        ]);

        $lat = $request->lat;
        $lng = $request->lng;
        $radius = $request->radius;

        // Calculate distance using Haversine formula
        $trails = Trail::active()
            ->with(['location', 'primaryImage'])
            ->get()
            ->filter(function ($trail) use ($lat, $lng, $radius) {
                if (!$trail->coordinates && !$trail->location) {
                    return false;
                }

                $trailLat = $trail->coordinates['lat'] ?? $trail->location->latitude;
                $trailLng = $trail->coordinates['lng'] ?? $trail->location->longitude;

                $distance = $this->calculateDistance($lat, $lng, $trailLat, $trailLng);
                return $distance <= $radius;
            })
            ->map(function ($trail) {
                return [
                    'id' => $trail->id,
                    'name' => $trail->trail_name,
                    'difficulty' => $trail->difficulty,
                    'length' => $trail->length,
                    'elevation_gain' => $trail->elevation_gain,
                    'location_name' => $trail->location->name . ', ' . $trail->location->province,
                    'image_url' => $trail->primaryImage?->url ?? $this->imageService->getTrailImage($trail, 'primary', 'medium'),
                    'coordinates' => $trail->coordinates ?? [
                        'lat' => $trail->location->latitude,
                        'lng' => $trail->location->longitude
                    ]
                ];
            })
            ->values();

        return response()->json($trails);
    }

    /**
     * Calculate distance between two points using Haversine formula
     */
    private function calculateDistance($lat1, $lng1, $lat2, $lng2)
    {
        $earthRadius = 6371; // Earth's radius in kilometers

        $latDelta = deg2rad($lat2 - $lat1);
        $lngDelta = deg2rad($lng2 - $lng1);

        $a = sin($latDelta / 2) * sin($latDelta / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($lngDelta / 2) * sin($lngDelta / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }
}
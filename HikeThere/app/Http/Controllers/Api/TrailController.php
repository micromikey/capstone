<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Trail;
use App\Services\TrailImageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class TrailController extends Controller
{
    protected $imageService;

    public function __construct(TrailImageService $imageService)
    {
        $this->imageService = $imageService;
    }

    public function search(Request $request)
    {
        $name = $request->get('name');

        if (! $name) {
            return response()->json(['error' => 'Trail name is required'], 400);
        }

        $trail = Trail::active()
            ->with(['location', 'user'])
            ->where('trail_name', 'like', '%'.$name.'%')
            ->orWhere('mountain_name', 'like', '%'.$name.'%')
            ->first();

        if (! $trail) {
            return response()->json(['error' => 'Trail not found'], 404);
        }

        return response()->json([
            'trail' => [
                'id' => $trail->id,
                'name' => $trail->trail_name,
                'mountain_name' => $trail->mountain_name,
                'difficulty' => $trail->difficulty,
                'coordinates' => $trail->coordinates,
                'location' => $trail->location ? $trail->location->name.', '.$trail->location->province : 'Location N/A',
            ],
        ]);
    }

    public function searchTrails(Request $request)
    {
        $query = $request->get('query', '');
        $category = $request->get('category', '');
        $filter = $request->get('filter', '');
        $page = $request->get('page', 1);
        $limit = $request->get('limit', 9);
        $offset = ($page - 1) * $limit;

        $trailsQuery = Trail::active()
            ->with(['location', 'user', 'primaryImage', 'reviews']);

        if (!empty($query)) {
            $searchTerm = trim($query);
            $trailsQuery->where(function ($q) use ($searchTerm) {
                $q->where('trail_name', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('mountain_name', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('name', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('difficulty', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('description', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('summary', 'LIKE', "%{$searchTerm}%")
                  ->orWhereHas('location', function ($locationQuery) use ($searchTerm) {
                      $locationQuery->where('name', 'LIKE', "%{$searchTerm}%")
                                   ->orWhere('province', 'LIKE', "%{$searchTerm}%");
                  });
            });
        }

        // Handle category filters
        if (!empty($category)) {
            switch (strtolower($category)) {
                case 'beginner':
                case 'easy':
                    $trailsQuery->whereIn('difficulty', ['beginner', 'easy']);
                    break;
                case 'challenging':
                case 'hard':
                    $trailsQuery->whereIn('difficulty', ['hard', 'very_hard', 'challenging', 'difficult']);
                    break;
                case 'popular':
                    $trailsQuery->withCount('reviews')
                               ->orderBy('reviews_count', 'desc');
                    break;
                case 'scenic':
                    $trailsQuery->where(function ($q) {
                        $q->where('description', 'LIKE', '%scenic%')
                          ->orWhere('description', 'LIKE', '%view%')
                          ->orWhere('description', 'LIKE', '%sunset%')
                          ->orWhere('description', 'LIKE', '%sunrise%')
                          ->orWhere('summary', 'LIKE', '%scenic%');
                    });
                    break;
            }
        }

        // Handle sorting filters
        if (!empty($filter)) {
            switch (strtolower($filter)) {
                case 'popular':
                    $trailsQuery->withCount('reviews')
                               ->orderBy('reviews_count', 'desc');
                    break;
                case 'newest':
                    $trailsQuery->orderBy('created_at', 'desc');
                    break;
                case 'shortest':
                    $trailsQuery->whereNotNull('length')
                               ->orderBy('length', 'asc');
                    break;
                case 'longest':
                    $trailsQuery->whereNotNull('length')
                               ->orderBy('length', 'desc');
                    break;
                default:
                    $trailsQuery->orderBy('created_at', 'desc');
                    break;
            }
        } else {
            // Default sorting if no filter is applied
            $trailsQuery->orderBy('created_at', 'desc');
        }

        // Get total count before applying limit
        $totalCount = $trailsQuery->count();
        
        // Apply pagination
        $trails = $trailsQuery->skip($offset)->take($limit)->get();

        $formattedTrails = $trails->map(function ($trail) {
            $primaryImage = $trail->primaryImage;
            $imageUrl = $primaryImage 
                ? $primaryImage->url
                : asset('img/default-trail.jpg');

            return [
                'id' => $trail->id,
                'name' => $trail->trail_name ?: $trail->name,
                'mountain_name' => $trail->mountain_name,
                'location' => $trail->location 
                    ? $trail->location->name . ', ' . $trail->location->province 
                    : 'Location N/A',
                'difficulty' => ucfirst($trail->difficulty),
                'distance' => $trail->length ? round($trail->length, 1) . ' km' : 'N/A',
                'duration' => $trail->estimated_time_formatted ?: $trail->duration ?: 'N/A',
                'rating' => $trail->average_rating ?: 0,
                'review_count' => $trail->total_reviews ?: 0,
                'image' => $imageUrl,
                'featured_image' => $imageUrl, // Add this for frontend compatibility
                'slug' => $trail->slug,
                'elevation_gain' => $trail->elevation_gain,
                'summary' => $trail->summary,
                'organization' => $trail->user ? $trail->user->display_name : 'Unknown',
                'created_at' => $trail->created_at->format('Y-m-d'),
                'tags' => $this->generateTrailTags($trail)
            ];
        });

        $hasMore = $totalCount > ($offset + $limit);

        return response()->json([
            'success' => true,
            'trails' => $formattedTrails,
            'total' => $totalCount,
            'current_page' => $page,
            'per_page' => $limit,
            'has_more' => $hasMore,
            'showing' => $trails->count(),
            'query' => $query,
            'category' => $category,
            'filter' => $filter
        ]);
    }

    private function generateTrailTags($trail)
    {
        $tags = [];
        
        // Add difficulty tags
        switch (strtolower($trail->difficulty)) {
            case 'easy':
                $tags[] = 'beginner';
                $tags[] = 'easy';
                break;
            case 'moderate':
                $tags[] = 'moderate';
                break;
            case 'hard':
                $tags[] = 'challenging';
                $tags[] = 'hard';
                break;
            case 'very_hard':
                $tags[] = 'challenging';
                $tags[] = 'advanced';
                break;
        }

        // Add popularity tags
        if ($trail->total_reviews > 10) {
            $tags[] = 'popular';
        }

        // Add scenic tags based on description
        $description = strtolower($trail->description . ' ' . $trail->summary);
        if (str_contains($description, 'scenic') || 
            str_contains($description, 'view') ||
            str_contains($description, 'sunset') ||
            str_contains($description, 'sunrise')) {
            $tags[] = 'scenic';
        }

        // Add other descriptive tags
        if (str_contains($description, 'loop')) {
            $tags[] = 'loop';
        }
        
        if (str_contains($description, 'family')) {
            $tags[] = 'family';
        }

        return $tags;
    }

    public function searchOSM(Request $request)
    {
        $request->validate([
            'mountain_name' => 'required|string|max:255',
            'trail_name' => 'required|string|max:255'
        ]);

        $mountainName = $request->get('mountain_name');
        $trailName = $request->get('trail_name');

        // Search in the OSM trails database with multiple strategies
        $osmTrails = Trail::where(function ($query) use ($mountainName, $trailName) {
                // Strategy 1: Combined search - "Mount Arayat Ambangeg Trail"
                $combined = $mountainName . ' ' . $trailName;
                $query->where('name', 'LIKE', '%' . $combined . '%');
                
                // Strategy 2: Mountain name in trail name - "Old Trail to Mount Arayat North Peak"
                $query->orWhere('name', 'LIKE', '%' . $mountainName . '%');
                
                // Strategy 3: Trail name only - "Makiling Trail", "Talamitam Trail"
                $query->orWhere('name', 'LIKE', '%' . $trailName . '%');
                
                // Strategy 4: Dash separated - "Mount Pulag - Ambangeg Trail"
                $dashCombined = $mountainName . ' - ' . $trailName;
                $query->orWhere('name', 'LIKE', '%' . $dashCombined . '%');
                
                // Strategy 5: Reverse dash - "Ambangeg Trail - Mount Pulag"
                $reverseDash = $trailName . ' - ' . $mountainName;
                $query->orWhere('name', 'LIKE', '%' . $reverseDash . '%');
            })
            ->whereNotNull('osm_id') // Only OSM trails
            ->whereNotNull('geometry')
            ->orderByRaw("
                CASE 
                    WHEN name LIKE ? THEN 1
                    WHEN name LIKE ? THEN 2
                    WHEN name LIKE ? THEN 3
                    WHEN name LIKE ? THEN 4
                    ELSE 5
                END
            ", [
                '%' . $mountainName . ' ' . $trailName . '%',
                '%' . $mountainName . ' - ' . $trailName . '%',
                '%' . $mountainName . '%',
                '%' . $trailName . '%'
            ])
            ->limit(5)
            ->get();

        $results = [];
        
        // Add OSM results
        foreach ($osmTrails as $trail) {
            $results[] = [
                'id' => $trail->id,
                'name' => $trail->name,
                'osm_id' => $trail->osm_id,
                'region' => $trail->region,
                'difficulty' => $trail->difficulty,
                'geometry' => $trail->geometry,
                'source' => 'osm',
                'created_at' => $trail->created_at,
            ];
        }

        // If OSM didn't return enough results, search Google Places
        if (count($results) < 3) {
            $googlePlacesResults = $this->searchGooglePlaces($mountainName, $trailName);
            $results = array_merge($results, $googlePlacesResults);
        }

        if (count($results) > 0) {
            return response()->json([
                'found' => true,
                'trails' => array_slice($results, 0, 5), // Limit to 5 total results
                'sources' => array_unique(array_column($results, 'source'))
            ]);
        }

        return response()->json([
            'found' => false,
            'message' => 'Trail not found in OpenStreetMap database or Google Places. You can upload a GPX file instead.'
        ]);
    }

    private function searchGooglePlaces($mountainName, $trailName)
    {
        $apiKey = config('services.google.maps_api_key');
        if (!$apiKey) {
            return [];
        }

        $results = [];
        $queries = [
            $mountainName . ' ' . $trailName . ' trail hiking',
            $mountainName . ' - ' . $trailName,
            $trailName . ' ' . $mountainName,
            $mountainName . ' trail',
            $trailName . ' hiking'
        ];

        foreach ($queries as $query) {
            try {
                $url = "https://maps.googleapis.com/maps/api/place/textsearch/json?" . http_build_query([
                    'query' => $query,
                    'location' => '14.5995,120.9842', // Philippines center
                    'radius' => 500000, // 500km radius
                    'key' => $apiKey,
                    'type' => 'tourist_attraction',
                ]);

                $response = file_get_contents($url);
                $data = json_decode($response, true);

                if ($data['status'] === 'OK' && !empty($data['results'])) {
                    foreach (array_slice($data['results'], 0, 2) as $place) {
                        // Filter for hiking/trail related places
                        $name = $place['name'];
                        $types = $place['types'] ?? [];
                        
                        if ($this->isHikingRelated($name, $types)) {
                            $results[] = [
                                'id' => 'google_' . $place['place_id'],
                                'name' => $name,
                                'place_id' => $place['place_id'],
                                'address' => $place['formatted_address'] ?? '',
                                'rating' => $place['rating'] ?? null,
                                'geometry' => [
                                    'lat' => $place['geometry']['location']['lat'],
                                    'lng' => $place['geometry']['location']['lng']
                                ],
                                'source' => 'google_places',
                                'types' => $types
                            ];
                        }
                    }
                }

                if (count($results) >= 3) break; // Stop if we have enough results
                
            } catch (\Exception $e) {
                \Log::warning('Google Places search failed: ' . $e->getMessage());
                continue;
            }
        }

        return array_unique($results, SORT_REGULAR);
    }

    private function isHikingRelated($name, $types)
    {
        $hikingKeywords = ['trail', 'hike', 'hiking', 'mountain', 'peak', 'summit', 'trek', 'climb'];
        $relevantTypes = ['tourist_attraction', 'natural_feature', 'park'];
        
        $nameHasKeyword = false;
        foreach ($hikingKeywords as $keyword) {
            if (stripos($name, $keyword) !== false) {
                $nameHasKeyword = true;
                break;
            }
        }
        
        $hasRelevantType = !empty(array_intersect($types, $relevantTypes));
        
        return $nameHasKeyword || $hasRelevantType;
    }

    public function getMapData(Request $request)
    {
        // For map data, we want to show all active trails regardless of authentication
        // This provides a comprehensive view for the map
        $trails = Trail::active()
            ->with(['location', 'primaryImage', 'reviews'])
            ->get()
            ->map(function ($trail) {
                return [
                    'id' => $trail->id,
                    'trail_name' => $trail->trail_name,
                    'mountain_name' => $trail->mountain_name,
                    'difficulty' => $trail->difficulty,
                    'difficulty_label' => $trail->difficulty_label,
                    'length' => $trail->length,
                    'elevation_gain' => $trail->elevation_gain,
                    'elevation_high' => $trail->elevation_high,
                    'elevation_low' => $trail->elevation_low,
                    'estimated_time' => $trail->estimated_time,
                    'estimated_time_formatted' => $trail->estimated_time_formatted,
                    'coordinates' => $trail->coordinates,
                        'gpx_file' => $trail->gpx_file ? Storage::url($trail->gpx_file) : null,
                    'average_rating' => $trail->average_rating,
                    'total_reviews' => $trail->total_reviews,
                    'location' => $trail->location ? [
                        'id' => $trail->location->id,
                        'name' => $trail->location->name,
                        'province' => $trail->location->province,
                        'latitude' => $trail->location->latitude,
                        'longitude' => $trail->location->longitude,
                    ] : null,
                    'primary_image' => $trail->primaryImage?->url ?? $this->imageService->getTrailImage($trail, 'primary', 'medium'),
                ];
            });

        return response()->json([
            'trails' => $trails,
            'total' => $trails->count(),
        ]);
    }

    public function getDetails(Trail $trail)
    {
        $trail->load(['location', 'images', 'reviews.user', 'user']);

        return response()->json([
            'id' => $trail->id,
            'trail_name' => $trail->trail_name,
            'mountain_name' => $trail->mountain_name,
            'slug' => $trail->slug,
            'difficulty' => $trail->difficulty,
            'difficulty_label' => $trail->difficulty_label,
            'difficulty_description' => $trail->difficulty_description,
            'length' => $trail->length,
            'elevation_gain' => $trail->elevation_gain,
            'elevation_high' => $trail->elevation_high,
            'elevation_low' => $trail->elevation_low,
            'estimated_time' => $trail->estimated_time,
            'estimated_time_formatted' => $trail->estimated_time_formatted,
            'coordinates' => $trail->coordinates,
                'gpx_file' => $trail->gpx_file ? Storage::url($trail->gpx_file) : null,
            'summary' => $trail->summary,
            'description' => $trail->description,
            'features' => $trail->features,
            'best_season' => $trail->best_season,
            'terrain_notes' => $trail->terrain_notes,
            'permit_required' => $trail->permit_required,
            'permit_process' => $trail->permit_process,
            'departure_point' => $trail->departure_point,
            'transport_options' => $trail->transport_options,
            'packing_list' => $trail->packing_list,
            'health_fitness' => $trail->health_fitness,
            'emergency_contacts' => $trail->emergency_contacts,
            'campsite_info' => $trail->campsite_info,
            'environmental_practices' => $trail->environmental_practices,
            'average_rating' => $trail->average_rating,
            'total_reviews' => $trail->total_reviews,
            'location' => $trail->location ? [
                'id' => $trail->location->id,
                'name' => $trail->location->name,
                'province' => $trail->location->province,
                'full_name' => $trail->location->name.', '.$trail->location->province,
                'latitude' => $trail->location->latitude,
                'longitude' => $trail->location->longitude,
            ] : null,
            'images' => $trail->images->map(function ($image) {
                return [
                    'id' => $image->id,
                    'url' => $image->url,
                    'image_type' => $image->image_type,
                    'is_primary' => $image->is_primary,
                    'sort_order' => $image->sort_order,
                ];
            }),
            'organization' => [
                'id' => $trail->user->id,
                'name' => $trail->user->display_name,
            ],
        ]);
    }

    public function index(Request $request)
    {
        $query = Trail::active()
            ->with(['location', 'primaryImage', 'mapImage', 'user']);

        // Filter trails based on user authentication and following relationships
        if (auth()->check() && auth()->user()->user_type === 'hiker') {
            // For hikers, only show trails from organizations they follow
            $followingIds = auth()->user()->following()->pluck('users.id')->toArray();
            if (! empty($followingIds)) {
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
            $query->where(function ($q) use ($request) {
                $q->where('trail_name', 'like', '%'.$request->search.'%')
                    ->orWhere('mountain_name', 'like', '%'.$request->search.'%');
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
                'location' => $trail->location->name.', '.$trail->location->province,
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
                    'gpx_file' => $trail->gpx_file ? Storage::url($trail->gpx_file) : null,
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
                'full_name' => $trail->location->name.', '.$trail->location->province,
                'coordinates' => [
                    'lat' => $trail->location->latitude,
                    'lng' => $trail->location->longitude,
                ],
            ],
            'organization' => [
                'name' => $trail->user->display_name,
                'id' => $trail->user->id,
            ],
            'images' => $trail->images->map(fn ($img) => [
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
            'points' => [],
        ];

        // Generate sample elevation points along the trail
        $numPoints = 20;
        $currentElevation = $trail->elevation_low;
        $elevationStep = ($trail->elevation_high - $trail->elevation_low) / $numPoints;

        for ($i = 0; $i < $numPoints; $i++) {
            $elevationData['points'][] = [
                'distance' => ($trail->length / $numPoints) * $i,
                'elevation' => round($currentElevation + ($elevationStep * $i)),
                'grade' => round(($elevationStep / ($trail->length / $numPoints)) * 100, 1),
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
            ->where(function($q){
                $q->whereNotNull('coordinates');
            })
            ->get();

        return $trails->map(function ($trail) {
            // Use coordinates as the source of trail path data
            $pathCoordinates = [];
            if (is_array($trail->coordinates) && count($trail->coordinates)) {
                $pathCoordinates = collect($trail->coordinates)
                    ->map(function($pt){
                        if (is_array($pt) && isset($pt['lat'],$pt['lng'])) return $pt;
                        if (is_array($pt) && count($pt)===2) return ['lat'=>$pt[1],'lng'=>$pt[0]]; // [lng,lat] fallback
                        return null;
                    })
                    ->filter()->values()->all();
            } elseif (is_array($trail->coordinates) && isset($trail->coordinates[0]) && is_array($trail->coordinates[0])) {
                $pathCoordinates = collect($trail->coordinates)
                    ->map(function($pt){
                        if (isset($pt['lat'],$pt['lng'])) return $pt;
                        if (count($pt)===2) return ['lat'=>$pt[1],'lng'=>$pt[0]]; // numeric index likely [lng,lat]
                        return null;
                    })->filter()->values()->all();
            } elseif (isset($trail->coordinates['lat'],$trail->coordinates['lng'])) {
                $pathCoordinates = $this->generateRouteFromPoint($trail->coordinates['lat'],$trail->coordinates['lng']);
            } else {
                $pathCoordinates = $this->generateRouteFromPoint($trail->location->latitude,$trail->location->longitude);
            }

            return [
                'id' => $trail->id,
                'name' => $trail->trail_name,
                'difficulty' => $trail->difficulty,
                'path_coordinates' => $pathCoordinates,
                'length' => $trail->length,
                'elevation_gain' => $trail->elevation_gain,
                'coordinate_generation_method' => $trail->coordinate_generation_method,
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
            'radius' => 'required|numeric|min:1|max:100',
        ]);

        $lat = $request->lat;
        $lng = $request->lng;
        $radius = $request->radius;

        // Calculate distance using Haversine formula
        $trails = Trail::active()
            ->with(['location', 'primaryImage'])
            ->get()
            ->filter(function ($trail) use ($lat, $lng, $radius) {
                if (! $trail->coordinates && ! $trail->location) {
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
                    'location_name' => $trail->location->name.', '.$trail->location->province,
                    'image_url' => $trail->primaryImage?->url ?? $this->imageService->getTrailImage($trail, 'primary', 'medium'),
                    'coordinates' => $trail->coordinates ?? [
                        'lat' => $trail->location->latitude,
                        'lng' => $trail->location->longitude,
                    ],
                ];
            })
            ->values();

        return response()->json($trails);
    }

    /**
     * Get trail route coordinates for a specific trail
     */
    public function getTrailRoute(Trail $trail)
    {
        $trail->load(['location']);
        if (is_array($trail->coordinates) && count($trail->coordinates)) {
            // Check if it's an array of coordinate points
            if (isset($trail->coordinates[0]) && is_array($trail->coordinates[0])) {
                $coordinates = $trail->coordinates;
            } elseif (isset($trail->coordinates['lat'],$trail->coordinates['lng'])) {
                // Single coordinate point
                $coordinates = $this->generateRouteFromPoint($trail->coordinates['lat'],$trail->coordinates['lng']);
            } else {
                $coordinates = $trail->coordinates;
            }
        } else {
            $coordinates = $this->generateRouteFromPoint($trail->location->latitude,$trail->location->longitude);
        }

        return response()->json([
            'id' => $trail->id,
            'name' => $trail->trail_name,
            'coordinates' => $coordinates,
            'length' => $trail->length,
            'elevation_gain' => $trail->elevation_gain,
            'estimated_time' => $trail->estimated_time_formatted,
            'difficulty' => $trail->difficulty,
            'gpx_file' => $trail->gpx_file ? \Storage::url($trail->gpx_file) : null,
            'coordinate_generation_method' => $trail->coordinate_generation_method,
        ]);
    }

    /**
     * Generate a sample route from a center point
     */
    private function generateRouteFromPoint($centerLat, $centerLng)
    {
        $coordinates = [];
        $numPoints = 15;

        for ($i = 0; $i < $numPoints; $i++) {
            $angle = ($i / $numPoints) * 2 * M_PI;
            $radius = 0.005 + (rand(-50, 50) / 10000);

            $coordinates[] = [
                'lat' => $centerLat + (cos($angle) * $radius),
                'lng' => $centerLng + (sin($angle) * $radius),
            ];
        }

        return $coordinates;
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

    public function getNearbyTrails(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'radius' => 'integer|min:1|max:50',
            'page' => 'integer|min:1',
            'per_page' => 'integer|min:1|max:50'
        ]);

        $latitude = $request->get('latitude');
        $longitude = $request->get('longitude');
        $radius = $request->get('radius', 5); // Default 5km
        $page = $request->get('page', 1);
        $perPage = $request->get('per_page', 3);

        try {
            // First, let's check if there are ANY trails in the database
            $totalTrails = Trail::active()->count();
            Log::info("Total active trails in database: " . $totalTrails);
            
            if ($totalTrails === 0) {
                return response()->json([
                    'success' => true,
                    'data' => [],
                    'pagination' => [
                        'current_page' => $page,
                        'per_page' => $perPage,
                        'total' => 0,
                        'total_pages' => 0,
                        'has_more_pages' => false
                    ],
                    'has_more_pages' => false,
                    'message' => 'No trails found in database',
                    'search_params' => [
                        'latitude' => $latitude,
                        'longitude' => $longitude,
                        'radius' => $radius . 'km'
                    ]
                ]);
            }

            // Calculate bounding box for more efficient querying
            $earthRadius = 6371; // km
            $latRadian = deg2rad($latitude);
            $lngRadian = deg2rad($longitude);
            
            $latDelta = $radius / $earthRadius;
            $lngDelta = $radius / ($earthRadius * cos($latRadian));
            
            $minLat = $latitude - rad2deg($latDelta);
            $maxLat = $latitude + rad2deg($latDelta);
            $minLng = $longitude - rad2deg($lngDelta);
            $maxLng = $longitude + rad2deg($lngDelta);

            // Query trails within bounding box first for performance
            $trails = Trail::active()
                ->with(['location', 'user', 'images'])
                ->where('is_active', true)
                ->whereNotNull('latitude')
                ->whereNotNull('longitude')
                ->whereBetween('latitude', [$minLat, $maxLat])
                ->whereBetween('longitude', [$minLng, $maxLng])
                ->get();

            Log::info("Trails in bounding box: " . $trails->count());

            // If no trails in bounding box, let's try to get ANY trails with coordinates
            if ($trails->isEmpty()) {
                $anyTrailsWithCoords = Trail::active()
                    ->whereNotNull('latitude')
                    ->whereNotNull('longitude')
                    ->count();
                    
                Log::info("Total trails with coordinates: " . $anyTrailsWithCoords);
                
                if ($anyTrailsWithCoords === 0) {
                    return response()->json([
                        'success' => true,
                        'data' => [],
                        'pagination' => [
                            'current_page' => $page,
                            'per_page' => $perPage,
                            'total' => 0,
                            'total_pages' => 0,
                            'has_more_pages' => false
                        ],
                        'has_more_pages' => false,
                        'message' => 'No trails with coordinates found in database',
                        'search_params' => [
                            'latitude' => $latitude,
                            'longitude' => $longitude,
                            'radius' => $radius . 'km'
                        ]
                    ]);
                }
            }

            // Filter by exact distance and calculate distance for each trail
            $nearbyTrails = $trails->map(function ($trail) use ($latitude, $longitude) {
                $distance = $this->calculateDistance(
                    $latitude, 
                    $longitude, 
                    $trail->latitude, 
                    $trail->longitude
                );
                
                $trail->distance = $distance;
                return $trail;
            })->filter(function ($trail) use ($radius) {
                return $trail->distance <= $radius;
            })->sortBy('distance');

            Log::info("Trails within radius: " . $nearbyTrails->count());

            // Paginate the results
            $total = $nearbyTrails->count();
            $offset = ($page - 1) * $perPage;
            $paginatedTrails = $nearbyTrails->slice($offset, $perPage);

            // Format trail data with images
            $formattedTrails = $paginatedTrails->map(function ($trail) {
                $images = $this->imageService->getTrailImages($trail);
                
                return [
                    'id' => $trail->id,
                    'name' => $trail->trail_name,
                    'location' => $trail->location ? 
                        $trail->location->name . ', ' . $trail->location->province : 
                        'Location not specified',
                    'difficulty_level' => ucfirst($trail->difficulty ?? 'unknown'),
                    'estimated_duration' => $trail->duration ? $trail->duration . ' hours' : null,
                    'distance' => round($trail->distance, 1),
                    'latitude' => $trail->latitude,
                    'longitude' => $trail->longitude,
                    'average_rating' => $trail->average_rating ? round($trail->average_rating, 1) : null,
                    'total_reviews' => $trail->total_reviews ?? 0,
                    'images' => $images,
                    'created_by' => $trail->user ? $trail->user->name : 'Unknown'
                ];
            })->values();

            return response()->json([
                'success' => true,
                'data' => $formattedTrails,
                'pagination' => [
                    'current_page' => $page,
                    'per_page' => $perPage,
                    'total' => $total,
                    'total_pages' => ceil($total / $perPage),
                    'has_more_pages' => $page < ceil($total / $perPage)
                ],
                'has_more_pages' => $page < ceil($total / $perPage),
                'debug_info' => [
                    'total_trails_in_db' => $totalTrails,
                    'trails_in_bounding_box' => $trails->count(),
                    'trails_within_radius' => $total
                ],
                'search_params' => [
                    'latitude' => $latitude,
                    'longitude' => $longitude,
                    'radius' => $radius . 'km'
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error in getNearbyTrails: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch nearby trails',
                'error' => $e->getMessage(),
                'debug_info' => [
                    'request_params' => $request->all()
                ]
            ], 500);
        }
    }

    public function debugTrails()
    {
        try {
            // Check total trails
            $totalTrails = Trail::count();
            
            // Check trails with coordinates (basic version)
            $trailsWithCoords = Trail::whereNotNull('latitude')
                ->whereNotNull('longitude')
                ->count();
                
            // Get a few sample trails
            $sampleTrails = Trail::whereNotNull('latitude')
                ->whereNotNull('longitude')
                ->select('id', 'trail_name', 'latitude', 'longitude')
                ->limit(5)
                ->get()
                ->toArray();

            return response()->json([
                'success' => true,
                'database_stats' => [
                    'total_trails' => $totalTrails,
                    'trails_with_coordinates' => $trailsWithCoords
                ],
                'sample_trails' => $sampleTrails,
                'timestamp' => now()->toISOString()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ], 500);
        }
    }
}

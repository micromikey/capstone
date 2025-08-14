<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Trail;
use App\Models\Location;
use Illuminate\Support\Facades\Cache;

class MapController extends Controller
{
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
        \Log::info('MapController::getTrails called');
        
        try {
            $trails = Cache::remember('map_trails', 3600, function () {
                \Log::info('Fetching trails from database');
                
                $trails = Trail::with(['location', 'images'])
                    ->where('is_active', true)
                    ->get();
                
                \Log::info("Found {$trails->count()} trails in database");
                
                return $trails->map(function ($trail) {
                    // Check if trail has location data
                    if (!$trail->location) {
                        \Log::warning("Trail {$trail->id} ({$trail->trail_name}) has no location data");
                        return null;
                    }
                    
                    \Log::info("Processing trail: {$trail->trail_name} at location: {$trail->location->name}");
                    
                    return [
                        'id' => $trail->id,
                        'slug' => $trail->slug,
                        'name' => $trail->trail_name,
                        'difficulty' => $trail->difficulty,
                        'length' => $trail->length,
                        'elevation_gain' => $trail->elevation_gain,
                        'coordinates' => [
                            'lat' => (float) $trail->location->latitude,
                            'lng' => (float) $trail->location->longitude
                        ],
                        'location_name' => $trail->location->name,
                        'image_url' => $trail->images->first()?->url ?? '/img/default-trail.jpg',
                        'description' => $trail->description,
                        'estimated_time' => $trail->estimated_time
                    ];
                })
                ->filter() // Remove null entries
                ->values(); // Re-index array
            });
            
            \Log::info("Returning " . count($trails) . " processed trails");
            return response()->json($trails);
            
        } catch (\Exception $e) {
            \Log::error('Error in MapController::getTrails: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json(['error' => 'Failed to load trails'], 500);
        }
    }

    public function getTrailDetails($id)
    {
        $trail = Trail::with(['location', 'images', 'reviews'])
            ->findOrFail($id);

        return response()->json([
            'id' => $trail->id,
            'slug' => $trail->slug,
            'name' => $trail->trail_name,
            'difficulty' => $trail->difficulty,
            'length' => $trail->length,
            'elevation_gain' => $trail->elevation_gain,
            'coordinates' => [
                'lat' => (float) $trail->location->latitude,
                'lng' => (float) $trail->location->longitude
            ],
            'location_name' => $trail->location->name,
            'images' => $trail->images->map(function ($image) {
                return $image->url;
            }),
            'description' => $trail->description,
            'estimated_time' => $trail->estimated_time,
            'reviews' => $trail->reviews->take(5)->map(function ($review) {
                return [
                    'rating' => $review->rating,
                    'comment' => $review->review,
                    'user_name' => $review->user->name,
                    'created_at' => $review->created_at->format('M d, Y')
                ];
            })
        ]);
    }

    public function searchNearby(Request $request)
    {
        $request->validate([
            'lat' => 'required|numeric|between:-90,90',
            'lng' => 'required|numeric|between:-180,180',
            'radius' => 'numeric|min:1|max:100'
        ]);

        $lat = $request->lat;
        $lng = $request->lng;
        $radius = $request->radius ?? 25; // Default 25km radius

        $trails = Trail::with(['location', 'images'])
            ->where('is_active', true)
            ->get()
            ->filter(function ($trail) use ($lat, $lng, $radius) {
                // Check if trail has location data
                if (!$trail->location) {
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
                        'lng' => (float) $trail->location->longitude
                    ],
                    'location_name' => $trail->location->name,
                    'image_url' => $trail->images->first()?->url ?? '/img/default-trail.jpg',
                    'description' => $trail->description,
                    'estimated_time' => $trail->estimated_time
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
}

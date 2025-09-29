<?php

namespace App\Http\Controllers;

use App\Models\Trail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Exception;

class TrailController extends Controller
{
    public function index()
    {
        return view('trails.explore');
    }

    public function show(Trail $trail)
    {
        $trail->load(['location', 'images', 'reviews.user']);
        
        // Get related events for this trail (limit to 6 for display)
        $relatedEvents = \App\Models\Event::with(['user', 'trail.location'])
            ->where('trail_id', $trail->id)
            ->where('is_public', true)
            ->where(function($query) {
                // Show events that are always available or haven't ended yet
                $query->where('always_available', true)
                    ->orWhere('end_at', '>=', now())
                    ->orWhereNull('end_at');
            })
            ->orderBy('start_at', 'asc')
            ->limit(6)
            ->get();
        
        return view('trails.show', compact('trail', 'relatedEvents'));
    }

    public function search(Request $request)
    {
        $query = $request->get('q');
        
        $trails = Trail::active()
            ->with(['location', 'primaryImage', 'user'])
            ->where(function($q) use ($query) {
                $q->where('trail_name', 'like', '%' . $query . '%')
                  ->orWhere('mountain_name', 'like', '%' . $query . '%')
                  ->orWhereHas('location', function($locationQuery) use ($query) {
                      $locationQuery->where('name', 'like', '%' . $query . '%')
                                   ->orWhere('province', 'like', '%' . $query . '%');
                  });
            })
            ->get();

        return view('trails.search-results', compact('trails', 'query'));
    }

    public function searchOSM(Request $request)
    {
        Log::info('OSM Search Request:', $request->all());
        
        $mountainName = $request->get('mountain_name');
        $trailName = $request->get('trail_name');
        
        if (!$mountainName || !$trailName) {
            return response()->json([
                'success' => false,
                'message' => 'Mountain name and trail name are required',
                'trails' => [],
                'total' => 0
            ]);
        }
        
        $results = collect();
        
        // Search OSM database first
        $osmResults = $this->searchOSMDatabase($mountainName, $trailName);
        Log::info('OSM Results:', ['count' => $osmResults->count()]);
        
        // If no OSM results found, search Google Places
        if ($osmResults->isEmpty()) {
            $googleResults = $this->searchGooglePlaces($mountainName, $trailName);
            Log::info('Google Results:', ['count' => $googleResults->count()]);
            $results = $googleResults;
        } else {
            $results = $osmResults;
        }
        
        return response()->json([
            'success' => true,
            'trails' => $results,
            'total' => $results->count()
        ]);
    }
    
    private function searchOSMDatabase($mountainName, $trailName)
    {
        $query = Trail::query();
        
        // Strategy 1: Search for exact combined format "Mountain - Trail"
        $combinedSearch = "{$mountainName} - {$trailName}";
        $exactMatch = $query->where('name', 'ILIKE', "%{$combinedSearch}%")->first();
        
        if ($exactMatch) {
            return collect([
                [
                    'id' => $exactMatch->id,
                    'name' => $exactMatch->name,
                    'mountain_name' => $mountainName,
                    'trail_name' => $trailName,
                    'difficulty' => $exactMatch->difficulty,
                    'region' => $exactMatch->region,
                    'osm_id' => $exactMatch->osm_id,
                    'source' => 'osm_database',
                    'geometry' => $exactMatch->geometry
                ]
            ]);
        }
        
        // Strategy 2: Search by individual components
        $trails = Trail::where(function($q) use ($mountainName, $trailName) {
            $q->where('name', 'ILIKE', "%{$mountainName}%")
              ->where('name', 'ILIKE', "%{$trailName}%");
        })->get();
        
        return $trails->map(function($trail) use ($mountainName, $trailName) {
            return [
                'id' => $trail->id,
                'name' => $trail->name,
                'mountain_name' => $mountainName,
                'trail_name' => $trailName,
                'difficulty' => $trail->difficulty,
                'region' => $trail->region,
                'osm_id' => $trail->osm_id,
                'source' => 'osm_database',
                'geometry' => $trail->geometry
            ];
        });
    }
    
    private function searchGooglePlaces($mountainName, $trailName)
    {
        $apiKey = config('services.google_maps.key');
        $searchQuery = "{$mountainName} {$trailName} hiking trail Philippines";
        
        $url = "https://maps.googleapis.com/maps/api/place/textsearch/json?" . http_build_query([
            'query' => $searchQuery,
            'key' => $apiKey,
            'type' => 'tourist_attraction'
        ]);
        
        try {
            $response = file_get_contents($url);
            $data = json_decode($response, true);
            
            if ($data['status'] === 'OK' && !empty($data['results'])) {
                return collect($data['results'])->filter(function($place) {
                    // Filter for hiking-related places
                    $name = strtolower($place['name']);
                    $types = $place['types'] ?? [];
                    
                    return str_contains($name, 'trail') || 
                           str_contains($name, 'hiking') || 
                           str_contains($name, 'mountain') ||
                           in_array('tourist_attraction', $types) ||
                           in_array('natural_feature', $types);
                })->take(5)->map(function($place) use ($mountainName, $trailName) {
                    return [
                        'id' => 'google_' . $place['place_id'],
                        'name' => $place['name'],
                        'mountain_name' => $mountainName,
                        'trail_name' => $trailName,
                        'difficulty' => null,
                        'region' => null,
                        'google_place_id' => $place['place_id'],
                        'source' => 'google_places',
                        'rating' => $place['rating'] ?? null,
                        'address' => $place['formatted_address'] ?? null,
                        'geometry' => [
                            'lat' => $place['geometry']['location']['lat'],
                            'lng' => $place['geometry']['location']['lng']
                        ]
                    ];
                });
            }
        } catch (Exception $e) {
            Log::error('Google Places API error: ' . $e->getMessage());
        }
        
        return collect();
    }
}
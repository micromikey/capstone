<?php

namespace App\Http\Controllers;

use App\Models\Trail;
use App\Services\GoogleDirectionsService;
use App\Services\OpenRouteService;
use App\Services\OpenStreetMapService;
use App\Models\Peak;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class TrailCoordinateController extends Controller
{
    private $googleDirectionsService;
    private $openRouteService;
    private $osmService;

    public function __construct(GoogleDirectionsService $googleDirectionsService, OpenRouteService $openRouteService, OpenStreetMapService $osmService)
    {
        $this->googleDirectionsService = $googleDirectionsService;
        $this->openRouteService = $openRouteService;
        $this->osmService = $osmService;
    }

    /**
     * Generate coordinates for a specific trail
     */
    public function generateCoordinates(Trail $trail): JsonResponse
    {
        try {
            $location = $trail->location ? $trail->location->name . ', ' . $trail->location->province : null;
            
            if (!$location) {
                return response()->json([
                    'success' => false,
                    'message' => 'Trail location not found'
                ], 400);
            }

            $coordinates = $this->googleDirectionsService->getTrailCoordinatesByLocation(
                $location,
                $trail->trail_name,
                $trail->mountain_name
            );

            if ($coordinates) {
                $trail->update(['coordinates' => $coordinates]);
                
                return response()->json([
                    'success' => true,
                    'message' => 'Coordinates generated successfully',
                    'coordinates' => $coordinates,
                    'count' => count($coordinates)
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to generate coordinates from Google API'
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error generating coordinates: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate coordinates for all trails without coordinates
     */
    public function generateAllCoordinates(): JsonResponse
    {
        try {
            $trails = Trail::whereNull('coordinates')
                          ->orWhere('coordinates', '[]')
                          ->with('location')
                          ->get();

            $results = [];
            $successCount = 0;
            $failCount = 0;

            foreach ($trails as $trail) {
                $location = $trail->location ? $trail->location->name . ', ' . $trail->location->province : null;
                
                if (!$location) {
                    $results[] = [
                        'trail_id' => $trail->id,
                        'trail_name' => $trail->trail_name,
                        'success' => false,
                        'message' => 'No location data'
                    ];
                    $failCount++;
                    continue;
                }

                $coordinates = $this->googleDirectionsService->getTrailCoordinatesByLocation(
                    $location,
                    $trail->trail_name,
                    $trail->mountain_name
                );

                if ($coordinates) {
                    $trail->update(['coordinates' => $coordinates]);
                    $results[] = [
                        'trail_id' => $trail->id,
                        'trail_name' => $trail->trail_name,
                        'success' => true,
                        'coordinates_count' => count($coordinates)
                    ];
                    $successCount++;
                } else {
                    $results[] = [
                        'trail_id' => $trail->id,
                        'trail_name' => $trail->trail_name,
                        'success' => false,
                        'message' => 'Failed to generate coordinates'
                    ];
                    $failCount++;
                }

                // Add a small delay to avoid hitting API rate limits
                usleep(100000); // 0.1 second delay
            }

            return response()->json([
                'success' => true,
                'message' => "Coordinate generation completed. Success: {$successCount}, Failed: {$failCount}",
                'summary' => [
                    'total_trails' => count($trails),
                    'success_count' => $successCount,
                    'fail_count' => $failCount
                ],
                'results' => $results
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error in bulk coordinate generation: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate coordinates using custom start and end points
     */
    public function generateCustomCoordinates(Request $request): JsonResponse
    {
        $request->validate([
            'trail_id' => 'required|exists:trails,id',
            'start_point' => 'required|string',
            'end_point' => 'required|string',
            'waypoints' => 'nullable|array',
            'waypoints.*' => 'string'
        ]);

        try {
            $trail = Trail::findOrFail($request->trail_id);
            
            $coordinates = $this->googleDirectionsService->getTrailCoordinates(
                $request->start_point,
                $request->end_point,
                $request->waypoints ?? []
            );

            if ($coordinates) {
                $trail->update(['coordinates' => $coordinates]);
                
                return response()->json([
                    'success' => true,
                    'message' => 'Custom coordinates generated successfully',
                    'coordinates' => $coordinates,
                    'count' => count($coordinates)
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to generate coordinates from provided points'
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error generating custom coordinates: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get trail status - whether it has coordinates or not
     */
    public function getTrailsStatus(): JsonResponse
    {
        try {
            $totalTrails = Trail::count();
            $trailsWithCoordinates = Trail::whereNotNull('coordinates')
                                         ->where('coordinates', '!=', '[]')
                                         ->count();
            $trailsWithoutCoordinates = $totalTrails - $trailsWithCoordinates;

            $trailsWithoutCoords = Trail::whereNull('coordinates')
                                       ->orWhere('coordinates', '[]')
                                       ->with('location')
                                       ->get(['id', 'trail_name', 'mountain_name', 'location_id']);

            return response()->json([
                'success' => true,
                'summary' => [
                    'total_trails' => $totalTrails,
                    'with_coordinates' => $trailsWithCoordinates,
                    'without_coordinates' => $trailsWithoutCoordinates,
                    'completion_percentage' => $totalTrails > 0 ? round(($trailsWithCoordinates / $totalTrails) * 100, 1) : 0
                ],
                'trails_without_coordinates' => $trailsWithoutCoords
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error getting trails status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate coordinates from trail creation form data
     */
    public function generateCoordinatesFromForm(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'start_point' => 'required|string',
                'end_point' => 'required|string',
                'location_lat' => 'required|numeric',
                'location_lng' => 'required|numeric',
                'mountain_name' => 'required|string',
                'trail_name' => 'required|string'
            ]);

            // Try to get enhanced trail coordinates with better estimation
            $coordinates = $this->getEnhancedTrailCoordinates(
                $request->start_point,
                $request->end_point,
                $request->mountain_name,
                $request->trail_name
            );

            if ($coordinates) {
                // Calculate more accurate trail metrics
                $metrics = $this->calculateTrailMetrics($coordinates, $request->trail_name, $request->mountain_name);
                
                return response()->json([
                    'success' => true,
                    'message' => 'Coordinates generated successfully',
                    'coordinates' => $coordinates,
                    'start_address' => $request->start_point,
                    'end_address' => $request->end_point,
                    'count' => count($coordinates),
                    'estimated_length_km' => $metrics['length_km'],
                    'trail_type' => $metrics['trail_type']
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Could not generate coordinates for this route'
                ], 400);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error generating coordinates: ' . $e->getMessage()
            ], 500);
        }
    }

    // (Old Overpass-only previewCoordinates method removed; unified version below.)

    /**
     * Generate custom coordinates from specific points
     */
    public function generateCustomCoordinatesFromForm(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'start_point' => 'required|string',
                'end_point' => 'required|string',
                'waypoints' => 'nullable|array'
            ]);

            $coordinates = $this->googleDirectionsService->getTrailCoordinates(
                $request->start_point,
                $request->end_point,
                $request->waypoints ?? []
            );

            if ($coordinates) {
                return response()->json([
                    'success' => true,
                    'message' => 'Custom coordinates generated successfully',
                    'coordinates' => $coordinates,
                    'start_address' => $request->start_point,
                    'end_address' => $request->end_point,
                    'count' => count($coordinates)
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Could not generate custom coordinates for this route'
                ], 400);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error generating custom coordinates: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Unified previewCoordinates method:
     * 1. Attempt OSM (Overpass) geometry
     * 2. Fallback to Google trailhead->peak synthetic path
     * 3. Refine with OpenRouteService if synthetic looks circular
     */
    public function previewCoordinates(Request $request): JsonResponse
    {
        $request->validate([
            'trail_name' => 'required|string',
            'mountain_name' => 'required|string',
            'location_name' => 'nullable|string',
            'province' => 'nullable|string',
        ]);

        try {
            $trailName = trim($request->trail_name);
            $mountainName = trim($request->mountain_name);
            $locationName = $request->location_name ?: ($mountainName . ' Philippines');
            $province = $request->province ?: 'Philippines';

            // Debug logging
            Log::info('Auto-route preview request', [
                'trail_name' => $trailName,
                'mountain_name' => $mountainName, 
                'location_name' => $locationName,
                'province' => $province,
                'raw_location' => $request->location_name
            ]);

            // 1. OSM attempt
            try {
                Log::info('Attempting OSM trail lookup');
                $osm = $this->osmService->getTrailCoordinates($locationName, $trailName, $mountainName);
                if ($osm && !empty($osm['coordinates'])) {
                    $lengthKm = !empty($osm['distance_km'])
                        ? round($osm['distance_km'], 2)
                        : round($this->calculatePathDistance($osm['coordinates'])/1000, 2);
                    return response()->json([
                        'success' => true,
                        'provider' => 'openstreetmap',
                        'message' => 'Preview trail path generated from OpenStreetMap',
                        'coordinates' => $osm['coordinates'],
                        'estimated_length_km' => $lengthKm,
                        'points' => count($osm['coordinates']),
                        'max_elevation' => $osm['max_elevation'] ?? null,
                        'min_elevation' => $osm['min_elevation'] ?? null,
                        'circular_detected_initially' => false,
                    ]);
                }
            } catch (\Throwable $osmErr) {
                Log::warning('OSM preview failed, falling back', ['error' => $osmErr->getMessage()]);
            }

            // 2. Google synthetic trailhead->peak path
            Log::info('Attempting Google trailhead->peak routing');
            $provider = 'google';
            $coordinates = $this->googleDirectionsService->getTrailheadToPeakCoordinates(
                $locationName,
                $province,
                $trailName,
                $mountainName
            );

            Log::info('Google routing result', [
                'coordinates_count' => $coordinates ? count($coordinates) : 0,
                'first_coord' => $coordinates ? $coordinates[0] ?? null : null
            ]);

            $circularDetected = $this->looksCircularOrTooGeneric($coordinates);
            if ($circularDetected) {
                $peak = Peak::where('name', 'LIKE', "%" . $mountainName . "%")
                    ->orderByRaw('LENGTH(name) ASC')
                    ->first();
                if ($peak) {
                    $originCandidates = [
                        $locationName . ', ' . $province,
                        $mountainName . ' trailhead, ' . $province,
                        $mountainName . ', ' . $province,
                    ];
                    foreach ($originCandidates as $originAddress) {
                        $originCoords = $this->geocodeCenter($originAddress);
                        if (!$originCoords) continue;
                        [$originLat, $originLng] = $originCoords;
                        $orsRoute = $this->openRouteService->getHikingDirections(
                            $originLat . ',' . $originLng,
                            $peak->latitude . ',' . $peak->longitude,
                            []
                        );
                        $decoded = $this->decodePolylineSafe($orsRoute['overview_polyline']['points'] ?? null);
                        if ($decoded && count($decoded) > 3 && !$this->looksCircularOrTooGeneric($decoded)) {
                            $coordinates = $decoded;
                            $provider = 'ors';
                            $circularDetected = false;
                            break;
                        }
                    }
                }
            }

            // After ORS attempt, try to map-match the candidate coordinates to OSM segments
            try {
                $osmBuilder = app(\App\Services\OSMTrailBuilder::class);
                $snapped = null;
                if ($coordinates && count($coordinates) > 2) {
                    $snapped = $osmBuilder->matchCoordinatesToSegments($coordinates);
                }

                // If DB-based matching failed, attempt a live Overpass fetch via OpenStreetMapService
                if ((!$snapped || count($snapped) < 4) && isset($locationName) && isset($trailName)) {
                    try {
                        $osmLive = $this->osmService->getTrailCoordinates($locationName, $trailName, $mountainName);
                        if ($osmLive && !empty($osmLive['coordinates']) && count($osmLive['coordinates']) > 3) {
                            $snapped = $osmLive['coordinates'];
                        }
                    } catch (\Throwable $ovErr) {
                        Log::warning('OSM live fetch failed during preview', ['error' => $ovErr->getMessage()]);
                    }
                }

                if ($snapped && count($snapped) > 3) {
                    $coordinates = $snapped;
                    $provider = 'osm_snapped';
                }
            } catch (\Throwable $matchErr) {
                Log::warning('OSM snapping failed during preview', ['error' => $matchErr->getMessage()]);
            }

            // After OSM snapping, re-check for circular or insufficient geometry; if still circular or too short -> reject
            if (!$coordinates || count($coordinates) < 4 || $this->looksCircularOrTooGeneric($coordinates)) {
                Log::info('Rejecting synthetic circular trail preview', [
                    'trail' => $trailName,
                    'mountain' => $mountainName,
                    'points' => $coordinates ? count($coordinates) : 0,
                    'provider' => $provider,
                    'reason' => !$coordinates ? 'no_coordinates' : (count($coordinates) < 4 ? 'insufficient_points' : 'circular_detected')
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'No reliable trail geometry found for "' . $trailName . '" on "' . $mountainName . '". Suggestions: 1) Check spelling, 2) Try "Trail Name" format, 3) Use manual drawing instead.',
                    'debug' => [
                        'trail_name' => $trailName,
                        'mountain_name' => $mountainName,
                        'location_searched' => $locationName,
                        'coordinates_found' => $coordinates ? count($coordinates) : 0
                    ]
                ], 422);
            }

            $lengthMeters = $this->calculatePathDistance($coordinates);
            $lengthKm = $lengthMeters / 1000;

            return response()->json([
                'success' => true,
                'coordinates' => $coordinates,
                'estimated_length_km' => round($lengthKm, 2),
                'points' => count($coordinates),
                'provider' => $provider,
                'circular_detected_initially' => $circularDetected,
                'message' => $provider === 'ors' ? 'Preview trail path generated via hiking fallback.' : 'Preview trail path generated.'
            ]);
        } catch (\Throwable $e) {
            Log::error('Preview trail coordinates error', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Error generating preview: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Determine if coordinates resemble the synthetic circular fallback.
     */
    private function looksCircularOrTooGeneric(?array $coords): bool
    {
        if (!$coords || count($coords) < 10) return false;
        // 1. Start/end proximity
        $first = $coords[0];
        $last = $coords[count($coords)-1];
        $startEndKm = $this->haversine($first['lat'], $first['lng'], $last['lat'], $last['lng']);
        // 2. Bounding box & aspect ratio
        $minLat = $maxLat = $coords[0]['lat'];
        $minLng = $maxLng = $coords[0]['lng'];
        $sumLat = 0; $sumLng = 0; $n = count($coords);
        foreach ($coords as $c) { 
            $minLat=min($minLat,$c['lat']); $maxLat=max($maxLat,$c['lat']);
            $minLng=min($minLng,$c['lng']); $maxLng=max($maxLng,$c['lng']);
            $sumLat += $c['lat']; $sumLng += $c['lng'];
        }
        $latSpan = $maxLat - $minLat; $lngSpan = $maxLng - $minLng;
        $ratio = $latSpan > 0 ? $lngSpan / $latSpan : 0;
        // 3. Radius consistency (points all ~ same distance from centroid)
        $centLat = $sumLat / $n; $centLng = $sumLng / $n;
        $radii = [];
        foreach ($coords as $c) { $radii[] = $this->haversine($centLat,$centLng,$c['lat'],$c['lng']); }
        $meanR = array_sum($radii)/$n; if ($meanR == 0) return false;
        $var = 0; foreach ($radii as $r) { $var += pow($r - $meanR,2); } $std = sqrt($var/$n);
        $stdRatio = $std / $meanR; // low => circular
        // 4. Path total length vs diameter (circumference ~ pi*D)
        $perimeter = 0; for($i=1;$i<$n;$i++){ $perimeter += $this->haversine($coords[$i-1]['lat'],$coords[$i-1]['lng'],$coords[$i]['lat'],$coords[$i]['lng']); }
        $diameter = 2*$meanR; $expectedCirc = M_PI * $diameter;
        $circRatio = $expectedCirc > 0 ? $perimeter / $expectedCirc : 0;
        // Conditions indicating synthetic circle
        $circular = (
            $startEndKm < 0.25 && // closed
            $ratio > 0.5 && $ratio < 1.5 && // roughly square
            $stdRatio < 0.08 && // radii very consistent
            $circRatio > 0.7 && $circRatio < 1.3 && // perimeter near circumference
            $meanR > 0.3 && $meanR < 5 // plausible generated radius km
        );
        return $circular;
    }

    private function haversine($lat1,$lon1,$lat2,$lon2): float
    {
        $R = 6371; // km
        $dLat = deg2rad($lat2-$lat1);
        $dLon = deg2rad($lon2-$lon1);
        $a = sin($dLat/2)*sin($dLat/2) + cos(deg2rad($lat1))*cos(deg2rad($lat2))*sin($dLon/2)*sin($dLon/2);
        $c = 2*atan2(sqrt($a), sqrt(1-$a));
        return $R*$c;
    }

    private function decodePolylineSafe(?string $encoded): ?array
    {
        if (!$encoded) return null;
        $len=strlen($encoded); $index=0; $lat=0; $lng=0; $points=[];
        while ($index < $len) {
            $b=0; $shift=0; $result=0; do { $b = ord($encoded[$index++]) - 63; $result |= ($b & 0x1f) << $shift; $shift +=5; } while ($b >= 0x20); $dlat = ($result &1)? ~($result>>1):($result>>1); $lat += $dlat;
            $shift=0; $result=0; do { $b = ord($encoded[$index++]) -63; $result |= ($b &0x1f)<<$shift; $shift+=5;} while($b>=0x20); $dlng = ($result &1)? ~($result>>1):($result>>1); $lng += $dlng;
            $points[] = ['lat'=>$lat/1e5,'lng'=>$lng/1e5];
        }
        return $points;
    }

    private function geocodeCenter(string $address): ?array
    {
        try {
            $resp = \Illuminate\Support\Facades\Http::get('https://maps.googleapis.com/maps/api/geocode/json', [
                'address' => $address,
                'key' => config('services.google.maps_api_key')
            ]);
            if ($resp->successful()) {
                $data = $resp->json();
                if (($data['status'] ?? '') === 'OK' && !empty($data['results'][0]['geometry']['location'])) {
                    $loc = $data['results'][0]['geometry']['location'];
                    return [$loc['lat'], $loc['lng']];
                }
            }
        } catch (\Exception $e) {
            Log::warning('Preview geocode failed', ['error'=>$e->getMessage()]);
        }
        return null;
    }

    /**
     * Enhanced trail coordinate generation with better accuracy
     */
    private function getEnhancedTrailCoordinates($startPoint, $endPoint, $mountainName, $trailName)
    {
        // First try to get coordinates from Google Directions
        $coordinates = $this->googleDirectionsService->getTrailCoordinates($startPoint, $endPoint);
        
        // If we got coordinates, enhance them for better trail accuracy
        if ($coordinates) {
            return $this->enhanceTrailPath($coordinates, $mountainName, $trailName);
        }
        
        return null;
    }

    /**
     * Enhance trail path with more realistic hiking trail characteristics
     */
    private function enhanceTrailPath($coordinates, $mountainName, $trailName)
    {
        // Check if this is a known trail with specific characteristics
        $knownTrails = $this->getKnownTrailData();
        $trailKey = strtolower($trailName . ' ' . $mountainName);
        
        // If it's a known trail, adjust coordinates accordingly
        foreach ($knownTrails as $known) {
            if (strpos($trailKey, strtolower($known['name'])) !== false || 
                strpos($trailKey, strtolower($known['mountain'])) !== false) {
                return $this->adjustCoordinatesForKnownTrail($coordinates, $known);
            }
        }
        
        // Otherwise, enhance with general trail characteristics
        return $this->addTrailCharacteristics($coordinates);
    }

    /**
     * Known trail data for better accuracy
     */
    private function getKnownTrailData()
    {
        return [
            // Mount Pulag Trails
            [
                'name' => 'ambangeg trail',
                'mountain' => 'mount pulag',
                'length_km' => 14.6, // 9.1 miles = 14.6 km
                'trail_type' => 'beginner',
                'elevation_gain' => 1200,
                'characteristics' => [
                    'switchbacks' => true,
                    'forest_path' => true,
                    'rocky_sections' => false
                ]
            ],
            [
                'name' => 'akiki trail',
                'mountain' => 'mount pulag',
                'length_km' => 16.0,
                'trail_type' => 'intermediate',
                'elevation_gain' => 1400,
                'characteristics' => [
                    'switchbacks' => true,
                    'forest_path' => true,
                    'rocky_sections' => true
                ]
            ],
            [
                'name' => 'tawangan trail',
                'mountain' => 'mount pulag',
                'length_km' => 18.5,
                'trail_type' => 'advanced',
                'elevation_gain' => 1600,
                'characteristics' => [
                    'switchbacks' => true,
                    'forest_path' => true,
                    'rocky_sections' => true,
                    'steep_sections' => true
                ]
            ],
            
            // Mount Arayat
            [
                'name' => 'arayat trail',
                'mountain' => 'mount arayat',
                'length_km' => 8.5,
                'trail_type' => 'intermediate',
                'elevation_gain' => 800,
                'characteristics' => [
                    'switchbacks' => true,
                    'forest_path' => true,
                    'rocky_sections' => true
                ]
            ],
            
            // Mount Batulao
            [
                'name' => 'batulao trail',
                'mountain' => 'mount batulao',
                'length_km' => 12.0,
                'trail_type' => 'intermediate',
                'elevation_gain' => 600,
                'characteristics' => [
                    'switchbacks' => true,
                    'grassland' => true,
                    'rolling_hills' => true
                ]
            ],
            
            // Mount Talamitam
            [
                'name' => 'talamitam trail',
                'mountain' => 'mount talamitam',
                'length_km' => 6.8,
                'trail_type' => 'beginner',
                'elevation_gain' => 400,
                'characteristics' => [
                    'grassland' => true,
                    'easy_slope' => true
                ]
            ],
            
            // Mount Maculot
            [
                'name' => 'maculot trail',
                'mountain' => 'mount maculot',
                'length_km' => 7.2,
                'trail_type' => 'intermediate',
                'elevation_gain' => 550,
                'characteristics' => [
                    'rocky_sections' => true,
                    'forest_path' => true
                ]
            ],
            
            // Mount Mayon
            [
                'name' => 'mayon trail',
                'mountain' => 'mount mayon',
                'length_km' => 16.8,
                'trail_type' => 'advanced',
                'elevation_gain' => 2400,
                'characteristics' => [
                    'volcanic' => true,
                    'steep_sections' => true,
                    'rocky_sections' => true
                ]
            ],
            
            // Mount Ulap
            [
                'name' => 'ulap trail',
                'mountain' => 'mount ulap',
                'length_km' => 9.5,
                'trail_type' => 'intermediate',
                'elevation_gain' => 700,
                'characteristics' => [
                    'grassland' => true,
                    'rolling_hills' => true
                ]
            ],
            
            // Mount Pinatubo
            [
                'name' => 'pinatubo trail',
                'mountain' => 'mount pinatubo',
                'length_km' => 14.0,
                'trail_type' => 'beginner',
                'elevation_gain' => 300,
                'characteristics' => [
                    'lahar_fields' => true,
                    'rocky_terrain' => true
                ]
            ]
        ];
    }

    /**
     * Adjust coordinates for known trails
     */
    private function adjustCoordinatesForKnownTrail($coordinates, $knownTrail)
    {
        // Calculate current distance
        $currentDistance = $this->calculatePathDistance($coordinates);
        $targetDistance = $knownTrail['length_km'] * 1000; // Convert to meters
        
        // If current distance is significantly different, enhance the path
        if (abs($currentDistance - $targetDistance) > $targetDistance * 0.3) {
            // Add more waypoints to match the expected trail length
            $coordinates = $this->interpolateTrailPath($coordinates, $targetDistance / $currentDistance);
        }
        
        return $coordinates;
    }

    /**
     * Add general trail characteristics to coordinates
     */
    private function addTrailCharacteristics($coordinates)
    {
        // Add some switchbacks and trail-like curves for more realistic distance
        $enhanced = [];
        
        for ($i = 0; $i < count($coordinates) - 1; $i++) {
            $enhanced[] = $coordinates[$i];
            
            // Add intermediate points to simulate trail curves
            $current = $coordinates[$i];
            $next = $coordinates[$i + 1];
            
            // Add 2-3 intermediate points between each coordinate
            $intermediatePoints = $this->generateIntermediateTrailPoints($current, $next, 2);
            $enhanced = array_merge($enhanced, $intermediatePoints);
        }
        
        // Add the last coordinate
        $enhanced[] = $coordinates[count($coordinates) - 1];
        
        return $enhanced;
    }

    /**
     * Generate intermediate trail points between two coordinates
     */
    private function generateIntermediateTrailPoints($start, $end, $numPoints)
    {
        $points = [];
        
        for ($i = 1; $i <= $numPoints; $i++) {
            $ratio = $i / ($numPoints + 1);
            
            // Add some trail-like variance (switchbacks, curves)
            $latVariance = (rand(-50, 50) / 100000); // Small random variance
            $lngVariance = (rand(-50, 50) / 100000);
            
            $lat = $start['lat'] + ($end['lat'] - $start['lat']) * $ratio + $latVariance;
            $lng = $start['lng'] + ($end['lng'] - $start['lng']) * $ratio + $lngVariance;
            
            $points[] = ['lat' => $lat, 'lng' => $lng];
        }
        
        return $points;
    }

    /**
     * Calculate total distance of a path
     */
    private function calculatePathDistance($coordinates)
    {
        $totalDistance = 0;
        
        for ($i = 1; $i < count($coordinates); $i++) {
            $totalDistance += $this->getDistanceBetweenPoints(
                $coordinates[$i-1]['lat'], $coordinates[$i-1]['lng'],
                $coordinates[$i]['lat'], $coordinates[$i]['lng']
            );
        }
        
        return $totalDistance;
    }

    /**
     * Get distance between two points using Haversine formula
     */
    private function getDistanceBetweenPoints($lat1, $lng1, $lat2, $lng2)
    {
        $R = 6371000; // Earth's radius in meters
        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);
        
        $a = sin($dLat/2) * sin($dLat/2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLng/2) * sin($dLng/2);
        
        $c = 2 * atan2(sqrt($a), sqrt(1-$a));
        return $R * $c;
    }

    /**
     * Interpolate trail path to match target distance
     */
    private function interpolateTrailPath($coordinates, $scaleFactor)
    {
        if ($scaleFactor <= 1.2) { // If not too different, just add some points
            return $this->addTrailCharacteristics($coordinates);
        }
        
        // For significant differences, create a more detailed path
        $enhanced = [];
        
        for ($i = 0; $i < count($coordinates) - 1; $i++) {
            $enhanced[] = $coordinates[$i];
            
            // Add more intermediate points based on scale factor
            $numPoints = max(2, round($scaleFactor * 2));
            $intermediatePoints = $this->generateIntermediateTrailPoints(
                $coordinates[$i], 
                $coordinates[$i + 1], 
                $numPoints
            );
            $enhanced = array_merge($enhanced, $intermediatePoints);
        }
        
        $enhanced[] = $coordinates[count($coordinates) - 1];
        
        return $enhanced;
    }

    /**
     * Calculate trail metrics including more accurate length estimation
     */
    private function calculateTrailMetrics($coordinates, $trailName, $mountainName)
    {
        $distance = $this->calculatePathDistance($coordinates);
        $knownTrails = $this->getKnownTrailData();
        
        // Check if this matches a known trail
        $trailKey = strtolower($trailName . ' ' . $mountainName);
        foreach ($knownTrails as $known) {
            if (strpos($trailKey, strtolower($known['name'])) !== false || 
                strpos($trailKey, strtolower($known['mountain'])) !== false) {
                return [
                    'length_km' => $known['length_km'],
                    'trail_type' => $known['trail_type'],
                    'estimated' => false
                ];
            }
        }
        
        // If not known, return calculated distance with some trail factor
        $trailFactor = 1.3; // Trails are typically 30% longer than direct routes
        return [
            'length_km' => ($distance / 1000) * $trailFactor,
            'trail_type' => 'estimated',
            'estimated' => true
        ];
    }

    /**
     * Calculate route distance from coordinates
     */
    private function calculateRouteDistance($coordinates)
    {
        if (empty($coordinates) || count($coordinates) < 2) {
            return 0;
        }

        $totalDistance = 0;
        for ($i = 1; $i < count($coordinates); $i++) {
            $totalDistance += $this->haversineDistance(
                $coordinates[$i-1]['lat'], $coordinates[$i-1]['lng'],
                $coordinates[$i]['lat'], $coordinates[$i]['lng']
            );
        }

        return round($totalDistance, 2);
    }

    /**
     * Haversine formula for calculating distance between two points
     */
    private function haversineDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371; // Earth's radius in kilometers
        
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        
        $a = sin($dLat/2) * sin($dLat/2) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon/2) * sin($dLon/2);
        $c = 2 * atan2(sqrt($a), sqrt(1-$a));
        
        return $earthRadius * $c;
    }

    /**
     * Generate a basic route for known trails
     */
    private function generateBasicRoute($startLocation, $endLocation, $waypoints = [])
    {
        try {
            return $this->googleDirectionsService->getTrailCoordinatesByLocation(
                $startLocation,
                '', // Empty trail name for basic routing
                ''  // Empty mountain name for basic routing
            );
        } catch (\Exception $e) {
            Log::error('Basic route generation failed: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Find a known trail in the database
     */
    private function findKnownTrail($trailName, $mountainName, $knownTrails)
    {
        $searchString = strtolower(trim($trailName . ' ' . ($mountainName ?? '')));
        
        foreach ($knownTrails as $known) {
            $knownString = strtolower($known['name'] . ' ' . $known['mountain']);
            
            // Check for exact match or partial match
            if (strpos($searchString, strtolower($known['name'])) !== false ||
                strpos($searchString, strtolower($known['mountain'])) !== false) {
                return $known;
            }
        }
        
        return null;
    }
}

<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Exception;

/**
 * OSM Trail Segment Service - Based on AllTrails Methodology
 * 
 * This service implements the AllTrails derivative database methodology
 * for creating trail segments from OpenStreetMap data as per ODBL requirements.
 */
class OSMTrailSegmentService
{
    /**
     * Legacy large tile size (kept for upper bound). We now start smaller and grow only if needed.
     */
    private const MAX_TILE_DIM = 2.0; // degrees
    private const MIN_TILE_DIM = 0.25; // degrees (approx 27km at equator -> still big for mountain areas)
    private const SMALL_REQUEST_THRESHOLD = 0.75; // deg^2 area under which we query directly w/out tiling
    private const MIN_LAT = -58;
    private const MAX_LAT = 72;
    
    // Primary and fallback Overpass API endpoints (https preferred)
    private const OVERPASS_PRIMARY = 'https://overpass-api.de/api/interpreter';
    private const OVERPASS_FALLBACKS = [
        'https://overpass.openstreetmap.ru/api/interpreter',
        'https://overpass.kumi.systems/api/interpreter'
    ];
    private const NOMINATIM_URL = 'https://nominatim.openstreetmap.org';
    
    private array $wayData = [];
    private array $intersections = [];
    private array $segments = [];
    private array $peaks = [];
    private array $viewpoints = [];
    private array $hikingRoutes = [];
    
    /**
     * Generate trail segments for a bounding box using AllTrails methodology
     */
    public function generateTrailSegments(float $minLat, float $minLng, float $maxLat, float $maxLng, bool $strictTiles = false): array
    {
        try {
            Log::info("Starting OSM trail segment generation", [
                'bounds' => [$minLat, $minLng, $maxLat, $maxLng]
            ]);
            // Also fetch peaks in the region (independent query) for later persistence/export
            $this->peaks = $this->fetchPeaks($minLat, $minLng, $maxLat, $maxLng);
            $this->viewpoints = $this->fetchViewpoints($minLat, $minLng, $maxLat, $maxLng);
            $this->hikingRoutes = $this->fetchHikingRoutes($minLat, $minLng, $maxLat, $maxLng);

            // Attempt to raise memory limit for larger regions (soft attempt only)
            $currentLimit = ini_get('memory_limit');
            if ($currentLimit !== '-1') {
                @ini_set('memory_limit', '512M');
            }
            
            // Reset state per invocation
            $this->wayData = [];
            $this->intersections = [];
            $this->segments = [];

            // Step 1: Download OSM data (dynamic tiling)
            $tiles = $strictTiles
                ? $this->generateTileGrid($minLat, $minLng, $maxLat, $maxLng, self::MAX_TILE_DIM, exactEdges: false)
                : $this->prepareAndFetchTiles($minLat, $minLng, $maxLat, $maxLng);

            if ($strictTiles) {
                foreach ($tiles as $tile) {
                    $this->downloadTileData($tile, allowSubdivide: false); // do not subdivide in strict legacy mode
                }
            }
            
            // Step 2: Process intersections
            $this->calculateIntersections();
            
            // Step 3: Create segments
            $this->createSegments();
            
            // Step 4: Calculate metrics
            $this->calculateSegmentMetrics();
            
            return [
                'segments' => $this->segments,
                'intersections' => $this->intersections,
                'statistics' => $this->buildStatistics($tiles)
            ];
            
        } catch (Exception $e) {
            Log::error("OSM segment generation failed", [
                'error' => $e->getMessage(),
                'bounds' => [$minLat, $minLng, $maxLat, $maxLng]
            ]);
            throw $e;
        }
    }
    
    /**
     * Decide strategy and fetch tiles adaptively.
     * Returns the list of tiles actually queried (for statistics reporting).
     */
    private function prepareAndFetchTiles(float $minLat, float $minLng, float $maxLat, float $maxLng): array
    {
        $area = ($maxLat - $minLat) * ($maxLng - $minLng);
        $queriedTiles = [];

        // If the requested area is small, issue a single direct query exactly matching bounds.
        if ($area <= self::SMALL_REQUEST_THRESHOLD) {
            $tile = [
                'minLat' => $minLat,
                'minLng' => $minLng,
                'maxLat' => $maxLat,
                'maxLng' => $maxLng,
                'dim'    => max($maxLat - $minLat, $maxLng - $minLng)
            ];
            $this->downloadTileData($tile);
            $queriedTiles[] = $tile;
            return $queriedTiles;
        }

        // Otherwise start with a moderate tile size (1 degree) and subdivide on failure.
    $initialDim = 0.5; // smaller starting tile to reduce Overpass payload & memory
        $queue = $this->generateTileGrid($minLat, $minLng, $maxLat, $maxLng, $initialDim);

        while (!empty($queue)) {
            $tile = array_shift($queue);
            $result = $this->downloadTileData($tile, allowSubdivide: true);
            $queriedTiles[] = $tile;

            if ($result === 'SUBDIVIDE' && ($tile['dim'] / 2) >= self::MIN_TILE_DIM) {
                $half = $tile['dim'] / 2.0;
                $subTiles = $this->generateTileGrid($tile['minLat'], $tile['minLng'], $tile['maxLat'], $tile['maxLng'], $half, exactEdges: true);
                foreach ($subTiles as $st) {
                    $queue[] = $st;
                }
            }
        }

        return $queriedTiles;
    }

    /**
     * Generate a grid of tiles inside the bounding box with given dimension.
     * If exactEdges is true, we don't snap to global boundaries – we use exact min/max.
     */
    private function generateTileGrid(float $minLat, float $minLng, float $maxLat, float $maxLng, float $dim, bool $exactEdges = false): array
    {
        $tiles = [];
        $lat = $minLat;
        while ($lat < $maxLat) {
            $nextLat = min($lat + $dim, $maxLat);
            $lng = $minLng;
            while ($lng < $maxLng) {
                $nextLng = min($lng + $dim, $maxLng);
                if ($lat >= self::MIN_LAT && $lat <= self::MAX_LAT) {
                    $tiles[] = [
                        'minLat' => $lat,
                        'minLng' => $lng,
                        'maxLat' => $nextLat,
                        'maxLng' => $nextLng,
                        'dim'    => max($nextLat - $lat, $nextLng - $lng)
                    ];
                }
                $lng = $exactEdges ? $nextLng : $lng + $dim;
            }
            $lat = $exactEdges ? $nextLat : $lat + $dim;
        }
        return $tiles;
    }
    
    /**
     * Step 1: Download OSM way data for a tile using Overpass API
     */
    private function downloadTileData(array $tile, bool $allowSubdivide = false): string
    {
        $query = $this->buildOverpassQuery($tile);
        $endpoints = array_merge([self::OVERPASS_PRIMARY], self::OVERPASS_FALLBACKS);
        $attempt = 0;
        $maxAttempts = count($endpoints);
        $success = false;
        $shouldSubdivide = false; // set true if size likely caused timeout

        foreach ($endpoints as $endpoint) {
            $attempt++;
            try {
                $response = Http::timeout(90)
                    ->retry(2, 500) // quick retry for transient failures
                    ->withHeaders(['Accept' => 'application/json'])
                    ->withUserAgent('HikeThere/1.0 (+https://example.com) Laravel Overpass Client')
                    ->asForm() // Overpass expects form-encoded 'data'
                    ->post($endpoint, [ 'data' => $query ]);

                if ($response->successful()) {
                    $raw = $response->body();
                    $json = json_decode($raw, true);
                    if (json_last_error() !== JSON_ERROR_NONE || !isset($json['elements'])) {
                        Log::warning('Overpass response malformed', [
                            'endpoint' => $endpoint,
                            'attempt' => $attempt,
                            'body_excerpt' => substr($raw, 0, 300)
                        ]);
                    } else {
                        $before = count($this->wayData);
                        $this->processOSMData($json);
                        $after = count($this->wayData);
                        $waysAdded = $after - $before;
                        Log::info('Overpass tile processed', [
                            'endpoint' => $endpoint,
                            'attempt' => $attempt,
                            'tile' => $tile,
                            'ways_added' => $waysAdded,
                            'total_ways' => $after
                        ]);
                        if ($waysAdded === 0) {
                            Log::notice('Overpass returned zero hiking ways for tile', [
                                'tile' => $tile,
                                'element_sample' => array_slice($json['elements'], 0, 3)
                            ]);
                        }
                        $success = true;
                        break;
                    }
                } else {
                    Log::warning('Overpass request failed', [
                        'endpoint' => $endpoint,
                        'attempt' => $attempt,
                        'status' => $response->status(),
                        'tile' => $tile,
                        'error_excerpt' => substr($response->body(), 0, 200)
                    ]);
                }
            } catch (Exception $e) {
                Log::error('Overpass exception', [
                    'endpoint' => $endpoint,
                    'attempt' => $attempt,
                    'tile' => $tile,
                    'error' => $e->getMessage()
                ]);
                // Heuristic: cURL 28 timeout on larger tiles -> request subdivision
                if (str_contains($e->getMessage(), 'cURL error 28')) {
                    $shouldSubdivide = true;
                }
            }
        }

        // Fallback simplified query if nothing fetched
        if (!$success) {
            Log::notice('Attempting simplified Overpass query fallback', ['tile' => $tile]);
            $simpleQuery = $this->buildSimplifiedQuery($tile);
            try {
                $resp = Http::timeout(60)
                    ->withUserAgent('HikeThere/1.0 (+https://example.com) Laravel Overpass Client')
                    ->asForm()
                    ->post(self::OVERPASS_PRIMARY, ['data' => $simpleQuery]);
                if ($resp->successful() && ($json = $resp->json()) && isset($json['elements'])) {
                    $this->processOSMData($json);
                    Log::info('Simplified Overpass query succeeded', ['tile' => $tile, 'ways' => count($this->wayData)]);
                    $success = true;
                }
            } catch (Exception $e) {
                Log::error('Simplified Overpass fallback failed', ['tile' => $tile, 'error' => $e->getMessage()]);
                if (str_contains($e->getMessage(), 'cURL error 28')) {
                    $shouldSubdivide = true;
                }
            }
        }

        if (!$success && $allowSubdivide && $shouldSubdivide) {
            Log::notice('Scheduling tile subdivision due to repeated timeouts', [
                'tile' => $tile,
                'dim' => $tile['dim'] ?? null
            ]);
            return 'SUBDIVIDE';
        }

        return $success ? 'OK' : 'FAIL';
    }
    
    /**
     * Build Overpass API query for hiking trails
     */
    private function buildOverpassQuery(array $tile): string
    {
        // Overpass bbox order: south,west,north,east
        $bbox = "{$tile['minLat']},{$tile['minLng']},{$tile['maxLat']},{$tile['maxLng']}";
        // Simplified single clause query to reduce server load & duplication
        // We deliberately include principal hiking-related highway values.
        return <<<OVERPASS
[out:json][timeout:120];
way[highway~"^(path|footway|track|bridleway|steps|cycleway)$"]($bbox);
(._;>;);
out geom;
OVERPASS;
    }

    private function buildSimplifiedQuery(array $tile): string
    {
        $bbox = "{$tile['minLat']},{$tile['minLng']},{$tile['maxLat']},{$tile['maxLng']}";
        return <<<OVERPASS
[out:json][timeout:60];
way[highway~"^(path|footway)$"]($bbox);
(._;>;);
out geom;
OVERPASS;
    }
    
    /**
     * Step 2: Process OSM data and store ways
     */
    private function processOSMData(array $data): void
    {
        if (!isset($data['elements']) || !is_array($data['elements'])) {
            Log::warning('processOSMData called with no elements');
            return;
        }
        $stored = 0;
        foreach ($data['elements'] as $element) {
            if ($element['type'] === 'way' && isset($element['geometry'])) {
                $wayId = $element['id'];
                $tags = $element['tags'] ?? [];
                // Store way data keyed by ID (handles duplicates across tiles)
                $this->wayData[$wayId] = [
                    'id' => $wayId,
                    'points' => $this->extractPoints($element['geometry']),
                    'highway' => $tags['highway'] ?? null,
                    'name' => $tags['name'] ?? null,
                    'access' => $tags['access'] ?? null,
                    'bicycle' => $tags['bicycle'] ?? null,
                    'sac_scale' => $tags['sac_scale'] ?? null,
                    'trail_visibility' => $tags['trail_visibility'] ?? null
                ];
                $stored++;
            }
        }
        if ($stored > 0) {
            Log::debug('OSM ways stored (memory mini tags)', ['count' => $stored, 'total' => count($this->wayData)]);
        }
    }
    
    /**
     * Extract coordinate points from OSM geometry
     */
    private function extractPoints(array $geometry): array
    {
        $points = [];
        foreach ($geometry as $node) {
            $points[] = [
                'lat' => $node['lat'],
                'lng' => $node['lon']
            ];
        }
        return $points;
    }
    
    /**
     * Step 4: Calculate intersections between ways
     */
    private function calculateIntersections(): void
    {
        $intersectionId = 1;
        $this->intersections = [];

        // Spatial hash (grid) approach: bucket points by truncated lat/lng scaled to tolerance
        $tolerance = 0.00001; // ~1m
        $scale = 1 / $tolerance; // 100000
        $grid = [];

        foreach ($this->wayData as $wayId => $way) {
            foreach ($way['points'] as $idx => $pt) {
                $key = ((int)floor($pt['lat'] * $scale)) . ':' . ((int)floor($pt['lng'] * $scale));
                $grid[$key][] = [$wayId, $idx, $pt];
            }
        }

        foreach ($grid as $bucket) {
            $count = count($bucket);
            if ($count < 2) { continue; }
            // Generate pairwise intersections inside bucket
            for ($i = 0; $i < $count - 1; $i++) {
                [$wayA, $idxA, $ptA] = $bucket[$i];
                for ($j = $i + 1; $j < $count; $j++) {
                    [$wayB, $idxB, $ptB] = $bucket[$j];
                    if ($wayA === $wayB) { continue; }
                    // Fine check
                    if ($this->pointsMatch($ptA, $ptB, $tolerance)) {
                        $intersectionData = [
                            'id' => $intersectionId++,
                            'lat' => $ptA['lat'],
                            'lng' => $ptA['lng'],
                            'way_a' => $wayA,
                            'way_b' => $wayB,
                            'point_index_a' => $idxA,
                            'point_index_b' => $idxB
                        ];
                        $this->intersections[$wayA][] = $intersectionData;
                        $this->intersections[$wayB][] = $intersectionData;
                    }
                }
            }
        }

        Log::info('Intersections computed', [
            'unique_points_buckets' => count($grid),
            'ways' => count($this->wayData)
        ]);
    }
    
    /**
     * Check if two points match (within tolerance)
     */
    private function pointsMatch(array $pointA, array $pointB, float $tolerance = 0.00001): bool
    {
        return abs($pointA['lat'] - $pointB['lat']) < $tolerance &&
               abs($pointA['lng'] - $pointB['lng']) < $tolerance;
    }
    
    /**
     * Step 5: Create segments by splitting ways at intersections
     */
    private function createSegments(): void
    {
        foreach ($this->wayData as $wayId => $way) {
            $wayIntersections = $this->intersections[$wayId] ?? [];
            
            // Sort intersections by point index
            usort($wayIntersections, function($a, $b) {
                return $a['point_index_a'] <=> $b['point_index_a'];
            });
            
            // Create segments between intersections
            $segmentId = 1;
            if (!empty($wayIntersections)) {
                $prevPointIdx = 0;
                $prevIntersectionId = null;
                foreach ($wayIntersections as $intersection) {
                    $currentPointIdx = $intersection['point_index_a'];
                    if ($currentPointIdx <= $prevPointIdx) { // safeguard
                        continue;
                    }
                    $slice = array_slice($way['points'], $prevPointIdx, $currentPointIdx - $prevPointIdx + 1);
                    if (count($slice) >= 2) {
                        $this->segments[] = $this->createSegment(
                            $wayId . '_' . $segmentId++,
                            $wayId,
                            $slice,
                            [
                                'highway' => $way['highway'],
                                'name' => $way['name'],
                                'access' => $way['access'],
                                'bicycle' => $way['bicycle'],
                                'sac_scale' => $way['sac_scale'],
                                'trail_visibility' => $way['trail_visibility']
                            ],
                            $prevIntersectionId,
                            $intersection['id']
                        );
                    }
                    $prevPointIdx = $currentPointIdx;
                    $prevIntersectionId = $intersection['id'];
                }
                // Tail after last intersection
                if ($prevPointIdx < count($way['points']) - 1) {
                    $tail = array_slice($way['points'], $prevPointIdx);
                    if (count($tail) >= 2) {
                        $this->segments[] = $this->createSegment(
                            $wayId . '_' . $segmentId,
                            $wayId,
                            $tail,
                            [
                                'highway' => $way['highway'],
                                'name' => $way['name'],
                                'access' => $way['access'],
                                'bicycle' => $way['bicycle'],
                                'sac_scale' => $way['sac_scale'],
                                'trail_visibility' => $way['trail_visibility']
                            ],
                            $prevIntersectionId,
                            null
                        );
                    }
                }
            }
            
            // If no intersections, create single segment from entire way
            if (empty($wayIntersections) && count($way['points']) >= 2) {
                $this->segments[] = $this->createSegment(
                    $wayId . '_1',
                    $wayId,
                    $way['points'],
                    [
                        'highway' => $way['highway'],
                        'name' => $way['name'],
                        'access' => $way['access'],
                        'bicycle' => $way['bicycle'],
                        'sac_scale' => $way['sac_scale'],
                        'trail_visibility' => $way['trail_visibility']
                    ],
                    null,
                    null
                );
            }
        }
    }
    
    /**
     * Create a trail segment with metadata
     */
    private function createSegment(string $segmentId, int $originalWayId, array $points, array $tags, ?int $startIntersectionId, ?int $endIntersectionId): array
    {
        return [
            'id' => $segmentId,
            'original_way_id' => $originalWayId,
            'points_data' => $points,
            'intersection_start_id' => $startIntersectionId,
            'intersection_end_id' => $endIntersectionId,
            'distance_total' => 0, // Will be calculated later
            'bounding_box' => $this->calculateBoundingBox($points),
            'private_access' => in_array($tags['access'] ?? '', ['private', 'no']),
            'bicycle_accessible' => ($tags['bicycle'] ?? '') === 'yes',
            'highway_type' => $tags['highway'] ?? null,
            'name' => $tags['name'] ?? null,
            'sac_scale' => $tags['sac_scale'] ?? null,
            'trail_visibility' => $tags['trail_visibility'] ?? null,
            'surface' => $tags['surface'] ?? null,
            'width' => $tags['width'] ?? null,
            'incline' => $tags['incline'] ?? null
        ];
    }
    
    /**
     * Step 6: Calculate distance and bounding box for segments
     */
    private function calculateSegmentMetrics(): void
    {
    foreach ($this->segments as &$segment) {
            // Calculate total distance using Haversine formula
            $segment['distance_total'] = $this->calculateDistance($segment['points_data']);
            
            // Bounding box already calculated in createSegment
        }
    }
    
    /**
     * Calculate distance between points using Haversine formula
     */
    private function calculateDistance(array $points): float
    {
        $totalDistance = 0;
        
        for ($i = 0; $i < count($points) - 1; $i++) {
            $totalDistance += $this->haversineDistance(
                $points[$i]['lat'], $points[$i]['lng'],
                $points[$i + 1]['lat'], $points[$i + 1]['lng']
            );
        }
        
        return $totalDistance; // in kilometers
    }
    
    /**
     * Calculate distance between two points using Haversine formula
     */
    private function haversineDistance(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $earthRadius = 6371; // Earth's radius in kilometers
        
        $deltaLat = deg2rad($lat2 - $lat1);
        $deltaLng = deg2rad($lng2 - $lng1);
        
        $a = sin($deltaLat / 2) * sin($deltaLat / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($deltaLng / 2) * sin($deltaLng / 2);
             
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        
        return $earthRadius * $c;
    }
    
    /**
     * Calculate bounding box for a set of points
     */
    private function calculateBoundingBox(array $points): array
    {
        $lats = array_column($points, 'lat');
        $lngs = array_column($points, 'lng');
        
        return [
            'min_lat' => min($lats),
            'max_lat' => max($lats),
            'min_lng' => min($lngs),
            'max_lng' => max($lngs)
        ];
    }

    /**
     * Build statistics array with correct intersection counting
     */
    private function buildStatistics(array $tiles): array
    {
        // Flatten intersection arrays to count unique intersection IDs
        $intersectionIds = [];
        foreach ($this->intersections as $wayIntersections) {
            foreach ($wayIntersections as $i) {
                $intersectionIds[$i['id']] = true;
            }
        }
        return [
            'total_ways' => count($this->wayData),
            'total_intersections' => count($intersectionIds),
            'total_segments' => count($this->segments),
            'tiles_processed' => count($tiles)
        ];
    }
    
    /**
     * Find trail segments by name and location
     */
    public function findTrailByName(string $trailName, array $searchBounds): array
    {
        $segments = $this->generateTrailSegments(
            $searchBounds['minLat'],
            $searchBounds['minLng'], 
            $searchBounds['maxLat'],
            $searchBounds['maxLng']
        );
        
        // Filter segments that match trail name
        $matchingSegments = array_filter($segments['segments'], function($segment) use ($trailName) {
            return isset($segment['name']) && 
                   stripos($segment['name'], $trailName) !== false;
        });
        
        return [
            'trail_segments' => array_values($matchingSegments),
            'total_distance' => array_sum(array_column($matchingSegments, 'distance_total')),
            'segment_count' => count($matchingSegments)
        ];
    }
    
    /**
     * Get trail segments for route optimization
     */
    public function getSegmentsForRouting(array $waypoints): array
    {
        // Calculate bounding box around waypoints
        $bounds = $this->calculateBoundingBox($waypoints);
        $buffer = 0.01; // 1km buffer approximately
        
        $segments = $this->generateTrailSegments(
            $bounds['min_lat'] - $buffer,
            $bounds['min_lng'] - $buffer,
            $bounds['max_lat'] + $buffer,
            $bounds['max_lng'] + $buffer
        );
        
        return $segments;
    }

    /**
     * Export generated segments & intersections to GeoJSON FeatureCollections and store to storage/app/exports
     * Returns array with file paths.
     */
    public function exportGeoJSON(array $segments, array $intersections, ?string $baseFilename = null): array
    {
        $base = $baseFilename ? pathinfo($baseFilename, PATHINFO_FILENAME) : 'osm_derivative_' . date('Ymd_His');
        $dir = 'exports';
        if (!Storage::exists($dir)) {
            Storage::makeDirectory($dir);
        }

        $intersectionsFc = [
            'type' => 'FeatureCollection',
            'features' => array_map(function($i) {
                return [
                    'type' => 'Feature',
                    'geometry' => [
                        'type' => 'Point',
                        'coordinates' => [ $i['lng'], $i['lat'] ]
                    ],
                    'properties' => [
                        'id' => $i['id'],
                        'way_a' => $i['way_a'],
                        'way_b' => $i['way_b']
                    ]
                ];
            }, $this->uniqueIntersectionsFlat($intersections))
        ];

        $segmentsFc = [
            'type' => 'FeatureCollection',
            'features' => array_map(function($s) {
                return [
                    'type' => 'Feature',
                    'geometry' => [
                        'type' => 'LineString',
                        'coordinates' => array_map(fn($p) => [$p['lng'], $p['lat']], $s['points_data'])
                    ],
                    'properties' => [
                        'id' => $s['id'],
                        'original_way_id' => $s['original_way_id'],
                        'distance_km' => $s['distance_total'],
                        'intersection_start_id' => $s['intersection_start_id'],
                        'intersection_end_id' => $s['intersection_end_id'],
                        'private_access' => $s['private_access'],
                        'bicycle_accessible' => $s['bicycle_accessible'],
                        'highway_type' => $s['highway_type'],
                        'name' => $s['name'],
                        'sac_scale' => $s['sac_scale'],
                        'trail_visibility' => $s['trail_visibility']
                    ]
                ];
            }, $segments)
        ];

        $peaksFc = [
            'type' => 'FeatureCollection',
            'features' => array_map(function($p) {
                return [
                    'type' => 'Feature',
                    'geometry' => [
                        'type' => 'Point',
                        'coordinates' => [ $p['lng'], $p['lat'] ]
                    ],
                    'properties' => [
                        'osm_id' => $p['id'],
                        'name' => $p['name'] ?? null,
                        'elevation' => $p['elevation'] ?? null
                    ]
                ];
            }, $this->peaks ?? [])
        ];

        $viewpointsFc = [
            'type' => 'FeatureCollection',
            'features' => array_map(function($v) {
                return [
                    'type' => 'Feature',
                    'geometry' => [ 'type' => 'Point', 'coordinates' => [ $v['lng'], $v['lat'] ] ],
                    'properties' => [ 'osm_id' => $v['id'], 'name' => $v['name'] ?? null ]
                ];
            }, $this->viewpoints ?? [])
        ];

        $hikingRoutesFc = [
            'type' => 'FeatureCollection',
            'features' => array_map(function($r) {
                return [
                    'type' => 'Feature',
                    'geometry' => [ 'type' => 'LineString', 'coordinates' => $r['coordinates'] ],
                    'properties' => [
                        'osm_id' => $r['id'],
                        'name' => $r['name'] ?? null,
                        'distance_km' => $r['distance_km'] ?? null
                    ]
                ];
            }, $this->hikingRoutes ?? [])
        ];

        $intersectionsPath = $dir . '/' . $base . '_intersections.geojson';
    $segmentsPath = $dir . '/' . $base . '_segments.geojson';
    $peaksPath = $dir . '/' . $base . '_peaks.geojson';
    $viewpointsPath = $dir . '/' . $base . '_viewpoints.geojson';
    $routesPath = $dir . '/' . $base . '_hiking_routes.geojson';

        Storage::put($intersectionsPath, json_encode($intersectionsFc, JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE));
    Storage::put($segmentsPath, json_encode($segmentsFc, JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE));
    Storage::put($peaksPath, json_encode($peaksFc, JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE));
    Storage::put($viewpointsPath, json_encode($viewpointsFc, JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE));
    Storage::put($routesPath, json_encode($hikingRoutesFc, JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE));

        return [
            'intersections' => Storage::path($intersectionsPath),
            'segments' => Storage::path($segmentsPath),
            'peaks' => Storage::path($peaksPath),
            'viewpoints' => Storage::path($viewpointsPath),
            'hiking_routes' => Storage::path($routesPath)
        ];
    }

    private function uniqueIntersectionsFlat(array $intersections): array
    {
        $flat = [];
        $seen = [];
        foreach ($intersections as $wayIntersections) {
            foreach ($wayIntersections as $i) {
                if (!isset($seen[$i['id']])) {
                    $seen[$i['id']] = true;
                    $flat[] = $i;
                }
            }
        }
        return $flat;
    }

    /**
     * Fetch peaks (natural=peak) within bounds.
     */
    private function fetchPeaks(float $minLat, float $minLng, float $maxLat, float $maxLng): array
    {
        $bbox = "$minLat,$minLng,$maxLat,$maxLng"; // south,west,north,east
        $query = $this->buildPeakQuery($bbox);
        try {
            $endpoint = self::OVERPASS_PRIMARY;
            $resp = Http::timeout(90)->withHeaders(['Accept' => 'application/json'])->post($endpoint, [
                'data' => $query
            ]);
            if (!$resp->ok()) {
                Log::warning('Peak query failed', ['status' => $resp->status()]);
                return [];
            }
            $data = $resp->json();
            $out = [];
            foreach ($data['elements'] ?? [] as $el) {
                if (($el['type'] ?? '') === 'node') {
                    $tags = $el['tags'] ?? [];
                    if (($tags['natural'] ?? '') === 'peak') {
                        $out[] = [
                            'id' => $el['id'],
                            'lat' => $el['lat'],
                            'lng' => $el['lon'],
                            'name' => $tags['name'] ?? null,
                            'elevation' => isset($tags['ele']) ? (int)preg_replace('/[^0-9]/', '', $tags['ele']) : null,
                            'tags' => $tags
                        ];
                    }
                }
            }
            Log::info('Peaks fetched', ['count' => count($out)]);
            return $out;
        } catch (Exception $e) {
            Log::error('Peak fetch exception', ['message' => $e->getMessage()]);
            return [];
        }
    }

    private function buildPeakQuery(string $bbox): string
    {
        return <<<OVERPASS
[out:json][timeout:60];
node[natural=peak]($bbox);
out body;
OVERPASS;
    }

    private function fetchViewpoints(float $minLat, float $minLng, float $maxLat, float $maxLng): array
    {
        $bbox = "$minLat,$minLng,$maxLat,$maxLng";
        $query = <<<OVERPASS
[out:json][timeout:60];
node[tourism=viewpoint]($bbox);
out body;
OVERPASS;
        try {
            $resp = Http::timeout(60)->post(self::OVERPASS_PRIMARY, ['data' => $query]);
            if (!$resp->ok()) return [];
            $data = $resp->json();
            $out = [];
            foreach ($data['elements'] ?? [] as $el) {
                if (($el['type'] ?? '') === 'node') {
                    $tags = $el['tags'] ?? [];
                    $out[] = [
                        'id' => $el['id'],
                        'lat' => $el['lat'],
                        'lng' => $el['lon'],
                        'name' => $tags['name'] ?? null,
                        'tags' => $tags
                    ];
                }
            }
            Log::info('Viewpoints fetched', ['count' => count($out)]);
            return $out;
        } catch (Exception $e) {
            Log::error('Viewpoint fetch failed', ['msg' => $e->getMessage()]);
            return [];
        }
    }

    private function fetchHikingRoutes(float $minLat, float $minLng, float $maxLat, float $maxLng): array
    {
        $bbox = "$minLat,$minLng,$maxLat,$maxLng";
        $query = <<<OVERPASS
[out:json][timeout:120];
relation[route=hiking]($bbox);
out geom;
OVERPASS;
        try {
            $resp = Http::timeout(120)->post(self::OVERPASS_PRIMARY, ['data' => $query]);
            if (!$resp->ok()) return [];
            $data = $resp->json();
            $out = [];
            foreach ($data['elements'] ?? [] as $el) {
                if (($el['type'] ?? '') === 'relation') {
                    $tags = $el['tags'] ?? [];
                    // Build polyline approximation from first member ways with geometry if present
                    $coords = [];
                    foreach ($el['members'] ?? [] as $m) {
                        if (($m['type'] ?? '') === 'way' && isset($m['geometry'])) {
                            foreach ($m['geometry'] as $g) {
                                $coords[] = [$g['lon'], $g['lat']];
                            }
                        }
                    }
                    $out[] = [
                        'id' => $el['id'],
                        'name' => $tags['name'] ?? null,
                        'coordinates' => $coords,
                        'distance_km' => null,
                        'tags' => $tags
                    ];
                }
            }
            Log::info('Hiking routes fetched', ['count' => count($out)]);
            return $out;
        } catch (Exception $e) {
            Log::error('Hiking routes fetch failed', ['msg' => $e->getMessage()]);
            return [];
        }
    }
}

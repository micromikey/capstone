<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OpenStreetMapService
{
    private const OVERPASS_API_URL = 'https://overpass-api.de/api/interpreter';
    private const NOMINATIM_API_URL = 'https://nominatim.openstreetmap.org/search';
    
    // Rate limiting: Wait between requests to be respectful
    private const REQUEST_DELAY_SECONDS = 1;
    
    /**
     * Get trail coordinates from OpenStreetMap using Overpass API
     * No API key required - OpenStreetMap is free!
     */
    public function getTrailCoordinates($location, $trailName, $mountainName = null)
    {
        try {
            // Check if OSM is enabled
            if (!config('app.osm_enabled', true)) {
                Log::info("OpenStreetMap is disabled in configuration");
                return null;
            }

            // First, get the bounding box for the location
            $boundingBox = $this->getBoundingBox($location, $mountainName);
            
            if (!$boundingBox) {
                Log::warning("Could not find bounding box for location: {$location}");
                return null;
            }

            // Rate limiting: Be respectful to free API
            $delay = config('app.osm_request_delay', self::REQUEST_DELAY_SECONDS);
            sleep($delay);

            // Search for hiking trails in the area (primary attempt)
            $trailData = $this->searchTrailsInArea($boundingBox, $trailName, $mountainName);
            $processed = $trailData ? $this->processTrailData($trailData) : null;

            // Retry with expanded bounding box if insufficient or empty
            if ((!$processed || ($processed && isset($processed['coordinates']) && count($processed['coordinates']) < 10)) && $boundingBox) {
                $expanded = $this->expandBoundingBox($boundingBox, 0.05); // ~5.5 km expansion each side
                $trailData2 = $this->searchTrailsInArea($expanded, $trailName, $mountainName);
                $processed2 = $trailData2 ? $this->processTrailData($trailData2) : null;
                if ($processed2 && isset($processed2['coordinates']) && count($processed2['coordinates']) > ($processed['coordinates'] ?? 0)) {
                    $processed = $processed2;
                }
            }

            if (!$processed) {
                Log::warning("No OSM trail geometry resolved for: {$trailName} @ {$location}");
                return null;
            }

            return $processed;
            
        } catch (\Exception $e) {
            Log::error("OpenStreetMap API error: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Expand a bounding box by delta degrees lat/lng.
     */
    private function expandBoundingBox(array $bbox, float $delta): array
    {
        return [
            'south' => max(-90, $bbox['south'] - $delta),
            'north' => min(90, $bbox['north'] + $delta),
            'west'  => max(-180, $bbox['west'] - $delta),
            'east'  => min(180, $bbox['east'] + $delta),
        ];
    }

    /**
     * Get bounding box coordinates for a location using Nominatim
     */
    private function getBoundingBox($location, $mountainName = null)
    {
        try {
            $searchQuery = $mountainName ? "{$mountainName}, {$location}" : $location;
            
            $response = Http::timeout(30)
                ->withHeaders([
                    'User-Agent' => config('app.osm_user_agent', 'HikeThere/1.0 (Trail Mapping Application)')
                ])
                ->get(self::NOMINATIM_API_URL, [
                    'q' => $searchQuery,
                    'format' => 'json',
                    'limit' => 1,
                    'countrycodes' => 'ph', // Focus on Philippines
                    'extratags' => 1,
                    'addressdetails' => 1
                ]);

            if ($response->successful()) {
                $data = $response->json();
                
                if (!empty($data) && isset($data[0]['boundingbox'])) {
                    $bbox = $data[0]['boundingbox'];
                    return [
                        'south' => (float) $bbox[0],
                        'north' => (float) $bbox[1], 
                        'west' => (float) $bbox[2],
                        'east' => (float) $bbox[3]
                    ];
                }
            }

            return null;
            
        } catch (\Exception $e) {
            Log::error("Nominatim API error: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Search for trails in the specified area using Overpass API
     */
    private function searchTrailsInArea($boundingBox, $trailName, $mountainName = null)
    {
        try {
            // Create Overpass QL query for hiking trails
            $overpassQuery = $this->buildOverpassQuery($boundingBox, $trailName, $mountainName);
            
            $response = Http::timeout(60)
                ->withHeaders([
                    'Content-Type' => 'application/x-www-form-urlencoded',
                    'User-Agent' => config('app.osm_user_agent', 'HikeThere/1.0 (Trail Mapping Application)')
                ])
                ->post(self::OVERPASS_API_URL, [
                    'data' => $overpassQuery
                ]);

            if ($response->successful()) {
                $data = $response->json();
                return $data['elements'] ?? [];
            }

            Log::warning("Overpass API request failed: " . $response->status());
            return null;
            
        } catch (\Exception $e) {
            Log::error("Overpass API error: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Build Overpass QL query for hiking trails
     */
    private function buildOverpassQuery($boundingBox, $trailName, $mountainName = null)
    {
        $bbox = "{$boundingBox['south']},{$boundingBox['west']},{$boundingBox['north']},{$boundingBox['east']}";
        // Build a more comprehensive query to find hiking trails, paths, tracks,
        // trail relations, and relevant POIs (peaks, viewpoints, mountain places).
        $query = "[out:json][timeout:60];";
        $query .= "(";

        // Primary highway/footway candidates (ways)
        $query .= "way[\"highway\"~\"^(path|track|footway|pedestrian|cycleway|bridleway)$\"]({$bbox});";
        $query .= "way[\"highway\"=\"steps\"]({$bbox});";
        $query .= "way[\"sac_scale\"]({$bbox});";
        $query .= "way[\"trail_visibility\"]({$bbox});";
        $query .= "way[\"foot\"=\"yes\"]({$bbox});";
        $query .= "way[\"hiking\"=\"yes\"]({$bbox});";

        // Include ways with names or refs matching the trail name
        if ($trailName) {
            $escapedTrailName = addslashes($trailName);
            $query .= "way[\"name\"~\"{$escapedTrailName}\",i]({$bbox});";
            $query .= "way[\"ref\"~\"{$escapedTrailName}\",i]({$bbox});";
            // Also check for relations named similarly
            $query .= "relation[\"route\"~\"^(hiking|foot)$\"][\"name\"~\"{$escapedTrailName}\",i]({$bbox});";
        }

        // If mountain name provided, include ways/relations and nodes for mountain/peak matching
        if ($mountainName) {
            $escapedMountainName = addslashes($mountainName);
            $query .= "way[\"name\"~\"{$escapedMountainName}\",i]({$bbox});";
            $query .= "relation[\"route\"=\"hiking\"][\"name\"~\"{$escapedMountainName}\",i]({$bbox});";
            // Also look for named peaks / mountain places (nodes)
            $query .= "node[\"name\"~\"{$escapedMountainName}\",i][\"natural\"=\"peak\"]({$bbox});";
            $query .= "node[\"name\"~\"{$escapedMountainName}\",i][\"place\"~\"^(mountain|peak|locality)$\"]({$bbox});";
        }

        // POIs that can help identify trailheads or summit points
        $query .= "node[\"natural\"~\"^(peak|ridge)$\"]({$bbox});";
        $query .= "node[\"tourism\"~\"^(viewpoint|attraction)$\"]({$bbox});";
        $query .= "node[\"place\"~\"^(mountain|peak)$\"]({$bbox});";

        // Fallback: relations that represent hiking routes inside bbox
        $query .= "relation[\"route\"=\"hiking\"]({$bbox});";

        $query .= ");";
        // Fetch member ways/nodes and their geometry, then output geometry
        $query .= "(._;>;);out geom;";

        return $query;
    }

    /**
     * Process trail data from OpenStreetMap
     */
    private function processTrailData($elements)
    {
        $coordinates = [];
        $totalDistance = 0;
        $elevationPoints = [];
        
        foreach ($elements as $element) {
            if ($element['type'] === 'way' && isset($element['geometry'])) {
                $wayCoordinates = $this->extractWayCoordinates($element['geometry']);
                
                if (!empty($wayCoordinates)) {
                    // Calculate trail metrics
                    $wayDistance = $this->calculateDistance($wayCoordinates);
                    $totalDistance += $wayDistance;
                    
                    // Merge coordinates
                    $coordinates = array_merge($coordinates, $wayCoordinates);
                    
                    // Extract elevation if available
                    $this->extractElevationData($element, $elevationPoints);
                }
            }
        }
        
        if (empty($coordinates)) {
            return null;
        }
        
        // Remove duplicate coordinates and optimize path
        $coordinates = $this->optimizeCoordinates($coordinates);
        
        // Get elevation data for the trail
        $elevationProfile = $this->getElevationProfile($coordinates);
        
        return [
            'coordinates' => $coordinates,
            'distance_km' => round($totalDistance, 2),
            'elevation_profile' => $elevationProfile,
            'max_elevation' => $elevationProfile ? max(array_column($elevationProfile, 'elevation')) : null,
            'min_elevation' => $elevationProfile ? min(array_column($elevationProfile, 'elevation')) : null,
            'source' => 'openstreetmap'
        ];
    }

    /**
     * Extract coordinates from OSM way geometry
     */
    private function extractWayCoordinates($geometry)
    {
        $coordinates = [];
        
        foreach ($geometry as $point) {
            if (isset($point['lat']) && isset($point['lon'])) {
                $coordinates[] = [
                    'lat' => (float) $point['lat'],
                    'lng' => (float) $point['lon']
                ];
            }
        }
        
        return $coordinates;
    }

    /**
     * Calculate distance between coordinates using Haversine formula
     */
    private function calculateDistance($coordinates)
    {
        $totalDistance = 0;
        
        for ($i = 1; $i < count($coordinates); $i++) {
            $totalDistance += $this->haversineDistance(
                $coordinates[$i-1]['lat'], $coordinates[$i-1]['lng'],
                $coordinates[$i]['lat'], $coordinates[$i]['lng']
            );
        }
        
        return $totalDistance;
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
     * Extract elevation data from OSM element
     */
    private function extractElevationData($element, &$elevationPoints)
    {
        if (isset($element['tags']['ele'])) {
            $elevation = (float) $element['tags']['ele'];
            if ($elevation > 0) {
                $elevationPoints[] = $elevation;
            }
        }
    }

    /**
     * Optimize coordinates by removing unnecessary points
     */
    private function optimizeCoordinates($coordinates)
    {
        if (count($coordinates) <= 2) {
            return $coordinates;
        }
        
        $optimized = [$coordinates[0]]; // Always keep first point
        $tolerance = 0.0001; // ~10 meters tolerance
        
        for ($i = 1; $i < count($coordinates) - 1; $i++) {
            $prev = $coordinates[$i - 1];
            $current = $coordinates[$i];
            $next = $coordinates[$i + 1];
            
            // Calculate perpendicular distance from current point to line between prev and next
            $distance = $this->perpendicularDistance($current, $prev, $next);
            
            if ($distance > $tolerance) {
                $optimized[] = $current;
            }
        }
        
        $optimized[] = $coordinates[count($coordinates) - 1]; // Always keep last point
        
        return $optimized;
    }

    /**
     * Calculate perpendicular distance from point to line
     */
    private function perpendicularDistance($point, $lineStart, $lineEnd)
    {
        $A = $point['lat'] - $lineStart['lat'];
        $B = $point['lng'] - $lineStart['lng'];
        $C = $lineEnd['lat'] - $lineStart['lat'];
        $D = $lineEnd['lng'] - $lineStart['lng'];
        
        $dot = $A * $C + $B * $D;
        $lenSq = $C * $C + $D * $D;
        
        if ($lenSq == 0) {
            return sqrt($A * $A + $B * $B);
        }
        
        $param = $dot / $lenSq;
        
        if ($param < 0) {
            $xx = $lineStart['lat'];
            $yy = $lineStart['lng'];
        } elseif ($param > 1) {
            $xx = $lineEnd['lat'];
            $yy = $lineEnd['lng'];
        } else {
            $xx = $lineStart['lat'] + $param * $C;
            $yy = $lineStart['lng'] + $param * $D;
        }
        
        $dx = $point['lat'] - $xx;
        $dy = $point['lng'] - $yy;
        
        return sqrt($dx * $dx + $dy * $dy);
    }

    /**
     * Get elevation profile for coordinates
     */
    private function getElevationProfile($coordinates)
    {
        // This could be enhanced to use external elevation services
        // For now, return basic structure
        $profile = [];
        $totalDistance = 0;
        
        foreach ($coordinates as $index => $coord) {
            if ($index > 0) {
                $totalDistance += $this->haversineDistance(
                    $coordinates[$index-1]['lat'], $coordinates[$index-1]['lng'],
                    $coord['lat'], $coord['lng']
                );
            }
            
            $profile[] = [
                'distance' => round($totalDistance, 3),
                'elevation' => 0, // Would be filled by elevation service
                'lat' => $coord['lat'],
                'lng' => $coord['lng']
            ];
        }
        
        return $profile;
    }

    /**
     * Search for specific trail by name in OSM
     */
    public function searchTrailByName($trailName, $location = null)
    {
        try {
            $boundingBox = null;
            
            if ($location) {
                $boundingBox = $this->getBoundingBox($location);
            }
            
            // If no bounding box, search Philippines-wide
            if (!$boundingBox) {
                $boundingBox = [
                    'south' => config('app.osm_philippines_bbox_south', 4.5),
                    'north' => config('app.osm_philippines_bbox_north', 21.0),
                    'west' => config('app.osm_philippines_bbox_west', 116.0),
                    'east' => config('app.osm_philippines_bbox_east', 127.0)
                ];
            }
            
            return $this->searchTrailsInArea($boundingBox, $trailName);
            
        } catch (\Exception $e) {
            Log::error("Trail search error: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Get trail information including difficulty and surface type
     */
    public function getTrailInfo($elements)
    {
        $info = [
            'surface_types' => [],
            'difficulty_levels' => [],
            'trail_visibility' => [],
            'total_ascent' => 0,
            'total_descent' => 0
        ];
        
        foreach ($elements as $element) {
            if (isset($element['tags'])) {
                $tags = $element['tags'];
                
                // Surface information
                if (isset($tags['surface'])) {
                    $info['surface_types'][] = $tags['surface'];
                }
                
                // SAC scale (hiking difficulty)
                if (isset($tags['sac_scale'])) {
                    $info['difficulty_levels'][] = $tags['sac_scale'];
                }
                
                // Trail visibility
                if (isset($tags['trail_visibility'])) {
                    $info['trail_visibility'][] = $tags['trail_visibility'];
                }
            }
        }
        
        // Remove duplicates
        $info['surface_types'] = array_unique($info['surface_types']);
        $info['difficulty_levels'] = array_unique($info['difficulty_levels']);
        $info['trail_visibility'] = array_unique($info['trail_visibility']);
        
        return $info;
    }
}

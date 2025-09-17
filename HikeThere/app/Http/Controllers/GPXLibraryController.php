<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use DOMDocument;
use DOMXPath;

class GPXLibraryController extends Controller
{
    /**
     * Get available GPX files from the public/geojson directory
     */
    public function index()
    {
        try {
            $gpxFiles = [];
            $gpxDirectory = public_path('geojson');
            
            if (is_dir($gpxDirectory)) {
                $files = glob($gpxDirectory . '/*.gpx');
                
                foreach ($files as $file) {
                    $filename = basename($file);
                    $name = pathinfo($filename, PATHINFO_FILENAME);
                    
                    // Prioritize Philippine trails files
                    $priority = 0;
                    if (str_contains($filename, 'philippine') || str_contains($filename, 'luzon')) {
                        $priority = 100;
                    } elseif (str_contains($filename, 'test')) {
                        $priority = 10;
                    }
                    
                    // Get basic file info
                    $gpxFiles[] = [
                        'filename' => $filename,
                        'name' => ucwords(str_replace(['_', '-'], ' ', $name)),
                        'path' => 'geojson/' . $filename,
                        'url' => asset('geojson/' . $filename),
                        'size' => filesize($file),
                        'modified' => filemtime($file),
                        'priority' => $priority
                    ];
                }
                
                // Sort by priority (Philippine trails first)
                usort($gpxFiles, function($a, $b) {
                    return $b['priority'] - $a['priority'];
                });
            }
            
            return response()->json([
                'success' => true,
                'files' => $gpxFiles
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error loading GPX library', ['error' => $e->getMessage()]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to load GPX library',
                'files' => []
            ]);
        }
    }
    
    /**
     * Parse a specific GPX file and return trail data
     */
    public function parseGPX(Request $request)
    {
        $request->validate([
            'filename' => 'required|string'
        ]);
        
        try {
            $filename = $request->filename;
            $filePath = public_path('geojson/' . $filename);
            
            if (!file_exists($filePath)) {
                return response()->json([
                    'success' => false,
                    'message' => 'GPX file not found'
                ]);
            }
            
            $gpxContent = file_get_contents($filePath);
            $gpxData = $this->parseGPXContent($gpxContent);
            
            return response()->json([
                'success' => true,
                'data' => $gpxData
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error parsing GPX file', [
                'filename' => $request->filename,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to parse GPX file'
            ]);
        }
    }
    
    /**
     * Search for trails across all GPX files
     */
    public function searchTrails(Request $request)
    {
        $request->validate([
            'mountain_name' => 'required|string|min:2',
            'trail_name' => 'nullable|string',
            'location' => 'nullable|string'
        ]);
        
        try {
            $mountainName = strtolower($request->mountain_name);
            $trailName = strtolower($request->trail_name ?? '');
            $location = strtolower($request->location ?? '');
            
            $allMatches = [];
            $gpxDirectory = public_path('geojson');
            
            if (is_dir($gpxDirectory)) {
                $files = glob($gpxDirectory . '/*.gpx');
                
                foreach ($files as $file) {
                    $filename = basename($file);
                    
                    // Prioritize Philippine trail files
                    if (!str_contains($filename, 'philippine') && !str_contains($filename, 'luzon') && !str_contains($filename, 'test')) {
                        continue;
                    }
                    
                    $gpxContent = file_get_contents($file);
                    $gpxData = $this->parseGPXContent($gpxContent);
                    
                    if ($gpxData && isset($gpxData['trails'])) {
                        $matches = $this->findMatchingTrails($gpxData['trails'], $mountainName, $trailName, $location);
                        
                        foreach ($matches as $match) {
                            $match['source_file'] = $filename;
                            $allMatches[] = $match;
                        }
                    }
                }
            }
            
            // Sort by match score
            usort($allMatches, function($a, $b) {
                return $b['match_score'] - $a['match_score'];
            });
            
            return response()->json([
                'success' => true,
                'trails' => array_slice($allMatches, 0, 10), // Return top 10 matches
                'total_matches' => count($allMatches),
                'search_params' => [
                    'mountain_name' => $request->mountain_name,
                    'trail_name' => $request->trail_name,
                    'location' => $request->location
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error searching trails', [
                'mountain_name' => $request->mountain_name,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to search trails',
                'trails' => []
            ]);
        }
    }
    
    /**
     * Find matching trails based on search criteria
     */
    private function findMatchingTrails($trails, $mountainName, $trailName, $location)
    {
        $matches = [];
        
        foreach ($trails as $trail) {
            $score = 0;
            $trailNameLower = strtolower($trail['name']);
            $trailDescLower = strtolower($trail['description'] ?? '');
            
            // Exact mountain name match (highest score)
            if (str_contains($trailNameLower, $mountainName)) {
                $score += 100;
            }
            
            // Partial mountain name match
            $mountainWords = explode(' ', $mountainName);
            foreach ($mountainWords as $word) {
                if (strlen($word) > 2 && str_contains($trailNameLower, $word)) {
                    $score += 30;
                }
            }
            
            // Trail name match (if provided)
            if ($trailName && str_contains($trailNameLower, $trailName)) {
                $score += 50;
            }
            
            // Location match (if available)
            if ($location && (str_contains($trailNameLower, $location) || str_contains($trailDescLower, $location))) {
                $score += 20;
            }
            
            // Special keywords for better matching
            $keywords = ['mount', 'mt', 'peak', 'summit'];
            foreach ($keywords as $keyword) {
                if (str_contains($mountainName, $keyword) && str_contains($trailNameLower, $keyword)) {
                    $score += 10;
                }
            }
            
            // Philippine-specific mountains get bonus
            $philippineMountains = ['pulag', 'apo', 'kanlaon', 'makiling', 'banahaw', 'batulao', 'pinatubo'];
            foreach ($philippineMountains as $mountain) {
                if (str_contains($mountainName, $mountain) && str_contains($trailNameLower, $mountain)) {
                    $score += 20;
                }
            }
            
            // Only include trails with reasonable match score
            if ($score >= 30) {
                $trail['match_score'] = $score;
                $matches[] = $trail;
            }
        }
        
        return $matches;
    }
    
    /**
     * Parse GPX content and extract trail data
     */
    private function parseGPXContent($gpxContent)
    {
        try {
            $dom = new DOMDocument();
            $dom->loadXML($gpxContent);
            $xpath = new DOMXPath($dom);
            $xpath->registerNamespace('gpx', 'http://www.topografix.com/GPX/1/1');
            
            $trails = [];
            
            // Parse tracks
            $tracks = $xpath->query('//gpx:trk');
            foreach ($tracks as $track) {
                $nameElement = $xpath->query('.//gpx:name', $track)->item(0);
                $descElement = $xpath->query('.//gpx:desc', $track)->item(0);
                
                $trail = [
                    'name' => $nameElement ? $nameElement->nodeValue : 'Unnamed Trail',
                    'description' => $descElement ? $descElement->nodeValue : '',
                    'type' => 'track',
                    'coordinates' => [],
                    'waypoints' => [],
                    'distance' => 0,
                    'elevation_gain' => 0,
                    'min_elevation' => null,
                    'max_elevation' => null
                ];
                
                // Get track points
                $trackPoints = $xpath->query('.//gpx:trkpt', $track);
                $coordinates = [];
                $elevations = [];
                $totalDistance = 0;
                $lastPoint = null;
                
                foreach ($trackPoints as $point) {
                    /** @var \DOMElement $point */
                    $lat = floatval($point->getAttribute('lat'));
                    $lng = floatval($point->getAttribute('lon'));
                    $eleElement = $xpath->query('.//gpx:ele', $point)->item(0);
                    $elevation = $eleElement ? floatval($eleElement->nodeValue) : null;
                    
                    $coordinates[] = [$lat, $lng];
                    if ($elevation !== null) {
                        $elevations[] = $elevation;
                    }
                    
                    // Calculate distance
                    if ($lastPoint) {
                        $totalDistance += $this->calculateDistance($lastPoint[0], $lastPoint[1], $lat, $lng);
                    }
                    $lastPoint = [$lat, $lng];
                }
                
                $trail['coordinates'] = $coordinates;
                $trail['distance'] = round($totalDistance);
                
                // Calculate elevation data
                if (!empty($elevations)) {
                    $trail['min_elevation'] = min($elevations);
                    $trail['max_elevation'] = max($elevations);
                    
                    // Calculate elevation gain
                    $elevationGain = 0;
                    for ($i = 1; $i < count($elevations); $i++) {
                        $gain = $elevations[$i] - $elevations[$i-1];
                        if ($gain > 0) {
                            $elevationGain += $gain;
                        }
                    }
                    $trail['elevation_gain'] = round($elevationGain);
                }
                
                $trails[] = $trail;
            }
            
            // Parse waypoints
            $waypoints = $xpath->query('//gpx:wpt');
            $waypointData = [];
            foreach ($waypoints as $waypoint) {
                /** @var \DOMElement $waypoint */
                $nameElement = $xpath->query('.//gpx:name', $waypoint)->item(0);
                $descElement = $xpath->query('.//gpx:desc', $waypoint)->item(0);
                
                $waypointData[] = [
                    'name' => $nameElement ? $nameElement->nodeValue : 'Waypoint',
                    'description' => $descElement ? $descElement->nodeValue : '',
                    'lat' => floatval($waypoint->getAttribute('lat')),
                    'lng' => floatval($waypoint->getAttribute('lon')),
                    'elevation' => $this->getElevationFromWaypoint($waypoint)
                ];
            }
            
            return [
                'trails' => $trails,
                'waypoints' => $waypointData,
                'total_trails' => count($trails),
                'total_waypoints' => count($waypointData)
            ];
            
        } catch (\Exception $e) {
            Log::error('Error parsing GPX content', ['error' => $e->getMessage()]);
            return null;
        }
    }
    
    /**
     * Calculate distance between two points using Haversine formula
     */
    private function calculateDistance($lat1, $lng1, $lat2, $lng2)
    {
        $earthRadius = 6371000; // Earth's radius in meters
        
        $lat1Rad = deg2rad($lat1);
        $lat2Rad = deg2rad($lat2);
        $deltaLatRad = deg2rad($lat2 - $lat1);
        $deltaLngRad = deg2rad($lng2 - $lng1);
        
        $a = sin($deltaLatRad/2) * sin($deltaLatRad/2) +
             cos($lat1Rad) * cos($lat2Rad) *
             sin($deltaLngRad/2) * sin($deltaLngRad/2);
        $c = 2 * atan2(sqrt($a), sqrt(1-$a));
        
        return $earthRadius * $c;
    }
    
    /**
     * Get elevation from waypoint
     */
    private function getElevationFromWaypoint($wpt)
    {
        $eleElement = $wpt->getElementsByTagName('ele')->item(0);
        return $eleElement ? floatval($eleElement->nodeValue) : null;
    }
}

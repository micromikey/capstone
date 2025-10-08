<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use DOMDocument;
use DOMXPath;

class GPXLibraryController extends Controller
{
    /**
     * Get available GPX files from the configured storage (GCS in production)
     */
    public function index()
    {
        try {
            $gpxFiles = [];
            $disk = config('filesystems.default', 'public');
            
            // Get all .gpx files from geojson folder in configured storage
            $files = Storage::disk($disk)->files('geojson');
            $gpxFilesList = array_filter($files, function($file) {
                return pathinfo($file, PATHINFO_EXTENSION) === 'gpx';
            });
            
            foreach ($gpxFilesList as $filePath) {
                $filename = basename($filePath);
                $name = pathinfo($filename, PATHINFO_FILENAME);
                
                // Prioritize Philippine trails files
                $priority = 0;
                if (str_contains($filename, 'philippine') || str_contains($filename, 'luzon')) {
                    $priority = 100;
                } elseif (str_contains($filename, 'test')) {
                    $priority = 10;
                }
                
                // Get file info from storage
                $size = Storage::disk($disk)->size($filePath);
                $modified = Storage::disk($disk)->lastModified($filePath);
                $url = Storage::disk($disk)->url($filePath);
                
                $gpxFiles[] = [
                    'filename' => $filename,
                    'name' => ucwords(str_replace(['_', '-'], ' ', $name)),
                    'path' => $filePath,
                    'url' => $url,
                    'size' => $size,
                    'modified' => $modified,
                    'priority' => $priority
                ];
            }
            
            // Sort by priority (Philippine trails first)
            usort($gpxFiles, function($a, $b) {
                return $b['priority'] - $a['priority'];
            });
            
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
            $disk = config('filesystems.default', 'public');
            $filePath = 'geojson/' . $filename;
            
            // Check if file exists in storage
            if (!Storage::disk($disk)->exists($filePath)) {
                return response()->json([
                    'success' => false,
                    'message' => 'GPX file not found'
                ]);
            }
            
            // Read content from storage
            $gpxContent = Storage::disk($disk)->get($filePath);
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
            $disk = config('filesystems.default', 'public');
            
            // Get all .gpx files from geojson folder
            $files = Storage::disk($disk)->files('geojson');
            $gpxFilesList = array_filter($files, function($file) {
                return pathinfo($file, PATHINFO_EXTENSION) === 'gpx';
            });
            
            foreach ($gpxFilesList as $filePath) {
                $filename = basename($filePath);
                
                // Consider all GPX files in the directory when searching
                $gpxContent = Storage::disk($disk)->get($filePath);
                $gpxData = $this->parseGPXContent($gpxContent);
                
                if ($gpxData && isset($gpxData['trails'])) {
                    $matches = $this->findMatchingTrails($gpxData['trails'], $mountainName, $trailName, $location);
                    
                    foreach ($matches as $match) {
                        $match['source_file'] = $filename;
                        $allMatches[] = $match;
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

        // Normalize incoming search terms
        $mountainClean = $this->normalizeKeyword($mountainName);
        $trailClean = $this->normalizeKeyword($trailName);
        $locationClean = $this->normalizeKeyword($location);

        foreach ($trails as $trail) {
            $score = 0;

            $trailNameText = $trail['name'] ?? '';
            $trailDescText = $trail['description'] ?? '';
            $trailCombined = trim($trailNameText . ' ' . $trailDescText);
            $trailClean = $this->normalizeKeyword($trailCombined);

            // If cleaned strings are identical or the trail contains the cleaned term, boost strongly
            if (!empty($mountainClean) && mb_stripos($trailClean, $mountainClean) !== false) {
                $score += 40;
            }

            if (!empty($trailClean) && mb_stripos($trailClean, $trailClean) !== false) {
                $score += 40;
            }

            // Token overlap: count common tokens between search tokens and trail tokens
            $mountTokens = $this->tokens($mountainClean);
            $trailTokens = $this->tokens($trailClean);
            $tokenMatches = count(array_intersect($mountTokens, $trailTokens));
            if ($tokenMatches > 0) {
                $score += min(30, $tokenMatches * 12);
            }

            // If explicit trail name provided, prefer tokens from that too
            $searchTrailTokens = $this->tokens($trailClean);
            $queryTrailTokens = $this->tokens($trailName);
            $trailTokenMatches = count(array_intersect($searchTrailTokens, $queryTrailTokens));
            if ($trailTokenMatches > 0) {
                $score += min(40, $trailTokenMatches * 15);
            }

            // Fuzzy distance improvements: compare each search token to trail tokens
            foreach (array_merge($mountTokens, $this->tokens($trailName)) as $qToken) {
                foreach ($trailTokens as $tToken) {
                    if (!$qToken || !$tToken) continue;
                    $dist = levenshtein($qToken, $tToken);
                    $len = max(mb_strlen($qToken), mb_strlen($tToken));
                    if ($len === 0) continue;
                    $ratio = 1 - ($dist / $len); // similarity
                    if ($ratio >= 0.8) {
                        $score += 12; // very close
                    } elseif ($ratio >= 0.6) {
                        $score += 6; // somewhat close
                    }
                }
            }

            // Location match
            if (!empty($locationClean) && (mb_stripos($trailClean, $locationClean) !== false)) {
                $score += 15;
            }

            // Bonus for explicit substring presence in original name/description
            if (!empty($mountainClean) && mb_stripos($trailCombined, $mountainClean) !== false) {
                $score += 10;
            }

            if (!empty($trailName) && mb_stripos($trailCombined, $trailName) !== false) {
                $score += 15;
            }

            // Small boost for Philippine-specific mountains found in text
            $philippineMountains = ['pulag', 'apo', 'kanlaon', 'makiling', 'banahaw', 'batulao', 'pinatubo'];
            foreach ($philippineMountains as $mountain) {
                if (!empty($mountainClean) && mb_stripos($mountainClean, $mountain) !== false && mb_stripos($trailClean, $mountain) !== false) {
                    $score += 8;
                }
            }

            // Normalize score to 0-100 range
            $score = max(0, min(100, (int)$score));

            // Only include trails with reasonable match score (>= 35)
            if ($score >= 35) {
                $trail['match_score'] = $score;
                $matches[] = $trail;
            }
        }

        return $matches;
    }

    /**
     * Normalize a keyword: lowercase, remove punctuation, strip generic tokens
     */
    private function normalizeKeyword($raw)
    {
        if (empty($raw)) return '';
        $s = mb_strtolower($raw);
        // Remove punctuation, keep unicode letters and numbers and spaces
        $s = preg_replace('/[^\p{L}\p{N}\s]+/u', ' ', $s);
        $s = preg_replace('/\s+/u', ' ', $s);
        $s = trim($s);

        if ($s === '') return '';

        // Remove common generic tokens that add noise
        $generic = ['mount', 'mountain', 'mt', 'mtn', 'trail', 'trails', 'hill', 'peak', 'range', 'summit'];
        $parts = preg_split('/\s+/u', $s);
        $parts = array_filter($parts, function($p) use ($generic) {
            return !in_array($p, $generic);
        });

        return trim(implode(' ', $parts));
    }

    /**
     * Tokenize a cleaned string into meaningful words
     */
    private function tokens($cleaned)
    {
        if (empty($cleaned)) return [];
        $parts = preg_split('/\s+/u', mb_strtolower(trim($cleaned)));
        $parts = array_filter($parts, function($p) { return mb_strlen($p) > 1; });
        return array_values($parts);
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

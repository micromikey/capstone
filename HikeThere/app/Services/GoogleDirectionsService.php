<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Peak;

class GoogleDirectionsService
{
    private $apiKey;

    public function __construct()
    {
        $this->apiKey = config('services.google.maps_api_key');
    }

    /**
     * Get trail coordinates from Google Directions API
     * 
     * @param string $origin Starting point (address or lat,lng)
     * @param string $destination Ending point (address or lat,lng)
     * @param array $waypoints Optional waypoints along the route
     * @return array|null Array of coordinates or null if failed
     */
    public function getTrailCoordinates($origin, $destination, $waypoints = [])
    {
        if (!$this->apiKey) {
            Log::error('Google Maps API key not configured');
            return null;
        }

        try {
            $params = [
                'origin' => $origin,
                'destination' => $destination,
                'mode' => 'walking', // Use walking mode for hiking trails
                'key' => $this->apiKey
            ];

            // Add waypoints if provided
            if (!empty($waypoints)) {
                $params['waypoints'] = implode('|', $waypoints);
            }

            $response = Http::get('https://maps.googleapis.com/maps/api/directions/json', $params);

            if ($response->successful()) {
                $data = $response->json();
                
                if ($data['status'] === 'OK' && !empty($data['routes'])) {
                    return $this->extractCoordinatesFromRoute($data['routes'][0]);
                } else {
                    Log::error('Google Directions API error: ' . ($data['error_message'] ?? $data['status']));
                }
            } else {
                Log::error('HTTP error when calling Google Directions API: ' . $response->status());
            }
        } catch (\Exception $e) {
            Log::error('Exception in Google Directions API call: ' . $e->getMessage());
        }

        return null;
    }

    /**
     * Generic directions fetch used by HybridRoutingService.
     * Standardizes structure somewhat similar to ORS parse response.
     *
     * @param string $origin
     * @param string $destination
     * @param array $waypoints
     * @param string $mode driving|walking|transit|bicycling
     * @param string|null $arrivalTime   HH:MM (local) only for transit
     * @param string|null $departureTime HH:MM (local) only for transit
     * @return array|null
     */
    public function getDirections(string $origin, string $destination, array $waypoints = [], string $mode = 'driving', ?string $arrivalTime = null, ?string $departureTime = null): ?array
    {
        if (!$this->apiKey) {
            Log::error('Google Maps API key not configured for getDirections');
            return null;
        }

        try {
            $params = [
                'origin' => $origin,
                'destination' => $destination,
                'mode' => $mode,
                'key' => $this->apiKey,
            ];

            if (!empty($waypoints)) {
                $params['waypoints'] = implode('|', $waypoints);
            }

            // Transit specific optional times (convert HH:MM today to timestamp)
            if ($mode === 'transit') {
                $now = now();
                if ($arrivalTime) {
                    [$h,$m] = explode(':', $arrivalTime);
                    $arrivalTs = $now->copy()->setTime((int)$h,(int)$m)->timestamp;
                    $params['arrival_time'] = $arrivalTs;
                } elseif ($departureTime) {
                    [$h,$m] = explode(':', $departureTime);
                    $departTs = $now->copy()->setTime((int)$h,(int)$m)->timestamp;
                    $params['departure_time'] = $departTs;
                }
            }

            $response = Http::get('https://maps.googleapis.com/maps/api/directions/json', $params);
            if (!$response->successful()) {
                Log::warning('Google Directions API HTTP failure', ['status'=>$response->status(),'body'=>$response->body()]);
                return null;
            }

            $data = $response->json();
            if (($data['status'] ?? '') !== 'OK' || empty($data['routes'])) {
                Log::info('Google Directions API non-OK', ['status'=>$data['status'] ?? 'NONE','error'=>$data['error_message'] ?? null]);
                return null;
            }

            $route = $data['routes'][0];
            return $this->formatRoute($route, $mode);
        } catch (\Exception $e) {
            Log::error('Exception in getDirections: '.$e->getMessage());
            return null;
        }
    }

    /**
     * Format Google route into unified structure similar to ORS output
     */
    private function formatRoute(array $route, string $mode): array
    {
        $legs = $route['legs'] ?? [];
        $totalDistance = 0;
        $totalDuration = 0;
        $formattedLegs = [];

        foreach ($legs as $leg) {
            $legDistance = $leg['distance']['value'] ?? 0; // meters
            $legDuration = $leg['duration']['value'] ?? 0; // seconds
            $totalDistance += $legDistance;
            $totalDuration += $legDuration;

            $steps = [];
            foreach ($leg['steps'] ?? [] as $step) {
                $steps[] = [
                    'instruction' => strip_tags($step['html_instructions'] ?? ''),
                    'distance_m' => $step['distance']['value'] ?? null,
                    'duration_s' => $step['duration']['value'] ?? null,
                    'travel_mode' => $step['travel_mode'] ?? null,
                    'start_location' => $step['start_location'] ?? null,
                    'end_location' => $step['end_location'] ?? null,
                ];
            }

            $formattedLegs[] = [
                'distance_m' => $legDistance,
                'duration_s' => $legDuration,
                'start_address' => $leg['start_address'] ?? null,
                'end_address' => $leg['end_address'] ?? null,
                'start_location' => $leg['start_location'] ?? null,
                'end_location' => $leg['end_location'] ?? null,
                'steps' => $steps,
            ];
        }

        return [
            'provider' => 'google',
            'mode' => $mode,
            'total_distance' => $totalDistance, // meters
            'total_duration' => $totalDuration, // seconds
            'legs' => $formattedLegs,
            'polyline' => $route['overview_polyline']['points'] ?? null,
        ];
    }

    /**
     * Extract coordinates from Google Directions route
     * 
     * @param array $route Route data from Google Directions API
     * @return array Array of lat/lng coordinates
     */
    private function extractCoordinatesFromRoute($route)
    {
        $coordinates = [];
        
        if (isset($route['legs'])) {
            foreach ($route['legs'] as $leg) {
                if (isset($leg['steps'])) {
                    foreach ($leg['steps'] as $step) {
                        // Add start location of step
                        $coordinates[] = [
                            'lat' => $step['start_location']['lat'],
                            'lng' => $step['start_location']['lng']
                        ];

                        // Decode polyline for more detailed path if available
                        if (isset($step['polyline']['points'])) {
                            $decodedPoints = $this->decodePolyline($step['polyline']['points']);
                            $coordinates = array_merge($coordinates, $decodedPoints);
                        }

                        // Add end location of step
                        $coordinates[] = [
                            'lat' => $step['end_location']['lat'],
                            'lng' => $step['end_location']['lng']
                        ];
                    }
                }
            }
        }

        // Remove duplicates and return unique coordinates
        return $this->removeDuplicateCoordinates($coordinates);
    }

    /**
     * Decode Google polyline encoding
     * 
     * @param string $encoded Encoded polyline string
     * @return array Array of decoded coordinates
     */
    private function decodePolyline($encoded)
    {
        $len = strlen($encoded);
        $index = 0;
        $array = [];
        $lat = 0;
        $lng = 0;

        while ($index < $len) {
            $b = 0;
            $shift = 0;
            $result = 0;
            do {
                $b = ord(substr($encoded, $index++, 1)) - 63;
                $result |= ($b & 0x1f) << $shift;
                $shift += 5;
            } while ($b >= 0x20);
            $dlat = (($result & 1) ? ~($result >> 1) : ($result >> 1));
            $lat += $dlat;

            $shift = 0;
            $result = 0;
            do {
                $b = ord(substr($encoded, $index++, 1)) - 63;
                $result |= ($b & 0x1f) << $shift;
                $shift += 5;
            } while ($b >= 0x20);
            $dlng = (($result & 1) ? ~($result >> 1) : ($result >> 1));
            $lng += $dlng;

            $array[] = [
                'lat' => $lat / 1E5,
                'lng' => $lng / 1E5
            ];
        }

        return $array;
    }

    /**
     * Remove duplicate coordinates to optimize the trail path
     * 
     * @param array $coordinates Array of coordinates
     * @param float $threshold Minimum distance between points (in degrees)
     * @return array Filtered coordinates
     */
    private function removeDuplicateCoordinates($coordinates, $threshold = 0.0001)
    {
        if (empty($coordinates)) {
            return [];
        }

        $filtered = [$coordinates[0]]; // Always include first point
        $lastPoint = $coordinates[0];

        for ($i = 1; $i < count($coordinates); $i++) {
            $current = $coordinates[$i];
            
            // Calculate simple distance
            $latDiff = abs($current['lat'] - $lastPoint['lat']);
            $lngDiff = abs($current['lng'] - $lastPoint['lng']);
            
            // Only add point if it's far enough from the last point
            if ($latDiff > $threshold || $lngDiff > $threshold) {
                $filtered[] = $current;
                $lastPoint = $current;
            }
        }

        // Always include last point if it's different from the last filtered point
        $lastCoord = end($coordinates);
        $lastFiltered = end($filtered);
        if ($lastCoord['lat'] !== $lastFiltered['lat'] || $lastCoord['lng'] !== $lastFiltered['lng']) {
            $filtered[] = $lastCoord;
        }

        return $filtered;
    }

    /**
     * Get coordinates for a trail based on location and trail name
     * 
     * @param string $location Location of the trail
     * @param string $trailName Name of the trail
     * @param string $mountain Mountain name (optional)
     * @return array|null Array of coordinates or null if failed
     */
    public function getTrailCoordinatesByLocation($location, $trailName, $mountain = null)
    {
        // Construct search queries for start and end points
        $baseLocation = $location . ($mountain ? ', ' . $mountain : '');
        
        // Try to find trail start and end points
        $startQuery = $trailName . ' trailhead, ' . $baseLocation;
        $endQuery = $trailName . ' summit, ' . $baseLocation;
        
        // If we can't find specific trail points, use the general location
        $fallbackQuery = $baseLocation;
        
        try {
            // First try to get specific trail coordinates
            $coordinates = $this->getTrailCoordinates($startQuery, $endQuery);

            if ($coordinates && count($coordinates) > 2) {
                return $coordinates;
            }

            // Do NOT return a generated circular fallback here — return null so callers
            // will continue to try other providers (ORS, OSM snapping) instead of
            // accepting a fake circular path as valid geometry.
            return null;

        } catch (\Exception $e) {
            Log::error('Error getting trail coordinates: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Attempt to build a route specifically from a trailhead to an actual stored peak (summit) if available.
     * Falls back to name-based route if peak not found or directions fail.
     *
     * @param string $locationName  e.g. Barangay or town
     * @param string $province      Province name
     * @param string $trailName     Provided trail name
     * @param string $mountainName  Mountain name
     * @return array|null
     */
    public function getTrailheadToPeakCoordinates(string $locationName, string $province, string $trailName, string $mountainName)
    {
        // Try to find a peak in DB matching mountain name (case-insensitive LIKE)
        $peak = Peak::where('name', 'LIKE', "%" . $mountainName . "%")
            ->orderByRaw('LENGTH(name) ASC')
            ->first();

        if (!$peak) {
            // No stored peak, fallback immediately
            return $this->getTrailCoordinatesByLocation($locationName . ', ' . $province, $trailName, $mountainName);
        }

        $summitPoint = $peak->latitude . ',' . $peak->longitude; // lat,lng for human readability in logs
        $destination = $peak->latitude . ',' . $peak->longitude; // use raw pair for destination

        // Candidate origins (most specific first)
        $origins = [
            $trailName . ' trailhead, ' . $locationName . ', ' . $province,
            $mountainName . ' trailhead, ' . $locationName . ', ' . $province,
            $locationName . ' trailhead, ' . $province,
            $locationName . ', ' . $province
        ];

        foreach ($origins as $origin) {
            $coords = $this->getTrailCoordinates($origin, $destination);
            if ($coords && count($coords) > 1) {
                Log::info('Trailhead->Peak path resolved', [
                    'origin' => $origin,
                    'peak' => $summitPoint,
                    'points' => count($coords)
                ]);
                return $coords;
            }
        }

        // Fallback to generic method
        return $this->getTrailCoordinatesByLocation($locationName . ', ' . $province, $trailName, $mountainName);
    }

    /**
     * Generate fallback coordinates for a location
     * 
     * @param string $location Location to center the coordinates around
     * @return array|null Array of coordinates or null if failed
     */
    private function generateFallbackCoordinates($location)
    {
        try {
            // Get coordinates for the location
            $response = Http::get('https://maps.googleapis.com/maps/api/geocode/json', [
                'address' => $location,
                'key' => $this->apiKey
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                if ($data['status'] === 'OK' && !empty($data['results'])) {
                    $centerLat = $data['results'][0]['geometry']['location']['lat'];
                    $centerLng = $data['results'][0]['geometry']['location']['lng'];
                    
                    // Generate a simple circular trail around the location
                    return $this->generateCircularTrail($centerLat, $centerLng);
                }
            }
        } catch (\Exception $e) {
            Log::error('Error in fallback coordinate generation: ' . $e->getMessage());
        }

        return null;
    }

    /**
     * Generate a circular trail around a center point
     * 
     * @param float $centerLat Center latitude
     * @param float $centerLng Center longitude
     * @param float $radius Radius in kilometers (default 2km)
     * @param int $points Number of points to generate
     * @return array Array of coordinates forming a circular trail
     */
    private function generateCircularTrail($centerLat, $centerLng, $radius = 2, $points = 20)
    {
        $coordinates = [];
        $earthRadius = 6371; // Earth's radius in kilometers
        
        for ($i = 0; $i <= $points; $i++) {
            $angle = ($i * 2 * M_PI) / $points;
            
            // Convert radius from km to degrees (approximate)
            $latRadius = $radius / $earthRadius * (180 / M_PI);
            $lngRadius = $radius / $earthRadius * (180 / M_PI) / cos($centerLat * M_PI / 180);
            
            $lat = $centerLat + ($latRadius * sin($angle));
            $lng = $centerLng + ($lngRadius * cos($angle));
            
            $coordinates[] = [
                'lat' => round($lat, 6),
                'lng' => round($lng, 6)
            ];
        }
        
        return $coordinates;
    }
}

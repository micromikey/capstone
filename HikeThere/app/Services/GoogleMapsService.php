<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class GoogleMapsService
{
    protected $apiKey;
    protected $baseUrl = 'https://maps.googleapis.com/maps/api';

    public function __construct()
    {
        $this->apiKey = config('services.google.maps_api_key');
    }

    /**
     * Calculate travel time and distance using Google Distance Matrix API
     *
     * @param float $originLat
     * @param float $originLng
     * @param float $destLat
     * @param float $destLng
     * @param string $mode (driving, walking, bicycling, transit)
     * @param string $departure_time (optional, for traffic-aware routing)
     * @return array|null
     */
    public function getDistanceMatrix($originLat, $originLng, $destLat, $destLng, $mode = 'driving', $departure_time = null)
    {
        if (empty($this->apiKey)) {
            Log::warning('Google Maps API key not configured');
            return null;
        }

        $origin = "$originLat,$originLng";
        $destination = "$destLat,$destLng";
        
        // Create cache key for this request
        $cacheKey = "google_distance_" . md5($origin . $destination . $mode . ($departure_time ?? ''));
        
        // Check cache first (cache for 30 minutes)
        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        $params = [
            'origins' => $origin,
            'destinations' => $destination,
            'mode' => $mode,
            'units' => 'metric',
            'key' => $this->apiKey,
            'language' => 'en',
            'region' => 'ph' // Philippines region for better local routing
        ];

        // Add departure time for traffic-aware routing (driving mode only)
        if ($mode === 'driving' && $departure_time) {
            $params['departure_time'] = $departure_time;
        }

        try {
            $response = Http::timeout(10)->get($this->baseUrl . '/distancematrix/json', $params);
            
            if (!$response->successful()) {
                Log::error('Google Distance Matrix API error: HTTP ' . $response->status());
                return null;
            }

            $data = $response->json();

            if ($data['status'] !== 'OK') {
                Log::error('Google Distance Matrix API error: ' . $data['status']);
                return null;
            }

            $element = $data['rows'][0]['elements'][0] ?? null;
            
            if (!$element || $element['status'] !== 'OK') {
                Log::error('Google Distance Matrix element error: ' . ($element['status'] ?? 'Unknown'));
                return null;
            }

            $result = [
                'distance_km' => round($element['distance']['value'] / 1000, 2),
                'distance_text' => $element['distance']['text'],
                'duration_minutes' => round($element['duration']['value'] / 60),
                'duration_text' => $element['duration']['text'],
                'mode' => $mode
            ];

            // Include traffic duration if available
            if (isset($element['duration_in_traffic'])) {
                $result['duration_in_traffic_minutes'] = round($element['duration_in_traffic']['value'] / 60);
                $result['duration_in_traffic_text'] = $element['duration_in_traffic']['text'];
            }

            // Cache the result
            Cache::put($cacheKey, $result, 1800); // 30 minutes

            return $result;

        } catch (\Exception $e) {
            Log::error('Google Distance Matrix API exception: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Get detailed directions using Google Directions API
     *
     * @param float $originLat
     * @param float $originLng
     * @param float $destLat
     * @param float $destLng
     * @param string $mode
     * @param array $waypoints (optional)
     * @return array|null
     */
    public function getDirections($originLat, $originLng, $destLat, $destLng, $mode = 'driving', $waypoints = [])
    {
        if (empty($this->apiKey)) {
            Log::warning('Google Maps API key not configured');
            return null;
        }

        $origin = "$originLat,$originLng";
        $destination = "$destLat,$destLng";

        $params = [
            'origin' => $origin,
            'destination' => $destination,
            'mode' => $mode,
            'key' => $this->apiKey,
            'language' => 'en',
            'region' => 'ph'
        ];

        // Add waypoints if provided
        if (!empty($waypoints)) {
            $waypointStr = '';
            foreach ($waypoints as $waypoint) {
                if (isset($waypoint['lat']) && isset($waypoint['lng'])) {
                    $waypointStr .= ($waypointStr ? '|' : '') . $waypoint['lat'] . ',' . $waypoint['lng'];
                }
            }
            if ($waypointStr) {
                $params['waypoints'] = $waypointStr;
            }
        }

        try {
            $response = Http::timeout(15)->get($this->baseUrl . '/directions/json', $params);
            
            if (!$response->successful()) {
                Log::error('Google Directions API error: HTTP ' . $response->status());
                return null;
            }

            $data = $response->json();

            if ($data['status'] !== 'OK') {
                Log::error('Google Directions API error: ' . $data['status']);
                return null;
            }

            $route = $data['routes'][0] ?? null;
            if (!$route) {
                Log::error('No routes found in Google Directions response');
                return null;
            }

            $leg = $route['legs'][0] ?? null;
            if (!$leg) {
                Log::error('No legs found in Google Directions route');
                return null;
            }

            return [
                'distance_km' => round($leg['distance']['value'] / 1000, 2),
                'distance_text' => $leg['distance']['text'],
                'duration_minutes' => round($leg['duration']['value'] / 60),
                'duration_text' => $leg['duration']['text'],
                'start_address' => $leg['start_address'],
                'end_address' => $leg['end_address'],
                'steps' => $this->extractSteps($leg['steps'] ?? []),
                'overview_polyline' => $route['overview_polyline']['points'] ?? '',
                'mode' => $mode
            ];

        } catch (\Exception $e) {
            Log::error('Google Directions API exception: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Calculate travel time for Philippines routes with Google API integration
     *
     * @param float $originLat
     * @param float $originLng
     * @param float $destLat
     * @param float $destLng
     * @param string $transportMode
     * @param string $departureTime (optional)
     * @return int Travel time in minutes
     */
    public function calculatePhilippinesTravelTime($originLat, $originLng, $destLat, $destLng, $transportMode = 'driving', $departureTime = null)
    {
        // Map transport modes to Google modes
        $googleMode = $this->mapTransportToGoogleMode($transportMode);
        
        // Try Google API first
        $googleResult = $this->getDistanceMatrix($originLat, $originLng, $destLat, $destLng, $googleMode, $departureTime);
        
        if ($googleResult) {
            $baseDuration = $googleResult['duration_minutes'];
            
            // Use traffic-aware duration if available (driving mode)
            if (isset($googleResult['duration_in_traffic_minutes'])) {
                $baseDuration = $googleResult['duration_in_traffic_minutes'];
            }
            
            // Apply Philippines-specific adjustments
            return $this->applyPhilippinesAdjustments($baseDuration, $transportMode, $originLat, $originLng, $destLat, $destLng);
        }
        
        // Return null if Google API fails - let calling method handle fallback
        Log::warning('Google API unavailable for travel time calculation');
        return null;
    }

    /**
     * Map transport modes to Google Maps modes
     */
    protected function mapTransportToGoogleMode($transportMode)
    {
        $transportMode = strtolower($transportMode);
        
        switch ($transportMode) {
            case 'walking':
                return 'walking';
            case 'bicycling':
            case 'bike':
                return 'bicycling';
            case 'transit':
            case 'bus':
            case 'train':
            case 'jeepney':
                return 'transit';
            case 'driving':
            case 'car':
            case 'van':
            case 'motorcycle':
            case 'tricycle':
            default:
                return 'driving';
        }
    }

    /**
     * Apply Philippines-specific adjustments to Google travel times
     */
    protected function applyPhilippinesAdjustments($baseDuration, $transportMode, $originLat, $originLng, $destLat, $destLng)
    {
        // Philippines-specific factors
        $adjustments = [
            'bus' => 1.3,        // Buses make frequent stops
            'jeepney' => 1.4,     // Jeepneys make many stops
            'van' => 1.2,         // Tourist vans are faster
            'tricycle' => 1.5,    // Limited to local roads
            'car' => 1.1,         // Private cars are most efficient
            'motorcycle' => 1.2   // Can navigate traffic better
        ];
        
        $multiplier = $adjustments[strtolower($transportMode)] ?? 1.2;
        
        // Additional adjustments for specific regions
        $isManila = $this->isInMetroManila($originLat, $originLng) || $this->isInMetroManila($destLat, $destLng);
        $isProvincial = $this->isProvincialRoute($originLat, $originLng, $destLat, $destLng);
        
        if ($isManila) {
            $multiplier *= 1.3; // Metro Manila traffic factor
        } elseif ($isProvincial) {
            $multiplier *= 1.2; // Provincial road conditions
        }
        
        return round($baseDuration * $multiplier);
    }

    /**
     * Fallback calculation when Google API is unavailable
     */
    protected function fallbackPhilippinesTravelTime($originLat, $originLng, $destLat, $destLng, $transportMode)
    {
        // Calculate distance using Haversine formula
        $distance = $this->calculateHaversineDistance($originLat, $originLng, $destLat, $destLng);
        
        // Philippines-specific speeds (km/h)
        $speeds = [
            'bus' => 35,
            'van' => 40,
            'car' => 45,
            'jeepney' => 30,
            'tricycle' => 25,
            'motorcycle' => 35
        ];
        
        $speed = $speeds[strtolower($transportMode)] ?? 40;
        $baseTime = ($distance / $speed) * 60; // Convert to minutes
        
        // Apply route-specific adjustments
        $isManila = $this->isInMetroManila($originLat, $originLng) || $this->isInMetroManila($destLat, $destLng);
        
        if ($isManila) {
            $baseTime *= 1.4; // Heavy traffic in Manila
        }
        
        return max(30, round($baseTime)); // Minimum 30 minutes
    }

    /**
     * Check if coordinates are in Metro Manila
     */
    protected function isInMetroManila($lat, $lng)
    {
        // Metro Manila bounding box (approximate)
        return $lat >= 14.4 && $lat <= 14.8 && $lng >= 120.9 && $lng <= 121.2;
    }

    /**
     * Check if route is provincial (long distance)
     */
    protected function isProvincialRoute($originLat, $originLng, $destLat, $destLng)
    {
        $distance = $this->calculateHaversineDistance($originLat, $originLng, $destLat, $destLng);
        return $distance > 100; // Routes over 100km considered provincial
    }

    /**
     * Calculate distance using Haversine formula
     */
    protected function calculateHaversineDistance($lat1, $lng1, $lat2, $lng2)
    {
        $earthRadius = 6371; // km
        
        $lat1Rad = deg2rad($lat1);
        $lat2Rad = deg2rad($lat2);
        $deltaLatRad = deg2rad($lat2 - $lat1);
        $deltaLngRad = deg2rad($lng2 - $lng1);
        
        $a = sin($deltaLatRad / 2) * sin($deltaLatRad / 2) +
             cos($lat1Rad) * cos($lat2Rad) *
             sin($deltaLngRad / 2) * sin($deltaLngRad / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        
        return $earthRadius * $c;
    }

    /**
     * Extract simplified steps from Google Directions response
     */
    protected function extractSteps($steps)
    {
        $simplifiedSteps = [];
        
        foreach ($steps as $step) {
            $simplifiedSteps[] = [
                'instruction' => strip_tags($step['html_instructions'] ?? ''),
                'distance' => $step['distance']['text'] ?? '',
                'duration' => $step['duration']['text'] ?? '',
                'maneuver' => $step['maneuver'] ?? ''
            ];
        }
        
        return $simplifiedSteps;
    }

    /**
     * Get current traffic conditions for a route
     */
    public function getTrafficConditions($originLat, $originLng, $destLat, $destLng)
    {
        $departureTime = now()->timestamp;
        
        $result = $this->getDistanceMatrix($originLat, $originLng, $destLat, $destLng, 'driving', $departureTime);
        
        if ($result && isset($result['duration_in_traffic_minutes'])) {
            $normalDuration = $result['duration_minutes'];
            $trafficDuration = $result['duration_in_traffic_minutes'];
            
            $trafficFactor = $trafficDuration / $normalDuration;
            
            if ($trafficFactor > 1.5) {
                return 'heavy';
            } elseif ($trafficFactor > 1.2) {
                return 'moderate';
            } else {
                return 'light';
            }
        }
        
        return 'unknown';
    }

    /**
     * Geocode an address or place name to get coordinates using Google Places API
     * 
     * @param string $address The address or place name to geocode
     * @param string $region Optional region bias (e.g., 'ph' for Philippines)
     * @return array|null Array with 'lat', 'lng', 'formatted_address', 'place_id' or null if failed
     */
    public function geocodeAddress($address, $region = 'ph')
    {
        if (empty($this->apiKey) || empty($address)) {
            Log::warning('Google Maps API key not configured or empty address provided');
            return null;
        }

        // Create cache key for this geocoding request
        $cacheKey = "google_geocode_" . md5($address . $region);
        
        // Check cache first (cache for 1 hour)
        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        $params = [
            'address' => $address,
            'key' => $this->apiKey,
            'language' => 'en',
        ];

        // Add region bias if provided
        if ($region) {
            $params['region'] = $region;
        }

        try {
            $response = Http::timeout(10)->get($this->baseUrl . '/geocode/json', $params);
            
            if (!$response->successful()) {
                Log::error('Google Geocoding API error: HTTP ' . $response->status());
                return null;
            }

            $data = $response->json();

            if ($data['status'] !== 'OK' || empty($data['results'])) {
                Log::warning('Google Geocoding API: No results found for address: ' . $address);
                return null;
            }

            $result = $data['results'][0];
            $location = $result['geometry']['location'];

            $geocodedData = [
                'lat' => $location['lat'],
                'lng' => $location['lng'],
                'formatted_address' => $result['formatted_address'],
                'place_id' => $result['place_id'],
                'types' => $result['types'] ?? []
            ];

            // Cache the result for 1 hour
            Cache::put($cacheKey, $geocodedData, 3600);

            return $geocodedData;

        } catch (\Exception $e) {
            Log::error('Google Geocoding API exception: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Find places using Google Places API Text Search
     * Useful for finding specific types of locations (e.g., "bus terminal in Manila")
     * 
     * @param string $query The search query
     * @param string $region Optional region bias
     * @param string $type Optional place type filter
     * @return array Array of places with coordinates and details
     */
    public function findPlaces($query, $region = 'ph', $type = null)
    {
        if (empty($this->apiKey) || empty($query)) {
            Log::warning('Google Maps API key not configured or empty query provided');
            return [];
        }

        // Create cache key for this search
        $cacheKey = "google_places_" . md5($query . $region . ($type ?? ''));
        
        // Check cache first (cache for 30 minutes)
        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        $params = [
            'query' => $query,
            'key' => $this->apiKey,
            'language' => 'en',
        ];

        // Add region bias if provided
        if ($region) {
            $params['region'] = $region;
        }

        // Add type filter if provided
        if ($type) {
            $params['type'] = $type;
        }

        try {
            $response = Http::timeout(10)->get($this->baseUrl . '/place/textsearch/json', $params);
            
            if (!$response->successful()) {
                Log::error('Google Places API error: HTTP ' . $response->status());
                return [];
            }

            $data = $response->json();

            if ($data['status'] !== 'OK' || empty($data['results'])) {
                Log::warning('Google Places API: No results found for query: ' . $query);
                return [];
            }

            $places = [];
            foreach ($data['results'] as $result) {
                $places[] = [
                    'name' => $result['name'],
                    'lat' => $result['geometry']['location']['lat'],
                    'lng' => $result['geometry']['location']['lng'],
                    'formatted_address' => $result['formatted_address'] ?? '',
                    'place_id' => $result['place_id'],
                    'types' => $result['types'] ?? [],
                    'rating' => $result['rating'] ?? null,
                    'price_level' => $result['price_level'] ?? null
                ];
            }

            // Cache the results for 30 minutes
            Cache::put($cacheKey, $places, 1800);

            return $places;

        } catch (\Exception $e) {
            Log::error('Google Places API exception: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get detailed information about a place using Place Details API
     * 
     * @param string $placeId The Google Place ID
     * @param string $fields Comma-separated list of fields to return
     * @return array|null Place details or null if failed
     */
    public function getPlaceDetails($placeId, $fields = 'name,formatted_address,geometry,types,rating')
    {
        if (empty($this->apiKey) || empty($placeId)) {
            return null;
        }

        // Create cache key
        $cacheKey = "google_place_details_" . md5($placeId . $fields);
        
        // Check cache first (cache for 1 hour)
        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        $params = [
            'place_id' => $placeId,
            'fields' => $fields,
            'key' => $this->apiKey,
            'language' => 'en',
        ];

        try {
            $response = Http::timeout(10)->get($this->baseUrl . '/place/details/json', $params);
            
            if (!$response->successful()) {
                Log::error('Google Place Details API error: HTTP ' . $response->status());
                return null;
            }

            $data = $response->json();

            if ($data['status'] !== 'OK' || empty($data['result'])) {
                Log::warning('Google Place Details API: No result found for place_id: ' . $placeId);
                return null;
            }

            $result = $data['result'];
            
            $placeDetails = [
                'name' => $result['name'] ?? '',
                'formatted_address' => $result['formatted_address'] ?? '',
                'types' => $result['types'] ?? [],
            ];

            // Add geometry if available
            if (isset($result['geometry']['location'])) {
                $placeDetails['lat'] = $result['geometry']['location']['lat'];
                $placeDetails['lng'] = $result['geometry']['location']['lng'];
            }

            // Add other fields if available
            if (isset($result['rating'])) {
                $placeDetails['rating'] = $result['rating'];
            }

            // Cache the result for 1 hour
            Cache::put($cacheKey, $placeDetails, 3600);

            return $placeDetails;

        } catch (\Exception $e) {
            Log::error('Google Place Details API exception: ' . $e->getMessage());
            return null;
        }
    }
}
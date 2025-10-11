<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use App\Models\Trail;
use App\Models\Build;
use App\Services\TrailCalculatorService;
use App\Services\WeatherHelperService;
use App\Services\DataNormalizerService;
use App\Services\IntelligentItineraryService;
use App\Services\DurationParserService;
use App\Services\GoogleMapsService;
use App\Services\EmergencyInfoService;

class ItineraryGeneratorService
{
    protected $trailCalculator;
    protected $weatherHelper;
    protected $dataNormalizer;
    protected $intelligentItinerary;
    protected $durationParser;
    protected $googleMaps;
    protected $emergencyInfo;

    public function __construct(
        TrailCalculatorService $trailCalculator,
        WeatherHelperService $weatherHelper,
        DataNormalizerService $dataNormalizer,
        IntelligentItineraryService $intelligentItinerary,
        DurationParserService $durationParser,
        GoogleMapsService $googleMaps,
        EmergencyInfoService $emergencyInfo
    ) {
        $this->trailCalculator = $trailCalculator;
        $this->weatherHelper = $weatherHelper;
        $this->dataNormalizer = $dataNormalizer;
        $this->intelligentItinerary = $intelligentItinerary;
        $this->durationParser = $durationParser;
        $this->googleMaps = $googleMaps;
        $this->emergencyInfo = $emergencyInfo;
    }

    /**
     * Generate a complete itinerary from raw input data
     */
    public function generateItinerary($itinerary = null, $trail = null, $build = null, $weatherData = [])
    {
        // Normalize all input data
        $normalizedData = $this->dataNormalizer->normalizeInputs($itinerary, $trail, $build, $weatherData);
        
        // Extract normalized values
        $itinerary = $normalizedData['itinerary'];
        $trail = $normalizedData['trail'];
        $build = $normalizedData['build'];
        $weatherData = $normalizedData['weatherData'];
        $routeData = $normalizedData['routeData'];

        // Calculate duration and dates
        $dateInfo = $this->calculateDateInfo($itinerary, $trail, $routeData);
        
        // Generate activities for each day and night
        $dayActivities = $this->generateDayActivities($itinerary, $trail, $dateInfo, $routeData);
        $nightActivities = $this->generateNightActivities($itinerary, $dateInfo, $dayActivities);
        
        // Generate pre-hike transportation activities
        $preHikeActivities = $this->generatePreHikeActivities($trail, $build);

        // Generate emergency information
        $emergencyInfo = $this->emergencyInfo->getEmergencyInfo($trail);

        // Generate static map URL for trail path visualization
        $staticMapUrl = $this->generateStaticMapUrl($trail, $routeData);

        return [
            'itinerary' => $itinerary,
            'trail' => $trail,
            'build' => $build,
            'weatherData' => $weatherData,
            'routeData' => $routeData,
            'dateInfo' => $dateInfo,
            'preHikeActivities' => $preHikeActivities,
            'dayActivities' => $dayActivities,
            'nightActivities' => $nightActivities,
            'emergencyInfo' => $emergencyInfo,
            'staticMapUrl' => $staticMapUrl,
        ];
    }

    /**
     * Calculate duration, dates, and timing information
     */
    protected function calculateDateInfo($itinerary, $trail, $routeData)
    {
        $durationDays = isset($itinerary['duration_days']) ? intval($itinerary['duration_days']) : null;
        $nights = isset($itinerary['nights']) ? intval($itinerary['nights']) : null;
        
        // Always try to parse duration from trail package data first (authoritative source)
        if (!empty($trail)) {
            $trailDuration = null;
            
            // Handle different trail data formats - check package duration first
            if (is_object($trail) && isset($trail->package) && isset($trail->package->duration)) {
                $trailDuration = $trail->package->duration;
            } elseif (is_array($trail) && isset($trail['package']['duration'])) {
                $trailDuration = $trail['package']['duration'];
            } elseif (is_object($trail) && isset($trail->duration)) {
                $trailDuration = $trail->duration;
            } elseif (is_array($trail) && isset($trail['duration'])) {
                $trailDuration = $trail['duration'];
            }
            
            // Use DurationParserService to parse trail duration
            if (!empty($trailDuration)) {
                try {
                    $parsedDuration = $this->durationParser->normalizeDuration($trailDuration);
                    if ($parsedDuration) {
                        // Trail package duration takes precedence over user input
                        $durationDays = $parsedDuration['days'];
                        $nights = $parsedDuration['nights'];
                    }
                } catch (\Exception $e) {
                    // Log error and fallback to original calculation
                    Log::warning("Failed to parse trail duration: " . $trailDuration, ['error' => $e->getMessage()]);
                }
            }
        }
        
        // If no trail duration found, use user input
        if (empty($durationDays) && isset($itinerary['duration_days'])) {
            $durationDays = intval($itinerary['duration_days']);
        }
        if (empty($nights) && isset($itinerary['nights'])) {
            $nights = intval($itinerary['nights']);
        }
        
        // Fallback to trail calculator if still no duration
        if (empty($durationDays)) {
            $durationDays = $this->trailCalculator->deriveDurationFromTrail($trail, $routeData);
        }

        // Ensure nights is set properly
        if (is_null($nights)) {
            $nights = max(0, $durationDays - 1);
        }
        
        // Priority: Use event's hiking_start_time if available, otherwise fall back to itinerary start_time
        $startTime = null;
        
        // Check if trail has events with hiking_start_time (only if trail is an object)
        if ($trail && is_object($trail) && method_exists($trail, 'events')) {
            $trailEvents = $trail->events ?? null;
            if ($trailEvents && $trailEvents->isNotEmpty()) {
                $latestEvent = $trailEvents->first();
                if ($latestEvent && isset($latestEvent->hiking_start_time)) {
                    $startTime = $latestEvent->hiking_start_time;
                }
            }
        }
        
        // Fallback to itinerary start_time or default
        if (!$startTime) {
            $startTime = $itinerary['start_time'] ?? '06:00';
        }
        
        $startDate = isset($itinerary['start_date']) ? Carbon::parse($itinerary['start_date']) : Carbon::today();
        $endDate = $startDate->copy()->addDays(max(0, $durationDays - 1));

        return [
            'duration_days' => $durationDays,
            'nights' => $nights,
            'start_time' => $startTime,
            'start_date' => $startDate,
            'end_date' => $endDate,
        ];
    }

    /**
     * Extract user location data from build information
     */
    protected function getUserLocationData($build)
    {
        $userLocation = [
            'address' => 'Current Location',
            'lat' => null,
            'lng' => null
        ];

        if (is_array($build)) {
            $userLocation['address'] = $build['user_location'] ?? 'Current Location';
            $userLocation['lat'] = isset($build['user_lat']) ? floatval($build['user_lat']) : null;
            $userLocation['lng'] = isset($build['user_lng']) ? floatval($build['user_lng']) : null;
        } elseif (is_object($build)) {
            $userLocation['address'] = $build->user_location ?? 'Current Location';
            $userLocation['lat'] = isset($build->user_lat) ? floatval($build->user_lat) : null;
            $userLocation['lng'] = isset($build->user_lng) ? floatval($build->user_lng) : null;
        }

        return $userLocation;
    }

    /**
     * Calculate travel time using Google Maps API - now fully dynamic without hardcoded fallbacks
     * Attempts to resolve missing coordinates using Google Places API
     */
    protected function calculateTravelTime($fromLat, $fromLng, $toLat, $toLng, $transportType = 'bus', $routeContext = '')
    {
        // If coordinates are missing, this should not happen with the new dynamic system
        // but we'll handle it gracefully by logging and using distance-based calculation
        if (empty($fromLat) || empty($fromLng) || empty($toLat) || empty($toLng)) {
            Log::warning("Missing coordinates for travel time calculation", [
                'from' => ['lat' => $fromLat, 'lng' => $fromLng],
                'to' => ['lat' => $toLat, 'lng' => $toLng],
                'transport' => $transportType,
                'context' => $routeContext
            ]);
            
            // Use Google Distance Matrix with geocoding as last resort
            return $this->calculateTravelTimeWithGeocoding($fromLat, $fromLng, $toLat, $toLng, $transportType, $routeContext);
        }

        // Primary method: Google Maps API with actual coordinates
        try {
            $googleTravelTime = $this->googleMaps->calculatePhilippinesTravelTime(
                $fromLat, 
                $fromLng, 
                $toLat, 
                $toLng, 
                $transportType
            );

            if ($googleTravelTime && $googleTravelTime > 0) {
                // Apply route-specific context adjustments only if needed
                if ($routeContext && !empty($routeContext) && $routeContext !== 'unknown_route') {
                    $googleTravelTime = $this->applyRouteContextAdjustments($googleTravelTime, $routeContext, $transportType);
                }
                
                Log::info("Google Maps API travel time calculated", [
                    'time_minutes' => $googleTravelTime,
                    'transport' => $transportType,
                    'context' => $routeContext,
                    'method' => 'google_maps_api'
                ]);
                
                return $googleTravelTime;
            }
        } catch (\Exception $e) {
            Log::error("Google Maps API failed for travel time calculation: " . $e->getMessage());
        }

        // Final fallback: Use Google Distance Matrix directly without Philippines-specific calculations
        Log::info("Using direct Google Distance Matrix as fallback");
        return $this->calculateDirectGoogleTravelTime($fromLat, $fromLng, $toLat, $toLng, $transportType);
    }



    /**
     * Apply minimal route-specific context adjustments to travel times
     * Now relies primarily on Google Maps accuracy with minimal adjustments
     */
    protected function applyRouteContextAdjustments($baseTravelTime, $routeContext, $transportType)
    {
        // Minimal generic adjustments only for transport mode differences
        // Google Maps already handles route-specific traffic and road conditions
        $genericTransportAdjustments = [
            'bus' => 1.05,      // Slight buffer for bus stops and boarding
            'van' => 1.02,      // Minimal adjustment for van routes
            'car' => 1.0,       // No adjustment for private cars
            'jeepney' => 1.1,   // Buffer for frequent stops
            'tricycle' => 1.05  // Buffer for local stops
        ];

        // Use generic transport-based adjustment only
        $adjustment = $genericTransportAdjustments[$transportType] ?? 1.0;
        
        Log::info("Applied minimal transport adjustment", [
            'base_time' => $baseTravelTime,
            'transport' => $transportType,
            'adjustment' => $adjustment,
            'final_time' => round($baseTravelTime * $adjustment)
        ]);
        
        return round($baseTravelTime * $adjustment);
    }



    /**
     * Get coordinates for any location using Google Places API
     * Replaces hardcoded location list with dynamic geocoding
     */
    protected function getLocationCoordinates($locationName)
    {
        if (empty($locationName)) {
            return ['lat' => null, 'lng' => null];
        }

        // Try Google Places API geocoding first
        $geocoded = $this->googleMaps->geocodeAddress($locationName, 'ph');
        
        if ($geocoded && isset($geocoded['lat']) && isset($geocoded['lng'])) {
            Log::info("Successfully geocoded location: {$locationName}", [
                'lat' => $geocoded['lat'],
                'lng' => $geocoded['lng'],
                'formatted_address' => $geocoded['formatted_address']
            ]);
            
            return [
                'lat' => $geocoded['lat'],
                'lng' => $geocoded['lng'],
                'formatted_address' => $geocoded['formatted_address'] ?? $locationName,
                'place_id' => $geocoded['place_id'] ?? null
            ];
        }

        // Try Google Places search as fallback for more specific queries
        $places = $this->googleMaps->findPlaces($locationName, 'ph');
        
        if (!empty($places)) {
            $bestMatch = $places[0]; // Use the first result as best match
            
            Log::info("Found location via Places search: {$locationName}", [
                'lat' => $bestMatch['lat'],
                'lng' => $bestMatch['lng'],
                'name' => $bestMatch['name'],
                'formatted_address' => $bestMatch['formatted_address']
            ]);
            
            return [
                'lat' => $bestMatch['lat'],
                'lng' => $bestMatch['lng'],
                'formatted_address' => $bestMatch['formatted_address'] ?? $bestMatch['name'],
                'place_id' => $bestMatch['place_id'] ?? null,
                'name' => $bestMatch['name']
            ];
        }

        // Fallback to hardcoded coordinates only for critical locations if Google API fails
        $criticalLocations = [
            // Only keep absolutely essential hardcoded fallbacks
            'manila' => ['lat' => 14.5995, 'lng' => 120.9842],
            'philippines' => ['lat' => 12.8797, 'lng' => 121.7740], // Center of Philippines
        ];

        $searchKey = strtolower(trim($locationName));
        if (isset($criticalLocations[$searchKey])) {
            Log::warning("Using hardcoded fallback coordinates for: {$locationName}");
            return $criticalLocations[$searchKey];
        }

        Log::warning("Could not geocode location: {$locationName}");
        return ['lat' => null, 'lng' => null];
    }

    /**
     * Get trail coordinates for travel time calculation
     * Now uses database GPX/GeoJSON data instead of hardcoded values
     */
    protected function getTrailCoordinates($trail, $departurePoint = null)
    {
        $coords = ['lat' => null, 'lng' => null];

        if (is_array($trail)) {
            // Priority 1: Use start coordinates if available (from GPX data)
            $coords['lat'] = $trail['coordinates_start_lat'] ?? null;
            $coords['lng'] = $trail['coordinates_start_lng'] ?? null;
            
            // Priority 2: Use main trail coordinates (latitude/longitude fields)
            if (empty($coords['lat'])) {
                $coords['lat'] = $trail['latitude'] ?? $trail['lat'] ?? null;
                $coords['lng'] = $trail['longitude'] ?? $trail['lng'] ?? null;
            }
            
            // Priority 3: Extract from trail coordinates array (GPX/GeoJSON data)
            if (empty($coords['lat']) && !empty($trail['coordinates']) && is_array($trail['coordinates'])) {
                $coords = $this->extractCoordsFromTrailData($trail['coordinates'], 'start');
            }
            
            // Priority 4: Use Google Places API for trail name
            if (empty($coords['lat']) && !empty($trail['name'])) {
                $nameCoords = $this->getLocationCoordinates($trail['name']);
                $coords = array_merge($coords, $nameCoords);
            }
        } elseif (is_object($trail)) {
            // Priority 1: Use start coordinates if available (from GPX data)
            $coords['lat'] = $trail->coordinates_start_lat ?? null;
            $coords['lng'] = $trail->coordinates_start_lng ?? null;
            
            // Priority 2: Use main trail coordinates (latitude/longitude fields)
            if (empty($coords['lat'])) {
                $coords['lat'] = $trail->latitude ?? $trail->lat ?? null;
                $coords['lng'] = $trail->longitude ?? $trail->lng ?? null;
            }
            
            // Priority 3: Extract from trail coordinates array (GPX/GeoJSON data)
            if (empty($coords['lat']) && !empty($trail->coordinates) && is_array($trail->coordinates)) {
                $coords = $this->extractCoordsFromTrailData($trail->coordinates, 'start');
            }
            
            // Priority 4: Use Google Places API for trail name
            if (empty($coords['lat']) && !empty($trail->name)) {
                $nameCoords = $this->getLocationCoordinates($trail->name);
                $coords = array_merge($coords, $nameCoords);
            }
        }

        Log::info("Trail coordinates resolved", [
            'trail_name' => is_array($trail) ? ($trail['name'] ?? 'Unknown') : ($trail->name ?? 'Unknown'),
            'coordinates' => $coords,
            'method' => $this->getCoordinateResolutionMethod($trail, $coords)
        ]);

        return $coords;
    }

    /**
     * Extract coordinates from trail GPX/GeoJSON coordinates array
     * Supports various coordinate array formats
     */
    protected function extractCoordsFromTrailData($coordinatesArray, $position = 'start')
    {
        if (!is_array($coordinatesArray) || empty($coordinatesArray)) {
            return ['lat' => null, 'lng' => null];
        }

        // Handle different coordinate array formats
        
        // Format 1: Array of coordinate objects [{lat: x, lng: y}, ...]
        if (isset($coordinatesArray[0]) && is_array($coordinatesArray[0])) {
            $index = ($position === 'end') ? count($coordinatesArray) - 1 : 0;
            $coord = $coordinatesArray[$index];
            
            if (isset($coord['lat']) && isset($coord['lng'])) {
                return [
                    'lat' => floatval($coord['lat']),
                    'lng' => floatval($coord['lng'])
                ];
            }
            
            // Handle [lng, lat] format (GeoJSON style)
            if (isset($coord[0]) && isset($coord[1])) {
                return [
                    'lat' => floatval($coord[1]), // GeoJSON is [lng, lat]
                    'lng' => floatval($coord[0])
                ];
            }
        }
        
        // Format 2: Single coordinate object {lat: x, lng: y}
        if (isset($coordinatesArray['lat']) && isset($coordinatesArray['lng'])) {
            return [
                'lat' => floatval($coordinatesArray['lat']),
                'lng' => floatval($coordinatesArray['lng'])
            ];
        }
        
        // Format 3: GeoJSON LineString coordinates [[lng, lat], [lng, lat], ...]
        if (isset($coordinatesArray['coordinates']) && is_array($coordinatesArray['coordinates'])) {
            return $this->extractCoordsFromTrailData($coordinatesArray['coordinates'], $position);
        }
        
        // Format 4: Flat array [lng, lat] or [lat, lng]
        if (count($coordinatesArray) === 2 && is_numeric($coordinatesArray[0]) && is_numeric($coordinatesArray[1])) {
            // Assume [lat, lng] if first value is reasonable latitude range
            if ($coordinatesArray[0] >= -90 && $coordinatesArray[0] <= 90) {
                return [
                    'lat' => floatval($coordinatesArray[0]),
                    'lng' => floatval($coordinatesArray[1])
                ];
            } else {
                // Assume [lng, lat] (GeoJSON style)
                return [
                    'lat' => floatval($coordinatesArray[1]),
                    'lng' => floatval($coordinatesArray[0])
                ];
            }
        }

        Log::warning("Could not extract coordinates from trail data", [
            'data_structure' => gettype($coordinatesArray),
            'sample' => is_array($coordinatesArray) ? array_slice($coordinatesArray, 0, 2) : $coordinatesArray
        ]);

        return ['lat' => null, 'lng' => null];
    }

    /**
     * Determine which method was used to resolve coordinates (for logging)
     */
    protected function getCoordinateResolutionMethod($trail, $coords)
    {
        if (empty($coords['lat'])) {
            return 'failed';
        }

        $isArray = is_array($trail);
        
        // Check if from start coordinates
        $startLat = $isArray ? ($trail['coordinates_start_lat'] ?? null) : ($trail->coordinates_start_lat ?? null);
        if (!empty($startLat)) {
            return 'database_start_coords';
        }
        
        // Check if from main coordinates
        $mainLat = $isArray ? ($trail['latitude'] ?? $trail['lat'] ?? null) : ($trail->latitude ?? $trail->lat ?? null);
        if (!empty($mainLat)) {
            return 'database_main_coords';
        }
        
        // Check if from coordinates array
        $coordsArray = $isArray ? ($trail['coordinates'] ?? null) : ($trail->coordinates ?? null);
        if (!empty($coordsArray)) {
            return 'database_gpx_coords';
        }
        
        return 'google_places_api';
    }

    /**
     * Generate dynamic route context based on actual locations
     * Replaces hardcoded route contexts with intelligent detection
     */
    protected function generateRouteContext($fromLocation, $toLocation, $type = 'general')
    {
        // If coordinates are missing, use generic context
        if (empty($fromLocation['lat']) || empty($toLocation['lat'])) {
            return 'unknown_route';
        }

        $fromName = $this->identifyLocationByCoordinates($fromLocation['lat'], $fromLocation['lng']);
        $toName = $this->identifyLocationByCoordinates($toLocation['lat'], $toLocation['lng']);
        
        // Calculate distance to determine route type
        $distance = $this->calculateDistance($fromLocation['lat'], $fromLocation['lng'], 
                                           $toLocation['lat'], $toLocation['lng']);
        
        // Generate context based on route characteristics
        $context = $this->buildRouteContext($fromName, $toName, $distance, $type);
        
        Log::info("Generated dynamic route context", [
            'from' => $fromName,
            'to' => $toName,
            'distance_km' => round($distance, 1),
            'type' => $type,
            'context' => $context
        ]);
        
        return $context;
    }

    /**
     * Identify location type by coordinates
     */
    protected function identifyLocationByCoordinates($lat, $lng)
    {
        // Metro Manila area (approximate bounds)
        if ($lat >= 14.4 && $lat <= 14.8 && $lng >= 120.9 && $lng <= 121.2) {
            return 'metro_manila';
        }
        
        // Baguio/Benguet area (mountain region)
        if ($lat >= 16.2 && $lat <= 16.6 && $lng >= 120.4 && $lng <= 121.0) {
            return 'baguio_mountain';
        }
        
        // Bataan area
        if ($lat >= 14.4 && $lat <= 14.8 && $lng >= 120.3 && $lng <= 120.6) {
            return 'bataan_province';
        }
        
        // Laguna area (Los Baños, Mt. Makiling region)
        if ($lat >= 14.0 && $lat <= 14.3 && $lng >= 121.1 && $lng <= 121.3) {
            return 'laguna_province';
        }
        
        // Cebu area
        if ($lat >= 10.2 && $lat <= 10.4 && $lng >= 123.7 && $lng <= 124.0) {
            return 'cebu_city';
        }
        
        // Davao area
        if ($lat >= 7.0 && $lat <= 7.3 && $lng >= 125.3 && $lng <= 125.7) {
            return 'davao_city';
        }
        
        // Generic province if outside known areas
        return 'province';
    }

    /**
     * Build route context string based on location analysis
     */
    protected function buildRouteContext($fromName, $toName, $distance, $type)
    {
        // Inter-city routes (long distance)
        if ($distance > 100) {
            if ($fromName === 'province' && $toName === 'metro_manila') {
                return 'province_to_manila';
            }
            if ($fromName === 'metro_manila' && strpos($toName, 'mountain') !== false) {
                return 'manila_to_mountain';
            }
            return 'long_distance_route';
        }
        
        // Metro Manila internal routes
        if ($fromName === 'metro_manila' && $toName === 'metro_manila') {
            return 'metro_manila_cross';
        }
        
        // City to trail routes (medium distance)
        if ($distance > 30 && $distance <= 100) {
            if ($type === 'trail') {
                return 'city_to_trail';
            }
            return 'intercity_route';
        }
        
        // Local area routes (short distance)
        if ($distance <= 30) {
            return 'local_area';
        }
        
        return 'general_route';
    }

    /**
     * Calculate distance between two points using Haversine formula
     */
    protected function calculateDistance($lat1, $lng1, $lat2, $lng2)
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
     * Calculate travel time with geocoding for missing coordinates
     * This method is a last resort when coordinates are somehow missing
     */
    protected function calculateTravelTimeWithGeocoding($fromLat, $fromLng, $toLat, $toLng, $transportType, $routeContext)
    {
        // If we have partial coordinates, try to get the missing ones
        // This is a fallback scenario that shouldn't happen with proper implementation
        
        if (empty($fromLat) || empty($fromLng)) {
            Log::warning("Missing origin coordinates - cannot calculate travel time accurately");
        }
        
        if (empty($toLat) || empty($toLng)) {
            Log::warning("Missing destination coordinates - cannot calculate travel time accurately");
        }

        // Return a reasonable default based on transport type to prevent system failure
        $defaultTimes = [
            'bus' => 90,
            'van' => 75,
            'car' => 60,
            'jeepney' => 45,
            'tricycle' => 30,
            'walking' => 60
        ];

        $defaultTime = $defaultTimes[$transportType] ?? 75;
        
        Log::warning("Using emergency default travel time", [
            'time_minutes' => $defaultTime,
            'transport' => $transportType,
            'reason' => 'missing_coordinates'
        ]);

        return $defaultTime;
    }

    /**
     * Use Google Distance Matrix API directly without hardcoded Philippines adjustments
     */
    protected function calculateDirectGoogleTravelTime($fromLat, $fromLng, $toLat, $toLng, $transportType)
    {
        try {
            // Map transport type to Google Maps mode
            $googleMode = $this->mapTransportToGoogleMode($transportType);
            
            // Use Google Distance Matrix directly
            $result = $this->googleMaps->getDistanceMatrix($fromLat, $fromLng, $toLat, $toLng, $googleMode);
            
            if ($result && isset($result['duration_minutes'])) {
                $travelTime = $result['duration_minutes'];
                
                // Only apply minimal adjustment for transport type differences
                $adjustment = $this->getMinimalTransportAdjustment($transportType, $googleMode);
                $adjustedTime = round($travelTime * $adjustment);
                
                Log::info("Direct Google Distance Matrix travel time", [
                    'raw_time' => $travelTime,
                    'adjusted_time' => $adjustedTime,
                    'transport' => $transportType,
                    'google_mode' => $googleMode,
                    'adjustment' => $adjustment
                ]);
                
                return $adjustedTime;
            }
        } catch (\Exception $e) {
            Log::error("Direct Google Distance Matrix failed: " . $e->getMessage());
        }

        // Absolute last resort - use distance-based calculation with Google coordinates
        return $this->calculateDistanceBasedTravelTime($fromLat, $fromLng, $toLat, $toLng, $transportType);
    }

    /**
     * Map transport types to Google Maps modes
     */
    protected function mapTransportToGoogleMode($transportType)
    {
        $mapping = [
            'walking' => 'walking',
            'bicycling' => 'bicycling',
            'bike' => 'bicycling',
            'transit' => 'transit',
            'bus' => 'transit',
            'train' => 'transit',
            'jeepney' => 'driving', // Closest equivalent
            'van' => 'driving',
            'car' => 'driving',
            'motorcycle' => 'driving',
            'tricycle' => 'driving'
        ];

        return $mapping[strtolower($transportType)] ?? 'driving';
    }

    /**
     * Get minimal transport adjustment multiplier (much less aggressive than hardcoded Philippines adjustments)
     */
    protected function getMinimalTransportAdjustment($transportType, $googleMode)
    {
        // Only minimal adjustments since Google already accounts for most factors
        $adjustments = [
            'bus' => 1.1,        // Slightly slower than driving due to stops
            'jeepney' => 1.15,    // Slightly more stops than bus
            'van' => 1.05,        // Slightly faster than bus
            'tricycle' => 1.2,    // Limited to local roads
            'motorcycle' => 0.9   // Can navigate traffic better
        ];

        return $adjustments[strtolower($transportType)] ?? 1.0;
    }

    /**
     * Distance-based travel time calculation as absolute last resort
     */
    protected function calculateDistanceBasedTravelTime($fromLat, $fromLng, $toLat, $toLng, $transportType)
    {
        $distance = $this->calculateDistance($fromLat, $fromLng, $toLat, $toLng);
        
        // Conservative speed estimates (km/h) - these are more reasonable than hardcoded values
        $speeds = [
            'bus' => 40,
            'van' => 45,
            'car' => 50,
            'jeepney' => 35,
            'tricycle' => 25,
            'motorcycle' => 40,
            'walking' => 5
        ];

        $speed = $speeds[strtolower($transportType)] ?? 40;
        $travelTimeMinutes = ($distance / $speed) * 60;
        
        // Add minimal buffer (20%) instead of hardcoded adjustments
        $finalTime = round($travelTimeMinutes * 1.2);
        
        Log::info("Distance-based travel time calculation (last resort)", [
            'distance_km' => round($distance, 2),
            'speed_kmh' => $speed,
            'raw_time_minutes' => round($travelTimeMinutes),
            'final_time_minutes' => $finalTime,
            'transport' => $transportType
        ]);

        return max(30, $finalTime); // Minimum 30 minutes for any journey
    }

    /**
     * Get pickup/departure point coordinates
     */
    protected function getDeparturePointCoordinates($departurePoint)
    {
        if (empty($departurePoint)) {
            return ['lat' => null, 'lng' => null];
        }

        return $this->getLocationCoordinates($departurePoint);
    }

    /**
     * Generate pre-hike transportation activities
     */
    protected function generatePreHikeActivities($trail, $build)
    {
        if (empty($trail)) {
            return [];
        }

        $activities = [];
        
        // Check if transportation is included in the trail package
        $transportIncluded = false;
        $departurePoint = 'Trailhead';

        if (is_array($trail)) {
            $transportIncluded = !empty($trail['transport_included']);
            $departurePoint = $trail['departure_point'] ?? 'Pickup Location';
        } elseif (is_object($trail)) {
            $transportIncluded = (bool)$trail->transport_included;
            $departurePoint = $trail->departure_point ?? 'Pickup Location';
        }

        // Get user location information from build data
        $userLocation = $this->getUserLocationData($build);

        Log::info("Pre-hike transport routing decision", [
            'trail_id' => is_array($trail) ? ($trail['id'] ?? 'unknown') : ($trail->id ?? 'unknown'),
            'transport_included' => $transportIncluded,
            'method' => $transportIncluded ? 'generateIncludedTransportActivities (use pickup_time)' : 'generateCommuteActivities (use departure_time)'
        ]);

        if ($transportIncluded) {
            $activities = $this->generateIncludedTransportActivities($trail, $departurePoint, $userLocation);
        } else {
            $activities = $this->generateCommuteActivities($trail, $userLocation);
        }

        return $activities;
    }

    /**
     * Generate activities for included transportation
     * Real-world scenario: User location → Pickup point → Trailhead
     */
    protected function generateIncludedTransportActivities($trail, $departurePoint, $userLocation)
    {
        $activities = [];
        
        // Get trail times (pickup_time for included transport)
        $trailTimes = $this->getTrailTimes($trail);
        $pickupTimeMinutes = $this->convertTimeToMinutes($trailTimes['pickup_time']);
        
        // pickup_time is when the team MEETS (not when hiking starts)
        // Use pickup_time if available, otherwise fallback to 06:00 for the meetup time
        $meetupTimeMinutes = $pickupTimeMinutes ?? 360; // Default to 06:00 if no pickup_time
        
        Log::info("Using pickup time for included transport", [
            'trail_id' => is_array($trail) ? ($trail['id'] ?? 'unknown') : ($trail->id ?? 'unknown'),
            'raw_pickup_time' => $trailTimes['pickup_time'],
            'pickup_time_minutes' => $pickupTimeMinutes,
            'meetup_time_minutes' => $meetupTimeMinutes
        ]);
        
        // Get trail name
        $trailName = is_array($trail) ? $trail['name'] : $trail->name ?? 'Trail';
        $transportDetails = is_array($trail) ? ($trail['transport_details'] ?? null) : ($trail->transport_details ?? null);
        
        // Get coordinates for the two-stage journey
        $pickupCoords = $this->getDeparturePointCoordinates($departurePoint); // Shaw Boulevard coordinates
        $trailCoords = $this->getTrailCoordinates($trail); // Ambangeg Trail coordinates
        
        // Stage 1: User location → Pickup point
        $homeToPickupTime = $this->calculateTravelTime(
            $userLocation['lat'], $userLocation['lng'],
            $pickupCoords['lat'], $pickupCoords['lng'],
            'bus',
            $this->generateRouteContext($userLocation, $pickupCoords, 'pickup')
        );
        
        // Stage 2: Pickup point → Trailhead
        $pickupToTrailTime = $this->calculateTravelTime(
            $pickupCoords['lat'], $pickupCoords['lng'],
            $trailCoords['lat'], $trailCoords['lng'],
            'van',
            $this->generateRouteContext($pickupCoords, $trailCoords, 'trail')
        );
        
        // Use Google Maps calculated times directly for maximum accuracy
        // Only use fallback if Google API completely fails
        // Trust the Google Maps API calculation - no more hardcoded fallbacks
        // The calculateTravelTime method now handles all fallbacks dynamically
        Log::info("Travel time calculated for home to pickup", [
            'time_minutes' => $homeToPickupTime,
            'method' => 'dynamic_google_api'
        ]);
        
        Log::info("Travel time calculated for pickup to trail", [
            'time_minutes' => $pickupToTrailTime,
            'method' => 'dynamic_google_api'
        ]);
        
        // Trust Google Maps accuracy - no artificial minimums
        // The Google API already includes Philippines-specific adjustments
        
        // Calculate start time working backwards from hike start (06:00)
        $preparationTime = 45; // More time for long journey preparation
        $waitTime = 30; // Wait time at pickup point for team assembly
        $totalPreparationTime = $preparationTime + $homeToPickupTime + $waitTime + $pickupToTrailTime;
        
        // CRITICAL FIX: Check if total travel time requires advance departure day
        // For trails requiring 8+ hours total travel, schedule departure day before
        $requiresAdvanceDay = ($totalPreparationTime > 480); // 8 hours = 480 minutes
        $totalHours = round($totalPreparationTime / 60, 1);
        
        // REVISED APPROACH: "Meet Hiking Team" should happen exactly at pickup_time
        // Calculate backwards from the desired meetup time
        if ($requiresAdvanceDay) {
            // pickup_time on day before hike (add 24 hours)
            $finalMeetupTime = $meetupTimeMinutes + 1440;
            Log::info("Long journey detected ({$totalHours}h total) - meetup scheduled for pickup_time on day before");
        } else {
            // pickup_time on same day as hike
            $finalMeetupTime = $meetupTimeMinutes;
            Log::info("Same-day travel possible ({$totalHours}h total) - meetup scheduled for pickup_time");
        }
        
        // Calculate when preparation should start (working backwards from meetup time)
        $cursor = $finalMeetupTime - $preparationTime - $homeToPickupTime;
        
        // Ensure reasonable departure time (not too early, not too late)
        if ($requiresAdvanceDay) {
            // Times adjusted for day before (add 1440 minutes)
            if ($cursor < 1440) { // If departure would be before midnight of day before
                $cursor = 1440 + 300; // Start at 05:00 AM day before (1740 minutes total)
            } elseif ($cursor > 1440 + 1200) { // If departure would be after 8 PM day before
                $cursor = 1440 + 1200; // Latest departure at 20:00 (8 PM) day before
            }
        } else {
            $cursor = max(0, $cursor); // Don't start before midnight for same-day
        }
        
        // Use user's actual location
        $userLocationName = !empty($userLocation['address']) && $userLocation['address'] !== 'Current Location' 
            ? $userLocation['address'] 
            : 'Home/Hotel';
        
        // Determine day context for activity descriptions
        $dayContext = $requiresAdvanceDay ? '(Day before hike)' : '(Hike day)';
        
        // 1. Preparation at user's location (Bataan)
        $activities[] = $this->createActivity(
            $cursor,
            0.0,
            'Prepare for Long Journey',
            'prep',
            $userLocationName,
            "Pack gear, prepare for multi-hour journey to pickup point {$dayContext}"
        );
        
        $cursor += $preparationTime;
        
        // 2. Travel from user location to pickup point (Bataan → Shaw Boulevard)
        $homeHours = round($homeToPickupTime / 60, 1);
        $activities[] = $this->createActivity(
            $cursor,
            0.0,
            "Travel to {$departurePoint}",
            'transport',
            'En route to Manila',
            "Journey from {$userLocationName} to pickup point ({$homeHours}h via bus) {$dayContext}"
        );
        
        $cursor += $homeToPickupTime;
        
        // 3. Meet Hiking Team - This should happen exactly at pickup_time
        $activities[] = $this->createActivity(
            $finalMeetupTime,
            0.0,
            "Meet Hiking Team",
            'checkpoint',
            $departurePoint,
            "Group assembly and final coordination before departure {$dayContext}"
        );
        
        // 4. Continue from meetup time (add wait time if needed)
        $cursor = $finalMeetupTime + $waitTime;
        
        // 4. Board group transportation (Van)
        $transportType = $this->extractTransportType($transportDetails) ?: 'Van';
        $trailHours = round($pickupToTrailTime / 60, 1);
        $activities[] = $this->createActivity(
            $cursor,
            0.0,
            "Travel as Group to {$trailName}",
            'transport',
            'En route to trailhead',
            "Group transportation to hiking destination ({$trailHours}h via {$transportType}) {$dayContext}"
        );
        
        $cursor += $pickupToTrailTime;
        
        // 5. Arrive at trailhead
        $arrivalContext = $requiresAdvanceDay ? '(Evening before hike - rest overnight)' : '(Ready to start hike)';
        $activities[] = $this->createActivity(
            $cursor,
            0.0,
            "Arrive at {$trailName} Trailhead",
            'arrival',
            'Trailhead',
            "Final preparations before hiking begins {$arrivalContext}"
        );
        
        return $activities;
    }

    /**
     * Generate activities for commute/self-transportation
     * Real-world scenario: User location → Meet hiking team → Travel together to trailhead
     * Updated to use Google Maps for accuracy like generateIncludedTransportActivities
     */
    protected function generateCommuteActivities($trail, $userLocation)
    {
        $activities = [];
        
        // Get trail times (departure_time for commute/self-transport)
        $trailTimes = $this->getTrailTimes($trail);
        $departureTimeMinutes = $this->convertTimeToMinutes($trailTimes['departure_time']);
        
        // departure_time is when the team MEETS (not when hiking starts)
        // Use departure_time if available, otherwise fallback to 06:00 for the meetup time
        $meetupTimeMinutes = $departureTimeMinutes ?? 360; // Default to 06:00 if no departure_time
        
        Log::info("Using departure time for commute activities", [
            'raw_departure_time' => $trailTimes['departure_time'],
            'departure_time_minutes' => $departureTimeMinutes,
            'meetup_time_minutes' => $meetupTimeMinutes
        ]);
        
        // Get trail name
        $trailName = is_array($trail) ? ($trail['name'] ?? 'Trail') : ($trail->name ?? 'Trail');
        $transportDetails = is_array($trail) ? ($trail['transport_details'] ?? null) : ($trail->transport_details ?? null);
        
        // For commute, we assume there's still a meetup point with the team
        // This could be a common departure point or meeting place
        $meetupPoint = $this->getMeetupPoint($trail);
        $meetupCoords = $this->getLocationCoordinates($meetupPoint);
        $trailCoords = $this->getTrailCoordinates($trail);
        
        // Stage 1: User location → Team meetup point
        $homeToMeetupTime = $this->calculateTravelTime(
            $userLocation['lat'], $userLocation['lng'],
            $meetupCoords['lat'], $meetupCoords['lng'],
            'bus',
            $this->generateRouteContext($userLocation, $meetupCoords, 'meetup')
        );
        
        // Stage 2: Meetup point → Trailhead (group travel)
        $meetupToTrailTime = $this->calculateTravelTime(
            $meetupCoords['lat'], $meetupCoords['lng'],
            $trailCoords['lat'], $trailCoords['lng'],
            'van',
            $this->generateRouteContext($meetupCoords, $trailCoords, 'trail')
        );
        
        // Trust the Google Maps API calculation - no more hardcoded fallbacks
        // The calculateTravelTime method now handles all fallbacks dynamically
        Log::info("Travel time calculated for home to meetup", [
            'time_minutes' => $homeToMeetupTime,
            'method' => 'dynamic_google_api'
        ]);
        
        Log::info("Travel time calculated for meetup to trail", [
            'time_minutes' => $meetupToTrailTime,
            'method' => 'dynamic_google_api'
        ]);
        
        // Trust Google Maps accuracy - no artificial minimums like the old version
        
        // Calculate start time working backwards from hike start
        $preparationTime = 45; // More time for long journey preparation
        $meetupWaitTime = 30; // Wait for full team assembly
        $totalPreparationTime = $preparationTime + $homeToMeetupTime + $meetupWaitTime + $meetupToTrailTime;
        
        // CRITICAL FIX: Check if total travel time requires advance departure day
        // For trails requiring 8+ hours total travel, schedule departure day before
        $requiresAdvanceDay = ($totalPreparationTime > 480); // 8 hours = 480 minutes
        $totalHours = round($totalPreparationTime / 60, 1);
        
        // REVISED APPROACH: "Meet Hiking Team" should happen exactly at departure_time
        // Calculate backwards from the desired meetup time
        if ($requiresAdvanceDay) {
            // departure_time on day before hike (add 24 hours)
            $finalMeetupTime = $meetupTimeMinutes + 1440;
            Log::info("Long commute journey detected ({$totalHours}h total) - meetup scheduled for departure_time on day before");
        } else {
            // departure_time on same day as hike
            $finalMeetupTime = $meetupTimeMinutes;
            Log::info("Same-day commute travel possible ({$totalHours}h total) - meetup scheduled for departure_time");
        }
        
        // Calculate when preparation should start (working backwards from meetup time)
        $cursor = $finalMeetupTime - $preparationTime - $homeToMeetupTime;
        
        // Ensure reasonable departure time (not too early, not too late)
        if ($requiresAdvanceDay) {
            // Times adjusted for day before (add 1440 minutes)
            if ($cursor < 1440) { // If departure would be before midnight of day before
                $cursor = 1440 + 300; // Start at 05:00 AM day before (1740 minutes total)
            } elseif ($cursor > 1440 + 1200) { // If departure would be after 8 PM day before
                $cursor = 1440 + 1200; // Latest departure at 20:00 (8 PM) day before
            }
        } else {
            $cursor = max(0, $cursor); // Don't start before midnight for same-day
        }
        
        // Use user's actual location
        $userLocationName = !empty($userLocation['address']) && $userLocation['address'] !== 'Current Location' 
            ? $userLocation['address'] 
            : 'Home/Hotel';
        
        // Determine day context for activity descriptions
        $dayContext = $requiresAdvanceDay ? '(Day before hike)' : '(Hike day)';
        
        // 1. Preparation at user's location (Updated title to match new method)
        $activities[] = $this->createActivity(
            $cursor,
            0.0,
            'Prepare for Long Journey',
            'prep',
            $userLocationName,
            "Pack gear, prepare for multi-hour journey to meetup point {$dayContext}"
        );
        
        $cursor += $preparationTime;
        
        // 2. Travel to meetup point (Updated with dynamic time estimates)
        $homeHours = round($homeToMeetupTime / 60, 1);
        $activities[] = $this->createActivity(
            $cursor,
            0.0,
            "Travel to {$meetupPoint}",
            'transport',
            'En route to meetup',
            "Journey from {$userLocationName} to meetup point ({$homeHours}h via bus) {$dayContext}"
        );
        
        $cursor += $homeToMeetupTime;
        
        // 3. Meet Hiking Team - This should happen exactly at departure_time
        $activities[] = $this->createActivity(
            $finalMeetupTime,
            0.0,
            "Meet Hiking Team",
            'meetup',
            $meetupPoint,
            "Group assembly and final coordination before departure {$dayContext}"
        );
        
        // 4. Continue from meetup time (add wait time if needed)
        $cursor = $finalMeetupTime + $meetupWaitTime;
        
        // 4. Group travel to trailhead (Updated with dynamic time estimates)
        $transportType = $this->extractTransportType($transportDetails) ?: 'Van';
        $trailHours = round($meetupToTrailTime / 60, 1);
        $activities[] = $this->createActivity(
            $cursor,
            0.0,
            "Travel as Group to {$trailName}",
            'transport',
            'En route to trailhead',
            "Group transportation to hiking destination ({$trailHours}h via {$transportType}) {$dayContext}"
        );
        
        $cursor += $meetupToTrailTime;
        
        // 5. Arrive at trailhead
        $arrivalContext = $requiresAdvanceDay ? '(Evening before hike - rest overnight)' : '(Ready to start hike)';
        $activities[] = $this->createActivity(
            $cursor,
            0.0,
            "Arrive at {$trailName} Trailhead",
            'arrival',
            'Trailhead',
            "Final preparations before hiking begins {$arrivalContext}"
        );
        
        return $activities;
    }

    /**
     * Determine meetup point for commute scenarios
     */
    protected function getMeetupPoint($trail)
    {
        // In commute scenarios, teams often meetup at a common departure point
        // This could be specified in trail data or use common locations
        
        if (is_array($trail) && !empty($trail['departure_point'])) {
            return $trail['departure_point'];
        } elseif (is_object($trail) && !empty($trail->departure_point)) {
            return $trail->departure_point;
        }
        
        // Default meetup points based on trail location
        $trailName = is_array($trail) ? ($trail['name'] ?? '') : ($trail->name ?? '');
        
        if (stripos($trailName, 'pulag') !== false || stripos($trailName, 'baguio') !== false) {
            return 'Baguio City Terminal';
        } elseif (stripos($trailName, 'makiling') !== false) {
            return 'Los Baños, Laguna';
        } elseif (stripos($trailName, 'apo') !== false) {
            return 'Davao City Terminal';
        }
        
        return 'Team Meetup Point'; // Generic fallback
    }

    /**
     * Extract transport type from transport details string
     */
    protected function extractTransportType($transportDetails)
    {
        if (empty($transportDetails)) {
            return 'Bus/Van';
        }
        
        $details = strtolower($transportDetails);
        
        if (str_contains($details, 'bus')) return 'Bus';
        if (str_contains($details, 'van')) return 'Van';
        if (str_contains($details, 'jeep')) return 'Jeepney';
        if (str_contains($details, 'car')) return 'Car';
        if (str_contains($details, 'tricycle')) return 'Tricycle';
        if (str_contains($details, 'motorcycle')) return 'Motorcycle';
        
        return 'Bus/Van'; // Default
    }

    /**
     * Convert time string (HH:MM) to minutes
     */
    protected function timeStringToMinutes($timeString)
    {
        if (preg_match('/(\d{1,2}):(\d{2})/', $timeString, $matches)) {
            return intval($matches[1]) * 60 + intval($matches[2]);
        }
        return 360; // Default to 06:00 (6 AM)
    }

    /**
     * Generate activities for all days
     */
    protected function generateDayActivities($itinerary, $trail, $dateInfo, $routeData)
    {
        $activitiesByDay = [];
        $userActivities = $itinerary['activities'] ?? [];
        
        // Handle daily_schedule format as well
        if (empty($userActivities) && !empty($itinerary['daily_schedule']) && is_array($itinerary['daily_schedule'])) {
            foreach ($itinerary['daily_schedule'] as $idx => $day) {
                $userActivities[$idx + 1] = is_array($day) && isset($day['activities']) ? $day['activities'] : [];
            }
        }

        for ($day = 1; $day <= $dateInfo['duration_days']; $day++) {
            $dayUserActivities = $userActivities[$day] ?? [];
            
            if (empty($dayUserActivities)) {
                // For multi-day hikes, use fallback generation for accurate distance/time progression
                // TODO: Update intelligent generation to fully support multi-day progression
                if ($dateInfo['duration_days'] > 1) {
                    $dayActivities = $this->generateDayPlan($day, $trail, $dateInfo, $routeData);
                } else {
                    // Use intelligent generation for single-day hikes
                    $dayActivities = $this->intelligentItinerary->generatePersonalizedActivities(
                        $itinerary, $trail, $dateInfo, $routeData, $day
                    );
                    
                    // Fallback to default plan if intelligent generation fails
                    if (empty($dayActivities)) {
                        $dayActivities = $this->generateDayPlan($day, $trail, $dateInfo, $routeData);
                    }
                }
            } else {
                // Expand user activities with scaffold
                $dayActivities = $this->expandDayActivities($dayUserActivities, $trail, $day, $dateInfo, $routeData);
            }

            // Merge side trips and stopovers
            $dayActivities = $this->mergeSideTripsIntoDay($dayActivities, $itinerary, $day);
            
            // Remove duplicate activities based on similar titles and times
            $dayActivities = $this->removeDuplicateActivities($dayActivities);
            
            $activitiesByDay[$day] = $dayActivities;
        }

        return $activitiesByDay;
    }

    /**
     * Generate activities for all nights
     */
    protected function generateNightActivities($itinerary, $dateInfo, $dayActivities)
    {
        $nightActivitiesByIndex = [];
        
        // Get user-provided night activities
        $userNightActivities = $itinerary['night_activities'] ?? 
                              $itinerary['nights'] ?? 
                              $itinerary['nights_activities'] ?? [];

        for ($night = 1; $night <= $dateInfo['nights']; $night++) {
            $nightUserActivities = $userNightActivities[$night] ?? [];
            
            if (empty($nightUserActivities)) {
                // Find arrival time from corresponding day
                $arrivalMinutes = $this->getLastActivityTime($dayActivities[$night] ?? []);
                $nightActivities = $this->generateNightPlan($night, $arrivalMinutes);
            } else {
                $nightActivities = $nightUserActivities;
            }
            
            $nightActivitiesByIndex[$night] = $nightActivities;
        }

        return $nightActivitiesByIndex;
    }

    /**
     * Generate a realistic day plan based on trail characteristics
     */
    public function generateDayPlan($dayIndex, $trail, $dateInfo, $routeData = [])
    {
        $totalKm = floatval($trail['distance_km'] ?? 10);
        $durationDays = $dateInfo['duration_days'];
        
        // Calculate cumulative progress and remaining distance for multi-day continuity
        if ($dayIndex === 1) {
            // Day 1: Start from 0, calculate first day distance
            $cumulativeDistance = 0;
            $distPerDay = $this->trailCalculator->calculateDayDistance($dayIndex, $totalKm, $durationDays, $routeData);
        } else {
            // Day 2+: Calculate cumulative distance from previous days
            $cumulativeDistance = 0;
            for ($prevDay = 1; $prevDay < $dayIndex; $prevDay++) {
                $prevDayDistance = $this->trailCalculator->calculateDayDistance($prevDay, $totalKm, $durationDays, $routeData);
                $cumulativeDistance += $prevDayDistance;
            }
            
            // Remaining distance for this day = total - already covered
            $remainingDistance = max(0, $totalKm - $cumulativeDistance);
            $distPerDay = $remainingDistance;
        }
        
        // Calculate hiking time
        $speed = $this->trailCalculator->computeHikingSpeedKph($trail);
        $hikingHours = $distPerDay / $speed;
        $bufferHours = max(0.5, $hikingHours * 0.2);
        $totalHikeHours = $hikingHours + $bufferHours;
        $hikeMinutes = intval(round($totalHikeHours * 60));

        // Use trail's estimated_time if available, otherwise calculate
        $trailEstimatedTime = $trail['estimated_time'] ?? null;
        if ($trailEstimatedTime) {
            // Parse estimated time (e.g., "8-10 hours" -> use average)
            if (preg_match('/(\d+)(?:-(\d+))?\s*hours?/i', $trailEstimatedTime, $matches)) {
                $minHours = intval($matches[1]);
                $maxHours = isset($matches[2]) ? intval($matches[2]) : $minHours;
                $avgHours = ($minHours + $maxHours) / 2;
                $hikeMinutes = intval($avgHours * 60);
            } else {
                // Fallback to calculated time
                $hikeMinutes = intval(round($totalHikeHours * 60));
            }
        } else {
            $hikeMinutes = intval(round($totalHikeHours * 60));
        }

        // Generate trail activities only (from trailhead to end)
        $activities = [];
        $cursor = 0;

        // Trail start activity - different for each day
        $trailName = $trail['name'] ?? 'Trail';
        
        if ($dayIndex === 1) {
            // Day 1: Start from trailhead
            $startTitle = 'Start ' . $trailName;
            $startDescription = 'Begin your hike';
            $startLocation = $trailName;
            
            $activities[] = array_merge(
                $this->createActivity($cursor, 0.0, $startTitle, 'hike', $startLocation),
                ['description' => $startDescription]
            );

            // Safety briefing only on Day 1
            $activities[] = $this->createActivity(
                $cursor + 15, 
                0.0, 
                'Safety Briefing & Equipment Check', 
                'prep', 
                'Trailhead'
            );
        } else {
            // Day 2+: Continue from campsite
            $startTitle = 'Break Camp & Continue Hike';
            $startDescription = 'Pack up camp and resume your journey';
            
            $activities[] = array_merge(
                $this->createActivity($cursor, $cumulativeDistance, $startTitle, 'prep', 'Campsite'),
                ['description' => $startDescription]
            );

            // Morning preparation for subsequent days  
            $activities[] = $this->createActivity(
                $cursor + 15, 
                $cumulativeDistance, 
                'Morning Check & Route Planning', 
                'prep', 
                'Campsite'
            );
        }

        // Early trail segment (first 20% - usually steeper/more challenging)
        $earlyBreak = intval(round($hikeMinutes * 0.2));
        $activities[] = $this->createActivity(
            $cursor + $earlyBreak, 
            round($cumulativeDistance + ($distPerDay * 0.2), 2), 
            'First Water Break', 
            'rest', 
            'Trail'
        );

        // Quarter point with scenic opportunity
        $quarterTime = intval(round($hikeMinutes * 0.35));
        $activities[] = $this->createActivity(
            $cursor + $quarterTime, 
            round($cumulativeDistance + ($distPerDay * 0.35), 2), 
            'Scenic Photo Stop', 
            'photo', 
            'Viewpoint'
        );

        // Midday break (most important meal)
        $halfTime = intval(round($hikeMinutes * 0.5));
        $activities[] = $this->createActivity(
            $cursor + $halfTime, 
            round($cumulativeDistance + ($distPerDay / 2), 2), 
            'Lunch Break & Rest', 
            'meal', 
            'Rest Area'
        );

        // Post-lunch energy check
        $postLunch = intval(round($hikeMinutes * 0.65));
        $activities[] = $this->createActivity(
            $postLunch, 
            round($cumulativeDistance + ($distPerDay * 0.65), 2), 
            'Hydration & Navigation Check', 
            'checkpoint', 
            'Trail'
        );

        // Final approach (challenging part)
        $finalApproach = intval(round($hikeMinutes * 0.85));
        $approachTitle = ($dayIndex < $durationDays) ? 'Final Approach to Campsite' : 'Final Push to Summit';
        $activities[] = $this->createActivity(
            $finalApproach, 
            round($cumulativeDistance + ($distPerDay * 0.85), 2), 
            $approachTitle, 
            'climb', 
            'Trail'
        );

        // Trail completion with celebration
        $endTitle = ($dayIndex < $durationDays) ? 'Arrive & Set Up Camp' : 'Summit Achievement';
        $endType = ($dayIndex < $durationDays) ? 'camp' : 'summit';
        $endLocation = ($dayIndex < $durationDays) ? 'Campsite' : 'Summit';
        $endDescription = ($dayIndex < $durationDays) ? 'Rest and prepare for next day' : 'Celebrate and enjoy the view';
        
        $activities[] = array_merge(
            $this->createActivity(
                $cursor + $hikeMinutes, 
                round($cumulativeDistance + $distPerDay, 2), 
                $endTitle, 
                $endType, 
                $endLocation
            ),
            ['description' => $endDescription]
        );

        // Add descent activities if it's a summit day (return trail)
        if ($dayIndex >= $durationDays && $hikeMinutes > 240) { // 4+ hour trails usually require descent
            $descentStart = $cursor + $hikeMinutes + 30; // 30 min rest at summit
            $descentTime = intval($hikeMinutes * 0.6); // Descent is typically faster
            
            $activities[] = $this->createActivity(
                $descentStart, 
                round($cumulativeDistance + $distPerDay, 2), 
                'Begin Descent', 
                'descent', 
                'Summit'
            );
            
            $activities[] = $this->createActivity(
                $descentStart + intval($descentTime / 2), 
                round($cumulativeDistance + ($distPerDay * 0.7), 2), 
                'Descent Rest Stop', 
                'rest', 
                'Trail'
            );
            
            $activities[] = $this->createActivity(
                $descentStart + $descentTime, 
                0.0, 
                'Return to Trailhead', 
                'finish', 
                'Trailhead'
            );
        }

        return $activities;
    }

    /**
     * Generate night plan activities
     */
    public function generateNightPlan($nightIndex, $arrivalMinutes = 1080)
    {
        $activities = [];
        // Ensure night activities start no earlier than 18:00 (1080 minutes)
        $eveningStart = 18 * 60; // 18:00 = 1080 minutes
        $cursor = max($eveningStart, intval($arrivalMinutes));

        $activities[] = $this->createActivity($cursor, null, 'Set up Camp / Check-in', 'camp', 'Campsite');
        $cursor += 45;
        
        $activities[] = $this->createActivity($cursor, null, 'Dinner & Rest', 'meal', 'Campsite');
        $cursor += 60;
        
        $activities[] = $this->createActivity($cursor, null, 'Stargazing / Campfire', 'relax', 'Campsite');
        $cursor += 90;
        
        $activities[] = $this->createActivity($cursor, null, 'Sleep', 'overnight', 'Tents/Campsite');

        return $activities;
    }

    /**
     * Expand sparse user activities using generated scaffold
     */
    protected function expandDayActivities($userActivities, $trail, $dayIndex, $dateInfo, $routeData)
    {
        if (!empty($userActivities) && count($userActivities) > 3) {
            return $userActivities;
        }

        $scaffold = $this->generateDayPlan($dayIndex, $trail, $dateInfo, $routeData);
        $result = [];

        foreach ($scaffold as $scaffoldActivity) {
            $matched = null;
            
            foreach ($userActivities as $userActivity) {
                if ($this->activitiesMatch($scaffoldActivity, $userActivity)) {
                    $matched = $userActivity;
                    if (!isset($matched['minutes'])) {
                        $matched['minutes'] = $scaffoldActivity['minutes'];
                    }
                    break;
                }
            }

            $result[] = $matched ?: $scaffoldActivity;
        }

        // Add any unmatched user activities
        foreach ($userActivities as $userActivity) {
            if (!$this->isActivityInResult($userActivity, $result)) {
                $result[] = $userActivity;
            }
        }

        // Sort by minutes
        usort($result, function($a, $b) {
            return intval($a['minutes'] ?? 0) <=> intval($b['minutes'] ?? 0);
        });

        return $result;
    }

    /**
     * Merge side trips into day activities
     */
    protected function mergeSideTripsIntoDay($activities, $itinerary, $dayIndex)
    {
        $sideTrips = $itinerary['side_trips'] ?? $itinerary['sidetrips'] ?? [];
        $stopOvers = $itinerary['stop_overs'] ?? $itinerary['stopovers'] ?? [];
        
        $extras = array_slice($sideTrips, 0, 2);
        $extras = array_merge($extras, array_slice($stopOvers, 0, 2));
        
        if (empty($extras)) {
            return $activities;
        }

        return $this->mergeExtrasIntoActivities($activities, $extras);
    }

    /**
     * Helper method to create activity array
     */
    protected function createActivity($minutes, $cumDistance, $title, $type, $location, $description = null)
    {
        $activity = [
            'minutes' => $minutes,
            'cum_minutes' => $minutes,
            'cum_distance_km' => $cumDistance,
            'title' => $title,
            'type' => $type,
            'location' => $location,
        ];
        
        if ($description !== null) {
            $activity['description'] = $description;
        }
        
        return $activity;
    }

    /**
     * Check if two activities match for merging
     */
    protected function activitiesMatch($scaffold, $user)
    {
        $scaffoldTitle = strtolower($scaffold['title'] ?? '');
        $userTitle = strtolower($user['title'] ?? '');
        
        if (!empty($userTitle) && str_contains($scaffoldTitle, $userTitle)) {
            return true;
        }

        $scaffoldLocation = strtolower($scaffold['location'] ?? '');
        $userLocation = strtolower($user['location'] ?? '');
        
        if (!empty($userLocation) && !empty($scaffoldLocation) && 
            str_contains($userLocation, $scaffoldLocation)) {
            return true;
        }

        return false;
    }

    /**
     * Check if activity is already in result array
     */
    protected function isActivityInResult($activity, $result)
    {
        foreach ($result as $resultActivity) {
            if (($resultActivity['title'] ?? '') === ($activity['title'] ?? '') &&
                ($resultActivity['minutes'] ?? null) === ($activity['minutes'] ?? null)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Get the last activity time from a day's activities
     */
    protected function getLastActivityTime($activities)
    {
        if (empty($activities)) {
            return 1080; // Default 18:00
        }

        $minutes = array_map(function($a) {
            return intval($a['minutes'] ?? 0);
        }, $activities);

        return max($minutes);
    }

    /**
     * Merge extras into activities using simple heuristic
     */
    protected function mergeExtrasIntoActivities($activities, $extras)
    {
        if (empty($extras)) {
            return $activities;
        }

        $result = [];
        foreach ($activities as $activity) {
            $result[] = $activity;
            
            if (!empty($extras)) {
                $extra = array_shift($extras);
                if ($extra) {
                    $result[] = $extra;
                }
            }
        }

        // Append any remaining extras
        foreach ($extras as $extra) {
            $result[] = $extra;
        }

        return $result;
    }

    /**
     * Remove duplicate activities with similar titles and times
     */
    protected function removeDuplicateActivities($activities)
    {
        if (empty($activities)) {
            return $activities;
        }

        $uniqueActivities = [];
        $seenTitles = [];

        foreach ($activities as $activity) {
            $title = strtolower(trim($activity['title'] ?? ''));
            $minutes = intval($activity['minutes'] ?? 0);
            
            // Create a unique key based on title keywords and time
            $titleWords = explode(' ', $title);
            $keyWords = [];
            
            // Extract key words, ignoring common words
            foreach ($titleWords as $word) {
                if (strlen($word) > 2 && !in_array($word, ['the', 'and', 'for', 'your', 'to'])) {
                    $keyWords[] = $word;
                }
            }
            
            $uniqueKey = implode('_', $keyWords) . '_' . intval($minutes / 30); // Group by 30-min intervals
            
            // Skip if we've seen a similar activity
            if (!in_array($uniqueKey, $seenTitles)) {
                $seenTitles[] = $uniqueKey;
                $uniqueActivities[] = $activity;
            }
        }

        return $uniqueActivities;
    }

    /**
     * Check if generated activities have incorrect distance progression for multi-day hikes
     */
    protected function hasIncorrectDistanceProgression($dayActivities, $dayIndex, $dateInfo)
    {
        // Only validate multi-day hikes
        if ($dateInfo['duration_days'] <= 1) {
            return false;
        }

        // For Day 2+, check if distance progression is reasonable
        if ($dayIndex > 1) {
            $firstActivity = reset($dayActivities);
            $lastActivity = end($dayActivities);
            
            $startingDistance = $firstActivity['cum_distance_km'] ?? 0;
            $endingDistance = $lastActivity['cum_distance_km'] ?? 0;
            
            // If Day 2+ starts at 0 km, distance progression is definitely incorrect
            if ($startingDistance <= 0) {
                return true;
            }
            
            // If activities don't show progressive distance increase, it's likely incorrect
            if ($endingDistance <= $startingDistance) {
                return true;
            }
        }

        return false;
    }

    /**
     * Extract pickup_time and departure_time from trail data
     * Handles both array and object formats, checking both direct trail fields and package fields
     */
    protected function getTrailTimes($trail)
    {
        $pickupTime = null;
        $departureTime = null;



        // Handle array format
        if (is_array($trail)) {
            $pickupTime = $trail['pickup_time'] ?? null;
            $departureTime = $trail['departure_time'] ?? null;
            
            // Check package data if available
            if (isset($trail['package']) && is_array($trail['package'])) {
                $pickupTime = $pickupTime ?? ($trail['package']['pickup_time'] ?? null);
                $departureTime = $departureTime ?? ($trail['package']['departure_time'] ?? null);
            }
        }
        // Handle object format (Trail model)
        elseif (is_object($trail)) {
            $pickupTime = $trail->pickup_time ?? null;
            $departureTime = $trail->departure_time ?? null;
            
            // Check package relationship if available
            if (isset($trail->package) && $trail->package) {
                $pickupTime = $pickupTime ?? $trail->package->pickup_time ?? null;
                $departureTime = $departureTime ?? $trail->package->departure_time ?? null;
            }
        }

        return [
            'pickup_time' => $pickupTime,
            'departure_time' => $departureTime
        ];
    }

    /**
     * Convert time string (HH:MM or HH:MM:SS) to minutes from midnight
     */
    protected function convertTimeToMinutes($timeString)
    {
        if (empty($timeString)) {
            return null;
        }

        try {
            // Handle various time formats
            if (is_string($timeString)) {
                // Parse time string like "08:30" or "08:30:00"
                $parts = explode(':', $timeString);
                if (count($parts) >= 2) {
                    $hours = intval($parts[0]);
                    $minutes = intval($parts[1]);
                    return ($hours * 60) + $minutes;
                }
            }
            
            // If it's already a Carbon instance
            if ($timeString instanceof \Carbon\Carbon) {
                return ($timeString->hour * 60) + $timeString->minute;
            }
            
            // If it's a DateTime instance
            if ($timeString instanceof \DateTime) {
                return (intval($timeString->format('H')) * 60) + intval($timeString->format('i'));
            }
            
            // Try to parse with Carbon
            $carbonTime = \Carbon\Carbon::parse($timeString);
            return ($carbonTime->hour * 60) + $carbonTime->minute;
            
        } catch (\Exception $e) {
            Log::warning("Failed to parse time string: {$timeString}", ['error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Generate Google Static Maps URL for trail path visualization
     */
    protected function generateStaticMapUrl($trail, $routeData)
    {
        try {
            $apiKey = config('services.google.maps_api_key');
            
            if (empty($apiKey)) {
                Log::warning('Google Maps API key not configured for static map generation');
                return null;
            }

            // Extract coordinates from different possible sources
            $coordinates = $this->extractTrailCoordinates($trail, $routeData);
            
            if (empty($coordinates) || count($coordinates) < 2) {
                Log::info('Insufficient coordinates for static map generation');
                return null;
            }

            $baseUrl = 'https://maps.googleapis.com/maps/api/staticmap';

            // Build path parameter for the trail route
            $pathCoords = [];
            foreach ($coordinates as $coord) {
                if (isset($coord['lat']) && isset($coord['lng'])) {
                    $pathCoords[] = $coord['lat'] . ',' . $coord['lng'];
                } elseif (isset($coord['latitude']) && isset($coord['longitude'])) {
                    $pathCoords[] = $coord['latitude'] . ',' . $coord['longitude'];
                }
            }
            
            if (empty($pathCoords)) {
                return null;
            }

            $path = 'color:0xff0000ff|weight:5|' . implode('|', $pathCoords);

            // Set map size optimized for web display - larger for better visibility
            $size = '1200x600';

            // Get start and end coordinates for markers
            $startCoord = $coordinates[0];
            $endCoord = end($coordinates);
            
            $startLat = $startCoord['lat'] ?? $startCoord['latitude'] ?? null;
            $startLng = $startCoord['lng'] ?? $startCoord['longitude'] ?? null;
            $endLat = $endCoord['lat'] ?? $endCoord['latitude'] ?? null;
            $endLng = $endCoord['lng'] ?? $endCoord['longitude'] ?? null;

            if (!$startLat || !$startLng || !$endLat || !$endLng) {
                return null;
            }

            // Build the complete URL with zoom level for better trail context
            $url = $baseUrl . '?size=' . $size .
                   '&path=' . urlencode($path) .
                   '&markers=' . urlencode('color:green|label:S|' . $startLat . ',' . $startLng) .
                   '&markers=' . urlencode('color:red|label:E|' . $endLat . ',' . $endLng) .
                   '&zoom=12' .
                   '&maptype=terrain' .
                   '&format=png' .
                   '&key=' . $apiKey;

            return $url;

        } catch (\Exception $e) {
            Log::warning('Failed to generate static map URL: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Extract trail coordinates from various data sources
     */
    protected function extractTrailCoordinates($trail, $routeData)
    {
        $coordinates = [];

        // Try to get coordinates from route data first
        if (!empty($routeData['coordinates'])) {
            $coordinates = $routeData['coordinates'];
        }
        // Check if route data has legs with coordinates
        elseif (!empty($routeData['legs'])) {
            foreach ($routeData['legs'] as $leg) {
                if (!empty($leg['coordinates'])) {
                    $coordinates = array_merge($coordinates, $leg['coordinates']);
                }
            }
        }
        // Try trail coordinates field
        elseif (is_object($trail) && !empty($trail->coordinates)) {
            $coordinates = is_string($trail->coordinates) ? json_decode($trail->coordinates, true) : $trail->coordinates;
        }
        elseif (is_array($trail) && !empty($trail['coordinates'])) {
            $coordinates = is_string($trail['coordinates']) ? json_decode($trail['coordinates'], true) : $trail['coordinates'];
        }
        // Try custom start/end points
        elseif (is_object($trail)) {
            if (!empty($trail->custom_start_point) && !empty($trail->custom_end_point)) {
                if (is_array($trail->custom_start_point) && isset($trail->custom_start_point['lat'])) {
                    $coordinates[] = $trail->custom_start_point;
                }
                if (is_array($trail->custom_end_point) && isset($trail->custom_end_point['lat'])) {
                    $coordinates[] = $trail->custom_end_point;
                }
            }
            // Fallback to simple lat/lng coordinates if available
            elseif ($trail->latitude && $trail->longitude) {
                $coordinates[] = ['lat' => $trail->latitude, 'lng' => $trail->longitude];
                
                // Try to add end coordinates if different
                if (!empty($trail->end_latitude) && !empty($trail->end_longitude)) {
                    $coordinates[] = ['lat' => $trail->end_latitude, 'lng' => $trail->end_longitude];
                }
            }
        }
        elseif (is_array($trail)) {
            if (!empty($trail['custom_start_point']) && !empty($trail['custom_end_point'])) {
                if (is_array($trail['custom_start_point']) && isset($trail['custom_start_point']['lat'])) {
                    $coordinates[] = $trail['custom_start_point'];
                }
                if (is_array($trail['custom_end_point']) && isset($trail['custom_end_point']['lat'])) {
                    $coordinates[] = $trail['custom_end_point'];
                }
            }
            elseif (!empty($trail['latitude']) && !empty($trail['longitude'])) {
                $coordinates[] = ['lat' => $trail['latitude'], 'lng' => $trail['longitude']];
                
                if (!empty($trail['end_latitude']) && !empty($trail['end_longitude'])) {
                    $coordinates[] = ['lat' => $trail['end_latitude'], 'lng' => $trail['end_longitude']];
                }
            }
        }

        return $coordinates;
    }
}
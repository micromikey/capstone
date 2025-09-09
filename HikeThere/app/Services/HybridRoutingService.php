<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class HybridRoutingService
{
    private GoogleDirectionsService $googleService;

    private OpenRouteService $orsService;

    public function __construct(
        GoogleDirectionsService $googleService,
        OpenRouteService $orsService
    ) {
        $this->googleService = $googleService;
        $this->orsService = $orsService;
    }

    /**
     * Get the best route using hybrid approach
     */
    public function getBestRoute(
        string $origin,
        string $destination,
        array $waypoints = [],
        string $transportation = 'driving',
        ?string $trailName = null
    ): ?array {
        Log::info('Starting hybrid routing', [
            'origin' => $origin,
            'destination' => $destination,
            'transportation' => $transportation,
            'trail' => $trailName,
        ]);

        // Strategy 1: For transit/commute, always use Google Maps
        if (strtolower($transportation) === 'commute') {
            return $this->getTransitRoute($origin, $destination, $waypoints);
        }

        // Strategy 2: For hiking/trails, prioritize ORS
        if ($this->isHikingRoute($trailName, $destination)) {
            return $this->getHikingRoute($origin, $destination, $waypoints);
        }

        // Strategy 3: For driving, use both and compare
        return $this->getDrivingRoute($origin, $destination, $waypoints);
    }

    /**
     * Get transit route using Google Maps (best for public transport)
     */
    private function getTransitRoute(string $origin, string $destination, array $waypoints = []): ?array
    {
        Log::info('Using Google Maps for transit routing');

        // Try transit mode first with default parameters
        $googleRoute = $this->googleService->getDirections($origin, $destination, $waypoints, 'transit');

        if ($googleRoute && $this->hasTransitSteps($googleRoute)) {
            $googleRoute['routing_strategy'] = 'google_transit';
            $googleRoute['primary_provider'] = 'google';
            Log::info('Transit route found with default parameters');

            return $googleRoute;
        }

        // If no transit found, try with different arrival times (morning, afternoon, evening)
        $arrivalTimes = ['08:00', '12:00', '18:00'];

        foreach ($arrivalTimes as $arrivalTime) {
            Log::info("Retrying transit routing with arrival time: {$arrivalTime}");

            // Try with specific arrival time
            $googleRoute = $this->googleService->getDirections($origin, $destination, $waypoints, 'transit', $arrivalTime);

            if ($googleRoute && $this->hasTransitSteps($googleRoute)) {
                $googleRoute['routing_strategy'] = 'google_transit_retry';
                $googleRoute['primary_provider'] = 'google';
                $googleRoute['retry_arrival_time'] = $arrivalTime;
                Log::info("Transit route found with arrival time: {$arrivalTime}");

                return $googleRoute;
            }
        }

        // If still no transit, try with different departure times
        $departureTimes = ['06:00', '09:00', '14:00'];

        foreach ($departureTimes as $departureTime) {
            Log::info("Retrying transit routing with departure time: {$departureTime}");

            // Try with specific departure time
            $googleRoute = $this->googleService->getDirections($origin, $destination, $waypoints, 'transit', null, $departureTime);

            if ($googleRoute && $this->hasTransitSteps($googleRoute)) {
                $googleRoute['routing_strategy'] = 'google_transit_retry';
                $googleRoute['primary_provider'] = 'google';
                $googleRoute['retry_departure_time'] = $departureTime;
                Log::info("Transit route found with departure time: {$departureTime}");

                return $googleRoute;
            }
        }

        // Final fallback to driving if transit not available
        Log::info('All transit attempts failed, falling back to driving');
        $fallbackRoute = $this->googleService->getDirections($origin, $destination, $waypoints, 'driving');

        if ($fallbackRoute) {
            $fallbackRoute['routing_strategy'] = 'google_driving_fallback';
            $fallbackRoute['primary_provider'] = 'google';
            $fallbackRoute['fallback_reason'] = 'Transit not available for this route after multiple attempts';
        }

        return $fallbackRoute;
    }

    /**
     * Get hiking route prioritizing ORS
     */
    private function getHikingRoute(string $origin, string $destination, array $waypoints = []): ?array
    {
        Log::info('Using ORS for hiking routing');

        // Try ORS hiking profile first
        if ($this->orsService->isAvailable()) {
            $orsRoute = $this->orsService->getHikingDirections($origin, $destination, $waypoints);

            if ($orsRoute) {
                $orsRoute['routing_strategy'] = 'ors_hiking';
                $orsRoute['primary_provider'] = 'openrouteservice';

                // Get Google route for comparison/backup
                $googleRoute = $this->googleService->getDirections($origin, $destination, $waypoints, 'driving');
                if ($googleRoute) {
                    $orsRoute['google_backup'] = [
                        'total_distance' => $googleRoute['total_distance'] ?? null,
                        'total_duration' => $googleRoute['total_duration'] ?? null,
                    ];
                }

                return $orsRoute;
            }
        }

        // Fallback to Google driving
        Log::info('ORS hiking not available, falling back to Google driving');
        $fallbackRoute = $this->googleService->getDirections($origin, $destination, $waypoints, 'driving');

        if ($fallbackRoute) {
            $fallbackRoute['routing_strategy'] = 'google_driving_fallback';
            $fallbackRoute['primary_provider'] = 'google';
            $fallbackRoute['fallback_reason'] = 'ORS hiking not available';
        }

        return $fallbackRoute;
    }

    /**
     * Get driving route using both APIs and choose the best
     */
    private function getDrivingRoute(string $origin, string $destination, array $waypoints = []): ?array
    {
        Log::info('Using hybrid approach for driving routing');

        $routes = [];

        // Get Google route
        $googleRoute = $this->googleService->getDirections($origin, $destination, $waypoints, 'driving');
        if ($googleRoute) {
            $googleRoute['provider'] = 'google';
            $routes['google'] = $googleRoute;
        }

        // Get ORS route
        if ($this->orsService->isAvailable()) {
            $orsRoute = $this->orsService->getDirections($origin, $destination, $waypoints, 'driving-car');
            if ($orsRoute) {
                $orsRoute['provider'] = 'openrouteservice';
                $routes['ors'] = $orsRoute;
            }
        }

        // Choose the best route
        $bestRoute = $this->chooseBestRoute($routes);

        if ($bestRoute) {
            $bestRoute['routing_strategy'] = 'hybrid_driving';
            $bestRoute['available_providers'] = array_keys($routes);

            // Add comparison data
            if (count($routes) > 1) {
                $bestRoute['route_comparison'] = $this->compareRoutes($routes);
            }
        }

        return $bestRoute;
    }

    /**
     * Determine if this is a hiking route
     */
    private function isHikingRoute(?string $trailName, string $destination): bool
    {
        if (! $trailName) {
            return false;
        }

        // Check for common hiking/mountain keywords
        $hikingKeywords = [
            'mt.', 'mount', 'mountain', 'trail', 'peak', 'summit', 'hike',
            'trek', 'falls', 'waterfall', 'volcano', 'ridge', 'camp',
        ];

        $searchText = strtolower($trailName.' '.$destination);

        foreach ($hikingKeywords as $keyword) {
            if (strpos($searchText, $keyword) !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if route has transit steps
     */
    private function hasTransitSteps(array $route): bool
    {
        foreach ($route['legs'] ?? [] as $leg) {
            foreach ($leg['steps'] ?? [] as $step) {
                if (($step['travel_mode'] ?? '') === 'TRANSIT') {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Choose the best route from available options
     */
    private function chooseBestRoute(array $routes): ?array
    {
        if (empty($routes)) {
            return null;
        }

        if (count($routes) === 1) {
            return array_values($routes)[0];
        }

        // Scoring system to choose best route
        $scores = [];

        foreach ($routes as $provider => $route) {
            $score = 0;

            // Prefer shorter duration (40% weight)
            $duration = $route['total_duration_seconds'] ?? PHP_INT_MAX;
            $score += (1 / max($duration, 1)) * 1000000 * 0.4;

            // Prefer shorter distance (30% weight)
            $distance = $this->extractDistanceValue($route['total_distance'] ?? '999 km');
            $score += (1 / max($distance, 1)) * 1000 * 0.3;

            // Provider reliability bonus (30% weight)
            if ($provider === 'google') {
                $score += 100 * 0.3; // Google is generally more reliable for driving
            } elseif ($provider === 'ors') {
                $score += 80 * 0.3; // ORS is good but less traffic-aware
            }

            // Bonus for having elevation data (hiking routes)
            if (isset($route['elevation_profile'])) {
                $score += 20;
            }

            $scores[$provider] = $score;
        }

        // Return the highest scoring route
        $bestProvider = array_key_first(array_filter($scores, fn ($score) => $score === max($scores)));
        $bestRoute = $routes[$bestProvider];
        $bestRoute['primary_provider'] = $bestProvider;
        $bestRoute['route_score'] = $scores[$bestProvider];

        Log::info('Route selection completed', [
            'chosen_provider' => $bestProvider,
            'scores' => $scores,
        ]);

        return $bestRoute;
    }

    /**
     * Compare routes from different providers
     */
    private function compareRoutes(array $routes): array
    {
        $comparison = [];

        foreach ($routes as $provider => $route) {
            $comparison[$provider] = [
                'distance' => $route['total_distance'] ?? 'unknown',
                'duration' => $route['total_duration'] ?? 'unknown',
                'has_elevation' => isset($route['elevation_profile']),
                'has_transit' => $this->hasTransitSteps($route),
                'legs_count' => count($route['legs'] ?? []),
            ];
        }

        return $comparison;
    }

    /**
     * Extract numeric distance value from text
     */
    private function extractDistanceValue(string $distanceText): float
    {
        preg_match('/(\d+\.?\d*)\s*(km|m)/i', $distanceText, $matches);

        if (! empty($matches)) {
            $value = (float) $matches[1];
            $unit = strtolower($matches[2]);

            // Convert to kilometers
            return $unit === 'm' ? $value / 1000 : $value;
        }

        return 999; // Default high value if parsing fails
    }

    /**
     * Get route with enhanced trail information
     */
    public function getEnhancedTrailRoute(
        string $origin,
        string $destination,
        array $waypoints = [],
        ?string $trailName = null
    ): ?array {
        $route = $this->getBestRoute($origin, $destination, $waypoints, 'driving', $trailName);

        if (! $route) {
            return null;
        }

        // Enhance with additional trail data if using ORS
        if (($route['primary_provider'] ?? '') === 'openrouteservice') {
            $route = $this->enhanceWithTrailData($route, $trailName);
        }

        return $route;
    }

    /**
     * Enhance route with additional trail-specific data
     */
    private function enhanceWithTrailData(array $route, ?string $trailName): array
    {
        if (! $trailName) {
            return $route;
        }

        // Add trail-specific enhancements
        $route['trail_enhancements'] = [
            'trail_name' => $trailName,
            'estimated_hiking_time' => $this->estimateHikingTime($route),
            'difficulty_assessment' => $this->assessTrailDifficulty($route),
            'recommended_gear' => $this->getRecommendedGear($route),
            'safety_notes' => $this->getSafetyNotes($route),
        ];

        return $route;
    }

    /**
     * Estimate hiking time based on route data
     */
    private function estimateHikingTime(array $route): string
    {
        $distance = $this->extractDistanceValue($route['total_distance'] ?? '0 km');
        $elevationGain = $route['elevation_profile']['total_ascent'] ?? 0;

        // Naismith's rule: 1 hour per 5km + 1 hour per 600m elevation gain
        $timeHours = ($distance / 5) + ($elevationGain / 600);

        // Add buffer for breaks and photos
        $timeHours *= 1.3;

        $hours = floor($timeHours);
        $minutes = floor(($timeHours - $hours) * 60);

        return $hours > 0 ? "{$hours}h {$minutes}m" : "{$minutes}m";
    }

    /**
     * Assess trail difficulty
     */
    private function assessTrailDifficulty(array $route): string
    {
        $distance = $this->extractDistanceValue($route['total_distance'] ?? '0 km');
        $elevationGain = $route['elevation_profile']['total_ascent'] ?? 0;

        $score = 0;

        // Distance factor
        if ($distance > 15) {
            $score += 3;
        } elseif ($distance > 8) {
            $score += 2;
        } elseif ($distance > 4) {
            $score += 1;
        }

        // Elevation factor
        if ($elevationGain > 1000) {
            $score += 3;
        } elseif ($elevationGain > 500) {
            $score += 2;
        } elseif ($elevationGain > 200) {
            $score += 1;
        }

        return match (true) {
            $score >= 5 => 'Expert',
            $score >= 3 => 'Hard',
            $score >= 2 => 'Moderate',
            default => 'Easy',
        };
    }

    /**
     * Get recommended gear based on route
     */
    private function getRecommendedGear(array $route): array
    {
        $gear = ['Water', 'First Aid Kit', 'Map/GPS'];

        $elevationGain = $route['elevation_profile']['total_ascent'] ?? 0;
        $distance = $this->extractDistanceValue($route['total_distance'] ?? '0 km');

        if ($elevationGain > 500) {
            $gear[] = 'Trekking Poles';
        }

        if ($distance > 10) {
            $gear[] = 'Extra Food';
            $gear[] = 'Headlamp';
        }

        if ($elevationGain > 1000) {
            $gear[] = 'Emergency Shelter';
            $gear[] = 'Warm Clothing';
        }

        return $gear;
    }

    /**
     * Get safety notes based on route
     */
    private function getSafetyNotes(array $route): array
    {
        $notes = [];

        $elevationGain = $route['elevation_profile']['total_ascent'] ?? 0;
        $distance = $this->extractDistanceValue($route['total_distance'] ?? '0 km');

        if ($elevationGain > 800) {
            $notes[] = 'High elevation gain - pace yourself and take breaks';
        }

        if ($distance > 12) {
            $notes[] = 'Long distance - start early and inform someone of your plans';
        }

        $notes[] = 'Check weather conditions before departure';
        $notes[] = 'Carry enough water and snacks';

        return $notes;
    }
}

<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OpenRouteService
{
    private string $apiKey;

    private string $baseUrl = 'https://api.openrouteservice.org';

    public function __construct()
    {
        $this->apiKey = config('services.openrouteservice.api_key');
    }

    /**
     * Get directions using OpenRouteService
     */
    public function getDirections(string $origin, string $destination, array $waypoints = [], string $profile = 'driving-car')
    {
        try {
            // Convert location strings to coordinates if needed
            $originCoords = $this->getCoordinates($origin);
            $destinationCoords = $this->getCoordinates($destination);

            if (! $originCoords || ! $destinationCoords) {
                return null;
            }

            // Build coordinates array [longitude, latitude]
            $coordinates = [$originCoords];

            // Add waypoints
            foreach ($waypoints as $waypoint) {
                $waypointCoords = $this->getCoordinates($waypoint);
                if ($waypointCoords) {
                    $coordinates[] = $waypointCoords;
                }
            }

            $coordinates[] = $destinationCoords;

            // ORS Directions API call
            $response = Http::withHeaders([
                'Authorization' => $this->apiKey,
                'Content-Type' => 'application/json',
            ])->post("{$this->baseUrl}/v2/directions/{$profile}", [
                'coordinates' => $coordinates,
                'format' => 'json',
                'instructions' => true,
                'geometry' => true,
                'elevation' => true,
                'extra_info' => ['surface', 'steepness', 'waytype'],
                'units' => 'km',
            ]);

            if ($response->successful()) {
                $data = $response->json();

                return $this->parseDirectionsResponse($data, $profile);
            }

            Log::warning('ORS Directions API failed', [
                'status' => $response->status(),
                'response' => $response->body(),
            ]);

            return null;

        } catch (\Exception $e) {
            Log::error('ORS Directions API exception', [
                'error' => $e->getMessage(),
                'origin' => $origin,
                'destination' => $destination,
            ]);

            return null;
        }
    }

    /**
     * Get hiking-specific directions with trail information
     */
    public function getHikingDirections(string $origin, string $destination, array $waypoints = [])
    {
        // Use foot-hiking profile for better trail routing
        return $this->getDirections($origin, $destination, $waypoints, 'foot-hiking');
    }

    /**
     * Get coordinates from location string using ORS Geocoding
     */
    private function getCoordinates(string $location): ?array
    {
        try {
            // Check if location is already coordinates (lat,lng format)
            if (preg_match('/^-?\d+\.?\d*,-?\d+\.?\d*$/', $location)) {
                $coords = explode(',', $location);

                return [(float) $coords[1], (float) $coords[0]]; // ORS uses [lng, lat]
            }

            // Use ORS Geocoding API
            $response = Http::withHeaders([
                'Authorization' => $this->apiKey,
            ])->get("{$this->baseUrl}/geocode/search", [
                'text' => $location,
                'boundary.country' => 'PH', // Focus on Philippines
                'size' => 1,
            ]);

            if ($response->successful()) {
                $data = $response->json();

                if (! empty($data['features'])) {
                    $coords = $data['features'][0]['geometry']['coordinates'];

                    return [$coords[0], $coords[1]]; // [longitude, latitude]
                }
            }

            Log::warning('ORS Geocoding failed', [
                'location' => $location,
                'response' => $response->body(),
            ]);

            return null;

        } catch (\Exception $e) {
            Log::error('ORS Geocoding exception', [
                'error' => $e->getMessage(),
                'location' => $location,
            ]);

            return null;
        }
    }

    /**
     * Parse ORS directions response into standardized format
     */
    private function parseDirectionsResponse(array $data, string $profile): array
    {
        if (empty($data['routes'])) {
            return [];
        }

        $route = $data['routes'][0];
        $segments = $route['segments'] ?? [];

        $legs = [];
        $totalDistance = 0;
        $totalDuration = 0;

        foreach ($segments as $segment) {
            $steps = [];

            foreach ($segment['steps'] ?? [] as $step) {
                $steps[] = [
                    'travel_mode' => $this->mapTravelMode($profile),
                    'distance' => [
                        'text' => round($step['distance'] / 1000, 2).' km',
                        'value' => $step['distance'], // meters
                    ],
                    'duration' => [
                        'text' => $this->formatDuration($step['duration']),
                        'value' => $step['duration'], // seconds
                    ],
                    'start_location' => [
                        'lat' => $step['way_points'][0] ?? null,
                        'lng' => $step['way_points'][1] ?? null,
                    ],
                    'end_location' => [
                        'lat' => $step['way_points'][2] ?? null,
                        'lng' => $step['way_points'][3] ?? null,
                    ],
                    'html_instructions' => $step['instruction'] ?? '',
                    'maneuver' => $step['maneuver'] ?? null,
                    // ORS-specific trail data
                    'elevation_gain' => $this->calculateElevationGain($step),
                    'surface_type' => $this->getSurfaceType($step),
                    'trail_difficulty' => $this->getTrailDifficulty($step, $profile),
                ];
            }

            $legDistance = $segment['distance'] ?? 0;
            $legDuration = $segment['duration'] ?? 0;

            $legs[] = [
                'distance' => [
                    'text' => round($legDistance / 1000, 2).' km',
                    'value' => $legDistance,
                ],
                'duration' => [
                    'text' => $this->formatDuration($legDuration),
                    'value' => $legDuration,
                ],
                'duration_seconds' => $legDuration,
                'steps' => $steps,
            ];

            $totalDistance += $legDistance;
            $totalDuration += $legDuration;
        }

        return [
            'legs' => $legs,
            'overview_polyline' => [
                'points' => $route['geometry'] ?? '',
            ],
            'summary' => $route['summary'] ?? '',
            'total_distance' => round($totalDistance / 1000, 2).' km',
            'total_duration' => $this->formatDuration($totalDuration),
            'total_duration_seconds' => $totalDuration,
            'elevation_profile' => $this->getElevationProfile($route),
            'trail_features' => $this->getTrailFeatures($route),
            'routing_provider' => 'openrouteservice',
            'profile_used' => $profile,
        ];
    }

    /**
     * Map ORS profile to travel mode
     */
    private function mapTravelMode(string $profile): string
    {
        return match ($profile) {
            'foot-walking', 'foot-hiking' => 'WALKING',
            'driving-car' => 'DRIVING',
            'cycling-regular', 'cycling-road', 'cycling-mountain' => 'BICYCLING',
            default => 'WALKING',
        };
    }

    /**
     * Format duration from seconds to human readable
     */
    private function formatDuration(int $seconds): string
    {
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);

        if ($hours > 0) {
            return $hours.' hr '.$minutes.' min';
        }

        return $minutes.' min';
    }

    /**
     * Calculate elevation gain from step data
     */
    private function calculateElevationGain(array $step): int
    {
        // Extract elevation data if available
        if (isset($step['elevation'])) {
            return max(0, $step['elevation']['ascent'] ?? 0);
        }

        return 0;
    }

    /**
     * Get surface type from step extras
     */
    private function getSurfaceType(array $step): string
    {
        if (isset($step['extras']['surface'])) {
            $surfaces = $step['extras']['surface']['values'] ?? [];
            if (! empty($surfaces)) {
                // Map ORS surface codes to readable names
                $surfaceCode = $surfaces[0][2] ?? 0;

                return $this->mapSurfaceCode($surfaceCode);
            }
        }

        return 'unknown';
    }

    /**
     * Map ORS surface codes to readable names
     */
    private function mapSurfaceCode(int $code): string
    {
        return match ($code) {
            1 => 'paved',
            2 => 'unpaved',
            3 => 'asphalt',
            4 => 'concrete',
            5 => 'paving_stones',
            6 => 'gravel',
            7 => 'dirt',
            8 => 'grass',
            9 => 'sand',
            10 => 'rock',
            11 => 'mud',
            default => 'unknown',
        };
    }

    /**
     * Determine trail difficulty based on profile and step data
     */
    private function getTrailDifficulty(array $step, string $profile): string
    {
        if ($profile !== 'foot-hiking') {
            return 'n/a';
        }

        $elevationGain = $this->calculateElevationGain($step);
        $distance = $step['distance'] ?? 0;

        // Simple difficulty calculation based on elevation gain per km
        if ($distance > 0) {
            $gainPerKm = ($elevationGain / $distance) * 1000;

            if ($gainPerKm > 200) {
                return 'hard';
            }
            if ($gainPerKm > 100) {
                return 'moderate';
            }

            return 'easy';
        }

        return 'unknown';
    }

    /**
     * Get elevation profile from route
     */
    private function getElevationProfile(array $route): array
    {
        if (! isset($route['elevation'])) {
            return [];
        }

        return [
            'total_ascent' => $route['elevation']['ascent'] ?? 0,
            'total_descent' => $route['elevation']['descent'] ?? 0,
            'min_elevation' => $route['elevation']['min_elevation'] ?? 0,
            'max_elevation' => $route['elevation']['max_elevation'] ?? 0,
        ];
    }

    /**
     * Extract trail-specific features
     */
    private function getTrailFeatures(array $route): array
    {
        $features = [];

        // Extract way types (trail types)
        if (isset($route['extras']['waytype'])) {
            $wayTypes = [];
            foreach ($route['extras']['waytype']['values'] ?? [] as $wayType) {
                $wayTypes[] = $this->mapWayType($wayType[2] ?? 0);
            }
            $features['way_types'] = array_unique($wayTypes);
        }

        // Extract steepness information
        if (isset($route['extras']['steepness'])) {
            $steepness = $route['extras']['steepness']['summary'] ?? [];
            $features['steepness'] = $steepness;
        }

        return $features;
    }

    /**
     * Map ORS way type codes to readable names
     */
    private function mapWayType(int $code): string
    {
        return match ($code) {
            0 => 'highway',
            1 => 'steps',
            2 => 'ferry',
            3 => 'unmarked',
            4 => 'track',
            5 => 'tunnel',
            6 => 'bridge',
            7 => 'path',
            8 => 'cycleway',
            9 => 'footway',
            10 => 'pedestrian',
            default => 'unknown',
        };
    }

    /**
     * Check if ORS API is available
     */
    public function isAvailable(): bool
    {
        return ! empty($this->apiKey);
    }
}

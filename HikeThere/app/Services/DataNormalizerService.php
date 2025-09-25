<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\Trail;

class DataNormalizerService
{
    /**
     * Normalize all input data for itinerary generation
     */
    public function normalizeInputs($itinerary = null, $trail = null, $build = null, $weatherData = [])
    {
        // Normalize itinerary data
        $normalizedItinerary = $this->normalizeItinerary($itinerary);
        
        // Normalize trail data
        $normalizedTrail = $this->normalizeTrail($trail, $normalizedItinerary);
        
        // Normalize build data
        $normalizedBuild = $this->normalizeBuild($build, $normalizedItinerary);
        
        // Normalize weather data
        $normalizedWeather = $this->normalizeWeatherData($weatherData);
        
        // Extract and normalize route data
        $routeData = $this->normalizeRouteData($normalizedItinerary);

        return [
            'itinerary' => $normalizedItinerary,
            'trail' => $normalizedTrail,
            'build' => $normalizedBuild,
            'weatherData' => $normalizedWeather,
            'routeData' => $routeData,
        ];
    }

    /**
     * Normalize itinerary data from various sources
     */
    protected function normalizeItinerary($itinerary)
    {
        // Handle null or invalid input
        if ($itinerary === null) {
            $itinerary = [];
        }

        // Handle string input (might be JSON)
        if (is_string($itinerary)) {
            $decoded = json_decode($itinerary, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $itinerary = $decoded;
            } else {
                // If not valid JSON, treat as empty array
                $itinerary = [];
            }
        }

        // Handle Eloquent models vs arrays
        if (is_object($itinerary) && method_exists($itinerary, 'toArray')) {
            $itinerary = $itinerary->toArray();
        } else {
            $itinerary = (array) $itinerary;
        }

        // Check for old Laravel session input (for live previews)
        try {
            $oldInput = session()->getOldInput('itinerary');
            if (!empty($oldInput)) {
                if (is_string($oldInput)) {
                    $decoded = json_decode($oldInput, true);
                    if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                        $oldInput = $decoded;
                    } else {
                        $oldInput = [];
                    }
                }

                if (is_array($oldInput)) {
                    // Decode any JSON-encoded nested fields safely
                    foreach ($oldInput as $key => $value) {
                        if (is_string($value) && strlen($value) > 0 && 
                            ($value[0] === '{' || $value[0] === '[')) {
                            $decoded = json_decode($value, true);
                            if (json_last_error() === JSON_ERROR_NONE && $decoded !== null) {
                                $oldInput[$key] = $decoded;
                            }
                        }
                    }

                    // Merge old input on top of provided itinerary
                    $itinerary = array_merge($itinerary, $oldInput);
                }
            }
        } catch (\Throwable $e) {
            // Ignore parsing errors
        }

        return $itinerary;
    }

    /**
     * Normalize trail data
     */
    protected function normalizeTrail($trail, $itinerary)
    {
        // Resolve trail if passed as ID
        if (is_numeric($trail)) {
            try {
                $trailModel = Trail::find($trail);
                $trail = $trailModel ? $trailModel->toArray() : [];
            } catch (\Throwable $e) {
                $trail = [];
            }
        } elseif (is_object($trail)) {
            $trail = (array) $trail;
        } else {
            $trail = (array) ($trail ?? []);
        }

        // Try to resolve from itinerary if trail is empty
        if (empty($trail) || (empty($trail['name']) && empty($trail['trail_name']))) {
            $trail = $this->resolveTrailFromItinerary($itinerary, $trail);
        }

        // Normalize trail fields for consistent access
        return array_merge([
            'name' => $itinerary['trail_name'] ?? $itinerary['trail'] ?? ($trail['name'] ?? null),
            'region' => $itinerary['region'] ?? $trail['region'] ?? ($trail['location'] ?? null),
            'distance_km' => $itinerary['distance_km'] ?? $itinerary['distance'] ?? 
                           $trail['distance_km'] ?? $trail['length'] ?? $trail['distance'] ?? null,
            'elevation_m' => $itinerary['elevation_m'] ?? $itinerary['elevation_gain'] ?? 
                           $trail['elevation_m'] ?? $trail['elevation_gain'] ?? null,
            'difficulty' => $itinerary['difficulty'] ?? $itinerary['difficulty_level'] ?? 
                          $trail['difficulty'] ?? null,
            'overnight_allowed' => $itinerary['overnight_allowed'] ?? 
                                 $trail['overnight_allowed'] ?? $trail['overnight'] ?? null,
            'route_description' => $itinerary['route_description'] ?? 
                                 $trail['route_description'] ?? $trail['summary'] ?? 
                                 $trail['description'] ?? null,
            'estimated_time' => $itinerary['estimated_time'] ?? $itinerary['estimated_duration'] ?? 
                              $trail['estimated_time'] ?? $trail['estimated_duration'] ?? null,
        ], $trail);
    }

    /**
     * Try to resolve trail from itinerary data
     */
    protected function resolveTrailFromItinerary($itinerary, $currentTrail)
    {
        $trailCandidate = null;
        
        if (!empty($itinerary['trail_id'])) {
            $trailCandidate = $itinerary['trail_id'];
        } elseif (!empty($itinerary['trail']) && is_numeric($itinerary['trail'])) {
            $trailCandidate = $itinerary['trail'];
        }

        if ($trailCandidate) {
            try {
                $trailModel = Trail::find($trailCandidate);
                if ($trailModel) {
                    return $trailModel->toArray();
                }
            } catch (\Throwable $e) {
                // Ignore
            }
        }

        // Try by name
        if (!empty($itinerary['trail_name'])) {
            try {
                $trailModel = Trail::where('trail_name', $itinerary['trail_name'])
                    ->orWhere('trail_name', 'like', '%' . $itinerary['trail_name'] . '%')
                    ->first();
                if ($trailModel) {
                    return $trailModel->toArray();
                }
            } catch (\Throwable $e) {
                // Ignore
            }
        }

        return $currentTrail;
    }

    /**
     * Normalize build/transport data
     */
    protected function normalizeBuild($build, $itinerary)
    {
        // Resolve build if passed as ID
        if (is_numeric($build)) {
            if (class_exists('App\\Models\\Build')) {
                try {
                    $buildClass = app()->make('App\\Models\\Build');
                    $buildModel = $buildClass::find($build);
                    $build = $buildModel ? $buildModel->toArray() : [];
                } catch (\Throwable $e) {
                    $build = [];
                }
            } else {
                $build = [];
            }
        } elseif (is_object($build)) {
            $build = (array) $build;
        } else {
            $build = (array) ($build ?? []);
        }

        // Try to get build info from itinerary if missing
        if (empty($build) && !empty($itinerary)) {
            $build = $itinerary['transport_details'] ?? 
                    $itinerary['build'] ?? 
                    $itinerary['transport'] ?? 
                    $build;
            
            if (is_object($build)) {
                $build = (array) $build;
            }
            
            if (!is_array($build)) {
                $build = [];
            }
        }

        // Check old input for build data
        if (empty($build)) {
            try {
                $oldInput = session()->getOldInput('itinerary');
                if (!empty($oldInput) && is_array($oldInput)) {
                    $candidate = $oldInput['build'] ?? 
                               $oldInput['transport_details'] ?? 
                               $oldInput['build_data'] ?? 
                               $oldInput;
                    
                    if ($candidate) {
                        $build = is_string($candidate) ? 
                               json_decode($candidate, true) ?? [] : 
                               (array) $candidate;
                    }
                }
            } catch (\Throwable $e) {
                // Ignore
            }
        }

        return is_array($build) ? $build : [];
    }

    /**
     * Normalize weather data
     */
    protected function normalizeWeatherData($weatherData)
    {
        return is_array($weatherData) ? $weatherData : [];
    }

    /**
     * Extract and normalize route data
     */
    protected function normalizeRouteData($itinerary)
    {
        $routeData = $itinerary['route_data'] ?? $itinerary['route'] ?? [];
        
        if (is_string($routeData)) {
            $routeData = json_decode($routeData, true) ?: [];
        }

        $routeData = is_array($routeData) ? $routeData : [];
        
        // Normalize legs structure
        $routeData['legs'] = $routeData['legs'] ?? 
                           $routeData['route']['legs'] ?? 
                           $routeData['routes'][0]['legs'] ?? 
                           null;

        // Convert legs object to array if needed
        if (!empty($routeData['legs']) && !is_array($routeData['legs'])) {
            $routeData['legs'] = (array) $routeData['legs'];
        }

        // Calculate total distance if missing
        if (empty($routeData['total_distance_km'])) {
            $routeData['total_distance_km'] = $this->calculateTotalDistance($routeData);
        }

        $routeData['legs_count'] = is_array($routeData['legs']) ? count($routeData['legs']) : 0;

        return $routeData;
    }

    /**
     * Calculate total distance from route data
     */
    protected function calculateTotalDistance($routeData)
    {
        $totalMeters = 0;
        
        if (!empty($routeData['total_distance'])) {
            $totalMeters = floatval($routeData['total_distance']);
        } elseif (!empty($routeData['total_distance_m'])) {
            $totalMeters = floatval($routeData['total_distance_m']);
        } elseif (!empty($routeData['legs']) && is_array($routeData['legs'])) {
            foreach ($routeData['legs'] as $leg) {
                $meters = $leg['distance_m'] ?? 
                         ($leg['distance']['value'] ?? 
                         ($leg['distance_meters'] ?? 0));
                if (is_numeric($meters)) {
                    $totalMeters += floatval($meters);
                }
            }
        }

        return $totalMeters > 0 ? round($totalMeters / 1000.0, 3) : null;
    }
}
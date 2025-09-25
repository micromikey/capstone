<?php

namespace App\Services;

use App\Models\Trail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

/**
 * Enhanced Weather Helper Service with ML-Integrated Intelligent Notes
 * 
 * This service provides:
 * 1. Non-redundant, activity-specific weather advice
 * 2. ML-integrated recommendations based on trail characteristics
 * 3. Single, consolidated weather notes per activity
 * 4. Distance-aware weather considerations
 */
class IntelligentWeatherService
{
    protected $weatherHelperService;
    protected $mlApiUrl;
    
    public function __construct()
    {
        $this->weatherHelperService = new WeatherHelperService();
        $this->mlApiUrl = config('app.ml_api_url', 'http://localhost:8000');
    }

    /**
     * Generate a single, intelligent weather note for an activity
     * Eliminates redundancy and provides activity-specific advice
     */
    public function generateSmartWeatherNote($activity, $weather, $trail = null, $dayIndex = 0)
    {
        if (!$weather) {
            return null;
        }

        $activityType = strtolower($activity['type'] ?? 'activity');
        $activityTitle = $activity['title'] ?? '';
        $distance = $activity['cum_distance_km'] ?? 0;
        $time = $activity['time'] ?? '';
        
        // Get weather condition and temperature
        $weatherParts = explode(' / ', $weather);
        $condition = trim($weatherParts[0] ?? $weather);
        $temperature = $this->extractTemperature($weather);
        
        // Generate ML-enhanced recommendations
        $mlRecommendations = $this->getMlWeatherRecommendations($activity, $weather, $trail);
        
        // Create activity-specific weather advice
        $advice = $this->createActivitySpecificAdvice($activityType, $activityTitle, $condition, $temperature, $distance, $mlRecommendations);
        
        return $advice;
    }

    /**
     * Get ML-enhanced weather recommendations
     */
    protected function getMlWeatherRecommendations($activity, $weather, $trail = null)
    {
        try {
            // If ML service is available, get intelligent recommendations
            if ($this->isMlServiceAvailable()) {
                return $this->callMlWeatherService($activity, $weather, $trail);
            }
        } catch (\Exception $e) {
            Log::warning('ML weather service unavailable: ' . $e->getMessage());
        }
        
        // Fallback to rule-based recommendations
        return $this->getRuleBasedRecommendations($activity, $weather, $trail);
    }

    /**
     * Create activity-specific weather advice (non-redundant)
     */
    protected function createActivitySpecificAdvice($activityType, $activityTitle, $condition, $temperature, $distance, $mlRecommendations)
    {
        $advice = [];
        $conditionLower = strtolower($condition);
        
        // Weather condition icon
        $weatherIcon = $this->getWeatherIcon($conditionLower);
        
        // Activity-specific base advice
        $activityAdvice = $this->getActivitySpecificAdvice($activityType, $activityTitle, $distance);
        if ($activityAdvice) {
            $advice[] = $activityAdvice;
        }
        
        // Weather-specific gear advice (concise, one sentence)
        $gearAdvice = $this->getWeatherSpecificGearAdvice($conditionLower, $temperature, $activityType);
        if ($gearAdvice) {
            $advice[] = $weatherIcon . ' ' . $gearAdvice;
        }
        
        // ML recommendations (if available)
        if (!empty($mlRecommendations['priority_advice'])) {
            $advice[] = $mlRecommendations['priority_advice'];
        }
        
        return implode(' ', $advice) ?: null;
    }

    /**
     * Get activity-specific advice (non-weather related)
     */
    protected function getActivitySpecificAdvice($activityType, $activityTitle, $distance)
    {
        $advice = null;
        
        switch ($activityType) {
            case 'prep':
            case 'safety':
                $advice = 'Final equipment check - verify all gear is secure.';
                break;
                
            case 'photo':
            case 'viewpoint':
                $advice = 'Photo opportunity - clean camera lens and check battery.';
                break;
                
            case 'meal':
            case 'lunch':
                $advice = 'Refuel and hydrate - check energy levels.';
                break;
                
            case 'summit':
                $advice = 'Summit achievement! Document the moment and check descent route.';
                break;
                
            case 'climb':
            case 'ascent':
                $advice = 'Steep section - pace yourself and use trekking poles.';
                break;
                
            case 'descent':
                $advice = 'Descent phase - focus on foot placement and knee stability.';
                break;
                
            case 'rest':
                if ($distance > 5) {
                    $advice = 'Mid-trail rest - assess fatigue and adjust pace.';
                } else {
                    $advice = 'Short break - stretch and check hydration.';
                }
                break;
                
            case 'camp':
                $advice = 'Camp setup - select sheltered spot and secure gear.';
                break;
                
            default:
                if ($distance > 10) {
                    $advice = 'Long-distance marker - monitor energy and supplies.';
                }
                break;
        }
        
        return $advice;
    }

    /**
     * Get concise, weather-specific gear advice
     */
    protected function getWeatherSpecificGearAdvice($conditionLower, $temperature, $activityType)
    {
        // Rain conditions
        if (str_contains($conditionLower, 'rain') || str_contains($conditionLower, 'shower')) {
            if ($activityType === 'summit' || $activityType === 'climb') {
                return 'Rain gear essential - extra grip needed on wet rocks.';
            } elseif ($activityType === 'descent') {
                return 'Extreme caution - wet trails are very slippery.';
            } else {
                return 'Waterproof jacket and pack cover required.';
            }
        }
        
        // Snow conditions
        if (str_contains($conditionLower, 'snow')) {
            return 'Snow conditions - insulated layers and traction devices needed.';
        }
        
        // Wind conditions
        if (str_contains($conditionLower, 'wind')) {
            if ($activityType === 'summit' || $activityType === 'photo') {
                return 'High winds - secure all loose items and layers.';
            } else {
                return 'Windproof shell recommended.';
            }
        }
        
        // Temperature-based advice
        if ($temperature) {
            if ($temperature >= 30) {
                return 'Hot conditions - sun protection and extra water critical.';
            } elseif ($temperature <= 5) {
                return 'Cold weather - insulating layers and warm accessories.';
            }
        }
        
        // Clear/mild conditions
        if (str_contains($conditionLower, 'clear') || str_contains($conditionLower, 'sunny')) {
            if ($temperature && $temperature > 20) {
                return 'Clear skies - sun protection and hydration priority.';
            } else {
                return 'Good conditions - standard hiking gear sufficient.';
            }
        }
        
        return null;
    }

    /**
     * Get weather icon for display
     */
    protected function getWeatherIcon($conditionLower)
    {
        if (str_contains($conditionLower, 'rain')) return '🌧️';
        if (str_contains($conditionLower, 'snow')) return '❄️';
        if (str_contains($conditionLower, 'wind')) return '💨';
        if (str_contains($conditionLower, 'storm')) return '⛈️';
        if (str_contains($conditionLower, 'cloud')) return '☁️';
        if (str_contains($conditionLower, 'clear') || str_contains($conditionLower, 'sunny')) return '☀️';
        
        return '🌤️';
    }

    /**
     * Call ML weather service for intelligent recommendations
     */
    protected function callMlWeatherService($activity, $weather, $trail)
    {
        try {
            $response = Http::timeout(3)->post($this->mlApiUrl . '/weather-advice', [
                'activity' => $activity,
                'weather' => $weather,
                'trail_data' => $trail ? [
                    'difficulty' => is_object($trail) ? $trail->difficulty : ($trail['difficulty'] ?? null),
                    'elevation_gain' => is_object($trail) ? $trail->elevation_gain : ($trail['elevation_gain'] ?? null),
                    'features' => is_object($trail) ? $trail->features : ($trail['features'] ?? null),
                ] : null,
            ]);
            
            if ($response->successful()) {
                return $response->json();
            }
        } catch (\Exception $e) {
            Log::warning('ML weather service call failed: ' . $e->getMessage());
        }
        
        return [];
    }

    /**
     * Rule-based recommendations fallback
     */
    protected function getRuleBasedRecommendations($activity, $weather, $trail)
    {
        $recommendations = [];
        
        $activityType = strtolower($activity['type'] ?? 'activity');
        $distance = $activity['cum_distance_km'] ?? 0;
        
        // Distance-based recommendations
        if ($distance > 15) {
            $recommendations['priority_advice'] = 'Long-distance hike - monitor fatigue and weather changes.';
        } elseif ($distance > 10) {
            $recommendations['priority_advice'] = 'Significant distance covered - assess supplies and energy.';
        }
        
        // Trail difficulty considerations
        if ($trail) {
            $difficulty = is_object($trail) ? $trail->difficulty : ($trail['difficulty'] ?? null);
            if ($difficulty && str_contains(strtolower($difficulty), 'advanced')) {
                if ($activityType === 'climb' || $activityType === 'summit') {
                    $recommendations['priority_advice'] = 'Technical terrain - double-check safety gear.';
                }
            }
        }
        
        return $recommendations;
    }

    /**
     * Check if ML service is available
     */
    protected function isMlServiceAvailable()
    {
        try {
            $response = Http::timeout(1)->get($this->mlApiUrl . '/health');
            return $response->successful();
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Extract temperature from weather string
     */
    protected function extractTemperature($weather)
    {
        if (preg_match('/(\d+)°?[CF]?/', $weather, $matches)) {
            return intval($matches[1]);
        }
        return null;
    }

    /**
     * Get dynamic weather for activity (delegate to existing service)
     */
    public function getDynamicWeatherForActivity($weatherData, $activities, $trail, $dayIndex, $time = null)
    {
        return $this->weatherHelperService->getWeatherFor($weatherData, $dayIndex, $time, null, $trail);
    }
}
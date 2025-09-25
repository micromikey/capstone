<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class WeatherHelperService
{
    /**
     * Fetch weather information for a specific day and time
     * Now supports dynamic weather calculation based on trail position and time
     */
    public function getWeatherFor($weatherData, $dayIndex, $time, $activity = null, $trail = null)
    {
        // Try dynamic weather calculation first if we have trail and activity data
        if ($activity && $trail && isset($weatherData['forecast'])) {
            $dynamicWeather = $this->getDynamicWeatherForActivity($weatherData, $activity, $trail, $dayIndex, $time);
            if ($dynamicWeather) {
                return $dynamicWeather;
            }
        }

        // Fallback to existing static weather lookup
        if (!is_array($weatherData)) {
            return null;
        }

        // Prefer exact match for the provided dayIndex
        if (array_key_exists($dayIndex, $weatherData)) {
            $day = $weatherData[$dayIndex];
            if (is_array($day)) {
                return isset($day[$time]) ? $day[$time] : null;
            }
            if (is_string($day)) {
                return $day;
            }
        }

        // Fallback: try dayIndex - 1 (some sources index days starting at 0)
        $zeroIndex = intval($dayIndex) - 1;
        if ($zeroIndex >= 0 && array_key_exists($zeroIndex, $weatherData)) {
            $day = $weatherData[$zeroIndex];
            if (is_array($day)) {
                return isset($day[$time]) ? $day[$time] : null;
            }
            if (is_string($day)) {
                return $day;
            }
        }

        return null;
    }

    /**
     * Get dynamic weather for a specific activity based on trail position and time
     */
    protected function getDynamicWeatherForActivity($weatherData, $activity, $trail, $dayIndex, $time)
    {
        try {
            // Get the forecast data
            $forecast = $weatherData['forecast'] ?? null;
            if (!$forecast || !is_array($forecast)) {
                return null;
            }

            // Find the correct day in the forecast
            $dayForecast = null;
            foreach ($forecast as $forecastDay) {
                if (($forecastDay['day_label'] === 'TODAY' && $dayIndex == 1) ||
                    ($forecastDay['day_label'] === 'TOMORROW' && $dayIndex == 2) ||
                    (isset($forecastDay['date']) && Carbon::parse($forecastDay['date'])->day == Carbon::now()->addDays($dayIndex - 1)->day)) {
                    $dayForecast = $forecastDay;
                    break;
                }
            }

            if (!$dayForecast) {
                // Fallback to the appropriate day by index
                $dayForecast = $forecast[$dayIndex - 1] ?? $forecast[0] ?? null;
            }

            if (!$dayForecast) {
                return null;
            }

            // Get hourly forecast for the time
            $hourlyForecasts = $dayForecast['hourly_forecasts'] ?? [];
            if (!empty($hourlyForecasts)) {
                // Try to find the closest time match
                $targetHour = intval(substr($time, 0, 2));
                $closestHourly = null;
                $closestDiff = 24;

                foreach ($hourlyForecasts as $hourly) {
                    $hourlyTime = $hourly['time'] ?? '';
                    $hourlyHour = intval(substr($hourlyTime, 0, 2));
                    $diff = abs($hourlyHour - $targetHour);
                    
                    if ($diff < $closestDiff) {
                        $closestDiff = $diff;
                        $closestHourly = $hourly;
                    }
                }

                if ($closestHourly) {
                    return $closestHourly['condition'] . ' / ' . $closestHourly['temp'] . '°C';
                }
            }

            // Fallback to daily weather
            $condition = $dayForecast['condition'] ?? 'Clear';
            $minTemp = $dayForecast['temp_min'] ?? 20;
            $maxTemp = $dayForecast['temp_max'] ?? 25;
            
            // Use temperature based on time of day
            $hour = intval(substr($time, 0, 2));
            $temp = $this->interpolateTemperatureByTime($hour, $minTemp, $maxTemp);
            
            return $condition . ' / ' . $temp . '°C';

        } catch (\Exception $e) {
            Log::warning('Error calculating dynamic weather: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Interpolate temperature based on time of day
     */
    protected function interpolateTemperatureByTime($hour, $minTemp, $maxTemp)
    {
        // Simple temperature curve: coolest at 6 AM, warmest at 2 PM
        if ($hour <= 6) {
            return $minTemp; // Early morning - minimum temp
        } elseif ($hour >= 14) {
            return $maxTemp; // Afternoon - maximum temp
        } else {
            // Linear interpolation between 6 AM and 2 PM
            $progress = ($hour - 6) / (14 - 6);
            return round($minTemp + ($maxTemp - $minTemp) * $progress);
        }
    }

    /**
     * Get coordinates for an activity based on its progress along the trail
     */
    protected function getActivityCoordinates($activity, $trail)
    {
        if (!$trail || !isset($trail->coordinates) || empty($trail->coordinates)) {
            // Fallback to main trail coordinates
            return [
                'lat' => $trail->latitude ?? null,
                'lng' => $trail->longitude ?? null
            ];
        }

        $coordinates = $trail->coordinates;
        if (!is_array($coordinates)) {
            return [
                'lat' => $trail->latitude ?? null,
                'lng' => $trail->longitude ?? null
            ];
        }

        // Calculate progress based on activity distance or cumulative distance
        $totalDistance = $trail->distance ?? 10; // km
        $activityDistance = $activity['cum_distance_km'] ?? 0;
        
        if ($totalDistance <= 0 || $activityDistance <= 0) {
            // Return starting coordinates
            return [
                'lat' => $coordinates[0]['lat'] ?? $trail->latitude,
                'lng' => $coordinates[0]['lng'] ?? $trail->longitude
            ];
        }

        // Calculate progress percentage
        $progress = min(1.0, $activityDistance / $totalDistance);
        
        // Find the corresponding point along the trail coordinates
        $totalPoints = count($coordinates);
        $targetIndex = (int) ($progress * ($totalPoints - 1));
        
        // Get the coordinate at this position
        $coordinate = $coordinates[$targetIndex] ?? $coordinates[0];
        
        return [
            'lat' => $coordinate['lat'] ?? $trail->latitude,
            'lng' => $coordinate['lng'] ?? $trail->longitude
        ];
    }

    /**
     * Fetch weather for specific coordinates and time (future enhancement)
     * For now, this falls back to the main trail weather but could be expanded
     * to fetch weather for specific coordinates if needed
     */
    protected function getWeatherForCoordinates($lat, $lng, $time, $weatherData)
    {
        // For now, return the main weather data
        // This could be enhanced to call the weather API for specific coordinates
        // if the trail covers a large geographic area
        
        return $this->getDynamicWeatherForActivity($weatherData, [], null, 1, $time);
    }

    /**
     * Generate intelligent notes based on activity and weather
     */
    public function generateIntelligentNote($activity, $weather)
    {
        $notes = [];
        $type = strtolower($activity['type'] ?? 'activity');
        $title = $activity['title'] ?? '';

        // Activity-based notes
        if (str_contains($title, 'Summit') || str_contains($title, 'Ascent')) {
            $notes[] = 'Steep sections expected. Use trekking poles if available.';
        }

        if ($type === 'camp' || $type === 'overnight') {
            $notes[] = 'Prepare sleeping gear and warm clothing.';
        }

        if ($type === 'prep') {
            $notes[] = 'Check all equipment before starting the hike.';
        }

        if ($type === 'rest' || str_contains($title, 'Water Break')) {
            $notes[] = 'Hydrate well and check energy levels.';
        }

        if ($type === 'photo' || str_contains($title, 'Photo')) {
            $notes[] = 'Great opportunity for photos and rest.';
        }

        if ($type === 'checkpoint' || str_contains($title, 'Navigation')) {
            $notes[] = 'Verify route and check trail markers.';
        }

        if ($type === 'climb' || str_contains($title, 'Final Push')) {
            $notes[] = 'Most challenging section. Pace yourself.';
        }

        if ($type === 'descent') {
            $notes[] = 'Watch your footing on the descent.';
        }

        if ($type === 'summit') {
            $notes[] = 'Congratulations! Enjoy the view and take photos.';
        }

        // Enhanced weather-based preparation notes
        if ($weather) {
            $weatherAdvice = $this->getWeatherPreparationAdvice($weather, $type, $title);
            if ($weatherAdvice) {
                $notes[] = $weatherAdvice;
            }
        }

        return implode(' ', $notes) ?: null;
    }

    /**
     * Generate comprehensive weather preparation advice
     */
    public function getWeatherPreparationAdvice($weather, $activityType = '', $activityTitle = '')
    {
        if (!$weather) return null;
        
        $weatherLower = strtolower($weather);
        $temp = $this->extractTemperature($weather);
        $advice = [];

        // Rain and precipitation
        if (str_contains($weatherLower, 'rain') || str_contains($weatherLower, 'shower') || str_contains($weatherLower, 'storm')) {
            $advice[] = '🌧️ Rain expected - pack waterproof jacket, rain pants, and pack cover.';
            $advice[] = 'Trails will be slippery - wear proper hiking boots with good grip.';
            $advice[] = 'Keep electronics in waterproof bags.';
        }

        // Snow conditions
        if (str_contains($weatherLower, 'snow') || str_contains($weatherLower, 'blizzard')) {
            $advice[] = '❄️ Snow conditions - bring insulated layers, waterproof boots, and gaiters.';
            $advice[] = 'Consider microspikes or crampons for traction.';
            $advice[] = 'Pack extra food and emergency shelter.';
        }

        // Wind conditions
        if (str_contains($weatherLower, 'wind') || str_contains($weatherLower, 'gust')) {
            $advice[] = '💨 Windy conditions - secure all loose items and wear windproof layers.';
            $advice[] = 'Be extra cautious near ridges and exposed areas.';
        }

        // Temperature-based advice
        if ($temp) {
            if ($temp >= 35) {
                $advice[] = '🔥 Extreme heat - start early, bring 3+ liters of water, electrolyte supplements.';
                $advice[] = 'Wear light-colored, long-sleeve shirts and wide-brimmed hat.';
                $advice[] = 'Take frequent shade breaks and watch for heat exhaustion signs.';
            } elseif ($temp >= 30) {
                $advice[] = '☀️ Hot weather - bring extra water (2+ liters), sunscreen SPF 30+, and hat.';
                $advice[] = 'Consider cooling towel and take breaks in shade.';
            } elseif ($temp >= 25) {
                $advice[] = '🌤️ Warm weather - stay hydrated, apply sunscreen, and wear breathable clothing.';
            } elseif ($temp >= 15) {
                $advice[] = '🌥️ Mild weather - perfect hiking conditions. Light layers recommended.';
            } elseif ($temp >= 5) {
                $advice[] = '🧥 Cool weather - wear insulating layers and bring warm hat and gloves.';
            } elseif ($temp >= 0) {
                $advice[] = '🥶 Cold weather - thermal base layers, insulated jacket, and winter accessories required.';
            } else {
                $advice[] = '🧊 Freezing conditions - full winter gear essential. Check for ice on trails.';
                $advice[] = 'Bring hand warmers and emergency bivvy.';
            }
        }

        // Fog and visibility
        if (str_contains($weatherLower, 'fog') || str_contains($weatherLower, 'mist')) {
            $advice[] = '🌫️ Limited visibility - bring headlamp, GPS/map, and stay on marked trails.';
        }

        // Sunny/clear conditions
        if (str_contains($weatherLower, 'sunny') || str_contains($weatherLower, 'clear')) {
            $advice[] = '☀️ Clear skies - excellent visibility but bring sun protection.';
            $advice[] = 'UV exposure higher at altitude - use sunglasses and SPF 30+ sunscreen.';
        }

        // Cloudy conditions
        if (str_contains($weatherLower, 'cloudy') || str_contains($weatherLower, 'overcast')) {
            $advice[] = '☁️ Overcast conditions - cooler than expected, bring an extra layer.';
        }

        return !empty($advice) ? implode(' ', $advice) : null;
    }

    /**
     * Extract temperature from weather string
     */
    protected function extractTemperature($weather)
    {
        if (preg_match('/(\d+)°[CF]/', $weather, $matches)) {
            return intval($matches[1]);
        }
        return null;
    }

    /**
     * Compute time for a specific row based on day and offset
     */
    public function computeTimeForRow($baseDate, $baseTime, $dayIndex, $minutesOffset)
    {
        $baseDateTime = Carbon::parse($baseDate->copy()->addDays($dayIndex - 1)->toDateString() . ' ' . $baseTime);
        return $baseDateTime->copy()->addMinutes($minutesOffset)->format('H:i');
    }
}
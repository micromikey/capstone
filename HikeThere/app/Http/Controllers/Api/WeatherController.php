<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WeatherController extends Controller
{
    private const OPENWEATHER_API_KEY = null; // Will use env variable

    private const OPENWEATHER_BASE_URL = 'https://api.openweathermap.org/data/2.5';

    /**
     * Get weather data for a specific location
     */
    public function getWeather(Request $request): JsonResponse
    {
        $request->validate([
            'lat' => 'required|numeric|between:-90,90',
            'lng' => 'required|numeric|between:-180,180',
        ]);

        $lat = $request->lat;
        $lng = $request->lng;

        try {
            $weatherData = $this->getOpenWeatherData($lat, $lng);

            return response()->json($weatherData);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Weather data unavailable',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get 7-day weather forecast for a specific location
     */
    public function getForecast(Request $request): JsonResponse
    {
        $request->validate([
            'lat' => 'required|numeric|between:-90,90',
            'lng' => 'required|numeric|between:-180,180',
        ]);

        $lat = $request->lat;
        $lng = $request->lng;

        try {
            $forecastData = $this->getOpenWeatherForecast($lat, $lng);

            return response()->json($forecastData);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Forecast data unavailable',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get weather data from OpenWeather API
     */
    private function getOpenWeatherData(float $lat, float $lng): array
    {
        $apiKey = config('services.openweather.api_key') ?? env('OPENWEATHER_API_KEY');

        if (! $apiKey) {
            throw new \Exception('OpenWeather API key not configured');
        }

        $response = Http::get(self::OPENWEATHER_BASE_URL.'/weather', [
            'lat' => $lat,
            'lon' => $lng,
            'appid' => $apiKey,
            'units' => 'metric', // Use Celsius
            'lang' => 'en',
        ]);

        if (! $response->successful()) {
            throw new \Exception('Failed to fetch weather data from OpenWeather API');
        }

        $data = $response->json();

        // Use Philippine timezone for timestamp
        $philippineTime = now()->setTimezone('Asia/Manila');

        return [
            'condition' => $this->mapWeatherCondition($data['weather'][0]['main'] ?? 'Unknown'),
            'condition_code' => $data['weather'][0]['main'] ?? null,
            'temperature' => round($data['main']['temp'] ?? 0),
            'feels_like' => isset($data['main']['feels_like']) ? round($data['main']['feels_like']) : null,
            'humidity' => $data['main']['humidity'] ?? 0,
            'wind_speed' => round(($data['wind']['speed'] ?? 0) * 3.6), // Convert m/s to km/h
            'uvIndex' => $this->getUVIndex($lat, $lng),
            'timestamp' => $philippineTime->toISOString(),
            'location' => [
                'lat' => $lat,
                'lng' => $lng,
            ],
            'description' => $data['weather'][0]['description'] ?? '',
            'icon' => $data['weather'][0]['icon'] ?? null,
            'pressure' => $data['main']['pressure'] ?? 0,
            'visibility' => $data['visibility'] ?? 0,
        ];
    }

    /**
     * Get 7-day weather forecast from OpenWeather API
     */
    private function getOpenWeatherForecast(float $lat, float $lng): array
    {
        $apiKey = config('services.openweather.api_key') ?? env('OPENWEATHER_API_KEY');

        if (! $apiKey) {
            throw new \Exception('OpenWeather API key not configured');
        }

        $response = Http::get(self::OPENWEATHER_BASE_URL.'/forecast', [
            'lat' => $lat,
            'lon' => $lng,
            'appid' => $apiKey,
            'units' => 'metric', // Use Celsius
            'lang' => 'en',
            'cnt' => 40, // 5 days * 8 forecasts per day (every 3 hours)
        ]);

        if (! $response->successful()) {
            throw new \Exception('Failed to fetch forecast data from OpenWeather API');
        }

        $data = $response->json();

        // Use Philippine timezone consistently
        $philippineTime = now()->setTimezone('Asia/Manila');
        $today = $philippineTime->startOfDay();

        // Group forecasts by day and get daily summaries, filtering out past dates
        $dailyForecasts = collect($data['list'] ?? [])
            ->filter(function ($item) use ($today) {
                // Only include forecasts from today onwards in Philippine time
                $forecastDate = Carbon::parse($item['dt_txt'])->setTimezone('Asia/Manila')->startOfDay();

                return $forecastDate->gte($today);
            })
            ->groupBy(function ($item) {
                return Carbon::parse($item['dt_txt'])->setTimezone('Asia/Manila')->format('Y-m-d');
            })
            ->map(function ($dayItems, $date) use ($philippineTime) {
                // Get midday forecast (around 12:00) for daily summary
                $midday = $dayItems->firstWhere('dt_txt', fn ($dt) => str_contains($dt, '12:00:00')) ?? $dayItems->first();

                // Calculate daily min/max temperatures
                $temperatures = $dayItems->pluck('main.temp');
                $minTemp = $temperatures->min();
                $maxTemp = $temperatures->max();

                // Get most common weather condition for the day
                $conditions = $dayItems->pluck('weather.0.main')->countBy();
                $mainCondition = $conditions->sortDesc()->keys()->first();

                // Get precipitation probability (average of the day)
                $precipitation = $dayItems->avg('pop') * 100; // Convert to percentage

                // Parse the date in Philippine timezone
                $dateObj = Carbon::parse($date)->setTimezone('Asia/Manila');

                // Determine if this is today, tomorrow, or a regular day
                $dayLabel = $this->getDayLabel($dateObj, $philippineTime);

                return [
                    'date' => $date,
                    'day_name' => $dateObj->format('D'),
                    'day_full' => $dateObj->format('l'),
                    'date_formatted' => $dateObj->format('M j'),
                    'day_label' => $dayLabel, // NEW: Custom day label (TODAY, TOMORROW, or day name)
                    'is_today' => $dayLabel === 'TODAY',
                    'is_tomorrow' => $dayLabel === 'TOMORROW',
                    'condition' => $this->mapWeatherCondition($mainCondition),
                    'condition_code' => $mainCondition,
                    'icon' => $midday['weather'][0]['icon'] ?? '01d',
                    'temp_min' => round($minTemp),
                    'temp_max' => round($maxTemp),
                    'temp_midday' => round($midday['main']['temp'] ?? 0),
                    'humidity' => round($dayItems->avg('main.humidity')),
                    'wind_speed' => round($dayItems->avg('wind.speed') * 3.6), // Convert m/s to km/h
                    'precipitation' => round($precipitation, 1),
                    'description' => $midday['weather'][0]['description'] ?? '',
                    'hourly_forecasts' => $dayItems->map(function ($hour) {
                        return [
                            'time' => Carbon::parse($hour['dt_txt'])->setTimezone('Asia/Manila')->format('H:i'),
                            'temp' => round($hour['main']['temp']),
                            'condition' => $this->mapWeatherCondition($hour['weather'][0]['main']),
                            'icon' => $hour['weather'][0]['icon'],
                            'precipitation' => round($hour['pop'] * 100, 1),
                        ];
                    })->values()->toArray(),
                ];
            })
            ->take(5)
            ->values();

        return [
            'location' => [
                'lat' => $lat,
                'lng' => $lng,
                'city' => $data['city']['name'] ?? 'Unknown',
                'country' => $data['city']['country'] ?? 'Unknown',
            ],
            'forecast' => $dailyForecasts->toArray(),
            'generated_at' => $philippineTime->toISOString(),
        ];
    }

    /**
     * Get UV index data
     */
    private function getUVIndex(float $lat, float $lng): int
    {
        try {
            $apiKey = config('services.openweather.api_key') ?? env('OPENWEATHER_API_KEY');

            $response = Http::get(self::OPENWEATHER_BASE_URL.'/uvi', [
                'lat' => $lat,
                'lon' => $lng,
                'appid' => $apiKey,
            ]);

            if ($response->successful()) {
                return round($response->json('value', 0));
            }
        } catch (\Exception $e) {
            // Fallback to estimated UV based on latitude and time
        }

        // Fallback UV calculation based on latitude
        $baseUV = 5; // Base UV at equator
        $latFactor = abs($lat) / 90; // 0 at equator, 1 at poles

        return max(1, round($baseUV - ($latFactor * 3)));
    }

    /**
     * Get appropriate day label (TODAY, TOMORROW, or day name)
     */
    private function getDayLabel(Carbon $dateObj, Carbon $philippineTime): string
    {
        $today = $philippineTime->startOfDay();
        $tomorrow = $philippineTime->copy()->addDay()->startOfDay();
        $dateStart = $dateObj->startOfDay();

        if ($dateStart->equalTo($today)) {
            return 'TODAY';
        } elseif ($dateStart->equalTo($tomorrow)) {
            return 'TOMORROW';
        } else {
            return $dateObj->format('l'); // Full day name (Monday, Tuesday, etc.)
        }
    }

    /**
     * Map OpenWeather conditions to user-friendly descriptions
     */
    private function mapWeatherCondition(string $condition): string
    {
        $conditionMap = [
            'Clear' => 'Clear',
            'Clouds' => 'Cloudy',
            'Rain' => 'Light Rain',
            'Drizzle' => 'Drizzle',
            'Thunderstorm' => 'Thunderstorm',
            'Snow' => 'Snow',
            'Mist' => 'Misty',
            'Fog' => 'Foggy',
            'Haze' => 'Hazy',
            'Smoke' => 'Smoky',
            'Dust' => 'Dusty',
            'Sand' => 'Sandy',
            'Ash' => 'Ash',
            'Squall' => 'Windy',
            'Tornado' => 'Tornado',
        ];

        return $conditionMap[$condition] ?? $condition;
    }

    /**
     * Get weather gradient class based on condition
     */
    public function getWeatherGradient(string $condition): string
    {
        $gradientMap = [
            'Clear' => 'from-yellow-400 to-orange-500',
            'Cloudy' => 'from-gray-400 to-gray-600',
            'Light Rain' => 'from-blue-400 to-blue-700',
            'Rain' => 'from-blue-400 to-blue-700',
            'Drizzle' => 'from-teal-300 to-teal-500',
            'Thunderstorm' => 'from-indigo-700 to-gray-900',
            'Snow' => 'from-blue-100 to-blue-300',
            'Misty' => 'from-gray-300 to-gray-500',
            'Foggy' => 'from-gray-200 to-gray-400',
            'Hazy' => 'from-yellow-200 to-yellow-400',
        ];

        return $gradientMap[$condition] ?? 'from-indigo-500 to-yellow-300';
    }

    /**
     * Get weather conditions for trail location
     */
    public function getTrailConditions(Request $request): JsonResponse
    {
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $latitude = $request->latitude;
        $longitude = $request->longitude;

        try {
            // Use existing weather data method but format for trail conditions

            $weatherData = $this->getOpenWeatherData($latitude, $longitude);

            // Format for trail-specific needs (use keys returned by getOpenWeatherData)
            $trailConditions = [
                'temperature' => isset($weatherData['temperature']) ? round($weatherData['temperature']) : null,
                'feels_like' => $weatherData['feels_like'] ?? null,
                'condition' => $weatherData['condition'] ?? ($weatherData['description'] ?? null),
                'humidity' => $weatherData['humidity'] ?? null,
                'wind_speed' => $weatherData['wind_speed'] ?? null,
                'visibility' => isset($weatherData['visibility']) ? round($weatherData['visibility'] / 1000) : 10,
                'alerts' => $this->getTrailAlerts($weatherData),
            ];

            return response()->json($trailConditions);
        } catch (\Exception $e) {
            // Fallback mock data if weather service fails
            $mockData = [
                'temperature' => rand(15, 30),
                'feels_like' => rand(15, 30),
                'condition' => collect(['sunny', 'cloudy', 'rainy', 'foggy'])->random(),
                'humidity' => rand(40, 90),
                'wind_speed' => rand(5, 25),
                'visibility' => rand(5, 15),
                'alerts' => rand(0, 10) > 8 ? 'Weather data temporarily unavailable' : null,
            ];

            return response()->json($mockData);
        }
    }

    /**
     * Generate trail-specific weather alerts
     */
    private function getTrailAlerts(array $weatherData): ?string
    {
        $alerts = [];

        // Wind alerts
        if (isset($weatherData['wind']['speed']) && $weatherData['wind']['speed'] > 10) {
            $alerts[] = 'Strong winds expected - exercise caution on exposed ridges';
        }

        // Rain alerts
        if (isset($weatherData['rain']) || str_contains(strtolower($weatherData['description']), 'rain')) {
            $alerts[] = 'Rain expected - trails may be slippery and muddy';
        }

        // Temperature alerts
        if ($weatherData['temperature'] < 5) {
            $alerts[] = 'Cold conditions - dress warmly and bring extra layers';
        } elseif ($weatherData['temperature'] > 35) {
            $alerts[] = 'Hot conditions - bring extra water and sun protection';
        }

        // Visibility alerts
        if (isset($weatherData['visibility']) && $weatherData['visibility'] < 1000) {
            $alerts[] = 'Poor visibility - consider postponing hike';
        }

        return empty($alerts) ? null : implode('; ', $alerts);
    }

    /**
     * Get current weather data for AJAX updates
     */
    public function getCurrentWeather(Request $request): JsonResponse
    {
        try {
            // Get user's location or default to Manila, Philippines
            $lat = $request->get('lat', 14.5995);  // Manila latitude
            $lon = $request->get('lon', 120.9842); // Manila longitude
            
            // Get current weather from OpenWeather API
            $weather = $this->getOpenWeatherData($lat, $lon);

            if (! $weather) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unable to fetch weather data'
                ], 500);
            }

            // Build frontend-friendly current weather object from our normalized data
            $formattedWeather = [
                'temp' => isset($weather['temperature']) ? round($weather['temperature']) : null,
                'feels_like' => $weather['feels_like'] ?? null,
                'humidity' => $weather['humidity'] ?? null,
                'description' => $weather['description'] ?? ($weather['condition'] ?? null),
                'icon' => $weather['icon'] ?? null,
                'city' => $weather['location']['name'] ?? ($weather['location']['city'] ?? 'Unknown'),
                'uv_index' => $weather['uvIndex'] ?? 0,
                'condition' => $weather['condition_code'] ?? ($weather['condition'] ?? null),
                'is_day' => $this->isDayTime($weather),
                'gradient' => $this->getWeatherGradient($weather['condition'] ?? ''),
            ];

            // Get a simple 5-day forecast using the forecast endpoint (keep separate to avoid OneCall dependency)
            try {
                $forecast = $this->getOpenWeatherForecast($lat, $lon);
                $formattedForecast = $this->formatForecastForAjax($forecast['forecast'] ?? $forecast['daily'] ?? []);
            } catch (\Exception $e) {
                $formattedForecast = [];
            }

            return response()->json([
                'success' => true,
                'weather' => $formattedWeather,
                'forecast' => $formattedForecast,
                'updated_at' => now()->setTimezone('Asia/Manila')->toISOString()
            ]);

        } catch (\Exception $e) {
            Log::error('Weather API Error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Weather data temporarily unavailable'
            ], 500);
        }
    }

    /**
     * Format forecast data for AJAX frontend consumption
     */
    private function formatForecastForAjax($dailyData): array
    {
        if (empty($dailyData)) {
            return [];
        }

        $formattedForecast = [];
        $count = 0;

        foreach ($dailyData as $day) {
            if ($count >= 5) break; // Only return 5 days

            // Support multiple shapes: our forecast summary, OpenWeather 'daily' from OneCall, or 3-hour list grouped
            if (isset($day['date']) && isset($day['temp'])) {
                // Already in our simplified format
                $formattedForecast[] = [
                    'date' => $day['date'],
                    'temp' => round($day['temp']),
                    'condition' => $day['condition'] ?? ($day['weather'][0]['description'] ?? null),
                    'icon' => $day['icon'] ?? ($day['weather'][0]['icon'] ?? null),
                ];
            } elseif (isset($day['dt']) && isset($day['temp'])) {
                // OpenWeather OneCall daily entry
                $date = Carbon::createFromTimestamp($day['dt'])->format('l, M j');
                $tempDay = is_array($day['temp']) ? ($day['temp']['day'] ?? $day['temp']) : $day['temp'];

                $formattedForecast[] = [
                    'date' => $date,
                    'temp' => round($tempDay),
                    'condition' => $day['weather'][0]['description'] ?? null,
                    'icon' => $day['weather'][0]['icon'] ?? null,
                ];
            } else {
                // Fallback: try to extract from possible list entries
                $date = isset($day['dt_txt']) ? Carbon::parse($day['dt_txt'])->format('l, M j') : (isset($day['date']) ? $day['date'] : null);
                $temp = $day['main']['temp'] ?? ($day['temp'] ?? null);
                $condition = $day['weather'][0]['description'] ?? null;
                $icon = $day['weather'][0]['icon'] ?? null;

                if ($date === null) continue;

                $formattedForecast[] = [
                    'date' => $date,
                    'temp' => isset($temp) ? round($temp) : null,
                    'condition' => $condition,
                    'icon' => $icon,
                ];
            }

            $count++;
        }

        return $formattedForecast;
    }

    /**
     * Check if it's daytime based on weather data
     */
    private function isDayTime($weatherData): bool
    {
        // If weatherData contains sunrise/sunset timestamps (OpenWeather), use them
        if (is_array($weatherData) && (isset($weatherData['sunrise']) || isset($weatherData['sunset']))) {
            $currentTime = time();
            $sunrise = $weatherData['sunrise'] ?? 0;
            $sunset = $weatherData['sunset'] ?? PHP_INT_MAX;

            return $currentTime >= $sunrise && $currentTime <= $sunset;
        }

        // Otherwise, approximate daytime based on hour in Manila
        $hour = now()->setTimezone('Asia/Manila')->hour;
        return $hour >= 6 && $hour <= 18;
    }
}

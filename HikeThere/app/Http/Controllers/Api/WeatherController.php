<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

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
            'temperature' => round($data['main']['temp'] ?? 0),
            'humidity' => $data['main']['humidity'] ?? 0,
            'windSpeed' => round(($data['wind']['speed'] ?? 0) * 3.6), // Convert m/s to km/h
            'uvIndex' => $this->getUVIndex($lat, $lng),
            'timestamp' => $philippineTime->toISOString(),
            'location' => [
                'lat' => $lat,
                'lng' => $lng,
            ],
            'description' => $data['weather'][0]['description'] ?? '',
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
}

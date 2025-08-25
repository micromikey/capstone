<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
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

        return [
            'condition' => $this->mapWeatherCondition($data['weather'][0]['main'] ?? 'Unknown'),
            'temperature' => round($data['main']['temp'] ?? 0),
            'humidity' => $data['main']['humidity'] ?? 0,
            'windSpeed' => round(($data['wind']['speed'] ?? 0) * 3.6), // Convert m/s to km/h
            'uvIndex' => $this->getUVIndex($lat, $lng),
            'timestamp' => now()->toISOString(),
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
}

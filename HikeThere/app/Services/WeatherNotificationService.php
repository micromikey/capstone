<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WeatherNotificationService
{
    protected $notificationService;
    
    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Send weather notification to user on login
     * Includes current location weather and latest itinerary trail weather
     */
    public function sendLoginWeatherNotification(User $user)
    {
        try {
            $weatherData = $this->prepareWeatherData($user);
            
            if (!empty($weatherData)) {
                return $this->notificationService->sendWeatherNotification($user, $weatherData);
            }
            
            return null;
        } catch (\Exception $e) {
            Log::error('Failed to send weather notification: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Prepare weather data for notification
     */
    protected function prepareWeatherData(User $user): array
    {
        $weatherData = [];

        Log::info('WeatherNotificationService: Preparing weather data for user', [
            'user_id' => $user->id,
            'user_location' => $user->location
        ]);

        // Get current location weather (using user's location or default to Manila)
        $currentWeather = $this->getCurrentLocationWeather($user);
        if ($currentWeather) {
            $weatherData['current_temp'] = $currentWeather['temperature'];
            $weatherData['current_location'] = $currentWeather['location'];
            $weatherData['current_condition'] = $currentWeather['condition'];
            $weatherData['weather_icon'] = $currentWeather['icon'];
            
            Log::info('WeatherNotificationService: Current weather fetched', [
                'temp' => $currentWeather['temperature'],
                'location' => $currentWeather['location']
            ]);
        }

        // Get latest itinerary trail weather
        $latestItinerary = $user->latestItinerary;
        
        Log::info('WeatherNotificationService: Checking latest itinerary', [
            'has_itinerary' => !is_null($latestItinerary),
            'itinerary_id' => $latestItinerary ? $latestItinerary->id : null
        ]);
        
        if ($latestItinerary && $latestItinerary->trail) {
            Log::info('WeatherNotificationService: Found trail', [
                'trail_id' => $latestItinerary->trail->id,
                'trail_name' => $latestItinerary->trail->name,
                'latitude' => $latestItinerary->trail->latitude,
                'longitude' => $latestItinerary->trail->longitude
            ]);
            
            $trailWeather = $this->getTrailWeather($latestItinerary->trail);
            if ($trailWeather) {
                $trailName = $latestItinerary->trail->name ?: 
                            $latestItinerary->trail_name ?:
                            $latestItinerary->title ?: 
                            'Your Trail';
                            
                $weatherData['trail_temp'] = $trailWeather['temperature'];
                $weatherData['trail_name'] = $trailName;
                $weatherData['trail_condition'] = $trailWeather['condition'];
                $weatherData['itinerary_id'] = $latestItinerary->id;
                
                Log::info('WeatherNotificationService: Trail weather fetched', [
                    'temp' => $trailWeather['temperature'],
                    'trail_name' => $latestItinerary->trail->name
                ]);
            } else {
                Log::warning('WeatherNotificationService: Failed to fetch trail weather');
            }
        } else {
            Log::warning('WeatherNotificationService: No itinerary or trail found for user');
        }

        return $weatherData;
    }

    /**
     * Get current location weather
     */
    protected function getCurrentLocationWeather(User $user): ?array
    {
        try {
            // Try multiple approaches to get user's location
            $coords = null;
            $locationLabel = 'Current Location';
            $locationSource = 'unknown';
            
            // 1. First, try to use user's saved location from profile
            if ($user->location) {
                $coords = $this->getCoordinatesFromLocation($user->location);
                if ($coords) {
                    $locationLabel = $user->location;
                    $locationSource = 'user_profile';
                    Log::info('WeatherNotificationService: Using user profile location', [
                        'location' => $user->location,
                        'coords' => $coords
                    ]);
                }
            }
            
            // 2. If no location found, try IP-based geolocation
            if (!$coords) {
                $ipLocation = $this->getLocationFromIP();
                if ($ipLocation) {
                    $coords = $ipLocation['coords'];
                    $locationLabel = $ipLocation['location'] ?? 'Current Location';
                    $locationSource = 'ip_geolocation';
                    Log::info('WeatherNotificationService: Using IP-based location', [
                        'location' => $locationLabel,
                        'coords' => $coords
                    ]);
                }
            }
            
            // 3. Fall back to Manila if nothing else works
            if (!$coords) {
                $coords = ['lat' => 14.5995, 'lng' => 120.9842];
                $locationLabel = 'Manila';
                $locationSource = 'default';
                Log::info('WeatherNotificationService: Using default Manila location', [
                    'coords' => $coords
                ]);
            }

            Log::info('WeatherNotificationService: Final coordinates to fetch weather', [
                'lat' => $coords['lat'],
                'lng' => $coords['lng'],
                'source' => $locationSource,
                'expected_location' => $locationLabel
            ]);

            $weather = $this->fetchWeatherFromAPI($coords['lat'], $coords['lng']);
            
            if ($weather) {
                // CRITICAL FIX: Prioritize user's location label over API location name
                // The API might return a generic or incorrect location name
                // Only use API location if we used default coordinates or IP geolocation failed
                $actualLocation = $locationLabel;
                
                // Only override with API location name if:
                // 1. We're using default Manila coordinates, OR
                // 2. We have no meaningful location label
                if ($locationSource === 'default' || $locationLabel === 'Current Location') {
                    $actualLocation = $weather['location_name'] ?? $locationLabel;
                }
                
                Log::info('WeatherNotificationService: Weather fetched successfully', [
                    'temperature' => $weather['temperature'],
                    'api_location_name' => $weather['location_name'] ?? 'N/A',
                    'final_location' => $actualLocation,
                    'coords_used' => ['lat' => $coords['lat'], 'lng' => $coords['lng']]
                ]);
                
                return [
                    'temperature' => round($weather['temperature'], 1),
                    'location' => $actualLocation,
                    'condition' => $weather['condition'],
                    'icon' => $weather['icon']
                ];
            }

            return null;
        } catch (\Exception $e) {
            Log::error('Failed to get current location weather: ' . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Get location from IP address
     */
    protected function getLocationFromIP(): ?array
    {
        try {
            $ip = request()->ip();
            
            Log::info('WeatherNotificationService: Attempting IP geolocation', ['ip' => $ip]);
            
            // Skip local IPs - but still try alternative geolocation
            if (in_array($ip, ['127.0.0.1', '::1', 'localhost'])) {
                Log::info('WeatherNotificationService: Local IP detected, cannot geolocate');
                
                // For local development, return null to use fallback
                return null;
            }
            
            // Use ipapi.co for geolocation (free, no API key needed)
            $response = Http::timeout(5)->get("https://ipapi.co/{$ip}/json/");
            
            if ($response->successful()) {
                $data = $response->json();
                
                Log::info('WeatherNotificationService: IP API response', [
                    'full_response' => $data
                ]);
                
                if (isset($data['latitude']) && isset($data['longitude'])) {
                    // Build a more complete location label
                    $locationParts = array_filter([
                        $data['city'] ?? null,
                        $data['region'] ?? null,
                        $data['country_name'] ?? null
                    ]);
                    
                    $locationLabel = !empty($locationParts) 
                        ? implode(', ', $locationParts) 
                        : 'Current Location';
                    
                    Log::info('WeatherNotificationService: IP geolocation successful', [
                        'location' => $locationLabel,
                        'lat' => $data['latitude'],
                        'lng' => $data['longitude'],
                        'country' => $data['country_name'] ?? 'Unknown'
                    ]);
                    
                    return [
                        'coords' => [
                            'lat' => $data['latitude'],
                            'lng' => $data['longitude']
                        ],
                        'location' => $locationLabel
                    ];
                }
            } else {
                Log::warning('WeatherNotificationService: IP geolocation API failed', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
            }
            
            Log::info('WeatherNotificationService: IP geolocation failed, no coordinates returned');
            return null;
        } catch (\Exception $e) {
            Log::warning('Failed to get location from IP: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Get trail weather
     */
    protected function getTrailWeather($trail): ?array
    {
        try {
            if (!$trail->latitude || !$trail->longitude) {
                Log::warning('WeatherNotificationService: Trail missing coordinates', [
                    'trail_id' => $trail->id,
                    'trail_name' => $trail->name
                ]);
                return null;
            }

            Log::info('WeatherNotificationService: Fetching weather for trail', [
                'trail_id' => $trail->id,
                'latitude' => $trail->latitude,
                'longitude' => $trail->longitude
            ]);

            $weather = $this->fetchWeatherFromAPI($trail->latitude, $trail->longitude);
            
            if ($weather) {
                return [
                    'temperature' => round($weather['temperature'], 1),
                    'condition' => $weather['condition'],
                    'icon' => $weather['icon']
                ];
            }

            return null;
        } catch (\Exception $e) {
            Log::error('Failed to get trail weather: ' . $e->getMessage(), [
                'trail_id' => $trail->id ?? null
            ]);
            return null;
        }
    }

    /**
     * Fetch weather data from OpenWeather API
     */
    protected function fetchWeatherFromAPI(float $lat, float $lng): ?array
    {
        try {
            $apiKey = config('services.openweather.api_key') ?? env('OPENWEATHER_API_KEY');

            if (!$apiKey) {
                Log::warning('WeatherNotificationService: OpenWeather API key not configured');
                return null;
            }

            Log::info('WeatherNotificationService: Calling OpenWeather API', [
                'lat' => $lat,
                'lng' => $lng
            ]);

            $response = Http::timeout(5)->get('https://api.openweathermap.org/data/2.5/weather', [
                'lat' => $lat,
                'lon' => $lng,
                'appid' => $apiKey,
                'units' => 'metric',
            ]);

            if (!$response->successful()) {
                Log::error('WeatherNotificationService: API request failed', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                return null;
            }

            $data = $response->json();

            return [
                'temperature' => $data['main']['temp'] ?? 0,
                'condition' => $data['weather'][0]['main'] ?? 'Clear',
                'icon' => $data['weather'][0]['icon'] ?? null,
                'description' => $data['weather'][0]['description'] ?? '',
                'location_name' => $data['name'] ?? null, // Actual location name from API
            ];
        } catch (\Exception $e) {
            Log::error('Failed to fetch weather from API: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Get coordinates from location string
     */
    protected function getCoordinatesFromLocation(string $location): ?array
    {
        // Comprehensive mapping for Philippine cities and popular locations
        $cityCoordinates = [
            // NCR
            'manila' => ['lat' => 14.5995, 'lng' => 120.9842],
            'quezon city' => ['lat' => 14.6760, 'lng' => 121.0437],
            'makati' => ['lat' => 14.5547, 'lng' => 121.0244],
            'pasig' => ['lat' => 14.5764, 'lng' => 121.0851],
            'taguig' => ['lat' => 14.5176, 'lng' => 121.0509],
            'paranaque' => ['lat' => 14.4793, 'lng' => 121.0198],
            'las pinas' => ['lat' => 14.4453, 'lng' => 120.9820],
            'muntinlupa' => ['lat' => 14.4081, 'lng' => 121.0414],
            
            // Luzon
            'baguio' => ['lat' => 16.4023, 'lng' => 120.5960],
            'tagaytay' => ['lat' => 14.1053, 'lng' => 120.9621],
            'batangas' => ['lat' => 13.7565, 'lng' => 121.0583],
            'laguna' => ['lat' => 14.2691, 'lng' => 121.4113],
            'pampanga' => ['lat' => 15.0794, 'lng' => 120.6200],
            'cavite' => ['lat' => 14.2456, 'lng' => 120.8783],
            'bulacan' => ['lat' => 14.7942, 'lng' => 120.8794],
            'rizal' => ['lat' => 14.6037, 'lng' => 121.3084],
            
            // Visayas
            'cebu' => ['lat' => 10.3157, 'lng' => 123.8854],
            'iloilo' => ['lat' => 10.7202, 'lng' => 122.5621],
            'bacolod' => ['lat' => 10.6770, 'lng' => 122.9500],
            'tacloban' => ['lat' => 11.2500, 'lng' => 125.0000],
            
            // Mindanao
            'davao' => ['lat' => 7.1907, 'lng' => 125.4553],
            'cagayan de oro' => ['lat' => 8.4542, 'lng' => 124.6319],
            'zamboanga' => ['lat' => 6.9214, 'lng' => 122.0790],
            'general santos' => ['lat' => 6.1164, 'lng' => 125.1716],
        ];

        $location = strtolower(trim($location));
        
        Log::info('WeatherNotificationService: Looking up coordinates for location', [
            'original_location' => $location
        ]);
        
        // Check for exact match first
        if (isset($cityCoordinates[$location])) {
            Log::info('WeatherNotificationService: Exact match found', [
                'location' => $location,
                'coords' => $cityCoordinates[$location]
            ]);
            return $cityCoordinates[$location];
        }
        
        // Check for partial match
        foreach ($cityCoordinates as $city => $coords) {
            if (str_contains($location, $city) || str_contains($city, $location)) {
                Log::info('WeatherNotificationService: Partial match found', [
                    'search' => $location,
                    'matched' => $city,
                    'coords' => $coords
                ]);
                return $coords;
            }
        }

        Log::info('WeatherNotificationService: No match found for location', [
            'location' => $location
        ]);
        
        return null;
    }

    /**
     * Check if weather notification should be sent
     * (e.g., not sent if user already received one in the last 6 hours)
     */
    public function shouldSendWeatherNotification(User $user): bool
    {
        // Check if user already received a weather notification in the last 6 hours
        $sixHoursAgo = now()->subHours(6);
        
        $existingNotification = $user->notifications()
            ->where('type', 'weather')
            ->where('created_at', '>=', $sixHoursAgo)
            ->exists();

        return !$existingNotification;
    }
}

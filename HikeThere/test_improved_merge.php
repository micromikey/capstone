<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Http\Controllers\Api\WeatherController;
use App\Services\WeatherHelperService;
use App\Models\Trail;
use App\Models\Itinerary;
use Illuminate\Http\Request;
use Carbon\Carbon;

echo "=== TESTING IMPROVED WEATHER DATA MERGE ===\n\n";

$itinerary = Itinerary::first();
$trail = Trail::first();

// Get original API weather data
$weatherController = new WeatherController();
$request = new Request(['lat' => $trail->latitude, 'lng' => $trail->longitude]);
$response = $weatherController->getForecast($request);
$freshWeatherData = json_decode($response->getContent(), true);

// Simulate improved merge logic
function formatWeatherDataForItinerary($apiWeatherData, $startDate, $durationDays) {
    $formattedData = [];
    foreach ($apiWeatherData['forecast'] as $dayIndex => $dayData) {
        if ($dayIndex >= $durationDays) break;
        
        $dayNumber = $dayIndex + 1;
        $condition = $dayData['condition'] ?? 'Fair';
        $temp = $dayData['temp_midday'] ?? $dayData['temp_max'] ?? 25;
        $defaultWeather = $condition . ' / ' . round($temp) . '°C';
        
        $formattedData[$dayNumber] = [
            '08:00' => $defaultWeather,
            '12:00' => $defaultWeather,
            '16:00' => $defaultWeather,
            '20:00' => $defaultWeather,
        ];
    }
    return $formattedData;
}

$formattedWeatherData = formatWeatherDataForItinerary($freshWeatherData, $itinerary->start_date, 1);

// Improved merge: start with API data, add formatted day data
$weatherData = $freshWeatherData;
foreach ($formattedWeatherData as $dayKey => $dayData) {
    $weatherData[$dayKey] = $dayData;
}

echo "Improved merged data:\n";
echo "Has 'forecast' key: " . (isset($weatherData['forecast']) ? 'YES' : 'NO') . "\n";
echo "Has day keys: " . (isset($weatherData[1]) ? 'YES' : 'NO') . "\n";
echo "Keys: " . implode(', ', array_keys($weatherData)) . "\n\n";

// Test dynamic weather
$weatherHelper = new WeatherHelperService();
$activities = $itinerary->daily_schedule[0]['activities'] ?? [];

echo "=== TESTING DYNAMIC WEATHER WITH IMPROVED MERGE ===\n\n";

foreach ($activities as $i => $activity) {
    $minutes = $activity['minutes'];
    $timeLabel = Carbon::parse('08:00')->addMinutes($minutes)->format('H:i');
    
    echo "Activity " . ($i+1) . ": {$activity['title']} at {$timeLabel}\n";
    
    $dynamicWeather = $weatherHelper->getWeatherFor($weatherData, 1, $timeLabel, $activity, $trail);
    echo "Dynamic weather result: " . ($dynamicWeather ?? 'NULL') . "\n";
    
    // Also test fallback
    $fallbackWeather = $weatherHelper->getWeatherFor($weatherData, 1, $timeLabel);
    echo "Fallback weather result: " . ($fallbackWeather ?? 'NULL') . "\n\n";
}

echo "✅ IMPROVED WEATHER MERGE TEST COMPLETE!\n";
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

echo "=== TESTING WEATHER DATA MERGE FIX ===\n\n";

$itinerary = Itinerary::first();
$trail = Trail::first();

// Get original API weather data (like controller does)
$weatherController = new WeatherController();
$request = new Request(['lat' => $trail->latitude, 'lng' => $trail->longitude]);
$response = $weatherController->getForecast($request);
$freshWeatherData = json_decode($response->getContent(), true);

echo "Original API weather data:\n";
echo "Has 'forecast' key: " . (isset($freshWeatherData['forecast']) ? 'YES' : 'NO') . "\n";
echo "Keys: " . implode(', ', array_keys($freshWeatherData)) . "\n\n";

// Simulate the formatWeatherDataForItinerary method
function formatWeatherDataForItinerary($apiWeatherData, $startDate, $durationDays) {
    if (!isset($apiWeatherData['forecast']) || !is_array($apiWeatherData['forecast'])) {
        return [];
    }

    $formattedData = [];
    foreach ($apiWeatherData['forecast'] as $dayIndex => $dayData) {
        if ($dayIndex >= $durationDays) break;
        
        $dayNumber = $dayIndex + 1;
        $formattedData[$dayNumber] = [];
        
        // Create default weather string
        $condition = $dayData['condition'] ?? 'Fair';
        $temp = $dayData['temp_midday'] ?? $dayData['temp_max'] ?? 25;
        $defaultWeather = $condition . ' / ' . round($temp) . '°C';
        
        $formattedData[$dayNumber]['08:00'] = $defaultWeather;
        $formattedData[$dayNumber]['12:00'] = $defaultWeather;
        $formattedData[$dayNumber]['16:00'] = $defaultWeather;
        $formattedData[$dayNumber]['20:00'] = $defaultWeather;
    }
    
    return $formattedData;
}

// Test the old way (controller was doing this)
$formattedData = formatWeatherDataForItinerary($freshWeatherData, $itinerary->start_date, 1);
echo "Old formatted data (loses forecast):\n";
echo "Has 'forecast' key: " . (isset($formattedData['forecast']) ? 'YES' : 'NO') . "\n";
echo "Has day keys: " . (isset($formattedData[1]) ? 'YES' : 'NO') . "\n";
echo "Keys: " . implode(', ', array_keys($formattedData)) . "\n\n";

// Test the new way (with merge)
$mergedData = array_merge($freshWeatherData, $formattedData);
echo "New merged data (keeps both):\n";
echo "Has 'forecast' key: " . (isset($mergedData['forecast']) ? 'YES' : 'NO') . "\n";
echo "Has day keys: " . (isset($mergedData[1]) ? 'YES' : 'NO') . "\n";
echo "Keys: " . implode(', ', array_keys($mergedData)) . "\n\n";

// Test the weather helper with merged data
$weatherHelper = new WeatherHelperService();
$activities = $itinerary->daily_schedule[0]['activities'] ?? [];

echo "=== TESTING DYNAMIC WEATHER WITH MERGED DATA ===\n\n";

foreach ($activities as $i => $activity) {
    $minutes = $activity['minutes'];
    $timeLabel = Carbon::parse('08:00')->addMinutes($minutes)->format('H:i');
    
    echo "Activity " . ($i+1) . ": {$activity['title']} at {$timeLabel}\n";
    
    $dynamicWeather = $weatherHelper->getWeatherFor($mergedData, 1, $timeLabel, $activity, $trail);
    echo "Dynamic weather result: " . ($dynamicWeather ?? 'NULL') . "\n\n";
}

echo "✅ WEATHER MERGE TEST COMPLETE!\n";
<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== FINAL WEATHER SYSTEM TEST ===\n\n";

// Simulate exact same conditions as the web interface
$itinerary = App\Models\Itinerary::first();
$trail = App\Models\Trail::find($itinerary->trail_id);

// Get fresh weather data exactly like ItineraryController does
$weatherController = new \App\Http\Controllers\Api\WeatherController();
$weatherRequest = new \Illuminate\Http\Request([
    'lat' => $trail->latitude,
    'lng' => $trail->longitude
]);

$weatherResponse = $weatherController->getForecast($weatherRequest);
$freshWeatherData = $weatherResponse->getData(true);

// Apply the same logic as the fixed ItineraryController
function formatWeatherDataForItinerary($apiWeatherData, $startDate, $durationDays) {
    if (!isset($apiWeatherData['forecast']) || !is_array($apiWeatherData['forecast'])) {
        return [];
    }

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

// Apply the exact same merge logic as the fixed controller
$weatherData = $freshWeatherData;
foreach ($formattedWeatherData as $dayKey => $dayData) {
    $weatherData[$dayKey] = $dayData;
}

echo "Final weather data structure:\n";
echo "✅ Has 'forecast' key: " . (isset($weatherData['forecast']) ? 'YES' : 'NO') . "\n";
echo "✅ Has day keys: " . (isset($weatherData[1]) ? 'YES' : 'NO') . "\n";
echo "✅ Forecast count: " . count($weatherData['forecast'] ?? []) . "\n";
echo "✅ Today's base condition: " . ($weatherData['forecast'][0]['condition'] ?? 'N/A') . "\n\n";

// Test the weather helper exactly like the day-table component does
$weatherHelper = new \App\Services\WeatherHelperService();
$activities = $itinerary->daily_schedule[0]['activities'] ?? [];

echo "=== SIMULATING DAY-TABLE COMPONENT WEATHER CALLS ===\n\n";

foreach ($activities as $index => $activity) {
    $activity = (array) $activity;
    $minutes = intval($activity['minutes'] ?? 0);
    
    // Simulate the exact same time calculation as the component
    $baseDateForDay = \Carbon\Carbon::parse($itinerary->start_date);
    $timeLabel = $weatherHelper->computeTimeForRow($baseDateForDay, $itinerary->start_time, 1, $minutes);
    
    echo "--- Row " . ($index + 1) . " ---\n";
    echo "Activity: {$activity['title']}\n";
    echo "Minutes offset: {$minutes}\n";  
    echo "Time: {$timeLabel}\n";
    
    // This is the EXACT call made by the day-table component
    $weatherLabel = $weatherHelper->getWeatherFor($weatherData, 1, $timeLabel, $activity, $trail) ?? 'Fair / 25°C';
    
    echo "Weather result: {$weatherLabel}\n";
    
    // Verify this is dynamic (should be different for different times)
    if (str_contains($weatherLabel, 'Light Rain') && !str_contains($weatherLabel, '25°C')) {
        echo "✅ Dynamic weather working!\n";
    } else {
        echo "❌ Still showing static fallback weather\n";
    }
    
    echo "\n";
}

echo "=== SUMMARY ===\n";
echo "If all activities show 'Light Rain' with DIFFERENT temperatures, then weather fix is working!\n";
echo "If any show 'Fair / 25°C', then there's still a fallback issue.\n";
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

echo "=== DEBUGGING WEATHER FALLBACK ISSUE ===\n\n";

$itinerary = Itinerary::first();
$trail = Trail::first();
$activities = $itinerary->daily_schedule[0]['activities'] ?? [];

// Get weather data exactly like the controller does
$weatherController = new WeatherController();
$request = new Request(['lat' => $trail->latitude, 'lng' => $trail->longitude]);
$response = $weatherController->getForecast($request);
$weatherData = json_decode($response->getContent(), true);

echo "Weather data structure:\n";
echo "Has 'forecast' key: " . (isset($weatherData['forecast']) ? 'YES' : 'NO') . "\n";
echo "Forecast count: " . count($weatherData['forecast'] ?? []) . "\n";
echo "Weather data keys: " . implode(', ', array_keys($weatherData)) . "\n\n";

$weatherHelper = new WeatherHelperService();

foreach ($activities as $i => $activity) {
    $minutes = $activity['minutes'];
    $timeLabel = Carbon::parse('08:00')->addMinutes($minutes)->format('H:i');
    
    echo "--- Activity " . ($i+1) . " ({$activity['title']}) ---\n";
    echo "Time: {$timeLabel} (+{$minutes} min)\n";
    
    // Test the weather method step by step
    echo "Testing getWeatherFor method...\n";
    
    // Test with dynamic parameters
    $weatherResult = $weatherHelper->getWeatherFor($weatherData, 1, $timeLabel, $activity, $trail);
    echo "Result: " . ($weatherResult ?? 'NULL') . "\n";
    
    // Test without dynamic parameters (old way)
    $staticResult = $weatherHelper->getWeatherFor($weatherData, 1, $timeLabel);
    echo "Static fallback result: " . ($staticResult ?? 'NULL') . "\n";
    
    echo "\n";
}

echo "=== DEBUG COMPLETE ===\n";
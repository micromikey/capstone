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

echo "=== TESTING UPDATED ACTIVITIES WITH DYNAMIC WEATHER ===\n\n";

$itinerary = Itinerary::first();
$trail = Trail::first();
$activities = $itinerary->daily_schedule[0]['activities'] ?? [];

// Get weather data
$weatherController = new WeatherController();
$request = new Request(['lat' => $trail->latitude, 'lng' => $trail->longitude]);
$response = $weatherController->getForecast($request);
$weatherData = json_decode($response->getContent(), true);

$weatherHelper = new WeatherHelperService();

echo "🌤️  Base weather: {$weatherData['forecast'][0]['condition']} ({$weatherData['forecast'][0]['temp_min']}°C - {$weatherData['forecast'][0]['temp_max']}°C)\n\n";

foreach ($activities as $i => $activity) {
    $minutes = $activity['minutes'];
    $timeLabel = Carbon::parse('08:00')->addMinutes($minutes)->format('H:i');
    
    echo "--- Activity " . ($i+1) . " ---\n";
    echo "🎯 {$activity['title']}\n";
    echo "📍 {$activity['location']}\n";
    echo "⏰ Time: {$timeLabel} (+{$minutes} min)\n";
    echo "🚶 Type: {$activity['type']}\n";
    
    // Test dynamic weather
    $dynamicWeather = $weatherHelper->getWeatherFor($weatherData, 1, $timeLabel, $activity, $trail);
    echo "🌤️  Weather: {$dynamicWeather}\n";
    
    // Test notes generation
    $activityNotes = $weatherHelper->generateIntelligentNote($activity, $dynamicWeather);
    $weatherAdvice = $weatherHelper->getWeatherPreparationAdvice($dynamicWeather, $activity['type'], $activity['title']);
    
    echo "📝 Activity Notes: " . ($activityNotes ?: 'None') . "\n";
    echo "💡 Weather Advice: " . substr($weatherAdvice ?: 'None', 0, 60) . "...\n";
    echo "\n";
}

echo "✅ DYNAMIC WEATHER TEST WITH UPDATED ACTIVITIES COMPLETE!\n";
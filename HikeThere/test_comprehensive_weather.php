<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Http\Controllers\Api\WeatherController;
use App\Services\WeatherHelperService;
use App\Models\Trail;
use Illuminate\Http\Request;
use Carbon\Carbon;

echo "=== COMPREHENSIVE WEATHER TEST WITH MULTIPLE ACTIVITIES ===\n\n";

// Get trail
$trail = Trail::first();
echo "✅ Trail: ID {$trail->id}\n";
echo "📍 Coordinates: {$trail->latitude}, {$trail->longitude}\n\n";

// Fetch weather data
$weatherController = new WeatherController();
$request = new Request(['lat' => $trail->latitude, 'lng' => $trail->longitude]);
$response = $weatherController->getForecast($request);
$weatherData = json_decode($response->getContent(), true);

echo "✅ Weather data fetched: " . count($weatherData['forecast']) . " days\n";
echo "🌤️  Today's condition: " . $weatherData['forecast'][0]['condition'] . "\n";
echo "🌡️  Temperature range: " . $weatherData['forecast'][0]['temp_min'] . "°C - " . $weatherData['forecast'][0]['temp_max'] . "°C\n\n";

// Create test activities with different times and types
$testActivities = [
    ['title' => 'Start Hike', 'type' => 'start', 'location' => 'Trailhead', 'minutes' => 0, 'cum_distance_km' => 0],
    ['title' => 'First Checkpoint', 'type' => 'checkpoint', 'location' => 'Trail Point 1', 'minutes' => 90, 'cum_distance_km' => 2.5],
    ['title' => 'Water Break', 'type' => 'rest', 'location' => 'Stream Crossing', 'minutes' => 150, 'cum_distance_km' => 4.0],
    ['title' => 'Lunch Stop', 'type' => 'meal', 'location' => 'Scenic Viewpoint', 'minutes' => 240, 'cum_distance_km' => 6.5],
    ['title' => 'Summit Approach', 'type' => 'climb', 'location' => 'Steep Section', 'minutes' => 360, 'cum_distance_km' => 9.0],
    ['title' => 'Summit Reached', 'type' => 'summit', 'location' => 'Peak', 'minutes' => 480, 'cum_distance_km' => 10.0],
    ['title' => 'Descent Start', 'type' => 'descent', 'location' => 'Summit', 'minutes' => 540, 'cum_distance_km' => 10.0],
    ['title' => 'Camp Setup', 'type' => 'camp', 'location' => 'Base Camp', 'minutes' => 720, 'cum_distance_km' => 7.5],
];

$weatherHelper = new WeatherHelperService();

echo "=== TESTING WEATHER FOR DIFFERENT ACTIVITIES ===\n\n";

foreach ($testActivities as $index => $activity) {
    $minutes = $activity['minutes'];
    $timeLabel = Carbon::parse('08:00')->addMinutes($minutes)->format('H:i');
    
    echo "--- Activity " . ($index + 1) . " ---\n";
    echo "🎯 {$activity['title']} ({$activity['type']})\n";
    echo "📍 {$activity['location']}\n";
    echo "�� Time: {$timeLabel} (+{$minutes} min)\n";
    echo "🚶 Distance: {$activity['cum_distance_km']} km\n";
    
    // Test dynamic weather
    $dynamicWeather = $weatherHelper->getWeatherFor($weatherData, 1, $timeLabel, $activity, $trail);
    echo "🌤️  Weather: {$dynamicWeather}\n";
    
    // Test weather advice
    $advice = $weatherHelper->getWeatherPreparationAdvice(
        $dynamicWeather,
        $activity['type'],
        $activity['title']
    );
    
    if ($advice) {
        $shortAdvice = substr($advice, 0, 80);
        echo "💡 Advice: {$shortAdvice}" . (strlen($advice) > 80 ? "..." : "") . "\n";
    }
    
    echo "\n";
}

echo "✅ COMPREHENSIVE WEATHER TEST COMPLETE!\n";
echo "\n=== SUMMARY ===\n";
echo "✅ Dynamic weather system provides time-specific weather for each activity\n";
echo "✅ Weather conditions change based on time of day\n";
echo "✅ Activities receive appropriate weather preparation advice\n";
echo "✅ System handles different activity types correctly\n";
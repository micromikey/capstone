<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Http\Controllers\Api\WeatherController;
use App\Services\WeatherHelperService;
use App\Models\Trail;
use App\Models\Itinerary;
use Illuminate\Http\Request;
use Carbon\Carbon;

echo "=== DYNAMIC WEATHER SYSTEM TEST ===\n\n";

// Get an itinerary with activities
$itinerary = Itinerary::first();
if (!$itinerary) {
    echo "❌ No itinerary found\n";
    exit(1);
}

echo "✅ Itinerary found: {$itinerary->title}\n";
echo "📅 Start Date: {$itinerary->start_date}\n";

// Get trail
$trail = null;
if ($itinerary->trail_id) {
    $trail = Trail::find($itinerary->trail_id);
    echo "✅ Trail found: ID {$trail->id}\n";
    echo "📍 Main Coordinates: {$trail->latitude}, {$trail->longitude}\n";
} else {
    echo "❌ No trail associated\n";
    exit(1);
}

// Fetch fresh weather data
$weatherController = new WeatherController();
$request = new Request();
$request->merge([
    'lat' => $trail->latitude,
    'lng' => $trail->longitude
]);

$response = $weatherController->getForecast($request);
$weatherData = json_decode($response->getContent(), true);

if (isset($weatherData['error'])) {
    echo "❌ Weather API Error: {$weatherData['error']}\n";
    exit(1);
}

echo "✅ Weather data fetched successfully\n";
echo "📊 Forecast days: " . count($weatherData['forecast']) . "\n\n";

// Test weather helper service
$weatherHelper = new WeatherHelperService();

// Get activities from the itinerary
$activities = $itinerary->daily_schedule[0]['activities'] ?? [];
if (empty($activities)) {
    echo "❌ No activities found in itinerary\n";
    exit(1);
}

echo "=== TESTING DYNAMIC WEATHER FOR EACH ACTIVITY ===\n\n";

$startTime = Carbon::parse($itinerary->start_time ?? '08:00');
$baseDate = Carbon::parse($itinerary->start_date);

foreach ($activities as $index => $activity) {
    $activity = (array) $activity;
    $minutes = intval($activity['minutes'] ?? 0);
    
    // Calculate time for this activity
    $activityTime = $startTime->copy()->addMinutes($minutes);
    $timeLabel = $activityTime->format('H:i');
    
    echo "--- Activity " . ($index + 1) . " ---\n";
    echo "🎯 Title: " . ($activity['title'] ?? 'Unknown') . "\n";
    echo "📍 Location: " . ($activity['location'] ?? 'N/A') . "\n";
    echo "⏰ Time: {$timeLabel} (+" . $minutes . " minutes)\n";
    echo "🚶 Type: " . ($activity['type'] ?? 'activity') . "\n";
    
    // Test dynamic weather
    $dynamicWeather = $weatherHelper->getWeatherFor($weatherData, 1, $timeLabel, $activity, $trail);
    echo "🌤️  Dynamic Weather: " . ($dynamicWeather ?? 'N/A') . "\n";
    
    // Test weather advice
    if ($dynamicWeather) {
        $advice = $weatherHelper->getWeatherPreparationAdvice(
            $dynamicWeather,
            $activity['type'] ?? '',
            $activity['title'] ?? ''
        );
        
        if ($advice) {
            echo "💡 Weather Advice: " . substr($advice, 0, 100) . "...\n";
        }
    }
    
    echo "\n";
}

echo "✅ DYNAMIC WEATHER SYSTEM TEST COMPLETE!\n";
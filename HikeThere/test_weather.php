<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Http\Controllers\Api\WeatherController;
use App\Models\Trail;
use Illuminate\Http\Request;

echo "=== WEATHER API INTEGRATION TEST ===\n\n";

// Get trail data
$trail = Trail::first();
if (!$trail) {
    echo "❌ No trail found in database\n";
    exit(1);
}

echo "✅ Trail found: ID {$trail->id}\n";
echo "📍 Coordinates: {$trail->latitude}, {$trail->longitude}\n\n";

// Test weather API
$weatherController = new WeatherController();
$request = new Request();
$request->merge([
    'lat' => $trail->latitude,
    'lng' => $trail->longitude
]);

try {
    echo "🌤️  Fetching weather forecast...\n";
    $response = $weatherController->getForecast($request);
    
    if ($response->getStatusCode() === 200) {
        $data = json_decode($response->getContent(), true);
        
        echo "✅ Weather API Response: SUCCESS\n";
        echo "📅 Forecast days: " . count($data['forecast']) . "\n";
        echo "🌡️  Hourly entries: " . count($data['hourly']) . "\n\n";
        
        // Show first day forecast
        if (!empty($data['forecast'])) {
            $firstDay = $data['forecast'][0];
            echo "=== FIRST DAY FORECAST ===\n";
            echo "📅 Day: {$firstDay['day_label']} - {$firstDay['date_formatted']}\n";
            echo "🌤️  Condition: {$firstDay['condition']}\n";
            echo "🌡️  Temperature: {$firstDay['temp_min']}°C - {$firstDay['temp_max']}°C\n";
            echo "💧 Humidity: {$firstDay['humidity']}%\n";
            echo "💨 Wind: {$firstDay['wind_speed']} km/h\n";
            echo "🌧️  Precipitation: {$firstDay['precipitation']}%\n\n";
        }
        
        // Test weather helper service
        echo "=== TESTING WEATHER ADVICE ===\n";
        $weatherHelper = new \App\Services\WeatherHelperService();
        $weatherString = $firstDay['condition'] . ' ' . $firstDay['temp_max'] . '°C';
        $advice = $weatherHelper->getWeatherPreparationAdvice(
            $weatherString,
            'hiking',
            'Trail Activity'
        );
        
        echo "🎒 Weather Preparation Advice:\n";
        if ($advice) {
            echo "   {$advice}\n";
        } else {
            echo "   No specific advice for current conditions\n";
        }
        
        echo "\n✅ WEATHER INTEGRATION TEST PASSED!\n";
        
    } else {
        echo "❌ Weather API Error: HTTP {$response->getStatusCode()}\n";
        echo "Response: " . $response->getContent() . "\n";
    }
    
} catch (Exception $e) {
    echo "❌ Exception: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}
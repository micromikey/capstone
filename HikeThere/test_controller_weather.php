<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Http\Controllers\ItineraryController;
use App\Models\Itinerary;
use Illuminate\Http\Request;

echo "=== TESTING FIXED WEATHER DATA STRUCTURE ===\n\n";

// Simulate the same call as the web interface
$itinerary = Itinerary::first();
$controller = new ItineraryController();

// Use reflection to test the private method
$reflection = new ReflectionClass($controller);
$method = $reflection->getMethod('show');

// Mock the request
$request = Request::create('/itinerary/' . $itinerary->id);
$request->setUserResolver(function () {
    return \App\Models\User::find(2); // Assuming user ID 2 owns the itinerary
});

// Override the user for the request
app()->instance('auth', new class {
    public function id() { return 2; }
    public function user() { return \App\Models\User::find(2); }
});

try {
    // This will call the actual controller method that processes weather data
    $response = $controller->show($itinerary);
    
    // Extract the weather data that would be passed to the view
    $viewData = $response->getData();
    $weatherData = $viewData['weatherData'] ?? [];
    
    echo "Weather data structure after controller processing:\n";
    echo "Has 'forecast' key: " . (isset($weatherData['forecast']) ? 'YES' : 'NO') . "\n";
    echo "Has day keys (1, 2, etc.): " . (isset($weatherData[1]) ? 'YES' : 'NO') . "\n";
    echo "Weather data keys: " . implode(', ', array_keys($weatherData)) . "\n\n";
    
    if (isset($weatherData['forecast'])) {
        echo "✅ Original forecast data preserved for dynamic weather\n";
    } else {
        echo "❌ Original forecast data lost - dynamic weather won't work\n";
    }
    
    if (isset($weatherData[1])) {
        echo "✅ Formatted weather data available for backward compatibility\n";
    } else {
        echo "❌ Formatted weather data missing\n";
    }
    
} catch (Exception $e) {
    echo "Error testing controller: " . $e->getMessage() . "\n";
    echo "Let's test the weather data directly...\n\n";
    
    // Direct test of the weather processing
    $weatherController = new \App\Http\Controllers\Api\WeatherController();
    $trail = \App\Models\Trail::first();
    $request = new \Illuminate\Http\Request(['lat' => $trail->latitude, 'lng' => $trail->longitude]);
    $response = $weatherController->getForecast($request);
    $freshWeatherData = json_decode($response->getContent(), true);
    
    // Test the formatting method directly
    $reflection = new ReflectionClass(ItineraryController::class);
    $formatMethod = $reflection->getMethod('formatWeatherDataForItinerary');
    $formatMethod->setAccessible(true);
    
    $controllerInstance = new ItineraryController();
    $formattedData = $formatMethod->invoke($controllerInstance, $freshWeatherData, $itinerary->start_date, 1);
    
    // Simulate the merge
    $mergedData = array_merge($freshWeatherData, $formattedData);
    
    echo "Merged weather data:\n";
    echo "Has 'forecast' key: " . (isset($mergedData['forecast']) ? 'YES' : 'NO') . "\n";
    echo "Has day keys: " . (isset($mergedData[1]) ? 'YES' : 'NO') . "\n";
    echo "Keys: " . implode(', ', array_keys($mergedData)) . "\n";
}

echo "\n=== TEST COMPLETE ===\n";
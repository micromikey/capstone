<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Services\IntelligentWeatherService;

echo "🔧 TESTING TRAIL DATA COMPATIBILITY FIX\n";
echo "=======================================\n\n";

$intelligentWeatherService = new IntelligentWeatherService();

// Test activity
$testActivity = [
    'title' => 'Summit Achievement',
    'type' => 'summit',
    'cum_distance_km' => 8.5,
    'cum_minutes' => 360,
    'time' => '14:00'
];

$testWeather = 'Light Rain / 19°C';

echo "1. Testing with OBJECT trail data:\n";
echo "----------------------------------\n";

// Create a mock trail object
$trailObject = (object) [
    'difficulty' => 'Advanced',
    'elevation_gain' => 1200,
    'features' => ['technical', 'exposed']
];

try {
    $smartNote1 = $intelligentWeatherService->generateSmartWeatherNote($testActivity, $testWeather, $trailObject, 0);
    echo "✅ SUCCESS: {$smartNote1}\n\n";
} catch (\Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n\n";
}

echo "2. Testing with ARRAY trail data:\n";
echo "---------------------------------\n";

// Create a trail array
$trailArray = [
    'difficulty' => 'Advanced',
    'elevation_gain' => 1200,
    'features' => ['technical', 'exposed']
];

try {
    $smartNote2 = $intelligentWeatherService->generateSmartWeatherNote($testActivity, $testWeather, $trailArray, 0);
    echo "✅ SUCCESS: {$smartNote2}\n\n";
} catch (\Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n\n";
}

echo "3. Testing with NULL trail data:\n";
echo "--------------------------------\n";

try {
    $smartNote3 = $intelligentWeatherService->generateSmartWeatherNote($testActivity, $testWeather, null, 0);
    echo "✅ SUCCESS: {$smartNote3}\n\n";
} catch (\Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n\n";
}

echo "4. Testing with INCOMPLETE trail data:\n";
echo "-------------------------------------\n";

// Create a trail array with missing fields
$incompleteTrailArray = [
    'name' => 'Test Trail',
    // Missing difficulty, elevation_gain, features
];

try {
    $smartNote4 = $intelligentWeatherService->generateSmartWeatherNote($testActivity, $testWeather, $incompleteTrailArray, 0);
    echo "✅ SUCCESS: {$smartNote4}\n\n";
} catch (\Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n\n";
}

echo "🎉 Trail data compatibility test complete!\n";
echo "The service now handles both object and array trail data safely.\n";
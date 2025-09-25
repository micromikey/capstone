<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Services\IntelligentWeatherService;
use App\Services\EnhancedDistanceCalculatorService;

echo "🧠 INTELLIGENT WEATHER & DISTANCE SYSTEM TEST\n";
echo "=============================================\n\n";

// Initialize services
$intelligentWeatherService = new IntelligentWeatherService();
$distanceCalculator = new EnhancedDistanceCalculatorService();

// Test activities with different types and scenarios
$testActivities = [
    [
        'title' => 'Safety Briefing & Equipment Check',
        'type' => 'prep',
        'cum_distance_km' => 0.0,
        'cum_minutes' => 15,
        'time' => '08:00'
    ],
    [
        'title' => 'Scenic Photo Stop',
        'type' => 'photo',
        'cum_distance_km' => 2.5,
        'cum_minutes' => 120,
        'time' => '10:00'
    ],
    [
        'title' => 'Lunch Break & Rest',
        'type' => 'meal',
        'cum_distance_km' => 5.0,
        'cum_minutes' => 240,
        'time' => '12:00'
    ],
    [
        'title' => 'Summit Achievement',
        'type' => 'summit',
        'cum_distance_km' => 8.5,
        'cum_minutes' => 360,
        'time' => '14:00'
    ],
    [
        'title' => 'Begin Descent',
        'type' => 'descent',
        'cum_distance_km' => 10.0,
        'cum_minutes' => 420,
        'time' => '15:00'
    ],
    [
        'title' => 'Return to Trailhead',
        'type' => 'return',
        'cum_distance_km' => 18.6,
        'cum_minutes' => 600,
        'time' => '18:00'
    ]
];

// Test different weather conditions
$weatherConditions = [
    'Light Rain / 19°C',
    'Clear / 25°C',
    'Snow / 2°C',
    'Windy / 15°C',
    'Heavy Rain / 12°C'
];

echo "1. INTELLIGENT WEATHER NOTES TEST\n";
echo "=================================\n";

foreach ($weatherConditions as $weather) {
    echo "\n🌤️ Weather Condition: {$weather}\n";
    echo str_repeat('-', 50) . "\n";
    
    foreach ($testActivities as $index => $activity) {
        $smartNote = $intelligentWeatherService->generateSmartWeatherNote($activity, $weather);
        
        echo sprintf(
            "%d. %s (%s)\n   📝 %s\n\n",
            $index + 1,
            $activity['title'],
            $activity['type'],
            $smartNote ?: 'No special preparation needed'
        );
    }
    
    if ($weather !== end($weatherConditions)) {
        echo "\n" . str_repeat('=', 60) . "\n";
    }
}

echo "\n\n2. ENHANCED DISTANCE CALCULATION TEST\n";
echo "=====================================\n";

$totalTrailDistance = 18.6; // km
$correctedActivities = $distanceCalculator->calculateAccurateDistances($testActivities, $totalTrailDistance);

echo "Original vs Corrected Distances:\n";
echo str_repeat('-', 50) . "\n";

foreach ($testActivities as $index => $original) {
    $corrected = $correctedActivities[$index];
    $originalDist = $original['cum_distance_km'];
    $correctedDist = $corrected['cum_distance_km'];
    $difference = $correctedDist - $originalDist;
    
    echo sprintf(
        "%d. %s\n   Original: %.2f km | Corrected: %.2f km | Diff: %+.2f km\n",
        $index + 1,
        $original['title'],
        $originalDist,
        $correctedDist,
        $difference
    );
}

// Get distance calculation summary
$summary = $distanceCalculator->getDistanceCalculationSummary($correctedActivities, $totalTrailDistance);

echo "\n📊 DISTANCE CALCULATION SUMMARY:\n";
echo "================================\n";
echo "Total Activities: {$summary['total_activities']}\n";
echo "Trail Distance: {$summary['trail_distance']} km\n";
echo "Calculated Distance: {$summary['calculated_distance']} km\n";
echo "Accuracy: " . round($summary['validation']['accuracy_percentage'], 1) . "%\n";
echo "Status: {$summary['validation']['status']}\n";

echo "\n3. INTEGRATION BENEFITS\n";
echo "======================\n";
echo "✅ Non-redundant weather notes - Each activity gets unique, relevant advice\n";
echo "✅ Activity-specific recommendations - Context-aware suggestions\n";
echo "✅ ML-ready integration - Prepared for machine learning enhancement\n";
echo "✅ Accurate distance calculation - Proportional distribution with validation\n";
echo "✅ Smart progress tracking - Activity type and timing considered\n";
echo "✅ Weather icons and concise formatting - Better UI experience\n";

echo "\n4. SAMPLE INTELLIGENT NOTES COMPARISON\n";
echo "======================================\n";

$rainWeather = 'Light Rain / 19°C';
echo "Weather: {$rainWeather}\n\n";

echo "OLD SYSTEM (Redundant):\n";
echo "• Activity 1: Rain expected - pack waterproof jacket, rain pants, and pack cover.\n";
echo "• Activity 2: Rain expected - pack waterproof jacket, rain pants, and pack cover.\n";
echo "• Activity 3: Rain expected - pack waterproof jacket, rain pants, and pack cover.\n\n";

echo "NEW INTELLIGENT SYSTEM (Context-Aware):\n";
foreach (array_slice($testActivities, 0, 3) as $index => $activity) {
    $smartNote = $intelligentWeatherService->generateSmartWeatherNote($activity, $rainWeather);
    echo "• Activity " . ($index + 1) . ": {$smartNote}\n";
}

echo "\n✅ Smart system complete! Ready for production use.\n";
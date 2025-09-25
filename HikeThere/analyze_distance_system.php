<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Itinerary;
use App\Services\TrailCalculatorService;
use App\Services\ItineraryGeneratorService;

/**
 * Analyze how distances are calculated in the system
 */

$trailCalculator = new TrailCalculatorService();

// Get a sample itinerary to analyze
$itinerary = Itinerary::first();

if (!$itinerary) {
    echo "❌ No itinerary found to analyze\n";
    exit;
}

echo "🔍 DISTANCE CALCULATION ANALYSIS\n";
echo "===============================\n\n";

echo "📍 Itinerary: {$itinerary->title}\n";
echo "🏔️ Trail: {$itinerary->trail_name}\n";
echo "📏 Total Distance: {$itinerary->distance} km\n";
echo "⏱️ Duration: {$itinerary->estimated_duration}\n\n";

// Analyze the activities and their cumulative distances
$dailySchedule = $itinerary->daily_schedule ?? [];
$activities = [];

if (!empty($dailySchedule)) {
    foreach ($dailySchedule as $day) {
        if (isset($day['activities'])) {
            $activities = array_merge($activities, $day['activities']);
        }
    }
}

echo "📊 ACTIVITY DISTANCE BREAKDOWN:\n";
echo "==============================\n";

$previousDistance = 0;
$totalCalculatedDistance = 0;

foreach ($activities as $index => $activity) {
    $cumDistance = $activity['cum_distance_km'] ?? 0;
    $segmentDistance = $cumDistance - $previousDistance;
    $totalCalculatedDistance = $cumDistance;
    
    echo sprintf(
        "%02d. %s - %s\n",
        $index + 1,
        $activity['title'] ?? 'Unknown',
        $activity['type'] ?? 'unknown'
    );
    echo sprintf(
        "    🚶 Cumulative: %.2f km | Segment: %.2f km | Time: %s\n",
        $cumDistance,
        $segmentDistance,
        isset($activity['cum_minutes']) ? $trailCalculator->formatElapsed($activity['cum_minutes']) : 'N/A'
    );
    
    $previousDistance = $cumDistance;
}

echo "\n📈 DISTANCE CALCULATION SUMMARY:\n";
echo "===============================\n";
$trailDistance = floatval($itinerary->distance);
echo "Trail Total Distance: {$trailDistance} km\n";
echo "Activities Total Distance: {$totalCalculatedDistance} km\n";
echo "Difference: " . round(abs($trailDistance - $totalCalculatedDistance), 2) . " km\n";

if (abs($trailDistance - $totalCalculatedDistance) > 0.5) {
    echo "⚠️ WARNING: Significant difference between trail distance and activity distances!\n";
} else {
    echo "✅ Distance calculations appear consistent\n";
}

echo "\n🧮 DISTANCE CALCULATION METHOD:\n";
echo "==============================\n";
echo "The system uses 'cum_distance_km' (cumulative distance) for each activity.\n";
echo "This represents the total distance from the start point to that activity.\n";
echo "Segment distances are calculated as the difference between consecutive activities.\n\n";

// Check if there's route data that might affect distance calculations
if ($itinerary->route_data) {
    $routeData = is_string($itinerary->route_data) ? json_decode($itinerary->route_data, true) : $itinerary->route_data;
    echo "🗺️ ROUTE DATA ANALYSIS:\n";
    echo "======================\n";
    
    if (isset($routeData['legs'])) {
        echo "Route has " . count($routeData['legs']) . " legs\n";
        $totalRouteDistance = 0;
        foreach ($routeData['legs'] as $leg) {
            $legDistance = ($leg['distance_m'] ?? 0) / 1000;
            $totalRouteDistance += $legDistance;
        }
        echo "Total route distance from legs: {$totalRouteDistance} km\n";
    } else {
        echo "No detailed leg data available in route_data\n";
    }
}

echo "\n✅ Analysis complete!\n";
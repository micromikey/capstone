<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Services\ItineraryGeneratorService;
use App\Models\Itinerary;

echo "🔍 ACTIVITY GENERATION DEBUG\n";
echo "===========================\n\n";

// Get the actual itinerary data
$itinerary = Itinerary::first();
if (!$itinerary) {
    echo "❌ No itinerary found\n";
    exit;
}

// Test individual generation methods
$itineraryService = app(ItineraryGeneratorService::class);
$intelligentService = app(\App\Services\IntelligentItineraryService::class);

// Generate the full data first
$generatedData = $itineraryService->generateItinerary($itinerary, null, null, []);
$trail = $generatedData['trail'];
$dateInfo = $generatedData['dateInfo'];
$routeData = $generatedData['routeData'];

echo "Trail: " . ($trail['name'] ?? 'Unknown') . "\n";
echo "Duration: {$dateInfo['duration_days']} days\n\n";

for ($day = 1; $day <= $dateInfo['duration_days']; $day++) {
    echo "Day {$day} Generation:\n";
    echo "-------------------\n";
    
    // Test intelligent generation
    $intelligentActivities = $intelligentService->generatePersonalizedActivities(
        $itinerary, $trail, $dateInfo, $routeData, $day
    );
    
    echo "Intelligent generation: " . (empty($intelligentActivities) ? "EMPTY (will use fallback)" : count($intelligentActivities) . " activities") . "\n";
    
    if (!empty($intelligentActivities)) {
        $firstActivity = reset($intelligentActivities);
        $distance = $firstActivity['cum_distance_km'] ?? 0;
        echo "  First activity: {$firstActivity['title']} ({$firstActivity['location']}) - {$distance} km\n";
    }
    
    // Test fallback generation
    $fallbackActivities = $itineraryService->generateDayPlan($day, $trail, $dateInfo, $routeData);
    echo "Fallback generation: " . count($fallbackActivities) . " activities\n";
    
    $firstFallback = reset($fallbackActivities);
    $fallbackDistance = $firstFallback['cum_distance_km'] ?? 0;
    echo "  First activity: {$firstFallback['title']} ({$firstFallback['location']}) - {$fallbackDistance} km\n";
    
    echo "\n";
}

echo "✅ Debug complete!\n";
<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Services\ItineraryGeneratorService;
use App\Models\Itinerary;

echo "🏕️ MULTI-DAY CONTINUITY TEST\n";
echo "============================\n\n";

// Get the actual itinerary data
$itinerary = Itinerary::first();
if (!$itinerary) {
    echo "❌ No itinerary found\n";
    exit;
}

// Generate itinerary data using the service
$itineraryService = app(ItineraryGeneratorService::class);
$generatedData = $itineraryService->generateItinerary($itinerary, null, null, []);

$dayActivities = $generatedData['dayActivities'];
$dateInfo = $generatedData['dateInfo'];

echo "Total days: {$dateInfo['duration_days']}\n\n";

foreach ($dayActivities as $day => $activities) {
    echo "Day {$day} Activities:\n";
    echo "---------------------\n";
    
    // Show first 5 activities to see how each day starts
    $displayActivities = array_slice($activities, 0, 5);
    
    foreach ($displayActivities as $i => $activity) {
        $minutes = $activity['minutes'] ?? 0;
        $hours = floor($minutes / 60);
        $mins = $minutes % 60;
        $timeStr = sprintf('%02d:%02d', $hours, $mins);
        
        echo "  {$timeStr} - {$activity['title']} ({$activity['location']})\n";
    }
    
    // Analysis
    $firstActivity = reset($activities);
    if ($day === 1) {
        if (str_contains(strtolower($firstActivity['title']), 'start') && str_contains(strtolower($firstActivity['location'] ?? ''), 'trail')) {
            echo "  ✅ Day 1 correctly starts at trailhead\n";
        } else {
            echo "  ❌ Day 1 should start at trailhead\n";
        }
    } else {
        if (str_contains(strtolower($firstActivity['title']), 'camp') || str_contains(strtolower($firstActivity['location'] ?? ''), 'campsite')) {
            echo "  ✅ Day {$day} correctly continues from campsite\n";
        } else {
            echo "  ❌ Day {$day} should continue from campsite, not restart at trailhead\n";
        }
    }
    echo "\n";
}

echo "✅ Multi-day continuity test complete!\n";
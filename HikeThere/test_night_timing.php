<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Services\ItineraryGeneratorService;
use App\Models\Itinerary;

echo "🌙 NIGHT ACTIVITIES TIMING TEST\n";
echo "===============================\n\n";

// Get the actual itinerary data
$itinerary = Itinerary::first();
if (!$itinerary) {
    echo "❌ No itinerary found\n";
    exit;
}

// Generate itinerary data using the service
$itineraryService = app(ItineraryGeneratorService::class);
$generatedData = $itineraryService->generateItinerary($itinerary, null, null, []);

$nightActivities = $generatedData['nightActivities'];
$dateInfo = $generatedData['dateInfo'];

echo "1. Night Activities Analysis:\n";
echo "----------------------------\n";
echo "Total nights: {$dateInfo['nights']}\n\n";

foreach ($nightActivities as $night => $activities) {
    echo "Night {$night} Activities:\n";
    echo "------------------------\n";
    
    foreach ($activities as $activity) {
        $minutes = $activity['minutes'] ?? 0;
        $hours = floor($minutes / 60);
        $mins = $minutes % 60;
        $timeStr = sprintf('%02d:%02d', $hours, $mins);
        
        echo "  {$timeStr} - {$activity['title']}\n";
    }
    echo "\n";
    
    // Check if activities start at reasonable evening time
    $firstActivity = reset($activities);
    $firstTime = $firstActivity['minutes'] ?? 0;
    $eveningStart = 1080; // 18:00
    
    if ($firstTime >= $eveningStart) {
        echo "✅ Night {$night} starts at reasonable evening time (" . sprintf('%02d:%02d', floor($firstTime/60), $firstTime%60) . ")\n";
    } else {
        echo "❌ Night {$night} starts too early (" . sprintf('%02d:%02d', floor($firstTime/60), $firstTime%60) . ")\n";
    }
    echo "\n";
}

echo "2. Expected Times:\n";
echo "-----------------\n";
echo "Stargazing/Campfire should start around 19:00-20:00\n";
echo "Evening activities should not start before 18:00\n";

echo "\n✅ Night timing test complete!\n";
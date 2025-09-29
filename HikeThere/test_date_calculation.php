<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Services\ItineraryGeneratorService;
use App\Models\Itinerary;

echo "🗓️ DATE CALCULATION TEST\n";
echo "========================\n\n";

// Get the actual itinerary data
$itinerary = Itinerary::first();
if (!$itinerary) {
    echo "❌ No itinerary found\n";
    exit;
}

echo "1. Testing with actual itinerary:\n";
echo "--------------------------------\n";
echo "Title: {$itinerary->title}\n";
echo "Trail Name: {$itinerary->trail_name}\n";
echo "Original duration_days: {$itinerary->duration_days}\n";
echo "Original nights: {$itinerary->nights}\n";
echo "Start date: {$itinerary->start_date}\n\n";

// Generate itinerary data using the service
$itineraryService = app(ItineraryGeneratorService::class);
$generatedData = $itineraryService->generateItinerary($itinerary, null, null, []);

$dateInfo = $generatedData['dateInfo'];
$trailData = $generatedData['trail'];

echo "2. After processing with trail package data:\n";
echo "-------------------------------------------\n";
echo "Package duration: " . ($trailData['package']['duration'] ?? 'None') . "\n";
echo "Calculated duration_days: {$dateInfo['duration_days']}\n";
echo "Calculated nights: {$dateInfo['nights']}\n";
echo "Start date: " . $dateInfo['start_date']->format('Y-m-d (l)') . "\n";
echo "End date: " . $dateInfo['end_date']->format('Y-m-d (l)') . "\n";
echo "Days span: " . $dateInfo['start_date']->diffInDays($dateInfo['end_date']) . " days\n";
echo "Expected: " . ($dateInfo['duration_days'] - 1) . " days span for {$dateInfo['duration_days']} days trip\n\n";

echo "3. Validation:\n";
echo "-------------\n";
$expectedSpan = $dateInfo['duration_days'] - 1;
$actualSpan = $dateInfo['start_date']->diffInDays($dateInfo['end_date']);

if ($expectedSpan === $actualSpan) {
    echo "✅ Date calculation is CORRECT\n";
    echo "   - A {$dateInfo['duration_days']}-day trip correctly spans {$actualSpan} days\n";
    echo "   - Start: " . $dateInfo['start_date']->format('Y-m-d') . "\n";
    echo "   - End:   " . $dateInfo['end_date']->format('Y-m-d') . "\n";
} else {
    echo "❌ Date calculation is INCORRECT\n";
    echo "   - A {$dateInfo['duration_days']}-day trip should span {$expectedSpan} days\n";
    echo "   - But actual span is {$actualSpan} days\n";
}

echo "\n✅ Date calculation test complete!\n";
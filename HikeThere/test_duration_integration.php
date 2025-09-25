<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Services\ItineraryGeneratorService;
use App\Services\DurationParserService;
use App\Models\Itinerary;

echo "🔍 DURATION DISPLAY INTEGRATION TEST\n";
echo "====================================\n\n";

// Get the actual itinerary data
$itinerary = Itinerary::first();
if (!$itinerary) {
    echo "❌ No itinerary found\n";
    exit;
}

echo "1. Raw Itinerary Data:\n";
echo "---------------------\n";
echo "Title: {$itinerary->title}\n";
echo "Trail Name: {$itinerary->trail_name}\n\n";

// Generate itinerary data using the service (like the blade template does)
$itineraryService = app(ItineraryGeneratorService::class);
$generatedData = $itineraryService->generateItinerary($itinerary, null, null, []);

$trailData = $generatedData['trail'];
$dateInfo = $generatedData['dateInfo'];

echo "2. Generated Trail Data Structure:\n";
echo "---------------------------------\n";
echo "Trail array keys: " . implode(', ', array_keys($trailData)) . "\n";

if (isset($trailData['package'])) {
    echo "Package data available: YES\n";
    echo "Package duration: " . ($trailData['package']['duration'] ?? 'Not set') . "\n";
} else {
    echo "Package data available: NO\n";
}

echo "\n3. Date Info:\n";
echo "------------\n";
echo "Duration Days: {$dateInfo['duration_days']}\n";
echo "Nights: {$dateInfo['nights']}\n\n";

// Test the duration parser with this data
$durationParser = new DurationParserService();

echo "4. Duration Parser Results:\n";
echo "---------------------------\n";

if (isset($trailData['package']['duration'])) {
    $packageDuration = $trailData['package']['duration'];
    echo "Package Duration: '{$packageDuration}'\n";
    echo "Parsed: " . $durationParser->formatDuration($packageDuration, 'days_nights') . "\n";
    echo "Full Format: " . $durationParser->formatDuration($packageDuration, 'full') . "\n";
} else {
    echo "❌ Package duration not available in trail data\n";
    echo "Fallback: {$dateInfo['duration_days']} days • {$dateInfo['nights']} nights\n";
}

echo "\n5. Expected Header Duration Display:\n";
echo "-----------------------------------\n";

// Simulate what the header component will show
$headerDurationLabel = null;

if (isset($trailData['package']['duration'])) {
    $headerDurationLabel = $durationParser->formatDuration($trailData['package']['duration'], 'days_nights');
} else {
    $headerDurationLabel = $dateInfo['duration_days'] . ' day' . ($dateInfo['duration_days'] != 1 ? 's' : '') . 
                         ' • ' . $dateInfo['nights'] . ' night' . ($dateInfo['nights'] != 1 ? 's' : '');
}

echo "Header will display: '{$headerDurationLabel}'\n";

echo "\n✅ Integration test complete!\n";
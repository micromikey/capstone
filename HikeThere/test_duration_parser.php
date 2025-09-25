<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Services\DurationParserService;

echo "🕒 DURATION PARSER SERVICE TEST\n";
echo "===============================\n\n";

$durationParser = new DurationParserService();

// Test with the actual database value
echo "1. Testing with actual trail package duration:\n";
echo "---------------------------------------------\n";
$actualDuration = "36 hours";
$parsed = $durationParser->parseDurationInput($actualDuration);
$normalized = $durationParser->normalizeDuration($actualDuration);

echo "Input: '{$actualDuration}'\n";
echo "Parsed: " . json_encode($parsed) . "\n";
echo "Normalized: " . json_encode($normalized) . "\n";
echo "Formatted (days_nights): " . $durationParser->formatDuration($actualDuration, 'days_nights') . "\n";
echo "Formatted (full): " . $durationParser->formatDuration($actualDuration, 'full') . "\n\n";

// Test all parsing scenarios
echo "2. Comprehensive parsing test:\n";
echo "-----------------------------\n";
$testResults = $durationParser->testParsing();

foreach ($testResults as $input => $result) {
    echo "Input: '{$input}'\n";
    echo "  → Parsed: " . json_encode($result['parsed']) . "\n";
    echo "  → Normalized: " . json_encode($result['normalized']) . "\n";
    echo "  → Days/Nights: " . $result['formatted'] . "\n";
    echo "  → Full: " . $result['full'] . "\n\n";
}

// Test with actual trail data
echo "3. Testing with actual trail data:\n";
echo "---------------------------------\n";

$trail = \App\Models\Trail::with('package')->find(1);
if ($trail) {
    echo "Trail: {$trail->name}\n";
    echo "Package Duration: {$trail->package->duration}\n";
    
    $trailDuration = $durationParser->getTrailDuration($trail);
    echo "Parsed Trail Duration: " . json_encode($trailDuration) . "\n";
    echo "Formatted: " . $durationParser->formatDuration($trail->package->duration, 'days_nights') . "\n";
}

echo "\n✅ Duration parser test complete!\n";
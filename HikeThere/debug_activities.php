<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$itinerary = App\Models\Itinerary::first();
$activities = $itinerary->daily_schedule[0]['activities'] ?? [];

echo "=== DEBUGGING ACTIVITIES ===\n";
echo "Activities count: " . count($activities) . "\n\n";

foreach ($activities as $i => $activity) {
    echo "Activity " . ($i+1) . ":\n";
    echo "  Title: " . ($activity['title'] ?? 'N/A') . "\n";
    echo "  Minutes: " . ($activity['minutes'] ?? 0) . "\n";
    echo "  Location: " . ($activity['location'] ?? 'N/A') . "\n";
    echo "  Type: " . ($activity['type'] ?? 'N/A') . "\n";
    echo "\n";
}
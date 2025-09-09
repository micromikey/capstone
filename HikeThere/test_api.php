<?php

require_once 'vendor/autoload.php';

use App\Models\Trail;

// Test direct database query
echo "Testing OSM database search...\n";

$mountainName = 'Mount Arayat';
$trailName = 'Arayat Trail';

// Search for exact combined format
$combinedSearch = "{$mountainName} - {$trailName}";
echo "Searching for: {$combinedSearch}\n";

$trails = Trail::where('name', 'ILIKE', "%{$combinedSearch}%")->get();
echo "Found " . $trails->count() . " trails with exact combined search\n";

if ($trails->count() > 0) {
    foreach ($trails as $trail) {
        echo "- {$trail->name} (OSM ID: {$trail->osm_id})\n";
    }
}

// Search by individual components
echo "\nSearching by individual components...\n";
$trails2 = Trail::where(function($q) use ($mountainName, $trailName) {
    $q->where('name', 'ILIKE', "%{$mountainName}%")
      ->where('name', 'ILIKE', "%{$trailName}%");
})->get();

echo "Found " . $trails2->count() . " trails with component search\n";

if ($trails2->count() > 0) {
    foreach ($trails2 as $trail) {
        echo "- {$trail->name} (OSM ID: {$trail->osm_id})\n";
    }
}

// Show some sample trail names to understand the data structure
echo "\nSample trail names in database:\n";
$sampleTrails = Trail::whereNotNull('osm_id')->limit(10)->get(['name', 'osm_id']);
foreach ($sampleTrails as $trail) {
    echo "- {$trail->name} (OSM ID: {$trail->osm_id})\n";
}

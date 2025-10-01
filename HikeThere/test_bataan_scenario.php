<?php

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/app/Services/ItineraryGeneratorService.php';

use App\Services\ItineraryGeneratorService;

/**
 * Test the real-world scenario:
 * User is in Bataan, Philippines
 * Needs to go to Shaw Boulevard Manila (departure point)
 * Then take van to Ambangeg Trail, Mt. Pulag
 */

echo "=== Testing Real-World Bataan → Shaw Boulevard → Mt. Pulag Scenario ===\n\n";

// Mock trail data for Mt. Pulag
$trail = [
    'name' => 'Mt. Pulag via Ambangeg Trail',
    'location' => 'Benguet, Philippines',
    'latitude' => 16.5966,
    'longitude' => 120.9060,
    'transport_included' => 1,
    'transport_details' => 'Van transportation from Shaw Boulevard Manila to Ambangeg Trail',
    'departure_point' => 'Shaw Boulevard Manila'
];

// User location: Bataan, Philippines
$userLocation = [
    'lat' => 14.6417,
    'lng' => 120.4736,
    'address' => 'Bataan, Philippines'
];

// Mock trail packages for duration
$trailPackages = [
    [
        'duration_days' => 2,
        'duration_nights' => 1
    ]
];

echo "📍 User Location: {$userLocation['address']}\n";
echo "📍 Trail: {$trail['name']}\n";
echo "📍 Departure Point: {$trail['departure_point']}\n\n";

$service = new ItineraryGeneratorService();

// Test calculateTravelTime method
echo "=== Testing Travel Time Calculations ===\n";

// Bataan to Shaw Boulevard Manila
$bataan_to_shaw = $service->calculateTravelTime(
    $userLocation['lat'], $userLocation['lng'],    // Bataan
    14.5868, 121.0584,                            // Shaw Boulevard
    'bus'
);

echo "🚌 Bataan → Shaw Boulevard: {$bataan_to_shaw} minutes (" . round($bataan_to_shaw/60, 1) . " hours)\n";

// Shaw Boulevard to Mt. Pulag
$shaw_to_pulag = $service->calculateTravelTime(
    14.5868, 121.0584,                            // Shaw Boulevard
    $trail['latitude'], $trail['longitude'],      // Mt. Pulag
    'van'
);

echo "🚐 Shaw Boulevard → Mt. Pulag: {$shaw_to_pulag} minutes (" . round($shaw_to_pulag/60, 1) . " hours)\n\n";

// Test location coordinate lookups
echo "=== Testing Location Database ===\n";

$locations = ['Shaw Boulevard Manila', 'Bataan', 'Ambangeg Trail'];
foreach ($locations as $location) {
    $coords = $service->getLocationCoordinates($location);
    echo "📍 {$location}: {$coords['lat']}, {$coords['lng']}\n";
}

echo "\n=== Testing Pre-Hike Transportation Activities ===\n";

// Generate pre-hike activities
try {
    $preHikeActivities = $service->generatePreHikeActivities($trail, $trailPackages, $userLocation);
    
    echo "Generated " . count($preHikeActivities) . " pre-hike activities:\n\n";
    
    foreach ($preHikeActivities as $index => $activity) {
        $startTime = sprintf('%02d:%02d', intval($activity['start_time'] / 60), $activity['start_time'] % 60);
        echo sprintf(
            "%d. %s | %s | %s\n   📋 %s\n",
            $index + 1,
            $startTime,
            $activity['activity'],
            $activity['location'],
            $activity['description']
        );
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

echo "\n=== Testing Philippines-Specific Route Context ===\n";

// Test specific Philippines route patterns
$testRoutes = [
    ['from' => 'Bataan', 'to' => 'Shaw Boulevard Manila', 'context' => 'province_to_manila'],
    ['from' => 'Shaw Boulevard Manila', 'to' => 'Mt. Pulag', 'context' => 'manila_to_baguio_trail'],
    ['from' => 'Makati', 'to' => 'Baguio City', 'context' => 'manila_to_baguio'],
];

foreach ($testRoutes as $route) {
    $fromCoords = $service->getLocationCoordinates($route['from']);
    $toCoords = $service->getLocationCoordinates($route['to']);
    
    if ($fromCoords && $toCoords) {
        $time = $service->calculateTravelTime(
            $fromCoords['lat'], $fromCoords['lng'],
            $toCoords['lat'], $toCoords['lng'],
            'van',
            $route['context']
        );
        
        echo "🛣️  {$route['from']} → {$route['to']} ({$route['context']}): " . 
             round($time/60, 1) . " hours\n";
    }
}

echo "\n✅ Test Complete!\n";

?>
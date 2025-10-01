<?php

require_once __DIR__ . '/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Trail;

try {
    echo "=== CHECKING ALL TRAIL PACKAGE DATA ===" . PHP_EOL;
    
    $trails = Trail::with('package')->get();
    
    if ($trails->isEmpty()) {
        echo "No trails found in database" . PHP_EOL;
        exit;
    }
    
    foreach ($trails as $trail) {
        echo PHP_EOL . "Trail ID: " . $trail->id . PHP_EOL;
        echo "Trail Name: " . ($trail->name ?? 'Unnamed') . PHP_EOL;
        
        if ($trail->package) {
            echo "Package ID: " . $trail->package->id . PHP_EOL;
            echo "Pickup Time: " . ($trail->package->pickup_time ?? 'null') . PHP_EOL;
            echo "Departure Time: " . ($trail->package->departure_time ?? 'null') . PHP_EOL;
            echo "Transport Included: " . ($trail->package->transport_included ? 'true' : 'false') . PHP_EOL;
        } else {
            echo "No package found" . PHP_EOL;
        }
        echo "---" . PHP_EOL;
    }
    
    // Use the first trail for method testing
    $trail = $trails->first();
    
    // Test the getTrailTimes method
    echo PHP_EOL . "=== TESTING getTrailTimes METHOD ===" . PHP_EOL;
    
    // Create the service with dependencies
    $service = $app->make(\App\Services\ItineraryGeneratorService::class);
    $reflection = new ReflectionClass($service);
    $method = $reflection->getMethod('getTrailTimes');
    $method->setAccessible(true);
    
    $times = $method->invoke($service, $trail);
    echo "Pickup Time from method: " . ($times['pickup_time'] ?? 'null') . PHP_EOL;
    echo "Departure Time from method: " . ($times['departure_time'] ?? 'null') . PHP_EOL;
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . PHP_EOL;
    echo "Stack trace: " . $e->getTraceAsString() . PHP_EOL;
}
<?php

use App\Services\ItineraryGeneratorService;
use Illuminate\Support\Facades\Log;

// Test Philippines travel time scenario
echo "=== Testing Bataan → Shaw Boulevard → Mt. Pulag Scenario ===\n";

try {
    $service = app(ItineraryGeneratorService::class);
    
    // Use reflection to access protected methods for testing
    $reflection = new ReflectionClass($service);
    
    // Test calculatePhilippinesTravelTime
    $travelMethod = $reflection->getMethod('calculatePhilippinesTravelTime');
    $travelMethod->setAccessible(true);
    
    // Test getLocationCoordinates
    $locationMethod = $reflection->getMethod('getLocationCoordinates');
    $locationMethod->setAccessible(true);
    
    // Test coordinates lookup
    echo "\n=== Location Coordinates ===\n";
    $locations = ['Shaw Boulevard Manila', 'Bataan', 'Ambangeg Trail'];
    foreach ($locations as $location) {
        $coords = $locationMethod->invoke($service, $location);
        echo "$location: {$coords['lat']}, {$coords['lng']}\n";
    }
    
    // Test travel times
    echo "\n=== Travel Time Calculations ===\n";
    
    // Bataan to Shaw Boulevard
    $bataan_coords = $locationMethod->invoke($service, 'Bataan');
    $shaw_coords = $locationMethod->invoke($service, 'Shaw Boulevard Manila');
    
    $time1 = $travelMethod->invoke(
        $service, 
        $bataan_coords['lat'], $bataan_coords['lng'],
        $shaw_coords['lat'], $shaw_coords['lng'],
        'bus'
    );
    
    echo "Bataan → Shaw Boulevard: " . $time1 . " minutes (" . round($time1/60, 1) . " hours)\n";
    
    // Shaw Boulevard to Mt. Pulag
    $pulag_coords = $locationMethod->invoke($service, 'Ambangeg Trail');
    
    $time2 = $travelMethod->invoke(
        $service,
        $shaw_coords['lat'], $shaw_coords['lng'],
        $pulag_coords['lat'], $pulag_coords['lng'],
        'van'
    );
    
    echo "Shaw Boulevard → Mt. Pulag: " . $time2 . " minutes (" . round($time2/60, 1) . " hours)\n";
    
    echo "\nTotal travel time: " . round(($time1 + $time2)/60, 1) . " hours\n";
    
    echo "\n✅ Philippines transportation system test completed!\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}
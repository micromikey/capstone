<?php

require_once __DIR__ . '/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\TrailPackage;

try {
    echo "=== UPDATING TRAIL PACKAGE TIMES ===" . PHP_EOL;
    
    // Update trail 1 with pickup_time
    $trail1Package = TrailPackage::find(1);
    if ($trail1Package) {
        $trail1Package->pickup_time = '06:00:00';
        $trail1Package->departure_time = '08:30:00';
        $trail1Package->save();
        echo "Trail 1 package updated: pickup_time=06:00:00, departure_time=08:30:00" . PHP_EOL;
    }

    // Update trail 2 with different times
    $trail2Package = TrailPackage::find(2);
    if ($trail2Package) {
        $trail2Package->pickup_time = '07:15:00';
        $trail2Package->departure_time = '09:45:00';
        $trail2Package->save();
        echo "Trail 2 package updated: pickup_time=07:15:00, departure_time=09:45:00" . PHP_EOL;
    }
    
    echo "Trail packages updated successfully" . PHP_EOL;
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . PHP_EOL;
}
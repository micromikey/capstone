<?php

require_once __DIR__ . '/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Trail;
use Illuminate\Support\Facades\DB;

try {
    echo "=== CHECKING TRAIL TRANSPORT_INCLUDED VALUES ===" . PHP_EOL;
    
    $trails = Trail::all();
    foreach ($trails as $trail) {
        $rawValue = $trail->getAttributes()['transport_included'] ?? 'not set in table';
        $accessorValue = $trail->transport_included;
        
        echo "Trail {$trail->id}:" . PHP_EOL;
        echo "  Raw database value: " . var_export($rawValue, true) . PHP_EOL;
        echo "  Accessor result: " . ($accessorValue ? 'true' : 'false') . PHP_EOL;
        echo "  Package transport_included: " . ($trail->package?->transport_included ? 'true' : 'false') . PHP_EOL;
        echo PHP_EOL;
    }
    
    echo "=== FIXING TRAIL TRANSPORT_INCLUDED VALUES ===" . PHP_EOL;
    
    // Set trail transport_included to null so it uses package values
    foreach ($trails as $trail) {
        if (isset($trail->getAttributes()['transport_included'])) {
            // Update raw attributes to set to null
            DB::table('trails')
                ->where('id', $trail->id)
                ->update(['transport_included' => null]);
            echo "Updated Trail {$trail->id} transport_included to null" . PHP_EOL;
        }
    }
    
    echo PHP_EOL . "=== VERIFYING UPDATES ===" . PHP_EOL;
    
    // Re-fetch and verify
    $trails = Trail::with('package')->get();
    foreach ($trails as $trail) {
        $rawValue = $trail->getAttributes()['transport_included'] ?? 'not set in table';
        $accessorValue = $trail->transport_included;
        
        echo "Trail {$trail->id}:" . PHP_EOL;
        echo "  Raw database value: " . var_export($rawValue, true) . PHP_EOL;
        echo "  Accessor result: " . ($accessorValue ? 'true' : 'false') . PHP_EOL;
        echo PHP_EOL;
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . PHP_EOL;
    echo "Stack trace: " . $e->getTraceAsString() . PHP_EOL;
}
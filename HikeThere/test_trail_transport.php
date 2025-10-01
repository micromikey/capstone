<?php

use App\Models\Trail;
use Illuminate\Support\Facades\Schema;

// Test creating a trail with transportation information to verify database structure
try {
    // Check if the trails table has the required transportation fields
    if (Schema::hasColumns('trails', ['transport_included', 'transport_details'])) {
        echo "✅ Database has required transportation fields\n";
        
        // Create a test trail record
        $trail = new Trail();
        $trail->name = 'Test Mt. Pulag Multi-Day Trek';
        $trail->distance_km = 15.5;
        $trail->difficulty_level = 'intermediate';
        $trail->trail_type = 'multi-day';
        $trail->elevation_gain_m = 2922;
        $trail->estimated_duration_hours = 36;
        $trail->transport_included = true;
        $trail->transport_details = 'Van transportation from Baguio City Terminal';
        $trail->departure_point = 'Baguio City Terminal';
        $trail->coordinates_start_lat = 16.5966;
        $trail->coordinates_start_lng = 120.9060;
        $trail->coordinates_end_lat = 16.5966;
        $trail->coordinates_end_lng = 120.9060;
        $trail->region = 'Benguet';
        $trail->city = 'Kabayan';
        $trail->save();
        
        echo "✅ Created test trail with ID: {$trail->id}\n";
        echo "   - Name: {$trail->name}\n";
        echo "   - Transport Included: " . ($trail->transport_included ? 'Yes' : 'No') . "\n";
        echo "   - Transport Details: {$trail->transport_details}\n";
        echo "   - Departure Point: {$trail->departure_point}\n";
        
        // Test retrieving and using this trail
        $retrievedTrail = Trail::find($trail->id);
        if ($retrievedTrail && $retrievedTrail->transport_included) {
            echo "✅ Trail retrieved successfully with transport information\n";
        }
        
        // Clean up - delete the test trail
        $trail->delete();
        echo "✅ Test trail cleaned up\n";
        
    } else {
        echo "❌ Database missing transportation fields\n";
        echo "Required fields: transport_included, transport_details\n";
    }
    
} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
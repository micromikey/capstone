<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Trail;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add sample coordinates to existing trails for testing
        $sampleCoordinates = [
            [
                ['lat' => 14.6760, 'lng' => 121.0437],
                ['lat' => 14.6761, 'lng' => 121.0440],
                ['lat' => 14.6763, 'lng' => 121.0443],
                ['lat' => 14.6765, 'lng' => 121.0446],
                ['lat' => 14.6767, 'lng' => 121.0449],
                ['lat' => 14.6769, 'lng' => 121.0452],
                ['lat' => 14.6771, 'lng' => 121.0455],
                ['lat' => 14.6773, 'lng' => 121.0458],
                ['lat' => 14.6775, 'lng' => 121.0461],
                ['lat' => 14.6777, 'lng' => 121.0464],
                ['lat' => 14.6779, 'lng' => 121.0467],
                ['lat' => 14.6781, 'lng' => 121.0470],
                ['lat' => 14.6783, 'lng' => 121.0473],
                ['lat' => 14.6785, 'lng' => 121.0476],
                ['lat' => 14.6787, 'lng' => 121.0479],
            ],
            [
                ['lat' => 14.5500, 'lng' => 120.9900],
                ['lat' => 14.5502, 'lng' => 120.9903],
                ['lat' => 14.5504, 'lng' => 120.9906],
                ['lat' => 14.5506, 'lng' => 120.9909],
                ['lat' => 14.5508, 'lng' => 120.9912],
                ['lat' => 14.5510, 'lng' => 120.9915],
                ['lat' => 14.5512, 'lng' => 120.9918],
                ['lat' => 14.5514, 'lng' => 120.9921],
                ['lat' => 14.5516, 'lng' => 120.9924],
                ['lat' => 14.5518, 'lng' => 120.9927],
                ['lat' => 14.5520, 'lng' => 120.9930],
                ['lat' => 14.5522, 'lng' => 120.9933],
            ]
        ];

        $trails = Trail::limit(2)->get();
        
        foreach ($trails as $index => $trail) {
            if (isset($sampleCoordinates[$index])) {
                $trail->update([
                    'coordinates' => $sampleCoordinates[$index],
                    'elevation_high' => 1200 + ($index * 100),
                    'elevation_low' => 800 + ($index * 50),
                    'elevation_gain' => 400 + ($index * 50),
                    'length' => 5.5 + ($index * 1.5)
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reset coordinates for the trails
        $trails = Trail::whereNotNull('coordinates')->limit(2)->get();
        
        foreach ($trails as $trail) {
            $trail->update([
                'coordinates' => null
            ]);
        }
    }
};

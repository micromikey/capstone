<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Trail;
use App\Models\TrailPackage;

class TrailPackageProxyTest extends TestCase
{
    use RefreshDatabase;

    public function test_trail_accessors_return_package_values_when_trail_columns_null()
    {
        // create a trail with null compatibility columns
        $trail = Trail::factory()->create([
            'price' => null,
            'package_inclusions' => null,
            'duration' => null,
            'permit_required' => null,
            'transport_included' => null,
            'transport_details' => null,
            'transportation_details' => null,
            'commute_legs' => null,
            'commute_summary' => null,
            'side_trips' => null,
        ]);

        // attach package data
        $package = TrailPackage::create([
            'trail_id' => $trail->id,
            'price' => 123.45,
            'package_inclusions' => 'Includes guide, permits',
            'duration' => '2 days',
            'permit_required' => true,
            'transport_included' => true,
            'transport_details' => 'Pickup from city',
            'transportation_details' => 'Van with driver',
            'commute_legs' => json_encode([['from' => 'City', 'to' => 'Trailhead']]),
            'commute_summary' => '1 hour drive',
            'side_trips' => 'Waterfall nearby',
        ]);

        // reload trail to ensure relations are present
        $trail->refresh();

        $this->assertEquals(123.45, $trail->price);
        $this->assertEquals('Includes guide, permits', $trail->package_inclusions);
        $this->assertEquals('2 days', $trail->duration);
        $this->assertTrue($trail->permit_required);
        $this->assertTrue($trail->transport_included);
        $this->assertEquals('Pickup from city', $trail->transport_details);
        $this->assertEquals('Van with driver', $trail->transportation_details);
        $this->assertIsArray($trail->commute_legs);
        $this->assertEquals('1 hour drive', $trail->commute_summary);
        $this->assertEquals('Waterfall nearby', $trail->side_trips);
    }
}

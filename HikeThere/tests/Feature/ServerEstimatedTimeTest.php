<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Location;
use App\Models\Trail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class ServerEstimatedTimeTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function server_computes_estimated_time_when_length_and_gain_provided(): void
    {
        $user = User::factory()->create();
        $user->user_type = 'organization';
        $user->approval_status = 'approved';
        $user->save();
    Auth::login($user);

        $location = Location::factory()->create();

        $payload = [
            'mountain_name' => 'Test Peak',
            'trail_name' => 'Test Trail',
            'location_id' => $location->id,
            'price' => 50,
            'package_inclusions' => 'Guide',
            'difficulty' => 'beginner',
            'duration' => '1 day',
            'best_season' => 'Dry',
            'terrain_notes' => 'Notes',
            'packing_list' => 'Pack',
            'health_fitness' => 'Good',
            'emergency_contacts' => '911',
            // Provide metrics but omit estimated_time
            'length' => 10.0, // km
            'elevation_gain' => 600, // m
        ];

        $this->post(route('org.trails.store'), $payload)->assertRedirect();

        $trail = Trail::where('trail_name', 'Test Trail')->first();
        $this->assertNotNull($trail, 'Trail should be created');
        $this->assertNotNull($trail->estimated_time, 'Server should have computed estimated_time');
        // Expect roughly: base 10km * 12 = 120 minutes + (600/600)*60 = 60 -> 180
        $this->assertEquals(180, $trail->estimated_time);
    }
}

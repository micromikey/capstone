<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Location;
use App\Models\Trail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class ServerEstimatedTimeOverrideTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function server_overrides_user_supplied_estimated_time_when_metrics_exist(): void
    {
        $user = User::factory()->create();
        $user->user_type = 'organization';
        $user->approval_status = 'approved';
        $user->save();
        Auth::login($user);

        $location = Location::factory()->create();

        $payload = [
            'mountain_name' => 'Override Peak',
            'trail_name' => 'Override Trail',
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
            // Provide metrics and a user-supplied estimated_time which should be ignored
            'length' => 10.0, // km
            'elevation_gain' => 600, // m
            'estimated_time' => 999,
        ];

        $this->post(route('org.trails.store'), $payload)->assertRedirect();

        $trail = Trail::where('trail_name', 'Override Trail')->first();
        $this->assertNotNull($trail, 'Trail should be created');
        $this->assertNotNull($trail->estimated_time, 'Server should have computed estimated_time');
        $this->assertEquals(180, $trail->estimated_time, 'Server estimate should override the user-supplied value');
    }
}

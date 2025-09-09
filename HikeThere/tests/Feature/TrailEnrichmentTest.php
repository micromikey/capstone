<?php

namespace Tests\Feature;

use App\Jobs\EnrichTrailData;
use App\Models\Trail;
use App\Models\User;
use App\Models\Location;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class TrailEnrichmentTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function creating_trail_without_coordinates_dispatches_enrichment_job(): void
    {
        Queue::fake();
    /** @var User $user */
    $user = User::factory()->create();
        $location = Location::factory()->create();

        $payload = [
            'mountain_name' => 'Sample Peak',
            'trail_name' => 'Main Ridge',
            'location_id' => $location->id,
            'price' => 100,
            'package_inclusions' => 'Guide',
            'difficulty' => 'beginner',
            'duration' => '1 day',
            'best_season' => 'Dry',
            'terrain_notes' => 'Forest',
            'packing_list' => 'Water',
            'health_fitness' => 'Average fitness',
            'emergency_contacts' => '911'
        ];

    // Authenticate as organization user (ensure user_type to pass middleware)
    $user->user_type = 'organization';
    $user->approval_status = 'approved';
    $user->save();
    Auth::login($user);
        $response = $this->post(route('org.trails.store'), $payload);
        $response->assertRedirect();

        Queue::assertPushed(EnrichTrailData::class);
    }

    /** @test */
    public function observer_derives_metrics_when_coordinates_present_immediately(): void
    {
        $user = User::factory()->create();
        $location = Location::factory()->create();
        $coords = [
            ['lat'=>14.6000,'lng'=>121.0000,'elevation'=>100],
            ['lat'=>14.6010,'lng'=>121.0010,'elevation'=>120],
            ['lat'=>14.6020,'lng'=>121.0020,'elevation'=>130],
        ];
        $trail = Trail::factory()->create([
            'user_id' => $user->id,
            'location_id' => $location->id,
            'coordinates' => $coords,
            'length' => null,
            'elevation_gain' => null,
        ]);

        $trail->refresh();
        $this->assertNotNull($trail->length, 'Length should be derived');
        $this->assertNotNull($trail->elevation_gain, 'Elevation gain should be derived');
        $this->assertNotNull($trail->estimated_time, 'Estimated time should be derived');
    }
}

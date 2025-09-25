<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Itinerary;

class ItineraryTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_saves_an_itinerary_posted_as_json_string()
    {
        // Create a user and act as them
        $user = User::factory()->create();
        $this->actingAs($user);

        $payload = [
            'title' => 'Test Trip',
            'start_date' => now()->toDateString(),
            'start_time' => '06:00',
            'duration_days' => 2,
            'nights' => 1,
            'daily_schedule' => [
                [
                    'date' => now()->toDateString(),
                    'meta' => ['note' => 'day1'],
                    'activities' => [
                        ['minutes' => 0, 'title' => 'Start', 'description' => 'Begin', 'location' => 'Trailhead']
                    ],
                ],
                [
                    'date' => now()->addDay()->toDateString(),
                    'meta' => ['note' => 'day2'],
                    'activities' => [
                        ['minutes' => 0, 'title' => 'Continue', 'description' => 'Keep going', 'location' => 'Trail']
                    ],
                ],
            ],
        ];

        $response = $this->post(route('hiker.itinerary.generate'), ['itinerary' => json_encode($payload)]);

        $response->assertRedirect();

        $this->assertDatabaseHas('itineraries', [
            'title' => 'Test Trip',
            'user_id' => $user->id,
        ]);

        $it = Itinerary::where('title', 'Test Trip')->first();
        $this->assertNotNull($it);
        $this->assertDatabaseCount('itinerary_days', 2);
        $this->assertDatabaseCount('itinerary_activities', 2);
    }
}

<?php

namespace Tests\Feature;

use App\Models\Batch;
use App\Models\Booking;
use App\Models\Event;
use App\Models\Trail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookingStoreTest extends TestCase
{
    use RefreshDatabase;

    public function test_happy_path_persists_event_id()
    {
        // Create an organization user and a trail owned by them
        $org = User::factory()->create(['user_type' => 'organization']);
        $trail = Trail::factory()->create(['user_id' => $org->id]);

        // Create an event for this trail
        $event = Event::create([
            'user_id' => $org->id,
            'title' => 'Group Hike',
            'description' => 'A nice hike',
            'start_at' => now()->addDays(3),
            'end_at' => now()->addDays(3)->addHours(4),
            'trail_id' => $trail->id,
            'capacity' => 20,
            'is_public' => true,
        ]);

        // Create a batch for the trail
        $batch = Batch::create([
            'trail_id' => $trail->id,
            'name' => 'Morning Slot',
            'capacity' => 10,
            'starts_at' => now()->addDays(3)->setTime(7,0,0),
            'ends_at' => now()->addDays(3)->setTime(11,0,0),
        ]);

        // Create a hiker and authenticate
        $hiker = User::factory()->create(['user_type' => 'hiker']);
        $this->actingAs($hiker);

        $response = $this->post(route('booking.store'), [
            'trail_id' => $trail->id,
            'batch_id' => $batch->id,
            'date' => now()->addDays(3)->toDateString(),
            'party_size' => 2,
            'notes' => 'Looking forward',
            'event_id' => $event->id,
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('bookings', [
            'user_id' => $hiker->id,
            'trail_id' => $trail->id,
            'batch_id' => $batch->id,
            'event_id' => $event->id,
        ]);
    }

    public function test_event_trail_mismatch_rejected()
    {
        // Organization A with trail A
        $orgA = User::factory()->create(['user_type' => 'organization']);
        $trailA = Trail::factory()->create(['user_id' => $orgA->id]);

        // Organization B with trail B
        $orgB = User::factory()->create(['user_type' => 'organization']);
        $trailB = Trail::factory()->create(['user_id' => $orgB->id]);

        // Event is for trail B
        $eventB = Event::create([
            'user_id' => $orgB->id,
            'title' => 'Other Hike',
            'start_at' => now()->addDays(5),
            'end_at' => now()->addDays(5)->addHours(3),
            'trail_id' => $trailB->id,
            'capacity' => 15,
            'is_public' => true,
        ]);

        // Create a batch for trail A
        $batchA = Batch::create([
            'trail_id' => $trailA->id,
            'name' => 'Slot A',
            'capacity' => 10,
        ]);

        // Hiker
        $hiker = User::factory()->create(['user_type' => 'hiker']);
        $this->actingAs($hiker);

        // Attempt to book trail A but reference event from trail B
        $response = $this->post(route('booking.store'), [
            'trail_id' => $trailA->id,
            'batch_id' => $batchA->id,
            'date' => now()->addDays(5)->toDateString(),
            'party_size' => 2,
            'notes' => 'Test mismatch',
            'event_id' => $eventB->id,
        ]);

        $response->assertSessionHasErrors('event_id');

        $this->assertDatabaseMissing('bookings', [
            'user_id' => $hiker->id,
            'trail_id' => $trailA->id,
            'event_id' => $eventB->id,
        ]);
    }
}

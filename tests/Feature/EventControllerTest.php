<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class EventControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        Sanctum::actingAs($this->user);
    }


    public function test_returns_paginated_events()
    {
        Event::factory()->count(15)->create();

        $response = $this->getJson('/api/events');

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'title', 'description', 'start_date', 'end_date']
                ],
                'links',
                'meta'
            ])
            ->assertJsonCount(10, 'data'); // Default per page
    }


    public function test_can_filter_events_by_country()
    {
        Event::factory()->create(['country' => 'US']);
        Event::factory()->create(['country' => 'UK']);

        $response = $this->getJson('/api/events?country=US');

        $response->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.country', 'US');
    }


    public function test_creates_an_event()
    {
        $data = [
            'title' => 'Tech Conference',
            'description' => 'Annual tech event',
            'start_date' => now()->addDay()->format('Y-m-d H:i:s'),
            'end_date' => now()->addDays(2)->format('Y-m-d H:i:s'),
            'capacity' => 100,
            'country' => 'US',
            'city' => 'San Francisco',
        ];

        $response = $this->postJson('/api/events', $data);

        $response->assertCreated()
            ->assertJsonPath('data.title', 'Tech Conference')
            ->assertJsonPath('data.user_id.id', $this->user->id);
    }


    public function test_validates_event_creation()
    {
        $response = $this->postJson('/api/events', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['title', 'start_date', 'capacity']);
    }


    public function test_shows_an_event()
    {
        $event = Event::factory()->create();

        $response = $this->getJson("/api/events/{$event->id}");

        $response->assertOk()
            ->assertJsonPath('data.id', $event->id)
            ->assertJsonPath('data.title', $event->title);
    }


    public function test_returns_404_for_missing_event()
    {
        $response = $this->getJson('/api/events/999');

        $response->assertNotFound()
            ->assertJsonPath('error.message', 'Event not found');
    }


    public function test_updates_an_event()
    {
        Sanctum::actingAs($this->user);

        $event = Event::factory()->create([
            'user_id' => $this->user->id,
        ]);

        $response = $this->putJson("/api/events/{$event->id}", [
            'title' => 'Updated Title',
        ]);

        $response->assertOk()
            ->assertJsonPath('data.title', 'Updated Title');
    }

    public function test_deletes_an_event()
    {
        $event = Event::factory()->create(['user_id' => $this->user->id]);

        $response = $this->deleteJson("/api/events/{$event->id}");

        $response->assertNoContent();
        $this->assertSoftDeleted('events', ['id' => $event->id]);
    }


    public function test_prevents_deleting_event_with_bookings()
    {
        $event = Event::factory()->hasBookings(1)->create(['user_id' => $this->user->id]);

        $response = $this->deleteJson("/api/events/{$event->id}");

        $response->assertStatus(409)
            ->assertJsonPath('error.message', 'Cannot delete event with existing bookings');
    }


    public function test_lists_event_bookings()
    {
        $event = Event::factory()->hasBookings(2)->create();

        $response = $this->getJson("/api/events/{$event->id}/bookings");

        $response->assertOk()
            ->assertJsonCount(2, 'data');
    }
}
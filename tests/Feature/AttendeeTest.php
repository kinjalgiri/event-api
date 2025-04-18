<?php

namespace Tests\Feature;

use App\Models\Attendee;
use App\Models\Event;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AttendeeTest extends TestCase
{
    use RefreshDatabase;


    public function test_can_get_all_attendees()
    {
        Attendee::factory()->count(3)->create();

        $response = $this->getJson('/api/attendees');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'name', 'email', 'phone']
                ],
                'links',
                'meta'
            ]);
    }


    public function test_can_create_an_attendee()
    {
        $attendeeData = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'phone' => '1234567890'
        ];

        $response = $this->postJson('/api/attendees', $attendeeData);

        $response->assertStatus(201)
            ->assertJsonFragment(['name' => 'John Doe']);

        $this->assertDatabaseHas('attendees', ['email' => 'john@example.com']);
    }


    public function test_cannot_create_attendee_with_duplicate_email()
    {
        Attendee::factory()->create(['email' => 'john@example.com']);

        $response = $this->postJson('/api/attendees', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'phone' => '1234567890'
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }


    public function can_get_single_attendee()
    {
        $attendee = Attendee::factory()->create();

        $response = $this->getJson("/api/attendees/{$attendee->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'email',
                    'phone',
                    'bookings'
                ]
            ])
            ->assertJson([
                'data' => [
                    'id' => $attendee->id,
                    'name' => $attendee->name
                ]
            ]);
    }


    public function test_can_update_attendee()
    {
        $attendee = Attendee::factory()->create();

        $updateData = [
            'name' => 'Updated Name',
            'phone' => '9876543210'
        ];

        $response = $this->putJson("/api/attendees/{$attendee->id}", $updateData);

        $response->assertStatus(200)
            ->assertJsonFragment(['name' => 'Updated Name']);

        $this->assertDatabaseHas('attendees', [
            'id' => $attendee->id,
            'phone' => '9876543210'
        ]);
    }


    public function test_can_delete_attendee()
    {
        $attendee = Attendee::factory()->create();

        $response = $this->deleteJson("/api/attendees/{$attendee->id}");

        $response->assertStatus(204);
        $this->assertSoftDeleted('attendees', ['id' => $attendee->id]);
    }


    public function test_cannot_delete_attendee_with_bookings()
    {
        $attendee = Attendee::factory()->create();
        $event = Event::factory()->create();
        $event->bookings()->create([
            'attendee_id' => $attendee->id,
            'booking_reference' => 'TEST123',
            'status' => 'confirmed'
        ]);

        $response = $this->deleteJson("/api/attendees/{$attendee->id}");

        $response->assertStatus(409); // Conflict
        $this->assertDatabaseHas('attendees', ['id' => $attendee->id]);
    }
}

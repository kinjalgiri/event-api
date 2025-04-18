<?php

namespace Tests\Unit;

use App\Exceptions\EventServiceException;
use App\Models\Event;
use App\Models\User;
use App\Services\EventService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EventServiceTest extends TestCase
{
    use RefreshDatabase;

    protected $service;
    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new EventService();
        $this->user = User::factory()->create();
    }


    public function test_can_get_all_events()
    {
        Event::factory()->count(3)->create(['user_id' => $this->user->id]);

        $events = $this->service->getAllEvents();

        $this->assertCount(3, $events->items());
        $this->assertEquals(3, $events->total());
    }


    public function test_can_filter_events()
    {
        Event::factory()->create(['title' => 'Tech Conference', 'country' => 'US']);
        Event::factory()->create(['title' => 'Music Festival', 'country' => 'UK']);

        $results = $this->service->getAllEvents(['search' => 'Tech']);

        $this->assertCount(1, $results->items());
        $this->assertEquals('Tech Conference', $results->first()->title);
    }


    public function test_can_create_an_event()
    {
        $data = [
            'title' => 'New Event',
            'description' => 'Description',
            'start_date' => now()->addDays(1)->format('Y-m-d H:i:s'),
            'end_date' => now()->addDays(2)->format('Y-m-d H:i:s'),
            'capacity' => 100,
            'country' => 'US',
            'city' => 'New York'
        ];

        $event = $this->service->createEvent($data, $this->user);

        $this->assertDatabaseHas('events', ['title' => 'New Event']);
        $this->assertEquals($this->user->id, $event->user_id);
    }


    public function test_throws_exception_when_creation_fails()
    {
        $this->expectException(EventServiceException::class);

        $invalidData = ['title' => null]; // Missing required fields

        $this->service->createEvent($invalidData, $this->user);
    }


    public function test_can_update_an_event()
    {
        $event = Event::factory()->create(['user_id' => $this->user->id]);

        $updated = $this->service->updateEvent($event, ['title' => 'Updated Title']);

        $this->assertEquals('Updated Title', $updated->title);
        $this->assertDatabaseHas('events', ['title' => 'Updated Title']);
    }


    public function test_throws_exception_when_update_fails()
    {
        $this->expectException(EventServiceException::class);

        $event = Event::factory()->create();
        $this->service->updateEvent($event, ['title' => null]); // Invalid data
    }


    public function test_can_delete_an_event()
    {
        $event = Event::factory()->create();

        $result = $this->service->deleteEvent($event);

        $this->assertTrue($result);
        $this->assertSoftDeleted('events', ['id' => $event->id]);
    }


    public function test_prevents_deleting_event_with_bookings()
    {
        $this->expectException(EventServiceException::class);
        $this->expectExceptionCode(409);

        $event = Event::factory()->hasBookings(1)->create();

        $this->service->deleteEvent($event);
    }
}
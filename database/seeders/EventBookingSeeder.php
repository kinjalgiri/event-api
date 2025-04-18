<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Event;
use App\Models\Attendee;
use App\Models\Booking;
use Illuminate\Support\Str;

class EventBookingSeeder extends Seeder
{
    public function run()
    {
        // Create organizer user
        $organizer = User::create([
            'name' => 'Event Organizer',
            'email' => 'organizer@example.com',
            'password' => bcrypt('password'),
        ]);

        // Create events
        $events = Event::factory(5)->create([
            'user_id' => $organizer->id,
        ]);

        // Create attendees and bookings
        $events->each(function ($event) {
            $attendees = Attendee::factory(3)->create();

            $attendees->each(function ($attendee) use ($event) {
                Booking::create([
                    'event_id' => $event->id,
                    'attendee_id' => $attendee->id,
                    'booking_reference' => Str::uuid(),
                    'status' => 'confirmed',
                ]);

                $event->decrement('available_slots');
            });
        });
    }
}
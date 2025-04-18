<?php

namespace App\Services;

use App\Models\Event;
use App\Models\Booking;
use App\Models\Attendee;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BookingService
{
    public function listBookings($perPage = 10)
    {
        return Booking::with(['event', 'attendee'])->latest()->paginate($perPage);
    }

    public function createBooking(array $data): Booking
    {
        return DB::transaction(function () use ($data) {
            $event = Event::findOrFail($data['event_id']);

            // Create or find attendee
            $attendee = Attendee::firstOrCreate(
                ['email' => $data['email']],
                ['name' => $data['name'], 'phone' => $data['phone']]
            );

            // Create booking
            return $event->bookings()->create([
                'attendee_id' => $attendee->id,
                'booking_reference' => Str::uuid(),
                'status' => 'confirmed',
            ]);
        });
    }

    public function updateBooking(Booking $booking, array $data): Booking
    {
        return DB::transaction(function () use ($booking, $data) {
            $booking->update($data);
            return $booking->fresh()->load(['event', 'attendee']);
        });
    }

    public function deleteBooking(Booking $booking): void
    {
        DB::transaction(function () use ($booking) {
            $booking->delete();
        });
    }

    public function getBookingDetails(Booking $booking): Booking
    {
        return $booking->load(['event', 'attendee']);
    }
}

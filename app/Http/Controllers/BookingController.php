<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Http\Requests\StoreBookingRequest;
use App\Http\Requests\UpdateBookingRequest;
use App\Http\Resources\BookingResource;
use App\Http\Resources\BookingCollection;
use Symfony\Component\HttpFoundation\Response;

use App\Services\BookingService;

class BookingController extends Controller
{
    protected $bookingService;

    public function __construct(BookingService $bookingService)
    {
        $this->bookingService = $bookingService;
    }

    public function index()
    {
        try {
            $bookings = $this->bookingService->listBookings();
            return new BookingCollection($bookings);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve bookings',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function store(StoreBookingRequest $request)
    {
        try {
            $booking = $this->bookingService->createBooking($request->validated());
            return (new BookingResource($booking->load(['event', 'attendee'])))
                ->response()
                ->setStatusCode(Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Booking failed',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show(Booking $booking)
    {
        try {
            $booking = $this->bookingService->getBookingDetails($booking);
            return new BookingResource($booking);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve booking',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function update(UpdateBookingRequest $request, Booking $booking)
    {
        try {
            $booking = $this->bookingService->updateBooking($booking, $request->validated());
            return new BookingResource($booking);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to update booking',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy(Booking $booking)
    {
        try {
            $this->bookingService->deleteBooking($booking);
            return response()->noContent();
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to delete booking',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}

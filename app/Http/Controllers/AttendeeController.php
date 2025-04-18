<?php

namespace App\Http\Controllers;

use App\Models\Attendee;
use App\Http\Requests\StoreAttendeeRequest;
use App\Http\Requests\UpdateAttendeeRequest;
use App\Http\Resources\AttendeeResource;
use App\Http\Resources\ErrorResource;
use Illuminate\Http\Response;

class AttendeeController extends Controller
{
    /**
     * Display a listing of attendees
     */
    public function index()
    {
        $attendees = Attendee::with('bookings.event')
            ->latest()
            ->filter(request(['search']))
            ->paginate(10);

        return AttendeeResource::collection($attendees);
    }

    /**
     * Store a newly created attendee
     */
    public function store(StoreAttendeeRequest $request)
    {
        $attendee = Attendee::create($request->validated());

        return (new AttendeeResource($attendee))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * Display the specified attendee
     */
    public function show(Attendee $attendee)
    {
        return new AttendeeResource($attendee->load('bookings.event'));
    }

    /**
     * Update the specified attendee
     */
    public function update(UpdateAttendeeRequest $request, Attendee $attendee)
    {
        $attendee->update($request->validated());

        return new AttendeeResource($attendee->fresh());
    }

    /**
     * Remove the specified attendee
     */
    public function destroy(Attendee $attendee)
    {
        // Prevent deletion if attendee has bookings
        if ($attendee->bookings()->exists()) {
            return new ErrorResource([
                'code' => Response::HTTP_CONFLICT,
                'message' => 'Cannot delete attendee with existing bookings',
            ]);
        }

        $attendee->delete();

        return response()->noContent();
    }
}
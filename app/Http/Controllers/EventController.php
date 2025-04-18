<?php

namespace App\Http\Controllers;

use App\Services\EventService;
use App\Http\Requests\StoreEventRequest;
use App\Http\Requests\UpdateEventRequest;
use App\Http\Resources\EventResource;
use App\Http\Resources\BookingResource;
use App\Http\Resources\EventCollection;
use App\Http\Resources\ErrorResource;
use App\Models\Event;
use App\Exceptions\EventServiceException;
use Symfony\Component\HttpFoundation\Response;

class EventController extends Controller
{
    protected $eventService;

    public function __construct(EventService $eventService)
    {
        $this->eventService = $eventService;
    }

    /**
     * Display a paginated list of events
     */
    public function index()
    {
        try {
            $events = $this->eventService->getAllEvents(
                request(['search', 'country', 'city', 'from_date', 'to_date', 'available'])
            );
            return new EventCollection($events);
        } catch (EventServiceException $e) {
            return new ErrorResource([
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
                'details' => config('app.debug') ? $e->getMessage() : null
            ]);
        }
    }

    /**
     * Store a newly created event
     */
    public function store(StoreEventRequest $request)
    {
        try {
            $event = $this->eventService->createEvent($request->validated(), $request->user());
            return new EventResource($event->load('user'));
        } catch (EventServiceException $e) {
            return new ErrorResource([
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
                'details' => config('app.debug') ? $e->getMessage() : null
            ]);
        }
    }

    /**
     * Display the specified event
     */
    public function show($id)
    {
        try {
            $event = $this->eventService->getEventWithRelations($id);
            return new EventResource($event);
        } catch (EventServiceException $e) {
            return new ErrorResource([
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
                'details' => config('app.debug') ? $e->getMessage() : null
            ]);
        }
    }

    /**
     * Update the specified event
     */
    public function update(UpdateEventRequest $request, Event $event)
    {
        try {
            $updatedEvent = $this->eventService->updateEvent($event, $request->validated());
            return new EventResource($updatedEvent->load('user'));
        } catch (EventServiceException $e) {
            return new ErrorResource([
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
                'details' => config('app.debug') ? $e->getMessage() : null
            ]);
        }
    }

    /**
     * Remove the specified event
     */
    public function destroy(Event $event)
    {
        try {
            $this->eventService->deleteEvent($event);
            return response()->noContent();
        } catch (EventServiceException $e) {
            return new ErrorResource([
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
                'details' => config('app.debug') ? $e->getMessage() : null
            ]);
        }
    }

    /**
     * Get bookings for a specific event
     */
    public function bookings(Event $event)
    {
        try {
            $bookings = $event->bookings()
                ->with('attendee')
                ->latest()
                ->paginate(10);

            return BookingResource::collection($bookings);
        } catch (\Exception $e) {
            return new ErrorResource([
                'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                'message' => 'Failed to retrieve bookings',
                'details' => config('app.debug') ? $e->getMessage() : null
            ]);
        }
    }
}

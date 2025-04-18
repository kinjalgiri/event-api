<?php

namespace App\Services;

use App\Models\Event;
use App\Exceptions\EventServiceException;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class EventService
{
    public function getAllEvents(array $filters = [])
    {
        try {
            return Event::with(['user', 'bookings'])
                ->withCount('bookings')
                ->filter($filters)
                ->latest()
                ->paginate(10);
        } catch (\Exception $e) {
            Log::error('EventService getAllEvents error: ' . $e->getMessage());
            throw new EventServiceException(
                'Failed to retrieve events',
                Response::HTTP_INTERNAL_SERVER_ERROR,
                $e
            );
        }
    }

    public function createEvent(array $validatedData, $user)
    {
        try {
            return $user->events()->create($validatedData);
        } catch (\Exception $e) {
            Log::error('EventService createEvent error: ' . $e->getMessage());
            throw new EventServiceException(
                'Failed to create event',
                Response::HTTP_INTERNAL_SERVER_ERROR,
                $e
            );
        }
    }

    public function updateEvent(Event $event, array $validatedData)
    {
        try {
            $event->update($validatedData);
            return $event->fresh();
        } catch (EventServiceException $e) {
            throw $e;
        } catch (\Exception $e) {
            Log::error('EventService updateEvent error: ' . $e->getMessage());
            throw new EventServiceException(
                'Failed to update event',
                Response::HTTP_INTERNAL_SERVER_ERROR,
                $e
            );
        }
    }

    public function deleteEvent(Event $event)
    {
        try {
            if ($event->bookings()->exists()) {
                throw new EventServiceException(
                    'Cannot delete event with existing bookings',
                    Response::HTTP_CONFLICT
                );
            }

            $event->delete();
            return true;
        } catch (EventServiceException $e) {
            throw $e;
        } catch (\Exception $e) {
            Log::error('EventService deleteEvent error: ' . $e->getMessage());
            throw new EventServiceException(
                'Failed to delete event',
                Response::HTTP_INTERNAL_SERVER_ERROR,
                $e
            );
        }
    }

    // In EventService
    public function getEventWithRelations($id)
    {
        try {
            return Event::with(['user', 'bookings'])->findOrFail($id);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            throw new EventServiceException('Event not found', Response::HTTP_NOT_FOUND, $e);
        }
    }
}

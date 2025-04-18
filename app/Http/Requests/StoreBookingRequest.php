<?php

namespace App\Http\Requests;

use App\Models\Event;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreBookingRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Authentication can be added later
    }

    public function rules()
    {
        return [
            'event_id' => [
                'required',
                'exists:events,id',
                function ($attribute, $value, $fail) {
                    $event = Event::find($value);
                    if ($event && $event->available_slots <= 0) {
                        $fail('This event is fully booked.');
                    }
                }
            ],
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                Rule::unique('attendees', 'email')->where(function ($query) {
                    return $query->whereExists(function ($query) {
                        $query->select('id')
                            ->from('bookings')
                            ->whereColumn('bookings.attendee_id', 'attendees.id')
                            ->where('bookings.event_id', $this->input('event_id'));
                    });
                })
            ],
            'phone' => 'required|string|max:20'
        ];
    }

    public function messages()
    {
        return [
            'event_id.exists' => 'The selected event does not exist.',
            'email.unique' => 'You have already booked this event with this email address.',
            'phone.required' => 'A phone number is required for booking.'
        ];
    }
}
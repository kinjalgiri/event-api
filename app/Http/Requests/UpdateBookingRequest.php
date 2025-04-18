<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBookingRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'status' => 'sometimes|in:confirmed,pending,cancelled',
            'attendee_id' => 'sometimes|exists:attendees,id',
            'event_id' => 'sometimes|exists:events,id',
        ];
    }
}
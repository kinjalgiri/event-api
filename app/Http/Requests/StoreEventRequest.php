<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEventRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'start_date' => [
                'required',
                'date',
                'after:now',
                function ($attribute, $value, $fail) {
                    if (strtotime($value) < strtotime('today midnight')) {
                        $fail('The start date must be in the future.');
                    }
                }
            ],
            'end_date' => 'required|date|after:start_date',
            'capacity' => 'required|integer|min:1|max:10000',
            'country' => 'required|string|max:100',
            'city' => 'required|string|max:100',
        ];
    }

    public function messages()
    {
        return [
            'start_date.after' => 'The event must start in the future.',
            'end_date.after' => 'The end date must be after the start date.',
            'capacity.min' => 'The event must have at least 1 attendee.',
            'capacity.max' => 'The event cannot have more than 10,000 attendees.',
        ];
    }

}
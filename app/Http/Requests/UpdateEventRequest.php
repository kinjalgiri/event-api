<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Event;

class UpdateEventRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'title' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'start_date' => [
                'sometimes',
                'date',
                'after:now',
                function ($attribute, $value, $fail) {
                    if (strtotime($value) < strtotime('today midnight')) {
                        $fail('The start date must be in the future.');
                    }
                }
            ],
            'end_date' => 'sometimes|date|after:start_date',
            'capacity' => [
                'sometimes',
                'integer',
                'min:1',
                'max:10000',
                function ($attribute, $value, $fail) {
                    if ($value < $this->event->bookings()->count()) {
                        $fail('Capacity cannot be less than current bookings count.');
                    }
                }
            ],
            'country' => 'sometimes|string|max:100',
            'city' => 'sometimes|string|max:100',
        ];
    }

 }
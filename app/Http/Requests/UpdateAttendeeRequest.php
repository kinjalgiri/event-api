<?php

namespace App\Http\Requests;

use App\Models\Attendee;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAttendeeRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Authorization handled at controller level
    }

    public function rules()
    {
        return [
            'name' => 'sometimes|string|max:255',
            'email' => [
                'sometimes',
                'email',
                Rule::unique('attendees', 'email')->ignore($this->attendee)
            ],
            'phone' => 'sometimes|string|max:20',
        ];
    }
}
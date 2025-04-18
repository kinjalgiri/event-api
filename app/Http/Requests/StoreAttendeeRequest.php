<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAttendeeRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Authorization handled at controller level
    }

    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:attendees,email',
            'phone' => 'required|string|max:20',
        ];
    }

    public function messages()
    {
        return [
            'email.unique' => 'An attendee with this email already exists.',
            'phone.required' => 'Phone number is required for communication.',
        ];
    }
}
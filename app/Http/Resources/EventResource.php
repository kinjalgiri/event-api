<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class EventResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'capacity' => $this->capacity,
            'available_slots' => $this->available_slots,
            'country' => $this->country,
            'city' => $this->city,
            'is_fully_booked' => $this->available_slots <= 0,
            'user_id' => new UserResource($this->whenLoaded('user')),
            'bookings' => BookingResource::collection($this->whenLoaded('bookings')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}

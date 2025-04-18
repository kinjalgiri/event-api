<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['title', 'description', 'start_date', 'end_date', 'capacity', 'country', 'city'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    // Accessor for available_slots
    public function getAvailableSlotsAttribute()
    {
        return $this->capacity - $this->bookings()->count();
    }

    // Check if event has available slots
    public function hasAvailableSlots()
    {
        return $this->available_slots > 0;
    }

    // Scope for events with available slots
    public function scopeWithAvailableSlots($query)
    {
        return $query->whereRaw('capacity > (select count(*) from bookings where bookings.event_id = events.id)');
    }

    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['search'] ?? false, fn($query, $search) =>
            $query->where('title', 'like', '%'.$search.'%')
                  ->orWhere('description', 'like', '%'.$search.'%')
        );

        $query->when($filters['country'] ?? false, fn($query, $country) =>
            $query->where('country', $country)
        );

        $query->when($filters['city'] ?? false, fn($query, $city) =>
            $query->where('city', $city)
        );

        $query->when($filters['from_date'] ?? false, fn($query, $fromDate) =>
            $query->where('start_date', '>=', $fromDate)
        );

        $query->when($filters['to_date'] ?? false, fn($query, $toDate) =>
            $query->where('end_date', '<=', $toDate)
        );

        $query->when($filters['available'] ?? false, fn($query) =>
            $query->withAvailableSlots()
        );
    }
}

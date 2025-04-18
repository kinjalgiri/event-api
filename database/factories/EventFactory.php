<?php

namespace Database\Factories;

use App\Models\Event;
use Illuminate\Database\Eloquent\Factories\Factory;

class EventFactory extends Factory
{
    protected $model = Event::class;

    public function definition()
    {
        return [
            'title' => $this->faker->sentence,
            'description' => $this->faker->paragraph,
            'start_date' => now()->addDays(1),
            'end_date' => now()->addDays(2),
            'capacity' => 10,
            'country' => 'IN',
            'city' => 'Surat',
            'user_id' => \App\Models\User::factory()
        ];
    }
}
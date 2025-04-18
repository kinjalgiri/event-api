<?php

namespace Database\Factories;

use App\Models\Attendee;
use Illuminate\Database\Eloquent\Factories\Factory;

class AttendeeFactory extends Factory
{
    protected $model = Attendee::class;

    public function definition(): array
    {
        return [
            'name'  => $this->faker->name(),
            'email' => $this->faker->safeEmail(),
            'phone' => $this->faker->phoneNumber(),
        ];
    }
}

<?php

namespace Database\Factories;

use App\Models\Booking;
use App\Models\Event;
use App\Models\Attendee;
use Illuminate\Database\Eloquent\Factories\Factory;

class BookingFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Booking::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'event_id' => Event::factory(),
            'attendee_id' => Attendee::factory(),
            'booking_reference' => $this->faker->unique()->regexify('[A-Z0-9]{8}'),
            'status' => $this->faker->randomElement(['confirmed', 'waiting', 'cancelled']),
            'created_at' => $this->faker->dateTimeBetween('-1 month', 'now'),
            'updated_at' => $this->faker->dateTimeBetween('-1 month', 'now'),
        ];
    }

    /**
     * Configure the model factory.
     *
     * @return $this
     */
    public function configure()
    {
        return $this->afterMaking(function (Booking $booking) {
            // Additional configuration after making (optional)
        })->afterCreating(function (Booking $booking) {
            // Additional configuration after creating (optional)
        });
    }

    /**
     * State for confirmed bookings
     */
    public function confirmed()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'confirmed',
            ];
        });
    }

    /**
     * State for waiting bookings
     */
    public function waiting()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'waiting',
            ];
        });
    }

    /**
     * State for cancelled bookings
     */
    public function cancelled()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'cancelled',
            ];
        });
    }
}
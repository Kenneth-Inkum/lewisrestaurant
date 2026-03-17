<?php

namespace Database\Factories;

use App\Models\Reservation;
use App\Enums\ReservationStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Reservation>
 */
class ReservationFactory extends Factory
{
    public function definition(): array
    {
        $date = fake()->dateTimeBetween('-7 days', '+30 days');

        return [
            'name' => fake()->name(),
            'email' => fake()->safeEmail(),
            'phone' => fake()->phoneNumber(),
            'party_size' => fake()->numberBetween(1, 8),
            'reservation_date' => $date->format('Y-m-d'),
            'reservation_time' => fake()->randomElement(['17:00', '17:30', '18:00', '18:30', '19:00', '19:30', '20:00', '20:30', '21:00']),
            'status' => fake()->randomElement([ReservationStatus::Pending, ReservationStatus::Confirmed, ReservationStatus::Confirmed]),
            'notes' => fake()->optional(0.3)->sentence(),
        ];
    }

    public function confirmed(): static
    {
        return $this->state(['status' => ReservationStatus::Confirmed]);
    }

    public function today(): static
    {
        return $this->state(['reservation_date' => today()->format('Y-m-d')]);
    }
}

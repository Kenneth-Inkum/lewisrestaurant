<?php

namespace Database\Factories;

use App\Models\RestaurantTable;
use App\Enums\TableStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<RestaurantTable>
 */
class RestaurantTableFactory extends Factory
{
    public function definition(): array
    {
        return [
            'number' => fake()->unique()->numberBetween(1, 50),
            'name' => null,
            'capacity' => fake()->randomElement([2, 2, 4, 4, 4, 6, 8]),
            'section' => fake()->randomElement(['main', 'patio', 'bar', 'private']),
            'status' => fake()->randomElement([TableStatus::Available, TableStatus::Available, TableStatus::Occupied, TableStatus::Reserved]),
        ];
    }

    public function available(): static
    {
        return $this->state(['status' => TableStatus::Available]);
    }

    public function occupied(): static
    {
        return $this->state(['status' => TableStatus::Occupied]);
    }
}

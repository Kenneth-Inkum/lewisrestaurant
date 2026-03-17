<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\RestaurantTable;
use App\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Order>
 */
class OrderFactory extends Factory
{
    public function definition(): array
    {
        $subtotal = fake()->randomFloat(2, 20, 200);
        $tax = $subtotal * 0.08;

        return [
            'restaurant_table_id' => RestaurantTable::factory(),
            'status' => fake()->randomElement([OrderStatus::Pending, OrderStatus::InProgress, OrderStatus::Ready, OrderStatus::Delivered]),
            'subtotal' => $subtotal,
            'tax' => $tax,
            'total' => $subtotal + $tax,
            'notes' => fake()->optional(0.2)->sentence(),
        ];
    }

    public function active(): static
    {
        return $this->state(['status' => OrderStatus::InProgress]);
    }
}

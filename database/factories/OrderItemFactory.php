<?php

namespace Database\Factories;

use App\Models\MenuItem;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<OrderItem>
 */
class OrderItemFactory extends Factory
{
    public function definition(): array
    {
        return [
            'order_id' => Order::factory(),
            'menu_item_id' => MenuItem::factory(),
            'name' => fake()->words(3, true),
            'price' => fake()->randomFloat(2, 8, 65),
            'quantity' => fake()->numberBetween(1, 3),
            'notes' => fake()->optional(0.2)->sentence(),
        ];
    }
}

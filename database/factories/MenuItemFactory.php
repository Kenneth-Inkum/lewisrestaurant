<?php

namespace Database\Factories;

use App\Models\MenuCategory;
use App\Models\MenuItem;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<MenuItem>
 */
class MenuItemFactory extends Factory
{
    public function definition(): array
    {
        $name = fake()->unique()->randomElement([
            'Seared Scallops', 'Wagyu Beef Tartare', 'Lobster Bisque', 'Caesar Salad',
            'Truffle Fettuccine', 'Pan-Seared Salmon', 'Filet Mignon', 'Rack of Lamb',
            'Duck Confit', 'Roasted Chicken', 'Mushroom Risotto', 'Shrimp Cocktail',
            'Charcuterie Board', 'Burrata Caprese', 'French Onion Soup',
            'Chocolate Lava Cake', 'Crème Brûlée', 'Tiramisu', 'New York Cheesecake',
            'House-Cut Fries', 'Truffle Mac & Cheese', 'Garlic Mashed Potatoes',
        ]);

        $allTags = ['gluten-free', 'vegetarian', 'vegan', 'dairy-free', 'nut-free', 'spicy'];

        return [
            'menu_category_id' => MenuCategory::factory(),
            'name' => $name,
            'slug' => Str::slug($name),
            'description' => fake()->sentence(12),
            'price' => fake()->randomFloat(2, 8, 65),
            'image' => null,
            'dietary_tags' => fake()->optional(0.6)->randomElements($allTags, fake()->numberBetween(0, 2)),
            'is_available' => fake()->boolean(85),
            'is_featured' => fake()->boolean(20),
            'sort_order' => fake()->numberBetween(0, 20),
        ];
    }

    public function featured(): static
    {
        return $this->state(['is_featured' => true, 'is_available' => true]);
    }

    public function unavailable(): static
    {
        return $this->state(['is_available' => false]);
    }
}

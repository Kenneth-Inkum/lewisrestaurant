<?php

namespace Database\Factories;

use App\Models\MenuCategory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<MenuCategory>
 */
class MenuCategoryFactory extends Factory
{
    public function definition(): array
    {
        $name = fake()->unique()->randomElement([
            'Starters', 'Soups & Salads', 'Pasta', 'Seafood', 'Steaks & Chops',
            'Poultry', 'Burgers', 'Sandwiches', 'Sides', 'Desserts',
            'Cocktails', 'Wine', 'Beer', 'Non-Alcoholic', 'Coffee & Tea',
        ]);

        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'description' => fake()->optional()->sentence(),
            'sort_order' => fake()->numberBetween(0, 10),
            'is_active' => true,
        ];
    }
}

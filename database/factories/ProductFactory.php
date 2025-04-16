<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $category = ['pizza', 'drink'];
        return [
            'name' => fake()->words(2, true),
            'description' => fake()->sentence(),
            'price' => fake()->randomFloat(2, 100, 1000),
            'category' => $category[rand(0, 1)],
            'image' => 'images/' . fake()->word . '.jpg',
            'is_available' => fake()->boolean(90)
        ];
    }
}

<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Image;
use App\Models\Product;
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
        return [
            'name' => fake()->words(2, true),
            'description' => fake()->sentence(),
            'price' => fake()->randomFloat(2, 100, 1000),
            'category_id' => Category::inRandomOrder()->first()->id,
            'is_available' => fake()->boolean(90)
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (Product $product) {
            Image::factory()->count(rand(2, 3))->create([
                'imageable_id' => $product->id,
                'imageable_type' => Product::class,
            ]);
        });
    }
}

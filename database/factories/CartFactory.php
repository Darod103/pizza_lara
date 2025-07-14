<?php

namespace Database\Factories;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Cart>
 */
class CartFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
        ];
    }

    public function withItem(int $count = 1): self
    {
        return $this->afterCreating(function (Cart $cart) use ($count) {
            CartItem::factory()->count($count)->create([
                'cart_id' => $cart->id,
            ]);
        });
    }


}

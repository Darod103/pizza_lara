<?php

namespace Tests\Feature;

use App\Models\Cart;
use App\Models\Product;
use App\Models\User;
use Database\Seeders\CartSeeder;
use Database\Seeders\CategorySeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CartTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(CategorySeeder::class);
        $this->user = User::factory()->create();
    }

    public function test_index_with_login_cart_success(): void
    {
        $cart = Cart::factory()->for($this->user)->withItem(2)->create();
        $response = $this->actingAs($this->user)->get('api/cart');

        $response->assertOk();
        $response->assertJsonFragment(['id' => $cart->id]);
        $response->assertJsonPath('data.items.0.id', $cart->cartItems[0]->id);
    }

    public function test_index_without_login_cart_fail(): void
    {
        $response = $this->getJson('api/cart');

        $response->assertStatus(401);
        $response->assertJsonFragment(['message' => 'Unauthenticated.']);
    }


    public function test_store_item_in_cart_with_login_cart_success(): void
    {
        $product = Product::factory()->create();
        $response = $this->actingAs($this->user)->postJson('api/cart', [
            'product_id' => $product->id,
            'quantity' => 1
        ]);

        $response->assertCreated();
        $response->assertJsonPath('items.0.product.id', $product->id);
    }

    public function test_store_item_with_over_limit_in_cart_fail(): void
    {
        $product = Product::factory()->create();
        $limit = $product->category->name != "Drink" ? 20 : 10;
        $response = $this->actingAs($this->user)->post('api/cart', [
            'product_id' => $product->id,
            'quantity' => $limit + 1
        ]);

        $response->assertStatus(429);
    }

    public function test_store_item_not_available_cart_fail(): void
    {
        $product = Product::factory()->create([
            'is_available' => false
        ]);
        $response = $this->actingAs($this->user)->post('api/cart', [
            'product_id' => $product->id,
            'quantity' => 1
        ]);

        $response->assertStatus(422);
    }

    public function test_cannot_add_nonexistent_item_to_cart(): void
    {
        $response = $this->actingAs($this->user)->postJson('api/cart', [
            'product_id' => 999,
            'quantity' => 1
        ]);

        $response->assertStatus(422);
    }

    public function test_update_item_in_cart_success(): void
    {
        $cart = Cart::factory()->for($this->user)->withItem()->create();
        $productId = $cart->cartItems->pluck('product_id')->first();

        $response = $this->actingAs($this->user)->putJson("api/cart/item/{$productId}", [
            'quantity' => 2
        ]);

        $response->assertOk();
        $this->assertDatabaseHas('cart_items', [
            'product_id' => $productId,
            'quantity' => 2
        ]);
    }

    public function test_update_item_not_available_cart_fail(): void
    {
        $product = Product::factory()->create();
        $response = $this->actingAs($this->user)->putJson("api/cart/item/{$product->id}", [
            'quantity' => 1
        ]);
        $response->assertStatus(422);
    }

    public function test_destroy_item_in_cart_success(): void
    {
        $cart = Cart::factory()->for($this->user)->withItem()->create();
        $productId = $cart->cartItems->pluck('product_id')->first();

        $response = $this->actingAs($this->user)->deleteJson("api/cart/item/{$productId}");

        $response->assertOk();
    }

    public function test_destroy_item_not_available_in_cart_fail(): void
    {
        $response = $this->actingAs($this->user)->deleteJson("api/cart/item/1");

        $response->assertStatus(422);
    }

    public function test_destroy_cart_success(): void
    {
        $cart = Cart::factory()->for($this->user)->withItem()->create();
        $response = $this->actingAs($this->user)->deleteJson("api/cart/{$cart->id}");

        $response->assertOk();
    }

    public function test_destroy_cart_fail(): void
    {
        $response = $this->actingAs($this->user)->deleteJson("api/cart/2");

        $response->assertStatus(404);
    }

}

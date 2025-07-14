<?php

namespace Tests\Feature;

use App\Models\Cart;
use App\Models\Order;
use App\Models\User;
use Database\Seeders\CategorySeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

use Tests\TestCase;

class OrderTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed([CategorySeeder::class, RoleSeeder::class]);
        $this->admin = User::factory()->create()->assignRole('admin');
        $this->user = User::factory()->create();
    }


    public function test_order_store_with_delivery_request_success(): void
    {
        Cart::factory()->for($this->user)->withItem(2)->create();
        $data = [
            'email' => 'test@test.com',
            'phone' => '+79990593223',
            'address' => 'Test st.Test',
            'delivery_time' => '12:23',
        ];
        $response = $this->actingAs($this->user)->postJson('api/order', $data);

        $data = $response->json()['data'];

        $response->assertCreated();
        $this->assertDatabaseHas('orders', ['id' => $data['id']]);
    }

    public function test_order_store_without_delivery_request_fail(): void
    {
        Cart::factory()->for($this->user)->withItem(2)->create();
        $data = [
            'email' => '',
            'phone' => '+79990593223',
            'address' => 'Test st.Test',
            'delivery_time' => '12:23',
        ];

        $response = $this->actingAs($this->user)->postJson('api/order', $data);

        $response->assertStatus(422);
    }

    public function test_index_order_success(): void
    {
        $cart = Cart::factory()->for($this->user)->create();
        $order = Order::factory()->for($cart)->withItems(2)->create();
        $response = $this->actingAs($this->user)->getJson('api/order');

        $response->assertOk();
        $this->assertDatabaseHas('orders', ['id' => $order->id]);
        $this->assertDatabaseCount('order_items', 2);
    }

    public function test_index_order_fail(): void
    {
        $response = $this->actingAs($this->user)->getJson('api/order');

        $response->assertStatus(404);
    }

    public function test_show_order_success(): void
    {
        $cart = Cart::factory()->for($this->user)->create();
        $order = Order::factory()->for($cart)->withItems(2)->create();
        $response = $this->actingAs($this->user)->getJson('api/order/' . $order->id);


        $response->assertOk();
        $response->assertJsonFragment([
            'id' => $order->id,
        ]);
    }

    public function test_show_order_fail(): void
    {
        $response = $this->actingAs($this->user)->getJson('api/order/9999');

        $response->assertNotFound();
    }

    public function test_cansel_order_success()
    {
        $cart = Cart::factory()->for($this->user)->create();
        $order = Order::factory()->for($cart)->withItems(2)->create();
        $response = $this->actingAs($this->user)->deleteJson('api/order/' . $order->id);

        $response->assertOk();
        $this->assertDatabaseHas('orders', ['status' => 'cancelled']);
    }

    public function test_cansel_order_fail(): void
    {
        $response = $this->actingAs($this->user)->deleteJson('api/order/9999');

        $response->assertNotFound();
    }

    public function test_order_delivery_update_success()
    {
        $data = [
            'email' => 'test@test.com',
            'phone' => '+79990593223',
            'address' => 'Test st.Test',
            'delivery_time' => '12:23',
        ];
        $cart = Cart::factory()->for($this->user)->create();
        $order = Order::factory()->for($cart)->withItems(2)->create();
        $response = $this->actingAs($this->user)->patchJson('api/order/' . $order->id, $data);


        $response->assertOk();
        $this->assertDatabaseHas('orders', $data);
    }

    public function test_order_delivery_update_fail(): void
    {
        $data = [
            'email' => '',
            'phone' => '+79990593223',
            'address' => 'Test st.Test',
            'delivery_time' => '12:23',
        ];
        $cart = Cart::factory()->for($this->user)->create();
        $order = Order::factory()->for($cart)->withItems(2)->create();
        $response = $this->actingAs($this->user)->patchJson('api/order/' . $order->id, $data);

        $response->assertStatus(422);
    }

    public function test_admin_destroy_order_success()
    {
        $order = Order::factory()->create();
        $response = $this->actingAs($this->admin)->deleteJson('api/admin/order/' . $order->id);

        $response->assertOk();
        $this->assertDatabaseMissing('orders', ['id' => $order->id]);
    }

    public function test_admin_destroy_order_fail(): void
    {
        $response = $this->actingAs($this->admin)->deleteJson('api/admin/order/9999');

        $response->assertNotFound();
    }

    public function test_admin_index_orders_success()
    {
        Order::factory()->count(10)->create();
        $response = $this->actingAs($this->admin)->getJson('api/admin/order');
        $response->assertOk();
        $this->assertDatabaseCount('orders', 10);
    }

    public function test_admin_index_orders_fail()
    {
        $response = $this->actingAs($this->admin)->getJson('api/admin/order');

        $response->assertNotFound();
    }

    public function test_admin_index_orders_without_admin_role_fail(): void
    {
        Order::factory()->count(10)->create();
        $response = $this->actingAs($this->user)->getJson('api/admin/order');

        $response->assertForbidden();
    }

    public function test_admin_update_order_status_success(): void
    {
        $data = [
            'status' => 'completed',
        ];
        $order = Order::factory()->create();

        $response = $this->actingAs($this->admin)->patchJson('api/admin/order/' . $order->id, $data);

        $response->assertOk();
        $this->assertDatabaseHas('orders', ['id' => $order->id, 'status' => 'completed']);
    }

    public function test_admin_update_order_status_fail(): void
    {
        $data = [
            'status' => 'testsss',
        ];

        $order = Order::factory()->create();
        $response = $this->actingAs($this->admin)->patchJson('api/admin/order/' . $order->id, $data);

        $response->assertStatus(422);
    }
}

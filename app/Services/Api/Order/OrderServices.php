<?php

namespace App\Services\Api\Order;

use App\Exceptions\CartItemNotFoundException;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;

class OrderServices
{

    public function createOrder(array $data, int $userId): Order
    {
        DB::beginTransaction();
        try {
            $cart = Cart::where('user_id', $userId)->with('cartItems.product')->first();

            if (!$cart || $cart->cartItems->isEmpty()) {
                throw new CartItemNotFoundException();
            }

            $order = Order::create([
                'cart_id' => $cart->id,
                'email' => $data['email'],
                'phone' => $data['phone'],
                'address' => $data['address'],
                'delivery_time' => $data['delivery_time'],
            ]);
            foreach ($cart->cartItems as $cartItem) {
                $order->items()->create([
                    'order_id' => $order->id,
                    'product_id' => $cartItem->product_id,
                    'quantity' => $cartItem->quantity,
                    'price' => $cartItem->product->price,

                ]);
            }
            $cart->cartItems()->delete();
            DB::commit();
            return $order->refresh();
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    public function getOrder(Order $order): Order
    {
        return $order->loadMissing('items.product');
    }

    public function getUserOrders(int $userId)
    {
        return Order::whereHas('cart', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })->with('items.product')->get();
    }

    public function cancelOrder(Order $order): Order
    {
        $order->update([
            'status' => 'cancelled'
        ]);
        return $order->refresh();
    }

    public function updateOrderDetails(Order $order, array $data): Order
    {
        $order->update([
            'email' => $data['email'],
            'phone' => $data['phone'],
            'address' => $data['address'],
            'delivery_time' => $data['delivery_time'],
        ]);
        return $order->refresh();
    }
}

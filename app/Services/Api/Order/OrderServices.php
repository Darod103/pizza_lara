<?php

namespace App\Services\Api\Order;

use App\DTO\OrderStatusDTO;
use App\Exceptions\CartItemNotFoundException;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;

/**
 * Сервис для управления заказами пользователя.
 */
class OrderServices
{

    /**
     * Создаёт новый заказ из корзины пользователя.
     *
     * @param array $data Данные заказа: email, phone, address, delivery_time
     * @param int $userId ID пользователя
     * @return Order Созданный заказ
     *
     * @throws CartItemNotFoundException Если корзина пуста
     * @throws \Throwable При ошибке создания заказа
     */
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

    /**
     * Возвращает заказ с подгруженными товарами.
     *
     * @param Order $order Заказ
     * @return Order Заказ с подгруженными items и product
     */
    public function getOrder(Order $order): Order
    {
       return $order->loadMissing('items.product');
    }


    /**
     * Отменяет заказ (устанавливает статус "cancelled").
     *
     * @param Order $order Заказ
     * @return Order Обновлённый заказ
     */
    public function cancelOrder(Order $order): Order
    {
        $order->update([
            'status' => OrderStatusDTO::STATUS_CANCELLED,
        ]);
        return $order->refresh();
    }

    public function updateOrder(Order $order, string $status): Order
    {
        $order->update([
            'status' => $status,
        ]);
        return $order->refresh();
    }


    /**
     * Обновляет контактную информацию и время доставки в заказе.
     *
     * @param Order $order Заказ
     * @param array $data Новые данные: email, phone, address, delivery_time
     * @return Order Обновлённый заказ
     */
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

    /**
     * Удаляет заказ и связанные товары заказа.
     *
     * @param Order $order Заказ
     * @return bool Успешность удаления (true — успешно)
     *
     * @throws \Throwable При ошибке удаления
     */
    public function deleteOrder(Order $order): bool
    {
        return DB::transaction(function () use ($order) {
            $order->items()->delete();
            return $order->delete();
        });
    }

}

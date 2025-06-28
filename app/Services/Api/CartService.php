<?php

namespace App\Services\Api;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Database\Eloquent\ModelNotFoundException;
//TODO сделать рефакторинг,подумать над тем что возвращать и добавить логику проверки количества +  CartServiceInterface а его бинд к реализации в AppServiceProvider.
/**
 * Сервис для работы с корзиной покупок
 *
 * @package App\Services\Api
 */
class CartService
{
    /**
     * Получить корзину пользователя с товарами, если нет корзины то создает ее
     *
     * @param int $userId ID пользователя
     * @return array
     */

    public function getUserCart(int $userId): array
    {

        $cart = Cart::with(['cartItems.product'])
            ->firstOrCreate(['user_id' => $userId]);
        return [
            'id' => $cart->id,
            'items' => $cart->cartItems->map(function ($item) {
                return [
                    'id' => $item->id,
                    'product' => [
                        'id' => $item->product->id,
                        'name' => $item->product->name,
                        'price' => $item->product->price,
                    ],
                    'quantity' => $item->quantity,
                    'price' => $item->price,
                    'subtotal' => $item->subtotal,
                ];
            }),
            'total' => $cart->total,
        ];
    }

    /**
     * Добавить товар в корзину или увеличить количество
     *
     * @param int $userId ID пользователя
     * @param int $productId ID товара
     * @param int $quantity Количество товара
     * @return CartItem Элемент корзины
     * @throws ModelNotFoundException Если товар не найден
     */
    public function addItem(int $userId, int $productId, int $quantity): CartItem
    {
        $product = Product::findOrFail($productId);
        $cart = Cart::firstOrCreate(['user_id' => $userId]);

        $existingItem = $cart->cartItems()
            ->where('product_id', $productId)
            ->first();

        if ($existingItem) {
            $existingItem->update([
                'quantity' => $existingItem->quantity + $quantity
            ]);
            return $existingItem->fresh();
        }

        return $cart->cartItems()->create([
            'product_id' => $productId,
            'quantity' => $quantity,
            'price' => $product->price,
        ]);
    }

    /**
     * Обновить количество товара в корзине
     *
     * @param int $itemId ID элемента корзины
     * @param int $quantity Новое количество
     * @param int $userId ID пользователя для проверки доступа
     * @return CartItem Обновленный элемент корзины
     * @throws ModelNotFoundException Если элемент не найден или не принадлежит пользователю
     */
    public function updateItem(int $itemId, int $quantity, int $userId): CartItem
    {
        $cartItem = CartItem::whereHas('cart', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })->findOrFail($itemId);

        $cartItem->update(['quantity' => $quantity]);

        return $cartItem->fresh();
    }

    /**
     * Удалить товар из корзины
     *
     * @param int $itemId ID элемента корзины
     * @param int $userId ID пользователя для проверки доступа
     * @return void
     * @throws ModelNotFoundException Если элемент не найден или не принадлежит пользователю
     */
    public function removeItem(int $itemId, int $userId): void
    {
        $cartItem = CartItem::whereHas('cart', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })->findOrFail($itemId);

        $cartItem->delete();
    }

    /**
     * Очистить корзину пользователя
     *
     * @param int $userId ID пользователя
     * @return void
     */
    public function clearCart(int $userId): void
    {
        $cart = Cart::where('user_id', $userId)->first();

        if ($cart) {
            $cart->cartItems()->delete();
        }
    }

}

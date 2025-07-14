<?php

namespace App\Services\Api;

use App\Exceptions\ProductIsNotAvailableException;
use App\Models\Cart;
use App\Models\Product;
use App\Models\CartItem;
use App\Exceptions\CartLimitException;
use App\Exceptions\CartItemNotFoundException;
use App\Services\Api\Interface\CartServiceInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;

/**
 * Сервис для управления корзиной покупок пользователя.
 *
 * @package App\Services\Api
 */
class CartService implements CartServiceInterface
{
    /**
     * Ограничения по количеству товаров в корзине для каждой категории.
     *
     * @var array<string,int>
     */
    private array $categoryLimits = [
        'Pizza' => 20,
        'Drink' => 10,
    ];

    /**
     * Получить корзину пользователя с товарами и их продуктами.
     * Если корзина отсутствует, создаёт новую.
     *
     * @param int $userId
     * @return Cart
     */
    public function getUserCart(int $userId): Cart
    {
        $cart = Cart::firstOrCreate(['user_id' => $userId]);
        return $cart->load('cartItems.product');
    }

    /**
     * Добавить товар в корзину или увеличить количество.
     *
     * @param int $userId
     * @param int $productId
     * @param int $quantity
     * @return Cart
     *
     * @throws ProductIsNotAvailableException
     * @throws ModelNotFoundException
     * @throws CartLimitException
     */
    public function addItem(int $userId, int $productId, int $quantity): Cart
    {
        return DB::transaction(function () use ($userId, $productId, $quantity) {
            $product = Product::with('category')->findOrFail($productId);

            if (!$product->is_available) {
                throw new ProductIsNotAvailableException();
            }

            $cart = Cart::with('cartItems.product')->firstOrCreate(['user_id' => $userId]);

            $this->checkProductLimits($cart, $product, $quantity);

            $existingItem = $cart->cartItems()->where('product_id', $productId)->first();

            if ($existingItem) {
                $existingItem->update([
                    'quantity' => $existingItem->quantity + $quantity,
                ]);
            } else {
                $cart->cartItems()->create([
                    'product_id' => $productId,
                    'quantity' => $quantity,
                    'price' => $product->price,
                ]);
            }

            return $cart->fresh('cartItems.product');
        });
    }

    /**
     * Обновить количество товара в корзине.
     *
     * @param int $userId
     * @param int $productId
     * @param int $quantity
     * @return CartItem
     *
     * @throws \InvalidArgumentException
     */
    public function updateItem(int $userId, int $productId, int $quantity): CartItem
    {
        return DB::transaction(function () use ($userId, $productId, $quantity) {
            $cartItem = $this->findUserCartItemOrFail($userId, $productId);

            $this->checkProductLimits($cartItem->cart, $cartItem->product, $quantity, $cartItem);

            $cartItem->update(['quantity' => $quantity]);

            return $cartItem->fresh('product');
        });
    }

    /**
     * Удалить товар из корзины.
     *
     * @param int $productId
     * @param int $userId
     * @return void
     *
     * @throws CartItemNotFoundException
     */
    public function removeItem(int $productId, int $userId): void
    {
        $cartItem = $this->findUserCartItemOrFail($userId, $productId);
        $cartItem->delete();
    }

    /**
     * Очистить корзину пользователя.
     *
     * @param int $userId
     * @return bool true если корзина была очищена, иначе false
     */
    public function clearCart(int $userId): bool
    {
        $cart = Cart::where('user_id', $userId)->first();

        if ($cart) {
            $cart->cartItems()->delete();
            return true;
        }

        return false;
    }

    /**
     * Проверить ограничения по количеству товаров в категории.
     *
     * @param Cart $cart
     * @param Product $product
     * @param int $quantity
     * @param CartItem|null $excludeItem
     * @return void
     *
     * @throws CartLimitException
     */
    private function checkProductLimits(
        Cart $cart,
        Product $product,
        int $quantity,
        CartItem $excludeItem = null
    ): void {
        $categoryName = $product->category?->name;

        if (!$categoryName || !isset($this->categoryLimits[$categoryName])) {
            return;
        }

        $limit = $this->categoryLimits[$categoryName];
        $categoryId = $product->category_id;

        $query = $cart->cartItems()
            ->whereHas('product', fn($q) => $q->where('category_id', $categoryId));

        if ($excludeItem) {
            $query->where('id', '!=', $excludeItem->id);
        }

        $currentQuantity = $query->sum('quantity');
        $totalQuantity = $currentQuantity + $quantity;

        if ($totalQuantity > $limit) {
            throw new CartLimitException("Максимум $limit {$categoryName} в корзине");
        }
    }

    /**
     * Найти элемент корзины по продукту и пользователю.
     *
     * @param int $userId
     * @param int $productId
     * @return CartItem
     *
     * @throws CartItemNotFoundException
     */
    private function findUserCartItemOrFail(int $userId, int $productId): CartItem
    {
        try {
            return CartItem::where('product_id', $productId)
                ->whereHas('cart', fn($q) => $q->where('user_id', $userId))
                ->firstOrFail();
        } catch (ModelNotFoundException) {
            throw new CartItemNotFoundException();
        }
    }
}

<?php

namespace App\Services\Api;

use App\Models\Cart;
use App\Models\Product;
use App\Models\CartItem;
use App\Exceptions\CartLimitException;
use App\Exceptions\CartItemNotFoundException;
use App\Services\Api\Interface\CartServiceInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * Сервис для управления корзиной покупок пользователя.
 * Отвечает за получение корзины, добавление, обновление и удаление товаров,
 * а также проверку ограничений по количеству товаров в категориях.
 *
 * @package App\Services\Api
 */
class CartService implements CartServiceInterface
{
    /**
     * Ограничения по количеству товаров в корзине для каждой категории.
     * Ключ — название категории, значение — максимальное количество товаров.
     *
     * @var array<string,int>
     */
    private array $categoryLimits = [
        'Pizza' => 20,
        'Drink' => 10,
        'Dessert' => 5,
    ];

    /**
     * Получить корзину пользователя с товарами и их продуктами.
     * Если корзина отсутствует, создаёт новую.
     *
     * @param int $userId ID пользователя
     * @return Cart Корзина пользователя с загруженными товарами и продуктами
     */
    public function getUserCart(int $userId): Cart
    {
        return Cart::with(['cartItems.product'])->firstOrCreate(['user_id' => $userId]);
    }

    /**
     * Добавить товар в корзину или увеличить количество, если товар уже есть.
     * Проверяет ограничения по количеству товаров в категории.
     *
     * @param int $userId ID пользователя
     * @param int $productId ID добавляемого товара
     * @param int $quantity Количество товара для добавления
     * @return Cart Обновлённая корзина с актуальными товарами и продуктами
     *
     * @throws ModelNotFoundException Если товар с заданным ID не найден
     * @throws CartLimitException Если превышен лимит по категории товара
     */
    public function addItem(int $userId, int $productId, int $quantity): Cart
    {
        $product = Product::findOrFail($productId);
        $cart = Cart::firstOrCreate(['user_id' => $userId]);
        $this->checkProductLimits($cart, $product, $quantity);

        $existingItem = $cart->cartItems()->where('product_id', $productId)->first();

        if ($existingItem) {
            $existingItem->update([
                'quantity' => $existingItem->quantity + $quantity
            ]);
        } else {
            $cart->cartItems()->create([
                'product_id' => $productId,
                'quantity' => $quantity,
                'price' => $product->price,
            ]);
        }

        return $cart->fresh(['cartItems.product']);
    }

    /**
     * Обновить количество товара в корзине.
     * Проверяет ограничения по количеству товаров в категории.
     *
     * @param int $itemId ID элемента корзины для обновления
     * @param int $quantity Новое количество товара
     * @param int $userId ID пользователя для проверки прав доступа
     * @return CartItem Обновлённый элемент корзины
     *
     * @throws ModelNotFoundException Если элемент корзины не найден
     * @throws CartItemNotFoundException Если элемент не принадлежит пользователю
     * @throws CartLimitException Если новое количество превышает лимит по категории
     */
    public function updateItem(int $itemId, int $quantity, int $userId): CartItem
    {
        $cartItem = $this->findUserCartItemOrFail($itemId, $userId);

        $this->checkProductLimits($cartItem->cart, $cartItem->product, $quantity, $cartItem);

        $cartItem->update(['quantity' => $quantity]);

        return $cartItem->fresh();
    }

    /**
     * Удалить товар из корзины.
     *
     * @param int $itemId ID элемента корзины для удаления
     * @param int $userId ID пользователя для проверки прав доступа
     * @return void
     *
     * @throws CartItemNotFoundException Если элемент не найден или не принадлежит пользователю
     */
    public function removeItem(int $itemId, int $userId): void
    {
        $cartItem = $this->findUserCartItemOrFail($itemId, $userId);
        $cartItem->delete();
    }

    /**
     * Очистить корзину пользователя, удалив все товары.
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

    /**
     * Проверить ограничения по количеству товара в корзине для заданной категории.
     * Исключает из подсчёта переданный элемент корзины, если указан.
     *
     * @param Cart $cart Корзина пользователя
     * @param Product $product Продукт для проверки
     * @param int $quantity Количество товара для добавления или обновления
     * @param CartItem|null $excludeItem Элемент корзины, который следует исключить из подсчёта (при обновлении)
     * @return void
     *
     * @throws CartLimitException Если суммарное количество товара в категории превышает лимит
     */
    private function checkProductLimits(
        Cart $cart,
        Product $product,
        int $quantity,
        CartItem $excludeItem = null
    ): void {
        $categoryName = $product->category->name ?? null;
        $categoryId = $product->category_id;

        $limit = $this->categoryLimits[$categoryName] ?? null;

        if ($limit !== null) {
            $query = $cart->cartItems()
                ->whereHas('product', function ($q) use ($categoryId) {
                    $q->where('category_id', $categoryId);
                });

            if ($excludeItem) {
                $query->where('id', '!=', $excludeItem->id);
            }

            $categoryCount = $query->sum('quantity');
            $categoryCount += $quantity;

            if ($categoryCount > $limit) {
                throw new CartLimitException("Максимум $limit {$categoryName} в корзине");
            }
        }
    }

    /**
     * Найти элемент корзины по ID и ID пользователя.
     * Проверяет принадлежность элемента корзины пользователю.
     *
     * @param int $itemId ID элемента корзины
     * @param int $userId ID пользователя для проверки принадлежности
     * @return CartItem Найденный элемент корзины
     *
     * @throws CartItemNotFoundException Если элемент не найден или не принадлежит пользователю
     */
    private function findUserCartItemOrFail(int $itemId, int $userId): CartItem
    {
        try {
            return CartItem::whereHas('cart', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })->findOrFail($itemId);
        } catch (ModelNotFoundException $e) {
            throw new CartItemNotFoundException();
        }
    }

}

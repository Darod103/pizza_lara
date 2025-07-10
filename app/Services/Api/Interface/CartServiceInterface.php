<?php

namespace App\Services\Api\Interface;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;

/**
 * Контракт для сервиса корзины покупок
 */
interface CartServiceInterface
{
    /**
     * Получить корзину пользователя с товарами.
     *
     * Если корзины не существует — создать новую.
     *
     * @param int $userId ID пользователя
     * @return Cart Корзина пользователя с товарами
     */
    public function getUserCart(int $userId): Cart;

    /**
     * Добавить товар в корзину или увеличить его количество, если товар уже есть.
     *
     * @param int $userId ID пользователя
     * @param int $productId ID продукта, который нужно добавить
     * @param int $quantity Количество добавляемого товара
     * @return Cart Обновлённая корзина пользователя
     */
    public function addItem(int $userId, int $productId, int $quantity): Cart;

    /**
     * Обновить количество определённого товара в корзине.
     *
     * @param int $userId ID пользователя
     * @param int $productId ID продукта, количество которого нужно изменить
     * @param int $quantity Новое количество товара
     * @return CartItem Обновлённый элемент корзины
     */
    public function updateItem(int $userId, int $productId, int $quantity): CartItem;

    /**
     * Удалить товар из корзины по его ID.
     *
     * @param int $productId ID элемента корзины (cart_items.id)
     * @param int $userId ID пользователя (для проверки доступа)
     * @return void
     */
    public function removeItem(int $productId, int $userId): void;

    /**
     * Очистить корзину пользователя (удалить все товары).
     *
     * @param int $userId ID пользователя
     * @return bool
     */
    public function clearCart(int $userId): bool;
}

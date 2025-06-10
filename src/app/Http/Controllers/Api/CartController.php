<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Api\CartService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Контроллер для управления корзиной товаров
 * 
 * @package App\Http\Controllers\Api
 */
class CartController extends Controller
{
    protected CartService $cartService;

    /**
     * Конструктор контроллера
     * 
     * @param CartService $cartService Сервис для работы с корзиной
     */
    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    /**
     * Получить корзину текущего пользователя
     * 
     * @return JsonResponse Корзина с товарами и общей стоимостью
     */
    public function index(): JsonResponse
    {
        $cart = $this->cartService->getUserCart(auth()->id());
        return response()->json($cart);
    }

    /**
     * Добавить товар в корзину
     * 
     * @param Request $request Запрос с данными товара (product_id, quantity)
     * @return JsonResponse Добавленный элемент корзины
     */
    public function addItem(Request $request): JsonResponse
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1'
        ]);

        $cartItem = $this->cartService->addItem(
            auth()->id(),
            $request->product_id,
            $request->quantity
        );

        return response()->json($cartItem, 201);
    }

    /**
     * Обновить количество товара в корзине
     * 
     * @param Request $request Запрос с новым количеством
     * @param int $itemId ID элемента корзины
     * @return JsonResponse Обновленный элемент корзины
     */
    public function updateItem(Request $request, int $itemId): JsonResponse
    {
        $request->validate([
            'quantity' => 'required|integer|min:1'
        ]);

        $cartItem = $this->cartService->updateItem(
            $itemId,
            $request->quantity,
            auth()->id()
        );

        return response()->json($cartItem);
    }

    /**
     * Удалить товар из корзины
     * 
     * @param int $itemId ID элемента корзины
     * @return JsonResponse Сообщение об успешном удалении
     */
    public function removeItem(int $itemId): JsonResponse
    {
        $this->cartService->removeItem($itemId, auth()->id());
        return response()->json(['message' => 'Item removed from cart']);
    }

    /**
     * Очистить корзину пользователя
     * 
     * @return JsonResponse Сообщение об успешной очистке
     */
    public function clear(): JsonResponse
    {
        $this->cartService->clearCart(auth()->id());
        return response()->json(['message' => 'Cart cleared']);
    }
}
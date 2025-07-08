<?php

namespace App\Http\Controllers\Api\Cart;


use App\Exceptions\CartItemNotFoundException;
use App\Exceptions\CartLimitException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Cart\CartStoreRequest;
use App\Http\Requests\Api\Cart\CartUpdateRequest;
use App\Http\Resources\Cart\CartResource;
use App\Http\Resources\CartItem\CartItemResource;
use App\Services\Api\Interface\CartServiceInterface;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * Контроллер для управления корзиной товаров
 *
 * @package App\Http\Controllers\Api
 */
class CartController extends Controller
{
    protected CartServiceInterface $cartService;

    /**
     * Конструктор контроллера
     *
     * @param CartServiceInterface $cartService Сервис для работы с корзиной
     */
    public function __construct(CartServiceInterface $cartService)
    {
        $this->cartService = $cartService;
    }

    /**
     * Получить корзину текущего пользователя
     *
     * @return CartResource Корзина с товарами и общей стоимостью
     */
    public function index(): CartResource
    {
        $cart = $this->cartService->getUserCart(auth()->id());
        return CartResource::make($cart);
    }

    /**
     * Сохранить (добавить) новый товар в корзину
     *
     * @param CartStoreRequest $request Запрос с данными товара (product_id, quantity)
     * @return CartResource Добавленный элемент корзины
     * @throws CartLimitException
     */
    public function store(CartStoreRequest $request): CartResource
    {
        $data = $request->validated();
        $cartItem = $this->cartService->addItem(auth()->id(), $data['product_id'], $data['quantity']);
        return CartResource::make($cartItem);
    }

    /**
     * Обновить количество товара в корзине
     *
     * @param CartUpdateRequest $request Запрос с новым количеством
     * @param int $itemId ID элемента корзины
     * @return CartItemResource Обновленный элемент корзины
     */
    public function update(CartUpdateRequest $request, int $itemId): CartItemResource
    {
        $data = $request->validated();

        $cartItem = $this->cartService->updateItem(
            $itemId,
            $data['quantity'],
            auth()->id()
        );
        return CartItemResource::make($cartItem);
    }

    /**
     * Удалить товар из корзины
     *
     * @param int $itemId ID элемента корзины
     * @return JsonResponse Сообщение об успешном удалении
     * @throws CartItemNotFoundException
     */
    public function destroy(int $itemId): JsonResponse
    {
        $this->cartService->removeItem($itemId, auth()->id());
        return response()->json(['message' => 'Продукт удален из корзины!'], Response::HTTP_OK);
    }

    /**
     * Очистить корзину пользователя
     *
     * @return JsonResponse Сообщение об успешной очистке
     */
    public function destroyAll(): JsonResponse
    {
        $this->cartService->clearCart(auth()->id());
        return response()->json(['message' => 'Cart cleared']);
    }
}

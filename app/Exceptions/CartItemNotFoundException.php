<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\Request;


class CartItemNotFoundException extends Exception
{
    /**
     * Render the exception as an HTTP response.
     */
    public function render(Request $request): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $this->getMessage() ?: 'Нет в корзине такого продукта.',
        ]);
    }
}

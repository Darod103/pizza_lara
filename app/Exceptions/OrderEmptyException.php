<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;


class OrderEmptyException extends Exception
{
    /**
     * Report the exception.
     */
    public function report(): void
    {

    }

    /**
     * Render the exception as an HTTP response.
     */
    public function render(Request $request): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $this->getMessage() ?: 'Заказов нет',
        ],404);
    }
}

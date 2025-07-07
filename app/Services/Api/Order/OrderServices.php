<?php

namespace App\Services\Api\Order;

use App\Exceptions\CartItemNotFoundException;
use App\Models\Cart;
use App\Models\Order;
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

        }

    }


}

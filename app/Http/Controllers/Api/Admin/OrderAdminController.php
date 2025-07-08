<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\Api\Order\OrderServices;
use Illuminate\Http\Request;


class OrderAdminController extends Controller
{

    protected OrderServices $orderServices;

    public function __construct(OrderServices $orderServices)
    {
        $this->orderServices = $orderServices;
    }

//    public function index()
//    {
//        $orders = $this->orderServices
//    }

    public function store(Request $request)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Order $order)
    {
        $orderId = $order->id;
        $this->orderServices->deleteOrder($order);

        return response()->json([
            'success' => true,
            'message' => "Заказ с ID {$orderId} удален",
        ]);
    }
}

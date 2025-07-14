<?php

namespace App\Http\Controllers\Api\Admin;

use App\Exceptions\OrderEmptyException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Order\UpdateOrderRequest;
use App\Http\Resources\Order\OrderResource;
use App\Models\Order;
use App\Services\Api\Order\OrderServices;


class OrderAdminController extends Controller
{

    protected OrderServices $orderServices;

    public function __construct(OrderServices $orderServices)
    {
        $this->orderServices = $orderServices;
    }

    public function index()
    {
        $orders = Order::with('items.product')->get();
        if ($orders->count() < 1) {
            throw new OrderEmptyException();
        }
        return OrderResource::collection($orders);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateOrderRequest $request, Order $order)
    {
        $status = $request->validated()['status'];
        $order = $this->orderServices->updateOrder($order, $status);
        return new OrderResource($order);
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

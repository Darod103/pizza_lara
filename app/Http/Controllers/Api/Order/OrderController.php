<?php

namespace App\Http\Controllers\Api\Order;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Order\StoreOrderReguest;
use App\Http\Resources\Order\OrderResource;
use App\Models\Order;
use App\Services\Api\Order\OrderServices;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    protected OrderServices $orderServices;

    public function __construct(OrderServices $orderServices)
    {
        $this->orderServices = $orderServices;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $userId = auth()->id();
        $orders = $this->orderServices->getUserOrders($userId);
        return OrderResource::collection($orders);
    }

     /**
     * Store a newly created resource in storage.
     */
    public function store(StoreOrderReguest $request)
    {
        $data = $request->validated();
        $userId = auth()->id();
        return OrderResource::make($this->orderServices->createOrder($data, $userId));
    }

    /**
     * Display the specified resource.
     */
    public function show(Order $order)
    {
        return OrderResource::make($this->orderServices->getOrder($order));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreOrderReguest $request, Order $order)
    {
        $data = $request->validated();
        $order =  $this->orderServices->updateOrderDetails($order, $data);
        return OrderResource::make($order);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Order $order)
    {
        $order = $this->orderServices->cancelOrder($order);
        return OrderResource::make($order);
    }
}

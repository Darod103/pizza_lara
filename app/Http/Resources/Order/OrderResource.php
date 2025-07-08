<?php

namespace App\Http\Resources\Order;

use App\Http\Resources\OrderItem\OrderItemResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {

        return [
            'id'=> $this->id,
            'status'=> $this->status,
            'delivery' => [
                'phone' => $this->phone,
                'email'=> $this->email,
                'address' => $this->address,
                'delivery_time' => $this->delivery_time,
            ],
            'items'=>OrderItemResource::collection($this->items),
            'total'=> $this->total
        ];
    }
}

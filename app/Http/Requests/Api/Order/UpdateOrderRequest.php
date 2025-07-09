<?php

namespace App\Http\Requests\Api\Order;

use App\DTO\OrderStatusDTO;
use App\Models\Order;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateOrderRequest extends FormRequest
{


    public function rules(): array
    {
        return [
            'status' => ['required', 'string', Rule::in(OrderStatusDTO::all())],
        ];
    }
}

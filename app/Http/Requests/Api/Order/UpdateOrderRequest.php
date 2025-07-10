<?php

namespace App\Http\Requests\Api\Order;

use App\Enum\OrderStatusEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateOrderRequest extends FormRequest
{


    public function rules(): array
    {
        return [
            'status' => ['required', 'string', Rule::in(OrderStatusEnum::all())],
        ];
    }
}

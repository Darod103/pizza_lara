<?php

namespace App\Http\Requests\Api\Product;

use Illuminate\Foundation\Http\FormRequest;

class ProductStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
//    public function authorize(): bool
//    {
//        return false;
//    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'category_id' => 'required|numeric|exists:categories,id',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'is_available' => 'required|boolean',
        ];
    }
}

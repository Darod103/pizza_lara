<?php

namespace App\Http\Requests\Api\Order;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrderReguest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => 'required|email',
            'phone' => 'required|string|min:10|regex:/^[+]?[0-9]{10,15}$/',
            'address' => 'required|string',
            'delivery_time' => 'required|date_format:H:i',
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => 'Поле email обязательно для заполнения.',
            'email.email' => 'Неверный формат email.',
            'phone.required' => 'Поле телефон обязательно для заполнения.',
            'phone.string' => 'Телефон должен быть строкой.',
            'phone.min' => 'Телефон должен содержать не менее 10 символов.',
            'phone.regex' => 'Неверный формат номера телефона. Допустимы цифры, пробелы, скобки, дефис и плюс в начале.',
            'address.required' => 'Поле адрес обязательно для заполнения.',
            'address.string' => 'Адрес должен быть строкой.',
            'delivery_time.required' => 'Поле "время доставки" обязательно для заполнения.',
            'delivery_time.date_format' => 'Время доставки должно быть в формате ЧЧ:мм.',
        ];
    }
}

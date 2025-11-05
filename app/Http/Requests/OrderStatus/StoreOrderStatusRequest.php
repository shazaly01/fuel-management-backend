<?php

namespace App\Http\Requests\OrderStatus;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrderStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255|unique:order_statuses,name',
            'color' => 'nullable|string|max:50', // مثال: #FF5733
        ];
    }
}

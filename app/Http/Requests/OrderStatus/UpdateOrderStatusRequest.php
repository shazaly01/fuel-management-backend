<?php

namespace App\Http\Requests\OrderStatus;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateOrderStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('order_statuses', 'name')->ignore($this->order_status),
            ],
            'color' => 'nullable|string|max:50',
        ];
    }
}

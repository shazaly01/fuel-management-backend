<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProductRequest extends FormRequest
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
                // التأكد من أن الاسم فريد، مع تجاهل المنتج الحالي
                Rule::unique('products', 'name')->ignore($this->product),
            ],
            'description' => 'nullable|string|max:1000',
        ];
    }
}

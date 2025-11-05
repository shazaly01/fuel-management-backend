<?php

namespace App\Http\Requests\Region;

use Illuminate\Foundation\Http\FormRequest;

class StoreRegionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255|unique:regions,name',
            'description' => 'nullable|string',
        ];
    }
}

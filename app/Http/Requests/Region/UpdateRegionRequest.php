<?php

namespace App\Http\Requests\Region;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRegionRequest extends FormRequest
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
                Rule::unique('regions', 'name')->ignore($this->region),
            ],
            'description' => 'nullable|string',
        ];
    }
}

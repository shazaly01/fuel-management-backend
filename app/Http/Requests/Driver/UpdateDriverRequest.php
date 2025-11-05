<?php

namespace App\Http\Requests\Driver;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateDriverRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'sometimes|required|string|max:255',
            'license_number' => [
                'sometimes',
                'required',
                'string',
                'max:255',
                Rule::unique('drivers', 'license_number')->ignore($this->driver),
            ],
            'phone_number' => 'nullable|string|max:20',
            'status' => 'sometimes|required|string|in:available,on_trip,unavailable',
        ];
    }
}

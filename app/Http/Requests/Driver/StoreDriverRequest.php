<?php

namespace App\Http\Requests\Driver;

use Illuminate\Foundation\Http\FormRequest;

class StoreDriverRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'license_number' => 'required|string|max:255|unique:drivers,license_number',
            'phone_number' => 'nullable|string|max:20',
            'status' => 'nullable|string|in:available,on_trip,unavailable,wants_to_work',
            // --- بداية الإضافة ---
            'address' => 'nullable|string|max:255',
            'work_nature_id' => 'nullable|integer|exists:work_natures,id',
            'document_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            // --- نهاية الإضافة ---
        ];
    }
}

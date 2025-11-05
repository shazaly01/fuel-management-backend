<?php

namespace App\Http\Requests\Truck;

use Illuminate\Foundation\Http\FormRequest;

class StoreTruckRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'truck_number' => 'required|string|max:255|unique:trucks,truck_number',
            'truck_type' => 'nullable|string|max:255',
            'color' => 'nullable|string|max:255',
            'trailer_number' => 'nullable|string|max:255|unique:trucks,trailer_number',
            // التأكد من أن السائق موجود (إذا تم إرساله)
            'driver_id' => 'nullable|integer|exists:drivers,id|unique:trucks,driver_id',
        ];
    }
}

<?php

namespace App\Http\Requests\Truck;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTruckRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'truck_number' => [
                'sometimes', 'required', 'string', 'max:255',
                Rule::unique('trucks', 'truck_number')->ignore($this->truck),
            ],
            'truck_type' => 'nullable|string|max:255',
            'color' => 'nullable|string|max:255',
            'trailer_number' => [
                'nullable', 'string', 'max:255',
                Rule::unique('trucks', 'trailer_number')->ignore($this->truck),
            ],
            'driver_id' => [
                'nullable', 'integer', 'exists:drivers,id',
                // عند التحديث، تأكد من أن السائق غير معين لشاحنة أخرى (غير الشاحنة الحالية)
                Rule::unique('trucks', 'driver_id')->ignore($this->truck),
            ],
        ];
    }
}

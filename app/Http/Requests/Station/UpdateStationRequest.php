<?php

namespace App\Http\Requests\Station;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateStationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'sometimes|required|string|max:255',
            'address' => 'nullable|string|max:500',
            'station_number' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('stations', 'station_number')->ignore($this->station),
            ],
            'company_id' => 'sometimes|required|integer|exists:companies,id',
            'region_id' => 'required|integer|exists:regions,id',
        ];
    }
}

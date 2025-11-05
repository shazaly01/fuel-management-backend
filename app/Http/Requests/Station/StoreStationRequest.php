<?php

namespace App\Http\Requests\Station;

use Illuminate\Foundation\Http\FormRequest;

class StoreStationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'address' => 'nullable|string|max:500',
            'station_number' => 'nullable|string|max:255|unique:stations,station_number',
            // التأكد من أن الشركة موجودة في قاعدة البيانات
            'company_id' => 'required|integer|exists:companies,id',
            'region_id' => 'required|integer|exists:regions,id',
        ];
    }
}

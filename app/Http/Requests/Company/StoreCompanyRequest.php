<?php

namespace App\Http\Requests\Company;

use Illuminate\Foundation\Http\FormRequest;

class StoreCompanyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // السماح بالوصول مؤقتاً
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255|unique:companies,name',
        ];
    }
}

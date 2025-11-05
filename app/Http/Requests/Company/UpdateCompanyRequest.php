<?php

namespace App\Http\Requests\Company;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCompanyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // السماح بالوصول مؤقتاً
    }

    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                // التأكد من أن الاسم فريد، مع تجاهل الشركة الحالية التي يتم تحديثها
                Rule::unique('companies', 'name')->ignore($this->company),
            ],
        ];
    }
}

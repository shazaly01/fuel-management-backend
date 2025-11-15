<?php

namespace App\Http\Requests\WorkNature;

use Illuminate\Foundation\Http\FormRequest;

class StoreWorkNatureRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // سيتم التعامل مع الصلاحيات في الـ Policy المرتبط بالـ Controller
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255|unique:work_natures,name',
            'description' => 'nullable|string',
        ];
    }
}

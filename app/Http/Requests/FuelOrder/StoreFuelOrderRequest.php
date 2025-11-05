<?php

namespace App\Http\Requests\FuelOrder;

use Illuminate\Foundation\Http\FormRequest;

class StoreFuelOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'driver_id' => 'required|integer|exists:drivers,id',
            'station_id' => 'required|integer|exists:stations,id',
            'product_id' => 'required|integer|exists:products,id',
            'order_status_id' => 'required|integer|exists:order_statuses,id',
            'quantity' => 'nullable|numeric|min:0',
            'order_date' => 'required|date',
            'delivery_date' => 'nullable|date|after_or_equal:order_date',
            'notes' => 'nullable|string',
        ];
    }
}

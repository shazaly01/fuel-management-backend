<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FuelOrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'quantity' => $this->quantity,
            'order_date' => $this->order_date->format('Y-m-d'), // تنسيق التاريخ
            'delivery_date' => $this->delivery_date?->format('Y-m-d H:i:s'), // استخدام Null-safe operator
            'notes' => $this->notes,
            'notification_number' => $this->notification_number,
            'created_at' => $this->created_at->toDateTimeString(),

            // تضمين البيانات من العلاقات المرتبطة
            'status' => OrderStatusResource::make($this->whenLoaded('status')),

            'driver' => DriverResource::make($this->whenLoaded('driver')),
            'station' => StationResource::make($this->whenLoaded('station')),
            'product' => ProductResource::make($this->whenLoaded('product')),
        ];
    }
}

<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DriverResource extends JsonResource
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
            'name' => $this->name,
            'license_number' => $this->license_number,
            'phone_number' => $this->phone_number,
            'status' => $this->status,
            // --- بداية الإضافة ---
            'address' => $this->address,
            // --- نهاية الإضافة ---

            'document_image_url' => $this->document_image_url,
            // تحميل بيانات الشاحنة المرتبطة بالسائق، فقط إذا تم تحميلها مسبقاً
            'truck' => TruckResource::make($this->whenLoaded('truck')),

            // --- بداية الإضافة ---
            // تحميل بيانات طبيعة العمل المرتبطة بالسائق، فقط إذا تم تحميلها مسبقاً
            'work_nature' => WorkNatureResource::make($this->whenLoaded('workNature')),
            // --- نهاية الإضافة ---
            'fuel_orders_count' => $this->whenCounted('fuelOrders'),
        ];
    }
}

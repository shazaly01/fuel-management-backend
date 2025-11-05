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
            // تحميل بيانات الشاحنة المرتبطة بالسائق، فقط إذا تم تحميلها مسبقاً
            'truck' => TruckResource::make($this->whenLoaded('truck')),
        ];
    }
}

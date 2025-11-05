<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'address' => $this->address,
            'station_number' => $this->station_number,
            // تضمين بيانات الشركة المرتبطة بالمحطة
            'company' => CompanyResource::make($this->whenLoaded('company')),
            'region' => RegionResource::make($this->whenLoaded('region')),
        ];
    }
}

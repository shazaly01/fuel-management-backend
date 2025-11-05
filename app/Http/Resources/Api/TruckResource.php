<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TruckResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'truck_number' => $this->truck_number,
            'truck_type' => $this->truck_type,
            'color' => $this->color,
            'trailer_number' => $this->trailer_number,
            // لا نقم بتضمين علاقة السائق هنا لتجنب التكرار اللانهائي (driver -> truck -> driver)
            // سنعرض فقط driver_id
            'driver_id' => $this->driver_id,
        ];
    }
}

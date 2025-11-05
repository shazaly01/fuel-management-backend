<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Truck extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'truck_number',
        'truck_type',
        'color',
        'trailer_number',
        'driver_id',
    ];

    /**
     * Get the driver that owns the truck.
     */
    public function driver(): BelongsTo
    {
        return $this->belongsTo(Driver::class);
    }
}

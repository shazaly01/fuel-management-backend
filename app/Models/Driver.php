<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Driver extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'license_number',
        'phone_number',
        'status',
    ];

    /**
     * Get the truck associated with the driver.
     */
    public function truck(): HasOne
    {
        return $this->hasOne(Truck::class);
    }

    /**
     * Get the fuel orders for the driver.
     */
    public function fuelOrders(): HasMany
    {
        return $this->hasMany(FuelOrder::class);
    }
}

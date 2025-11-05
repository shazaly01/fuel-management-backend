<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany; // <-- [مهم] استيراد HasMany
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderStatus extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'color',
    ];

    // --- [الإضافة الأساسية هنا] ---
    /**
     * Get all of the fuel orders for the OrderStatus.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function fuelOrders(): HasMany
    {
        return $this->hasMany(FuelOrder::class, 'order_status_id');
    }
    // --- [نهاية الإضافة] ---
}

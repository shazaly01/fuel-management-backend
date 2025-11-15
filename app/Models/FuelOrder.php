<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class FuelOrder extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'driver_id',
        'station_id',
        'product_id',
        'order_status_id',
        'quantity',
        'order_date',
        'delivery_date',
        'notes',
        'notification_number',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'order_date' => 'date',
        'delivery_date' => 'datetime',
        'quantity' => 'decimal:2',
    ];

    /**
     * Get the driver associated with the fuel order.
     */
    public function driver(): BelongsTo
    {
        return $this->belongsTo(Driver::class);
    }

    /**
     * Get the station associated with the fuel order.
     */
    public function station(): BelongsTo
    {
        return $this->belongsTo(Station::class);
    }

    /**
     * Get the product associated with the fuel order.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the status of the fuel order.
     */
    public function status(): BelongsTo
    {
        return $this->belongsTo(OrderStatus::class, 'order_status_id');
    }
}

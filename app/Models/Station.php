<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Station extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'address',
        'station_number',
        'company_id',
        'region_id',
    ];

    /**
     * Get the company that owns the station.
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

     public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class);
    }
}

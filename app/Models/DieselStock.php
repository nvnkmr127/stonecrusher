<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DieselStock extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'date',
        'opening_liters',
        'purchased_liters',
        'closing_liters',
        'operational_unit_id',
        'remarks',
    ];

    protected $casts = [
        'date' => 'date',
        'opening_liters' => 'decimal:2',
        'purchased_liters' => 'decimal:2',
        'closing_liters' => 'decimal:2',
    ];

    public function operationalUnit()
    {
        return $this->belongsTo(OperationalUnit::class);
    }

    // Accessors for calculated fields
    public function getTotalAvailableAttribute()
    {
        return $this->opening_liters + $this->purchased_liters;
    }

    public function getConsumedLitersAttribute()
    {
        return $this->total_available - $this->closing_liters;
    }
}

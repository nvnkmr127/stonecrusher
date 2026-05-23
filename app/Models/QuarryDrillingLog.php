<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class QuarryDrillingLog extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'operational_unit_id',
        'vendor_id',
        'date',
        'no_of_holes',
        'total_feet',
        'rate_per_foot',
        'gross_amount',
        'diesel_deduction_amount',
        'advance_deduction_amount',
        'net_amount',
        'remarks',
        'recorded_by',
    ];

    protected $casts = [
        'date' => 'date',
        'total_feet' => 'decimal:2',
        'rate_per_foot' => 'decimal:2',
        'gross_amount' => 'decimal:2',
        'diesel_deduction_amount' => 'decimal:2',
        'advance_deduction_amount' => 'decimal:2',
        'net_amount' => 'decimal:2',
    ];

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    public function operationalUnit(): BelongsTo
    {
        return $this->belongsTo(OperationalUnit::class);
    }

    public function recorder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    public function operationalRecord(): HasOne
    {
        return $this->hasOne(OperationalRecord::class);
    }
}

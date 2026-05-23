<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class QuarrySecondaryBlasting extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'operational_unit_id',
        'vendor_id',
        'date',
        'no_of_holes',
        'amount',
        'diesel_deduction_amount',
        'net_amount',
        'remarks',
        'recorded_by',
    ];

    protected $casts = [
        'date' => 'date',
        'amount' => 'decimal:2',
        'diesel_deduction_amount' => 'decimal:2',
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

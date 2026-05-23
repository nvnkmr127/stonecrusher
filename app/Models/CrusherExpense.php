<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class CrusherExpense extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'operational_unit_id',
        'vendor_id',
        'date',
        'category',
        'amount',
        'quantity',
        'rate',
        'payment_mode',
        'invoice_number',
        'remarks',
        'recorded_by',
    ];

    protected $casts = [
        'date' => 'date',
        'amount' => 'decimal:2',
        'quantity' => 'decimal:2',
        'rate' => 'decimal:2',
    ];

    // Relationships
    public function operationalUnit(): BelongsTo
    {
        return $this->belongsTo(OperationalUnit::class);
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    public function recorder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    public function operationalRecord(): HasOne
    {
        return $this->hasOne(OperationalRecord::class);
    }

    // Dynamic scopes for reporting
    public function scopeForUnit(Builder $query, int $unitId): Builder
    {
        return $query->where('operational_unit_id', $unitId);
    }

    public function scopeBetweenDates(Builder $query, $start, $end): Builder
    {
        return $query->whereBetween('date', [$start, $end]);
    }
}

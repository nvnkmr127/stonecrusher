<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class QuarryBlast extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'operational_unit_id',
        'date',
        'blast_number',
        'holes_blasted',
        'remarks',
        'recorded_by',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function operationalUnit(): BelongsTo
    {
        return $this->belongsTo(OperationalUnit::class);
    }

    public function materials(): HasMany
    {
        return $this->hasMany(QuarryBlastingMaterialUsed::class);
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

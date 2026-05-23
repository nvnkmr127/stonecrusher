<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuarryBlastingMaterialUsed extends Model
{
    protected $table = 'quarry_blasting_materials_used';

    protected $fillable = [
        'quarry_blast_id',
        'vendor_id',
        'material_type',
        'quantity',
        'rate',
        'amount',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'rate' => 'decimal:2',
        'amount' => 'decimal:2',
    ];

    public function blast(): BelongsTo
    {
        return $this->belongsTo(QuarryBlast::class, 'quarry_blast_id');
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }
}

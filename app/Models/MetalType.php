<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MetalType extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'unit_price',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'unit_price' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }
}

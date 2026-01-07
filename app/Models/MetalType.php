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
    protected static function boot()
    {
        parent::boot();

        static::saved(function () {
            \Illuminate\Support\Facades\Cache::forget('metal_types_active');
        });

        static::deleted(function () {
            \Illuminate\Support\Facades\Cache::forget('metal_types_active');
        });
    }

    public static function getCached()
    {
        return \Illuminate\Support\Facades\Cache::rememberForever('metal_types_active', function () {
            return static::where('is_active', true)->get();
        });
    }
}

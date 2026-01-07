<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeliveryDestination extends Model
{
    protected $fillable = [
        'name',
        'latitude',
        'longitude',
        'distance_km',
    ];

    protected $casts = [
        'distance_km' => 'decimal:2',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
    ];
    protected static function boot()
    {
        parent::boot();

        static::saved(function () {
            \Illuminate\Support\Facades\Cache::forget('destinations_all');
        });

        static::deleted(function () {
            \Illuminate\Support\Facades\Cache::forget('destinations_all');
        });
    }

    public static function getCached()
    {
        return \Illuminate\Support\Facades\Cache::rememberForever('destinations_all', function () {
            return static::orderBy('name')->get();
        });
    }
}

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
}

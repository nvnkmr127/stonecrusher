<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class DieselLocation extends Model
{
    protected $fillable = ['name', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::saved(function () {
            Cache::forget('diesel_locations_active');
        });

        static::deleted(function () {
            Cache::forget('diesel_locations_active');
        });
    }

    public static function getActive()
    {
        return Cache::rememberForever('diesel_locations_active', function () {
            return static::where('is_active', true)->get();
        });
    }

    public function dieselEntries()
    {
        return $this->hasMany(DieselEntry::class);
    }
}

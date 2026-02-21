<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class OperationalUnit extends Model
{
    protected $fillable = ['code', 'name', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::saved(function () {
            Cache::forget('operational_units_active');
        });

        static::deleted(function () {
            Cache::forget('operational_units_active');
        });
    }

    public static function getActive()
    {
        return Cache::rememberForever('operational_units_active', function () {
            return static::where('is_active', true)->get();
        });
    }

    public function dieselEntries()
    {
        return $this->hasMany(DieselEntry::class);
    }
}

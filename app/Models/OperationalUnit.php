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

    public function tags()
    {
        return $this->hasMany(OperationalTag::class);
    }

    public function records()
    {
        return $this->hasMany(OperationalRecord::class);
    }

    public function crusherExpenses()
    {
        return $this->hasMany(CrusherExpense::class);
    }

    public function drillingLogs()
    {
        return $this->hasMany(QuarryDrillingLog::class);
    }

    public function blasts()
    {
        return $this->hasMany(QuarryBlast::class);
    }

    public function secondaryBlastings()
    {
        return $this->hasMany(QuarrySecondaryBlasting::class);
    }

    public function labourSheets()
    {
        return $this->hasMany(QuarryLabourSheet::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Vehicle extends Model
{
    use SoftDeletes, LogsActivity;

    protected $fillable = [
        'registration_number',
        'type',
        'diesel_location_id',
        'model',
        'transport_multiplier',
        'is_active',
        'operational_status',
    ];

    public function dieselLocation()
    {
        return $this->belongsTo(DieselLocation::class);
    }

    public function maintenances()
    {
        return $this->hasMany(VehicleMaintenance::class);
    }

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'transport_multiplier' => 'decimal:2',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['registration_number', 'type', 'model', 'is_active'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
    protected static function boot()
    {
        parent::boot();

        static::saved(function () {
            \Illuminate\Support\Facades\Cache::forget('vehicles_active');
        });

        static::deleted(function () {
            \Illuminate\Support\Facades\Cache::forget('vehicles_active');
        });
    }

    public static function getCached()
    {
        return \Illuminate\Support\Facades\Cache::rememberForever('vehicles_active', function () {
            return static::where('is_active', true)->get();
        });
    }
}

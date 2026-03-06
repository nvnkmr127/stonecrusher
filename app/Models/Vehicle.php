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
        'operational_unit_id',
        'model',
        'cft',
        'is_active',
        'is_owned',
        'operational_status',
    ];

    public function operationalUnit()
    {
        return $this->belongsTo(OperationalUnit::class);
    }

    public function maintenances()
    {
        return $this->hasMany(VehicleMaintenance::class);
    }

    public function dieselEntries()
    {
        return $this->hasMany(DieselEntry::class);
    }

    public function gatePasses()
    {
        return $this->hasMany(GatePass::class);
    }

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'is_owned' => 'boolean',
            'cft' => 'decimal:2',
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

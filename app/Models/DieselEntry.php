<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class DieselEntry extends Model
{
    use SoftDeletes, LogsActivity;

    protected $fillable = [
        'date',
        'vehicle_id',
        'diesel_location_id',
        'liters',
        'purpose',
        'location',
        'driver_name',
    ];

    protected $casts = [
        'date' => 'date',
        'liters' => 'decimal:2',
    ];

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function dieselLocation()
    {
        return $this->belongsTo(DieselLocation::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['*'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}

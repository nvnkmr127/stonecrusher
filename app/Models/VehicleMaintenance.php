<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class VehicleMaintenance extends Model
{
    use SoftDeletes, LogsActivity;

    protected $fillable = [
        'vehicle_id',
        'date',
        'completion_date',
        'type',
        'status',
        'cost',
        'odometer_reading',
        'description',
        'workshop_name',
        'performed_by',
    ];

    protected $casts = [
        'date' => 'date',
        'completion_date' => 'date',
        'cost' => 'decimal:2',
    ];

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['date', 'type', 'cost', 'vehicle.registration_number'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}

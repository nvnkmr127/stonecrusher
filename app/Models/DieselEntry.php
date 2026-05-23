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
        'operational_unit_id',
        'gate_pass_id',
        'liters',
        'work_type',
        'location',
        'driver_name',
        'driver_id',
        'vendor_id',
        'is_deducted',
        'deducted_at_invoice_type',
        'deducted_at_invoice_id',
    ];

    protected $casts = [
        'date' => 'date',
        'liters' => 'decimal:2',
        'is_deducted' => 'boolean',
    ];

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function operationalUnit()
    {
        return $this->belongsTo(OperationalUnit::class);
    }

    public function gatePass()
    {
        return $this->belongsTo(GatePass::class);
    }

    public function driver()
    {
        return $this->belongsTo(Employee::class, 'driver_id');
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function operationalRecord()
    {
        return $this->hasOne(OperationalRecord::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['*'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}

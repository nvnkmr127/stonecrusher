<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class GatePass extends Model
{
    use LogsActivity, SoftDeletes;
    protected $fillable = [
        'gate_pass_number',
        'date',
        'vehicle_id',
        'client_id',
        'source_unit_id',
        'destination_unit_id',
        'activity_type',
        'manual_customer_name',
        'project_id',
        'metal_type_id',
        'trips',
        'driver_name',
        'gross_weight',
        'tare_weight',
        'net_weight',
        'loading_quantity',
        'rate_per_ton',
        'total_amount',
        'paid_amount',
        'diesel_amount',
        'diesel_qty',
        'advance_amount',
        'status',
        'payment_status',
        'remarks',
        'delivery_location',
        'distance_km',
        'transport_cost',
        'transport_is_billable',
    ];

    protected $casts = [
        'date' => 'datetime',
        'gross_weight' => 'decimal:2',
        'tare_weight' => 'decimal:2',
        'net_weight' => 'decimal:2',
        'loading_quantity' => 'decimal:2',
        'rate_per_ton' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'diesel_amount' => 'decimal:2',
        'diesel_qty' => 'decimal:2',
        'advance_amount' => 'decimal:2',
        'distance_km' => 'decimal:2',
        'transport_cost' => 'decimal:2',
        'transport_is_billable' => 'boolean',
        'status' => \App\Enums\GatePassStatus::class,
        'activity_type' => \App\Enums\ActivityType::class,
        'trips' => 'integer',
    ];

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function metalType()
    {
        return $this->belongsTo(MetalType::class);
    }

    public function sourceUnit()
    {
        return $this->belongsTo(OperationalUnit::class, 'source_unit_id');
    }

    public function destinationUnit()
    {
        return $this->belongsTo(OperationalUnit::class, 'destination_unit_id');
    }

    public function transaction()
    {
        return $this->hasOne(ClientTransaction::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['*'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}

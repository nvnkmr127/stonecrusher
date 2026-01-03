<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GatePass extends Model
{
    protected $fillable = [
        'gate_pass_number',
        'date',
        'vehicle_id',
        'client_id',
        'metal_type_id',
        'driver_name',
        'gross_weight',
        'tare_weight',
        'net_weight',
        'loading_quantity',
        'rate_per_ton',
        'total_amount',
        'paid_amount',
        'diesel_amount',
        'advance_amount',
        'status',
        'payment_status',
        'remarks',
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
        'advance_amount' => 'decimal:2',
    ];

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function metalType()
    {
        return $this->belongsTo(MetalType::class);
    }

    public function transaction()
    {
        return $this->hasOne(ClientTransaction::class);
    }
}

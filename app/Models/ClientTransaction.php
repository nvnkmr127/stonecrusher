<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class ClientTransaction extends Model
{
    use LogsActivity;
    protected $fillable = [
        'client_id',
        'gate_pass_id',
        'transaction_type',
        'amount',
        'payment_mode',
        'transaction_date',
        'description',
        'reference_number',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'transaction_date' => 'date',
        ];
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function gatePass()
    {
        return $this->belongsTo(GatePass::class);
    }

    public function getActivitylogOptions(): \Spatie\Activitylog\LogOptions
    {
        return \Spatie\Activitylog\LogOptions::defaults()
            ->logOnly(['client_id', 'gate_pass_id', 'transaction_type', 'amount', 'payment_mode', 'transaction_date', 'description', 'reference_number'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}

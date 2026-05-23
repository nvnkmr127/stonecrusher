<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class OperationalRecord extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'operational_unit_id',
        'operational_tag_id',
        'date',
        'quantity',
        'rate',
        'amount',
        'remarks',
        'diesel_entry_id',
        'gate_pass_id',
        'crusher_expense_id',
        'quarry_drilling_log_id',
        'quarry_blast_id',
        'quarry_secondary_blasting_id',
        'quarry_labour_sheet_id',
    ];

    protected $casts = [
        'date' => 'date',
        'quantity' => 'decimal:2',
        'rate' => 'decimal:2',
        'amount' => 'decimal:2',
    ];

    public function operationalUnit()
    {
        return $this->belongsTo(OperationalUnit::class);
    }

    public function tag()
    {
        return $this->belongsTo(OperationalTag::class, 'operational_tag_id');
    }

    public function dieselEntry()
    {
        return $this->belongsTo(DieselEntry::class);
    }

    public function gatePass()
    {
        return $this->belongsTo(GatePass::class);
    }

    public function crusherExpense()
    {
        return $this->belongsTo(CrusherExpense::class);
    }

    public function drillingLog()
    {
        return $this->belongsTo(QuarryDrillingLog::class, 'quarry_drilling_log_id');
    }

    public function blast()
    {
        return $this->belongsTo(QuarryBlast::class, 'quarry_blast_id');
    }

    public function secondaryBlasting()
    {
        return $this->belongsTo(QuarrySecondaryBlasting::class, 'quarry_secondary_blasting_id');
    }

    public function labourSheet()
    {
        return $this->belongsTo(QuarryLabourSheet::class, 'quarry_labour_sheet_id');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['operational_unit_id', 'operational_tag_id', 'date', 'quantity', 'rate', 'amount', 'remarks'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}

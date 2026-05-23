<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class OperationalTag extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'operational_unit_id',
        'name',
        'type', // 'expense' or 'revenue'
    ];

    public function operationalUnit()
    {
        return $this->belongsTo(OperationalUnit::class);
    }

    public function records()
    {
        return $this->hasMany(OperationalRecord::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['operational_unit_id', 'name', 'type'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}

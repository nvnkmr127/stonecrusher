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
        'model',
        'transport_multiplier',
        'is_active',
    ];

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
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Project extends Model
{
    use SoftDeletes, LogsActivity;

    protected $fillable = [
        'name',
        'client_id',
        'is_internal',
        'location',
        'description',
        'estimated_quantity',
        'start_date',
        'end_date',
        'status',
        'progress',
    ];

    protected function casts(): array
    {
        return [
            'is_internal' => 'boolean',
            'start_date' => 'date',
            'end_date' => 'date',
            'estimated_quantity' => 'decimal:2',
            'progress' => 'integer',
        ];
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'client_id', 'location', 'status', 'progress'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}

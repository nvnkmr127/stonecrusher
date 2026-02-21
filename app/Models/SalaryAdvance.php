<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class SalaryAdvance extends Model
{
    use SoftDeletes, LogsActivity;

    protected $fillable = [
        'user_id',
        'amount',
        'date',
        'remarks',
    ];

    protected $casts = [
        'date' => 'date',
        'amount' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['user_id', 'amount', 'date', 'remarks'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}

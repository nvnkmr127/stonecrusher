<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class PayrollPeriod extends Model
{
    use LogsActivity;
    protected $fillable = [
        'month',
        'year',
        'is_locked',
        'locked_at',
        'locked_by',
        'is_released',
        'released_at',
        'released_by',
        'total_payable',
        'total_paid',
    ];

    protected $casts = [
        'is_locked' => 'boolean',
        'is_released' => 'boolean',
        'locked_at' => 'datetime',
        'released_at' => 'datetime',
        'total_payable' => 'decimal:2',
        'total_paid' => 'decimal:2',
    ];

    public function lockedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'locked_by');
    }

    public function releasedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'released_by');
    }

    public static function isLocked($month, $year): bool
    {
        return self::where('month', $month)->where('year', $year)->where('is_locked', true)->exists();
    }

    public function getStatus(): string
    {
        if (!$this->is_locked) {
            return 'Draft';
        }

        if ($this->is_released) {
            return 'Paid';
        }

        $canRelease = now()->greaterThanOrEqualTo(\Carbon\Carbon::createFromDate($this->year, $this->month, 1)->addMonths(2));

        return $canRelease ? 'Pending' : 'Locked';
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['is_locked', 'is_released', 'total_payable', 'total_paid'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}

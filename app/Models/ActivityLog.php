<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityLog extends Model
{
    protected $fillable = [
        'user_id',
        'performed_by',
        'action',
        'description',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function performedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'performed_by');
    }

    /**
     * Log an activity
     */
    public static function log(int $userId, string $action, ?string $description = null): void
    {
        static::create([
            'user_id' => $userId,
            'performed_by' => auth()->id(),
            'action' => $action,
            'description' => $description,
        ]);
    }
}

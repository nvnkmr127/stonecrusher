<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;

use Spatie\Activitylog\Traits\LogsActivity;

class Attendance extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'user_id',
        'date',
        'check_in',
        'check_out',
        'status',
        'remarks',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getActivitylogOptions(): \Spatie\Activitylog\LogOptions
    {
        return \Spatie\Activitylog\LogOptions::defaults()
            ->logOnly(['user_id', 'date', 'check_in', 'check_out', 'status', 'remarks'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}

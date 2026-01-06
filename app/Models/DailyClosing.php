<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyClosing extends Model
{
    use HasFactory;

    protected $fillable = [
        'date',
        'total_sales',
        'total_cash',
        'total_expenses',
        'status',
        'closed_by_user_id',
        'notes',
    ];

    protected $casts = [
        'date' => 'date',
        'total_sales' => 'decimal:2',
        'total_cash' => 'decimal:2',
        'total_expenses' => 'decimal:2',
    ];

    public function closedBy()
    {
        return $this->belongsTo(User::class, 'closed_by_user_id');
    }
}

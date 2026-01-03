<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Client extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'credit_limit',
        'notes',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }
    public function transactions()
    {
        return $this->hasMany(ClientTransaction::class)->orderBy('transaction_date', 'desc')->orderBy('created_at', 'desc');
    }

    public function getBalanceAttribute()
    {
        // Balance = Total Credit (Advance) - Total Debit (Sales)
        // Positive Balance means Client has paid extra (Advance)
        // Negative Balance means Client owes money
        $credit = $this->transactions()->where('transaction_type', 'credit')->sum('amount');
        $debit = $this->transactions()->where('transaction_type', 'debit')->sum('amount');

        return $credit - $debit;
    }
}

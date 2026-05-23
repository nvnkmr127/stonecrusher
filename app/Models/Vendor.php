<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vendor extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'contact_person',
        'phone',
        'email',
        'gstin',
        'address',
        'opening_balance',
        'is_active',
    ];

    protected $casts = [
        'opening_balance' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function expenses(): HasMany
    {
        return $this->hasMany(CrusherExpense::class);
    }

    public function drillingLogs(): HasMany
    {
        return $this->hasMany(QuarryDrillingLog::class);
    }

    public function advances(): HasMany
    {
        return $this->hasMany(ContractorAdvance::class);
    }

    public function secondaryBlastings(): HasMany
    {
        return $this->hasMany(QuarrySecondaryBlasting::class);
    }

    public function labourSheets(): HasMany
    {
        return $this->hasMany(QuarryLabourSheet::class);
    }

    public function dieselEntries(): HasMany
    {
        return $this->hasMany(DieselEntry::class);
    }
}

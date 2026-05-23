<?php

namespace App\Observers;

use App\Models\CrusherExpense;
use App\Models\OperationalRecord;
use App\Models\OperationalTag;
use App\Services\DayClosureService;

class CrusherExpenseObserver
{
    /**
     * Handle the CrusherExpense "saved" event.
     */
    public function saved(CrusherExpense $expense): void
    {
        // Enforce day-closure checks
        if ($expense->wasChanged('date')) {
            DayClosureService::checkAllowed($expense->getOriginal('date'));
        }
        DayClosureService::checkAllowed($expense->date);

        // Find or create appropriate operational tag based on category
        $tagName = match ($expense->category) {
            'diesel' => 'Diesel Used',
            'electricity' => 'Electricity',
            'labour' => 'Labour',
            'maintenance' => 'Maintenance',
            default => 'Other Expenses',
        };

        $tag = OperationalTag::firstOrCreate([
            'operational_unit_id' => $expense->operational_unit_id,
            'name' => $tagName,
            'type' => 'expense',
        ]);

        $remarks = "Auto-generated from Crusher Expense Log [Invoice: " . ($expense->invoice_number ?? 'N/A') . "]";
        if ($expense->remarks) {
            $remarks .= " - " . $expense->remarks;
        }

        OperationalRecord::updateOrCreate(
            ['crusher_expense_id' => $expense->id],
            [
                'operational_unit_id' => $expense->operational_unit_id,
                'operational_tag_id' => $tag->id,
                'date' => $expense->date,
                'quantity' => $expense->quantity,
                'rate' => $expense->rate,
                'amount' => $expense->amount,
                'remarks' => $remarks,
            ]
        );
    }

    /**
     * Handle the CrusherExpense "deleted" event.
     */
    public function deleted(CrusherExpense $expense): void
    {
        $record = OperationalRecord::where('crusher_expense_id', $expense->id)->first();
        if ($record) {
            DayClosureService::checkAllowed($record->date);
            $record->delete();
        }
    }

    /**
     * Handle the CrusherExpense "restored" event.
     */
    public function restored(CrusherExpense $expense): void
    {
        $this->saved($expense);
    }
}

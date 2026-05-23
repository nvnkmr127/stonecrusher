<?php

namespace App\Services\Crusher;

use App\Models\CrusherExpense;
use App\Models\OperationalRecord;
use App\Models\GatePass;
use App\Models\Vendor;
use Illuminate\Support\Facades\DB;

class CrusherReportingService
{
    /**
     * Get the validation rules for a Crusher Expense.
     */
    public static function getValidationRules(): array
    {
        return [
            'operational_unit_id' => 'required|exists:operational_units,id',
            'vendor_id' => 'nullable|exists:vendors,id',
            'date' => [
                'required',
                'date',
                function ($attribute, $value, $fail) {
                    if (\App\Services\DayClosureService::isClosed($value)) {
                        $fail('Transactions for this date are locked due to Daily Closing.');
                    }
                }
            ],
            'category' => 'required|in:diesel,electricity,labour,maintenance,other',
            'amount' => 'required|numeric|min:0.01',
            'quantity' => 'nullable|numeric|min:0',
            'rate' => 'nullable|numeric|min:0',
            'payment_mode' => 'required|in:cash,bank,upi,on_account',
            'invoice_number' => 'nullable|string|max:50',
            'remarks' => 'nullable|string|max:500',
        ];
    }

    /**
     * Compute summary statistics for a crusher unit between dates.
     */
    public function getCrusherSummary(int $unitId, string $startDate, string $endDate): array
    {
        // 1. Fetch Sales Revenue (from Gate Passes synced as 'revenue' type tags or directly from GatePass for absolute accuracy)
        $salesRevenue = (float) GatePass::where('source_unit_id', $unitId)
            ->where('status', \App\Enums\GatePassStatus::COMPLETED->value)
            ->where('activity_type', \App\Enums\ActivityType::SALES->value)
            ->whereBetween('date', [$startDate, $endDate])
            ->sum('total_amount');

        // 2. Fetch Detailed Expenses grouped by Category
        $expenses = CrusherExpense::where('operational_unit_id', $unitId)
            ->whereBetween('date', [$startDate, $endDate])
            ->select('category', DB::raw('SUM(amount) as total'))
            ->groupBy('category')
            ->pluck('total', 'category')
            ->toArray();

        $totalExpense = (float) array_sum($expenses);

        return [
            'unit_id' => $unitId,
            'period' => ['start' => $startDate, 'end' => $endDate],
            'revenue' => $salesRevenue,
            'expenses' => [
                'diesel' => (float) ($expenses['diesel'] ?? 0),
                'electricity' => (float) ($expenses['electricity'] ?? 0),
                'labour' => (float) ($expenses['labour'] ?? 0),
                'maintenance' => (float) ($expenses['maintenance'] ?? 0),
                'other' => (float) ($expenses['other'] ?? 0),
            ],
            'total_expense' => $totalExpense,
            'net_profit' => $salesRevenue - $totalExpense,
        ];
    }

    /**
     * Get vendor ledger statement / transaction list.
     */
    public function getVendorStatement(int $vendorId, string $startDate, string $endDate): array
    {
        $vendor = Vendor::findOrFail($vendorId);

        $expenses = CrusherExpense::where('vendor_id', $vendorId)
            ->whereBetween('date', [$startDate, $endDate])
            ->with('operationalUnit')
            ->orderBy('date', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        $totalInvoiced = (float) $expenses->sum('amount');
        $onAccountTotal = (float) $expenses->where('payment_mode', 'on_account')->sum('amount');
        $paidTotal = $totalInvoiced - $onAccountTotal;

        return [
            'vendor' => $vendor,
            'period' => ['start' => $startDate, 'end' => $endDate],
            'transactions' => $expenses,
            'summary' => [
                'total_invoiced' => $totalInvoiced,
                'paid_immediately' => $paidTotal,
                'added_to_outstanding' => $onAccountTotal,
            ]
        ];
    }
}

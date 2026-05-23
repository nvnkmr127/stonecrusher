<?php

namespace App\Services\Quarry;

use App\Models\ContractorAdvance;
use App\Models\DieselEntry;
use App\Models\QuarryDrillingLog;
use App\Models\QuarrySecondaryBlasting;
use App\Models\QuarryLabourSheet;
use App\Models\Setting;
use Illuminate\Support\Facades\DB;

class QuarryContractorService
{
    /**
     * Fetch un-deducted diesel logs for a contractor.
     */
    public function getUnadjustedDiesel(int $vendorId, string $asOfDate): \Illuminate\Support\Collection
    {
        $rate = Setting::get('default_diesel_rate', 100.00);

        return DieselEntry::where('vendor_id', $vendorId)
            ->where('is_deducted', false)
            ->where('date', '<=', $asOfDate)
            ->get()
            ->map(function ($entry) use ($rate) {
                $entry->cost = (float) ($entry->liters * $rate);
                return $entry;
            });
    }

    /**
     * Fetch outstanding advance balance for a contractor (vendor).
     */
    public function getAdvanceBalance(int $vendorId): float
    {
        $advancesPaid = (float) ContractorAdvance::where('vendor_id', $vendorId)->sum('amount');
        
        $drillingDeductions = (float) QuarryDrillingLog::where('vendor_id', $vendorId)->sum('advance_deduction_amount');
        $labourDeductions = (float) QuarryLabourSheet::where('vendor_id', $vendorId)->sum('advance_deduction_amount');

        return $advancesPaid - ($drillingDeductions + $labourDeductions);
    }

    /**
     * Create drilling log, executing diesel deductions and advance allocations atomically.
     */
    public function storeDrillingLog(array $data, array $dieselEntryIds): QuarryDrillingLog
    {
        return DB::transaction(function () use ($data, $dieselEntryIds) {
            // 1. Double check day lock
            \App\Services\DayClosureService::checkAllowed($data['date']);

            // 2. Fetch and aggregate chosen diesel costs
            $dieselAmount = 0.00;
            if (!empty($dieselEntryIds)) {
                $rate = Setting::get('default_diesel_rate', 100.00);
                $entries = DieselEntry::whereIn('id', $dieselEntryIds)
                    ->where('vendor_id', $data['vendor_id'])
                    ->where('is_deducted', false)
                    ->lockForUpdate()
                    ->get();

                $dieselAmount = (float) $entries->sum(fn($e) => $e->liters * $rate);
            }

            // 3. Check and cap advance deduction
            $advanceBalance = $this->getAdvanceBalance($data['vendor_id']);
            $advanceDeduction = min((float) ($data['advance_deduction_amount'] ?? 0), $advanceBalance);

            // 4. Calculate pricing
            $gross = (float) ($data['total_feet'] * $data['rate_per_foot']);
            $net = $gross - $dieselAmount - $advanceDeduction;

            // 5. Store log
            $log = QuarryDrillingLog::create(array_merge($data, [
                'gross_amount' => $gross,
                'diesel_deduction_amount' => $dieselAmount,
                'advance_deduction_amount' => $advanceDeduction,
                'net_amount' => $net,
            ]));

            // 6. Update diesel entries to prevent reuse
            if (!empty($dieselEntryIds)) {
                DieselEntry::whereIn('id', $dieselEntryIds)->update([
                    'is_deducted' => true,
                    'deducted_at_invoice_type' => 'drilling',
                    'deducted_at_invoice_id' => $log->id,
                ]);
            }

            return $log;
        });
    }

    /**
     * Create secondary blasting log with diesel deduction atomically.
     */
    public function storeSecondaryBlasting(array $data, array $dieselEntryIds): QuarrySecondaryBlasting
    {
        return DB::transaction(function () use ($data, $dieselEntryIds) {
            \App\Services\DayClosureService::checkAllowed($data['date']);

            $dieselAmount = 0.00;
            if (!empty($dieselEntryIds) && isset($data['vendor_id'])) {
                $rate = Setting::get('default_diesel_rate', 100.00);
                $entries = DieselEntry::whereIn('id', $dieselEntryIds)
                    ->where('vendor_id', $data['vendor_id'])
                    ->where('is_deducted', false)
                    ->lockForUpdate()
                    ->get();

                $dieselAmount = (float) $entries->sum(fn($e) => $e->liters * $rate);
            }

            $net = (float) $data['amount'] - $dieselAmount;

            $sb = QuarrySecondaryBlasting::create(array_merge($data, [
                'diesel_deduction_amount' => $dieselAmount,
                'net_amount' => $net,
            ]));

            if (!empty($dieselEntryIds)) {
                DieselEntry::whereIn('id', $dieselEntryIds)->update([
                    'is_deducted' => true,
                    'deducted_at_invoice_type' => 'secondary_blasting',
                    'deducted_at_invoice_id' => $sb->id,
                ]);
            }

            return $sb;
        });
    }

    /**
     * Create labour sheet log with advance deduction atomically.
     */
    public function storeLabourSheet(array $data): QuarryLabourSheet
    {
        return DB::transaction(function () use ($data) {
            \App\Services\DayClosureService::checkAllowed($data['date']);

            $advanceBalance = $this->getAdvanceBalance($data['vendor_id']);
            $advanceDeduction = min((float) ($data['advance_deduction_amount'] ?? 0), $advanceBalance);

            $gross = (float) ($data['no_of_workers'] * $data['rate_per_worker']);
            $net = $gross - $advanceDeduction;

            return QuarryLabourSheet::create(array_merge($data, [
                'gross_amount' => $gross,
                'advance_deduction_amount' => $advanceDeduction,
                'net_amount' => $net,
            ]));
        });
    }
}

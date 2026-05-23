<?php

namespace App\Services\Quarry;

use App\Models\QuarryDrillingLog;
use App\Models\QuarryBlast;
use App\Models\QuarryBlastingMaterialUsed;
use App\Models\QuarrySecondaryBlasting;
use App\Models\QuarryLabourSheet;
use App\Models\DieselEntry;
use App\Models\Setting;
use App\Models\Vendor;
use App\Models\OperationalRecord;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class QuarryCostService
{
    protected const CACHE_TTL = 600; // 10 minutes

    /**
     * Compute cost breakdown for a given date range.
     */
    public function getCostBreakdown(int $unitId, string $startDate, string $endDate, bool $forceRefresh = false): array
    {
        $cacheKey = "quarry:cost:breakdown:{$unitId}:{$startDate}:{$endDate}";

        if ($forceRefresh) {
            $this->forgetKey($cacheKey);
        }

        $this->registerKey($cacheKey);

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($unitId, $startDate, $endDate) {
            // 1. Drilling Logs Summary
            $drilling = QuarryDrillingLog::where('operational_unit_id', $unitId)
                ->whereBetween('date', [$startDate, $endDate])
                ->selectRaw('COALESCE(SUM(gross_amount), 0) as gross, COALESCE(SUM(diesel_deduction_amount), 0) as diesel_deduct, COALESCE(SUM(advance_deduction_amount), 0) as advance_deduct, COALESCE(SUM(net_amount), 0) as net')
                ->first();

            // 2. Blasting Materials Summary
            $blasting = QuarryBlastingMaterialUsed::join('quarry_blasts as qb', 'qb.id', '=', 'quarry_blasting_materials_used.quarry_blast_id')
                ->where('qb.operational_unit_id', $unitId)
                ->whereBetween('qb.date', [$startDate, $endDate])
                ->selectRaw('COALESCE(SUM(quarry_blasting_materials_used.amount), 0) as total')
                ->first();

            // 3. Secondary Blasting Summary
            $secondary = QuarrySecondaryBlasting::where('operational_unit_id', $unitId)
                ->whereBetween('date', [$startDate, $endDate])
                ->selectRaw('COALESCE(SUM(amount), 0) as gross, COALESCE(SUM(diesel_deduction_amount), 0) as diesel_deduct, COALESCE(SUM(net_amount), 0) as net')
                ->first();

            // 4. Contractor Labour Summary
            $labour = QuarryLabourSheet::where('operational_unit_id', $unitId)
                ->whereBetween('date', [$startDate, $endDate])
                ->selectRaw('COALESCE(SUM(gross_amount), 0) as gross, COALESCE(SUM(advance_deduction_amount), 0) as advance_deduct, COALESCE(SUM(net_amount), 0) as net')
                ->first();

            // 5. Internal Diesel (Quarry unit issues not linked to any contractor/vendor)
            $rate = Setting::get('default_diesel_rate', 100.00);
            $internalDieselLiters = (float) DieselEntry::where('operational_unit_id', $unitId)
                ->whereNull('vendor_id')
                ->whereBetween('date', [$startDate, $endDate])
                ->sum('liters');
            $internalDieselCost = $internalDieselLiters * $rate;

            $totalGross = (float) ($drilling->gross + ($blasting->total ?? 0) + $secondary->gross + $labour->gross + $internalDieselCost);
            $totalDieselDeductions = (float) ($drilling->diesel_deduct + $secondary->diesel_deduct);
            $totalAdvanceDeductions = (float) ($drilling->advance_deduct + $labour->advance_deduct);
            
            // Total net spending of the quarry
            $totalNet = (float) ($drilling->net + ($blasting->total ?? 0) + $secondary->net + $labour->net + $internalDieselCost);

            return [
                'unit_id' => $unitId,
                'period' => ['start' => $startDate, 'end' => $endDate],
                'drilling' => [
                    'gross' => (float) $drilling->gross,
                    'diesel_deduction' => (float) $drilling->diesel_deduct,
                    'advance_deduction' => (float) $drilling->advance_deduct,
                    'net' => (float) $drilling->net,
                ],
                'blasting_materials' => (float) ($blasting->total ?? 0),
                'secondary_blasting' => [
                    'gross' => (float) $secondary->gross,
                    'diesel_deduction' => (float) $secondary->diesel_deduct,
                    'net' => (float) $secondary->net,
                ],
                'contractor_labour' => [
                    'gross' => (float) $labour->gross,
                    'advance_deduction' => (float) $labour->advance_deduct,
                    'net' => (float) $labour->net,
                ],
                'internal_diesel' => [
                    'liters' => $internalDieselLiters,
                    'cost' => $internalDieselCost,
                ],
                'totals' => [
                    'gross' => $totalGross,
                    'diesel_deductions' => $totalDieselDeductions,
                    'advance_deductions' => $totalAdvanceDeductions,
                    'net' => $totalNet,
                ]
            ];
        });
    }

    /**
     * Compute daily summaries for a date range.
     */
    public function getDailySummary(int $unitId, string $startDate, string $endDate, bool $forceRefresh = false): array
    {
        $cacheKey = "quarry:cost:daily:{$unitId}:{$startDate}:{$endDate}";

        if ($forceRefresh) {
            $this->forgetKey($cacheKey);
        }

        $this->registerKey($cacheKey);

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($unitId, $startDate, $endDate) {
            // Sourced from operational_records for instant aggregation across all modules
            $records = OperationalRecord::query()
                ->join('operational_tags as t', 't.id', '=', 'operational_records.operational_tag_id')
                ->where('operational_records.operational_unit_id', $unitId)
                ->where('t.type', 'expense')
                ->whereBetween('operational_records.date', [$startDate, $endDate])
                ->selectRaw("operational_records.date, t.name as tag_name, SUM(operational_records.amount) as total")
                ->groupBy('operational_records.date', 'tag_name')
                ->orderBy('operational_records.date', 'asc')
                ->get();

            $dailyMap = [];
            $cursor = Carbon::parse($startDate);
            $end = Carbon::parse($endDate);

            while ($cursor <= $end) {
                $dateStr = $cursor->toDateString();
                $dailyMap[$dateStr] = [
                    'date' => $dateStr,
                    'drilling' => 0.00,
                    'blasting' => 0.00,
                    'secondary_blasting' => 0.00,
                    'labour' => 0.00,
                    'diesel' => 0.00,
                    'other' => 0.00,
                    'total' => 0.00,
                ];
                $cursor->addDay();
            }

            foreach ($records as $row) {
                $dateStr = $row->date->toDateString();
                if (!isset($dailyMap[$dateStr])) {
                    continue;
                }

                $total = (float) $row->total;
                $tagName = strtolower(trim($row->tag_name));

                if ($tagName === 'borewells') {
                    $dailyMap[$dateStr]['drilling'] += $total;
                } elseif ($tagName === 'blasting materials') {
                    $dailyMap[$dateStr]['blasting'] += $total;
                } elseif ($tagName === 'secondary blasting') {
                    $dailyMap[$dateStr]['secondary_blasting'] += $total;
                } elseif ($tagName === 'labour') {
                    $dailyMap[$dateStr]['labour'] += $total;
                } elseif ($tagName === 'diesel used') {
                    $dailyMap[$dateStr]['diesel'] += $total;
                } else {
                    $dailyMap[$dateStr]['other'] += $total;
                }
            }

            foreach ($dailyMap as &$day) {
                $day['total'] = $day['drilling'] + $day['blasting'] + $day['secondary_blasting'] + $day['labour'] + $day['diesel'] + $day['other'];
            }

            return [
                'unit_id' => $unitId,
                'period' => ['start' => $startDate, 'end' => $endDate],
                'daily_breakdown' => array_values($dailyMap),
            ];
        });
    }

    /**
     * Compute monthly summaries for a given year.
     */
    public function getMonthlySummary(int $unitId, int $year, bool $forceRefresh = false): array
    {
        $cacheKey = "quarry:cost:monthly:{$unitId}:{$year}";

        if ($forceRefresh) {
            $this->forgetKey($cacheKey);
        }

        $this->registerKey($cacheKey);

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($unitId, $year) {
            $driver = DB::connection()->getDriverName();
            $monthExpr = $driver === 'sqlite'
                ? "strftime('%m', operational_records.date)"
                : "MONTH(operational_records.date)";

            $records = OperationalRecord::query()
                ->join('operational_tags as t', 't.id', '=', 'operational_records.operational_tag_id')
                ->where('operational_records.operational_unit_id', $unitId)
                ->where('t.type', 'expense')
                ->whereYear('operational_records.date', $year)
                ->selectRaw("{$monthExpr} as month, t.name as tag_name, SUM(operational_records.amount) as total")
                ->groupBy('month', 'tag_name')
                ->get();

            $monthsData = [];
            for ($m = 1; $m <= 12; $m++) {
                $monthStr = str_pad($m, 2, '0', STR_PAD_LEFT);
                $monthsData[$monthStr] = [
                    'month' => $monthStr,
                    'month_name' => Carbon::createFromDate($year, $m, 1)->format('F'),
                    'drilling' => 0.00,
                    'blasting' => 0.00,
                    'secondary_blasting' => 0.00,
                    'labour' => 0.00,
                    'diesel' => 0.00,
                    'other' => 0.00,
                    'total' => 0.00,
                ];
            }

            foreach ($records as $row) {
                $monthStr = str_pad((int) $row->month, 2, '0', STR_PAD_LEFT);
                if (!isset($monthsData[$monthStr])) {
                    continue;
                }

                $total = (float) $row->total;
                $tagName = strtolower(trim($row->tag_name));

                if ($tagName === 'borewells') {
                    $monthsData[$monthStr]['drilling'] += $total;
                } elseif ($tagName === 'blasting materials') {
                    $monthsData[$monthStr]['blasting'] += $total;
                } elseif ($tagName === 'secondary blasting') {
                    $monthsData[$monthStr]['secondary_blasting'] += $total;
                } elseif ($tagName === 'labour') {
                    $monthsData[$monthStr]['labour'] += $total;
                } elseif ($tagName === 'diesel used') {
                    $monthsData[$monthStr]['diesel'] += $total;
                } else {
                    $monthsData[$monthStr]['other'] += $total;
                }
            }

            foreach ($monthsData as &$month) {
                $month['total'] = $month['drilling'] + $month['blasting'] + $month['secondary_blasting'] + $month['labour'] + $month['diesel'] + $month['other'];
            }

            return [
                'unit_id' => $unitId,
                'year' => $year,
                'monthly_breakdown' => array_values($monthsData),
            ];
        });
    }

    /**
     * Compute vendor-wise costing & deduction summaries for a date range.
     */
    public function getVendorSummary(int $unitId, string $startDate, string $endDate, bool $forceRefresh = false): array
    {
        $cacheKey = "quarry:cost:vendor:{$unitId}:{$startDate}:{$endDate}";

        if ($forceRefresh) {
            $this->forgetKey($cacheKey);
        }

        $this->registerKey($cacheKey);

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($unitId, $startDate, $endDate) {
            // Sourced from raw tables to track details of each vendor accurately
            $vendors = Vendor::where('is_active', true)->get();
            $summary = [];

            foreach ($vendors as $vendor) {
                // Drilling rig logs
                $drilling = QuarryDrillingLog::where('operational_unit_id', $unitId)
                    ->where('vendor_id', $vendor->id)
                    ->whereBetween('date', [$startDate, $endDate])
                    ->selectRaw('COALESCE(SUM(gross_amount), 0) as gross, COALESCE(SUM(diesel_deduction_amount), 0) as diesel, COALESCE(SUM(advance_deduction_amount), 0) as advance, COALESCE(SUM(net_amount), 0) as net')
                    ->first();

                // Secondary blasting
                $secondary = QuarrySecondaryBlasting::where('operational_unit_id', $unitId)
                    ->where('vendor_id', $vendor->id)
                    ->whereBetween('date', [$startDate, $endDate])
                    ->selectRaw('COALESCE(SUM(amount), 0) as gross, COALESCE(SUM(diesel_deduction_amount), 0) as diesel, COALESCE(SUM(net_amount), 0) as net')
                    ->first();

                // Labour sheets
                $labour = QuarryLabourSheet::where('operational_unit_id', $unitId)
                    ->where('vendor_id', $vendor->id)
                    ->whereBetween('date', [$startDate, $endDate])
                    ->selectRaw('COALESCE(SUM(gross_amount), 0) as gross, COALESCE(SUM(advance_deduction_amount), 0) as advance, COALESCE(SUM(net_amount), 0) as net')
                    ->first();

                $gross = (float) ($drilling->gross + $secondary->gross + $labour->gross);
                $dieselDeductions = (float) ($drilling->diesel + $secondary->diesel);
                $advanceDeductions = (float) ($drilling->advance + $labour->advance);
                $net = (float) ($drilling->net + $secondary->net + $labour->net);

                if ($gross > 0 || $dieselDeductions > 0 || $advanceDeductions > 0) {
                    $summary[] = [
                        'vendor_id' => $vendor->id,
                        'vendor_name' => $vendor->name,
                        'gross' => $gross,
                        'diesel_deductions' => $dieselDeductions,
                        'advance_deductions' => $advanceDeductions,
                        'net' => $net,
                    ];
                }
            }

            return [
                'unit_id' => $unitId,
                'period' => ['start' => $startDate, 'end' => $endDate],
                'vendors_breakdown' => $summary,
            ];
        });
    }

    /**
     * Flush all cached results for a specific operational unit.
     */
    public function clearCache(int $unitId): void
    {
        Cache::forget("dashboard:admin:payload:" . Carbon::today()->toDateString());

        $registry = Cache::get('quarry:cost:registry', []);
        $remaining = [];

        foreach ($registry as $key) {
            if (str_contains($key, "quarry:cost:breakdown:{$unitId}:") || 
                str_contains($key, "quarry:cost:daily:{$unitId}:") || 
                str_contains($key, "quarry:cost:monthly:{$unitId}:") || 
                str_contains($key, "quarry:cost:vendor:{$unitId}:")
            ) {
                Cache::forget($key);
            } else {
                $remaining[] = $key;
            }
        }

        Cache::forever('quarry:cost:registry', $remaining);
    }

    protected function registerKey(string $key): void
    {
        $registry = Cache::get('quarry:cost:registry', []);
        if (!in_array($key, $registry)) {
            $registry[] = $key;
            Cache::forever('quarry:cost:registry', $registry);
        }
    }

    protected function forgetKey(string $key): void
    {
        Cache::forget($key);
        $registry = Cache::get('quarry:cost:registry', []);
        if (($idx = array_search($key, $registry)) !== false) {
            unset($registry[$idx]);
            Cache::forever('quarry:cost:registry', array_values($registry));
        }
    }
}

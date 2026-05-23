<?php

namespace App\Services\Crusher;

use App\Models\OperationalRecord;
use App\Models\OperationalUnit;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CrusherProfitService
{
    /**
     * Cache TTL in seconds (10 minutes).
     */
    protected const CACHE_TTL = 600;

    /**
     * Calculate crusher profitability breakdown for a date range.
     */
    public function getProfitability(int $unitId, string $startDate, string $endDate, bool $forceRefresh = false): array
    {
        $cacheKey = "crusher:profit:{$unitId}:{$startDate}:{$endDate}";

        if ($forceRefresh) {
            $this->forgetKey($cacheKey);
        }

        $this->registerKey($cacheKey);

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($unitId, $startDate, $endDate) {
            // Single optimized query grouping by tag and type to avoid N+1 queries
            $records = OperationalRecord::query()
                ->join('operational_tags as t', 't.id', '=', 'operational_records.operational_tag_id')
                ->where('operational_records.operational_unit_id', $unitId)
                ->whereBetween('operational_records.date', [$startDate, $endDate])
                ->selectRaw("t.name as tag_name, t.type as tag_type, SUM(operational_records.amount) as total")
                ->groupBy('tag_name', 'tag_type')
                ->get();

            $metalSales = 0.00;
            $diesel = 0.00;
            $electricity = 0.00;
            $otherExpenses = 0.00;

            foreach ($records as $record) {
                $total = (float) $record->total;
                $tagName = strtolower(trim($record->tag_name));
                $tagType = strtolower(trim($record->tag_type));

                if ($tagType === 'revenue') {
                    $metalSales += $total;
                } else {
                    if ($tagName === 'diesel used') {
                        $diesel += $total;
                    } elseif ($tagName === 'electricity') {
                        $electricity += $total;
                    } else {
                        $otherExpenses += $total;
                    }
                }
            }

            $totalExpenses = $diesel + $electricity + $otherExpenses;
            $profit = $metalSales - $totalExpenses;

            return [
                'unit_id' => $unitId,
                'period' => [
                    'start' => $startDate,
                    'end' => $endDate,
                ],
                'metal_sales' => $metalSales,
                'diesel' => $diesel,
                'electricity' => $electricity,
                'other_expenses' => $otherExpenses,
                'total_expenses' => $totalExpenses,
                'profit' => $profit,
            ];
        });
    }

    /**
     * Get monthly profit summaries for a given year.
     */
    public function getMonthlyProfitSummary(int $unitId, int $year, bool $forceRefresh = false): array
    {
        $cacheKey = "crusher:profit:monthly:{$unitId}:{$year}";

        if ($forceRefresh) {
            $this->forgetKey($cacheKey);
        }

        $this->registerKey($cacheKey);

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($unitId, $year) {
            $driver = DB::connection()->getDriverName();

            // Extract month raw expression based on database engine
            $monthExpr = $driver === 'sqlite'
                ? "strftime('%m', operational_records.date)"
                : "MONTH(operational_records.date)";

            // Fetch all records for the year, grouped by month and tag type/name
            $rawRecords = OperationalRecord::query()
                ->join('operational_tags as t', 't.id', '=', 'operational_records.operational_tag_id')
                ->where('operational_records.operational_unit_id', $unitId)
                ->whereYear('operational_records.date', $year)
                ->selectRaw("{$monthExpr} as month, t.name as tag_name, t.type as tag_type, SUM(operational_records.amount) as total")
                ->groupBy('month', 'tag_name', 'tag_type')
                ->get();

            // Initialize all 12 months with default zero structures
            $monthsData = [];
            for ($m = 1; $m <= 12; $m++) {
                $monthStr = str_pad($m, 2, '0', STR_PAD_LEFT);
                $monthsData[$monthStr] = [
                    'month' => $monthStr,
                    'month_name' => Carbon::createFromDate($year, $m, 1)->format('F'),
                    'metal_sales' => 0.00,
                    'diesel' => 0.00,
                    'electricity' => 0.00,
                    'other_expenses' => 0.00,
                    'total_expenses' => 0.00,
                    'profit' => 0.00,
                ];
            }

            // Fill monthly buckets
            foreach ($rawRecords as $row) {
                $monthKey = str_pad((int) $row->month, 2, '0', STR_PAD_LEFT);
                if (!isset($monthsData[$monthKey])) {
                    continue;
                }

                $total = (float) $row->total;
                $tagName = strtolower(trim($row->tag_name));
                $tagType = strtolower(trim($row->tag_type));

                if ($tagType === 'revenue') {
                    $monthsData[$monthKey]['metal_sales'] += $total;
                } else {
                    if ($tagName === 'diesel used') {
                        $monthsData[$monthKey]['diesel'] += $total;
                    } elseif ($tagName === 'electricity') {
                        $monthsData[$monthKey]['electricity'] += $total;
                    } else {
                        $monthsData[$monthKey]['other_expenses'] += $total;
                    }
                }
            }

            // Calculate totals and profits
            foreach ($monthsData as $monthStr => &$data) {
                $data['total_expenses'] = $data['diesel'] + $data['electricity'] + $data['other_expenses'];
                $data['profit'] = $data['metal_sales'] - $data['total_expenses'];
            }

            return [
                'unit_id' => $unitId,
                'year' => $year,
                'monthly_breakdown' => array_values($monthsData),
            ];
        });
    }

    /**
     * Flush all cached results for a specific crusher unit.
     */
    public function clearCache(int $unitId): void
    {
        // Clear standard dashboard metrics cache as well
        Cache::forget("dashboard:admin:payload:" . Carbon::today()->toDateString());

        $registry = Cache::get('crusher:profit:registry', []);
        $remaining = [];

        foreach ($registry as $key) {
            if (str_contains($key, "crusher:profit:{$unitId}:") || str_contains($key, "crusher:profit:monthly:{$unitId}:")) {
                Cache::forget($key);
            } else {
                $remaining[] = $key;
            }
        }

        Cache::forever('crusher:profit:registry', $remaining);
    }

    protected function registerKey(string $key): void
    {
        $registry = Cache::get('crusher:profit:registry', []);
        if (!in_array($key, $registry)) {
            $registry[] = $key;
            Cache::forever('crusher:profit:registry', $registry);
        }
    }

    protected function forgetKey(string $key): void
    {
        Cache::forget($key);
        $registry = Cache::get('crusher:profit:registry', []);
        if (($idx = array_search($key, $registry)) !== false) {
            unset($registry[$idx]);
            Cache::forever('crusher:profit:registry', array_values($registry));
        }
    }
}

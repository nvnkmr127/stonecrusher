<?php

namespace App\Services\Finance;

use App\Models\OperationalRecord;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ProfitLossService
{
    protected const CACHE_TTL = 600; // 10 minutes

    /**
     * Compute P&L breakdown for a given date range.
     */
    public function getProfitLossBreakdown(string $startDate, string $endDate, bool $forceRefresh = false): array
    {
        $cacheKey = "finance:profit_loss:breakdown:{$startDate}:{$endDate}";

        if ($forceRefresh) {
            $this->forgetKey($cacheKey);
        }

        $this->registerKey($cacheKey);

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($startDate, $endDate) {
            $records = OperationalRecord::query()
                ->join('operational_tags as t', 't.id', '=', 'operational_records.operational_tag_id')
                ->join('operational_units as u', 'u.id', '=', 'operational_records.operational_unit_id')
                ->whereBetween('operational_records.date', [$startDate, $endDate])
                ->selectRaw("
                    t.name as tag_name, 
                    t.type as tag_type, 
                    u.code as unit_code,
                    SUM(operational_records.amount) as total
                ")
                ->groupBy('tag_name', 'tag_type', 'unit_code')
                ->get();

            $sales = 0.00;
            $crusherExpense = 0.00;
            $quarryExpense = 0.00;
            $labour = 0.00;
            $diesel = 0.00;
            $otherExpense = 0.00;

            foreach ($records as $record) {
                $total = (float) $record->total;
                $tagName = strtolower(trim($record->tag_name));
                $tagType = strtolower(trim($record->tag_type));
                $unitCode = strtoupper(trim($record->unit_code));

                if ($tagType === 'revenue') {
                    $sales += $total;
                } else {
                    if ($tagName === 'diesel used') {
                        $diesel += $total;
                    } elseif ($tagName === 'labour') {
                        $labour += $total;
                    } else {
                        if ($unitCode === 'CRS') {
                            $crusherExpense += $total;
                        } elseif ($unitCode === 'QRY') {
                            $quarryExpense += $total;
                        } else {
                            $otherExpense += $total;
                        }
                    }
                }
            }

            $netProfit = $sales - $crusherExpense - $quarryExpense - $labour - $diesel - $otherExpense;

            return [
                'period' => [
                    'start' => $startDate,
                    'end' => $endDate,
                ],
                'sales' => $sales,
                'crusher_expense' => $crusherExpense,
                'quarry_expense' => $quarryExpense,
                'labour' => $labour,
                'diesel' => $diesel,
                'other_expense' => $otherExpense,
                'net_profit' => $netProfit,
            ];
        });
    }

    /**
     * Compute monthly P&L summaries for a given year.
     */
    public function getMonthlyProfitLossSummary(int $year, bool $forceRefresh = false): array
    {
        $cacheKey = "finance:profit_loss:monthly:{$year}";

        if ($forceRefresh) {
            $this->forgetKey($cacheKey);
        }

        $this->registerKey($cacheKey);

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($year) {
            $driver = DB::connection()->getDriverName();
            $monthExpr = $driver === 'sqlite'
                ? "strftime('%m', operational_records.date)"
                : "MONTH(operational_records.date)";

            $records = OperationalRecord::query()
                ->join('operational_tags as t', 't.id', '=', 'operational_records.operational_tag_id')
                ->join('operational_units as u', 'u.id', '=', 'operational_records.operational_unit_id')
                ->whereYear('operational_records.date', $year)
                ->selectRaw("
                    {$monthExpr} as month,
                    t.name as tag_name, 
                    t.type as tag_type, 
                    u.code as unit_code,
                    SUM(operational_records.amount) as total
                ")
                ->groupBy('month', 'tag_name', 'tag_type', 'unit_code')
                ->get();

            $monthsData = [];
            for ($m = 1; $m <= 12; $m++) {
                $monthStr = str_pad($m, 2, '0', STR_PAD_LEFT);
                $monthsData[$monthStr] = [
                    'month' => $monthStr,
                    'month_name' => Carbon::createFromDate($year, $m, 1)->format('F'),
                    'sales' => 0.00,
                    'crusher_expense' => 0.00,
                    'quarry_expense' => 0.00,
                    'labour' => 0.00,
                    'diesel' => 0.00,
                    'other_expense' => 0.00,
                    'net_profit' => 0.00,
                ];
            }

            foreach ($records as $row) {
                $monthKey = str_pad((int) $row->month, 2, '0', STR_PAD_LEFT);
                if (!isset($monthsData[$monthKey])) {
                    continue;
                }

                $total = (float) $row->total;
                $tagName = strtolower(trim($row->tag_name));
                $tagType = strtolower(trim($row->tag_type));
                $unitCode = strtoupper(trim($row->unit_code));

                if ($tagType === 'revenue') {
                    $monthsData[$monthKey]['sales'] += $total;
                } else {
                    if ($tagName === 'diesel used') {
                        $monthsData[$monthKey]['diesel'] += $total;
                    } elseif ($tagName === 'labour') {
                        $monthsData[$monthKey]['labour'] += $total;
                    } else {
                        if ($unitCode === 'CRS') {
                            $monthsData[$monthKey]['crusher_expense'] += $total;
                        } elseif ($unitCode === 'QRY') {
                            $monthsData[$monthKey]['quarry_expense'] += $total;
                        } else {
                            $monthsData[$monthKey]['other_expense'] += $total;
                        }
                    }
                }
            }

            foreach ($monthsData as &$month) {
                $month['net_profit'] = $month['sales'] 
                    - $month['crusher_expense'] 
                    - $month['quarry_expense'] 
                    - $month['labour'] 
                    - $month['diesel'] 
                    - $month['other_expense'];
            }

            return [
                'year' => $year,
                'monthly_breakdown' => array_values($monthsData),
            ];
        });
    }

    /**
     * Flush all P&L cache keys.
     */
    public function clearCache(): void
    {
        Cache::forget("dashboard:admin:payload:" . Carbon::today()->toDateString());

        $registry = Cache::get('finance:profit_loss:registry', []);
        $remaining = [];

        foreach ($registry as $key) {
            if (str_contains($key, "finance:profit_loss:breakdown:") || 
                str_contains($key, "finance:profit_loss:monthly:")
            ) {
                Cache::forget($key);
            } else {
                $remaining[] = $key;
            }
        }

        Cache::forever('finance:profit_loss:registry', $remaining);
    }

    protected function registerKey(string $key): void
    {
        $registry = Cache::get('finance:profit_loss:registry', []);
        if (!in_array($key, $registry)) {
            $registry[] = $key;
            Cache::forever('finance:profit_loss:registry', $registry);
        }
    }

    protected function forgetKey(string $key): void
    {
        Cache::forget($key);
        $registry = Cache::get('finance:profit_loss:registry', []);
        if (($idx = array_search($key, $registry)) !== false) {
            unset($registry[$idx]);
            Cache::forever('finance:profit_loss:registry', array_values($registry));
        }
    }
}

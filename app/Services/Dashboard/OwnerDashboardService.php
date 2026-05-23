<?php

namespace App\Services\Dashboard;

use App\Models\Client;
use App\Models\ClientTransaction;
use App\Models\DieselEntry;
use App\Models\GatePass;
use App\Models\OperationalRecord;
use App\Services\Finance\ProfitLossService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class OwnerDashboardService
{
    protected const CACHE_TTL = 600; // 10 minutes
    protected ProfitLossService $profitLossService;

    public function __construct(ProfitLossService $profitLossService)
    {
        $this->profitLossService = $profitLossService;
    }

    /**
     * Get owner dashboard metrics payload.
     */
    public function getMetrics(bool $forceRefresh = false): array
    {
        $todayStr = Carbon::today()->toDateString();
        $cacheKey = "dashboard:owner:payload:{$todayStr}";

        if ($forceRefresh) {
            $this->clearCache();
        }

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($todayStr) {
            $today = Carbon::today();
            $monthStart = $today->copy()->startOfMonth()->toDateString();
            $monthEnd = $today->copy()->endOfMonth()->toDateString();

            // 1. Today's Widgets
            $todaySales = (float) OperationalRecord::query()
                ->join('operational_tags as t', 't.id', '=', 'operational_records.operational_tag_id')
                ->where('t.type', 'revenue')
                ->whereDate('operational_records.date', $todayStr)
                ->sum('operational_records.amount');

            $todayCrusherExpense = (float) OperationalRecord::query()
                ->join('operational_tags as t', 't.id', '=', 'operational_records.operational_tag_id')
                ->join('operational_units as u', 'u.id', '=', 'operational_records.operational_unit_id')
                ->where('t.type', 'expense')
                ->where('u.code', 'CRS')
                ->whereNotIn('t.name', ['Labour', 'Diesel Used'])
                ->whereDate('operational_records.date', $todayStr)
                ->sum('operational_records.amount');

            $todayQuarryExpense = (float) OperationalRecord::query()
                ->join('operational_tags as t', 't.id', '=', 'operational_records.operational_tag_id')
                ->join('operational_units as u', 'u.id', '=', 'operational_records.operational_unit_id')
                ->where('t.type', 'expense')
                ->where('u.code', 'QRY')
                ->whereNotIn('t.name', ['Labour', 'Diesel Used'])
                ->whereDate('operational_records.date', $todayStr)
                ->sum('operational_records.amount');

            $todayLabour = (float) OperationalRecord::query()
                ->join('operational_tags as t', 't.id', '=', 'operational_records.operational_tag_id')
                ->where('t.type', 'expense')
                ->where('t.name', 'Labour')
                ->whereDate('operational_records.date', $todayStr)
                ->sum('operational_records.amount');

            $todayDiesel = (float) OperationalRecord::query()
                ->join('operational_tags as t', 't.id', '=', 'operational_records.operational_tag_id')
                ->where('t.type', 'expense')
                ->where('t.name', 'Diesel Used')
                ->whereDate('operational_records.date', $todayStr)
                ->sum('operational_records.amount');

            $todayNetProfit = $todaySales - $todayCrusherExpense - $todayQuarryExpense - $todayLabour - $todayDiesel;

            $todayTrips = GatePass::whereDate('date', $todayStr)
                ->where('status', 'completed')
                ->count();

            $todayDieselLiters = (float) DieselEntry::whereDate('date', $todayStr)->sum('liters');

            // 2. Month-To-Date (MTD) Widgets via ProfitLossService
            $mtdPL = $this->profitLossService->getProfitLossBreakdown($monthStart, $todayStr);

            $mtdTrips = GatePass::whereBetween('date', [$monthStart, $todayStr . ' 23:59:59'])
                ->where('status', 'completed')
                ->count();

            $mtdDieselLiters = (float) DieselEntry::whereBetween('date', [$monthStart, $todayStr])
                ->sum('liters');

            // 3. Outstanding Receivables
            $clients = Client::withSum(['transactions as total_credit' => function ($q) {
                $q->where('transaction_type', 'credit');
            }], 'amount')
            ->withSum(['transactions as total_debit' => function ($q) {
                $q->where('transaction_type', 'debit');
            }], 'amount')
            ->get();

            $totalOutstanding = 0.00;
            foreach ($clients as $client) {
                $bal = ($client->total_debit ?? 0) - ($client->total_credit ?? 0);
                if ($bal > 0) {
                    $totalOutstanding += $bal;
                }
            }

            // 4. Charts Summary Data

            // Chart A: YTD P&L Trend (Last 6 Months)
            $ytdSummary = $this->profitLossService->getMonthlyProfitLossSummary($today->year);
            $monthsList = $ytdSummary['monthly_breakdown'];
            // Slice last 6 months up to current month
            $currentMonthInt = (int) $today->month;
            $chartMonths = array_slice($monthsList, max(0, $currentMonthInt - 6), min(6, $currentMonthInt));

            $ytdChart = [
                'months' => collect($chartMonths)->pluck('month_name')->toArray(),
                'sales' => collect($chartMonths)->pluck('sales')->toArray(),
                'expenses' => collect($chartMonths)->map(function ($m) {
                    return $m['crusher_expense'] + $m['quarry_expense'] + $m['labour'] + $m['diesel'] + $m['other_expense'];
                })->toArray(),
                'net_profit' => collect($chartMonths)->pluck('net_profit')->toArray(),
            ];

            // Chart B: MTD Expense split (Donut)
            $expenseDonut = [
                'labels' => ['Crusher Expenses', 'Quarry Expenses', 'Contractor Labour', 'Fuel/Diesel'],
                'values' => [
                    $mtdPL['crusher_expense'],
                    $mtdPL['quarry_expense'],
                    $mtdPL['labour'],
                    $mtdPL['diesel']
                ]
            ];

            // Chart C: Last 15 Days Sales & Fuel Trend
            $dates = [];
            $salesTrend = [];
            $dieselTrend = [];

            for ($i = 14; $i >= 0; $i--) {
                $cursorDate = $today->copy()->subDays($i);
                $cursorDateStr = $cursorDate->toDateString();
                $dates[] = $cursorDate->format('d M');

                $daySales = (float) OperationalRecord::query()
                    ->join('operational_tags as t', 't.id', '=', 'operational_records.operational_tag_id')
                    ->where('t.type', 'revenue')
                    ->whereDate('operational_records.date', $cursorDateStr)
                    ->sum('operational_records.amount');

                $dayDieselLiters = (float) DieselEntry::whereDate('date', $cursorDateStr)->sum('liters');

                $salesTrend[] = $daySales;
                $dieselTrend[] = $dayDieselLiters;
            }

            $dailyTrendChart = [
                'dates' => $dates,
                'sales' => $salesTrend,
                'diesel' => $dieselTrend,
            ];

            return [
                'today' => [
                    'sales' => $todaySales,
                    'crusher_expense' => $todayCrusherExpense,
                    'quarry_expense' => $todayQuarryExpense,
                    'net_profit' => $todayNetProfit,
                    'trips' => $todayTrips,
                    'diesel_liters' => $todayDieselLiters,
                ],
                'mtd' => [
                    'sales' => $mtdPL['sales'],
                    'crusher_expense' => $mtdPL['crusher_expense'],
                    'quarry_expense' => $mtdPL['quarry_expense'],
                    'net_profit' => $mtdPL['net_profit'],
                    'trips' => $mtdTrips,
                    'diesel_liters' => $mtdDieselLiters,
                ],
                'outstanding' => $totalOutstanding,
                'charts' => [
                    'ytd' => $ytdChart,
                    'donut' => $expenseDonut,
                    'daily' => $dailyTrendChart,
                ],
                'last_updated' => Carbon::now()->format('h:i A'),
            ];
        });
    }

    /**
     * Clear owner dashboard cache.
     */
    public function clearCache(): void
    {
        $todayStr = Carbon::today()->toDateString();
        Cache::forget("dashboard:owner:payload:{$todayStr}");
    }
}

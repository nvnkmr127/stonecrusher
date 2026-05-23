<?php
 
namespace App\Services\Dashboard;
 
use App\Models\DieselEntry;
use App\Models\GatePass;
use App\Models\OperationalRecord;
use App\Models\OperationalUnit;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
 
class AdminDashboardMetricsService
{
    public function get(): array
    {
        $today = Carbon::today();
        $monthStart = $today->copy()->startOfMonth();
        $monthKey = $today->format('Y-m');
 
        return Cache::remember("dashboard:admin:payload:{$today->toDateString()}", 60, function () use ($today, $monthStart) {
            $todayStart = $today->copy()->startOfDay();
            $todayEnd = $today->copy()->endOfDay();
 
            $projectStats = $this->projectStats();
            $recentProjects = \App\Models\Project::with('client')->latest()->take(5)->get();
            $totalClients = \App\Models\Client::count();
            $vehicleStats = $this->vehicleStats();
 
            $dailyStats = $this->gatePassDailyStats($todayStart, $todayEnd);
            $dieselStats = $this->dieselStats($todayStart, $todayEnd, $today);
            $maintenanceStats = $this->maintenanceStats($today);
 
            $series7 = $this->seriesLastNDays(7, $today);
            $materialMix = $this->materialMixForRange($monthStart->copy()->startOfDay(), $todayEnd);
 
            $pnl = $this->monthToDatePnl($monthStart->toDateString(), $today->toDateString());
            $monthlyPnlSnapshot = $this->monthlyPnlSnapshot($today->month, $today->year);
 
            return [
                'projectStats' => $projectStats,
                'recentProjects' => $recentProjects,
                'totalClients' => $totalClients,
                'vehicleStats' => $vehicleStats,
                'dailyStats' => $dailyStats,
                'dieselStats' => $dieselStats,
                'maintenanceStats' => $maintenanceStats,
                'chartData' => [
                    'dates' => json_encode($series7['labels']),
                    'loads' => json_encode($series7['loads']),
                    'tonnage' => json_encode($series7['tonnage']),
                    'revenue' => json_encode($series7['revenue']),
                    'diesel' => json_encode($series7['diesel']),
                    'material_names' => json_encode($materialMix['names']),
                    'material_counts' => json_encode($materialMix['counts']),
                ],
                'profitability' => $pnl,
                'monthlyPnlSnapshot' => $monthlyPnlSnapshot,
            ];
        });
    }
 
    private function projectStats(): array
    {
        $rows = \App\Models\Project::select('status', DB::raw('COUNT(*) as c'))
            ->groupBy('status')
            ->pluck('c', 'status');
 
        return [
            'total' => (int) $rows->sum(),
            'active' => (int) ($rows['active'] ?? 0),
            'completed' => (int) ($rows['completed'] ?? 0),
            'pending' => (int) ($rows['pending'] ?? 0),
        ];
    }
 
    private function vehicleStats(): array
    {
        $total = \App\Models\Vehicle::count();
        $active = \App\Models\Vehicle::where('is_active', true)->count();
        return ['total' => $total, 'active' => $active];
    }
 
    private function gatePassDailyStats(Carbon $start, Carbon $end): array
    {
        $row = GatePass::whereBetween('date', [$start, $end])
            ->where('status', 'completed')
            ->selectRaw('COUNT(*) as loads, COALESCE(SUM(net_weight),0) as tonnage, COALESCE(SUM(total_amount),0) as amount')
            ->first();
 
        return [
            'loads' => (int) ($row->loads ?? 0),
            'tonnage' => (float) ($row->tonnage ?? 0),
            'amount' => (float) ($row->amount ?? 0),
        ];
    }
 
    private function dieselStats(Carbon $todayStart, Carbon $todayEnd, Carbon $today): array
    {
        $todayLiters = (float) DieselEntry::whereBetween('date', [$todayStart, $todayEnd])->sum('liters');
        $monthLiters = (float) DieselEntry::whereMonth('date', $today->month)->whereYear('date', $today->year)->sum('liters');
 
        return [
            'today_liters' => $todayLiters,
            'month_liters' => $monthLiters,
        ];
    }
 
    private function maintenanceStats(Carbon $today): array
    {
        $thisMonthCost = (float) \App\Models\VehicleMaintenance::whereMonth('date', $today->month)
            ->whereYear('date', $today->year)
            ->sum('cost');
 
        return [
            'this_month_cost' => $thisMonthCost,
            'pending_reminders' => 0,
        ];
    }
 
    private function seriesLastNDays(int $days, Carbon $today): array
    {
        $start = $today->copy()->subDays($days - 1)->startOfDay();
        $end = $today->copy()->endOfDay();
 
        $gatePassRows = GatePass::whereBetween('date', [$start, $end])
            ->where('status', 'completed')
            ->selectRaw('DATE(date) as d, COUNT(*) as loads, COALESCE(SUM(net_weight),0) as tonnage, COALESCE(SUM(total_amount),0) as revenue')
            ->groupBy('d')
            ->get()
            ->keyBy('d');
 
        $dieselRows = DieselEntry::whereBetween('date', [$start, $end])
            ->selectRaw('DATE(date) as d, COALESCE(SUM(liters),0) as liters')
            ->groupBy('d')
            ->pluck('liters', 'd');
 
        $labels = [];
        $loads = [];
        $tonnage = [];
        $revenue = [];
        $diesel = [];
 
        $cursor = $start->copy();
        while ($cursor <= $end) {
            $key = $cursor->toDateString();
            $labels[] = $cursor->format('d M');
 
            $gp = $gatePassRows[$key] ?? null;
            $loads[] = $gp ? (int) $gp->loads : 0;
            $tonnage[] = $gp ? round((float) $gp->tonnage, 2) : 0.0;
            $revenue[] = $gp ? (float) $gp->revenue : 0.0;
            $diesel[] = isset($dieselRows[$key]) ? (float) $dieselRows[$key] : 0.0;
 
            $cursor->addDay();
        }
 
        return compact('labels', 'loads', 'tonnage', 'revenue', 'diesel');
    }
 
    private function materialMixForRange(Carbon $start, Carbon $end): array
    {
        $rows = GatePass::selectRaw('metal_type_id, COUNT(*) as c')
            ->whereBetween('date', [$start, $end])
            ->where('status', 'completed')
            ->whereNotNull('metal_type_id')
            ->groupBy('metal_type_id')
            ->with('metalType')
            ->get()
            ->map(fn ($item) => [
                'name' => $item->metalType ? $item->metalType->name : 'Unknown',
                'count' => (int) $item->c,
            ]);
 
        return [
            'names' => $rows->pluck('name')->values(),
            'counts' => $rows->pluck('count')->values(),
        ];
    }
 
    private function monthToDatePnl(string $startDate, string $endDate): array
    {
        $quarry = OperationalUnit::where('code', 'QRY')->first();
        $crusher = OperationalUnit::where('code', 'CRS')->first();
 
        $quarryNet = $quarry ? $this->unitNet($quarry->id, $startDate, $endDate) : ['revenue' => 0.0, 'expense' => 0.0, 'net' => 0.0];
        $crusherNet = $crusher ? $this->unitNet($crusher->id, $startDate, $endDate) : ['revenue' => 0.0, 'expense' => 0.0, 'net' => 0.0];
 
        return [
            'quarry' => $quarryNet,
            'crusher' => $crusherNet,
            'net' => (float) $quarryNet['net'] + (float) $crusherNet['net'],
        ];
    }
 
    private function unitNet(int $unitId, string $startDate, string $endDate): array
    {
        $row = OperationalRecord::query()
            ->join('operational_tags as t', 't.id', '=', 'operational_records.operational_tag_id')
            ->where('operational_records.operational_unit_id', $unitId)
            ->whereBetween('operational_records.date', [$startDate, $endDate])
            ->selectRaw("
                COALESCE(SUM(CASE WHEN t.type = 'revenue' THEN operational_records.amount ELSE 0 END),0) as revenue,
                COALESCE(SUM(CASE WHEN t.type = 'expense' THEN operational_records.amount ELSE 0 END),0) as expense
            ")
            ->first();
 
        $revenue = (float) ($row->revenue ?? 0);
        $expense = (float) ($row->expense ?? 0);
        return ['revenue' => $revenue, 'expense' => $expense, 'net' => $revenue - $expense];
    }
 
    private function monthlyPnlSnapshot(int $month, int $year): array
    {
        $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth()->toDateString();
        $endDate = Carbon::createFromDate($year, $month, 1)->endOfMonth()->toDateString();
 
        $quarry = OperationalUnit::where('code', 'QRY')->first();
        $crusher = OperationalUnit::where('code', 'CRS')->first();
 
        return [
            'month' => $month,
            'year' => $year,
            'quarry' => $quarry ? $this->unitTagBreakdown($quarry->id, $startDate, $endDate) : null,
            'crusher' => $crusher ? $this->unitTagBreakdown($crusher->id, $startDate, $endDate) : null,
        ];
    }
 
    private function unitTagBreakdown(int $unitId, string $startDate, string $endDate): array
    {
        $rows = OperationalRecord::query()
            ->join('operational_tags as t', 't.id', '=', 'operational_records.operational_tag_id')
            ->where('operational_records.operational_unit_id', $unitId)
            ->whereBetween('operational_records.date', [$startDate, $endDate])
            ->selectRaw("t.name as name, t.type as type, COALESCE(SUM(operational_records.amount),0) as amount")
            ->groupBy('name', 'type')
            ->orderByDesc('amount')
            ->get();
 
        $revenues = $rows->where('type', 'revenue')->values();
        $expenses = $rows->where('type', 'expense')->values();
 
        $totalRevenue = (float) $revenues->sum('amount');
        $totalExpense = (float) $expenses->sum('amount');
 
        return [
            'revenues' => $revenues,
            'expenses' => $expenses,
            'total_revenue' => $totalRevenue,
            'total_expense' => $totalExpense,
            'net' => $totalRevenue - $totalExpense,
        ];
    }
}

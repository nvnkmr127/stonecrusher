<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $projectStats = [
            'total' => \App\Models\Project::count(),
            'active' => \App\Models\Project::where('status', 'active')->count(),
            'completed' => \App\Models\Project::where('status', 'completed')->count(),
            'pending' => \App\Models\Project::where('status', 'pending')->count(),
        ];

        $recentProjects = \App\Models\Project::with('client')
            ->latest()
            ->take(5)
            ->get();

        $totalClients = \App\Models\Client::count();
        $vehicleStats = [
            'total' => \App\Models\Vehicle::count(),
            'active' => \App\Models\Vehicle::where('is_active', true)->count(),
        ];

        $todayStart = now()->startOfDay();
        $todayEnd = now()->endOfDay();

        $dailyStats = [
            'loads' => \App\Models\GatePass::whereBetween('date', [$todayStart, $todayEnd])->count(),
            'tonnage' => \App\Models\GatePass::whereBetween('date', [$todayStart, $todayEnd])->sum('net_weight'),
            'amount' => \App\Models\GatePass::whereBetween('date', [$todayStart, $todayEnd])->sum('total_amount'),
        ];

        $dieselStats = [
            'today_liters' => \App\Models\DieselEntry::whereBetween('date', [$todayStart, $todayEnd])->sum('liters'),
            'month_liters' => \App\Models\DieselEntry::whereMonth('date', now()->month)
                ->whereYear('date', now()->year)
                ->sum('liters'),
        ];

        $maintenanceStats = [
            'this_month_cost' => \App\Models\VehicleMaintenance::whereMonth('date', now()->month)
                ->whereYear('date', now()->year)
                ->sum('cost'),
            'pending_reminders' => 0, // Placeholder for future
        ];


        // --- Chart Data ---
        $chartDates = [];
        $chartLoads = [];
        $chartTonnage = [];
        $chartRevenue = [];
        $chartDiesel = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = \Carbon\Carbon::today()->subDays($i);
            $chartDates[] = $date->format('d M');

            $start = $date->copy()->startOfDay();
            $end = $date->copy()->endOfDay();

            $chartLoads[] = \App\Models\GatePass::whereBetween('date', [$start, $end])->count();
            $chartTonnage[] = round((float) \App\Models\GatePass::whereBetween('date', [$start, $end])->sum('net_weight'), 2);
            $chartRevenue[] = \App\Models\GatePass::whereBetween('date', [$start, $end])->sum('total_amount');
            $chartDiesel[] = round((float) \App\Models\DieselEntry::whereBetween('date', [$start, $end])->sum('liters'), 2);
        }

        $materialData = \App\Models\GatePass::selectRaw('metal_type_id, count(*) as count')
            ->whereNotNull('metal_type_id')
            ->groupBy('metal_type_id')
            ->with('metalType')
            ->get()
            ->map(function ($item) {
                return [
                    'name' => $item->metalType ? $item->metalType->name : 'Unknown',
                    'count' => $item->count
                ];
            });

        $chartData = [
            'dates' => json_encode($chartDates),
            'loads' => json_encode($chartLoads),
            'tonnage' => json_encode($chartTonnage),
            'revenue' => json_encode($chartRevenue),
            'diesel' => json_encode($chartDiesel),
            'material_names' => json_encode($materialData->pluck('name')),
            'material_counts' => json_encode($materialData->pluck('count')),
        ];

        return view('admin.dashboard', compact('projectStats', 'recentProjects', 'totalClients', 'vehicleStats', 'dailyStats', 'dieselStats', 'maintenanceStats', 'chartData'));
    }

}

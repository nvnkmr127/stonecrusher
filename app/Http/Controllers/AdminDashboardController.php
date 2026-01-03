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

        return view('admin.dashboard', compact('projectStats', 'recentProjects', 'totalClients', 'vehicleStats', 'dailyStats'));
    }
}

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

        $systemHealth = [
            'database' => 'Online', // Default
            'disk_free' => $this->humanFileSize(disk_free_space(base_path())),
            'disk_total' => $this->humanFileSize(disk_total_space(base_path())),
            'server_time' => now()->format('Y-m-d H:i:s'),
        ];

        try {
            \Illuminate\Support\Facades\DB::connection()->getPdo();
        } catch (\Exception $e) {
            $systemHealth['database'] = 'Offline';
        }

        return view('admin.dashboard', compact('projectStats', 'recentProjects', 'totalClients', 'vehicleStats', 'dailyStats', 'systemHealth'));
    }

    private function humanFileSize($size, $unit = "")
    {
        if ((!$unit && $size >= 1 << 30) || $unit == "GB")
            return number_format($size / (1 << 30), 2) . "GB";
        if ((!$unit && $size >= 1 << 20) || $unit == "MB")
            return number_format($size / (1 << 20), 2) . "MB";
        if ((!$unit && $size >= 1 << 10) || $unit == "KB")
            return number_format($size / (1 << 10), 2) . "KB";
        return number_format($size) . " bytes";
    }
}

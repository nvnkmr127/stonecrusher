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

        return view('admin.dashboard', compact('projectStats', 'recentProjects'));
    }
}

<?php

namespace App\Http\Controllers;

use App\Services\Dashboard\OwnerDashboardService;
use Illuminate\Http\Request;

class OwnerDashboardController extends Controller
{
    protected OwnerDashboardService $dashboardService;

    public function __construct(OwnerDashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }

    /**
     * Display the owner dashboard.
     */
    public function index(Request $request)
    {
        $forceRefresh = filter_var($request->input('force_refresh'), FILTER_VALIDATE_BOOLEAN);
        $data = $this->dashboardService->getMetrics($forceRefresh);

        return view('owner.dashboard', $data);
    }
}

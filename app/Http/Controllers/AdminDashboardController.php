<?php
 
namespace App\Http\Controllers;
 
use Illuminate\Http\Request;
use App\Services\Dashboard\AdminDashboardMetricsService;
 
class AdminDashboardController extends Controller
{
    public function index(AdminDashboardMetricsService $metrics)
    {
        $data = $metrics->get();
        return view('admin.dashboard', $data);
    }
}

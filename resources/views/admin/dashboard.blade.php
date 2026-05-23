<x-tabler-layout>
    <x-slot name="header">
        <div class="row g-2 align-items-center d-print-none">
            <div class="col">
                <div class="page-pretitle text-uppercase fw-bold text-muted" style="letter-spacing: 0.05em;">Overview</div>
                <h2 class="page-title h1 fw-bold">Admin Dashboard</h2>
            </div>
            <div class="col-auto ms-auto">
                <div class="btn-list">
                    <span class="d-none d-md-inline-block text-muted me-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-clock me-1" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0"></path><path d="M12 7l0 5l3 3"></path></svg>
                        Last updated: {{ now()->format('h:i A') }}
                    </span>
                    <a href="{{ route('gate-passes.create') }}" class="btn btn-primary shadow-sm px-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-plus" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M12 5l0 14"></path><path d="M5 12l14 0"></path></svg>
                        New Entry
                    </a>
                </div>
            </div>
        </div>
    </x-slot>
 
    <!-- Welcome -->
    <div class="premium-header-card mb-4">
        <div class="row align-items-center">
            <div class="col-md-7">
                <h1 class="text-white fw-bold mb-2">Welcome back, {{ explode(' ', Auth::user()->name)[0] }}!</h1>
                <p class="text-white-50 mb-0">The system is working correctly. Here is what happened today.</p>
            </div>
            <div class="col-md-5 text-md-end mt-4 mt-md-0">
                <div class="d-inline-block text-start p-3 rounded-3" style="background: rgba(255,255,255,0.05); backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.1);">
                    <div class="text-white-50 small text-uppercase fw-bold mb-1" style="letter-spacing: 1px;">Today's Date</div>
                    <div class="h2 text-white mb-0 fw-bold">{{ now()->format('l, d M Y') }}</div>
                </div>
            </div>
        </div>
    </div>
 
    <!-- Today's Stats -->
    <div class="premium-stats-grid mb-4">
        <x-metric-card label="Today's Trips" value="{{ $dailyStats['loads'] }}" subtext="Total loads entered">
            <x-slot name="icon">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon text-blue" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M7 17m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" /><path d="M17 17m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" /></svg>
            </x-slot>
        </x-metric-card>
 
        <x-metric-card label="Today's Material" value="{{ number_format($dailyStats['tonnage'], 1) }} <span class='fs-6 text-muted fw-normal'>CFT</span>" accentText="text-green" accentBg="bg-green-lt" subtext="Total material out">
            <x-slot name="icon">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon text-green" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 3l8 4.5l0 9l-8 4.5l-8 -4.5l0 -9l8 -4.5" /></svg>
            </x-slot>
        </x-metric-card>
 
        <x-metric-card label="Today's Billing" value="₹ {{ number_format($dailyStats['amount'], 0) }}" accentText="text-orange" accentBg="bg-orange-lt" subtext="Total value of trips">
            <x-slot name="icon">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon text-orange" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M16.7 8a3 3 0 1 0 -2.7 -2" /><path d="M12 8v11m-5 -5h10" /></svg>
            </x-slot>
        </x-metric-card>
 
        <x-metric-card label="Today's Diesel" value="{{ number_format($dieselStats['today_liters'], 1) }} <span class='fs-6 text-muted fw-normal'>LTRS</span>" accentText="text-purple" accentBg="bg-purple-lt" subtext="Total fuel given out">
            <x-slot name="icon">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon text-purple" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M14 11h1a2 2 0 0 1 2 2v3a1.5 1.5 0 0 0 3 0v-7l-3 -3"></path><path d="M4 20v-14a2 2 0 0 1 2 -2h6a2 2 0 0 1 2 2v14"></path></svg>
            </x-slot>
        </x-metric-card>
    </div>
 
    <!-- Profitability MTD Row -->
    <div class="row row-cards mb-4">
        <div class="col-md-4">
            <x-metric-card label="Crusher Profit" value="₹ {{ number_format($profitability['crusher']['net'], 2) }}" accentText="text-primary" accentBg="bg-primary-lt" subtext="Month-to-Date Net Profit" class="w-100 h-100">
                <x-slot name="icon">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon text-primary" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M11.414 10l-7.383 7.414a2 2 0 1 0 2.829 2.828l7.414 -7.386" /><path d="M16.828 8.172l-1.414 -1.414l1.414 -1.414l2.829 2.829l-1.414 1.414z" /></svg>
                </x-slot>
            </x-metric-card>
        </div>
        <div class="col-md-4">
            <x-metric-card label="Quarry Expense" value="₹ {{ number_format($profitability['quarry']['expense'], 2) }}" accentText="text-danger" accentBg="bg-red-lt" subtext="Month-to-Date Operating Cost" class="w-100 h-100">
                <x-slot name="icon">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon text-danger" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M17 4l3 3" /><path d="M18.5 5.5l-8.5 8.5" /><path d="M8.5 12.5l-2.5 2.5a2 2 0 1 0 2.828 2.828l2.5 -2.5" /></svg>
                </x-slot>
            </x-metric-card>
        </div>
        <div class="col-md-4">
            <x-metric-card label="Net Profit" value="₹ {{ number_format($profitability['net'], 2) }}" accentText="{{ $profitability['net'] >= 0 ? 'text-success' : 'text-danger' }}" accentBg="{{ $profitability['net'] >= 0 ? 'bg-green-lt' : 'bg-red-lt' }}" subtext="Quarry & Crusher Combined MTD" class="w-100 h-100">
                <x-slot name="icon">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon text-success" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0"></path><path d="M9 12l2 2l4 -4"></path></svg>
                </x-slot>
            </x-metric-card>
        </div>
    </div>
 
    <!-- Projects Stats -->
    <div class="row row-cards mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header border-0 bg-transparent py-3">
                    <h3 class="card-title fw-bold">Projects Overview</h3>
                </div>
                <div class="card-body pt-0">
                    <div class="row g-3">
                        <div class="col-6 col-sm-3">
                            <div class="text-muted small fw-bold text-uppercase mb-1">Total Projects</div>
                            <div class="h2 mb-0 fw-bold">{{ $projectStats['total'] }}</div>
                        </div>
                        <div class="col-6 col-sm-3">
                            <div class="text-muted small fw-bold text-uppercase mb-1">Active Projects</div>
                            <div class="h2 mb-0 fw-bold text-primary">{{ $projectStats['active'] }}</div>
                        </div>
                        <div class="col-6 col-sm-3">
                            <div class="text-muted small fw-bold text-uppercase mb-1">Completed Projects</div>
                            <div class="h2 mb-0 fw-bold text-success">{{ $projectStats['completed'] }}</div>
                        </div>
                        <div class="col-6 col-sm-3">
                            <div class="text-muted small fw-bold text-uppercase mb-1">Pending Projects</div>
                            <div class="h2 mb-0 fw-bold text-warning">{{ $projectStats['pending'] }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
 
    <!-- Charts Section -->
    <div class="row row-cards mb-4">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header border-0 bg-transparent py-3">
                    <div>
                        <h3 class="card-title fw-bold">Weekly Sales</h3>
                        <p class="card-subtitle text-muted small">Sales value vs Material volume (Last 7 Days)</p>
                    </div>
                </div>
                <div class="card-body">
                    <div id="chart-sales-trend" style="min-height: 350px;"></div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header border-0 bg-transparent py-3">
                    <h3 class="card-title fw-bold">Material Types</h3>
                </div>
                <div class="card-body pt-0">
                    <div id="chart-material-distribution" style="min-height: 280px;"></div>
                    <div class="mt-4 pt-3 border-top">
                        <div class="row g-2">
                            <div class="col-6">
                                <div class="text-muted small text-uppercase fw-bold" style="font-size: 0.6rem;">Monthly Diesel</div>
                                <div class="h3 fw-bold mb-0 text-warning">{{ number_format($dieselStats['month_liters'], 0) }} L</div>
                            </div>
                            <div class="col-6">
                                <div class="text-muted small text-uppercase fw-bold" style="font-size: 0.6rem;">Repair Costs</div>
                                <div class="h3 fw-bold mb-0 text-danger">₹ {{ number_format($maintenanceStats['this_month_cost'], 0) }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
 
    <!-- Secondary Stats -->
    <div class="row row-cards mb-4">
        <div class="col-md-4">
            <div class="row row-cards">
                <div class="col-12">
                    <div class="card bg-primary-lt border-0 shadow-sm">
                        <div class="card-body p-4 d-flex align-items-center">
                            <div class="icon-box bg-white text-primary rounded-3 me-3" style="width: 48px; height: 48px; display: flex; align-items: center; justify-content: center;">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0"></path><path d="M12 10m-3 0a3 3 0 1 0 6 0a3 3 0 1 0 -6 0"></path><path d="M6.168 18.849a4 4 0 0 1 3.832 -2.849h4a4 4 0 0 1 3.834 2.855"></path></svg>
                            </div>
                            <div>
                                <div class="text-muted small fw-bold text-uppercase" style="font-size: 0.65rem;">Total Clients</div>
                                <div class="h2 mb-0 fw-bold">{{ $totalClients }} Active</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="card bg-azure-lt border-0 shadow-sm">
                        <div class="card-body p-4 d-flex align-items-center">
                            <div class="icon-box bg-white text-azure rounded-3 me-3" style="width: 48px; height: 48px; display: flex; align-items: center; justify-content: center;">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M7 17h10"></path><path d="M12 13h5"></path><path d="M15 17h2"></path><path d="M3 17h2"></path><path d="M3 13h2"></path></svg>
                            </div>
                            <div>
                                <div class="text-muted small fw-bold text-uppercase" style="font-size: 0.65rem;">Total Vehicles</div>
                                <div class="h2 mb-0 fw-bold">{{ $vehicleStats['active'] }} Works</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header border-0 bg-transparent py-3">
                            <h3 class="card-title fw-bold">Diesel Trend</h3>
                        </div>
                        <div class="card-body pt-0">
                            <div id="chart-diesel-trend" style="height: 120px;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
 
        <!-- Recent Projects -->
        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header border-0 bg-transparent py-3 d-flex justify-content-between align-items-center">
                    <h3 class="card-title fw-bold">Recent Projects</h3>
                    <a href="{{ route('projects.index') }}" class="btn btn-sm btn-light">View All</a>
                </div>
                <div class="table-responsive">
                    <table class="table table-vcenter table-premium card-table border-0">
                        <thead>
                            <tr>
                                <th>Project Name</th>
                                <th>Location</th>
                                <th>Progress</th>
                                <th class="text-center">Active</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentProjects as $project)
                                <tr>
                                    <td>
                                        <div class="fw-bold">{{ $project->name }}</div>
                                        <div class="small text-muted">{{ $project->client->name ?? 'Self' }}</div>
                                    </td>
                                    <td>{{ $project->location ?? '-' }}</td>
                                    <td>
                                        <div class="d-flex align-items-center" style="min-width: 120px;">
                                            <div class="progress flex-fill me-2" style="height: 6px;">
                                                <div class="progress-bar bg-primary" style="width: {{ $project->progress ?? 0 }}%"></div>
                                            </div>
                                            <span class="small fw-bold">{{ $project->progress ?? 0 }}%</span>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <span class="status-indicator-dot {{ $project->status == 'active' ? 'dot-online' : 'bg-secondary' }}"></span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-5 text-muted">No projects found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
 
    <!-- Monthly P&L Snapshot -->
    <div class="row row-cards mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header border-0 bg-transparent py-3 d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="card-title fw-bold">Monthly P&L Snapshot</h3>
                        <p class="card-subtitle text-muted small">Quarry vs Crusher Month-to-Date breakdown by categories</p>
                    </div>
                    <a href="{{ route('reports.operational-profit-loss') }}" class="btn btn-sm btn-light">View Full Report</a>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <!-- Quarry Unit -->
                        <div class="col-md-6 border-end">
                            <h4 class="fw-bold mb-3 text-uppercase small text-muted" style="letter-spacing: 1px;">Quarry Unit (QRY)</h4>
                            @if($monthlyPnlSnapshot['quarry'] && ($monthlyPnlSnapshot['quarry']['total_revenue'] > 0 || $monthlyPnlSnapshot['quarry']['total_expense'] > 0))
                                <div class="table-responsive">
                                    <table class="table table-vcenter card-table table-striped table-bordered border-0">
                                        <thead>
                                            <tr>
                                                <th>Category</th>
                                                <th>Type</th>
                                                <th class="text-end">Amount</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($monthlyPnlSnapshot['quarry']['revenues'] as $rev)
                                                <tr>
                                                    <td>{{ $rev->name }}</td>
                                                    <td><span class="badge bg-success-lt">Revenue</span></td>
                                                    <td class="text-end text-success fw-bold">₹ {{ number_format($rev->amount, 2) }}</td>
                                                </tr>
                                            @endforeach
                                            @foreach($monthlyPnlSnapshot['quarry']['expenses'] as $exp)
                                                <tr>
                                                    <td>{{ $exp->name }}</td>
                                                    <td><span class="badge bg-danger-lt">Expense</span></td>
                                                    <td class="text-end text-danger">₹ {{ number_format($exp->amount, 2) }}</td>
                                                </tr>
                                            @endforeach
                                            <tr class="bg-light-subtle">
                                                <td colspan="2" class="fw-bold">Total Revenue / Expense</td>
                                                <td class="text-end fw-bold">
                                                    <span class="text-success">₹ {{ number_format($monthlyPnlSnapshot['quarry']['total_revenue'], 2) }}</span> / 
                                                    <span class="text-danger">₹ {{ number_format($monthlyPnlSnapshot['quarry']['total_expense'], 2) }}</span>
                                                </td>
                                            </tr>
                                            <tr class="table-info">
                                                <td colspan="2" class="fw-bold">Quarry Net Profit/Loss</td>
                                                <td class="text-end fw-bold text-primary">₹ {{ number_format($monthlyPnlSnapshot['quarry']['net'], 2) }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="text-center py-5 text-muted small border rounded-3 bg-light-subtle">No Quarry operational data logged this month.</div>
                            @endif
                        </div>
 
                        <!-- Crusher Unit -->
                        <div class="col-md-6">
                            <h4 class="fw-bold mb-3 text-uppercase small text-muted" style="letter-spacing: 1px;">Crusher Unit (CRS)</h4>
                            @if($monthlyPnlSnapshot['crusher'] && ($monthlyPnlSnapshot['crusher']['total_revenue'] > 0 || $monthlyPnlSnapshot['crusher']['total_expense'] > 0))
                                <div class="table-responsive">
                                    <table class="table table-vcenter card-table table-striped table-bordered border-0">
                                        <thead>
                                            <tr>
                                                <th>Category</th>
                                                <th>Type</th>
                                                <th class="text-end">Amount</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($monthlyPnlSnapshot['crusher']['revenues'] as $rev)
                                                <tr>
                                                    <td>{{ $rev->name }}</td>
                                                    <td><span class="badge bg-success-lt">Revenue</span></td>
                                                    <td class="text-end text-success fw-bold">₹ {{ number_format($rev->amount, 2) }}</td>
                                                </tr>
                                            @endforeach
                                            @foreach($monthlyPnlSnapshot['crusher']['expenses'] as $exp)
                                                <tr>
                                                    <td>{{ $exp->name }}</td>
                                                    <td><span class="badge bg-danger-lt">Expense</span></td>
                                                    <td class="text-end text-danger">₹ {{ number_format($exp->amount, 2) }}</td>
                                                </tr>
                                            @endforeach
                                            <tr class="bg-light-subtle">
                                                <td colspan="2" class="fw-bold">Total Revenue / Expense</td>
                                                <td class="text-end fw-bold">
                                                    <span class="text-success">₹ {{ number_format($monthlyPnlSnapshot['crusher']['total_revenue'], 2) }}</span> / 
                                                    <span class="text-danger">₹ {{ number_format($monthlyPnlSnapshot['crusher']['total_expense'], 2) }}</span>
                                                </td>
                                            </tr>
                                            <tr class="table-info">
                                                <td colspan="2" class="fw-bold">Crusher Net Profit/Loss</td>
                                                <td class="text-end fw-bold text-primary">₹ {{ number_format($monthlyPnlSnapshot['crusher']['net'], 2) }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="text-center py-5 text-muted small border rounded-3 bg-light-subtle">No Crusher operational data logged this month.</div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
 
    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
        <script>
            document.addEventListener("DOMContentLoaded", function () {
                const dates = {!! $chartData['dates'] !!}.reverse();
 
                // Set Global Apex options
                window.Apex = {
                    chart: {
                        fontFamily: 'Inter, sans-serif',
                        toolbar: { show: false },
                        animations: { speed: 400 }
                    },
                    tooltip: { theme: 'dark' }
                };
 
                // 1. Sales & Operations Trend
                new ApexCharts(document.getElementById('chart-sales-trend'), {
                    series: [{
                        name: "Revenue (₹)",
                        type: "column",
                        data: {!! $chartData['revenue'] !!}.reverse()
                    }, {
                        name: "Volume (CFT)",
                        type: "area",
                        data: {!! $chartData['tonnage'] !!}.reverse()
                    }],
                    chart: { height: 350, type: "line", stacked: false },
                    stroke: { width: [0, 3], curve: 'smooth', dashArray: [0, 0] },
                    colors: ['#0f172a', '#3b82f6'],
                    fill: {
                        opacity: [1, 0.15],
                        type: ['solid', 'gradient'],
                        gradient: { shadeIntensity: 1, opacityFrom: 0.5, opacityTo: 0.1, stops: [0, 100] }
                    },
                    dataLabels: { enabled: false },
                    xaxis: { categories: dates },
                    yaxis: [
                        { title: { text: "Revenue" }, labels: { formatter: (v) => "₹"+(v/1000).toFixed(0)+"k" } },
                        { opposite: true, title: { text: "Volume (CFT)" } }
                    ],
                    legend: { position: 'top', horizontalAlign: 'left' },
                    grid: { borderColor: '#f1f5f9' }
                }).render();
 
                // 2. Material Mix
                let materialNames = {!! $chartData['material_names'] !!};
                let materialCounts = {!! $chartData['material_counts'] !!};
 
                if (materialCounts.length > 0) {
                    new ApexCharts(document.getElementById('chart-material-distribution'), {
                        series: materialCounts,
                        chart: { type: "donut", height: 280 },
                        labels: materialNames,
                        colors: ['#0f172a', '#1e293b', '#3b82f6', '#60a5fa', '#93c5fd'],
                        legend: { position: 'bottom' },
                        plotOptions: { pie: { donut: { size: '75%', labels: { show: true, total: { show: true, label: 'TOTAL' } } } } }
                    }).render();
                }
 
                // 3. Mini Diesel Trend
                new ApexCharts(document.getElementById('chart-diesel-trend'), {
                    series: [{ name: "Diesel (L)", data: {!! $chartData['diesel'] !!}.reverse() }],
                    chart: { type: "area", height: 120, sparkline: { enabled: true } },
                    stroke: { width: 3, curve: 'smooth' },
                    colors: ['#f59f00'],
                    fill: { type: 'gradient', gradient: { opacityFrom: 0.3, opacityTo: 0 } },
                    xaxis: { categories: dates }
                }).render();
            });
        </script>
    @endpush
</x-tabler-layout>
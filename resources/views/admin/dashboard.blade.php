<x-tabler-layout>
    <x-slot name="header">
        <h2 class="page-title">
            {{ __('Admin Dashboard') }}
        </h2>
        <div class="page-subtitle">
            <div class="row align-items-center">
                <div class="col-auto">
                    Welcome back, {{ Auth::user()->name }}
                </div>
            </div>
        </div>
    </x-slot>

    <!-- System Health -->
    <div class="row row-deck row-cards mb-3">
        <div class="col-12">
            <x-card>
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="subheader">System Status</div>
                        <div class="d-flex align-items-center mt-2">
                            <span
                                class="status-indicator {{ $systemHealth['database'] == 'Online' ? 'status-green' : 'status-red' }} me-2">
                                <span class="status-indicator-circle"></span>
                                <span class="status-indicator-animated"></span>
                            </span>
                            <span class="fw-bold me-3">Database: {{ $systemHealth['database'] }}</span>

                            <span class="mx-2 text-muted">|</span>

                            <span class="me-3">Disk Usage: <strong>{{ $systemHealth['disk_free'] }}</strong> free of
                                {{ $systemHealth['disk_total'] }}</span>

                            <span class="mx-2 text-muted">|</span>

                            <span class="text-muted">Server Time: {{ $systemHealth['server_time'] }}</span>
                        </div>
                    </div>
                </div>
            </x-card>
        </div>
    </div>


    <!-- Daily Sales Stats -->
    <div class="row row-deck row-cards mb-3">
        <div class="col-sm-6 col-lg-4">
            <x-card>
                <div class="subheader">Today's Loads</div>
                <div class="h1 mb-3">{{ $dailyStats['loads'] }}</div>
                <div class="d-flex mb-2">
                    <div>Vehicle Trips</div>
                </div>
            </x-card>
        </div>
        <div class="col-sm-6 col-lg-4">
            <x-card>
                <div class="subheader">Today's Volume</div>
                <div class="h1 mb-3 text-primary">{{ number_format($dailyStats['tonnage'], 2) }} CFT</div>
                <div class="d-flex mb-2">
                    <div>Material Out</div>
                </div>
            </x-card>
        </div>
        <div class="col-sm-6 col-lg-4">
            <x-card>
                <div class="subheader">Today's Sales Value</div>
                <div class="h1 mb-3 text-success">₹ {{ number_format($dailyStats['amount'], 2) }}</div>
                <div class="d-flex mb-2">
                    <div>Est. Revenue</div>
                </div>
            </x-card>
        </div>
    </div>

    <!-- Fleet Operations -->
    <div class="row row-deck row-cards mb-3">
        <div class="col-md-6">
            <x-card>
                <x-slot name="header">
                    <h3 class="card-title">Diesel Consumption</h3>
                    <div class="card-actions">
                        <a href="{{ route('diesel.index') }}" class="btn btn-sm btn-outline-primary">View Register</a>
                    </div>
                </x-slot>
                <div class="row row-cards p-3">
                    <div class="col-6 text-center border-end">
                        <div class="subheader">Today (L)</div>
                        <div class="h2 mb-0">{{ number_format($dieselStats['today_liters'], 1) }}</div>
                    </div>
                    <div class="col-6 text-center">
                        <div class="subheader">This Month (L)</div>
                        <div class="h2 mb-0">{{ number_format($dieselStats['month_liters'], 1) }}</div>
                    </div>
                </div>
            </x-card>
        </div>
        <div class="col-md-6">
            <x-card>
                <x-slot name="header">
                    <h3 class="card-title">Maintenance Costs</h3>
                    <div class="card-actions">
                        <a href="{{ route('vehicle-maintenance.index') }}" class="btn btn-sm btn-outline-primary">Log
                            Maintenance</a>
                    </div>
                </x-slot>
                <div class="row row-cards p-3">
                    <div class="col-6 text-center border-end">
                        <div class="subheader">This Month</div>
                        <div class="h2 mb-0 text-danger">₹ {{ number_format($maintenanceStats['this_month_cost'], 0) }}
                        </div>
                    </div>
                    <div class="col-6 text-center">
                        <div class="subheader">Under Maintenance</div>
                        <div class="h2 mb-0 text-warning">
                            {{ \App\Models\Vehicle::where('operational_status', 'Under Maintenance')->count() }}
                        </div>
                    </div>
                </div>
            </x-card>
        </div>
    </div>

    <!-- Resources Stats Row -->
    <div class="row row-deck row-cards mb-3">
        <div class="col-sm-6 col-lg-3">
            <x-card>
                <div class="subheader">Total Clients</div>
                <div class="h1 mb-3">{{ $totalClients }}</div>
                <div class="d-flex mb-2">
                    <div>Active partners</div>
                </div>
            </x-card>
        </div>
        <div class="col-sm-6 col-lg-3">
            <x-card>
                <div class="subheader">Total Vehicles</div>
                <div class="h1 mb-3">{{ $vehicleStats['total'] }}</div>
                <div class="d-flex mb-2">
                    <div>Fleet size</div>
                </div>
            </x-card>
        </div>
        <div class="col-sm-6 col-lg-3">
            <x-card>
                <div class="subheader">Total Projects</div>
                <div class="h1 mb-3">{{ $projectStats['total'] }}</div>
                <div class="d-flex mb-2">
                    <div>All projects</div>
                </div>
            </x-card>
        </div>
        <div class="col-sm-6 col-lg-3">
            <x-card>
                <div class="subheader">Active Projects</div>
                <div class="h1 mb-3 text-primary">{{ $projectStats['active'] }}</div>
                <div class="d-flex mb-2">
                    <div>In progress</div>
                </div>
            </x-card>
        </div>
    </div>

    <!-- Analytics Charts Row -->
    <div class="row row-deck row-cards mb-3">
        <!-- Sales & Revenue Trend Chart -->
        <div class="col-lg-8">
            <div class="card shadow-sm border-0">
                <div class="card-header border-bottom bg-white py-3">
                    <h3 class="card-title fw-bold">7-Day Sales & Revenue Trend</h3>
                </div>
                <div class="card-body">
                    <div id="chart-sales-trend" class="chart-lg"></div>
                </div>
            </div>
        </div>

        <!-- Material Distribution Pie Chart -->
        <div class="col-lg-4">
            <div class="card shadow-sm border-0">
                <div class="card-header border-bottom bg-white py-3">
                    <h3 class="card-title fw-bold">Material Overview</h3>
                </div>
                <div class="card-body d-flex flex-column align-items-center justify-content-center">
                    <div id="chart-material-distribution" class="chart-lg w-100"></div>
                </div>
            </div>
        </div>

        <!-- Diesel Consumption Bar Chart -->
        <div class="col-lg-12">
            <div class="card shadow-sm border-0">
                <div class="card-header border-bottom bg-white py-3">
                    <h3 class="card-title fw-bold">Weekly Diesel Consumption (Liters)</h3>
                </div>
                <div class="card-body">
                    <div id="chart-diesel-trend" class="chart-lg"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Projects -->
    <div class="row row-deck row-cards mb-3">
        <div class="col-12">
            <x-card>
                <x-slot name="header">Recent Projects</x-slot>
                <x-slot name="actions">
                    <a href="{{ route('projects.index') }}" class="btn btn-sm btn-primary">View All</a>
                </x-slot>

                <x-table>
                    <thead>
                        <tr>
                            <th>Project</th>
                            <th>Client</th>
                            <th>Location</th>
                            <th>Progress</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentProjects as $project)
                            <tr>
                                <td><strong>{{ $project->name }}</strong></td>
                                <td>{{ $project->client->name ?? ($project->is_internal ? 'Internal' : '-') }}</td>
                                <td>{{ $project->location ?? '-' }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="progress flex-fill" style="height: 6px; width: 100px;">
                                            <div class="progress-bar" style="width: {{ $project->progress ?? 0 }}%"></div>
                                        </div>
                                        <span class="ms-2 text-muted small">{{ $project->progress ?? 0 }}%</span>
                                    </div>
                                </td>
                                <td>
                                    <span
                                        class="badge bg-{{ match ($project->status) { 'pending' => 'secondary', 'active' => 'primary', 'completed' => 'success', 'cancelled' => 'danger', default => 'secondary'} }}">
                                        {{ ucfirst($project->status) }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">No projects yet</td>
                            </tr>
                        @endforelse
                    </tbody>
                </x-table>
            </x-card>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
        <script>
            document.addEventListener("DOMContentLoaded", function () {
                let dates = {!! $chartData['dates'] !!}.reverse();

                // 1. Sales Trend Chart (Mixed: Line & Column)
                window.ApexCharts && (new ApexCharts(document.getElementById('chart-sales-trend'), {
                    chart: {
                        type: "line",
                        fontFamily: 'inherit',
                        height: 320,
                        parentHeightOffset: 0,
                        toolbar: { show: false },
                        animations: { enabled: true },
                    },
                    series: [{
                        name: "Revenue (₹)",
                        type: "column",
                        data: {!! $chartData['revenue'] !!}.reverse()
                    }, {
                        name: "Volume (CFT)",
                        type: "line",
                        data: {!! $chartData['tonnage'] !!}.reverse()
                    }],
                    colors: ['#206bc4', '#2fb344'],
                    dataLabels: { enabled: false },
                    stroke: { width: [0, 3], curve: 'smooth' },
                    xaxis: { categories: dates, tooltip: { enabled: false } },
                    yaxis: [
                        { title: { text: "Revenue (₹)" }, labels: { formatter: (val) => "₹ " + val } },
                        { opposite: true, title: { text: "Volume (CFT)" } }
                    ],
                    legend: { position: 'top', horizontalAlign: 'right' }
                })).render();

                // 2. Material Distribution Donut Chart
                let materialNames = {!! $chartData['material_names'] !!};
                let materialCounts = {!! $chartData['material_counts'] !!};

                if (materialCounts.length > 0) {
                    window.ApexCharts && (new ApexCharts(document.getElementById('chart-material-distribution'), {
                        chart: { type: "donut", fontFamily: 'inherit', height: 320, sparkline: { enabled: true } },
                        series: materialCounts,
                        labels: materialNames,
                        colors: ['#206bc4', '#4299e1', '#66b3ff', '#99ccff', '#cce6ff'],
                        legend: { show: true, position: 'bottom' },
                        tooltip: { fillSeriesColor: false },
                        plotOptions: { pie: { donut: { size: '65%' } } }
                    })).render();
                } else {
                    document.getElementById('chart-material-distribution').innerHTML = '<div class="text-muted text-center py-5">No material data available yet.</div>';
                }

                // 3. Diesel Trend Area Chart
                window.ApexCharts && (new ApexCharts(document.getElementById('chart-diesel-trend'), {
                    chart: {
                        type: "area",
                        fontFamily: 'inherit',
                        height: 240,
                        parentHeightOffset: 0,
                        toolbar: { show: false },
                    },
                    series: [{
                        name: "Diesel (L)",
                        data: {!! $chartData['diesel'] !!}.reverse()
                    }],
                    fill: {
                        type: 'gradient',
                        gradient: { shadeIntensity: 1, opacityFrom: 0.7, opacityTo: 0.1, stops: [0, 100] }
                    },
                    colors: ['#f59f00'],
                    dataLabels: { enabled: false },
                    stroke: { width: 3, curve: 'smooth' },
                    xaxis: { categories: dates, tooltip: { enabled: false } },
                    yaxis: { title: { text: "Liters" } },
                    legend: { show: false }
                })).render();
            });
        </script>
    @endpush
</x-tabler-layout>
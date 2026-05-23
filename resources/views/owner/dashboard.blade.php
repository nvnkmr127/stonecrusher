<x-tabler-layout>
    <x-slot name="header">
        <div class="row g-2 align-items-center d-print-none">
            <div class="col">
                <div class="page-pretitle text-uppercase fw-bold text-muted" style="letter-spacing: 0.05em;">Financial Intelligence</div>
                <h2 class="page-title h1 fw-bold">Owner Dashboard</h2>
            </div>
            <div class="col-auto ms-auto">
                <div class="btn-list">
                    <span class="d-none d-md-inline-block text-muted me-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-clock me-1" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0"></path><path d="M12 7l0 5l3 3"></path></svg>
                        Last updated: {{ $last_updated }}
                    </span>
                    <a href="{{ route('owner.dashboard', ['force_refresh' => 1]) }}" class="btn btn-white shadow-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-refresh" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M20 11a8.1 8.1 0 0 0 -15.5 -2m-.5 -5v5h5"></path><path d="M4 13a8.1 8.1 0 0 0 15.5 2m.5 5v-5h-5"></path></svg>
                        Force Refresh
                    </a>
                </div>
            </div>
        </div>
    </x-slot>

    <!-- Welcome and Date banner -->
    <div class="premium-header-card mb-4" style="background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);">
        <div class="row align-items-center">
            <div class="col-md-7">
                <h1 class="text-white fw-bold mb-2">Executive Summary</h1>
                <p class="text-white-50 mb-0">Overview of combined Crusher and Quarry cashflows, receivables outstanding, and operational performance.</p>
            </div>
            <div class="col-md-5 text-md-end mt-4 mt-md-0">
                <div class="d-inline-block text-start p-3 rounded-3" style="background: rgba(255,255,255,0.05); backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.1);">
                    <div class="text-white-50 small text-uppercase fw-bold mb-1" style="letter-spacing: 1px;">Today's Date</div>
                    <div class="h2 text-white mb-0 fw-bold">{{ now()->format('l, d M Y') }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Outstanding Receivables Overview (Highlight) -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm" style="border-left: 4px solid #ef4444 !important;">
                <div class="card-body py-4 d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <div>
                        <div class="text-muted small text-uppercase fw-bold mb-1" style="letter-spacing: 1px;">Outstanding Accounts Receivables</div>
                        <h2 class="mb-0 fw-bold text-red" style="font-size: 2rem;">₹ {{ number_format($outstanding, 2) }}</h2>
                        <div class="text-muted small mt-1">Total pending balances across active clients (excludes client advances).</div>
                    </div>
                    <div>
                        <a href="{{ route('reports.outstanding') }}" class="btn btn-red px-4 shadow-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-file-report" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><circle cx="17" cy="17" r="4"></circle><path d="M17 13v4h4"></path><path d="M12 3v4a1 1 0 0 0 1 1h4"></path><path d="M11.5 21h-6.5a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v2.5"></path></svg>
                            View Aging Ledger
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Performance Tabs Grid (MTD vs Today) -->
    <div class="row mb-4">
        <!-- Today's Operations Grid -->
        <div class="col-lg-6 mb-4 mb-lg-0">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h3 class="mb-0 fw-bold d-flex align-items-center text-secondary">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-bolt me-2 text-warning" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><polyline points="13 3 13 10 19 10 11 21 11 14 5 14 13 3"></polyline></svg>
                    Today's Operations
                </h3>
            </div>
            <div class="row g-3">
                <div class="col-sm-6">
                    <x-metric-card label="Today's Sales" value="₹ {{ number_format($today['sales'], 2) }}" accentText="text-green" accentBg="bg-green-lt">
                        <x-slot name="icon">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon text-green" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><circle cx="12" cy="12" r="9" /><path d="M14.8 9a2 2 0 0 0 -1.8 -1h-2a2 2 0 0 0 0 4h2a2 2 0 0 1 0 4h-2a2 2 0 0 1 -1.8 -1" /><path d="M12 6v2m0 8v2" /></svg>
                        </x-slot>
                    </x-metric-card>
                </div>
                <div class="col-sm-6">
                    <x-metric-card label="Today's Net Profit" value="₹ {{ number_format($today['net_profit'], 2) }}" accentText="{{ $today['net_profit'] >= 0 ? 'text-success' : 'text-danger' }}" accentBg="{{ $today['net_profit'] >= 0 ? 'bg-green-lt' : 'bg-red-lt' }}">
                        <x-slot name="icon">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon text-success" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><polyline points="9 11 12 14 20 6"></polyline><path d="M20 12v6a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2h9"></path></svg>
                        </x-slot>
                    </x-metric-card>
                </div>
                <div class="col-sm-6">
                    <x-metric-card label="Today's Crusher Expense" value="₹ {{ number_format($today['crusher_expense'], 2) }}" accentText="text-blue" accentBg="bg-blue-lt">
                        <x-slot name="icon">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon text-blue" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M17 17m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" /><path d="M17 17h2v-4m-1 -8h11v12m-4 0h6m4 0h2v-6h-8m0 -5h5l3 5" /></svg>
                        </x-slot>
                    </x-metric-card>
                </div>
                <div class="col-sm-6">
                    <x-metric-card label="Today's Quarry Expense" value="₹ {{ number_format($today['quarry_expense'], 2) }}" accentText="text-orange" accentBg="bg-orange-lt">
                        <x-slot name="icon">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon text-orange" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 3l8 4.5l0 9l-8 4.5l-8 -4.5l0 -9l8 -4.5" /></svg>
                        </x-slot>
                    </x-metric-card>
                </div>
                <div class="col-sm-6">
                    <x-metric-card label="Today's Trips" value="{{ $today['trips'] }} <span class='fs-6 text-muted fw-normal'>LOADS</span>" accentText="text-azure" accentBg="bg-azure-lt">
                        <x-slot name="icon">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon text-azure" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><circle cx="7" cy="17" r="2" /><circle cx="17" cy="17" r="2" /><path d="M5 17h-2v-11a1 1 0 0 1 1 -1h9v12m-4 0h6m4 0h2v-6h-8m0 -5h5l3 5" /></svg>
                        </x-slot>
                    </x-metric-card>
                </div>
                <div class="col-sm-6">
                    <x-metric-card label="Today's Diesel Issued" value="{{ number_format($today['diesel_liters'], 1) }} <span class='fs-6 text-muted fw-normal'>LTRS</span>" accentText="text-purple" accentBg="bg-purple-lt">
                        <x-slot name="icon">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon text-purple" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M14 11h1a2 2 0 0 1 2 2v3a1.5 1.5 0 0 0 3 0v-7l-3 -3"></path><path d="M4 20v-14a2 2 0 0 1 2 -2h6a2 2 0 0 1 2 2v14"></path></svg>
                        </x-slot>
                    </x-metric-card>
                </div>
            </div>
        </div>

        <!-- Month-to-Date Operations Grid -->
        <div class="col-lg-6">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h3 class="mb-0 fw-bold d-flex align-items-center text-secondary">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-calendar me-2 text-primary" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><rect x="4" y="4" width="16" height="16" rx="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="4" y1="10" x2="20" y2="10"></line><line x1="10" y1="14" x2="14" y2="14"></line><line x1="10" y1="18" x2="14" y2="18"></line></svg>
                    Month-to-Date (MTD)
                </h3>
            </div>
            <div class="row g-3">
                <div class="col-sm-6">
                    <x-metric-card label="MTD Sales" value="₹ {{ number_format($mtd['sales'], 2) }}" accentText="text-green" accentBg="bg-green-lt">
                        <x-slot name="icon">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon text-green" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><circle cx="12" cy="12" r="9" /><path d="M14.8 9a2 2 0 0 0 -1.8 -1h-2a2 2 0 0 0 0 4h2a2 2 0 0 1 0 4h-2a2 2 0 0 1 -1.8 -1" /><path d="M12 6v2m0 8v2" /></svg>
                        </x-slot>
                    </x-metric-card>
                </div>
                <div class="col-sm-6">
                    <x-metric-card label="MTD Net Profit" value="₹ {{ number_format($mtd['net_profit'], 2) }}" accentText="{{ $mtd['net_profit'] >= 0 ? 'text-success' : 'text-danger' }}" accentBg="{{ $mtd['net_profit'] >= 0 ? 'bg-green-lt' : 'bg-red-lt' }}">
                        <x-slot name="icon">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon text-success" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><polyline points="9 11 12 14 20 6"></polyline><path d="M20 12v6a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2h9"></path></svg>
                        </x-slot>
                    </x-metric-card>
                </div>
                <div class="col-sm-6">
                    <x-metric-card label="MTD Crusher Expense" value="₹ {{ number_format($mtd['crusher_expense'], 2) }}" accentText="text-blue" accentBg="bg-blue-lt">
                        <x-slot name="icon">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon text-blue" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M17 17m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" /><path d="M17 17h2v-4m-1 -8h11v12m-4 0h6m4 0h2v-6h-8m0 -5h5l3 5" /></svg>
                        </x-slot>
                    </x-metric-card>
                </div>
                <div class="col-sm-6">
                    <x-metric-card label="MTD Quarry Expense" value="₹ {{ number_format($mtd['quarry_expense'], 2) }}" accentText="text-orange" accentBg="bg-orange-lt">
                        <x-slot name="icon">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon text-orange" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 3l8 4.5l0 9l-8 4.5l-8 -4.5l0 -9l8 -4.5" /></svg>
                        </x-slot>
                    </x-metric-card>
                </div>
                <div class="col-sm-6">
                    <x-metric-card label="MTD Trips" value="{{ $mtd['trips'] }} <span class='fs-6 text-muted fw-normal'>LOADS</span>" accentText="text-azure" accentBg="bg-azure-lt">
                        <x-slot name="icon">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon text-azure" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><circle cx="7" cy="17" r="2" /><circle cx="17" cy="17" r="2" /><path d="M5 17h-2v-11a1 1 0 0 1 1 -1h9v12m-4 0h6m4 0h2v-6h-8m0 -5h5l3 5" /></svg>
                        </x-slot>
                    </x-metric-card>
                </div>
                <div class="col-sm-6">
                    <x-metric-card label="MTD Diesel Issued" value="{{ number_format($mtd['diesel_liters'], 1) }} <span class='fs-6 text-muted fw-normal'>LTRS</span>" accentText="text-purple" accentBg="bg-purple-lt">
                        <x-slot name="icon">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon text-purple" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M14 11h1a2 2 0 0 1 2 2v3a1.5 1.5 0 0 0 3 0v-7l-3 -3"></path><path d="M4 20v-14a2 2 0 0 1 2 -2h6a2 2 0 0 1 2 2v14"></path></svg>
                        </x-slot>
                    </x-metric-card>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row row-cards mb-4">
        <!-- YTD P&L Trend -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header border-0 bg-transparent">
                    <h3 class="card-title fw-bold">Profitability Trend (Last 6 Months)</h3>
                </div>
                <div class="card-body">
                    <div id="chart-ytd-pl" style="min-height: 320px;"></div>
                </div>
            </div>
        </div>

        <!-- MTD Expense split Donut -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header border-0 bg-transparent">
                    <h3 class="card-title fw-bold">Expense Distribution (MTD)</h3>
                </div>
                <div class="card-body d-flex align-items-center justify-content-center">
                    <div id="chart-expense-donut" style="min-height: 280px; width: 100%;"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- 15-day Sales & Fuel Trend -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header border-0 bg-transparent">
                    <h3 class="card-title fw-bold">Daily Sales & Fuel Consumption Trend (Last 15 Days)</h3>
                </div>
                <div class="card-body">
                    <div id="chart-daily-ops" style="min-height: 280px;"></div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
        <script>
            document.addEventListener("DOMContentLoaded", function () {
                // Set Global Apex options
                window.Apex = {
                    chart: {
                        fontFamily: 'Inter, sans-serif',
                        toolbar: { show: false },
                        animations: { speed: 400 }
                    },
                    tooltip: { theme: 'dark' }
                };

                // 1. YTD P&L Trend Chart
                const ytdMonths = {!! json_encode($charts['ytd']['months']) !!};
                const ytdSales = {!! json_encode($charts['ytd']['sales']) !!};
                const ytdExpenses = {!! json_encode($charts['ytd']['expenses']) !!};
                const ytdNetProfit = {!! json_encode($charts['ytd']['net_profit']) !!};

                new ApexCharts(document.getElementById('chart-ytd-pl'), {
                    series: [
                        { name: "Sales (₹)", type: "column", data: ytdSales },
                        { name: "Expenses (₹)", type: "column", data: ytdExpenses },
                        { name: "Net Profit (₹)", type: "line", data: ytdNetProfit }
                    ],
                    chart: { height: 320, type: "line", stacked: false },
                    stroke: { width: [0, 0, 3], curve: 'smooth' },
                    colors: ['#2fb344', '#d63939', '#206bc4'],
                    dataLabels: { enabled: false },
                    xaxis: { categories: ytdMonths },
                    yaxis: {
                        labels: {
                            formatter: (v) => "₹" + (v/1000).toFixed(0) + "k"
                        }
                    },
                    legend: { position: 'top', horizontalAlign: 'left' },
                    grid: { borderColor: '#f1f5f9' }
                }).render();

                // 2. MTD Expense Distribution Donut Chart
                const donutLabels = {!! json_encode($charts['donut']['labels']) !!};
                const donutValues = {!! json_encode($charts['donut']['values']) !!};
                const totalExpenses = donutValues.reduce((a, b) => a + b, 0);

                if (totalExpenses > 0) {
                    new ApexCharts(document.getElementById('chart-expense-donut'), {
                        series: donutValues,
                        chart: { type: "donut", height: 280 },
                        labels: donutLabels,
                        colors: ['#206bc4', '#f59f00', '#74b816', '#ae3ec9'],
                        legend: { position: 'bottom' },
                        plotOptions: {
                            pie: {
                                donut: {
                                    size: '72%',
                                    labels: {
                                        show: true,
                                        total: {
                                            show: true,
                                            label: 'TOTAL',
                                            formatter: () => "₹" + (totalExpenses/1000).toFixed(0) + "k"
                                        }
                                    }
                                }
                            }
                        }
                    }).render();
                } else {
                    document.getElementById('chart-expense-donut').innerHTML = '<div class="text-muted small">No expenses logged this month</div>';
                }

                // 3. Daily Sales & Fuel Trend Chart
                const dailyDates = {!! json_encode($charts['daily']['dates']) !!};
                const dailySales = {!! json_encode($charts['daily']['sales']) !!};
                const dailyDiesel = {!! json_encode($charts['daily']['diesel']) !!};

                new ApexCharts(document.getElementById('chart-daily-ops'), {
                    series: [
                        { name: "Daily Sales (₹)", data: dailySales },
                        { name: "Fuel Consumption (L)", data: dailyDiesel }
                    ],
                    chart: { height: 280, type: "area" },
                    stroke: { width: [3, 3], curve: 'smooth' },
                    colors: ['#2fb344', '#f59f00'],
                    fill: { type: 'gradient', gradient: { opacityFrom: 0.25, opacityTo: 0.05 } },
                    xaxis: { categories: dailyDates },
                    yaxis: [
                        { title: { text: "Sales (₹)" }, labels: { formatter: (v) => "₹" + v.toFixed(0) } },
                        { opposite: true, title: { text: "Fuel (L)" } }
                    ],
                    legend: { position: 'top', horizontalAlign: 'left' },
                    grid: { borderColor: '#f1f5f9' }
                }).render();
            });
        </script>
    @endpush
</x-tabler-layout>

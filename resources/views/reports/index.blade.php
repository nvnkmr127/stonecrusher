<x-tabler-layout>
    <x-slot name="header">
        <h2 class="page-title">
            {{ __('Reports Dashboard') }}
        </h2>
    </x-slot>

    <div class="row row-cards">

        <!-- Daily Report -->
        <div class="col-sm-6 col-lg-4">
            <div class="card h-100 hover-shadow-sm transition-all">
                <div class="card-body">
                    <h3 class="card-title">Daily Closing Report</h3>
                    <p class="text-muted">View daily sales, collections, and financial closing status.</p>
                    <a href="{{ route('reports.daily') }}" class="btn btn-primary w-100">View Daily Report &rarr;</a>
                </div>
            </div>
        </div>

        <!-- Monthly Report -->
        <div class="col-sm-6 col-lg-4">
            <div class="card h-100 hover-shadow-sm transition-all">
                <div class="card-body">
                    <h3 class="card-title">Monthly Summary</h3>
                    <p class="text-muted">Consolidated monthly view of sales and collections.</p>
                    <a href="{{ route('reports.monthly') }}" class="btn btn-primary w-100">View Monthly Report
                        &rarr;</a>
                </div>
            </div>
        </div>

        <!-- Outstanding & Advance Report -->
        <div class="col-sm-6 col-lg-4">
            <div class="card h-100 hover-shadow-sm transition-all">
                <div class="card-body">
                    <h3 class="card-title">Outstanding Balances</h3>
                    <p class="text-muted">Track client receivables and advance payments.</p>
                    <a href="{{ route('reports.outstanding') }}" class="btn btn-outline-danger w-100">View Outstanding
                        &rarr;</a>
                </div>
            </div>
        </div>

        <!-- Custom Range Report -->
        <div class="col-sm-6 col-lg-4">
            <div class="card h-100 hover-shadow-sm transition-all">
                <div class="card-body">
                    <h3 class="card-title">Custom Date Range</h3>
                    <p class="text-muted">Detailed sales list for any specific date range.</p>
                    <a href="{{ route('reports.custom') }}" class="btn btn-secondary w-100">View Custom Report
                        &rarr;</a>
                </div>
            </div>
        </div>

        <!-- Metal Wise -->
        <div class="col-sm-6 col-lg-4">
            <div class="card h-100 hover-shadow-sm transition-all">
                <div class="card-body">
                    <h3 class="card-title">Metal-wise Sales</h3>
                    <p class="text-muted">Breakdown of sales by Metal Type.</p>
                    <a href="{{ route('reports.summary', 'metal') }}" class="btn btn-info w-100">View Breakdown
                        &rarr;</a>
                </div>
            </div>
        </div>

        <!-- Client Wise -->
        <div class="col-sm-6 col-lg-4">
            <div class="card h-100 hover-shadow-sm transition-all">
                <div class="card-body">
                    <h3 class="card-title">Client-wise Sales</h3>
                    <p class="text-muted">Top clients and sales volume analysis.</p>
                    <a href="{{ route('reports.summary', 'client') }}" class="btn btn-info w-100">View Client Report
                        &rarr;</a>
                </div>
            </div>
        </div>

        <!-- Distance & Transport Report -->
        <div class="col-sm-6 col-lg-4">
            <div class="card h-100 hover-shadow-sm transition-all">
                <div class="card-body">
                    <h3 class="card-title">Distance & Transport</h3>
                    <p class="text-muted">Transport cost analysis and distance reporting.</p>
                    <a href="{{ route('gate-passes.distance-report') }}" class="btn btn-warning w-100">View Distance
                        Report &rarr;</a>
                </div>
            </div>
        </div>

        <!-- Vehicle Wise -->
        <div class="col-sm-6 col-lg-4">
            <div class="card h-100 hover-shadow-sm transition-all">
                <div class="card-body">
                    <h3 class="card-title">Vehicle Performance</h3>
                    <p class="text-muted">Trip counts and distance covered by vehicles.</p>
                    <a href="{{ route('reports.summary', 'vehicle') }}" class="btn btn-success w-100">View Vehicle
                        Report &rarr;</a>
                </div>
            </div>
        </div>

        <!-- Attendance Report Link -->
        <div class="col-sm-6 col-lg-4">
            <div class="card h-100 hover-shadow-sm transition-all">
                <div class="card-body">
                    <h3 class="card-title">Attendance Report</h3>
                    <p class="text-muted">Employee attendance logs and status.</p>
                    <a href="{{ route('attendance.report') }}" class="btn btn-dark w-100">View Attendance Log &rarr;</a>
                </div>
            </div>
        </div>

    </div>
</x-tabler-layout>
<x-tabler-layout>
    <x-slot name="header">
        <h2 class="page-title">
            {{ __('Distance & Transport Report') }}
        </h2>
    </x-slot>

    <div class="space-y-6">
        <!-- Filters -->
        <x-card>
            <div class="card-body py-3">
                <form method="GET" action="{{ route('gate-passes.distance-report') }}"
                    class="d-flex gap-2 align-items-end">
                    <div>
                        <label class="form-label">Start Date</label>
                        <input type="date" name="start_date" class="form-control" value="{{ $startDate }}">
                    </div>
                    <div>
                        <label class="form-label">End Date</label>
                        <input type="date" name="end_date" class="form-control" value="{{ $endDate }}">
                    </div>
                    <div>
                        <button type="submit" class="btn btn-primary">Filter</button>
                    </div>
                    <div class="ms-auto d-flex gap-2">
                        <a href="{{ route('gate-passes.distance-report.export', ['start_date' => $startDate, 'end_date' => $endDate, 'format' => 'csv']) }}" class="btn btn-success">
                            Export CSV
                        </a>
                        <a href="{{ route('gate-passes.distance-report.export', ['start_date' => $startDate, 'end_date' => $endDate, 'format' => 'pdf']) }}" class="btn btn-danger">
                            Export PDF
                        </a>
                        <button type="button" onclick="window.print()" class="btn btn-secondary">
                            Print Report
                        </button>
                    </div>
                </form>
            </div>
        </x-card>

        <!-- Summary Statistics -->
        <div class="row row-cards">
            <div class="col-sm-6 col-lg-3">
                <div class="card card-sm">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <span class="bg-blue text-white avatar">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-truck"
                                        width="24" height="24" viewBox="0 0 24 24" stroke-width="2"
                                        stroke="currentColor" fill="none" stroke-linecap="round"
                                        stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <circle cx="7" cy="17" r="2" />
                                        <circle cx="17" cy="17" r="2" />
                                        <path d="M5 17h-2v-11a1 1 0 0 1 1 -1h9v12m-4 0h6m4 0h2v-6h-8m0 -5h5l3 5" />
                                    </svg>
                                </span>
                            </div>
                            <div class="col">
                                <div class="font-weight-medium">
                                    {{ $summary['total_trips'] }} Trips
                                </div>
                                <div class="text-muted">
                                    {{ number_format($summary['total_distance'], 2) }} km Total
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="card card-sm">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <span class="bg-green text-white avatar">
                                    <!-- INR Icon or similar -->
                                    <svg xmlns="http://www.w3.org/2000/svg"
                                        class="icon icon-tabler icon-tabler-currency-rupee" width="24" height="24"
                                        viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                        stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M18 5h-11h3a4 4 0 0 1 0 8h-3l6 6" />
                                        <line x1="7" y1="9" x2="18" y2="9" />
                                    </svg>
                                </span>
                            </div>
                            <div class="col">
                                <div class="font-weight-medium">
                                    ₹{{ number_format($summary['total_cost'], 2) }}
                                </div>
                                <div class="text-muted">
                                    Transport Cost
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="card card-sm">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <span class="bg-yellow text-white avatar">
                                    <svg xmlns="http://www.w3.org/2000/svg"
                                        class="icon icon-tabler icon-tabler-chart-line" width="24" height="24"
                                        viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                        stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M4 19l4 -6l4 2l4 -5l4 4" />
                                    </svg>
                                </span>
                            </div>
                            <div class="col">
                                <div class="font-weight-medium">
                                    ₹{{ number_format($summary['avg_cost_per_km'], 2) }} / km
                                </div>
                                <div class="text-muted">
                                    Avg Cost Efficiency
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="card card-sm">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <span class="bg-red text-white avatar">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-scale"
                                        width="24" height="24" viewBox="0 0 24 24" stroke-width="2"
                                        stroke="currentColor" fill="none" stroke-linecap="round"
                                        stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <line x1="7" y1="20" x2="17" y2="20" />
                                        <path d="M6 6l6 -1l6 1" />
                                        <line x1="12" y1="3" x2="12" y2="20" />
                                        <path d="M9 12l-3 -6l-3 6a3 3 0 0 0 6 0" />
                                        <path d="M21 12l-3 -6l-3 6a3 3 0 0 0 6 0" />
                                    </svg>
                                </span>
                            </div>
                            <div class="col">
                                <div class="font-weight-medium">
                                    {{ number_format($summary['cost_to_sales_ratio'], 1) }}%
                                </div>
                                <div class="text-muted">
                                    Cost / Sales Ratio
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row row-cards">
            <!-- Distance Categories -->
            <div class="col-md-6">
                <x-card>
                    <div class="card-header">
                        <h3 class="card-title">Analysis by Distance Range</h3>
                    </div>
                    <div class="table-responsive">
                        <table class="table card-table table-vcenter text-nowrap">
                            <thead>
                                <tr>
                                    <th>Range</th>
                                    <th class="text-center">Trips</th>
                                    <th class="text-end">Avg Cost/KM</th>
                                    <th class="text-end">Total Cost</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($rangeStats as $stat)
                                    <tr>
                                        <td>{{ $stat->range_label }}</td>
                                        <td class="text-center">{{ $stat->count }}</td>
                                        <td class="text-end">₹{{ number_format($stat->avg_cost_per_km, 2) }}</td>
                                        <td class="text-end">₹{{ number_format($stat->total_cost, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center">No data available.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </x-card>
            </div>

            <!-- Location Breakdown -->
            <div class="col-md-6">
                <x-card>
                    <div class="card-header">
                        <h3 class="card-title">Top Locations by Cost</h3>
                    </div>
                    <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                        <table class="table card-table table-vcenter text-nowrap">
                            <thead>
                                <tr>
                                    <th>Location</th>
                                    <th class="text-center">Trips</th>
                                    <th class="text-end">Cost/KM</th>
                                    <th class="text-end">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($reportData->take(10) as $row)
                                    <tr>
                                        <td>{{ $row->delivery_location ?: 'Unknown' }}</td>
                                        <td class="text-center">{{ $row->trip_count }}</td>
                                        <td class="text-end">₹{{ number_format($row->cost_per_km, 2) }}</td>
                                        <td class="text-end fw-bold">₹{{ number_format($row->total_cost, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center">No data.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </x-card>
            </div>
        </div>

        <!-- Detailed Table -->
        <x-card>
            <div class="card-header">
                <h3 class="card-title">Detailed Location Performance</h3>
            </div>
            <div class="table-responsive">
                <table class="table card-table table-vcenter text-nowrap datatable">
                    <thead>
                        <tr>
                            <th>Location</th>
                            <th class="text-center">Trips</th>
                            <th class="text-end">Total Distance</th>
                            <th class="text-end">Total Qty (Tons)</th>
                            <th class="text-end">Cost/KM</th>
                            <th class="text-end">Cost/Ton</th>
                            <th class="text-end">Total Cost</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($reportData as $row)
                            <tr>
                                <td>{{ $row->delivery_location ?: 'Unknown' }}</td>
                                <td class="text-center">{{ $row->trip_count }}</td>
                                <td class="text-end">{{ number_format($row->total_distance, 0) }} km</td>
                                <td class="text-end">{{ number_format($row->total_qty, 0) }}</td>
                                <td class="text-end">₹{{ number_format($row->cost_per_km, 2) }}</td>
                                <td class="text-end">₹{{ number_format($row->cost_per_ton, 2) }}</td>
                                <td class="text-end fw-bold">₹{{ number_format($row->total_cost, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </x-card>
    </div>
</x-tabler-layout>
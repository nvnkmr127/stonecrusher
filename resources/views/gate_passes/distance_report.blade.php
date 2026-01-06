<x-tabler-layout>
    <x-slot name="header">
        <div class="row align-items-center">
            <div class="col">
                <h2 class="page-title">Distance & Transport Report</h2>
                <div class="page-subtitle">Analysis of Transport Usage and Costs</div>
            </div>
            <div class="col-auto">
                <button class="btn btn-primary" onclick="window.print()">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-printer" width="24"
                        height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                        stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path d="M17 17h2a2 2 0 0 0 2 -2v-4a2 2 0 0 0 -2 -2h-14a2 2 0 0 0 -2 2v4a2 2 0 0 0 2 2h2" />
                        <path d="M17 9v-4a2 2 0 0 0 -2 -2h-6a2 2 0 0 0 -2 2v4" />
                        <path d="M7 13m0 2a2 2 0 0 1 2 -2h6a2 2 0 0 1 2 2v4a2 2 0 0 1 -2 2h-6a2 2 0 0 1 -2 -2z" />
                    </svg>
                    Print Report
                </button>
            </div>
        </div>
    </x-slot>

    <div class="row row-cards">
        <!-- Date Filter -->
        <div class="col-12">
            <form action="{{ route('gate-passes.distance-report') }}" method="GET">
                <x-card>
                    <div class="card-body">
                        <div class="row g-2 align-items-end">
                            <div class="col-md-3">
                                <label class="form-label">Start Date</label>
                                <input type="date" class="form-control" name="start_date" value="{{ $startDate }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">End Date</label>
                                <input type="date" class="form-control" name="end_date" value="{{ $endDate }}">
                            </div>
                            <div class="col-auto">
                                <button type="submit" class="btn btn-primary">Filter</button>
                                <a href="{{ route('gate-passes.distance-report') }}" class="btn btn-secondary">Reset</a>
                            </div>
                        </div>
                    </div>
                </x-card>
            </form>
        </div>

        <!-- Summary Cards -->
        <div class="col-sm-6 col-lg-4">
            <div class="card card-sm">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <span class="bg-primary text-white avatar">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-truck"
                                    width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                    fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path d="M7 17m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" />
                                    <path d="M17 17m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" />
                                    <path d="M5 17h-2v-11a1 1 0 0 1 1 -1h9v12m-4 0h6m4 0h2v-6h-8m0 -5h5l3 5" />
                                </svg>
                            </span>
                        </div>
                        <div class="col">
                            <div class="font-weight-medium">
                                {{ number_format($summary['total_trips']) }}
                            </div>
                            <div class="text-secondary">
                                Total Trips Included
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-4">
            <div class="card card-sm">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <span class="bg-green text-white avatar">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-route"
                                    width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                    fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path d="M3 19a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" />
                                    <path d="M19 7a2 2 0 1 0 0 -4a2 2 0 0 0 0 4" />
                                    <path d="M11 19h5.5a3.5 3.5 0 0 0 0 -7h-8a3.5 3.5 0 0 1 0 -7h4.5" />
                                </svg>
                            </span>
                        </div>
                        <div class="col">
                            <div class="font-weight-medium">
                                {{ number_format($summary['total_distance'], 2) }} KM
                            </div>
                            <div class="text-secondary">
                                Total Distance Covered
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-4">
            <div class="card card-sm">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <span class="bg-purple text-white avatar">
                                <svg xmlns="http://www.w3.org/2000/svg"
                                    class="icon icon-tabler icon-tabler-currency-rupee" width="24" height="24"
                                    viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path d="M18 5h-11h3a4 4 0 0 1 0 8h-3l6 6" />
                                    <path d="M7 9l11 0" />
                                </svg>
                            </span>
                        </div>
                        <div class="col">
                            <div class="font-weight-medium">
                                ₹{{ number_format($summary['total_cost'], 2) }}
                            </div>
                            <div class="text-secondary">
                                Total Transport Cost
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detailed Table -->
        <div class="col-12">
            <x-card>
                <x-slot name="header">
                    Location-wise Breakdown
                </x-slot>

                <div class="table-responsive">
                    <table class="table card-table table-vcenter text-nowrap datatable">
                        <thead>
                            <tr>
                                <th>Location</th>
                                <th class="text-center">Trips</th>
                                <th class="text-end">Total Distance (KM)</th>
                                <th class="text-end">Avg. Distance/Trip</th>
                                <th class="text-end">Total Cost (₹)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($reportData as $row)
                                <tr>
                                    <td>{{ $row->delivery_location ?? 'Start (Not Specified)' }}</td>
                                    <td class="text-center">{{ $row->trip_count }}</td>
                                    <td class="text-end">{{ number_format($row->total_distance, 2) }}</td>
                                    <td class="text-end">
                                        {{ $row->trip_count > 0 ? number_format($row->total_distance / $row->trip_count, 2) : '0.00' }}
                                    </td>
                                    <td class="text-end fw-bold">{{ number_format($row->total_cost, 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">
                                        No data found for the selected period.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                        <tfoot>
                            <tr class="fw-bold bg-light">
                                <td>TOTAL</td>
                                <td class="text-center">{{ number_format($summary['total_trips']) }}</td>
                                <td class="text-end">{{ number_format($summary['total_distance'], 2) }}</td>
                                <td class="text-end">-</td>
                                <td class="text-end">{{ number_format($summary['total_cost'], 2) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </x-card>
        </div>
    </div>
</x-tabler-layout>
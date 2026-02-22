<x-tabler-layout>
    <x-slot name="header">
        <div class="row align-items-center">
            <div class="col">
                <h2 class="page-title">Daily Sales Summary</h2>
                <div class="page-subtitle">Report for {{ \Carbon\Carbon::parse($date)->format('d M, Y') }}</div>
            </div>
            <div class="col-auto">
                <form action="{{ route('gate-passes.daily-report') }}" method="GET" class="d-flex gap-2">
                    <input type="date" name="date" class="form-control" value="{{ $date }}"
                        onchange="this.form.submit()">
                    <button type="submit" class="btn btn-white btn-icon" title="Refresh">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24"
                            stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round"
                            stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M20 11a8.1 8.1 0 0 0 -15.5 -2m-.5 -4v4h4" />
                            <path d="M4 13a8.1 8.1 0 0 0 15.5 2m.5 4v-4h-4" />
                        </svg>
                    </button>
                    <button type="button" class="btn btn-primary" onclick="window.print()">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24"
                            stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round"
                            stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M17 17h2a2 2 0 0 0 2 -2v-4a2 2 0 0 0 -2 -2h-14a2 2 0 0 0 -2 2v4a2 2 0 0 0 2 2h2" />
                            <path d="M17 9v-4a2 2 0 0 0 -2 -2h-6a2 2 0 0 0 -2 2v4" />
                            <path d="M7 13m0 2a2 2 0 0 1 2 -2h6a2 2 0 0 1 2 2v4a2 2 0 0 1 -2 2h-6a2 2 0 0 1 -2 -2z" />
                        </svg>
                        Print
                    </button>
                </form>
            </div>
        </div>
    </x-slot>

    <div class="row row-cards">
        <!-- Summary Cards -->
        <div class="col-sm-6 col-lg-3">
            <div class="card card-sm">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <span class="bg-primary text-white avatar">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                    viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path
                                        d="M16.7 8a3 3 0 0 0 -2.7 -2h-4a3 3 0 0 0 0 6h4a3 3 0 0 1 0 6h-4a3 3 0 0 1 -2.7 -2" />
                                    <path d="M12 3v3m0 12v3" />
                                </svg>
                            </span>
                        </div>
                        <div class="col">
                            <div class="font-weight-medium">
                                Total Sales
                            </div>
                            <div class="text-muted">
                                ₹ {{ number_format($summary['total_sales'], 2) }}
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
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                    viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path d="M3 21l18 0" />
                                    <path d="M5 21v-14l8 -4l8 4v14" />
                                    <path d="M19 21v-10l-6 -4" />
                                    <path d="M9 9l0 .01" />
                                    <path d="M9 12l0 .01" />
                                    <path d="M9 15l0 .01" />
                                    <path d="M9 18l0 .01" />
                                </svg>
                            </span>
                        </div>
                        <div class="col">
                            <div class="font-weight-medium">
                                Diesel Cost
                            </div>
                            <div class="text-muted">
                                ₹ {{ number_format($summary['total_diesel'], 2) }}
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
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                    viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" />
                                    <path d="M12 10m-3 0a3 3 0 1 0 6 0a3 3 0 1 0 -6 0" />
                                    <path d="M6.168 18.849a4 4 0 0 1 3.832 -2.849h4a4 4 0 0 1 3.834 2.855" />
                                </svg>
                            </span>
                        </div>
                        <div class="col">
                            <div class="font-weight-medium">
                                Outstanding
                            </div>
                            <div class="text-muted">
                                ₹ {{ number_format($summary['outstanding'], 2) }}
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
                            <span class="bg-blue text-white avatar">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                    viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path d="M7 17m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" />
                                    <path d="M17 17m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" />
                                    <path d="M5 17h-2v-4m-1 -8h11v12m-4 0h6m4 0h2v-6h-8m0 -5h5l3 5" />
                                    <path d="M3 9l4 0" />
                                </svg>
                            </span>
                        </div>
                        <div class="col">
                            <div class="font-weight-medium">
                                Total Loads
                            </div>
                            <div class="text-muted">
                                {{ $summary['total_loads'] }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detailed Breakdown -->
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Material Breakdown</h3>
                </div>
                <div class="table-responsive">
                    <table class="table card-table table-vcenter text-nowrap datatable">
                        <thead>
                            <tr>
                                <th>Material / Metal Type</th>
                                <th>No. of Loads</th>
                                <th>Total Volume (CFT)</th>
                                <th class="text-end">Total Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($metalStats as $stat)
                                <tr>
                                    <td>{{ $stat->metalType->name ?? 'Unknown' }}</td>
                                    <td>{{ $stat->count }}</td>
                                    <td>
                                        @if($stat->total_cft > 0)
                                            {{ number_format($stat->total_cft, 2) }} CFT
                                        @elseif($stat->total_tons > 0)
                                            {{ number_format($stat->total_tons, 2) }} CFT
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="text-end fw-bold">₹ {{ number_format($stat->total_amount, 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center">No completed sales found for this date.</td>
                                </tr>
                            @endforelse
                        </tbody>
                        <tfoot>
                            <tr class="fw-bold bg-light">
                                <td>TOTAL</td>
                                <td>{{ $summary['total_loads'] }}</td>
                                <td>-</td>
                                <td class="text-end">₹ {{ number_format($summary['total_sales'], 2) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-tabler-layout>
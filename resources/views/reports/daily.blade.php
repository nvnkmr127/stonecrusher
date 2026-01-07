<x-tabler-layout>
    <x-slot name="header">
        <h2 class="page-title">
            {{ __('Daily Report') }}
        </h2>
    </x-slot>

    <div class="row row-cards">
        <div class="col-12">
            <!-- Filters -->
            <div class="card mb-3">
                <div class="card-body py-3">
                    <div class="d-flex justify-content-between align-items-end">
                        <form action="{{ route('reports.daily') }}" method="GET" class="d-flex gap-2 align-items-end">
                            <div>
                                <label class="form-label">{{ __('Select Date') }}</label>
                                <input type="date" name="date" value="{{ $date }}" class="form-control" required>
                            </div>
                            <button type="submit" class="btn btn-primary">{{ __('Fetch Report') }}</button>
                        </form>

                        <div class="d-flex align-items-center gap-3">
                            <span
                                class="badge bg-{{ \App\Services\DayClosureService::isClosed($date) ? 'green' : 'secondary' }} text-white">
                                Status: {{ \App\Services\DayClosureService::isClosed($date) ? 'Closed' : 'Open' }}
                            </span>

                            <div class="btn-group">
                                <a href="{{ route('reports.daily.export', ['date' => $date, 'format' => 'csv']) }}"
                                    class="btn btn-success">CSV</a>
                                <a href="{{ route('reports.daily.export', ['date' => $date, 'format' => 'pdf']) }}"
                                    class="btn btn-danger">PDF</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sales Summary Block -->
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-body">
                    <h3 class="card-title">Sales Activity</h3>
                    <div class="row g-3">
                        <div class="col-6 col-sm-4">
                            <div class="text-secondary mb-1">Total Gate Passes</div>
                            <div class="h2 mb-0">{{ $salesSummary['count'] }}</div>
                        </div>
                        <div class="col-6 col-sm-4">
                            <div class="text-secondary mb-1">Total Sales</div>
                            <div class="h2 mb-0 text-primary">{{ number_format($salesSummary['total_amount'], 2) }}
                            </div>
                        </div>
                        <div class="col-6 col-sm-4">
                            <div class="text-secondary mb-1">Diesel Cost</div>
                            <div class="h3 mb-0 text-danger">{{ number_format($salesSummary['total_diesel'], 2) }}</div>
                        </div>
                        <div class="col-6 col-sm-4">
                            <div class="text-secondary mb-1">Advances Adjusted</div>
                            <div class="h3 mb-0 text-warning">{{ number_format($salesSummary['total_advance'], 2) }}
                            </div>
                        </div>
                        <div class="col-6 col-sm-4">
                            <div class="text-secondary mb-1">Outstanding</div>
                            <div class="h3 mb-0 text-azure">{{ number_format($salesSummary['outstanding'], 2) }}</div>
                        </div>
                        <div class="col-6 col-sm-4">
                            <div class="text-secondary mb-1">Volume/Qty</div>
                            <div class="h3 mb-0">{{ number_format($salesSummary['total_volume'], 2) }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Collections Summary Block -->
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-body">
                    <h3 class="card-title">Collections (Cash In)</h3>
                    <div class="row mb-3">
                        <div class="col">
                            <div class="text-secondary mb-1">Total Collected</div>
                            <div class="h2 mb-0 text-success">
                                {{ number_format($collectionSummary['total_collected'], 2) }}
                            </div>
                        </div>
                    </div>

                    <div class="vr mx-2 bg-secondary opacity-10"></div>

                    <div class="mt-3">
                        <div class="fw-bold mb-2">Breakdown by Mode:</div>
                        <ul class="list-group list-group-flush">
                            @forelse($collectionSummary['by_mode'] as $mode => $amount)
                                <li
                                    class="list-group-item d-flex justify-content-between align-items-center px-0 py-2 border-0">
                                    <span>{{ $mode ?: 'Unknown' }}</span>
                                    <span class="fw-medium">{{ number_format($amount, 2) }}</span>
                                </li>
                            @empty
                                <li class="list-group-item px-0 text-muted">No collections today.</li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Metal Wise Summary -->
        <div class="col-12 mt-3">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Metal-wise Summary</h3>
                </div>
                <div class="table-responsive">
                    <table class="table table-vcenter card-table">
                        <thead>
                            <tr>
                                <th>Metal Type</th>
                                <th class="text-end">Count</th>
                                <th class="text-end">Total Quantity</th>
                                <th class="text-end">Total Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($metalStats as $stat)
                                <tr>
                                    <td>{{ $stat['name'] }}</td>
                                    <td class="text-end">{{ $stat['count'] }}</td>
                                    <td class="text-end">{{ number_format($stat['quantity'], 2) }}</td>
                                    <td class="text-end">{{ number_format($stat['amount'], 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted">No data.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Detailed Lists -->
        <div class="col-12 mt-3">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Completed Sales (Gate Passes)</h3>
                </div>
                <div class="table-responsive">
                    <table class="table table-vcenter card-table">
                        <thead>
                            <tr>
                                <th>GP#</th>
                                <th>Client</th>
                                <th>Vehicle / Driver</th>
                                <th>Material</th>
                                <th class="text-end">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($gatePasses as $gp)
                                <tr>
                                    <td>{{ $gp->gate_pass_number }}</td>
                                    <td>{{ $gp->client->name ?? 'N/A' }}</td>
                                    <td>{{ $gp->vehicle->registration_number ?? 'N/A' }} / {{ $gp->driver_name }}</td>
                                    <td>{{ $gp->metalType->name ?? 'N/A' }} ({{ $gp->loading_quantity ?: $gp->net_weight }})
                                    </td>
                                    <td class="text-end font-medium">
                                        {{ number_format($gp->total_amount, 2) }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted">No sales found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</x-tabler-layout>
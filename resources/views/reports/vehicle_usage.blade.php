<x-tabler-layout>
    <x-slot name="header">
        <div class="row g-2 align-items-center">
            <div class="col">
                <h2 class="page-title">
                    {{ __('Vehicle Usage Report') }}
                </h2>
                <div class="text-muted small mt-1">
                    {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} to
                    {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}
                </div>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <form action="{{ route('reports.vehicle-usage') }}" method="GET" class="d-flex gap-2 align-items-end">
                    <div>
                        <label class="form-label mb-1 small">{{ __('Start Date') }}</label>
                        <input type="date" name="start_date" value="{{ $startDate }}"
                            class="form-control form-control-sm">
                    </div>
                    <div>
                        <label class="form-label mb-1 small">{{ __('End Date') }}</label>
                        <input type="date" name="end_date" value="{{ $endDate }}" class="form-control form-control-sm">
                    </div>
                    <button type="submit" class="btn btn-primary btn-sm px-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-filter" width="24"
                            height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path
                                d="M4 4h16v2.172a2 2 0 0 1 -.586 1.414l-4.414 4.414v7l-6 2v-8.5l-4.414 -4.414a2 2 0 0 1 -.586 -1.414v-2.172z" />
                        </svg>
                        {{ __('Filter') }}
                    </button>
                    <button type="button" onclick="window.print()" class="btn btn-outline-secondary btn-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-printer" width="24"
                            height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M17 17h2a2 2 0 0 0 2 -2v-4a2 2 0 0 0 -2 -2h-14a2 2 0 0 0 -2 2v4a2 2 0 0 0 2 2h2" />
                            <path d="M17 9v-4a2 2 0 0 0 -2 -2h-6a2 2 0 0 0 -2 2v4" />
                            <path d="M7 13m0 2a2 2 0 0 1 2 -2h6a2 2 0 0 1 2 2v4a2 2 0 0 1 -2 2h-6a2 2 0 0 1 -2 -2z" />
                        </svg>
                        {{ __('Print') }}
                    </button>
                </form>
            </div>
        </div>
    </x-slot>

    <div class="row row-cards">
        @forelse($usageData as $data)
            <div class="col-12">
                <div class="card card-stacked">
                    <div class="card-header bg-light-subtle py-3">
                        <div class="row g-3 align-items-center w-100">
                            <div class="col">
                                <h3 class="card-title fw-bold mb-0" style="font-size: 1.1rem;">
                                    <span class="badge bg-blue-lt me-2">{{ $data->vehicle->registration_number }}</span>
                                    {{ $data->vehicle->type }}
                                </h3>
                                <div class="text-muted small mt-1">
                                    Model: {{ $data->vehicle->model ?: 'N/A' }} | Owned:
                                    {{ $data->vehicle->is_owned ? 'Yes' : 'No' }}
                                </div>
                            </div>
                            <div class="col-auto">
                                <div class="d-flex gap-4">
                                    <div class="text-center">
                                        <div class="text-secondary small fw-bold">TOTAL TRIPS</div>
                                        <div class="h3 mb-0">{{ $data->total_trips }}</div>
                                    </div>
                                    <div class="text-center">
                                        <div class="text-secondary small fw-bold">TOTAL QTY</div>
                                        <div class="h3 mb-0">{{ number_format($data->total_qty, 2) }}</div>
                                    </div>
                                    <div class="text-center">
                                        <div class="text-secondary small fw-bold">DIESEL (ACTUAL)</div>
                                        <div class="h3 mb-0 text-danger">{{ number_format($data->actual_diesel_liters, 2) }}
                                            L</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row g-4">
                            <div class="col-md-3">
                                <div class="card bg-gray-50 border-0 shadow-none">
                                    <div class="card-body p-3">
                                        <h4 class="fw-bold mb-3 d-flex align-items-center">
                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                class="icon icon-tabler icon-tabler-chart-pie me-2 text-primary" width="24"
                                                height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                                fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                <path
                                                    d="M10 3.2a9 9 0 1 0 10.8 10.8a1 1 0 0 0 -1 -1h-6.8a1 1 0 0 1 -1 -1v-6.8a1 1 0 0 0 -1 -1" />
                                                <path d="M15 3.5a9 9 0 0 1 5.5 5.5" />
                                            </svg>
                                            Used In (% of Trips)
                                        </h4>
                                        <div class="mb-3">
                                            <div class="d-flex justify-content-between mb-1 small">
                                                <span>Quarry</span>
                                                <span class="fw-bold">{{ $data->percentages['quarry'] }}%</span>
                                            </div>
                                            <div class="progress progress-sm">
                                                <div class="progress-bar bg-primary"
                                                    style="width: {{ $data->percentages['quarry'] }}%"></div>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <div class="d-flex justify-content-between mb-1 small">
                                                <span>Crusher</span>
                                                <span class="fw-bold">{{ $data->percentages['crusher'] }}%</span>
                                            </div>
                                            <div class="progress progress-sm">
                                                <div class="progress-bar bg-teal"
                                                    style="width: {{ $data->percentages['crusher'] }}%"></div>
                                            </div>
                                        </div>
                                        <div class="mb-0">
                                            <div class="d-flex justify-content-between mb-1 small">
                                                <span>External</span>
                                                <span class="fw-bold">{{ $data->percentages['external'] }}%</span>
                                            </div>
                                            <div class="progress progress-sm">
                                                <div class="progress-bar bg-orange"
                                                    style="width: {{ $data->percentages['external'] }}%"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-9 border-start">
                                <div class="table-responsive">
                                    <table class="table table-vcenter table-nowrap card-table table-sm">
                                        <thead>
                                            <tr>
                                                <th class="bg-light-subtle">Usage Type</th>
                                                <th class="text-center bg-light-subtle">Trips</th>
                                                <th class="text-end bg-light-subtle">Qty Carried</th>
                                                <th class="text-end bg-light-subtle text-danger">Diesel (Ltrs)</th>
                                                <th class="text-end bg-light-subtle">Allocated (₹)</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($data->breakdown as $type => $stats)
                                                <tr>
                                                    <td class="fw-bold py-2">{{ $type }}</td>
                                                    <td class="text-center">{{ $stats['trips'] }}</td>
                                                    <td class="text-end">{{ number_format($stats['qty'], 2) }}</td>
                                                    <td class="text-end text-danger fw-medium">
                                                        {{ number_format($stats['diesel_qty'], 2) }} L</td>
                                                    <td class="text-end text-muted small">₹
                                                        {{ number_format($stats['diesel_amount'], 0) }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot class="bg-light-subtle fw-bold">
                                            <tr>
                                                <td>Total Usage</td>
                                                <td class="text-center">{{ $data->total_trips }}</td>
                                                <td class="text-end">{{ number_format($data->total_qty, 2) }}</td>
                                                <td class="text-end text-danger">
                                                    {{ number_format($data->allocated_diesel_qty, 2) }} L</td>
                                                <td class="text-end">₹ {{ number_format($data->allocated_diesel, 0) }}</td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                                <div class="mt-3 d-flex justify-content-between align-items-center">
                                    <div class="small text-muted">
                                        <i class="ti ti-info-circle me-1"></i> Efficiency:
                                        @if($data->total_qty > 0 && $data->allocated_diesel_qty > 0)
                                            <strong>{{ number_format($data->total_qty / $data->allocated_diesel_qty, 2) }} Qty /
                                                L</strong>
                                        @else
                                            N/A
                                        @endif
                                    </div>
                                    <div class="small">
                                        Diesel Gap:
                                        <span
                                            class="badge bg-{{ abs($data->actual_diesel_liters - $data->allocated_diesel_qty) < 5 ? 'green' : 'red' }}-lt px-2">
                                            {{ number_format($data->actual_diesel_liters - $data->allocated_diesel_qty, 2) }}
                                            L
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center py-5">
                <div class="empty">
                    <div class="empty-img"><img src="./static/illustrations/undraw_no_data_re_kwbl.svg" height="128" alt="">
                    </div>
                    <p class="empty-title">No vehicle usage records found</p>
                    <p class="empty-subtitle text-muted">
                        Try adjusting your filters or record some gate passes.
                    </p>
                </div>
            </div>
        @endforelse
    </div>
</x-tabler-layout>
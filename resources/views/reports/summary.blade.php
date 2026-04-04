<x-tabler-layout>
    <x-slot name="header">
        <div class="row align-items-center d-print-none">
            <div class="col">
                <div class="page-pretitle text-uppercase fw-bold text-muted" style="letter-spacing: 0.05em;">Categorical Analysis</div>
                <h2 class="page-title h1 fw-bold">{{ $title }}</h2>
            </div>
            <div class="col-auto ms-auto">
                <div class="btn-list">
                    <a href="{{ route('reports.summary.export', ['type' => $type, 'start_date' => $startDate, 'end_date' => $endDate, 'format' => 'pdf']) }}" class="btn btn-white shadow-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-file-download" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M14 3v4a1 1 0 0 0 1 1h4"></path><path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z"></path><path d="M12 17v-6"></path><path d="M9 14l3 3l3 -3"></path></svg>
                        Export PDF
                    </a>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="premium-header-card mb-4">
        <form action="{{ route('reports.summary', $type) }}" method="GET" class="row align-items-center">
            <div class="col-md-7">
                <div class="d-flex align-items-center">
                    <span class="avatar avatar-lg bg-white-lt text-white me-3 border border-white-subtle" style="backdrop-filter: blur(4px);">
                        @if($type === 'metal')
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-box" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 3l8 4.5l0 9l-8 4.5l-8 -4.5l0 -9l8 -4.5" /><path d="M12 12l8 -4.5" /><path d="M12 12l0 9" /><path d="M12 12l-8 -4.5" /></svg>
                        @elseif($type === 'client')
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-users" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><circle cx="9" cy="7" r="4" /><path d="M3 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" /><path d="M16 3.13a4 4 0 0 1 0 7.75" /><path d="M21 21v-2a4 4 0 0 0 -3 -3.85" /></svg>
                        @else
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-truck" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><circle cx="7" cy="17" r="2" /><circle cx="17" cy="17" r="2" /><path d="M5 17h-2v-11a1 1 0 0 1 1 -1h9v12m-4 0h6m4 0h2v-6h-8m0 -5h5l3 5" /></svg>
                        @endif
                    </span>
                    <div>
                        <div class="text-white-50 small text-uppercase fw-bold" style="letter-spacing: 1px;">AUDIT RANGE</div>
                        <div class="d-flex align-items-center gap-2">
                           <input type="date" name="start_date" value="{{ $startDate }}" class="form-control form-control-flush fw-bold fs-3 text-white bg-transparent border-0 p-0" onchange="this.form.submit()" style="width: auto;">
                           <span class="text-white-50">to</span>
                           <input type="date" name="end_date" value="{{ $endDate }}" class="form-control form-control-flush fw-bold fs-3 text-white bg-transparent border-0 p-0" onchange="this.form.submit()" style="width: auto;">
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-5 text-md-end mt-4 mt-md-0">
                <div class="p-3 bg-white-lt rounded-4 border border-white-subtle d-inline-block text-start" style="backdrop-filter: blur(8px); min-width: 250px;">
                    <div class="text-white-50 small text-uppercase fw-bold mb-1">Total Period Revenue</div>
                    <div class="h1 mb-0 fw-bold">₹ {{ number_format($data->sum('total_sales'), 2) }}</div>
                </div>
            </div>
        </form>
    </div>

    <!-- Period Highlights -->
    <div class="premium-stats-grid mb-4">
        <div class="stat-premium-card">
            <div class="stat-icon-wrapper bg-blue-lt text-blue">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 13h1" /><path d="M20 13h1" /><path d="M5.6 15.4l.7 .7" /><path d="M17.7 16.1l.7 -.7" /><path d="M7.1 7.1l.7 .7" /><path d="M16.2 7.8l.7 -.7" /><path d="M12 3v1" /><path d="M12 20v1" /><circle cx="12" cy="12" r="5" /><path d="M12 9v3" /></svg>
            </div>
            <div class="text-muted small fw-bold text-uppercase mb-1">Total Transactions</div>
            <div class="h2 mb-0 fw-bold">{{ number_format($data->sum('count')) }} <span class="small text-muted">Loads</span></div>
        </div>

        @if($type === 'client')
        <div class="stat-premium-card text-decoration-none">
            <div class="stat-icon-wrapper bg-teal-lt text-teal">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M17 8v-3a1 1 0 0 0 -1 -1h-10a2 2 0 0 0 0 4h12a1 1 0 0 1 1 1v3m0 4v3a1 1 0 0 1 -1 1h-12a2 2 0 0 1 -2 -2v-12" /><path d="M20 12v4h-4a2 2 0 0 1 0 -4h4" /></svg>
            </div>
            <div class="text-muted small fw-bold text-uppercase mb-1">Total Period Collections</div>
            <div class="h2 mb-0 fw-bold">₹ {{ number_format($data->sum('total_collections'), 2) }}</div>
        </div>
        @endif

        @if($type === 'metal')
        <div class="stat-premium-card text-decoration-none">
            <div class="stat-icon-wrapper bg-teal-lt text-teal">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 4v16" /><path d="M18 12l-6 -6l-6 6" /></svg>
            </div>
            <div class="text-muted small fw-bold text-uppercase mb-1">Total Output Volume</div>
            <div class="h2 mb-0 fw-bold">{{ number_format($data->sum('total_qty'), 2) }} <span class="small text-muted">CFT</span></div>
        </div>
        @endif

        @if($type === 'vehicle')
        <div class="stat-premium-card text-decoration-none">
            <div class="stat-icon-wrapper bg-purple-lt text-purple">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><circle cx="12" cy="12" r="9" /><path d="M12 7l0 5l3 3" /></svg>
            </div>
            <div class="text-muted small fw-bold text-uppercase mb-1">Total Logistics Distance</div>
            <div class="h2 mb-0 fw-bold">{{ number_format($data->sum('total_km')) }} <span class="small text-muted">KM</span></div>
        </div>
        @endif

        <div class="stat-premium-card text-decoration-none">
            <div class="stat-icon-wrapper bg-green-lt text-green">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M16.7 8a3 3 0 0 0 -2.7 -2h-4a3 3 0 0 0 0 6h4a3 3 0 0 1 0 6h-4a3 3 0 0 1 -2.7 -2" /><path d="M12 3l0 3" /><path d="M12 18l0 3" /></svg>
            </div>
            <div class="text-muted small fw-bold text-uppercase mb-1">Average Revenue / Trip</div>
            @php $avg = $data->sum('count') > 0 ? $data->sum('total_sales') / $data->sum('count') : 0; @endphp
            <div class="h2 mb-0 fw-bold">₹ {{ number_format($avg, 2) }}</div>
        </div>
    </div>

    <div class="row row-cards">
        <div class="col-12">
            <div class="card border-0 shadow-sm overflow-hidden">
                <div class="card-header bg-transparent border-0 py-3">
                    <h3 class="card-title fw-bold">Categorized Data Table</h3>
                </div>
                <div class="table-responsive">
                    <table class="table table-vcenter table-premium card-table border-top">
                        <thead class="bg-light">
                            <tr>
                                <th>Category / Name</th>
                                <th class="text-end">Trips</th>
                                @if($type === 'metal') <th class="text-end">Volume (CFT)</th> @endif
                                @if($type === 'vehicle') <th class="text-end">Distance (KM)</th> @endif
                                <th class="text-end">Period Billing</th>
                                @if($type === 'client') <th class="text-end">Period Collections</th> @endif
                                @if($type === 'client') <th class="text-end">Net Deviation</th> @endif
                                @if($type === 'client') <th class="text-end text-muted">Transport Cost</th> @endif
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($data as $row)
                                <tr>
                                    <td>
                                        <div class="fw-bold fs-3 text-dark">
                                            @if($type === 'metal')
                                                {{ $row->metalType->name ?? 'Direct' }}
                                            @elseif($type === 'client')
                                                <a href="{{ route('clients.show', $row->client_id) }}" class="text-dark text-decoration-none hover-text-primary">
                                                    {{ $row->client->name ?? 'Counter Sale' }}
                                                </a>
                                            @elseif($type === 'vehicle')
                                                {{ $row->vehicle->registration_number ?? 'External' }}
                                            @endif
                                        </div>
                                    </td>
                                    <td class="text-end fw-medium"><span class="badge bg-gray-lt text-dark px-2">{{ $row->count }}</span></td>
                                    @if($type === 'metal') <td class="text-end fw-bold">{{ number_format($row->total_qty, 2) }}</td> @endif
                                    @if($type === 'vehicle') <td class="text-end fw-bold">{{ number_format($row->total_km) }}</td> @endif
                                    <td class="text-end fw-bold text-dark fs-3">₹ {{ number_format($row->total_sales, 2) }}</td>
                                    @if($type === 'client')
                                        <td class="text-end fw-bold text-teal fs-3">₹ {{ number_format($row->total_collections ?? 0, 2) }}</td>
                                        <td class="text-end">
                                            @php $deviation = ($row->total_collections ?? 0) - $row->total_sales; @endphp
                                            <div class="fw-bold text-{{ $deviation >= 0 ? 'green' : 'red' }} fs-3">
                                                ₹ {{ number_format($deviation, 2) }}
                                            </div>
                                        </td>
                                    @endif
                                    @if($type === 'client') <td class="text-end text-muted">₹ {{ number_format($row->transport, 2) }}</td> @endif
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5 text-muted">No categorical data found for the selected period.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-tabler-layout>

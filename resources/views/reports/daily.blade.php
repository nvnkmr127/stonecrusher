<x-tabler-layout>
    <x-slot name="header">
        <div class="row align-items-center d-print-none">
            <div class="col">
                <div class="page-pretitle text-uppercase fw-bold text-muted" style="letter-spacing: 0.05em;">Reporting / Financials</div>
                <h2 class="page-title h1 fw-bold">{{ __('Daily Business Report') }}</h2>
            </div>
            <div class="col-auto ms-auto">
                <div class="btn-list">
                    <a href="{{ route('reports.daily.export', ['date' => $date, 'format' => 'pdf']) }}" class="btn btn-white shadow-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-file-download" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M14 3v4a1 1 0 0 0 1 1h4"></path><path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z"></path><path d="M12 17v-6"></path><path d="M9 14l3 3l3 -3"></path></svg>
                        Export PDF
                    </a>
                    @if(!\App\Services\DayClosureService::isClosed($date))
                        <button class="btn btn-primary shadow-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-lock" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><rect x="5" y="11" width="14" height="10" rx="2"></rect><circle cx="12" cy="16" r="1"></circle><path d="M8 11v-4a4 4 0 1 1 8 0v4"></path></svg>
                            Close Day
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </x-slot>

    <div class="premium-header-card mb-4">
        <form action="{{ route('reports.daily') }}" method="GET" class="row align-items-center">
            <div class="col-md-6">
                <div class="d-flex align-items-center">
                    <span class="avatar avatar-lg bg-white-lt text-white me-3 border border-white-subtle" style="backdrop-filter: blur(4px);">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-calendar-stats" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M11.795 21h-6.795a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v4" /><path d="M18 14v4h4" /><circle cx="18" cy="18" r="4" /><path d="M15 3v4" /><path d="M7 3v4" /><path d="M3 11h16" /></svg>
                    </span>
                    <div>
                        <div class="text-white-50 small text-uppercase fw-bold" style="letter-spacing: 1px;">REPORTING DATE</div>
                        <div class="d-flex align-items-center gap-2">
                           <input type="date" name="date" value="{{ $date }}" class="form-control form-control-flush fw-bold fs-2 text-white bg-transparent border-0 p-0" onchange="this.form.submit()" style="width: auto; min-width: 200px;">
                           <span class="badge bg-{{ \App\Services\DayClosureService::isClosed($date) ? 'green' : 'orange' }} text-white border-0 shadow-sm px-2 py-1">
                                {{ \App\Services\DayClosureService::isClosed($date) ? 'CLOSED' : 'OPEN' }}
                           </span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 text-md-end mt-4 mt-md-0">
                <div class="p-3 bg-white-lt rounded-4 border border-white-subtle d-inline-block text-start" style="backdrop-filter: blur(8px); min-width: 250px;">
                    <div class="text-white-50 small text-uppercase fw-bold mb-1">CASH POSITION Today</div>
                    <div class="h1 mb-0 fw-bold">₹ {{ number_format($collectionSummary['total_collected'], 2) }}</div>
                </div>
            </div>
        </form>
    </div>

    <!-- Main Stats Highlights -->
    <div class="premium-stats-grid mb-4">
        <div class="stat-premium-card">
            <div class="stat-icon-wrapper bg-blue-lt">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon text-blue" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M16 6h3a1 1 0 0 1 1 1v11a2 2 0 0 1 -4 0v-13a1 1 0 0 0 -1 -1h-10a1 1 0 0 0 -1 1v12a3 3 0 0 0 3 3h11" /><path d="M8 8l4 0" /><path d="M8 12l4 0" /><path d="M8 16l4 0" /></svg>
            </div>
            <div class="text-muted small fw-bold text-uppercase mb-1">Total Sales</div>
            <div class="h2 mb-0 fw-bold text-blue">₹ {{ number_format($salesSummary['total_amount'], 2) }}</div>
            <div class="text-muted mt-2 small fw-medium">{{ $salesSummary['count'] }} Gate Passes issued</div>
        </div>

        <div class="stat-premium-card">
            <div class="stat-icon-wrapper bg-green-lt">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon text-green" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><circle cx="12" cy="12" r="9" /><path d="M14.8 9a2 2 0 0 0 -1.8 -1h-2a2 2 0 0 0 0 4h2a2 2 0 0 1 0 4h-2a2 2 0 0 1 -1.8 -1" /><path d="M12 6v2m0 8v2" /></svg>
            </div>
            <div class="text-muted small fw-bold text-uppercase mb-1">Total Collections</div>
            <div class="h2 mb-0 fw-bold text-green">₹ {{ number_format($collectionSummary['total_collected'], 2) }}</div>
            <div class="text-muted mt-2 small fw-medium">Cash in-hand flows</div>
        </div>

        <div class="stat-premium-card">
            <div class="stat-icon-wrapper bg-orange-lt">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon text-orange" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M13 3l0 7l6 0l-8 11l0 -7l-6 0l8 -11" /></svg>
            </div>
            <div class="text-muted small fw-bold text-uppercase mb-1">Diesel Consumption</div>
            <div class="h2 mb-0 fw-bold text-orange">{{ number_format($salesSummary['total_diesel_liters'] ?? 0, 2) }} <span class="small">Ltrs</span></div>
            <div class="text-muted mt-2 small fw-medium">Cost: ₹{{ number_format($salesSummary['total_diesel'], 2) }}</div>
        </div>

        <div class="stat-premium-card">
            <div class="stat-icon-wrapper bg-red-lt">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon text-red" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 20l-3 1v-8.5l-4.472 -4.472a0.75 .75 0 0 1 0 -1.056l4.472 -4.472v-1.5l1.5 1.5l1.5 -1.5v1.5l4.472 4.472a0.75 .75 0 0 1 0 1.056l-4.472 4.472v8.5l-3 -1z" /></svg>
            </div>
            <div class="text-muted small fw-bold text-uppercase mb-1">Closing Due</div>
            <div class="h2 mb-0 fw-bold text-red">₹ {{ number_format($salesSummary['outstanding'], 2) }}</div>
            <div class="text-muted mt-2 small fw-medium">Uncollected receivables</div>
        </div>
    </div>

    <div class="row row-cards">
        <!-- Stock Details -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm mb-4 h-100">
                <div class="card-header bg-transparent border-0 py-3 d-flex justify-content-between align-items-center">
                    <h3 class="card-title fw-bold">Diesel Inventory (Daily Tank)</h3>
                    @if(!$dieselStock)
                        <a href="{{ route('diesel-stocks.create', ['date' => $date]) }}" class="btn btn-sm btn-primary shadow-sm">Record Stock</a>
                    @endif
                </div>
                <div class="table-responsive">
                    <table class="table table-vcenter table-premium card-table border-top">
                        <tbody>
                            @if($dieselStock)
                                <tr>
                                    <td class="text-muted small fw-bold text-uppercase">Opening Balance</td>
                                    <td class="text-end fw-bold fs-3">{{ number_format($dieselStock->opening_liters, 2) }} L</td>
                                </tr>
                                <tr>
                                    <td class="text-muted small fw-bold text-uppercase">Stock In (Purchase)</td>
                                    <td class="text-end text-success fw-bold fs-3">+ {{ number_format($dieselStock->purchased_liters, 2) }} L</td>
                                </tr>
                                <tr class="bg-primary-lt">
                                    <td class="text-primary small fw-bold text-uppercase">Total Available</td>
                                    <td class="text-end text-primary fw-bold fs-2">{{ number_format($dieselStock->total_available, 2) }} L</td>
                                </tr>
                                <tr>
                                    <td class="text-muted small fw-bold text-uppercase">Closing Balance</td>
                                    <td class="text-end fw-bold fs-3">{{ number_format($dieselStock->closing_liters, 2) }} L</td>
                                </tr>
                            @else
                                <tr>
                                    <td colspan="2" class="text-center py-5 text-muted">
                                        <div class="mb-2">No stock record found for this date.</div>
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Collections Breakdown -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm mb-4 h-100">
                <div class="card-header bg-transparent border-0 py-3">
                    <h3 class="card-title fw-bold">Collections by Mode</h3>
                </div>
                <div class="card-body">
                    <div class="row g-2">
                        @forelse($collectionSummary['by_mode'] as $mode => $amount)
                            <div class="col-12">
                                <div class="p-3 bg-light rounded-3 d-flex justify-content-between align-items-center mb-2">
                                    <div class="d-flex align-items-center">
                                        <div class="stat-icon-wrapper bg-white text-primary me-3 border-0" style="width: 40px; height: 40px;">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M17 8l4 4l-4 4" /><path d="M3 12l18 0" /></svg>
                                        </div>
                                        <div class="fw-bold text-uppercase small" style="letter-spacing: 0.5px;">{{ $mode ?: 'Direct' }}</div>
                                    </div>
                                    <div class="h3 mb-0 fw-bold text-dark">₹ {{ number_format($amount, 2) }}</div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12 text-center py-5 text-muted">No collections recorded today.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- Metal Stats -->
        <div class="col-12">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-transparent border-0 py-3">
                    <h3 class="card-title fw-bold">Material Production & Sales</h3>
                </div>
                <div class="table-responsive">
                    <table class="table table-vcenter table-premium card-table border-top">
                        <thead>
                            <tr class="bg-light">
                                <th>Material Type</th>
                                <th class="text-end">Ticket Count</th>
                                <th class="text-end">Volume (CFT)</th>
                                <th class="text-end">Sales Value</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($metalStats as $stat)
                                <tr>
                                    <td><span class="fw-bold">{{ $stat['name'] }}</span></td>
                                    <td class="text-end"><span class="badge bg-gray-lt text-dark px-2">{{ $stat['count'] }}</span></td>
                                    <td class="text-end fw-medium">{{ number_format($stat['quantity'], 2) }}</td>
                                    <td class="text-end fw-bold text-blue">₹ {{ number_format($stat['amount'], 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">No material sales today.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- GP List -->
        <div class="col-12">
            <div class="card border-0 shadow-sm overflow-hidden mb-4">
                <div class="card-header bg-transparent border-0 py-3">
                    <h3 class="card-title fw-bold">Load Detail Log (Summary)</h3>
                </div>
                <div class="table-responsive">
                    <table class="table table-vcenter table-premium card-table border-top">
                        <thead>
                            <tr class="bg-light">
                                <th>GP #</th>
                                <th>Client / Project</th>
                                <th>Vehicle / Logistics</th>
                                <th>Material Qty</th>
                                <th class="text-end">Billed Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($gatePasses as $gp)
                                <tr>
                                    <td class="fw-bold">#{{ $gp->gate_pass_number }}</td>
                                    <td>
                                        <div class="fw-medium text-dark">{{ $gp->client->name ?? $gp->manual_customer_name ?? 'N/A' }}</div>
                                        <div class="small text-muted">{{ $gp->project->name ?? 'Direct' }}</div>
                                    </td>
                                    <td>
                                        <div class="small fw-bold text-uppercase">{{ $gp->vehicle->registration_number ?? 'Manual' }}</div>
                                        <div class="small text-muted">{{ $gp->driver_name }}</div>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-azure">{{ number_format($gp->loading_quantity ?: $gp->net_weight, 2) }} CFT</div>
                                        <div class="small text-muted">{{ $gp->metalType->name }}</div>
                                    </td>
                                    <td class="text-end">
                                        <div class="fw-bold text-dark">₹ {{ number_format($gp->total_amount, 2) }}</div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">No load movements found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-tabler-layout>
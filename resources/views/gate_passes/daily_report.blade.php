<x-tabler-layout>
    <x-slot name="header">
        <div class="row align-items-center d-print-none">
            <div class="col">
                <div class="page-pretitle text-uppercase fw-bold text-muted" style="letter-spacing: 0.1em;">Sales Report</div>
                <h2 class="page-title h1 fw-bold">Daily Summary</h2>
                <div class="text-muted mt-1">Sales recorded for <strong>{{ \Carbon\Carbon::parse($date)->format('d M, Y') }}</strong></div>
            </div>
            <div class="col-auto ms-auto">
                <form action="{{ route('gate-passes.daily-report') }}" method="GET" class="d-flex align-items-center gap-2">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-calendar-event" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M4 5m0 2a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2z"></path><path d="M16 3l0 4"></path><path d="M8 3l0 4"></path><path d="M4 11l16 0"></path><path d="M8 15h2v2h-2z"></path></svg>
                        </span>
                        <input type="date" name="date" class="form-control border-start-0 ps-0" value="{{ $date }}" onchange="this.form.submit()" style="width: 150px;">
                    </div>
                    
                    <div class="btn-list">
                        <button type="submit" class="btn btn-white btn-icon shadow-sm" title="Refresh">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none" /><path d="M20 11a8.1 8.1 0 0 0 -15.5 -2m-.5 -4v4h4" /><path d="M4 13a8.1 8.1 0 0 0 15.5 2m.5 4v-4h-4" /></svg>
                        </button>
                        <button type="button" class="btn btn-primary d-none d-md-flex shadow-sm px-4" onclick="window.print()">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon me-2" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none" /><path d="M17 17h2a2 2 0 0 0 2 -2v-4a2 2 0 0 0 -2 -2h-14a2 2 0 0 0 -2 2v4a2 2 0 0 0 2 2h2" /><path d="M17 9v-4a2 2 0 0 0 -2 -2h-6a2 2 0 0 0 -2 2v4" /><path d="M7 13m0 2a2 2 0 0 1 2 -2h6a2 2 0 0 1 2 2v4a2 2 0 0 1 -2 2h-6a2 2 0 0 1 -2 -2z" /></svg>
                            Print Report
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </x-slot>

    <!-- Detailed Stats Grid -->
    <div class="premium-stats-grid mb-4">
        <div class="stat-premium-card border-0 shadow-sm border-start border-4 border-blue">
            <div class="stat-icon-wrapper bg-blue-lt">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon text-blue" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M16.7 8a3 3 0 0 0 -2.7 -2h-4a3 3 0 0 0 0 6h4a3 3 0 0 1 0 6h-4a3 3 0 0 1 -2.7 -2" /><path d="M12 3v3m0 12v3" /></svg>
            </div>
            <div class="text-muted small fw-bold text-uppercase mb-1" style="letter-spacing: 0.5px;">Total Billing</div>
            <div class="h2 mb-0 fw-bold">₹ {{ number_format($summary['total_sales'], 2) }}</div>
            <div class="text-muted small mt-2">Value of all sales</div>
        </div>

        <div class="stat-premium-card border-0 shadow-sm border-start border-4 border-cyan">
            <div class="stat-icon-wrapper bg-cyan-lt">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon text-cyan" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M7 17m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" /><path d="M17 17m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" /><path d="M5 17h-2v-11a1 1 0 0 1 1 -1h9v12m-4 0h6m4 0h2v-6h-8m0 -5h5l3 5" /></svg>
            </div>
            <div class="text-muted small fw-bold text-uppercase mb-1" style="letter-spacing: 0.5px;">Total Transport</div>
            <div class="h2 mb-0 fw-bold">₹ {{ number_format($summary['total_lead'], 2) }}</div>
            <div class="text-muted small mt-2">Billed delivery charges</div>
        </div>

        <div class="stat-premium-card border-0 shadow-sm border-start border-4 border-yellow">
            <div class="stat-icon-wrapper bg-yellow-lt">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon text-yellow" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" /><path d="M12 10m-3 0a3 3 0 1 0 6 0a3 3 0 1 0 -6 0" /><path d="M6.168 18.849a4 4 0 0 1 3.832 -2.849h4a4 4 0 0 1 3.834 2.855" /></svg>
            </div>
            <div class="text-muted small fw-bold text-uppercase mb-1" style="letter-spacing: 0.5px;">Today's Balance</div>
            <div class="h2 mb-0 fw-bold text-yellow">₹ {{ number_format($summary['outstanding'], 2) }}</div>
            <div class="text-muted small mt-2">Money still to receive</div>
        </div>

        <div class="stat-premium-card border-0 shadow-sm border-start border-4 border-purple">
            <div class="stat-icon-wrapper bg-purple-lt">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon text-purple" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 11v-7a2 2 0 0 1 2 -2h4a2 2 0 0 1 2 2v7" /><path d="M10 21l-6 -6l1.41 -1.41l4.59 4.59l9.59 -9.59l1.41 1.41l-11 11" /></svg>
            </div>
            <div class="text-muted small fw-bold text-uppercase mb-1" style="letter-spacing: 0.5px;">Total Trips</div>
            <div class="h2 mb-0 fw-bold">{{ $summary['total_loads'] }}</div>
            <div class="text-muted small mt-2">Vehicle movements</div>
        </div>
    </div>

    <div class="row row-cards">
        <div class="col-12">
            <div class="card border-0 shadow-sm overflow-hidden">
                <div class="card-header border-0 bg-transparent py-4">
                    <div class="d-flex align-items-center">
                        <div class="icon-box bg-dark text-white rounded-circle me-3" style="width: 42px; height: 42px; display: flex; align-items: center; justify-content: center;">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-stack-2" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M12 4l-8 4l8 4l8 -4l-8 -4"></path><path d="M4 12l8 4l8 -4"></path><path d="M4 16l8 4l8 -4"></path></svg>
                        </div>
                        <div>
                            <h3 class="card-title fw-bold">Material Breakdown</h3>
                            <p class="text-muted small mb-0">Volume and sales grouped by stone type</p>
                        </div>
                    </div>
                </div>
                
                <div class="table-responsive">
                    <table class="table card-table table-vcenter table-premium">
                        <thead>
                            <tr>
                                <th style="width: 40%">Material Type</th>
                                <th class="text-center">Total Trips</th>
                                <th class="text-center">Weight / Volume</th>
                                <th class="text-end">Total Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($metalStats as $stat)
                                <tr>
                                    <td>
                                        <div class="fw-bold fs-3">{{ $stat->metalType->name ?? 'Other / Mixed' }}</div>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-light text-dark fw-bold px-3 py-2 border">{{ $stat->count }} Loads</span>
                                    </td>
                                    <td class="text-center text-muted">
                                        @if($stat->total_cft > 0)
                                            <span class="fw-bold text-dark">{{ number_format($stat->total_cft, 1) }}</span> <small>CFT</small>
                                        @elseif($stat->total_tons > 0)
                                            <span class="fw-bold text-dark">{{ number_format($stat->total_tons, 1) }}</span> <small>TONS</small>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        <div class="fw-extrabold fs-2">₹ {{ number_format($stat->total_amount, 2) }}</div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-5">
                                        <div class="text-muted mb-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-inbox" width="40" height="40" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M4 4m0 2a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2z"></path><path d="M4 13h3l3 3h4l3 -3h3"></path></svg>
                                        </div>
                                        <div class="fw-bold">No sales records found for this date.</div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                        @if($metalStats->count() > 0)
                        <tfoot>
                            <tr class="bg-dark text-white">
                                <td class="fw-bold py-3">TOTAL REPORTED</td>
                                <td class="text-center fw-bold py-3">{{ $summary['total_loads'] }} Trips</td>
                                <td class="text-center py-3 opacity-50">-</td>
                                <td class="text-end fw-extrabold fs-2 py-3">₹ {{ number_format($summary['total_sales'], 2) }}</td>
                            </tr>
                        </tfoot>
                        @endif
                    </table>
                </div>
            </div>
        </div>

        <!-- Client Breakdown -->
        <div class="col-md-6 col-12">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header border-0 bg-transparent py-3">
                    <h3 class="card-title fw-bold">Sales by Client</h3>
                </div>
                <div class="table-responsive">
                    <table class="table card-table table-vcenter">
                        <thead class="bg-light">
                            <tr>
                                <th>Client Name</th>
                                <th class="text-center">Trips</th>
                                <th class="text-end">Total Money</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($clientStats as $stat)
                                <tr>
                                    <td class="fw-bold">{{ $stat->client->name ?? ($stat->manual_customer_name ?: 'Cash Customer') }}</td>
                                    <td class="text-center"><span class="badge bg-light text-dark">{{ $stat->count }}</span></td>
                                    <td class="text-end fw-bold">₹ {{ number_format($stat->total_amount, 2) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="text-center py-4 text-muted">No client data.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Vehicle Breakdown -->
        <div class="col-md-6 col-12">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header border-0 bg-transparent py-3">
                    <h3 class="card-title fw-bold">Work by Vehicle</h3>
                </div>
                <div class="table-responsive">
                    <table class="table card-table table-vcenter">
                        <thead class="bg-light">
                            <tr>
                                <th>Truck No.</th>
                                <th class="text-center">Trips</th>
                                <th class="text-end">Total Work</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($vehicleStats as $stat)
                                <tr>
                                    <td class="fw-bold">{{ $stat->vehicle->registration_number ?? 'Manual Vehicle' }}</td>
                                    <td class="text-center"><span class="badge bg-light text-dark">{{ $stat->count }}</span></td>
                                    <td class="text-end fw-bold">₹ {{ number_format($stat->total_amount, 2) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="text-center py-4 text-muted">No vehicle data.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <div class="mt-4 d-none d-print-block text-center border-top pt-4">
        <p class="text-muted small">Report generated from {{ config('app.name') }} on {{ now()->format('d M Y, h:i A') }}</p>
        <div class="d-flex justify-content-between mt-5 pt-5 px-5">
            <div style="width: 200px; border-top: 1px solid #ddd; padding-top: 5px;">Manager Signature</div>
            <div style="width: 200px; border-top: 1px solid #ddd; padding-top: 5px;">Authorised Stamp</div>
        </div>
    </div>
</x-tabler-layout>
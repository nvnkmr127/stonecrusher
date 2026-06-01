<x-tabler-layout>
    <x-slot name="header">
        <div class="row align-items-center d-print-none">
            <div class="col">
                <div class="page-pretitle text-uppercase fw-bold text-muted" style="letter-spacing: 0.05em;">Date Range Audit</div>
                <h2 class="page-title h1 fw-bold">{{ __('Custom Transaction Log') }}</h2>
            </div>
            <div class="col-auto ms-auto">
                <div class="btn-list">
                    <a href="{{ route('reports.custom.export', ['start_date' => $startDate, 'end_date' => $endDate, 'client_id' => $clientId, 'format' => 'pdf']) }}" class="btn btn-white shadow-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-file-download" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M14 3v4a1 1 0 0 0 1 1h4"></path><path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z"></path><path d="M12 17v-6"></path><path d="M9 14l3 3l3 -3"></path></svg>
                        Export PDF
                    </a>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="premium-header-card mb-4">
        <form action="{{ route('reports.custom') }}" method="GET" class="row align-items-end g-3">
            <div class="col-md-3">
                <label class="form-label text-white-50 small fw-bold">START DATE</label>
                <input type="date" name="start_date" value="{{ $startDate }}" class="form-control bg-white-lt text-white border-0" style="backdrop-filter: blur(4px);">
            </div>
            <div class="col-md-3">
                <label class="form-label text-white-50 small fw-bold">END DATE</label>
                <input type="date" name="end_date" value="{{ $endDate }}" class="form-control bg-white-lt text-white border-0" style="backdrop-filter: blur(4px);">
            </div>
            <div class="col-md-4">
                <label class="form-label text-white-50 small fw-bold">CLIENT (OPTIONAL)</label>
                <select name="client_id" class="form-select bg-white-lt text-white border-0" style="backdrop-filter: blur(4px);">
                    <option value="" class="text-dark">All Clients</option>
                    @foreach($clients as $c)
                        <option value="{{ $c->id }}" {{ $clientId == $c->id ? 'selected' : '' }} class="text-dark">{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-white w-100 fw-bold">APPLY FILTER</button>
            </div>
        </form>
    </div>

    <!-- Quick Stats -->
    <div class="premium-stats-grid mb-4">
        <div class="stat-premium-card">
            <div class="stat-icon-wrapper bg-blue-lt">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon text-blue" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><rect x="4" y="5" width="16" height="16" rx="2" /><line x1="16" y1="3" x2="16" y2="7" /><line x1="8" y1="3" x2="8" y2="7" /><line x1="4" y1="11" x2="20" y2="11" /></svg>
            </div>
            <div class="text-muted small fw-bold text-uppercase mb-1">Total Loads</div>
            <div class="h2 mb-0 fw-bold">{{ $totalCount }}</div>
            <div class="text-muted mt-2 small fw-medium">Trips in period</div>
        </div>

        <div class="stat-premium-card">
            <div class="stat-icon-wrapper bg-green-lt text-green">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M16.7 8a3 3 0 0 0 -2.7 -2h-4a3 3 0 0 0 0 6h4a3 3 0 0 1 0 6h-4a3 3 0 0 1 -2.7 -2" /><path d="M12 3l0 3" /><path d="M12 18l0 3" /></svg>
            </div>
            <div class="text-muted small fw-bold text-uppercase mb-1">Audit Revenue</div>
            <div class="h2 mb-0 fw-bold">₹ {{ number_format($totalSales, 2) }}</div>
            <div class="text-muted mt-2 small fw-medium">Cumulative billings</div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm overflow-hidden">
                <div class="card-header bg-transparent border-0 py-3">
                    <h3 class="card-title fw-bold">Detailed Transaction Log</h3>
                </div>
                <div class="table-responsive">
                    <table class="table table-vcenter table-premium card-table border-top">
                        <thead class="bg-light">
                            <tr>
                                <th>Date</th>
                                <th>Gate Pass #</th>
                                <th>Client Details</th>
                                <th>Material / Info</th>
                                <th class="text-end">Billing Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($sales as $sale)
                                <tr>
                                    <td>
                                        <div class="fw-bold fs-4">{{ $sale->date->format('d M, Y') }}</div>
                                        <div class="small text-muted">{{ $sale->date->format('h:i A') }}</div>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-azure">{{ $sale->gate_pass_number }}</div>
                                    </td>
                                    <td>
                                        @if($sale->client_id)
                                            <a href="{{ route('clients.show', $sale->client_id) }}" class="fw-bold text-dark text-decoration-none hover-text-primary">
                                                {{ $sale->client->name }}
                                            </a>
                                        @else
                                            <span class="fw-bold text-secondary">{{ $sale->manual_customer_name ?? 'Counter Sale' }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge border text-dark px-2 fw-medium">{{ $sale->metalType->name }}</span>
                                        @if($sale->vehicle)
                                            <div class="small text-muted mt-1">{{ $sale->vehicle->registration_number }}</div>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        <div class="fw-bold fs-3 text-dark">₹ {{ number_format($sale->total_amount, 2) }}</div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-5 text-muted">No transactions found for the selected criteria.</td>
                                </tr>
                            @endforelse
                        </tbody>
                        @if($sales->isNotEmpty())
                        <tfoot class="bg-dark text-white fw-bold">
                            <tr>
                                <td colspan="4" class="py-3">AGGRAGATED AUDIT TOTAL</td>
                                <td class="text-end py-3 fs-2">₹ {{ number_format($totalSales, 2) }}</td>
                            </tr>
                        </tfoot>
                        @endif
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-tabler-layout>
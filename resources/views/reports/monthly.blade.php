<x-tabler-layout>
    <x-slot name="header">
        <div class="row align-items-center d-print-none">
            <div class="col">
                <div class="page-pretitle text-uppercase fw-bold text-muted" style="letter-spacing: 0.05em;">Financial Analysis</div>
                <h2 class="page-title h1 fw-bold">{{ __('Monthly Summary') }}</h2>
            </div>
            <div class="col-auto ms-auto">
                <div class="btn-list">
                    <a href="{{ route('reports.monthly.export', ['month' => $month, 'year' => $year, 'format' => 'pdf']) }}" class="btn btn-white shadow-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-file-download" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M14 3v4a1 1 0 0 0 1 1h4"></path><path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z"></path><path d="M12 17v-6"></path><path d="M9 14l3 3l3 -3"></path></svg>
                        Export PDF
                    </a>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="premium-header-card mb-4">
        <form action="{{ route('reports.monthly') }}" method="GET" class="row align-items-center">
            <div class="col-md-6">
                <div class="d-flex align-items-center">
                    <span class="avatar avatar-lg bg-white-lt text-white me-3 border border-white-subtle" style="backdrop-filter: blur(4px);">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-chart-bar" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><rect x="3" y="12" width="6" height="8" rx="1" /><rect x="9" y="8" width="6" height="12" rx="1" /><rect x="15" y="4" width="6" height="16" rx="1" /><line x1="4" y1="20" x2="18" y2="20" /></svg>
                    </span>
                    <div>
                        <div class="text-white-50 small text-uppercase fw-bold" style="letter-spacing: 1px;">PERIOD SELECTION</div>
                        <div class="d-flex align-items-center gap-2">
                           <select name="month" class="form-select form-select-flush fw-bold fs-2 text-white bg-transparent border-0 p-0" onchange="this.form.submit()" style="width: auto; cursor: pointer;">
                                @foreach(range(1, 12) as $m)
                                    <option value="{{ $m }}" {{ $m == $month ? 'selected' : '' }} class="text-dark">
                                        {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                                    </option>
                                @endforeach
                           </select>
                           <select name="year" class="form-select form-select-flush fw-bold fs-3 text-white-50 bg-transparent border-0 p-0" onchange="this.form.submit()" style="width: auto; cursor: pointer;">
                                @foreach(range(date('Y') - 5, date('Y') + 1) as $y)
                                    <option value="{{ $y }}" {{ $y == $year ? 'selected' : '' }} class="text-dark">{{ $y }}</option>
                                @endforeach
                           </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 text-md-end mt-4 mt-md-0">
                <div class="p-3 bg-white-lt rounded-4 border border-white-subtle d-inline-block text-start" style="backdrop-filter: blur(8px); min-width: 250px;">
                    <div class="text-white-50 small text-uppercase fw-bold mb-1">NET PERFORMANCE</div>
                    <div class="h1 mb-0 fw-bold">₹ {{ number_format($totalCollections - $totalSales, 2) }}</div>
                </div>
            </div>
        </form>
    </div>

    <!-- Monthly Highlights -->
    <div class="premium-stats-grid mb-4">
        <div class="stat-premium-card">
            <div class="stat-icon-wrapper bg-blue-lt">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon text-blue" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M17 8v-3a1 1 0 0 0 -1 -1h-10a2 2 0 0 0 0 4h12a1 1 0 0 1 1 1v3m0 4v3a1 1 0 0 1 -1 1h-12a2 2 0 0 1 -2 -2v-12" /><path d="M20 12v4h-4a2 2 0 0 1 0 -4h4" /></svg>
            </div>
            <div class="text-muted small fw-bold text-uppercase mb-1">Monthly Billing</div>
            <div class="h2 mb-0 fw-bold text-blue">₹ {{ number_format($totalSales, 2) }}</div>
            <div class="text-muted mt-2 small fw-medium">Total invoiced value</div>
        </div>

        <div class="stat-premium-card">
            <div class="stat-icon-wrapper bg-teal-lt">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon text-teal" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M7 10l5 -6l5 6" /><path d="M21 10l-2 8a2 2 0 0 1 -2 2h-10a2 2 0 0 1 -2 -2l-2 -8" /><path d="M12 10l0 10" /></svg>
            </div>
            <div class="text-muted small fw-bold text-uppercase mb-1">Receipted Cash</div>
            <div class="h2 mb-0 fw-bold text-teal">₹ {{ number_format($totalCollections, 2) }}</div>
            <div class="text-muted mt-2 small fw-medium">Total collections received</div>
        </div>

        <div class="stat-premium-card">
            <div class="stat-icon-wrapper bg-{{ ($totalCollections - $totalSales) >= 0 ? 'green' : 'red' }}-lt">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon text-{{ ($totalCollections - $totalSales) >= 0 ? 'green' : 'red' }}" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 17l6 -6l4 4l8 -8" /><path d="M14 7l7 0l0 7" /></svg>
            </div>
            <div class="text-muted small fw-bold text-uppercase mb-1">Cashflow Variance</div>
            <div class="h2 mb-0 fw-bold text-{{ ($totalCollections - $totalSales) >= 0 ? 'green' : 'red' }}">₹ {{ number_format($totalCollections - $totalSales, 2) }}</div>
            <div class="text-muted mt-2 small fw-medium">Receipts vs Billing gap</div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm overflow-hidden">
                <div class="card-header bg-transparent border-0 py-3">
                    <h3 class="card-title fw-bold">Daily Breakdown - {{ date('F Y', mktime(0, 0, 0, $month, 1, $year)) }}</h3>
                </div>
                <div class="table-responsive">
                    <table class="table table-vcenter table-premium card-table border-top">
                        <thead class="bg-light">
                            <tr>
                                <th>Operational Date</th>
                                <th class="text-end">Billing (Sales)</th>
                                <th class="text-end">Tickets</th>
                                <th class="text-end">Receipts (Cash)</th>
                                <th class="text-end">Net Position</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($reportData as $date => $data)
                                <tr>
                                    <td>
                                        <a href="{{ route('reports.daily', ['date' => $date]) }}" class="fw-bold text-dark text-decoration-none hover-text-primary">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-inline me-1 text-muted" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><rect x="4" y="5" width="16" height="16" rx="2" /><line x1="16" y1="3" x2="16" y2="7" /><line x1="8" y1="3" x2="8" y2="7" /><line x1="4" y1="11" x2="20" y2="11" /><line x1="10" y1="16" x2="14" y2="16" /></svg>
                                            {{ \Carbon\Carbon::parse($date)->format('d M, Y') }}
                                        </a>
                                    </td>
                                    <td class="text-end">
                                        <div class="fw-bold fs-3 text-dark">₹ {{ number_format($data['sales'], 2) }}</div>
                                    </td>
                                    <td class="text-end">
                                        <span class="badge bg-gray-lt text-dark px-2">{{ $data['sales_count'] }}</span>
                                    </td>
                                    <td class="text-end">
                                        <div class="fw-bold fs-3 text-teal">₹ {{ number_format($data['collections'], 2) }}</div>
                                    </td>
                                    <td class="text-end">
                                        @php $variance = $data['collections'] - $data['sales']; @endphp
                                        <div class="fw-bold fs-3 text-{{ $variance >= 0 ? 'green' : 'red' }}">
                                            {{ $variance >= 0 ? '+' : '' }} ₹ {{ number_format($variance, 2) }}
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-5 text-muted">No operational data found for this period.</td>
                                </tr>
                            @endforelse
                        </tbody>
                        <tfoot class="bg-dark text-white fw-bold">
                            <tr>
                                <td class="py-3">MONTHLY TOTALS</td>
                                <td class="text-end py-3 fs-2">₹ {{ number_format($totalSales, 2) }}</td>
                                <td class="text-end py-3"></td>
                                <td class="text-end py-3 fs-2 text-teal-light">₹ {{ number_format($totalCollections, 2) }}</td>
                                <td class="text-end py-3 fs-2 text-{{ ($totalCollections - $totalSales) >= 0 ? 'green' : 'red' }}">
                                    ₹ {{ number_format($totalCollections - $totalSales, 2) }}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-tabler-layout>
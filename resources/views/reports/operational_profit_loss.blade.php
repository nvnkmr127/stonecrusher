<x-tabler-layout>
    <x-slot name="header">
        <div class="row align-items-center mb-3">
            <div class="col">
                <div class="page-pretitle text-uppercase fw-bold text-muted" style="letter-spacing: 0.05em;">Reporting / Financials</div>
                <h2 class="page-title h1 fw-bold">Operational Profit & Loss</h2>
            </div>
        </div>
    </x-slot>

    <!-- Month Filter Card -->
    <div class="card mb-4 border-0 shadow-sm">
        <div class="card-body">
            <form action="{{ route('reports.operational-profit-loss') }}" method="GET" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label small text-muted text-uppercase fw-bold" style="letter-spacing: 0.05em;">Month</label>
                    <select name="month" class="form-select">
                        @for ($m = 1; $m <= 12; $m++)
                            <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
                                {{ Carbon\Carbon::create()->month($m)->format('F') }}
                            </option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label small text-muted text-uppercase fw-bold" style="letter-spacing: 0.05em;">Year</label>
                    <select name="year" class="form-select">
                        @for ($y = Carbon\Carbon::now()->year - 5; $y <= Carbon\Carbon::now()->year + 2; $y++)
                            <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>
                                {{ $y }}
                            </option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary w-100">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><circle cx="10" cy="10" r="7" /><line x1="21" y1="21" x2="15" y2="15" /></svg>
                        Generate Statement
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- P&L Stats Grid -->
    <div class="premium-stats-grid mb-4">
        <div class="stat-premium-card">
            <div class="stat-icon-wrapper bg-blue-lt">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon text-blue" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M17 8v-3a1 1 0 0 0 -1 -1h-10a2 2 0 0 0 0 4h12a1 1 0 0 1 1 1v3m0 4v3a1 1 0 0 1 -1 1h-12a2 2 0 0 1 -2 -2v-12" /><path d="M20 12v4h-4a2 2 0 0 1 0 -4h4" /></svg>
            </div>
            <div class="text-muted small fw-bold text-uppercase mb-1">Quarry Net Profit/Loss</div>
            <div class="h2 mb-0 fw-bold {{ $quarryData['net'] >= 0 ? 'text-teal' : 'text-orange' }}">
                ₹ {{ number_format($quarryData['net'], 2) }}
            </div>
            <div class="text-muted mt-2 small fw-medium text-nowrap">
                Rev: ₹{{ number_format($quarryData['total_revenue'], 2) }} | Exp: ₹{{ number_format($quarryData['total_expense'], 2) }}
            </div>
        </div>

        <div class="stat-premium-card">
            <div class="stat-icon-wrapper bg-indigo-lt">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon text-indigo" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M17 8v-3a1 1 0 0 0 -1 -1h-10a2 2 0 0 0 0 4h12a1 1 0 0 1 1 1v3m0 4v3a1 1 0 0 1 -1 1h-12a2 2 0 0 1 -2 -2v-12" /><path d="M20 12v4h-4a2 2 0 0 1 0 -4h4" /></svg>
            </div>
            <div class="text-muted small fw-bold text-uppercase mb-1">Crusher Net Profit/Loss</div>
            <div class="h2 mb-0 fw-bold {{ $crusherData['net'] >= 0 ? 'text-teal' : 'text-orange' }}">
                ₹ {{ number_format($crusherData['net'], 2) }}
            </div>
            <div class="text-muted mt-2 small fw-medium text-nowrap">
                Rev: ₹{{ number_format($crusherData['total_revenue'], 2) }} | Exp: ₹{{ number_format($crusherData['total_expense'], 2) }}
            </div>
        </div>

        <div class="stat-premium-card">
            <div class="stat-icon-wrapper {{ $overallNet >= 0 ? 'bg-green-lt text-green' : 'bg-red-lt text-red' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><circle cx="12" cy="12" r="9" /><path d="M14.8 9a2 2 0 0 0 -1.8 -1h-2a2 2 0 0 0 0 4h2a2 2 0 0 1 0 4h-2a2 2 0 0 1 -1.8 -1" /><path d="M12 6v2m0 8v2" /></svg>
            </div>
            <div class="text-muted small fw-bold text-uppercase mb-1">Overall Consolidated Net</div>
            <div class="h2 mb-0 fw-bold {{ $overallNet >= 0 ? 'text-green' : 'text-danger' }}">
                ₹ {{ number_format($overallNet, 2) }}
            </div>
            <div class="text-muted mt-2 small fw-medium">Consolidated Operations P&L</div>
        </div>
    </div>

    <!-- Side-by-Side Breakdown -->
    <div class="row row-cards">
        <!-- Quarry Details Card -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-0 py-3">
                    <h3 class="card-title fw-bold">Quarry Operations - Itemized P&L</h3>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-vcenter table-premium card-table border-top mb-0">
                            <thead>
                                <tr class="bg-light">
                                    <th>Item / Category</th>
                                    <th class="text-end">Amount (₹)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Revenues Section -->
                                <tr class="bg-green-lt text-green font-weight-bold">
                                    <td colspan="2" class="text-uppercase small tracking-wide px-3 py-2">REVENUES</td>
                                </tr>
                                @forelse($quarryData['revenues'] as $item)
                                    <tr>
                                        <td class="px-3">{{ $item->name }}</td>
                                        <td class="text-end text-green fw-semibold px-3">+ ₹{{ number_format($item->amount, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2" class="text-center text-muted small py-3 px-3">No revenue items recorded.</td>
                                    </tr>
                                @endforelse
                                <tr class="border-bottom">
                                    <td class="text-muted small text-uppercase font-weight-semibold px-3">Total Quarry Revenue</td>
                                    <td class="text-end text-green fw-bold fs-3 px-3">₹{{ number_format($quarryData['total_revenue'], 2) }}</td>
                                </tr>

                                <!-- Expenses Section -->
                                <tr class="bg-red-lt text-red font-weight-bold">
                                    <td colspan="2" class="text-uppercase small tracking-wide px-3 py-2">EXPENSES</td>
                                </tr>
                                @forelse($quarryData['expenses'] as $item)
                                    <tr>
                                        <td class="px-3">{{ $item->name }}</td>
                                        <td class="text-end text-danger fw-semibold px-3">- ₹{{ number_format($item->amount, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2" class="text-center text-muted small py-3 px-3">No expense items recorded.</td>
                                    </tr>
                                @endforelse
                                <tr class="border-bottom">
                                    <td class="text-muted small text-uppercase font-weight-semibold px-3">Total Quarry Expense</td>
                                    <td class="text-end text-danger fw-bold fs-3 px-3">₹{{ number_format($quarryData['total_expense'], 2) }}</td>
                                </tr>

                                <!-- Net Summary -->
                                <tr class="bg-dark text-white">
                                    <td class="text-uppercase small tracking-wider fw-bold text-white px-3 py-3">Quarry Net Profit/Loss</td>
                                    <td class="text-end fw-bold fs-2 text-white px-3 py-3">₹{{ number_format($quarryData['net'], 2) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Crusher Details Card -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-0 py-3">
                    <h3 class="card-title fw-bold">Crusher Operations - Itemized P&L</h3>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-vcenter table-premium card-table border-top mb-0">
                            <thead>
                                <tr class="bg-light">
                                    <th>Item / Category</th>
                                    <th class="text-end">Amount (₹)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Revenues Section -->
                                <tr class="bg-green-lt text-green font-weight-bold">
                                    <td colspan="2" class="text-uppercase small tracking-wide px-3 py-2">REVENUES</td>
                                </tr>
                                @forelse($crusherData['revenues'] as $item)
                                    <tr>
                                        <td class="px-3">{{ $item->name }}</td>
                                        <td class="text-end text-green fw-semibold px-3">+ ₹{{ number_format($item->amount, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2" class="text-center text-muted small py-3 px-3">No revenue items recorded.</td>
                                    </tr>
                                @endforelse
                                <tr class="border-bottom">
                                    <td class="text-muted small text-uppercase font-weight-semibold px-3">Total Crusher Revenue</td>
                                    <td class="text-end text-green fw-bold fs-3 px-3">₹{{ number_format($crusherData['total_revenue'], 2) }}</td>
                                </tr>

                                <!-- Expenses Section -->
                                <tr class="bg-red-lt text-red font-weight-bold">
                                    <td colspan="2" class="text-uppercase small tracking-wide px-3 py-2">EXPENSES</td>
                                </tr>
                                @forelse($crusherData['expenses'] as $item)
                                    <tr>
                                        <td class="px-3">{{ $item->name }}</td>
                                        <td class="text-end text-danger fw-semibold px-3">- ₹{{ number_format($item->amount, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2" class="text-center text-muted small py-3 px-3">No expense items recorded.</td>
                                    </tr>
                                @endforelse
                                <tr class="border-bottom">
                                    <td class="text-muted small text-uppercase font-weight-semibold px-3">Total Crusher Expense</td>
                                    <td class="text-end text-danger fw-bold fs-3 px-3">₹{{ number_format($crusherData['total_expense'], 2) }}</td>
                                </tr>

                                <!-- Net Summary -->
                                <tr class="bg-dark text-white">
                                    <td class="text-uppercase small tracking-wider fw-bold text-white px-3 py-3">Crusher Net Profit/Loss</td>
                                    <td class="text-end fw-bold fs-2 text-white px-3 py-3">₹{{ number_format($crusherData['net'], 2) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-tabler-layout>

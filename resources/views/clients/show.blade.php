<x-tabler-layout>
    <x-slot name="header">
        <div class="row g-2 align-items-center d-print-none">
            <div class="col">
                <div class="page-pretitle text-uppercase fw-bold text-muted" style="letter-spacing: 0.05em;">Client CRM</div>
                <h2 class="page-title h1 fw-bold">{{ $client->name }}</h2>
            </div>
            <div class="col-auto ms-auto">
                <div class="btn-list">
                    <button onclick="window.print()" class="btn btn-white shadow-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-printer" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M17 17h2a2 2 0 0 0 2 -2v-4a2 2 0 0 0 -2 -2h-14a2 2 0 0 0 -2 2v4a2 2 0 0 0 2 2h2"></path><path d="M17 9v-4a2 2 0 0 0 -2 -2h-6a2 2 0 0 0 -2 2v4"></path><path d="M7 13m0 2a2 2 0 0 1 2 -2h6a2 2 0 0 1 2 2v4a2 2 0 0 1 -2 2h-6a2 2 0 0 1 -2 -2z"></path></svg>
                        Print Statement
                    </button>
                    <a href="{{ route('clients.transactions.create', $client) }}" class="btn btn-primary shadow-sm px-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-plus" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M12 5l0 14"></path><path d="M5 12l14 0"></path></svg>
                        New Transaction
                    </a>
                </div>
            </div>
        </div>
    </x-slot>

    <!-- Header Summary Card -->
    <div class="premium-header-card">

        <div class="row align-items-center">
            <div class="col-md-7">
                <div class="d-flex align-items-center mb-3">
                    <div class="avatar avatar-xl rounded-circle bg-primary-lt text-primary fw-bold me-3 border border-2 border-white shadow-sm">
                        {{ substr($client->name, 0, 2) }}
                    </div>
                    <div>
                        <h1 class="mb-1 fw-bold text-white">{{ $client->name }}</h1>
                        <div class="d-flex flex-wrap gap-3 mt-1">
                            <span class="d-flex align-items-center text-white-50 small">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-phone me-1" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M5 4h4l2 5l-2.5 1.5a11 11 0 0 0 5 5l1.5 -2.5l5 2v4a2 2 0 0 1 -2 2a16 16 0 0 1 -15 -15a2 2 0 0 1 2 -2"></path></svg>
                                {{ $client->phone ?? 'No phone' }}
                            </span>
                            <span class="d-flex align-items-center text-white-50 small">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-mail me-1" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M3 7a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v10a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2v-10z"></path><path d="M3 7l9 6l9 -6"></path></svg>
                                {{ $client->email ?? 'No email' }}
                            </span>
                            <span class="d-flex align-items-center text-white-50 small">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-map-pin me-1" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M9 11a3 3 0 1 0 6 0a3 3 0 0 0 -6 0"></path><path d="M17.657 16.657l-4.243 4.243a2 2 0 0 1 -2.827 0l-4.244 -4.244a8 8 0 1 1 11.314 0z"></path></svg>
                                {{ $client->address ?? 'No address set' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-5 text-md-end mt-4 mt-md-0">
                <div class="d-inline-block p-4 rounded-3" style="background: rgba(255,255,255,0.05); backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.1);">
                    @php
                        $balance = $client->balance;
                        $isPositive = $balance >= 0;
                        $themeColor = $isPositive ? '#10b981' : '#ef4444';
                        $themeBg = $isPositive ? 'rgba(16, 185, 129, 0.1)' : 'rgba(239, 68, 68, 0.1)';
                    @endphp
                    <div class="small text-white-50 text-uppercase fw-bold mb-1" style="letter-spacing: 1px;">Closing Balance</div>
                    <div class="h1 mb-0 fw-bold d-flex align-items-center justify-content-md-end">
                        <span style="color: {{ $themeColor }};">₹ {{ number_format(abs($balance), 2) }}</span>
                    </div>
                    <div class="mt-2">
                        <span class="premium-balance-pill" style="background: {{ $themeBg }}; color: {{ $themeColor }};">
                            {{ $isPositive ? 'Advance / Excess' : 'Outstanding / Due' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Month Wise Quick Stats -->
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h3 class="mb-0 fw-bold d-flex align-items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-chart-bar me-2 text-primary" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M3 12m0 1a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v6a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z"></path><path d="M9 8m0 1a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v10a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z"></path><path d="M15 4m0 1a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v14a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z"></path><path d="M4 20l14 0"></path></svg>
            Monthly Performance
        </h3>
        <div class="card-actions d-print-none">
            <form method="GET" action="{{ route('clients.show', $client) }}" id="monthFilterForm" class="premium-month-selector">
                <span class="px-2 text-muted small fw-bold">SELECT MONTH:</span>
                <select name="month" class="form-select form-select-sm border-0 bg-transparent fw-bold" onchange="this.form.submit()" style="min-width: 150px; cursor: pointer;">
                    @foreach($monthList as $value => $label)
                        <option value="{{ $value }}" {{ $selectedMonth == $value ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </form>
        </div>
    </div>

    <div class="premium-stats-grid">
        <div class="stat-premium-card">
            <div class="stat-icon-wrapper bg-blue-lt">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon text-blue" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M7 17m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" /><path d="M17 17m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" /><path d="M5 17h-2v-4m-1 -8h11v12m-4 0h6m4 0h2v-6h-8m0 -5h5l3 5" /></svg>
            </div>
            <div class="text-muted small fw-bold text-uppercase mb-1" style="letter-spacing: 0.5px;">Total Trips</div>
            <div class="h2 mb-0 fw-bold">{{ $monthlyStats['trips'] }}</div>
            <div class="text-muted small mt-2">In current month</div>
        </div>

        <div class="stat-premium-card">
            <div class="stat-icon-wrapper bg-green-lt">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon text-green" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 3l8 4.5l0 9l-8 4.5l-8 -4.5l0 -9l8 -4.5" /><path d="M12 12l8 -4.5" /><path d="M12 12l0 9" /><path d="M12 12l-8 -4.5" /></svg>
            </div>
            <div class="text-muted small fw-bold text-uppercase mb-1" style="letter-spacing: 0.5px;">Material Volume</div>
            <div class="h2 mb-0 fw-bold">{{ number_format($monthlyStats['quantity'], 2) }} <span class="fs-6 text-muted fw-normal">CFT</span></div>
            <div class="text-muted small mt-2">Delivered so far</div>
        </div>

        <div class="stat-premium-card">
            <div class="stat-icon-wrapper bg-orange-lt">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon text-orange" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M14 3v4a1 1 0 0 0 1 1h4" /><path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z" /><path d="M9 15l2 2l4 -4" /></svg>
            </div>
            <div class="text-muted small fw-bold text-uppercase mb-1" style="letter-spacing: 0.5px;">Billed Amount</div>
            <div class="h2 mb-0 fw-bold text-orange">₹ {{ number_format($monthlyStats['bill'], 2) }}</div>
            <div class="text-muted small mt-2">Total month revenue</div>
        </div>

        <div class="stat-premium-card">
            <div class="stat-icon-wrapper bg-azure-lt">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon text-azure" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M17 8v-3a1 1 0 0 0 -1 -1h-10a2 2 0 0 0 0 4h12a1 1 0 0 1 1 1v3m0 4v3a1 1 0 0 1 -1 1h-12a2 2 0 0 1 -2 -2v-12" /><path d="M20 12v4h-4a2 2 0 0 1 0 -4h4" /></svg>
            </div>
            <div class="text-muted small fw-bold text-uppercase mb-1" style="letter-spacing: 0.5px;">Collections</div>
            <div class="h2 mb-0 fw-bold text-azure">₹ {{ number_format($monthlyStats['paid'], 2) }}</div>
            <div class="text-muted small mt-2">Total month received</div>
        </div>
    </div>

    <!-- Content Tabs -->
    <ul class="nav nav-tabs nav-tabs-premium d-print-none" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="ledger-tab" data-bs-toggle="tab" data-bs-target="#ledger-content" type="button" role="tab">
                Transaction Ledger
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="trips-tab" data-bs-toggle="tab" data-bs-target="#trips-content" type="button" role="tab">
                Gate Passes & Trips ({{ $totalTrips }})
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile-content" type="button" role="tab">
                Client Profile
            </button>
        </li>
    </ul>

    <div class="tab-content mt-3">
        <!-- Ledger Tab -->
        <div class="tab-pane fade show active" id="ledger-content" role="tabpanel">
            <div class="card border-0 shadow-sm overflow-hidden">
                <div class="card-header border-0 bg-transparent py-3">
                    <div class="row align-items-center">
                        <div class="col">
                            <h3 class="card-title fw-bold">Recent Transactions</h3>
                        </div>
                        <div class="col-auto d-print-none">
                            <form method="GET" action="{{ route('clients.show', $client) }}" class="d-flex gap-2">
                                <input type="hidden" name="month" value="{{ $selectedMonth }}">
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text bg-white border-end-0"><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-calendar-event" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M4 5m0 2a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2z"></path><path d="M16 3l0 4"></path><path d="M8 3l0 4"></path><path d="M4 11l16 0"></path><path d="M8 15l2 2l4 -4"></path></svg></span>
                                    <input type="date" name="start_date" class="form-control border-start-0" value="{{ request('start_date') }}" placeholder="From">
                                </div>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text bg-white border-end-0"><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-calendar-event" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M4 5m0 2a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2z"></path><path d="M16 3l0 4"></path><path d="M8 3l0 4"></path><path d="M4 11l16 0"></path><path d="M8 15l2 2l4 -4"></path></svg></span>
                                    <input type="date" name="end_date" class="form-control border-start-0" value="{{ request('end_date') }}" placeholder="To">
                                </div>
                                <button type="submit" class="btn btn-sm btn-primary">Filter</button>
                                @if(request('start_date'))
                                    <a href="{{ route('clients.show', $client) }}" class="btn btn-sm btn-light">Reset</a>
                                @endif
                            </form>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-vcenter table-premium card-table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Details</th>
                                <th>Ref / Mode</th>
                                <th class="text-end">Credit (In)</th>
                                <th class="text-end">Debit (Out)</th>
                                <th class="text-end d-print-none">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($transactions as $txn)
                                <tr>
                                    <td class="small fw-bold">{{ $txn->transaction_date->format('d M, Y') }}</td>
                                    <td>
                                        <div class="fw-bold">{{ $txn->description ?? 'No Description' }}</div>
                                        <div class="text-muted small text-uppercase" style="font-size: 0.65rem; letter-spacing: 0.05em;">
                                            {{ $txn->transaction_type }}
                                        </div>
                                    </td>
                                    <td>
                                        <div class="small fw-medium">{{ $txn->payment_mode ?? '-' }}</div>
                                        <div class="text-muted small">{{ $txn->reference_number ?? '-' }}</div>
                                    </td>
                                    <td class="text-end">
                                        @if($txn->transaction_type === 'credit')
                                            <span class="text-green fw-bold">₹ {{ number_format($txn->amount, 2) }}</span>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        @if($txn->transaction_type === 'debit')
                                            <span class="text-red fw-bold">₹ {{ number_format($txn->amount, 2) }}</span>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="text-end d-print-none">
                                        @if(auth()->user()->hasRole('admin'))
                                            <div class="d-flex justify-content-end gap-1">
                                                <a href="{{ route('clients.transactions.edit', [$client, $txn]) }}" class="btn btn-premium-action btn-ghost-primary" title="Edit">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-pencil" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M4 20h4l10.5 -10.5a1.5 1.5 0 0 0 -4 -4l-10.5 10.5v4"></path><line x1="13.5" y1="6.5" x2="17.5" y2="10.5"></line></svg>
                                                </a>
                                                <form action="{{ route('clients.transactions.destroy', [$client, $txn]) }}" method="POST" onsubmit="return confirm('Delete this transaction?')">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="btn btn-premium-action btn-ghost-danger" title="Delete">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-trash" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><line x1="4" y1="7" x2="20" y2="7"></line><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line><path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12"></path><path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3"></path></svg>
                                                    </button>
                                                </form>
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5">
                                        <div class="text-muted mb-2">No transactions matching your criteria.</div>
                                        <a href="{{ route('clients.transactions.create', $client) }}" class="btn btn-sm btn-primary">Record First Transaction</a>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="card-footer bg-transparent border-0 d-flex justify-content-between align-items-center">
                    <div class="text-muted small">Showing {{ $transactions->firstItem() ?? 0 }} to {{ $transactions->lastItem() ?? 0 }} of {{ $transactions->total() }} records</div>
                    {{ $transactions->links() }}
                </div>
            </div>
        </div>

        <!-- Trips Tab -->
        <div class="tab-pane fade" id="trips-content" role="tabpanel">
            <div class="card border-0 shadow-sm">
                <div class="table-responsive">
                    <table class="table table-vcenter table-premium card-table">
                        <thead>
                            <tr>
                                <th>GP #</th>
                                <th>Date & Time</th>
                                <th>Vehicle Info</th>
                                <th>Material</th>
                                <th class="text-end">Quantity</th>
                                <th class="text-end">Total Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($gatePasses as $gp)
                                <tr>
                                    <td>
                                        <a href="{{ route('gate-passes.show', $gp) }}" class="fw-bold text-primary">{{ $gp->gate_pass_number }}</a>
                                    </td>
                                    <td class="small fw-medium">{{ $gp->date->format('d M, Y') }} <span class="text-muted">{{ $gp->date->format('h:i A') }}</span></td>
                                    <td>
                                        <div class="fw-bold">{{ $gp->vehicle->registration_number ?? $gp->manual_vehicle_number ?? '-' }}</div>
                                        <div class="text-muted small">{{ $gp->vehicle->driver_name ?? '-' }}</div>
                                    </td>
                                    <td>
                                        <span class="badge border text-dark fw-medium">{{ $gp->metalType->name ?? 'Standard' }}</span>
                                    </td>
                                    <td class="text-end fw-bold">{{ number_format($gp->net_weight, 2) }} <span class="text-muted fw-normal">CFT</span></td>
                                    <td class="text-end text-primary fw-bold">₹ {{ number_format($gp->total_amount, 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5">
                                        <div class="text-muted">No trips recorded for this client.</div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="card-footer bg-transparent border-0">
                    {{ $gatePasses->links() }}
                </div>
            </div>
        </div>

        <!-- Profile Tab -->
        <div class="tab-pane fade" id="profile-content" role="tabpanel">
            <div class="row g-4">
                <div class="col-md-8">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header border-0 bg-transparent">
                            <h3 class="card-title fw-bold">General Information</h3>
                            <div class="card-actions">
                                <a href="{{ route('clients.edit', $client) }}" class="btn btn-sm btn-white">Edit Profile</a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row g-4">
                                <div class="col-sm-6">
                                    <div class="text-muted small text-uppercase fw-bold mb-1">Company / Name</div>
                                    <div class="fw-bold h4">{{ $client->name }}</div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="text-muted small text-uppercase fw-bold mb-1">Credit Limit</div>
                                    <div class="fw-bold h4">₹ {{ number_format($client->credit_limit ?? 0, 2) }}</div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="text-muted small text-uppercase fw-bold mb-1">Registered On</div>
                                    <div class="fw-bold h4">{{ $client->created_at->format('d M, Y') }}</div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="text-muted small text-uppercase fw-bold mb-1">Status</div>
                                    <div>
                                        <span class="badge {{ $client->is_active ? 'bg-green' : 'bg-red' }}-lt px-3">
                                            {{ $client->is_active ? 'ACTIVE' : 'INACTIVE' }}
                                        </span>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="text-muted small text-uppercase fw-bold mb-1">Notes</div>
                                    <div class="p-3 bg-light rounded">{{ $client->notes ?? 'No internal notes added.' }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm bg-primary text-white h-100 overflow-hidden" style="min-height: 200px;">
                        <div class="card-body relative" style="z-index: 1;">
                            <h3 class="card-title fw-bold mb-4">Lifetime Analytics</h3>
                            <div class="mb-4">
                                <div class="text-white-50 small text-uppercase fw-bold mb-1">Total Purchased</div>
                                <div class="h1 fw-bold mb-0">{{ number_format($totalCft, 2) }} CFT</div>
                            </div>
                            <div>
                                <div class="text-white-50 small text-uppercase fw-bold mb-1">Business Volume</div>
                                <div class="h1 fw-bold mb-0">₹ {{ number_format($client->transactions()->where('transaction_type', 'debit')->sum('amount'), 2) }}</div>
                            </div>
                        </div>
                        <div class="position-absolute bottom-0 end-0 p-3 opacity-10">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-chart-dots" width="120" height="120" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M3 3v18h18"></path><circle cx="9" cy="15" r="2"></circle><circle cx="13" cy="5" r="2"></circle><circle cx="18" cy="12" r="2"></circle></svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Tab preservation
            const hash = window.location.hash;
            if (hash) {
                const triggerEl = document.querySelector(`.nav-tabs-premium button[data-bs-target="${hash}-content"]`);
                if (triggerEl) {
                    const tab = new bootstrap.Tab(triggerEl);
                    tab.show();
                }
            }

            const tabEls = document.querySelectorAll('.nav-tabs-premium button[data-bs-toggle="tab"]');
            tabEls.forEach(el => {
                el.addEventListener('shown.bs.tab', function(event) {
                    const target = event.target.getAttribute('data-bs-target').replace('-content', '');
                    window.location.hash = target;
                });
            });
        });
    </script>
    @endpush

</x-tabler-layout>
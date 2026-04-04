<x-tabler-layout>
    <x-slot name="header">
        <x-page-header title="Clients" subtitle="Manage client information">
            <x-slot name="actions">
                <a href="{{ route('clients.create') }}" class="btn btn-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24"
                        stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round"
                        stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path d="M12 5l0 14" />
                        <path d="M5 12l14 0" />
                    </svg>
                    Add Client
                </a>
            </x-slot>
        </x-page-header>
    </x-slot>

    <div class="row row-cards mb-4">
        <div class="col-sm-6 col-lg-3">
            <div class="stat-premium-card h-100">
                <div class="stat-icon-wrapper bg-blue-lt">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon text-blue" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10 13a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" /><path d="M8 21v-1a2 2 0 0 1 2 -2h4a2 2 0 0 1 2 2v1" /><path d="M15 5a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" /><path d="M17 10h2a2 2 0 0 1 2 2v1" /><path d="M5 5a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" /><path d="M3 13v-1a2 2 0 0 1 2 -2h2" /></svg>
                </div>
                <div class="text-muted small fw-bold text-uppercase mb-1" style="letter-spacing: 0.5px;">All Clients</div>
                <div class="h2 mb-0 fw-bold">{{ $summary['total'] }}</div>
                <div class="text-muted small mt-2">{{ $summary['active'] }} Active accounts</div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="stat-premium-card h-100">
                <div class="stat-icon-wrapper bg-red-lt">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon text-red" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M7 6l10 12l-8 -8l8 8" /><path d="M12 21l-9 -9l9 -9l9 9l-9 9" /></svg>
                </div>
                <div class="text-muted small fw-bold text-uppercase mb-1" style="letter-spacing: 0.5px;">Money to Get</div>
                <div class="h2 mb-0 fw-bold text-red">₹ {{ number_format($summary['receivable'], 0) }}</div>
                <div class="text-muted small mt-2">Total bill amount pending</div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="stat-premium-card h-100">
                <div class="stat-icon-wrapper bg-green-lt">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon text-green" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M17 8l-7 7l-3 -3" /><path d="M9 11v-7a2 2 0 0 1 2 -2h4a2 2 0 0 1 2 2v7" /><path d="M10 21l-6 -6l1.41 -1.41l4.59 4.59l9.59 -9.59l1.41 1.41l-11 11" /></svg>
                </div>
                <div class="text-muted small fw-bold text-uppercase mb-1" style="letter-spacing: 0.5px;">Advance Money</div>
                <div class="h2 mb-0 fw-bold text-green">₹ {{ number_format($summary['advance'], 0) }}</div>
                <div class="text-muted small mt-2">Total client advances</div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="stat-premium-card h-100 border-primary-lt">
                <div class="stat-icon-wrapper bg-purple-lt">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon text-purple" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 3l8 4.5l0 9l-8 4.5l-8 -4.5l0 -9l8 -4.5" /><path d="M12 12l8 -4.5" /></svg>
                </div>
                <div class="text-muted small fw-bold text-uppercase mb-1" style="letter-spacing: 0.5px;">Business Balance</div>
                <div class="h2 mb-0 fw-bold {{ $summary['receivable'] > $summary['advance'] ? 'text-red' : 'text-green' }}">
                    ₹ {{ number_format(abs($summary['receivable'] - $summary['advance']), 0) }}
                </div>
                <div class="text-muted small mt-2">{{ $summary['receivable'] > $summary['advance'] ? 'Total Debt' : 'Total Surplus' }}</div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm overflow-hidden">
        <div class="card-header border-0 bg-transparent py-3">
            <div class="row align-items-center">
                <div class="col">
                    <h3 class="card-title fw-bold">Client Directory</h3>
                </div>
                <div class="col-auto">
                    <form method="GET" action="{{ route('clients.index') }}" class="input-group input-group-sm input-group-flat" style="width: 250px;">
                        <input type="text" name="search" value="{{ request('search') }}"
                            class="form-control ps-2" aria-label="Search client"
                            placeholder="Find by name, phone...">
                        <span class="input-group-text">
                            <button type="submit" class="btn btn-sm btn-ghost-primary p-0 border-0">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-search" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><circle cx="10" cy="10" r="7"></circle><line x1="21" y1="21" x2="15" y2="15"></line></svg>
                            </button>
                        </span>
                    </form>
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-vcenter table-premium card-table">
                <thead>
                    <tr>
                        <th style="width: 25%">Client Name</th>
                        <th>Phone / Email</th>
                        <th>Status</th>
                        <th class="text-center">This Month Info</th>
                        <th class="text-end">Final Balance</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($clients as $client)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-sm bg-primary-lt text-primary fw-bold me-2">{{ substr($client->name, 0, 1) }}</div>
                                    <div>
                                        <a href="{{ route('clients.show', $client) }}" class="fw-bold text-reset">{{ $client->name }}</a>
                                        <div class="small text-muted">{{ $client->created_at->format('M Y') }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="small fw-medium">{{ $client->phone ?? 'No phone' }}</div>
                                <div class="text-muted small">{{ $client->email ?? 'No email' }}</div>
                            </td>
                            <td>
                                <x-status-badge :status="$client->is_active ? 'active' : 'inactive'" />
                            </td>
                            <td class="text-center">
                                @php
                                    $monthBill = $client->current_month_bill ?? 0;
                                    $monthPaid = $client->current_month_paid ?? 0;
                                @endphp
                                <div class="d-flex flex-column align-items-center">
                                    <div class="d-flex gap-2">
                                        <span class="badge bg-red-lt border-0" title="Billed this month">₹ {{ number_format($monthBill, 0) }}</span>
                                        <span class="badge bg-green-lt border-0" title="Paid this month">₹ {{ number_format($monthPaid, 0) }}</span>
                                    </div>
                                    <div class="text-muted small mt-1" style="font-size: 0.6rem;">BILL / PAID</div>
                                </div>
                            </td>
                            <td class="text-end">
                                @php
                                    $balance = ($client->total_credit ?? 0) - ($client->total_debit ?? 0);
                                    $color = $balance >= 0 ? 'text-green' : 'text-red';
                                @endphp
                                <div class="fw-bold {{ $color }}">
                                    ₹ {{ number_format(abs($balance), 2) }}
                                </div>
                                <div class="small text-muted text-uppercase" style="font-size: 0.6rem;">{!! $balance >= 0 ? 'Advance' : 'Pending' !!}</div>
                            </td>
                            <td class="text-end">
                                <div class="d-flex justify-content-end gap-1">
                                    <a href="{{ route('clients.show', $client) }}" class="btn btn-premium-action btn-ghost-primary" title="Ledger">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-books" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><rect x="5" y="4" width="4" height="16" rx="1"></rect><rect x="9" y="4" width="4" height="16" rx="1"></rect><path d="M13 5l3 11l-2 2"></path><line x1="5" y1="8" x2="9" y2="8"></line></svg>
                                    </a>
                                    <a href="{{ route('clients.edit', $client) }}" class="btn btn-premium-action btn-ghost-primary" title="Edit">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-pencil" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M4 20h4l10.5 -10.5a1.5 1.5 0 0 0 -4 -4l-10.5 10.5v4"></path><line x1="13.5" y1="6.5" x2="17.5" y2="10.5"></line></svg>
                                    </a>
                                    <form action="{{ route('clients.destroy', $client) }}" method="POST" onsubmit="return confirm('Remove this client?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-premium-action btn-ghost-danger" title="Delete">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-trash" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><line x1="4" y1="7" x2="20" y2="7"></line><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line><path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12"></path><path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3"></path></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <div class="text-muted mb-2">No clients found.</div>
                                <a href="{{ route('clients.create') }}" class="btn btn-sm btn-primary px-4">Register First Client</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer bg-transparent border-0">
            {{ $clients->links() }}
        </div>
    </div>
</x-tabler-layout>
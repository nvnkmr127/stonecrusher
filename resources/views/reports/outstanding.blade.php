<x-tabler-layout>
    <x-slot name="header">
        <div class="row align-items-center">
            <div class="col">
                <div class="page-pretitle text-uppercase fw-bold text-muted" style="letter-spacing: 0.05em;">Credit Management</div>
                <h2 class="page-title h1 fw-bold">{{ __('Outstanding Ledger') }}</h2>
            </div>
        </div>
    </x-slot>

    <div class="premium-header-card mb-4">
        <div class="row align-items-center">
            <div class="col-md-8">
                <div class="d-flex align-items-center">
                    <span class="avatar avatar-lg bg-white-lt text-white me-3 border border-white-subtle" style="backdrop-filter: blur(4px);">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-wallet" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M17 8v-3a1 1 0 0 0 -1 -1h-10a2 2 0 0 0 0 4h12a1 1 0 0 1 1 1v3m0 4v3a1 1 0 0 1 -1 1h-12a2 2 0 0 1 -2 -2v-12" /><path d="M20 12v4h-4a2 2 0 0 1 0 -4h4" /></svg>
                    </span>
                    <div>
                        <div class="text-white-50 small text-uppercase fw-bold" style="letter-spacing: 1px;">LEDGER OVERVIEW</div>
                        <h1 class="mb-0 fw-bold">Receivables Analysis</h1>
                        <div class="text-white-50">Current balance status of all clients across the system.</div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 text-md-end mt-4 mt-md-0">
                <div class="p-3 bg-white-lt rounded-4 border border-white-subtle d-inline-block text-start" style="backdrop-filter: blur(8px);">
                    <div class="text-white-50 small text-uppercase fw-bold mb-1">Total Net Balance</div>
                    <div class="h1 mb-0 fw-bold">₹ {{ number_format($advanceClients->sum('current_balance') - abs($outstandingClients->sum('current_balance')), 2) }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Summary Stats -->
    <div class="premium-stats-grid mb-4">
        <div class="stat-premium-card border-bottom border-danger border-3">
            <div class="stat-icon-wrapper bg-red-lt">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon text-red" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" /><path d="M12 8l0 4" /><path d="M12 16l.01 0" /></svg>
            </div>
            <div class="text-muted small fw-bold text-uppercase mb-1">Total Outstanding</div>
            <div class="h2 mb-0 fw-bold text-red">₹ {{ number_format(abs($outstandingClients->sum('current_balance')), 2) }}</div>
            <div class="text-muted mt-2 small fw-medium">{{ $outstandingClients->count() }} clients with debt</div>
        </div>

        <div class="stat-premium-card border-bottom border-green border-3">
            <div class="stat-icon-wrapper bg-green-lt">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon text-green" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" /><path d="M9 12l2 2l4 -4" /></svg>
            </div>
            <div class="text-muted small fw-bold text-uppercase mb-1">Total Advances</div>
            <div class="h2 mb-0 fw-bold text-green">₹ {{ number_format($advanceClients->sum('current_balance'), 2) }}</div>
            <div class="text-muted mt-2 small fw-medium">{{ $advanceClients->count() }} clients in credit</div>
        </div>
    </div>

    <!-- Tables -->
    <div class="row row-cards">
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-transparent border-0 py-3">
                    <h3 class="card-title fw-bold text-danger">Debtor List (Owed to Us)</h3>
                </div>
                <div class="table-responsive">
                    <table class="table table-vcenter table-premium card-table border-top">
                        <thead class="bg-light">
                            <tr>
                                <th>Client Name</th>
                                <th class="text-end">Due Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($outstandingClients as $client)
                                <tr>
                                    <td>
                                        <div class="fw-bold text-dark">{{ $client->name }}</div>
                                        <div class="small text-muted">{{ $client->phone ?? 'No Contact' }}</div>
                                    </td>
                                    <td class="text-end fw-bold text-red fs-3">
                                        ₹ {{ number_format(abs($client->current_balance), 2) }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="text-center py-5 text-muted">Excellent! No outstanding balances.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-transparent border-0 py-3">
                    <h3 class="card-title fw-bold text-green">Creditor List (Advance Paid)</h3>
                </div>
                <div class="table-responsive">
                    <table class="table table-vcenter table-premium card-table border-top">
                        <thead class="bg-light">
                            <tr>
                                <th>Client Name</th>
                                <th class="text-end">Credit Balance</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($advanceClients as $client)
                                <tr>
                                    <td>
                                        <div class="fw-bold text-dark">{{ $client->name }}</div>
                                        <div class="small text-muted">{{ $client->phone ?? 'No Contact' }}</div>
                                    </td>
                                    <td class="text-end fw-bold text-green fs-3">
                                        ₹ {{ number_format($client->current_balance, 2) }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="text-center py-5 text-muted">No client advance balances found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-tabler-layout>
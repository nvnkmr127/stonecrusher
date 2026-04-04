<x-tabler-layout>
    <x-slot name="header">
        <div class="row align-items-center mb-3">
            <div class="col">
                <div class="page-pretitle text-uppercase fw-bold text-muted" style="letter-spacing: 0.05em;">Administration</div>
                <h2 class="page-title h1 fw-bold">{{ __('Dashboard') }}</h2>
            </div>
        </div>
    </x-slot>

    <div class="premium-header-card mb-4">
        <div class="row align-items-center">
            <div class="col-md-8">
                <div class="d-flex align-items-center mb-3">
                    <span class="avatar avatar-xl bg-white-lt text-white me-4 border border-white-subtle" style="backdrop-filter: blur(4px);">
                        {{ substr(Auth::user()->name, 0, 1) }}
                    </span>
                    <div>
                        <div class="text-white-50 small text-uppercase fw-bold" style="letter-spacing: 1px;">WELCOME BACK</div>
                        <h1 class="mb-0 fw-bold">{{ Auth::user()->name }}</h1>
                        <div class="text-white-50">{{ Auth::user()->email }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 text-md-end">
                <div class="p-3 bg-white-lt rounded-4 border border-white-subtle" style="backdrop-filter: blur(8px);">
                    <div class="text-white-50 small text-uppercase fw-bold mb-1">System Role</div>
                    <div class="h2 mb-0 fw-bold">
                        <span class="badge bg-green text-white text-uppercase px-3 py-2" style="font-size: 0.7rem;">
                            {{ ucfirst(Auth::user()->getRoleNames()->first() ?? 'Administrator') }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row row-cards">
        <div class="col-md-6 col-lg-4">
            <div class="card border-0 shadow-sm h-100 overflow-hidden">
                <div class="card-header bg-transparent border-0 py-3">
                    <h3 class="card-title fw-bold">Your Profile</h3>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center mb-4">
                        <div class="stat-icon-wrapper bg-blue-lt text-blue me-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-user" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><circle cx="12" cy="7" r="4" /><path d="M6 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" /></svg>
                        </div>
                        <div>
                            <div class="text-muted small fw-bold text-uppercase">Account Name</div>
                            <div class="fw-bold">{{ Auth::user()->name }}</div>
                        </div>
                    </div>
                    <div class="d-flex align-items-center">
                        <div class="stat-icon-wrapper bg-purple-lt text-purple me-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-mail" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><rect x="3" y="5" width="18" height="14" rx="2" /><polyline points="3 7 12 13 21 7" /></svg>
                        </div>
                        <div>
                            <div class="text-muted small fw-bold text-uppercase">Email Address</div>
                            <div class="fw-bold">{{ Auth::user()->email }}</div>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-light border-0">
                    <a href="{{ route('profile.edit') }}" class="btn btn-primary w-100 shadow-sm">Manage Profile</a>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-8">
            <div class="card border-0 shadow-sm h-100 overflow-hidden">
                <div class="card-header bg-transparent border-0 py-3">
                    <h3 class="card-title fw-bold">Quick Navigation</h3>
                </div>
                <div class="card-body">
                    <div class="premium-stats-grid">
                        <a href="{{ route('gate-passes.index') }}" class="stat-premium-card text-decoration-none">
                            <div class="stat-icon-wrapper bg-azure-lt">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon text-azure" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><circle cx="7" cy="17" r="2" /><circle cx="17" cy="17" r="2" /><path d="M5 17h-2v-11a1 1 0 0 1 1 -1h9v12m-4 0h6m4 0h2v-6h-8m0 -5h5l3 5" /></svg>
                            </div>
                            <div class="h3 mb-1 fw-bold text-dark">Gate Passes</div>
                            <div class="text-muted small">Manage vehicles and material movements</div>
                        </a>
                        <a href="{{ route('clients.index') }}" class="stat-premium-card text-decoration-none">
                            <div class="stat-icon-wrapper bg-teal-lt">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon text-teal" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10 13l4 -2l4 2" /><path d="M14 21v-1a2 2 0 0 1 2 -2h4a2 2 0 0 1 2 2v1" /><path d="M15 5a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" /></svg>
                            </div>
                            <div class="h3 mb-1 fw-bold text-dark">Clients</div>
                            <div class="text-muted small">View ledger and transactions</div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-tabler-layout>
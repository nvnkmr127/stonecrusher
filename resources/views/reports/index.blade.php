<x-tabler-layout>
    <x-slot name="header">
        <div class="row align-items-center mb-3">
            <div class="col">
                <div class="page-pretitle text-uppercase fw-bold text-muted" style="letter-spacing: 0.05em;">Analytics & Insights</div>
                <h2 class="page-title h1 fw-bold">{{ __('Reports Engine') }}</h2>
            </div>
        </div>
    </x-slot>

    <div class="premium-header-card mb-4">
        <div class="row align-items-center">
            <div class="col-md-8">
                <div class="d-flex align-items-center">
                    <span class="avatar avatar-lg bg-white-lt text-white me-3 border border-white-subtle" style="backdrop-filter: blur(4px);">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-chart-dots" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 3v18h18" /><circle cx="9" cy="15" r="2" /><circle cx="13" cy="5" r="2" /><circle cx="18" cy="12" r="2" /><path d="M21 3l-6 1.5" /><path d="M3 11l6 -1.5" /></svg>
                    </span>
                    <div>
                        <div class="text-white-50 small text-uppercase fw-bold" style="letter-spacing: 1px;">FINANCIAL INTELLIGENCE</div>
                        <h1 class="mb-0 fw-bold">Business Overview</h1>
                        <div class="text-white-50">Detailed performance metrics and operational logs</div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 text-md-end mt-4 mt-md-0">
                <div class="p-3 bg-white-lt rounded-4 border border-white-subtle" style="backdrop-filter: blur(8px);">
                    <div class="text-white-50 small text-uppercase fw-bold mb-1">Last Analysis</div>
                    <div class="h2 mb-0 fw-bold">{{ now()->format('d M, Y') }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row row-cards">
        <!-- Main Financial Reports -->
        <div class="col-12">
            <div class="hr-text fw-bold text-uppercase text-muted" style="letter-spacing: 2px;">Financial Reports</div>
        </div>

        <div class="col-md-6 col-lg-4">
            <a href="{{ route('reports.daily') }}" class="stat-premium-card text-decoration-none h-100">
                <div class="stat-icon-wrapper bg-blue-lt">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon text-blue" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><rect x="4" y="5" width="16" height="16" rx="2" /><line x1="16" y1="3" x2="16" y2="7" /><line x1="8" y1="3" x2="8" y2="7" /><line x1="4" y1="11" x2="20" y2="11" /><line x1="11" y1="15" x2="12" y2="15" /><line x1="12" y1="15" x2="12" y2="18" /></svg>
                </div>
                <div class="h3 mb-2 fw-bold text-dark">Daily Closing</div>
                <p class="text-muted small mb-0">Sales, collections, and closing status for a specific date.</p>
                <div class="mt-3 text-blue fw-bold small">OPEN REPORT &rarr;</div>
            </a>
        </div>

        <div class="col-md-6 col-lg-4">
            <a href="{{ route('reports.monthly') }}" class="stat-premium-card text-decoration-none h-100">
                <div class="stat-icon-wrapper bg-purple-lt">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon text-purple" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 3v18h18" /><path d="M20 18l-4 -4l-3 3l-4 -4l-3 3" /><path d="M16 14h4v4" /></svg>
                </div>
                <div class="h3 mb-2 fw-bold text-dark">Monthly Summary</div>
                <p class="text-muted small mb-0">Consolidated financial overview of entire months.</p>
                <div class="mt-3 text-purple fw-bold small">OPEN REPORT &rarr;</div>
            </a>
        </div>

        <div class="col-md-6 col-lg-4">
            <a href="{{ route('reports.outstanding') }}" class="stat-premium-card text-decoration-none h-100">
                <div class="stat-icon-wrapper bg-red-lt">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon text-red" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 20l-3 1v-8.5l-4.472 -4.472a0.75 .75 0 0 1 0 -1.056l4.472 -4.472v-1.5l1.5 1.5l1.5 -1.5v1.5l4.472 4.472a0.75 .75 0 0 1 0 1.056l-4.472 4.472v8.5l-3 -1z" /></svg>
                </div>
                <div class="h3 mb-2 fw-bold text-dark">Outstanding Balances</div>
                <p class="text-muted small mb-0">In-depth tracking of receivables and debt metrics.</p>
                <div class="mt-3 text-red fw-bold small">OPEN REPORT &rarr;</div>
            </a>
        </div>

        <div class="col-12 mt-4">
            <div class="hr-text fw-bold text-uppercase text-muted" style="letter-spacing: 2px;">Operational Analysis</div>
        </div>

        <div class="col-md-6 col-lg-4">
            <a href="{{ route('reports.summary', 'metal') }}" class="stat-premium-card text-decoration-none h-100">
                <div class="stat-icon-wrapper bg-teal-lt">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon text-teal" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 3l8 4.5l0 9l-8 4.5l-8 -4.5l0 -9l8 -4.5" /></svg>
                </div>
                <div class="h3 mb-2 fw-bold text-dark">Metal-wise Sales</div>
                <p class="text-muted small mb-0">Volume analysis based on material categories.</p>
                <div class="mt-3 text-teal fw-bold small">VIEW BREAKDOWN &rarr;</div>
            </a>
        </div>

        <div class="col-md-6 col-lg-4">
            <a href="{{ route('reports.summary', 'client') }}" class="stat-premium-card text-decoration-none h-100">
                <div class="stat-icon-wrapper bg-azure-lt">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon text-azure" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><circle cx="9" cy="7" r="4" /><path d="M3 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" /><path d="M16 3.13a4 4 0 0 1 0 7.75" /><path d="M21 21v-2a4 4 0 0 0 -3 -3.85" /></svg>
                </div>
                <div class="h3 mb-2 fw-bold text-dark">Client Sales Top-List</div>
                <p class="text-muted small mb-0">Identification of key accounts and performance.</p>
                <div class="mt-3 text-azure fw-bold small">VIEW CLIENTS &rarr;</div>
            </a>
        </div>

        <div class="col-md-6 col-lg-4">
            <a href="{{ route('gate-passes.distance-report') }}" class="stat-premium-card text-decoration-none h-100">
                <div class="stat-icon-wrapper bg-orange-lt">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon text-orange" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 21l18 0" /><path d="M3 7l18 0" /><path d="M3 14l18 0" /><path d="M7 21l0 -14" /><path d="M17 21l0 -14" /></svg>
                </div>
                <div class="h3 mb-2 fw-bold text-dark">Transport & Distance</div>
                <p class="text-muted small mb-0">Logistics cost and distance reporting metrics.</p>
                <div class="mt-3 text-orange fw-bold small">VIEW LOGISTICS &rarr;</div>
            </a>
        </div>

        <div class="col-12 mt-4">
            <div class="hr-text fw-bold text-uppercase text-muted" style="letter-spacing: 2px;">Utilities</div>
        </div>

        <div class="col-md-6 col-lg-4">
            <a href="{{ route('attendance.report') }}" class="stat-premium-card text-decoration-none h-100">
                <div class="stat-icon-wrapper bg-dark-lt">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon text-dark" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><circle cx="12" cy="12" r="9" /><polyline points="12 7 12 12 15 15" /></svg>
                </div>
                <div class="h3 mb-2 fw-bold text-dark">Attendance Logs</div>
                <p class="text-muted small mb-0">Employee presence and efficiency reporting.</p>
            </a>
        </div>

        <div class="col-md-6 col-lg-4">
            <a href="{{ route('reports.custom') }}" class="stat-premium-card text-decoration-none h-100">
                <div class="stat-icon-wrapper bg-green-lt">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon text-green" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><rect x="4" y="5" width="16" height="16" rx="2" /><line x1="16" y1="3" x2="16" y2="7" /><line x1="8" y1="3" x2="8" y2="7" /><line x1="4" y1="11" x2="20" y2="11" /><rect x="8" y="15" width="2" height="2" /></svg>
                </div>
                <div class="h3 mb-2 fw-bold text-dark">Custom Audit</div>
                <p class="text-muted small mb-0">Generate reports for any specific date range.</p>
            </a>
        </div>
    </div>
</x-tabler-layout>
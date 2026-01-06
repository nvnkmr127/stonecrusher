<aside class="navbar navbar-vertical navbar-expand-lg" data-bs-theme="dark">
    <div class="container-fluid">
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#sidebar-menu"
            aria-controls="sidebar-menu" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <h1 class="navbar-brand navbar-brand-autodark">
            <a href="{{ url('/') }}">
                {{ config('app.name') }}
            </a>
        </h1>
        <div class="collapse navbar-collapse show" id="sidebar-menu" aria-expanded="true">
            <ul class="navbar-nav pt-lg-3">

                @hasanyrole('admin|manager|accountant')
                <li class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('admin.dashboard') }}">
                        <span class="nav-link-icon d-md-none d-lg-inline-block">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M5 12l-2 0l9 -9l9 9l-2 0" />
                                <path d="M5 12v7a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-7" />
                                <path d="M9 21v-6a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v6" />
                            </svg>
                        </span>
                        <span class="nav-link-title">
                            Dashboard
                        </span>
                    </a>
                </li>

                <!-- Sales Section -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle {{ request()->is('gate-passes*') ? 'active' : '' }}"
                        href="#sidebar-sales" data-bs-toggle="dropdown" data-bs-auto-close="false" role="button"
                        aria-expanded="false">
                        <span class="nav-link-icon d-md-none d-lg-inline-block">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path
                                    d="M3 5m0 2a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v10a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2z" />
                                <path d="M3 10h18" />
                                <path d="M7 15h.01" />
                                <path d="M11 15h2" />
                            </svg>
                        </span>
                        <span class="nav-link-title">Sales</span>
                    </a>
                    <div class="dropdown-menu">
                        <a class="dropdown-item" href="{{ route('gate-passes.index') }}">Gate Passes (Dispatch)</a>
                        <a class="dropdown-item" href="{{ route('gate-passes.create') }}">Create Gate Pass</a>
                        <a class="dropdown-item" href="{{ route('gate-passes.calculator') }}">Payment Calculator</a>
                    </div>
                </li>

                <!-- Finance Section -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle {{ request()->is('clients/*/transactions*', 'daily-closings*') ? 'active' : '' }}"
                        href="#sidebar-finance" data-bs-toggle="dropdown" data-bs-auto-close="false" role="button"
                        aria-expanded="false">
                        <span class="nav-link-icon d-md-none d-lg-inline-block">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" />
                                <path
                                    d="M14.8 9a2 2 0 0 0 -1.8 -1h-2a2 2 0 0 0 0 4h2a2 2 0 0 1 0 4h-2a2 2 0 0 1 -1.8 -1" />
                                <path d="M12 6v2m0 8v2" />
                            </svg>
                        </span>
                        <span class="nav-link-title">Finance</span>
                    </a>
                    <div class="dropdown-menu">
                        <a class="dropdown-item" href="{{ route('clients.index') }}">Client Ledgers</a>
                        @role('admin')
                        <a class="dropdown-item" href="{{ route('daily-closings.index') }}">Daily Closings</a>
                        @endrole
                    </div>
                </li>

                <!-- Master Data Section -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle {{ request()->is('clients*', 'vehicles*', 'metal-types*', 'projects*') ? 'active' : '' }}"
                        href="#navbar-master-data" data-bs-toggle="dropdown" data-bs-auto-close="false" role="button"
                        aria-expanded="false">
                        <span class="nav-link-icon d-md-none d-lg-inline-block">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M12 3l8 4.5l0 9l-8 4.5l-8 -4.5l0 -9l8 -4.5" />
                                <path d="M12 12l8 -4.5" />
                                <path d="M12 12l0 9" />
                                <path d="M12 12l-8 -4.5" />
                            </svg>
                        </span>
                        <span class="nav-link-title">Master Data</span>
                    </a>
                    <div class="dropdown-menu">
                        <a class="dropdown-item" href="{{ route('clients.index') }}">Clients</a>
                        <a class="dropdown-item" href="{{ route('vehicles.index') }}">Vehicles</a>
                        <a class="dropdown-item" href="{{ route('metal-types.index') }}">Metal Types</a>
                        <a class="dropdown-item" href="{{ route('projects.index') }}">Projects</a>
                    </div>
                </li>

                <!-- Attendance Section -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle {{ request()->routeIs('attendance.*') ? 'active' : '' }}"
                        href="#navbar-attendance" data-bs-toggle="dropdown" data-bs-auto-close="false" role="button"
                        aria-expanded="false">
                        <span class="nav-link-icon d-md-none d-lg-inline-block">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M11.795 21h-6.795a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v4" />
                                <path d="M18 18m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0" />
                                <path d="M15 3v4" />
                                <path d="M7 3v4" />
                                <path d="M3 11h16" />
                                <path d="M18 16.496v1.504l1 1" />
                            </svg>
                        </span>
                        <span class="nav-link-title">Attendance</span>
                    </a>
                    <div class="dropdown-menu">
                        <a class="dropdown-item" href="{{ route('attendance.index') }}">Daily Register</a>
                        <a class="dropdown-item" href="{{ route('attendance.report') }}">Monthly Summary</a>
                    </div>
                </li>

                <!-- Comprehensive Reports Section -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle {{ request()->is('reports*', 'gate-passes/*report*') ? 'active' : '' }}"
                        href="#navbar-reports" data-bs-toggle="dropdown" data-bs-auto-close="false" role="button"
                        aria-expanded="false">
                        <span class="nav-link-icon d-md-none d-lg-inline-block">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M8 5h-2a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h5.697" />
                                <path d="M18 14v4h4" />
                                <path d="M18 11v-4a2 2 0 0 0 -2 -2h-2" />
                                <path
                                    d="M8 3m0 2a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v0a2 2 0 0 1 -2 2h-2a2 2 0 0 1 -2 -2z" />
                                <path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" />
                                <path d="M12 9v4l2 2" />
                            </svg>
                        </span>
                        <span class="nav-link-title">Reports Center</span>
                    </a>
                    <div class="dropdown-menu">
                        <a class="dropdown-item" href="{{ route('reports.index') }}">
                            <strong>Reports Dashboard</strong>
                        </a>
                        <div class="dropdown-divider"></div>
                        <div class="dropdown-header">Sales Reports</div>
                        <a class="dropdown-item" href="{{ route('reports.daily') }}">Daily Sales</a>
                        <a class="dropdown-item" href="{{ route('gate-passes.daily-report') }}">Daily Dispatch</a>
                        <a class="dropdown-item" href="{{ route('gate-passes.distance-report') }}">Distance Report</a>
                        <a class="dropdown-item" href="{{ route('reports.monthly') }}">Monthly Sales</a>

                        <div class="dropdown-divider"></div>
                        <div class="dropdown-header">Financial Reports</div>
                        <a class="dropdown-item" href="{{ route('reports.outstanding') }}">Outstanding & Advance</a>
                        <a class="dropdown-item" href="{{ route('clients.reports.outstanding') }}">Client Balance
                            Summary</a>

                        <div class="dropdown-divider"></div>
                        <div class="dropdown-header">Other Reports</div>
                        <a class="dropdown-item" href="{{ route('attendance.report') }}">Staff Attendance</a>
                        <a class="dropdown-item" href="{{ route('reports.custom') }}">Custom Date Filter</a>
                    </div>
                </li>

                @role('admin')
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle {{ request()->is('users*') ? 'active' : '' }}"
                        href="#navbar-users" data-bs-toggle="dropdown" data-bs-auto-close="false" role="button"
                        aria-expanded="false">
                        <span class="nav-link-icon d-md-none d-lg-inline-block">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0" />
                                <path d="M6 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" />
                            </svg>
                        </span>
                        <span class="nav-link-title">Administration</span>
                    </a>
                    <div class="dropdown-menu">
                        <a class="dropdown-item" href="{{ route('users.index') }}">Staff Management</a>
                        <a class="dropdown-item" href="{{ route('audit-logs.index') }}">Audit Logs</a>
                        <a class="dropdown-item" href="{{ route('backups.index') }}">Backups</a>
                        <a class="dropdown-item" href="{{ route('settings.index') }}">System Settings</a>
                    </div>
                </li>
                @endrole
                @endhasanyrole

                @role('user')
                <li class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('dashboard') }}">
                        <span class="nav-link-icon d-md-none d-lg-inline-block">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M5 12l-2 0l9 -9l9 9l-2 0" />
                                <path d="M5 12v7a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-7" />
                                <path d="M9 21v-6a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v6" />
                            </svg>
                        </span>
                        <span class="nav-link-title">
                            Dashboard
                        </span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">
                        <span class="nav-link-icon d-md-none d-lg-inline-block">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M3 19a9 9 0 0 1 9 0a9 9 0 0 1 9 0" />
                                <path d="M3 6a9 9 0 0 1 9 0a9 9 0 0 1 9 0" />
                                <path d="M3 6l0 13" />
                                <path d="M12 6l0 13" />
                                <path d="M21 6l0 13" />
                            </svg>
                        </span>
                        <span class="nav-link-title">
                            My Orders
                        </span>
                    </a>
                </li>
                @endrole

            </ul>
        </div>
    </div>
</aside>
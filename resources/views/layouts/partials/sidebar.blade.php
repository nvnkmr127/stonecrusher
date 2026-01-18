<aside class="navbar navbar-vertical navbar-expand-lg navbar-dark border-end-0 shadow-lg" style="background: #0f172a;">
    <div class="container-fluid px-0 h-100 d-flex flex-column">

        <!-- Sidebar Header (Logo) -->
        <div class="px-4 py-4 d-none d-lg-block border-bottom border-white-5">
            <a href="{{ route('admin.dashboard') }}"
                class="text-decoration-none text-white d-flex align-items-center gap-3 group">
                <div class="bg-primary rounded-3 p-2 shadow-sm group-hover-scale transition-all">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-building-fortress"
                        width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                        stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path d="M7 21h1a1 1 0 0 0 1 -1v-1h0a3 3 0 0 1 6 0h0v1a1 1 0 0 0 1 1h1" />
                        <path d="M5 21v-7m0 -4v-5a2 2 0 1 1 4 0v5m0 -4h6m0 4v-5a2 2 0 1 1 4 0v5m0 -4v7" />
                        <path d="M11 14h2" />
                    </svg>
                </div>
                <div>
                    <span class="lh-1 d-block fw-bold tracking-tight text-white mb-0"
                        style="font-size: 1.1rem;">{{ config('app.name', 'StoneCrusher') }}</span>
                    <span class="badge bg-primary-lt border-0 text-uppercase fw-bold"
                        style="font-size: 0.55rem; letter-spacing: 0.05em;">Enterprise ERP</span>
                </div>
            </a>
        </div>

        <div class="mobile-sidebar flex-fill overflow-y-auto" id="sidebar-menu">
            <!-- Mobile Sidebar Header -->
            <div class="d-lg-none d-flex align-items-center justify-content-between p-4 border-bottom border-white-5">
                <div class="h3 mb-0 fw-bold text-white tracking-wide">{{ config('app.name', 'StoneCrusher') }}</div>
                <button type="button" class="btn-close btn-close-white" id="mobile-menu-close"
                    aria-label="Close"></button>
            </div>

            <ul class="navbar-nav py-3 d-flex flex-column gap-1">
                <!-- Dashboard -->
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.dashboard') || request()->routeIs('dashboard') ? 'active' : '' }}"
                        href="{{ route('admin.dashboard') }}">
                        <span class="nav-link-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M5 12l-2 0l9 -9l9 9l-2 0" />
                                <path d="M5 12v7a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-7" />
                                <path d="M9 21v-6a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v6" />
                            </svg>
                        </span>
                        <span class="nav-link-title">Dashboard</span>
                    </a>
                </li>

                <!-- Operations Section -->
                <li class="nav-item mt-3">
                    <div class="sidebar-section-header">Core Operations</div>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle {{ request()->routeIs('gate-passes.*') ? 'active' : '' }}"
                        href="#navbar-operations" data-bs-toggle="dropdown" data-bs-auto-close="false" role="button"
                        aria-expanded="{{ request()->routeIs('gate-passes.*') ? 'true' : 'false' }}">
                        <span class="nav-link-icon">
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
                        <span class="nav-link-title">Gate Passes</span>
                    </a>
                    <div class="dropdown-menu {{ request()->routeIs('gate-passes.*') ? 'show' : '' }}">
                        <a class="dropdown-item {{ request()->routeIs('gate-passes.create') ? 'active' : '' }}"
                            href="{{ route('gate-passes.create') }}">Create New</a>
                        <a class="dropdown-item {{ request()->routeIs('gate-passes.index') ? 'active' : '' }}"
                            href="{{ route('gate-passes.index') }}">All Records</a>
                        <a class="dropdown-item {{ request()->routeIs('gate-passes.calculator') ? 'active' : '' }}"
                            href="{{ route('gate-passes.calculator') }}">Calculator</a>
                        <a class="dropdown-item {{ request()->routeIs('gate-passes.daily-report') ? 'active' : '' }}"
                            href="{{ route('gate-passes.daily-report') }}">Daily Report</a>
                    </div>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('daily-closings.*') ? 'active' : '' }}"
                        href="{{ route('daily-closings.index') }}">
                        <span class="nav-link-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path
                                    d="M5 11m0 2a2 2 0 0 1 2 -2h10a2 2 0 0 1 2 2v6a2 2 0 0 1 -2 2h-10a2 2 0 0 1 -2 -2z" />
                                <path d="M12 16m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0" />
                                <path d="M8 11v-4a4 4 0 1 1 8 0" />
                            </svg>
                        </span>
                        <span class="nav-link-title">Daily Closing</span>
                    </a>
                </li>

                <!-- CRM Section -->
                <li class="nav-item mt-3">
                    <div class="sidebar-section-header">CRM & Sales</div>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle {{ request()->routeIs('clients.*') || request()->routeIs('projects.*') ? 'active' : '' }}"
                        href="#navbar-crm" data-bs-toggle="dropdown" data-bs-auto-close="false" role="button"
                        aria-expanded="{{ request()->routeIs('clients.*') || request()->routeIs('projects.*') ? 'true' : 'false' }}">
                        <span class="nav-link-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M9 7m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0" />
                                <path d="M3 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" />
                            </svg>
                        </span>
                        <span class="nav-link-title">Clients & Projects</span>
                    </a>
                    <div
                        class="dropdown-menu {{ request()->routeIs('clients.*') || request()->routeIs('projects.*') ? 'show' : '' }}">
                        <a class="dropdown-item {{ request()->routeIs('clients.index') ? 'active' : '' }}"
                            href="{{ route('clients.index') }}">Client Directory</a>
                        <a class="dropdown-item {{ request()->routeIs('clients.create') ? 'active' : '' }}"
                            href="{{ route('clients.create') }}">Add Client</a>
                        <a class="dropdown-item {{ request()->routeIs('projects.index') ? 'active' : '' }}"
                            href="{{ route('projects.index') }}">Projects</a>
                    </div>
                </li>

                <!-- Fleet Section -->
                <li class="nav-item mt-3">
                    <div class="sidebar-section-header">Fleet & Masters</div>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('vehicles.*') ? 'active' : '' }}"
                        href="{{ route('vehicles.index') }}">
                        <span class="nav-link-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M7 17m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" />
                                <path d="M17 17m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" />
                                <path d="M5 17h-2v-11a1 1 0 0 1 1 -1h9v12m-4 0h6m4 0h2v-6h-8m0 -5h5l3 5" />
                            </svg>
                        </span>
                        <span class="nav-link-title">Vehicles</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('metal-types.*') ? 'active' : '' }}"
                        href="{{ route('metal-types.index') }}">
                        <span class="nav-link-icon">
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
                        <span class="nav-link-title">Metal Types</span>
                    </a>
                </li>

                <!-- Analytics Section -->
                <li class="nav-item mt-3">
                    <div class="sidebar-section-header">Analytics</div>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle {{ request()->routeIs('reports.*') || request()->routeIs('gate-passes.distance-report') || request()->routeIs('attendance.report') || request()->routeIs('attendance.report.*') ? 'active' : '' }}"
                        href="#navbar-reports" data-bs-toggle="dropdown" data-bs-auto-close="false" role="button"
                        aria-expanded="{{ request()->routeIs('reports.*') ? 'true' : 'false' }}">
                        <span class="nav-link-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path
                                    d="M3 12m0 1a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v6a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z" />
                                <path
                                    d="M9 8m0 1a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v10a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z" />
                                <path
                                    d="M15 4m0 1a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v14a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z" />
                                <path d="M4 20l14 0" />
                            </svg>
                        </span>
                        <span class="nav-link-title">Reports Center</span>
                    </a>
                    <div
                        class="dropdown-menu {{ request()->routeIs('reports.*') || request()->routeIs('gate-passes.distance-report') || request()->routeIs('attendance.report') || request()->routeIs('attendance.report.*') ? 'show' : '' }}">
                        <a class="dropdown-item {{ request()->routeIs('reports.daily') ? 'active' : '' }}"
                            href="{{ route('reports.daily') }}">Daily Sales</a>
                        <a class="dropdown-item {{ request()->routeIs('reports.monthly') ? 'active' : '' }}"
                            href="{{ route('reports.monthly') }}">Monthly Sales</a>
                        <a class="dropdown-item {{ request()->routeIs('reports.outstanding') ? 'active' : '' }}"
                            href="{{ route('reports.outstanding') }}">Outstanding & Advance</a>
                        <a class="dropdown-item {{ request()->routeIs('gate-passes.distance-report') ? 'active' : '' }}"
                            href="{{ route('gate-passes.distance-report') }}">Distance Report</a>
                        <a class="dropdown-item {{ request()->routeIs('reports.custom') ? 'active' : '' }}"
                            href="{{ route('reports.custom') }}">Custom Date</a>
                        <a class="dropdown-item {{ request()->routeIs('reports.summary') ? 'active' : '' }}"
                            href="{{ route('reports.summary', ['type' => 'vehicle']) }}">Summary View</a>
                        <a class="dropdown-item {{ request()->routeIs('attendance.report') ? 'active' : '' }}"
                            href="{{ route('attendance.report') }}">Attendance Report</a>
                    </div>
                </li>

                <!-- Admin Section -->
                @role('admin')
                <li class="nav-item mt-3">
                    <div class="sidebar-section-header">System</div>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('attendance.index') ? 'active' : '' }}"
                        href="{{ route('attendance.index') }}">
                        <span class="nav-link-icon">
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
                        <span class="nav-link-title">Attendance Mgmt</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('users.index') ? 'active' : '' }}"
                        href="{{ route('users.index') }}">
                        <span class="nav-link-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0" />
                                <path d="M6 21v-2a4 4 0 0 1 4 -4h4" />
                                <path d="M15 19l2 2l4 -4" />
                            </svg>
                        </span>
                        <span class="nav-link-title">Users</span>
                    </a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle {{ request()->routeIs('settings.*') || request()->routeIs('backups.*') || request()->routeIs('audit-logs.*') ? 'active' : '' }}"
                        href="#navbar-system" data-bs-toggle="dropdown" data-bs-auto-close="false" role="button"
                        aria-expanded="{{ request()->routeIs('settings.*') ? 'true' : 'false' }}">
                        <span class="nav-link-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path
                                    d="M10.325 4.317c.426 -1.756 2.924 -1.756 3.35 0a1.724 1.724 0 0 0 2.573 1.066c1.543 -.94 3.31 .826 2.37 2.37a1.724 1.724 0 0 0 1.065 2.572c1.756 .426 1.756 2.924 0 3.35a1.724 1.724 0 0 0 -1.066 2.573c.94 1.543 -.826 3.31 -2.37 2.37a1.724 1.724 0 0 0 -2.572 1.065c-.426 1.756 -2.924 1.756 -3.35 0a1.724 1.724 0 0 0 -2.573 -1.066c-1.543 .94 -3.31 -.826 -2.37 -2.37a1.724 1.724 0 0 0 -1.065 -2.572c-1.756 -.426 -1.756 -2.924 0 -3.35a1.724 1.724 0 0 0 1.066 -2.573c-.94 -1.543 .826 -3.31 2.37 -2.37c1 .608 2.296 .07 2.572 -1.065z" />
                                <path d="M9 12a3 3 0 1 0 6 0a3 3 0 0 0 -6 0" />
                            </svg>
                        </span>
                        <span class="nav-link-title">System Management</span>
                    </a>
                    <div
                        class="dropdown-menu {{ request()->routeIs('settings.*') || request()->routeIs('backups.*') || request()->routeIs('audit-logs.*') ? 'show' : '' }}">
                        <a class="dropdown-item {{ request()->routeIs('settings.index') ? 'active' : '' }}"
                            href="{{ route('settings.index') }}">General Settings</a>
                        <a class="dropdown-item {{ request()->routeIs('backups.index') ? 'active' : '' }}"
                            href="{{ route('backups.index') }}">System Backups</a>
                        <a class="dropdown-item {{ request()->routeIs('audit-logs.index') ? 'active' : '' }}"
                            href="{{ route('audit-logs.index') }}">Audit Logs</a>
                    </div>
                </li>
                @endrole
            </ul>
        </div>

        <!-- Sidebar Footer (Optional user info or logout could go here) -->
        <div class="px-4 py-3 border-top border-white-5 mt-auto">
            <div class="d-flex align-items-center gap-2 text-white-50">
                <span class="badge bg-success-lt border-0 p-1"></span>
                <span style="font-size: 0.7rem;" class="fw-medium text-uppercase tracking-wider">System Online</span>
            </div>
        </div>
    </div>
</aside>
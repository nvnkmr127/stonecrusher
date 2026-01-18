<aside class="navbar navbar-vertical navbar-expand-lg navbar-dark border-end-0 bg-dark" data-bs-theme="dark"
    style="background: linear-gradient(180deg, #0f172a 0%, #1e293b 100%); color: #fff; box-shadow: 4px 0 24px 0 rgba(0,0,0,0.1);">
    <div class="container-fluid px-3 py-2">

        <h1 class="navbar-brand navbar-brand-autodark mb-3 mt-2 d-none d-lg-block">
            <a href="{{ route('admin.dashboard') }}"
                class="text-decoration-none text-white d-flex align-items-center gap-2">
                <!-- Optional Logo Icon can go here -->
                <span class="fw-bold tracking-wide"
                    style="letter-spacing: 0.5px;">{{ config('app.name', 'StoneCrusher') }}</span>
            </a>
        </h1>
        <div class="mobile-sidebar" id="sidebar-menu">
            <!-- Mobile Sidebar Header (Logo + Close) -->
            <div class="d-lg-none d-flex align-items-center justify-content-between p-3 border-bottom border-white-10">
                <div class="h3 mb-0 fw-bold text-white tracking-wide">{{ config('app.name', 'StoneCrusher') }}</div>
                <button type="button" class="btn-close btn-close-white" id="mobile-menu-close"
                    aria-label="Close"></button>
            </div>

            <ul class="navbar-nav pt-lg-2 d-flex flex-column gap-1">
                <!-- Dashboard -->
                <li class="nav-item">
                    <a class="nav-link px-3 py-2 rounded-pill {{ request()->routeIs('admin.dashboard') || request()->routeIs('dashboard') ? 'bg-primary text-white shadow-sm' : 'text-white-50 hover-light' }}"
                        href="{{ route('admin.dashboard') }}">
                        <span class="nav-link-icon d-inline-block me-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M5 12l-2 0l9 -9l9 9l-2 0" />
                                <path d="M5 12v7a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-7" />
                                <path d="M9 21v-6a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v6" />
                            </svg>
                        </span>
                        <span class="nav-link-title fw-medium">
                            Dashboard
                        </span>
                    </a>
                </li>

                <div class="my-2 border-top border-white-10 opacity-25"></div>

                <!-- Operations -->
                <li class="nav-item mb-1">
                    <span class="nav-link text-uppercase fw-bold text-white-50 fs-6 ps-3 tracking-wider"
                        style="font-size: 0.7rem; letter-spacing: 0.05em;">
                        Operations
                    </span>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle px-3 py-2 rounded-pill {{ request()->routeIs('gate-passes.*') ? 'bg-white-10 text-white' : 'text-white-50 hover-light' }}"
                        href="#navbar-operations" data-bs-toggle="dropdown" data-bs-auto-close="false" role="button"
                        aria-expanded="{{ request()->routeIs('gate-passes.*') ? 'true' : 'false' }}">
                        <span class="nav-link-icon d-inline-block me-2">
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
                        <span class="nav-link-title fw-medium">
                            Gate Passes
                        </span>
                    </a>
                    <div
                        class="dropdown-menu text-bg-dark border-0 bg-transparent ps-4 {{ request()->routeIs('gate-passes.*') ? 'show' : '' }}">
                        <a class="dropdown-item py-1 rounded-pill {{ request()->routeIs('gate-passes.create') ? 'text-white fw-bold' : 'text-white-50' }}"
                            href="{{ route('gate-passes.create') }}">Create New</a>
                        <a class="dropdown-item py-1 rounded-pill {{ request()->routeIs('gate-passes.index') ? 'text-white fw-bold' : 'text-white-50' }}"
                            href="{{ route('gate-passes.index') }}">All Records</a>
                        <a class="dropdown-item py-1 rounded-pill {{ request()->routeIs('gate-passes.calculator') ? 'text-white fw-bold' : 'text-white-50' }}"
                            href="{{ route('gate-passes.calculator') }}">Calculator</a>
                        <a class="dropdown-item py-1 rounded-pill {{ request()->routeIs('gate-passes.daily-report') ? 'text-white fw-bold' : 'text-white-50' }}"
                            href="{{ route('gate-passes.daily-report') }}">Daily Report</a>
                    </div>
                </li>
                <li class="nav-item">
                    <a class="nav-link px-3 py-2 rounded-pill {{ request()->routeIs('daily-closings.*') ? 'bg-white-10 text-white' : 'text-white-50 hover-light' }}"
                        href="{{ route('daily-closings.index') }}">
                        <span class="nav-link-icon d-inline-block me-2">
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
                        <span class="nav-link-title fw-medium">
                            Daily Closing
                        </span>
                    </a>
                </li>

                <!-- CRM -->
                <li class="nav-item mb-1 mt-3">
                    <span class="nav-link text-uppercase fw-bold text-white-50 fs-6 ps-3 tracking-wider"
                        style="font-size: 0.7rem; letter-spacing: 0.05em;">
                        CRM & Sales
                    </span>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle px-3 py-2 rounded-pill {{ request()->routeIs('clients.*') ? 'bg-white-10 text-white' : 'text-white-50 hover-light' }}"
                        href="#navbar-crm" data-bs-toggle="dropdown" data-bs-auto-close="false" role="button"
                        aria-expanded="{{ request()->routeIs('clients.*') ? 'true' : 'false' }}">
                        <span class="nav-link-icon d-inline-block me-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M9 7m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0" />
                                <path d="M3 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" />
                            </svg>
                        </span>
                        <span class="nav-link-title fw-medium">
                            Clients & Projects
                        </span>
                    </a>
                    <div
                        class="dropdown-menu text-bg-dark border-0 bg-transparent ps-4 {{ request()->routeIs('clients.*') || request()->routeIs('projects.*') ? 'show' : '' }}">
                        <a class="dropdown-item py-1 rounded-pill {{ request()->routeIs('clients.index') ? 'text-white fw-bold' : 'text-white-50' }}"
                            href="{{ route('clients.index') }}">Client Directory</a>
                        <a class="dropdown-item py-1 rounded-pill {{ request()->routeIs('clients.create') ? 'text-white fw-bold' : 'text-white-50' }}"
                            href="{{ route('clients.create') }}">Add Client</a>
                        <a class="dropdown-item py-1 rounded-pill {{ request()->routeIs('projects.index') ? 'text-white fw-bold' : 'text-white-50' }}"
                            href="{{ route('projects.index') }}">Projects</a>
                    </div>
                </li>

                <!-- Fleet & Data -->
                <li class="nav-item mb-1 mt-3">
                    <span class="nav-link text-uppercase fw-bold text-white-50 fs-6 ps-3 tracking-wider"
                        style="font-size: 0.7rem; letter-spacing: 0.05em;">
                        Fleet & Master
                    </span>
                </li>
                <li class="nav-item">
                    <a class="nav-link px-3 py-2 rounded-pill {{ request()->routeIs('vehicles.*') ? 'bg-white-10 text-white' : 'text-white-50 hover-light' }}"
                        href="{{ route('vehicles.index') }}">
                        <span class="nav-link-icon d-inline-block me-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M7 17m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" />
                                <path d="M17 17m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" />
                                <path d="M5 17h-2v-11a1 1 0 0 1 1 -1h9v12m-4 0h6m4 0h2v-6h-8m0 -5h5l3 5" />
                            </svg>
                        </span>
                        <span class="nav-link-title fw-medium">
                            Vehicles
                        </span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link px-3 py-2 rounded-pill {{ request()->routeIs('metal-types.*') ? 'bg-white-10 text-white' : 'text-white-50 hover-light' }}"
                        href="{{ route('metal-types.index') }}">
                        <span class="nav-link-icon d-inline-block me-2">
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
                        <span class="nav-link-title fw-medium">
                            Metal Types
                        </span>
                    </a>
                </li>

                <!-- Reporting -->
                <li class="nav-item mb-1 mt-3">
                    <span class="nav-link text-uppercase fw-bold text-white-50 fs-6 ps-3 tracking-wider"
                        style="font-size: 0.7rem; letter-spacing: 0.05em;">
                        Analytics
                    </span>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle px-3 py-2 rounded-pill {{ request()->routeIs('reports.*') || request()->routeIs('gate-passes.distance-report') || request()->routeIs('attendance.report') || request()->routeIs('attendance.report.*') ? 'bg-white-10 text-white' : 'text-white-50 hover-light' }}"
                        href="#navbar-reports" data-bs-toggle="dropdown" data-bs-auto-close="false" role="button"
                        aria-expanded="{{ request()->routeIs('reports.*') ? 'true' : 'false' }}">
                        <span class="nav-link-icon d-inline-block me-2">
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
                        <span class="nav-link-title fw-medium">
                            Reports Center
                        </span>
                    </a>
                    <div
                        class="dropdown-menu text-bg-dark border-0 bg-transparent ps-4 {{ request()->routeIs('reports.*') || request()->routeIs('gate-passes.distance-report') || request()->routeIs('attendance.report') || request()->routeIs('attendance.report.*') ? 'show' : '' }}">
                        <a class="dropdown-item py-1 rounded-pill {{ request()->routeIs('reports.daily') ? 'text-white fw-bold' : 'text-white-50' }}"
                            href="{{ route('reports.daily') }}">Daily Sales</a>
                        <a class="dropdown-item py-1 rounded-pill {{ request()->routeIs('reports.monthly') ? 'text-white fw-bold' : 'text-white-50' }}"
                            href="{{ route('reports.monthly') }}">Monthly Sales</a>
                        <a class="dropdown-item py-1 rounded-pill {{ request()->routeIs('reports.outstanding') ? 'text-white fw-bold' : 'text-white-50' }}"
                            href="{{ route('reports.outstanding') }}">Outstanding & Advance</a>
                        <a class="dropdown-item py-1 rounded-pill {{ request()->routeIs('gate-passes.distance-report') ? 'text-white fw-bold' : 'text-white-50' }}"
                            href="{{ route('gate-passes.distance-report') }}">Distance Report</a>
                        <a class="dropdown-item py-1 rounded-pill {{ request()->routeIs('reports.custom') ? 'text-white fw-bold' : 'text-white-50' }}"
                            href="{{ route('reports.custom') }}">Custom Date</a>
                        <a class="dropdown-item py-1 rounded-pill {{ request()->routeIs('reports.summary') ? 'text-white fw-bold' : 'text-white-50' }}"
                            href="{{ route('reports.summary', ['type' => 'vehicle']) }}">Summary View</a>
                        <a class="dropdown-item py-1 rounded-pill {{ request()->routeIs('attendance.report') ? 'text-white fw-bold' : 'text-white-50' }}"
                            href="{{ route('attendance.report') }}">Attendance Report</a>
                    </div>
                </li>

                <!-- Admin & System (Footer like) -->
                @role('admin')
                <div class="my-2 border-top border-white-10 opacity-25"></div>

                <li class="nav-item">
                    <a class="nav-link px-3 py-2 rounded-pill {{ request()->routeIs('attendance.index') ? 'bg-white-10 text-white' : 'text-white-50 hover-light' }}"
                        href="{{ route('attendance.index') }}">
                        <span class="nav-link-icon d-inline-block me-2">
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
                        <span class="nav-link-title fw-medium">
                            Attendance Mgmt
                        </span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link px-3 py-2 rounded-pill {{ request()->routeIs('users.index') ? 'bg-white-10 text-white' : 'text-white-50 hover-light' }}"
                        href="{{ route('users.index') }}">
                        <span class="nav-link-icon d-inline-block me-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0" />
                                <path d="M6 21v-2a4 4 0 0 1 4 -4h4" />
                                <path d="M15 19l2 2l4 -4" />
                            </svg>
                        </span>
                        <span class="nav-link-title fw-medium">
                            Users
                        </span>
                    </a>
                </li>

                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle px-3 py-2 rounded-pill {{ request()->routeIs('settings.*') || request()->routeIs('backups.*') || request()->routeIs('audit-logs.*') ? 'bg-white-10 text-white' : 'text-white-50 hover-light' }}"
                        href="#navbar-system" data-bs-toggle="dropdown" data-bs-auto-close="false" role="button"
                        aria-expanded="{{ request()->routeIs('settings.*') ? 'true' : 'false' }}">
                        <span class="nav-link-icon d-inline-block me-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path
                                    d="M10.325 4.317c.426 -1.756 2.924 -1.756 3.35 0a1.724 1.724 0 0 0 2.573 1.066c1.543 -.94 3.31 .826 2.37 2.37a1.724 1.724 0 0 0 1.065 2.572c1.756 .426 1.756 2.924 0 3.35a1.724 1.724 0 0 0 -1.066 2.573c.94 1.543 -.826 3.31 -2.37 2.37a1.724 1.724 0 0 0 -2.572 1.065c-.426 1.756 -2.924 1.756 -3.35 0a1.724 1.724 0 0 0 -2.573 -1.066c-1.543 .94 -3.31 -.826 -2.37 -2.37a1.724 1.724 0 0 0 -1.065 -2.572c-1.756 -.426 -1.756 -2.924 0 -3.35a1.724 1.724 0 0 0 1.066 -2.573c-.94 -1.543 .826 -3.31 2.37 -2.37c1 .608 2.296 .07 2.572 -1.065z" />
                                <path d="M9 12a3 3 0 1 0 6 0a3 3 0 0 0 -6 0" />
                            </svg>
                        </span>
                        <span class="nav-link-title fw-medium">
                            System
                        </span>
                    </a>
                    <div
                        class="dropdown-menu text-bg-dark border-0 bg-transparent ps-4 {{ request()->routeIs('settings.*') || request()->routeIs('backups.*') || request()->routeIs('audit-logs.*') ? 'show' : '' }}">
                        <a class="dropdown-item py-1 rounded-pill {{ request()->routeIs('settings.index') ? 'text-white fw-bold' : 'text-white-50' }}"
                            href="{{ route('settings.index') }}">Settings</a>
                        <a class="dropdown-item py-1 rounded-pill {{ request()->routeIs('backups.index') ? 'text-white fw-bold' : 'text-white-50' }}"
                            href="{{ route('backups.index') }}">Backups</a>
                        <a class="dropdown-item py-1 rounded-pill {{ request()->routeIs('audit-logs.index') ? 'text-white fw-bold' : 'text-white-50' }}"
                            href="{{ route('audit-logs.index') }}">Audit Logs</a>
                    </div>
                </li>
                @endrole

            </ul>
        </div>
    </div>
</aside>
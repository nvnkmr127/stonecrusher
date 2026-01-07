<aside class="navbar navbar-vertical navbar-expand-lg" id="sidebar">
    <!-- Mobile Close Button -->
    <button class="sidebar-close" onclick="toggleSidebar()">
        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2"
            stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
            <path d="M18 6l-12 12" />
            <path d="M6 6l12 12" />
        </svg>
    </button>

    <div class="container-fluid">
        <h1 class="navbar-brand navbar-brand-autodark">
            <a href="{{ url('/') }}">
                {{ config('app.name') }}
            </a>
        </h1>

        <div class="collapse navbar-collapse show" id="sidebar-menu" aria-expanded="true">

            @hasanyrole('admin|manager|accountant')
            <!-- Quick Action - Create Gate Pass -->
            <div class="sidebar-quick-action d-none d-lg-block">
                <a href="{{ route('gate-passes.create') }}" class="btn btn-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24"
                        stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round"
                        stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path d="M12 5l0 14" />
                        <path d="M5 12l14 0" />
                    </svg>
                    New Gate Pass
                </a>
            </div>
            @endhasanyrole

            <ul class="navbar-nav pt-lg-3">

                @hasanyrole('admin|manager|accountant')
                <!-- Dashboard -->
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
                        <span class="nav-link-title">Dashboard</span>
                    </a>
                </li>

                <div class="sidebar-divider"></div>

                <!-- OPERATIONS Section -->
                <div class="sidebar-heading">Operations</div>

                <li class="nav-item {{ request()->routeIs('gate-passes.index') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('gate-passes.index') }}">
                        <span class="nav-link-icon d-md-none d-lg-inline-block">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M9 6l11 0" />
                                <path d="M9 12l11 0" />
                                <path d="M9 18l11 0" />
                                <path d="M5 6l0 .01" />
                                <path d="M5 12l0 .01" />
                                <path d="M5 18l0 .01" />
                            </svg>
                        </span>
                        <span class="nav-link-title">Gate Passes</span>
                    </a>
                </li>

                <li class="nav-item {{ request()->routeIs('gate-passes.calculator') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('gate-passes.calculator') }}">
                        <span class="nav-link-icon d-md-none d-lg-inline-block">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path
                                    d="M4 3m0 2a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v14a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2z" />
                                <path
                                    d="M8 7m0 1a1 1 0 0 1 1 -1h6a1 1 0 0 1 1 1v1a1 1 0 0 1 -1 1h-6a1 1 0 0 1 -1 -1z" />
                                <path d="M8 14l0 .01" />
                                <path d="M12 14l0 .01" />
                                <path d="M16 14l0 .01" />
                                <path d="M8 17l0 .01" />
                                <path d="M12 17l0 .01" />
                                <path d="M16 17l0 .01" />
                            </svg>
                        </span>
                        <span class="nav-link-title">Payment Calculator</span>
                    </a>
                </li>

                <div class="sidebar-divider"></div>

                <!-- CLIENTS & FINANCE Section -->
                <div class="sidebar-heading">Clients & Finance</div>

                <li
                    class="nav-item {{ request()->routeIs('clients.*') && !request()->routeIs('clients.reports.*') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('clients.index') }}">
                        <span class="nav-link-icon d-md-none d-lg-inline-block">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M9 7m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0" />
                                <path d="M3 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" />
                                <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                                <path d="M21 21v-2a4 4 0 0 0 -3 -3.85" />
                            </svg>
                        </span>
                        <span class="nav-link-title">Clients & Ledgers</span>
                    </a>
                </li>

                @role('admin')
                <li class="nav-item {{ request()->routeIs('daily-closings.*') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('daily-closings.index') }}">
                        <span class="nav-link-icon d-md-none d-lg-inline-block">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path
                                    d="M5 13a2 2 0 0 1 2 -2h10a2 2 0 0 1 2 2v6a2 2 0 0 1 -2 2h-10a2 2 0 0 1 -2 -2v-6z" />
                                <path d="M11 16a1 1 0 1 0 2 0a1 1 0 0 0 -2 0" />
                                <path d="M8 11v-4a4 4 0 1 1 8 0v4" />
                            </svg>
                        </span>
                        <span class="nav-link-title">Daily Closings</span>
                    </a>
                </li>
                @endrole

                <div class="sidebar-divider"></div>

                <!-- WORKFORCE Section -->
                <div class="sidebar-heading">Workforce</div>

                <li class="nav-item {{ request()->routeIs('attendance.index') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('attendance.index') }}">
                        <span class="nav-link-icon d-md-none d-lg-inline-block">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path
                                    d="M9 5h-2a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-12a2 2 0 0 0 -2 -2h-2" />
                                <path
                                    d="M9 3m0 2a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v0a2 2 0 0 1 -2 2h-2a2 2 0 0 1 -2 -2z" />
                                <path d="M9 14l2 2l4 -4" />
                            </svg>
                        </span>
                        <span class="nav-link-title">Daily Register</span>
                    </a>
                </li>

                <li class="nav-item {{ request()->routeIs('attendance.report') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('attendance.report') }}">
                        <span class="nav-link-icon d-md-none d-lg-inline-block">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M11.795 21h-6.795a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v4" />
                                <path d="M18 14v4h4" />
                                <path d="M18 18m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0" />
                                <path d="M15 3v4" />
                                <path d="M7 3v4" />
                                <path d="M3 11h16" />
                            </svg>
                        </span>
                        <span class="nav-link-title">Monthly Summary</span>
                    </a>
                </li>

                @hasanyrole('admin|manager')
                <div class="sidebar-divider"></div>

                <!-- MASTER DATA Section -->
                <div class="sidebar-heading">Master Data</div>

                <li class="nav-item {{ request()->routeIs('vehicles.*') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('vehicles.index') }}">
                        <span class="nav-link-icon d-md-none d-lg-inline-block">
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

                <li class="nav-item {{ request()->routeIs('metal-types.*') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('metal-types.index') }}">
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
                        <span class="nav-link-title">Metal Types</span>
                    </a>
                </li>

                <li class="nav-item {{ request()->routeIs('projects.*') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('projects.index') }}">
                        <span class="nav-link-icon d-md-none d-lg-inline-block">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M3 21l18 0" />
                                <path d="M9 8l1 0" />
                                <path d="M9 12l1 0" />
                                <path d="M9 16l1 0" />
                                <path d="M14 8l1 0" />
                                <path d="M14 12l1 0" />
                                <path d="M14 16l1 0" />
                                <path d="M5 21v-16a2 2 0 0 1 2 -2h10a2 2 0 0 1 2 2v16" />
                            </svg>
                        </span>
                        <span class="nav-link-title">Projects</span>
                    </a>
                </li>
                @endhasanyrole

                <div class="sidebar-divider"></div>

                <!-- REPORTS Section -->
                <div class="sidebar-heading">Reports</div>

                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle {{ request()->is('reports*', 'gate-passes/*report*') ? 'active' : '' }}"
                        href="#sidebar-reports" data-bs-toggle="dropdown" data-bs-auto-close="false" role="button"
                        aria-expanded="false">
                        <span class="nav-link-icon d-md-none d-lg-inline-block">
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
                <div class="sidebar-divider"></div>

                <!-- ADMINISTRATION Section -->
                <div class="sidebar-heading">Administration</div>

                <li class="nav-item {{ request()->routeIs('users.*') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('users.index') }}">
                        <span class="nav-link-icon d-md-none d-lg-inline-block">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0" />
                                <path d="M6 21v-2a4 4 0 0 1 4 -4h2.5" />
                                <path d="M19.001 19m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" />
                                <path d="M19.001 15.5v1.5" />
                                <path d="M19.001 21v1.5" />
                                <path d="M22.032 17.25l-1.299 .75" />
                                <path d="M17.27 20l-1.3 .75" />
                                <path d="M15.97 17.25l1.3 .75" />
                                <path d="M20.733 20l1.3 .75" />
                            </svg>
                        </span>
                        <span class="nav-link-title">Staff Management</span>
                    </a>
                </li>

                <li class="nav-item {{ request()->routeIs('audit-logs.*') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('audit-logs.index') }}">
                        <span class="nav-link-icon d-md-none d-lg-inline-block">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M14 3v4a1 1 0 0 0 1 1h4" />
                                <path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z" />
                                <path d="M12 10l0 4" />
                                <path d="M10 12l4 0" />
                                <path d="M12 17h.01" />
                            </svg>
                        </span>
                        <span class="nav-link-title">Audit Logs</span>
                    </a>
                </li>

                <li class="nav-item {{ request()->routeIs('backups.*') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('backups.index') }}">
                        <span class="nav-link-icon d-md-none d-lg-inline-block">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M12 6m-8 0a8 3 0 1 0 16 0a8 3 0 1 0 -16 0" />
                                <path d="M4 6v6a8 3 0 0 0 16 0v-6" />
                                <path d="M4 12v6a8 3 0 0 0 16 0v-6" />
                            </svg>
                        </span>
                        <span class="nav-link-title">Backups</span>
                    </a>
                </li>

                <li class="nav-item {{ request()->routeIs('settings.*') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('settings.index') }}">
                        <span class="nav-link-icon d-md-none d-lg-inline-block">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path
                                    d="M10.325 4.317c.426 -1.756 2.924 -1.756 3.35 0a1.724 1.724 0 0 0 2.573 1.066c1.543 -.94 3.31 .826 2.37 2.37a1.724 1.724 0 0 0 1.065 2.572c1.756 .426 1.756 2.924 0 3.35a1.724 1.724 0 0 0 -1.066 2.573c.94 1.543 -.826 3.31 -2.37 2.37a1.724 1.724 0 0 0 -2.572 1.065c-.426 1.756 -2.924 1.756 -3.35 0a1.724 1.724 0 0 0 -2.573 -1.066c-1.543 .94 -3.31 -.826 -2.37 -2.37a1.724 1.724 0 0 0 -1.065 -2.572c-1.756 -.426 -1.756 -2.924 0 -3.35a1.724 1.724 0 0 0 1.066 -2.573c-.94 -1.543 .826 -3.31 2.37 -2.37c1 .608 2.296 .07 2.572 -1.065z" />
                                <path d="M9 12a3 3 0 1 0 6 0a3 3 0 0 0 -6 0" />
                            </svg>
                        </span>
                        <span class="nav-link-title">System Settings</span>
                    </a>
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
                        <span class="nav-link-title">Dashboard</span>
                    </a>
                </li>
                @endrole

            </ul>

            <!-- User Info at Bottom (Mobile) -->
            <div class="sidebar-user d-lg-none">
                <div class="sidebar-user-info">
                    <div class="sidebar-user-avatar">
                        {{ substr(Auth::user()->name ?? 'U', 0, 1) }}
                    </div>
                    <div class="sidebar-user-details">
                        <div class="sidebar-user-name">{{ Auth::user()->name ?? 'User' }}</div>
                        <div class="sidebar-user-role">{{ Auth::user()->roles->pluck('name')->implode(', ') }}</div>
                    </div>
                </div>
                <form method="POST" action="{{ route('logout') }}" class="mt-2">
                    @csrf
                    <button type="submit" class="btn btn-ghost-secondary btn-sm w-100">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24"
                            stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round"
                            stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M14 8v-2a2 2 0 0 0 -2 -2h-7a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h7a2 2 0 0 0 2 -2v-2" />
                            <path d="M9 12h12l-3 -3" />
                            <path d="M18 15l3 -3" />
                        </svg>
                        Logout
                    </button>
                </form>
            </div>
        </div>
    </div>
</aside>

<!-- Mobile Menu Toggle Button -->
<button class="mobile-menu-toggle" onclick="toggleSidebar()">
    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2"
        stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
        <path d="M4 6l16 0" />
        <path d="M4 12l16 0" />
        <path d="M4 18l16 0" />
    </svg>
</button>

<!-- Mobile Backdrop -->
<div class="sidebar-backdrop" id="sidebarBackdrop" onclick="toggleSidebar()"></div>

<!-- Mobile Bottom Quick Bar -->
<nav class="mobile-quick-bar">
    <a href="{{ route('admin.dashboard') }}"
        class="quick-bar-item {{ request()->routeIs('admin.dashboard', 'dashboard') ? 'active' : '' }}">
        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2"
            stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
            <path d="M5 12l-2 0l9 -9l9 9l-2 0" />
            <path d="M5 12v7a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-7" />
            <path d="M9 21v-6a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v6" />
        </svg>
        Home
    </a>
    <a href="{{ route('gate-passes.index') }}"
        class="quick-bar-item {{ request()->routeIs('gate-passes.index') ? 'active' : '' }}">
        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2"
            stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
            <path d="M9 6l11 0" />
            <path d="M9 12l11 0" />
            <path d="M9 18l11 0" />
            <path d="M5 6l0 .01" />
            <path d="M5 12l0 .01" />
            <path d="M5 18l0 .01" />
        </svg>
        Passes
    </a>
    <a href="{{ route('clients.index') }}" class="quick-bar-item {{ request()->routeIs('clients.*') ? 'active' : '' }}">
        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2"
            stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
            <path d="M9 7m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0" />
            <path d="M3 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" />
            <path d="M16 3.13a4 4 0 0 1 0 7.75" />
            <path d="M21 21v-2a4 4 0 0 0 -3 -3.85" />
        </svg>
        Clients
    </a>
    <a href="{{ route('reports.index') }}" class="quick-bar-item {{ request()->is('reports*') ? 'active' : '' }}">
        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2"
            stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
            <path d="M3 12m0 1a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v6a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z" />
            <path d="M9 8m0 1a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v10a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z" />
            <path d="M15 4m0 1a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v14a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z" />
            <path d="M4 20l14 0" />
        </svg>
        Reports
    </a>
</nav>

<!-- Mobile FAB for Create Gate Pass -->
@hasanyrole('admin|manager|accountant')
<a href="{{ route('gate-passes.create') }}" class="mobile-fab">
    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2"
        stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
        <path d="M12 5l0 14" />
        <path d="M5 12l14 0" />
    </svg>
</a>
@endhasanyrole

<script>
    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        const backdrop = document.getElementById('sidebarBackdrop');

        sidebar.classList.toggle('show');
        backdrop.classList.toggle('show');

        // Prevent body scroll when sidebar is open
        document.body.style.overflow = sidebar.classList.contains('show') ? 'hidden' : '';
    }

    // Close sidebar when clicking a nav link on mobile
    document.querySelectorAll('#sidebar .nav-link:not(.dropdown-toggle), #sidebar .dropdown-item').forEach(link => {
        link.addEventListener('click', () => {
            if (window.innerWidth < 992) {
                toggleSidebar();
            }
        });
    });

    // Close sidebar on escape key
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            const sidebar = document.getElementById('sidebar');
            if (sidebar.classList.contains('show')) {
                toggleSidebar();
            }
        }
    });
</script>
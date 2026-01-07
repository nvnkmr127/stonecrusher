<aside class="navbar navbar-vertical navbar-expand-lg" style="background-color: #1e293b; color: #fff;">
    <div class="container-fluid">
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#sidebar-menu"
            aria-controls="sidebar-menu" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <h1 class="navbar-brand navbar-brand-autodark">
            <a href="{{ route('admin.dashboard') }}" class="text-decoration-none text-white">
                {{ config('app.name', 'StoneCrusher') }}
            </a>
        </h1>
        <div class="collapse navbar-collapse" id="sidebar-menu">
            <ul class="navbar-nav pt-lg-3">
                <!-- Dashboard -->
                <li class="nav-item {{ request()->routeIs('admin.dashboard') || request()->routeIs('dashboard') ? 'active' : '' }}">
                    <a class="nav-link text-white-50 {{ request()->routeIs('admin.dashboard') || request()->routeIs('dashboard') ? 'text-white' : '' }}" href="{{ route('admin.dashboard') }}">
                        <span class="nav-link-icon d-md-none d-lg-inline-block">
                            <!-- Icon: home -->
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

                <hr class="my-2 border-white-50">

                <!-- Operations -->
                <li class="nav-item">
                    <div class="nav-link text-white fw-bold disabled">
                        OPERATIONS
                    </div>
                </li>
                <li class="nav-item dropdown {{ request()->routeIs('gate-passes.*') ? 'active' : '' }}">
                    <a class="nav-link dropdown-toggle text-white-50 {{ request()->routeIs('gate-passes.*') ? 'text-white show' : '' }}" href="#navbar-operations" data-bs-toggle="dropdown"
                        data-bs-auto-close="false" role="button" aria-expanded="{{ request()->routeIs('gate-passes.*') ? 'true' : 'false' }}">
                        <span class="nav-link-icon d-md-none d-lg-inline-block">
                            <!-- Icon: package -->
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
                        <span class="nav-link-title">
                            Gate Passes
                        </span>
                    </a>
                    <div class="dropdown-menu text-bg-dark border-0 bg-transparent ps-3 {{ request()->routeIs('gate-passes.*') ? 'show' : '' }}">
                        <a class="dropdown-item text-white-50 {{ request()->routeIs('gate-passes.create') ? 'text-white active' : '' }}" href="{{ route('gate-passes.create') }}">Create New</a>
                        <a class="dropdown-item text-white-50 {{ request()->routeIs('gate-passes.index') ? 'text-white active' : '' }}" href="{{ route('gate-passes.index') }}">Check List</a>
                        <a class="dropdown-item text-white-50 {{ request()->routeIs('gate-passes.calculator') ? 'text-white active' : '' }}" href="{{ route('gate-passes.calculator') }}">Calculator</a>
                    </div>
                </li>
                <li class="nav-item {{ request()->routeIs('daily-closings.*') ? 'active' : '' }}">
                    <a class="nav-link text-white-50 {{ request()->routeIs('daily-closings.*') ? 'text-white' : '' }}" href="{{ route('daily-closings.index') }}">
                        <span class="nav-link-icon d-md-none d-lg-inline-block">
                            <!-- Icon: lock-open -->
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M5 11m0 2a2 2 0 0 1 2 -2h10a2 2 0 0 1 2 2v6a2 2 0 0 1 -2 2h-10a2 2 0 0 1 -2 -2z" />
                                <path d="M12 16m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0" />
                                <path d="M8 11v-4a4 4 0 1 1 8 0" />
                            </svg>
                        </span>
                        <span class="nav-link-title">
                            Daily Closing
                        </span>
                    </a>
                </li>

                <!-- CRM -->
                <li class="nav-item mt-3">
                    <div class="nav-link text-white fw-bold disabled">
                        CRM
                    </div>
                </li>
                <li class="nav-item {{ request()->routeIs('clients.*') ? 'active' : '' }}">
                    <a class="nav-link text-white-50 {{ request()->routeIs('clients.*') ? 'text-white' : '' }}" href="{{ route('clients.index') }}">
                        <span class="nav-link-icon d-md-none d-lg-inline-block">
                            <!-- Icon: users -->
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
                        <span class="nav-link-title">
                            Clients
                        </span>
                    </a>
                </li>
                <li class="nav-item {{ request()->routeIs('projects.*') ? 'active' : '' }}">
                    <a class="nav-link text-white-50 {{ request()->routeIs('projects.*') ? 'text-white' : '' }}" href="{{ route('projects.index') }}">
                        <span class="nav-link-icon d-md-none d-lg-inline-block">
                            <!-- Icon: briefcase -->
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M3 7m0 2a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v9a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2z" />
                                <path d="M8 7v-2a2 2 0 0 1 2 -2h4a2 2 0 0 1 2 2v2" />
                                <path d="M12 12l0 .01" />
                                <path d="M3 13a20 20 0 0 0 18 0" />
                            </svg>
                        </span>
                        <span class="nav-link-title">
                            Projects
                        </span>
                    </a>
                </li>

                <!-- Fleet -->
                <li class="nav-item mt-3">
                    <div class="nav-link text-white fw-bold disabled">
                        FLEET & INVENTORY
                    </div>
                </li>
                <li class="nav-item {{ request()->routeIs('vehicles.*') ? 'active' : '' }}">
                    <a class="nav-link text-white-50 {{ request()->routeIs('vehicles.*') ? 'text-white' : '' }}" href="{{ route('vehicles.index') }}">
                        <span class="nav-link-icon d-md-none d-lg-inline-block">
                            <!-- Icon: truck -->
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M7 17m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" />
                                <path d="M17 17m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" />
                                <path d="M5 17h-2v-11a1 1 0 0 1 1 -1h9v12m-4 0h6m4 0h2v-6h-8m0 -5h5l3 5" />
                            </svg>
                        </span>
                        <span class="nav-link-title">
                            Vehicles
                        </span>
                    </a>
                </li>
                <li class="nav-item {{ request()->routeIs('metal-types.*') ? 'active' : '' }}">
                    <a class="nav-link text-white-50 {{ request()->routeIs('metal-types.*') ? 'text-white' : '' }}" href="{{ route('metal-types.index') }}">
                        <span class="nav-link-icon d-md-none d-lg-inline-block">
                            <!-- Icon: box -->
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
                        <span class="nav-link-title">
                            Metal Types
                        </span>
                    </a>
                </li>

                <!-- HR -->
                <li class="nav-item mt-3">
                    <div class="nav-link text-white fw-bold disabled">
                        HR & STAFF
                    </div>
                </li>
                <li class="nav-item {{ request()->routeIs('attendance.*') ? 'active' : '' }}">
                    <a class="nav-link text-white-50 {{ request()->routeIs('attendance.*') ? 'text-white' : '' }}" href="{{ route('attendance.index') }}">
                        <span class="nav-link-icon d-md-none d-lg-inline-block">
                            <!-- Icon: calendar-time -->
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
                        <span class="nav-link-title">
                            Attendance
                        </span>
                    </a>
                </li>
                @role('admin')
                <li class="nav-item {{ request()->routeIs('users.*') ? 'active' : '' }}">
                    <a class="nav-link text-white-50 {{ request()->routeIs('users.*') ? 'text-white' : '' }}" href="{{ route('users.index') }}">
                        <span class="nav-link-icon d-md-none d-lg-inline-block">
                            <!-- Icon: user-check -->
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0" />
                                <path d="M6 21v-2a4 4 0 0 1 4 -4h4" />
                                <path d="M15 19l2 2l4 -4" />
                            </svg>
                        </span>
                        <span class="nav-link-title">
                            Users
                        </span>
                    </a>
                </li>
                @endrole

                <!-- Reports -->
                <li class="nav-item mt-3">
                    <div class="nav-link text-white fw-bold disabled">
                        REPORTS
                    </div>
                </li>
                <li class="nav-item dropdown {{ request()->routeIs('reports.*') || request()->routeIs('gate-passes.distance-report') || request()->routeIs('attendance.report') ? 'active' : '' }}">
                    <a class="nav-link dropdown-toggle text-white-50 {{ request()->routeIs('reports.*') || request()->routeIs('gate-passes.distance-report') || request()->routeIs('attendance.report') ? 'text-white show' : '' }}" href="#navbar-reports" data-bs-toggle="dropdown"
                        data-bs-auto-close="false" role="button" aria-expanded="{{ request()->routeIs('reports.*') || request()->routeIs('gate-passes.distance-report') || request()->routeIs('attendance.report') ? 'true' : 'false' }}">
                        <span class="nav-link-icon d-md-none d-lg-inline-block">
                            <!-- Icon: chart-bar -->
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M3 12m0 1a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v6a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z" />
                                <path d="M9 8m0 1a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v10a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z" />
                                <path d="M15 4m0 1a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v14a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z" />
                                <path d="M4 20l14 0" />
                            </svg>
                        </span>
                        <span class="nav-link-title">
                            Reports Center
                        </span>
                    </a>
                    <div class="dropdown-menu text-bg-dark border-0 bg-transparent ps-3 {{ request()->routeIs('reports.*') || request()->routeIs('gate-passes.distance-report') || request()->routeIs('attendance.report') ? 'show' : '' }}">
                        <a class="dropdown-item text-white-50 {{ request()->routeIs('reports.daily') ? 'text-white active' : '' }}" href="{{ route('reports.daily') }}">Daily Sales</a>
                        <a class="dropdown-item text-white-50 {{ request()->routeIs('reports.monthly') ? 'text-white active' : '' }}" href="{{ route('reports.monthly') }}">Monthly Sales</a>
                        <a class="dropdown-item text-white-50 {{ request()->routeIs('reports.outstanding') ? 'text-white active' : '' }}" href="{{ route('reports.outstanding') }}">Outstanding & Advance</a>
                        <a class="dropdown-item text-white-50 {{ request()->routeIs('gate-passes.distance-report') ? 'text-white active' : '' }}" href="{{ route('gate-passes.distance-report') }}">Distance Report</a>
                        <a class="dropdown-item text-white-50 {{ request()->routeIs('attendance.report') ? 'text-white active' : '' }}" href="{{ route('attendance.report') }}">Attendance Report</a>
                    </div>
                </li>

                <!-- System -->
                @role('admin')
                <li class="nav-item mt-3">
                    <div class="nav-link text-white fw-bold disabled">
                        SYSTEM
                    </div>
                </li>
                <li class="nav-item {{ request()->routeIs('settings.*') ? 'active' : '' }}">
                    <a class="nav-link text-white-50 {{ request()->routeIs('settings.*') ? 'text-white' : '' }}" href="{{ route('settings.index') }}">
                        <span class="nav-link-icon d-md-none d-lg-inline-block">
                            <!-- Icon: settings -->
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M10.325 4.317c.426 -1.756 2.924 -1.756 3.35 0a1.724 1.724 0 0 0 2.573 1.066c1.543 -.94 3.31 .826 2.37 2.37a1.724 1.724 0 0 0 1.065 2.572c1.756 .426 1.756 2.924 0 3.35a1.724 1.724 0 0 0 -1.066 2.573c.94 1.543 -.826 3.31 -2.37 2.37a1.724 1.724 0 0 0 -2.572 1.065c-.426 1.756 -2.924 1.756 -3.35 0a1.724 1.724 0 0 0 -2.573 -1.066c-1.543 .94 -3.31 -.826 -2.37 -2.37a1.724 1.724 0 0 0 -1.065 -2.572c-1.756 -.426 -1.756 -2.924 0 -3.35a1.724 1.724 0 0 0 1.066 -2.573c-.94 -1.543 .826 -3.31 2.37 -2.37c1 .608 2.296 .07 2.572 -1.065z" />
                                <path d="M9 12a3 3 0 1 0 6 0a3 3 0 0 0 -6 0" />
                            </svg>
                        </span>
                        <span class="nav-link-title">
                            Settings
                        </span>
                    </a>
                </li>
                <li class="nav-item {{ request()->routeIs('backups.*') ? 'active' : '' }}">
                    <a class="nav-link text-white-50 {{ request()->routeIs('backups.*') ? 'text-white' : '' }}" href="{{ route('backups.index') }}">
                        <span class="nav-link-icon d-md-none d-lg-inline-block">
                            <!-- Icon: database -->
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M12 6m-8 0a8 3 0 1 0 16 0a8 3 0 1 0 -16 0" />
                                <path d="M4 6v6a8 3 0 0 0 16 0v-6" />
                                <path d="M4 12v6a8 3 0 0 0 16 0v-6" />
                            </svg>
                        </span>
                        <span class="nav-link-title">
                            Backups
                        </span>
                    </a>
                </li>
                <li class="nav-item {{ request()->routeIs('audit-logs.*') ? 'active' : '' }}">
                    <a class="nav-link text-white-50 {{ request()->routeIs('audit-logs.*') ? 'text-white' : '' }}" href="{{ route('audit-logs.index') }}">
                        <span class="nav-link-icon d-md-none d-lg-inline-block">
                            <!-- Icon: file-text -->
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M14 3v4a1 1 0 0 0 1 1h4" />
                                <path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z" />
                                <path d="M9 9l1 0" />
                                <path d="M9 13l6 0" />
                                <path d="M9 17l6 0" />
                            </svg>
                        </span>
                        <span class="nav-link-title">
                            Audit Logs
                        </span>
                    </a>
                </li>
                @endrole
            </ul>
        </div>
    </div>
</aside>

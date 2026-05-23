<aside class="navbar navbar-vertical navbar-expand-lg navbar-dark border-end-0 shadow-lg premium-sidebar">
    <div class="container-fluid px-0 h-100 d-flex flex-column">

        <!-- Sidebar Header (Logo) -->
        <div class="brand-section px-4 py-4 d-none d-lg-block">
            <a href="{{ route('admin.dashboard') }}" class="brand-link group">
                <div class="brand-icon shadow-premium">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-building-fortress" width="24"
                        height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                        stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path d="M7 21h1a1 1 0 0 0 1 -1v-1h0a3 3 0 0 1 6 0h0v1a1 1 0 0 0 1 1h1" />
                        <path d="M5 21v-7m0 -4v-5a2 2 0 1 1 4 0v5m0 -4h6m0 4v-5a2 2 0 1 1 4 0v5m0 -4v7" />
                        <path d="M11 14h2" />
                    </svg>
                </div>
                <div class="brand-text">
                    <span class="brand-name">{{ config('app.name', 'StoneCrusher') }}</span>
                    <span class="brand-tag">ERP SYSTEM</span>
                </div>
            </a>
        </div>

        <div class="sidebar-content flex-fill overflow-y-auto" id="sidebar-menu">
            <!-- Mobile Sidebar Header -->
            <div class="d-lg-none d-flex align-items-center justify-content-between p-4 border-bottom border-white-10">
                <div class="h3 mb-0 fw-bold text-white tracking-wide">{{ config('app.name', 'StoneCrusher') }}</div>
                <button type="button" class="btn-close btn-close-white" id="mobile-menu-close" aria-label="Close"></button>
            </div>

            <ul class="navbar-nav py-3 d-flex flex-column gap-1">
                <!-- Dashboard -->
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.dashboard') || request()->routeIs('dashboard') ? 'active' : '' }}"
                        href="{{ route('admin.dashboard') }}">
                        <span class="nav-link-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2"
                                stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M5 12l-2 0l9 -9l9 9l-2 0" />
                                <path d="M5 12v7a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-7" />
                                <path d="M9 21v-6a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v6" />
                            </svg>
                        </span>
                        <span class="nav-link-title">Dashboard</span>
                    </a>
                </li>

                <!-- Quick Actions Section -->
                <li class="nav-item">
                    <div class="sidebar-section-header">Fast Entry</div>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-primary-emphasis" href="{{ route('gate-passes.create') }}">
                        <span class="nav-link-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-plus" width="24" height="24"
                                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round"
                                stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M12 5l0 14" />
                                <path d="M5 12l14 0" />
                            </svg>
                        </span>
                        <span class="nav-link-title">New Entry</span>
                        </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-info-emphasis" href="{{ route('clients.index') }}">
                                <span class="nav-link-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-users" width="24" height="24"
                                        viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round"
                                        stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M9 7m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0" />
                                        <path d="M3 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" />
                                    </svg>
                                </span>
                                <span class="nav-link-title">Client List</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-success-emphasis" href="{{ route('attendance.bulk') }}">
                        <span class="nav-link-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-user-check" width="24" height="24"
                                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round"
                                stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0" />
                                <path d="M6 21v-2a4 4 0 0 1 4 -4h4" />
                                <path d="M15 19l2 2l4 -4" />
                            </svg>
                        </span>
                        <span class="nav-link-title">Attendance</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-warning-emphasis" href="{{ route('salary-advances.create') }}">
                        <span class="nav-link-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-cash" width="24" height="24"
                                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round"
                                stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M7 9m0 2a2 2 0 0 1 2 -2h10a2 2 0 0 1 2 2v6a2 2 0 0 1 -2 2h-10a2 2 0 0 1 -2 -2z" />
                                <path d="M18 12s-2.5 2 -5.5 2s-5.5 -2 -5.5 -2" />
                            </svg>
                        </span>
                        <span class="nav-link-title">Advance Salary</span>
                    </a>
                </li>

                <!-- Tasks Section -->
                <li class="nav-item">
                    <div class="sidebar-section-header">Office Work</div>
                </li>
                <li class="nav-item dropdown" x-data="{ open: {{ request()->routeIs('gate-passes.*') ? 'true' : 'false' }} }">
                    <a class="nav-link dropdown-toggle"
                        :class="{ 'show': open, 'active': {{ request()->routeIs('gate-passes.*') ? 'true' : 'false' }} }"
                        href="#navbar-operations" @click.prevent="open = !open" role="button" :aria-expanded="open">
                        <span class="nav-link-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2"
                                stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M12 3l8 4.5l0 9l-8 4.5l-8 -4.5l0 -9l8 -4.5" />
                                <path d="M12 12l8 -4.5" />
                                <path d="M12 12l0 9" />
                                <path d="M12 12l-8 -4.5" />
                            </svg>
                        </span>
                        <span class="nav-link-title">Gate Passes</span>
                    </a>
                    <div class="dropdown-menu" :class="{ 'show': open }">
                        <a class="dropdown-item {{ request()->routeIs('gate-passes.create') ? 'active' : '' }}"
                            href="{{ route('gate-passes.create') }}">Add New</a>
                        <a class="dropdown-item {{ request()->routeIs('gate-passes.index') ? 'active' : '' }}"
                            href="{{ route('gate-passes.index') }}">History</a>
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
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2"
                                stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M5 11m0 2a2 2 0 0 1 2 -2h10a2 2 0 0 1 2 2v6a2 2 0 0 1 -2 2h-10a2 2 0 0 1 -2 -2z" />
                                <path d="M8 11v-4a4 4 0 1 1 8 0" />
                            </svg>
                        </span>
                        <span class="nav-link-title">Day Close</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('projects.*') ? 'active' : '' }}" href="{{ route('projects.index') }}">
                        <span class="nav-link-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2"
                                stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M3 21v-13l9 -4l9 4v13" />
                                <path d="M13 13h4v8h-10v-6h6" />
                            </svg>
                        </span>
                        <span class="nav-link-title">All Projects</span>
                    </a>
                </li>

                <!-- CRM Section -->
                <li class="nav-item">
                    <div class="sidebar-section-header">Our Clients</div>
                </li>
                <li class="nav-item dropdown" x-data="{ open: {{ request()->routeIs('clients.*') ? 'true' : 'false' }} }">
                    <a class="nav-link dropdown-toggle"
                        :class="{ 'show': open, 'active': {{ request()->routeIs('clients.*') ? 'true' : 'false' }} }" href="#navbar-crm"
                        @click.prevent="open = !open" role="button" :aria-expanded="open">
                        <span class="nav-link-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2"
                                stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M9 7m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0" />
                                <path d="M3 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" />
                            </svg>
                        </span>
                        <span class="nav-link-title">Client List</span>
                    </a>
                    <div class="dropdown-menu" :class="{ 'show': open }">
                        <a class="dropdown-item {{ request()->routeIs('clients.index') ? 'active' : '' }}"
                            href="{{ route('clients.index') }}">Directory</a>
                        <a class="dropdown-item {{ request()->routeIs('clients.create') ? 'active' : '' }}"
                            href="{{ route('clients.create') }}">Add New</a>
                    </div>
                </li>

                <!-- Quarry & Crusher Section -->
                <li class="nav-item">
                    <div class="sidebar-section-header">Quarry & Crusher</div>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('quarry.index') ? 'active' : '' }}" href="{{ route('quarry.index') }}">
                        <span class="nav-link-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-shovel" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <path d="M17 4l3 3" />
                                <path d="M18.5 5.5l-8.5 8.5" />
                                <path d="M8.5 12.5l-2.5 2.5a2 2 0 1 0 2.828 2.828l2.5 -2.5" />
                                <path d="M13 16l-3 -3" />
                                <path d="M3 21h4" />
                            </svg>
                        </span>
                        <span class="nav-link-title">Quarry Ops</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('crusher.index') ? 'active' : '' }}" href="{{ route('crusher.index') }}">
                        <span class="nav-link-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-hammer" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <path d="M11.414 10l-7.383 7.414a2 2 0 1 0 2.829 2.828l7.414 -7.386" />
                                <path d="M16.828 8.172l-1.414 -1.414l1.414 -1.414l2.829 2.829l-1.414 1.414z" />
                                <path d="M12.586 9.414l-1.414 -1.414l4.242 -4.243l1.414 1.414z" />
                            </svg>
                        </span>
                        <span class="nav-link-title">Crusher Ops</span>
                    </a>
                </li>

                <!-- Fleet & Inventory Section -->
                <li class="nav-item">
                    <div class="sidebar-section-header">Vehicles & Fuel</div>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('vehicles.*') ? 'active' : '' }}" href="{{ route('vehicles.index') }}">
                        <span class="nav-link-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2"
                                stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M7 17m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" />
                                <path d="M17 17m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" />
                                <path d="M5 17h-2v-11a1 1 0 0 1 1 -1h9v12m-4 0h6m4 0h2v-6h-8m0 -5h5l3 5" />
                            </svg>
                        </span>
                        <span class="nav-link-title">Our Vehicles</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('vehicle-maintenance.*') ? 'active' : '' }}"
                        href="{{ route('vehicle-maintenance.index') }}">
                        <span class="nav-link-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2"
                                stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M7 10h3v-3l-3.5 -3.5" />
                            </svg>
                        </span>
                        <span class="nav-link-title">Repair Log</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('diesel.*') ? 'active' : '' }}" href="{{ route('diesel.index') }}">
                        <span class="nav-link-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-gas-station" width="24" height="24"
                                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round"
                                stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                <path d="M14 11h1a2 2 0 0 1 2 2v3a1.5 1.5 0 0 0 3 0v-7l-3 -3"></path>
                                <path d="M4 20v-14a2 2 0 0 1 2 -2h6a2 2 0 0 1 2 2v14"></path>
                            </svg>
                        </span>
                        <span class="nav-link-title">Diesel Used</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('diesel-stocks.*') ? 'active' : '' }}"
                        href="{{ route('diesel-stocks.index') }}">
                        <span class="nav-link-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-barrel" width="24" height="24"
                                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round"
                                stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                <path d="M10 21v-6m4 0v6"></path>
                                <path d="M5 7v10c0 1.657 3.134 3 7 3s7 -1.343 7 -3"></path>
                            </svg>
                        </span>
                        <span class="nav-link-title">Diesel Stock</span>
                    </a>
                </li>

                <!-- Analytics Section -->
                <li class="nav-item">
                    <div class="sidebar-section-header">Reports</div>
                </li>
                <li class="nav-item dropdown"
                    x-data="{ open: {{ (request()->routeIs('reports.*') || request()->routeIs('gate-passes.distance-report') || request()->routeIs('attendance.report')) ? 'true' : 'false' }} }">
                    <a class="nav-link dropdown-toggle"
                        :class="{ 'show': open, 'active': {{ (request()->routeIs('reports.*') || request()->routeIs('gate-passes.distance-report') || request()->routeIs('attendance.report')) ? 'true' : 'false' }} }"
                        href="#navbar-reports" @click.prevent="open = !open" role="button" :aria-expanded="open">
                        <span class="nav-link-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2"
                                stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M3 12m0 1a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v6" />
                            </svg>
                        </span>
                        <span class="nav-link-title">Report Center</span>
                    </a>
                    <div class="dropdown-menu" :class="{ 'show': open }">
                        <a class="dropdown-item {{ request()->routeIs('reports.daily') ? 'active' : '' }}"
                            href="{{ route('reports.daily') }}">Daily Sales</a>
                        <a class="dropdown-item {{ request()->routeIs('reports.monthly') ? 'active' : '' }}"
                            href="{{ route('reports.monthly') }}">Monthly Sales</a>
                        <a class="dropdown-item {{ request()->routeIs('reports.operational-profit-loss') ? 'active' : '' }}"
                            href="{{ route('reports.operational-profit-loss') }}">Operational P&L</a>
                        <a class="dropdown-item {{ request()->routeIs('reports.outstanding') ? 'active' : '' }}"
                            href="{{ route('reports.outstanding') }}">Debt & Advance</a>
                        <a class="dropdown-item {{ request()->routeIs('gate-passes.distance-report') ? 'active' : '' }}"
                            href="{{ route('gate-passes.distance-report') }}">Distance Report</a>
                        <a class="dropdown-item {{ request()->routeIs('reports.vehicle-usage') ? 'active' : '' }}"
                            href="{{ route('reports.vehicle-usage') }}">Vehicle Use</a>
                        <a class="dropdown-item {{ request()->routeIs('reports.custom') ? 'active' : '' }}"
                            href="{{ route('reports.custom') }}">Choose Date</a>
                        <a class="dropdown-item {{ request()->routeIs('attendance.report') ? 'active' : '' }}"
                            href="{{ route('attendance.report') }}">Staff Attendance</a>
                    </div>
                </li>

                <!-- Administration Section -->
                @role('admin')
                <li class="nav-item">
                    <div class="sidebar-section-header">Master Data</div>
                </li>
                <li class="nav-item dropdown"
                    x-data="{ open: {{ (request()->routeIs('clients.*') || request()->routeIs('vehicles.*') || request()->routeIs('metal-types.*') || request()->routeIs('projects.*') || request()->routeIs('employees.*')) ? 'true' : 'false' }} }">
                    <a class="nav-link dropdown-toggle"
                        :class="{ 'show': open, 'active': {{ (request()->routeIs('clients.*') || request()->routeIs('vehicles.*') || request()->routeIs('metal-types.*') || request()->routeIs('projects.*') || request()->routeIs('employees.*')) ? 'true' : 'false' }} }"
                        href="#navbar-master-data" @click.prevent="open = !open" role="button" :aria-expanded="open">
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
                        <span class="nav-link-title">Master Data</span>
                    </a>
                    <div class="dropdown-menu" :class="{ 'show': open }">
                        <a class="dropdown-item {{ request()->routeIs('clients.*') ? 'active' : '' }}" href="{{ route('clients.index') }}">Clients</a>
                        <a class="dropdown-item {{ request()->routeIs('vehicles.*') ? 'active' : '' }}" href="{{ route('vehicles.index') }}">Vehicles</a>
                        <a class="dropdown-item {{ request()->routeIs('metal-types.*') ? 'active' : '' }}" href="{{ route('metal-types.index') }}">Metal Types</a>
                        <a class="dropdown-item {{ request()->routeIs('projects.*') ? 'active' : '' }}" href="{{ route('projects.index') }}">Projects</a>
                        <a class="dropdown-item {{ request()->routeIs('employees.*') ? 'active' : '' }}" href="{{ route('employees.index') }}">Employees</a>
                    </div>
                </li>

                <li class="nav-item">
                    <div class="sidebar-section-header">System</div>
                </li>
                <li class="nav-item dropdown"
                    x-data="{ open: {{ (request()->routeIs('users.*') || request()->routeIs('settings.*')) ? 'true' : 'false' }} }">
                    <a class="nav-link dropdown-toggle"
                        :class="{ 'show': open, 'active': {{ (request()->routeIs('users.*') || request()->routeIs('settings.*')) ? 'true' : 'false' }} }"
                        href="#navbar-system" @click.prevent="open = !open" role="button" :aria-expanded="open">
                        <span class="nav-link-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2"
                                stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M10.325 4.317c.426 -1.756 2.924 -1.756 3.35 0" />
                            </svg>
                        </span>
                        <span class="nav-link-title">Settings</span>
                    </a>
                    <div class="dropdown-menu" :class="{ 'show': open }">
                        <a class="dropdown-item" href="{{ route('users.index') }}">Manage Users</a>
                        <a class="dropdown-item" href="{{ route('attendance.index') }}">Attendance Logs</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="{{ route('settings.index') }}">Global Setup</a>
                        <a class="dropdown-item" href="{{ route('backups.index') }}">System Backups</a>
                    </div>
                </li>
                @endrole
            </ul>
        </div>

        <!-- Sidebar Footer -->
        <div class="px-4 py-3 border-top border-white-10 mt-auto sidebar-status">
            <div class="d-flex align-items-center gap-2">
                <span class="status-indicator-dot dot-online"></span>
                <span class="small fw-bold text-white-50 opacity-50 uppercase tracking-widest">System is working</span>
            </div>
        </div>
    </div>
</aside>
<style>
    /* Premium Sidebar Design System */
    .premium-sidebar {
        background: #0f172a !important;
        /* Deepest blue base */
        position: relative;
        overflow: hidden;
        height: 100vh;
        border-right: 1px solid rgba(255, 255, 255, 0.05) !important;
    }

    .brand-section {
        position: relative;
        z-index: 2;
        background: rgba(15, 23, 42, 0.82);
        backdrop-filter: blur(10px);
    }

    .brand-link {
        display: flex;
        align-items: center;
        gap: 1rem;
        text-decoration: none;
        transition: all 0.3s ease;
    }

    .brand-icon {
        background: #3b82f6;
        /* Premium Accent */
        color: white;
        border-radius: 12px;
        padding: 0.6rem;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }

    .brand-link:hover .brand-icon {
        transform: scale(1.1) rotate(5deg);
        box-shadow: 0 0 20px rgba(59, 130, 246, 0.5);
    }

    .brand-text {
        display: flex;
        flex-direction: column;
    }

    .brand-name {
        color: white;
        font-size: 1.25rem;
        font-weight: 800;
        line-height: 1;
        letter-spacing: -0.025em;
        margin-bottom: 0.25rem;
    }

    .brand-tag {
        color: #64748b;
        font-size: 0.65rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.1em;
    }

    /* Nav Item Modernization */
    .navbar-nav .nav-link {
        margin: 0.125rem 0.75rem !important;
        padding: 0.75rem 1rem !important;
        border-radius: 10px !important;
        color: #94a3b8 !important;
        font-weight: 500 !important;
        transition: all 0.23s cubic-bezier(0.4, 0, 0.2, 1) !important;
        border: 1px solid transparent !important;
        display: flex !important;
        align-items: center !important;
    }

    .navbar-nav .nav-link:hover {
        color: #f8fafc !important;
        background: rgba(255, 255, 255, 0.04) !important;
    }

    .navbar-nav .nav-link.active {
        background: linear-gradient(90deg, rgba(59, 130, 246, 0.15) 0%, rgba(59, 130, 246, 0.05) 100%) !important;
        color: #60a5fa !important;
        border: 1px solid rgba(59, 130, 246, 0.2) !important;
    }

    .nav-link-icon {
        margin-right: 0.875rem !important;
        opacity: 0.7;
        transition: transform 0.2s ease;
        display: flex;
        align-items: center;
    }

    .nav-link.active .nav-link-icon {
        opacity: 1;
        transform: scale(1.1);
    }

    /* Section Separation */
    .sidebar-section-header {
        margin-top: 1.5rem;
        padding: 0.5rem 1.75rem;
        font-size: 0.65rem;
        font-weight: 800;
        color: #475569 !important;
        text-transform: uppercase;
        letter-spacing: 0.15em;
    }

    /* Dropdown Customization */
    .navbar-vertical .dropdown-menu {
        border-left: 2px solid rgba(255, 255, 255, 0.05) !important;
        margin-left: 1.75rem !important;
        padding-left: 0.5rem !important;
        background: transparent !important;
        border-radius: 0 !important;
        box-shadow: none !important;
    }

    .navbar-vertical .dropdown-item {
        font-size: 0.825rem !important;
        padding: 0.5rem 1rem !important;
        color: #64748b !important;
        border-radius: 8px !important;
        margin: 0.1rem 0 !important;
        transition: all 0.2s ease;
    }

    .navbar-vertical .dropdown-item:hover,
    .navbar-vertical .dropdown-item.active {
        color: #f8fafc !important;
        background: rgba(255, 255, 255, 0.03) !important;
    }

    /* Status Dot */
    .status-indicator-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        display: inline-block;
    }

    .dot-online {
        background: #10b981;
        box-shadow: 0 0 8px rgba(16, 185, 129, 0.6);
    }

    /* Scrollbar Sleekness */
    .sidebar-content::-webkit-scrollbar {
        width: 3px;
    }

    .sidebar-content::-webkit-scrollbar-thumb {
        background: #1e293b;
        border-radius: 10px;
    }
</style>
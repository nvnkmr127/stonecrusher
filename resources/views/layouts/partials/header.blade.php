<header class="navbar navbar-expand-md d-print-none border-bottom-0 bg-white/80 backdrop-blur-sm min-h-[4rem]">
    <div class="container-xl d-flex justify-content-between align-items-center">
        <!-- Sidebar Toggle (Mobile) -->
        <button class="btn btn-icon btn-ghost-secondary d-md-none" type="button" id="mobile-menu-trigger"
            aria-label="Toggle navigation">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24"
                stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                <path d="M4 6l16 0" />
                <path d="M4 12l16 0" />
                <path d="M4 18l16 0" />
            </svg>
        </button>

        <!-- Brand Name (Mobile Only) -->
        <h1 class="navbar-brand navbar-brand-autodark d-md-none ms-2 me-auto">
            <a href="{{ route('admin.dashboard') }}" class="text-decoration-none text-dark fw-bold">
                {{ config('app.name', 'StoneCrusher') }}
            </a>
        </h1>

        <!-- Center Search (Hidden on small mobile, visible on md+) -->
        <div class="d-none d-md-flex position-relative mx-auto">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon header-search-icon" width="24" height="24"
                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round"
                stroke-linejoin="round">
                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                <path d="M10 10m-7 0a7 7 0 1 0 14 0a7 7 0 1 0 -14 0" />
                <path d="M21 21l-6 -6" />
            </svg>
            <input type="text" class="form-control header-search-input" placeholder="Search..." aria-label="Search">
        </div>

        <!-- Right Side Actions -->
        <div class="navbar-nav flex-row order-md-last align-items-center">
            <!-- Mobile Search Toggle (Optional, can just hide search on mobile for now) -->

            <!-- Notifications -->
            <div class="nav-item d-none d-md-flex me-3">
                <a href="#" class="nav-link px-0" tabindex="-1" aria-label="Show notifications">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24"
                        stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round"
                        stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path
                            d="M10 5a2 2 0 1 1 4 0a7 7 0 0 1 4 6v3a4 4 0 0 0 2 3h-16a4 4 0 0 0 2 -3v-3a7 7 0 0 1 4 -6" />
                        <path d="M9 17v1a3 3 0 0 0 6 0v-1" />
                    </svg>
                    <span class="badge bg-red badge-blink"></span>
                </a>
            </div>

            <!-- User Menu -->
            <div class="nav-item dropdown" x-data="{ open: false }" @click.outside="open = false">
                <a href="#" class="nav-link d-flex lh-1 text-reset p-0 align-items-center" @click.prevent="open = !open"
                    :class="{ 'show': open }" :aria-expanded="open" aria-label="Open user menu">
                    <span class="avatar avatar-sm avatar-initials rounded-circle" style="background-image: none;">
                        {{ substr(Auth::user()?->name ?? 'U', 0, 1) }}
                    </span>
                    <div class="d-none d-xl-block ps-2 text-start">
                        <div class="fw-bold text-dark">{{ Auth::user()?->name ?? 'User' }}</div>
                        <div class="small text-muted" style="margin-top: -2px;">
                            {{ Auth::user()?->roles->pluck('name')->implode(', ') ?? 'Guest' }}
                        </div>
                    </div>
                </a>
                <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow" :class="{ 'show': open }">
                    <a href="{{ route('profile.edit') }}" class="dropdown-item d-flex align-items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-user-circle"
                            width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                            fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" />
                            <path d="M12 10m-3 0a3 3 0 1 0 6 0a3 3 0 1 0 -6 0" />
                            <path d="M6.168 18.849a4 4 0 0 1 3.832 -2.849h4a4 4 0 0 1 3.834 2.855" />
                        </svg>
                        Profile
                    </a>
                    <!-- Settings Link -->
                    <a href="{{ route('settings.index') }}" class="dropdown-item d-flex align-items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-settings" width="24"
                            height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path
                                d="M10.325 4.317c.426 -1.756 2.924 -1.756 3.35 0a1.724 1.724 0 0 0 2.573 1.066c1.543 -.94 3.31 .826 2.37 2.37a1.724 1.724 0 0 0 1.065 2.572c1.756 .426 1.756 2.924 0 3.35a1.724 1.724 0 0 0 -1.066 2.573c.94 1.543 -.826 3.31 -2.37 2.37a1.724 1.724 0 0 0 -2.572 1.065c-.426 1.756 -2.924 1.756 -3.35 0a1.724 1.724 0 0 0 -2.573 -1.066c-1.543 .94 -3.31 -.826 -2.37 -2.37a1.724 1.724 0 0 0 -1.065 -2.572c-1.756 -.426 -1.756 -2.924 0 -3.35a1.724 1.724 0 0 0 1.066 -2.573c-.94 -1.543 .826 -3.31 2.37 -2.37c1 .608 2.296 .07 2.572 -1.065z" />
                            <path d="M9 12a3 3 0 1 0 6 0a3 3 0 0 0 -6 0" />
                        </svg>
                        Settings
                    </a>

                    <div class="dropdown-divider"></div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <a href="{{ route('logout') }}" onclick="event.preventDefault(); this.closest('form').submit();"
                            class="dropdown-item d-flex align-items-center gap-2 text-danger">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-logout"
                                width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path
                                    d="M14 8v-2a2 2 0 0 0 -2 -2h-7a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h7a2 2 0 0 0 2 -2v-2" />
                                <path d="M9 12h12l-3 -3" />
                                <path d="M18 15l3 -3" />
                            </svg>
                            Logout
                        </a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</header>
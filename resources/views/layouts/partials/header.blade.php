<header class="navbar navbar-expand-md d-print-none premium-header">
    <div class="container-xl d-flex justify-content-between align-items-center">
        <!-- Sidebar Toggle (Mobile) -->
        <button class="btn btn-icon btn-ghost-secondary d-md-none" type="button" id="mobile-menu-trigger" aria-label="Toggle navigation">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none" /><path d="M4 6l16 0" /><path d="M4 12l16 0" /><path d="M4 18l16 0" /></svg>
        </button>

        <!-- Brand Name (Mobile Only) -->
        <h1 class="navbar-brand navbar-brand-autodark d-md-none ms-2 me-auto">
            <a href="{{ route('admin.dashboard') }}" class="text-decoration-none text-dark fw-bold">
                {{ config('app.name', 'StoneCrusher') }}
            </a>
        </h1>

        <!-- Center Search - Premium Styled -->
        <div class="d-none d-md-flex search-wrapper">
            <div class="position-relative">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon header-search-icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none" /><path d="M10 10m-7 0a7 7 0 1 0 14 0a7 7 0 1 0 -14 0" /><path d="M21 21l-6 -6" /></svg>
                <input type="text" class="form-control header-search-input-premium" placeholder="Universal Search (Ctrl + K)..." id="global-search">
            </div>
        </div>

        <!-- Right Side Actions -->
        <div class="navbar-nav flex-row order-md-last align-items-center gap-2">
            <!-- Notifications - Premium Badge -->
            <div class="nav-item d-none d-md-flex">
                <a href="#" class="nav-link-premium rounded-circle" id="notification-trigger">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none" /><path d="M10 5a2 2 0 1 1 4 0a7 7 0 0 1 4 6v3a4 4 0 0 0 2 3h-16a4 4 0 0 0 2 -3v-3a7 7 0 0 1 4 -6" /><path d="M9 17v1a3 3 0 0 0 6 0v-1" /></svg>
                    <span class="badge-premium-dot"></span>
                </a>
            </div>

            <!-- User Menu - Refined Profile -->
            <div class="nav-item dropdown" x-data="{ open: false }" @click.outside="open = false">
                <a href="#" class="nav-link user-profile-link px-2 d-flex align-items-center" @click.prevent="open = !open" :class="{ 'active': open }">
                    <div class="avatar avatar-sm rounded-12 bg-primary-lt text-primary fw-bold shadow-sm border border-white">
                        {{ substr(Auth::user()?->name ?? 'U', 0, 1) }}
                    </div>
                    <div class="d-none d-xl-block ps-2">
                        <div class="fw-bold text-dark fs-4 lh-1">{{ Auth::user()?->name ?? 'User' }}</div>
                        <div class="text-muted small text-uppercase tracking-wider fw-bold" style="font-size: 0.6rem;">{{ Auth::user()?->roles->pluck('name')->first() ?? 'Staff' }}</div>
                    </div>
                </a>
                <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow dropdown-menu-premium" :class="{ 'show': open }">
                    <div class="dropdown-header text-uppercase text-muted fw-bold small">Account Management</div>
                    <a href="{{ route('profile.edit') }}" class="dropdown-item d-flex align-items-center gap-2">
                        <div class="icon-box bg-blue-lt"><svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none" /><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" /><path d="M12 10m-3 0a3 3 0 1 0 6 0a3 3 0 1 0 -6 0" /><path d="M6.168 18.849a4 4 0 0 1 3.832 -2.849h4a4 4 0 0 1 3.834 2.855" /></svg></div>
                        Personal Profile
                    </a>
                    @role('admin')
                    <a href="{{ route('settings.index') }}" class="dropdown-item d-flex align-items-center gap-2">
                        <div class="icon-box bg-purple-lt"><svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none" /><path d="M10.325 4.317c.426 -1.756 2.924 -1.756 3.35 0a1.724 1.724 0 0 0 2.573 1.066c1.543 -.94 3.31 .826 2.37 2.37a1.724 1.724 0 0 0 1.065 2.572c1.756 .426 1.756 2.924 0 3.35a1.724 1.724 0 0 0 -1.066 2.573c.94 1.543 -.826 3.31 -2.37 2.37a1.724 1.724 0 0 0 -2.572 1.065c-.426 1.756 -2.924 1.756 -3.35 0a1.724 1.724 0 0 0 -2.573 -1.066c-1.543 .94 -3.31 -.826 -2.37 -2.37a1.724 1.724 0 0 0 -1.065 -2.572c-1.756 -.426 -1.756 -2.924 0 -3.35a1.724 1.724 0 0 0 1.066 -2.573c-.94 -1.543 .826 -3.31 2.37 -2.37c1 .608 2.296 .07 2.572 -1.065z" /></svg></div>
                        System Settings
                    </a>
                    @endrole

                    <div class="dropdown-divider"></div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <a href="{{ route('logout') }}" onclick="event.preventDefault(); this.closest('form').submit();" class="dropdown-item d-flex align-items-center gap-2 text-danger fw-bold">
                            <div class="icon-box bg-red-lt"><svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none" /><path d="M14 8v-2a2 2 0 0 0 -2 -2h-7a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h7a2 2 0 0 0 2 -2v-2" /><path d="M9 12h12l-3 -3" /></svg></div>
                            Sign Out
                        </a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</header>

<style>
.premium-header {
    background: rgba(255, 255, 255, 0.7) !important;
    backdrop-filter: blur(12px) saturate(180%);
    border-bottom: 1px solid rgba(0, 0, 0, 0.05) !important;
    position: sticky;
    top: 0;
    z-index: 1000;
}

.search-wrapper {
    flex: 1;
    max-width: 500px;
}

.header-search-input-premium {
    background: #f1f5f9 !important;
    border: none !important;
    border-radius: 12px !important;
    padding-left: 2.75rem !important;
    height: 42px !important;
    font-size: 0.9rem !important;
    transition: all 0.3s ease !important;
}

.header-search-input-premium:focus {
    background: #ffffff !important;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05), 0 0 0 2px rgba(59, 130, 246, 0.1) !important;
    transform: translateY(-1px);
}

.header-search-icon {
    position: absolute;
    left: 1rem;
    top: 50%;
    transform: translateY(-50%);
    color: #94a3b8;
    z-index: 1;
}

.nav-link-premium {
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #64748b;
    position: relative;
    transition: all 0.2s ease;
}

.nav-link-premium:hover {
    background: #f1f5f9;
    color: #0f172a;
}

.badge-premium-dot {
    position: absolute;
    top: 10px;
    right: 10px;
    width: 8px;
    height: 8px;
    background: #ef4444;
    border-radius: 50%;
    border: 2px solid white;
}

.user-profile-link {
    background: #f8fafc;
    border-radius: 14px;
    padding: 0.35rem 0.75rem !important;
    transition: all 0.2s ease;
    border: 1px solid #e2e8f0;
}

.user-profile-link:hover, .user-profile-link.active {
    background: #ffffff;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    border-color: #cbd5e1;
}

.rounded-12 {
    border-radius: 12px;
}

.dropdown-menu-premium {
    border: none !important;
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04) !important;
    border-radius: 16px !important;
    padding: 0.75rem !important;
    min-width: 240px !important;
    margin-top: 0.5rem !important;
}

.dropdown-item {
    padding: 0.65rem 1rem !important;
    border-radius: 10px !important;
    margin-bottom: 0.2rem;
}

.icon-box {
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 8px;
    margin-right: 1rem;
}

.icon-box .icon {
    width: 18px;
    height: 18px;
}
</style>
<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <title>{{ config('app.name', 'Laravel') }}</title>
    <!-- Tabler Core -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    @stack('styles')
</head>

<body>
    <div class="page">
        <!-- Sidebar -->
        @include('layouts.partials.sidebar')

        <!-- Navbar -->
        @include('layouts.partials.header')

        <div class="page-wrapper">
            <!-- Page header -->
            @if (isset($header))
                <div class="page-header d-print-none">
                    <div class="container-xl">
                        <div class="row g-2 align-items-center">
                            <div class="col">
                                {{ $header }}
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Page body -->
            <div class="page-body">
                <div class="container-xl">
                    <!-- Flash Messages -->
                    @if (session('success'))
                        <x-alert type="success" dismissible>
                            {{ session('success') }}
                        </x-alert>
                    @endif
                    @if (session('error'))
                        <x-alert type="danger" dismissible>
                            {{ session('error') }}
                        </x-alert>
                    @endif
                    @if (session('warning'))
                        <x-alert type="warning" dismissible>
                            {{ session('warning') }}
                        </x-alert>
                    @endif
                    @if (session('info'))
                        <x-alert type="info" dismissible>
                            {{ session('info') }}
                        </x-alert>
                    @endif

                    {{ $slot }}
                </div>
            </div>

            @include('layouts.partials.footer')
        </div>
    </div>
    @stack('scripts')

    <!-- Mobile Backdrop -->
    <div class="mobile-backdrop" id="mobile-menu-backdrop"></div>

    <!-- Mobile Menu Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const trigger = document.getElementById('mobile-menu-trigger');
            const closeBtn = document.getElementById('mobile-menu-close');
            const backdrop = document.getElementById('mobile-menu-backdrop');
            const sidebar = document.getElementById('sidebar-menu');

            // Global State helpers
            window.closeMobileMenu = function () {
                if (sidebar) sidebar.classList.remove('show');
                if (backdrop) backdrop.classList.remove('show');
                document.body.style.overflow = '';
            };

            window.openMobileMenu = function () {
                if (sidebar) sidebar.classList.add('show');
                if (backdrop) backdrop.classList.add('show');
                document.body.style.overflow = 'hidden';
            };

            if (trigger) {
                trigger.onclick = function (e) {
                    e.preventDefault();
                    e.stopPropagation();
                    console.log('Mobile menu trigger clicked');
                    window.openMobileMenu();
                };
            }

            if (closeBtn) {
                closeBtn.onclick = function (e) {
                    e.preventDefault();
                    console.log('Mobile menu close clicked');
                    window.closeMobileMenu();
                };
            }

            if (backdrop) {
                backdrop.onclick = function () {
                    console.log('Mobile menu backdrop clicked');
                    window.closeMobileMenu();
                };
            }

            // Link handling
            if (sidebar) {
                sidebar.addEventListener('click', function (e) {
                    // Check if clicked element is a link
                    const link = e.target.closest('a.nav-link');
                    if (link && !link.getAttribute('data-bs-toggle')) {
                        // Close menu for normal links
                        window.closeMobileMenu();
                    }
                });
            }
        });
    </script>
</body>

</html>
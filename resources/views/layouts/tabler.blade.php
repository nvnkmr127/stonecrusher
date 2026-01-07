<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <title>{{ config('app.name', 'Laravel') }}</title>
    <!-- Tabler Core -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
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
</body>

</html>
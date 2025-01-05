<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    @livewireStyles
    @vite(['resources/css/app.css'])
</head>

<body>
    <div class="antialiased bg-gray-50 dark:bg-gray-900">
        @livewire('components.header')
        <!-- ===== Sidebar Start ===== -->
        @livewire('components.sidebar')
        <main id="main-content" class="p-4 md:ml-64 h-auto pt-20">
           {{ $slot}}
        </main>

        @livewireScripts
        @stack('modals')
        @stack('scripts')
        @livewire('components.flash-message')
        @vite(['resources/js/index.js', 'resources/js/app.js'])
        @yield('scripts')
    </div>
</body>

</html>

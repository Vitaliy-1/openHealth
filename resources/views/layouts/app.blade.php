<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    @livewireStyles
    @vite(['resources/css/app.css'])

    <script>
        // Flowbite's recommendation: On page load or when changing themes, best to add inline in `head` to avoid FOUC
        if (localStorage.getItem('color-theme') === 'dark' || (!('color-theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark')
        }
    </script>
</head>

<body>
    <div class="antialiased bg-white dark:bg-gray-800">
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

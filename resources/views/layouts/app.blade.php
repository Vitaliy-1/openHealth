<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet"/>

    @livewireStyles
    @livewireScripts
    <!-- Scripts -->
    @vite([ 'resources/js/index.js','resources/css/app.css', 'resources/js/app.js'])
{{--            @vite([ 'resources/css/style.css'])--}}
</head>


<body class="bg-gray-50 dark:bg-gray-800">
@livewire('components.header')
<div class="flex pt-16 overflow-hidden bg-gray-50 dark:bg-gray-900">
    <!-- ===== Sidebar Start ===== -->
    @livewire('components.sidebar')
    <div id="main-content" class="relative w-full h-full overflow-y-auto bg-gray-50 lg:ml-64 dark:bg-gray-900">
        <main>

                            {{ $slot }}
        </main>
    </div>
</div>

@stack('modals')

@stack('scripts')
@livewire('components.flash-message')
@yield('scripts')
</body>


</html>

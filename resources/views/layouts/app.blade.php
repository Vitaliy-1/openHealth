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
    {{--        @vite([ 'resources/css/style.css'])--}}
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
<div id="preloader" style="display: none; position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background: rgba(255,255,255,0.8); z-index: 9999;">
    <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);">
        <div class="spinner"></div> <!-- Можете додати тут свій анімований прелоадер -->
        Завантаження...
    </div>
</div>

<style>
    .spinner {
        width: 50px;
        height: 50px;
        border: 5px solid rgba(0,0,0,.1);
        border-top-color: #007bff;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        to {
            transform: rotate(360deg);
        }
    }
</style>

<script>
    document.addEventListener('livewire:load', function () {
        Livewire.hook('message.sent', () => {
            document.getElementById('preloader').style.display = 'block';
        });

        Livewire.hook('message.processed', () => {
            document.getElementById('preloader').style.display = 'none';
        });
    });
</script>

@yield('scripts')
</body>


</html>

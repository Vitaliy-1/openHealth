<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        <!-- Scripts -->
        @livewireScripts

        @vite([
            'resources/js/index.js',
            'resources/js/app.js',
            ])

        <!-- Styles -->
        @livewireStyles

        @vite([
            'resources/css/app.css',
//            'resources/css/style.css',
            ])
    </head>
    <body>

    <main class="bg-gray-50 dark:bg-gray-900">
        <div class="flex flex-col items-center justify-center px-6 pt-8 mx-auto md:h-screen pt:mt-0 dark:bg-gray-900">
            {{ $slot}}
        </div>

    </main>


    @stack('modals')


    </body>
</html>

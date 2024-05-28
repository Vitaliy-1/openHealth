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



        <!-- Styles -->
        @livewireStyles

        @vite([ 'resources/js/index.js','resources/css/app.css', 'resources/js/app.js'])
        @vite([ 'resources/css/style.css'])

    </head>
    <body>
    <div class="mx-auto max-w-screen-2xl p-4 md:p-6 2xl:p-10 antialiased">
        <div class="rounded-sm border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark">
            <div class="flex flex-wrap items-center">
                {{ $slot }}
            </div>
        </div>
        <!-- ====== Forms Section Start -->

        <!-- ====== Forms Section End -->
    </div>


    @stack('modals')


    </body>
</html>

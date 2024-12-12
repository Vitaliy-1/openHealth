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
            <div class="flex flex-col justify-center items-center px-6 mx-auto h-screen xl:px-0 dark:bg-gray-900">
                <div class="block md:max-w-lg">
                    <img src="https://flowbite-admin-dashboard.vercel.app/images/illustrations/404.svg" alt="astronaut image">
                </div>
                <div class="text-center xl:max-w-4xl">
                    <h1 class="mb-3 text-2xl font-bold leading-tight text-gray-900 sm:text-4xl lg:text-5xl dark:text-white">{{__('errors.pageNotFound') }}</h1>
                    <p class="mb-5 text-base font-normal text-gray-500 md:text-lg dark:text-gray-400">  {{__('errors.oops') }}</p>
                    <a href="{{ route('dashboard') }}" class="text-white  inline-flex items-center mr-3 default-button">
                        <svg class="mr-2 -ml-1 w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                       {{__('errors.goBack') }}
                    </a>
                </div>
            </div>        </main>
    </div>
</div>

@stack('modals')

@stack('scripts')
@livewire('components.flash-message')
@yield('scripts')
</body>


</html>

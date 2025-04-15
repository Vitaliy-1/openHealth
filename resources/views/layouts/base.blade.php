<!-- resources/views/layouts/base.blade.php -->
<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <title>@yield('title', trans('oh.title'))</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta name="color-scheme" content="light only">
    <meta name="description" content="@yield('description', trans('oh.description'))">
    <meta name="robots" content="index, follow">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="canonical" href="{{ url()->current() }}">
    <meta name="theme-color" content="#f4881b">
    <meta property="og:title" content="@yield('title', trans('oh.title'))">
    <meta property="og:description" content="@yield('description', trans('oh.description'))">
    {{-- <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet"> --}}
    <meta property="og:image" content="{{ Vite::asset('resources/images/photo.jpg') }}">
    <meta property="og:image:secure_url" content="{{ Vite::asset('resources/images/photo.jpg') }}">
    <link rel="shortcut icon" type="image/png" href="{{ Vite::asset('resources/images/logo-16x16.png') }}" sizes="16x16">
    <link rel="shortcut icon" type="image/png" href="{{ Vite::asset('resources/images/logo-32x32.png') }}" sizes="32x32">
    <link rel="shortcut icon" type="image/png" href="{{ Vite::asset('resources/images/logo-96x96.png') }}" sizes="96x96">
    <link rel="apple-touch-icon" sizes="120x120" href="{{ Vite::asset('resources/images/logo-120x120.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ Vite::asset('resources/images/logo-180x180.png') }}">
    <link rel="apple-touch-icon" sizes="152x152" href="{{ Vite::asset('resources/images/logo-152x152.png') }}">
    <link rel="apple-touch-icon" sizes="167x167" href="{{ Vite::asset('resources/images/logo-167x167.png') }}">
    @vite('resources/css/style.css')
{{--    @stack('styles')--}}
</head>
<body id="body">
<header id="header" class="logo bg-gray-800 py-1 px-3.5">
    <div class="lg:container mx-auto sm:w-full flex justify-between items-center">
        <!-- Left-aligned logo -->
        <div class="flex items-center">
            <a href="/" class="text-black text-lg font-bold">
                <img src="{{ Vite::asset('resources/images/logo.webp') }}" alt="{{ trans('oh.title') }}" width="400" height="150">
            </a>
        </div>

        <!-- Center-aligned menu (hidden on small screens) -->
        <nav class="hidden lg:block text-black text-lg font-semibold">
            <ul class="flex">
                <li><a href="#services" class="text-link p-4 hover:text-orange hover:underline">{{ trans('Переваги') }}</a></li>
                <!--<li><a href="#team" class="text-link p-4 hover:text-orange hover:underline">{{ trans('Команда') }}</a></li>-->
                <li><a href="#offers" class="text-link p-4 hover:text-orange hover:underline">{{ trans('Індивідуальна розробка') }}</a></li>
                <li><a href="#consultation-form" class="text-link p-4 hover:text-orange hover:underline">{{ trans('Контакти') }}</a></li>
            </ul>
        </nav>

        <!-- Right-aligned menu toggle button (visible on small screens) -->
        <button id="menuToggle" class="menu lg:hidden md:block text-black focus:outline-none p-2 w-10 h-10" aria-label="{{ trans('menu')}}">
            <!--<i class="fas fa-bars"></i>
            <i class="fas fa-times hidden"></i>-->

            <svg id="openIcon" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path>
            </svg>
            <svg id="closeIcon" class="hidden w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>

        <!-- Right-aligned social icons -->
        <div class="hidden lg:flex items-center">
            <a href="https://www.facebook.com/openhealthmis" class="text-black mr-4 hover:text-gray-300" aria-label="facebook">
                <svg class="w-10 h-10 icon fill-icon hover:fill-black" viewBox="0 0 32 32" style="border-radius:4px"><path class="icon" d="M5,0 L27,0 C29.7614237,-5.07265313e-16 32,2.23857625 32,5 L32,27 C32,29.7614237 29.7614237,32 27,32 L5,32 C2.23857625,32 3.38176876e-16,29.7614237 0,27 L0,5 C-3.38176876e-16,2.23857625 2.23857625,5.07265313e-16 5,0 Z M13.6383065,25 L16.9133212,25 L16.9133212,16.0044 L19.3701815,16.0044 L19.8560792,13.1936 L16.9133212,13.1936 L16.9133212,11.1568001 C16.9133212,10.5002 17.3378823,9.81079996 17.944655,9.81079996 L19.6171262,9.81079996 L19.6171262,7 L17.567445,7 L17.567445,7.0126 C14.3603601,7.129 13.7014413,8.98640001 13.6443004,10.9374 L13.6383065,10.9374 L13.6383065,13.1936 L12,13.1936 L12,16.0044 L13.6383065,16.0044 L13.6383065,25 Z" fill-rule="evenodd"></path><path d="M5,1 C2.790861,1 1,2.790861 1,5 L1,27 C1,29.209139 2.790861,31 5,31 L27,31 C29.209139,31 31,29.209139 31,27 L31,5 C31,2.790861 29.209139,1 27,1 L5,1 Z M5,0 L27,0 C29.7614237,-5.07265313e-16 32,2.23857625 32,5 L32,27 C32,29.7614237 29.7614237,32 27,32 L5,32 C2.23857625,32 3.38176876e-16,29.7614237 0,27 L0,5 C-3.38176876e-16,2.23857625 2.23857625,5.07265313e-16 5,0 Z" class="icon"></path><path class="icon" d="M13.6383 25H16.9133V16.0044H19.3702L19.8561 13.1936H16.9133V11.1568C16.9133 10.5002 17.3379 9.8108 17.9447 9.8108H19.6171V7H17.5674V7.0126C14.3604 7.129 13.7014 8.9864 13.6443 10.9374H13.6383V13.1936H12V16.0044H13.6383V25Z" style="color:#fff;fill:#fff"></path></svg>
            </a>
            <a href="https://github.com/Vitaliy-1/openHealth" class="text-black mr-4 hover:text-gray-300" aria-label="github">
                <svg class="w-10 h-10" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                    <path fill-rule="evenodd" d="M12.006 2a9.847 9.847 0 0 0-6.484 2.44 10.32 10.32 0 0 0-3.393 6.17 10.48 10.48 0 0 0 1.317 6.955 10.045 10.045 0 0 0 5.4 4.418c.504.095.683-.223.683-.494 0-.245-.01-1.052-.014-1.908-2.78.62-3.366-1.21-3.366-1.21a2.711 2.711 0 0 0-1.11-1.5c-.907-.637.07-.621.07-.621.317.044.62.163.885.346.266.183.487.426.647.71.135.253.318.476.538.655a2.079 2.079 0 0 0 2.37.196c.045-.52.27-1.006.635-1.37-2.219-.259-4.554-1.138-4.554-5.07a4.022 4.022 0 0 1 1.031-2.75 3.77 3.77 0 0 1 .096-2.713s.839-.275 2.749 1.05a9.26 9.26 0 0 1 5.004 0c1.906-1.325 2.74-1.05 2.74-1.05.37.858.406 1.828.101 2.713a4.017 4.017 0 0 1 1.029 2.75c0 3.939-2.339 4.805-4.564 5.058a2.471 2.471 0 0 1 .679 1.897c0 1.372-.012 2.477-.012 2.814 0 .272.18.592.687.492a10.05 10.05 0 0 0 5.388-4.421 10.473 10.473 0 0 0 1.313-6.948 10.32 10.32 0 0 0-3.39-6.165A9.847 9.847 0 0 0 12.007 2Z" clip-rule="evenodd"/>
                </svg>
            </a>
        </div>
    </div>

    <!-- Responsive menu (visible on small screens) -->
    <div id="responsiveMenu" class="hidden lg:hidden bg-gray-800">
        <ul class="text-black text-lg font-semibold">
            <li class="py-2 px-4"><a href="#services" class="text-center text-link block hover:text-orange">{{ trans('Переваги') }}</a></li>
            <!--<li class="py-2 px-4"><a href="#team" class="text-center text-link block hover:text-orange">{{ trans('Команда') }}</a></li>-->
            <li class="py-2 px-4"><a href="#offers" class="text-center text-link block hover:text-orange">{{ trans('Індивідуальна розробка') }}</a></li>
            <li class="py-2 px-4"><a href="#consultation-form" class="text-center text-link block hover:text-orange">{{ trans('Контакти') }}</a></li>
        </ul>

        <!-- Right-aligned social icons -->
        <div class="flex justify-center text-center items-center mt-8">
            <a href="https://facebook.com" class="flex justify-center text-black mr-4 hover:text-gray-300">
                <svg class="w-10 h-10 icon fill-icon hover:fill-black" viewBox="0 0 32 32" style="border-radius:4px"><path class="icon" d="M5,0 L27,0 C29.7614237,-5.07265313e-16 32,2.23857625 32,5 L32,27 C32,29.7614237 29.7614237,32 27,32 L5,32 C2.23857625,32 3.38176876e-16,29.7614237 0,27 L0,5 C-3.38176876e-16,2.23857625 2.23857625,5.07265313e-16 5,0 Z M13.6383065,25 L16.9133212,25 L16.9133212,16.0044 L19.3701815,16.0044 L19.8560792,13.1936 L16.9133212,13.1936 L16.9133212,11.1568001 C16.9133212,10.5002 17.3378823,9.81079996 17.944655,9.81079996 L19.6171262,9.81079996 L19.6171262,7 L17.567445,7 L17.567445,7.0126 C14.3603601,7.129 13.7014413,8.98640001 13.6443004,10.9374 L13.6383065,10.9374 L13.6383065,13.1936 L12,13.1936 L12,16.0044 L13.6383065,16.0044 L13.6383065,25 Z" fill-rule="evenodd"></path><path d="M5,1 C2.790861,1 1,2.790861 1,5 L1,27 C1,29.209139 2.790861,31 5,31 L27,31 C29.209139,31 31,29.209139 31,27 L31,5 C31,2.790861 29.209139,1 27,1 L5,1 Z M5,0 L27,0 C29.7614237,-5.07265313e-16 32,2.23857625 32,5 L32,27 C32,29.7614237 29.7614237,32 27,32 L5,32 C2.23857625,32 3.38176876e-16,29.7614237 0,27 L0,5 C-3.38176876e-16,2.23857625 2.23857625,5.07265313e-16 5,0 Z" class="icon"></path><path class="icon" d="M13.6383 25H16.9133V16.0044H19.3702L19.8561 13.1936H16.9133V11.1568C16.9133 10.5002 17.3379 9.8108 17.9447 9.8108H19.6171V7H17.5674V7.0126C14.3604 7.129 13.7014 8.9864 13.6443 10.9374H13.6383V13.1936H12V16.0044H13.6383V25Z" style="color:#fff;fill:#fff"></path></svg>
            </a>
            <a href="https://github.com/Vitaliy-1/openHealth" class="text-black mr-4 hover:text-gray-300" aria-label="github">
                <svg class="w-10 h-10" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                    <path fill-rule="evenodd" d="M12.006 2a9.847 9.847 0 0 0-6.484 2.44 10.32 10.32 0 0 0-3.393 6.17 10.48 10.48 0 0 0 1.317 6.955 10.045 10.045 0 0 0 5.4 4.418c.504.095.683-.223.683-.494 0-.245-.01-1.052-.014-1.908-2.78.62-3.366-1.21-3.366-1.21a2.711 2.711 0 0 0-1.11-1.5c-.907-.637.07-.621.07-.621.317.044.62.163.885.346.266.183.487.426.647.71.135.253.318.476.538.655a2.079 2.079 0 0 0 2.37.196c.045-.52.27-1.006.635-1.37-2.219-.259-4.554-1.138-4.554-5.07a4.022 4.022 0 0 1 1.031-2.75 3.77 3.77 0 0 1 .096-2.713s.839-.275 2.749 1.05a9.26 9.26 0 0 1 5.004 0c1.906-1.325 2.74-1.05 2.74-1.05.37.858.406 1.828.101 2.713a4.017 4.017 0 0 1 1.029 2.75c0 3.939-2.339 4.805-4.564 5.058a2.471 2.471 0 0 1 .679 1.897c0 1.372-.012 2.477-.012 2.814 0 .272.18.592.687.492a10.05 10.05 0 0 0 5.388-4.421 10.473 10.473 0 0 0 1.313-6.948 10.32 10.32 0 0 0-3.39-6.165A9.847 9.847 0 0 0 12.007 2Z" clip-rule="evenodd"/>
                </svg>
            </a>
        </div>
    </div>
</header>

<main>
    @yield('content')
</main>

<footer class="bg-gray-3 pt-6 sm:pt-5 pb-6 sm:pb-3">
    <div class="container w-full lg:w-3/5 mx-auto md:text-left text-center text-black">
        <div class="grid grid-cols-1 sm:grid-cols-1 md:grid-cols-3 gap-3">
            <div class="p-4">
                <div class="wrapper-content">
                    <h3 class="md:text-3xl text-xl font-bold mb-2">
                        &copy; {{ date('Y') }}
                        {{ trans('Open Health') }}
                    </h3>
                    <p class="text-meta-10 font-bold">{{ trans('Медична інформаційна система') }}</p>
                </div>
            </div>
            <div class="p-4 flex justify-center">
                <div class="wrapper-content">
                    <h3 class="text-xl font-bold mb-2">{{ trans('Телефонуйте') }}</h3>
                    <p><a href="tel:{{ $phone }}" class="hover:text-orange hover:underline">{{ $phone }}</a></p>
                </div>
            </div>
            <div class="p-4 md:flex justify-end hidden">
                <div class="wrapper-content">
                    <h3 class="text-xl font-bold mb-2">{{ trans('Пишіть нам') }}</h3>
                    <p><a href="mailto:{{ $email }}" class="hover:text-orange hover:underline">{{ $email }}</a></p>
                </div>
            </div>
        </div>
        <ul class="flex justify-center mt-5">
            <li>
                <a href="https://www.facebook.com/openhealthmis" class="icon facebook" aria-label="facebook">
                    <svg class="w-10 h-10 icon hover:fill-orange" viewBox="0 0 32 32" style="border-radius:50%"><path class="outer_bDW" d="M32 0H0V32H32V0ZM16.9133 25H13.6383V16.0044H12V13.1936H13.6383V10.9374H13.6443C13.7014 8.9864 14.3604 7.129 17.5674 7.0126V7H19.6171V9.8108H17.9447C17.3379 9.8108 16.9133 10.5002 16.9133 11.1568V13.1936H19.8561L19.3702 16.0044H16.9133V25Z" fill-rule="evenodd"></path><path d="M16,31 C24.2842712,31 31,24.2842712 31,16 C31,7.71572875 24.2842712,1 16,1 C7.71572875,1 1,7.71572875 1,16 C1,24.2842712 7.71572875,31 16,31 Z M16,32 C7.163444,32 0,24.836556 0,16 C0,7.163444 7.163444,0 16,0 C24.836556,0 32,7.163444 32,16 C32,24.836556 24.836556,32 16,32 Z" class="border_2yy"></path><path class="icon" d="M13.6383 25H16.9133V16.0044H19.3702L19.8561 13.1936H16.9133V11.1568C16.9133 10.5002 17.3379 9.8108 17.9447 9.8108H19.6171V7H17.5674V7.0126C14.3604 7.129 13.7014 8.9864 13.6443 10.9374H13.6383V13.1936H12V16.0044H13.6383V25Z" style="color:#fff;fill:#fff"></path></svg>
                </a>
            </li>
        </ul>

        <div class="md:hidden sm:block grid grid-cols-1 sm:grid-cols-1 md:grid-cols-3 gap-3 mt-4">
            <div class="p-4">
                <h3 class="text-xl font-bold mb-2">{{ trans('Пишіть нам') }}</h3>
                <p><a href="mailto:{{ $email }}" class="hover:text-orange hover:underline">{{ $email }}</a></p>
            </div>
        </div>
    </div>
</footer>

@stack('modals')

@vite('resources/js/app.js')
@vite('resources/js/base.js')
@stack('scripts')
</body>
</html>

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @hasSection('title')
        <title>@yield('title') - {{ config('app.name') }}</title>
    @else
        <title>{{ config('app.name') }}</title>
    @endif

    <!-- Favicon -->
    <link rel="shortcut icon" href="{{ url(asset('favicon.ico')) }}">

    <!-- Fonts -->
    <link rel="stylesheet" href="https://rsms.me/inter/inter.css">

    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    {{-- @livewireStyles
        @livewireScripts --}}

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<body>
    @yield('body')


    <footer class="bg-[#4a74ac] mt-8 py-4">
        <div class="container mx-auto text-center">
            <div class="md:flex items-center">
                <div class="md:w-1/4">
                    <a href="https://bnuto.cultura.gov.it" class="navlink" target="_blank">
                        <img class="h-16 inline"
                            src="{{ Vite::asset('resources/images/biblioteca-nazionale-universita-di-torino.jpg') }}"
                            alt="Biblioteca nazionale Universitaria di Torino">
                    </a>
                </div>
                <div class="md:w-1/2 py-6">
                    <a href="#" class="navlink" target="_blank">
                        <img class="h-10 inline" src="{{ Vite::asset('resources/images/logo-GitHub-Mark-64px.png') }}"
                            alt="github">
                    </a>
                </div>
                <div class="md:w-1/4">
                    <a href="https://sib.swiss" class="navlink" target="_blank">
                        <img class="h-16 inline" src="{{ Vite::asset('resources/images/sib_logo2023.svg') }}"
                            alt="SIB">
                    </a>
                </div>
            </div>
        </div>
    </footer>
</body>

</html>

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

    @vite(['resources/sass/app.scss', 'resources/js/annotation.js'])
    {{-- @livewireStyles
        @livewireScripts --}}

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<body>

    @php
        $manuscript = \App\Models\Manuscript::first();    
    @endphp

    <div x-data="manuscriptAnnotation({
        manuscriptName: '{{ $manuscript->name }}',
        manifest: '{{ route('iiif.presentation.manifest', $manuscript->name) }}'
    })">
        <h2>TEST</h2>
        <div id="mirador"></div>
    </div>




</body>

</html>

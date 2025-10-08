<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'HikeThere') }}</title>

    <!-- Open Graph / Facebook / Messenger -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="{{ $metaTitle ?? config('app.name', 'HikeThere') }}">
    <meta property="og:description" content="{{ $metaDescription ?? 'Discover amazing hiking trails, join events, and connect with fellow hikers on HikeThere - Your Ultimate Hiking Companion.' }}">
    <meta property="og:image" content="{{ $metaImage ?? asset('img/main-logo.png') }}">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:site_name" content="{{ config('app.name', 'HikeThere') }}">

    <!-- Twitter -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:url" content="{{ url()->current() }}">
    <meta name="twitter:title" content="{{ $metaTitle ?? config('app.name', 'HikeThere') }}">
    <meta name="twitter:description" content="{{ $metaDescription ?? 'Discover amazing hiking trails, join events, and connect with fellow hikers on HikeThere - Your Ultimate Hiking Companion.' }}">
    <meta name="twitter:image" content="{{ $metaImage ?? asset('img/main-logo.png') }}">

    <!-- General Meta -->
    <meta name="description" content="{{ $metaDescription ?? 'Discover amazing hiking trails, join events, and connect with fellow hikers on HikeThere - Your Ultimate Hiking Companion.' }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <meta name="google-maps-api-key" content="{{ config('services.google.maps_api_key') }}" />
    <script src="https://code.iconify.design/3/3.1.0/iconify.min.js"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        [x-cloak] { display: none !important; }
    </style>

    <!-- Styles -->
    @livewireStyles
</head>

<body>
    <div class="font-sans text-gray-900 antialiased">
        {{ $slot }}
    </div>

    @livewireScripts
</body>

</html>
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'HikeThere') }}</title>

    @php
        // Use GCS URL if available, otherwise fallback to local asset
        $defaultOgImage = env('OG_IMAGE_URL') 
            ? env('OG_IMAGE_URL') 
            : (config('filesystems.default') === 'gcs' && env('GCS_BUCKET')
                ? 'https://storage.googleapis.com/' . env('GCS_BUCKET') . '/img/og-image.png'
                : asset('img/og-image.png'));
    @endphp

    <!-- Open Graph / Facebook / Messenger -->
    <meta property="og:type" content="{{ $metaType ?? 'website' }}">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="{{ $metaTitle ?? config('app.name', 'HikeThere') . ' - Your Ultimate Hiking Companion' }}">
    <meta property="og:description" content="{{ $metaDescription ?? 'Discover breathtaking hiking trails, join exciting outdoor events, connect with fellow adventurers, and ensure your safety with emergency readiness features. Start your hiking journey with HikeThere today!' }}">
    <meta property="og:image" content="{{ $metaImage ?? $defaultOgImage }}">
    <meta property="og:image:secure_url" content="{{ $metaImage ?? $defaultOgImage }}">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:image:alt" content="{{ $metaImageAlt ?? 'HikeThere - Discover hiking trails and join outdoor adventures' }}">
    <meta property="og:site_name" content="{{ config('app.name', 'HikeThere') }}">
    <meta property="og:locale" content="en_US">

    <!-- Twitter -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:site" content="@HikeThere">
    <meta name="twitter:creator" content="@HikeThere">
    <meta name="twitter:url" content="{{ url()->current() }}">
    <meta name="twitter:title" content="{{ $metaTitle ?? config('app.name', 'HikeThere') . ' - Your Ultimate Hiking Companion' }}">
    <meta name="twitter:description" content="{{ $metaDescription ?? 'Discover breathtaking hiking trails, join exciting outdoor events, connect with fellow adventurers, and ensure your safety with emergency readiness features.' }}">
    <meta property="og:image" content="{{ $metaImage ?? $defaultOgImage }}">
    <meta name="twitter:image:alt" content="{{ $metaImageAlt ?? 'HikeThere - Discover hiking trails and join outdoor adventures' }}">

    <!-- General Meta -->
    <meta name="description" content="{{ $metaDescription ?? 'Discover breathtaking hiking trails, join exciting outdoor events, connect with fellow adventurers, and ensure your safety with emergency readiness features. Start your hiking journey with HikeThere today!' }}">
    <meta name="keywords" content="{{ $metaKeywords ?? 'hiking, trails, outdoor events, hiking community, emergency readiness, trail maps, hiking safety, adventure, nature, outdoor activities' }}">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="{{ url()->current() }}">

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
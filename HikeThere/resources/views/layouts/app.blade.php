<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="app-version" content="1.0.1">

    <title>{{ config('app.name', 'HikeThere') }}</title>

    @php
        // Use GCS URL if available, otherwise fallback to local asset
        $defaultOgImage = env('OG_IMAGE_URL') 
            ? env('OG_IMAGE_URL') 
            : (config('filesystems.default') === 'gcs' && env('GCS_BUCKET')
                ? 'https://storage.googleapis.com/' . env('GCS_BUCKET') . '/assets/og-image.png'
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
    @if(isset($metaAuthor))
    <meta property="article:author" content="{{ $metaAuthor }}">
    @endif

    <!-- Twitter -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:site" content="@HikeThere">
    <meta name="twitter:creator" content="@HikeThere">
    <meta name="twitter:url" content="{{ url()->current() }}">
    <meta name="twitter:title" content="{{ $metaTitle ?? config('app.name', 'HikeThere') . ' - Your Ultimate Hiking Companion' }}">
    <meta name="twitter:description" content="{{ $metaDescription ?? 'Discover breathtaking hiking trails, join exciting outdoor events, connect with fellow adventurers, and ensure your safety with emergency readiness features.' }}">
    <meta name="twitter:image" content="{{ $metaImage ?? $defaultOgImage }}">
    <meta name="twitter:image:alt" content="{{ $metaImageAlt ?? 'HikeThere - Discover hiking trails and join outdoor adventures' }}">

    <!-- General Meta -->
    <meta name="description" content="{{ $metaDescription ?? 'Discover breathtaking hiking trails, join exciting outdoor events, connect with fellow adventurers, and ensure your safety with emergency readiness features. Start your hiking journey with HikeThere today!' }}">
    <meta name="keywords" content="{{ $metaKeywords ?? 'hiking, trails, outdoor events, hiking community, emergency readiness, trail maps, hiking safety, adventure, nature, outdoor activities' }}">
    <meta name="author" content="{{ $metaAuthor ?? 'HikeThere' }}">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="{{ url()->current() }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Font Awesome (for header search icons) -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">


    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <meta name="google-maps-api-key" content="{{ config('services.google.maps_api_key') }}" />



    <!-- Styles -->
    @livewireStyles
    @stack('styles')
</head>

<body class="font-sans antialiased">
    <x-banner />

    <div class="min-h-screen bg-gray-100">
        @livewire('navigation-menu')

        <!-- Floating Section Navigation -->
        @stack('floating-navigation')

        <!-- Floating Weather Card -->
        @stack('floating-weather')

        <!-- Page Heading -->
        @isset($header)
        <header class="bg-white shadow sticky top-16 z-40">
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                {{ $header }}
            </div>
        </header>
        @endisset

        <!-- Page Content -->
        <main>
            {{ $slot ?? '' }}
        </main>
    </div>

    @push('modals')
        @if(session('info'))
            <div x-data="{ show: true }" x-show="show" x-cloak class="fixed inset-0 z-50 flex items-start justify-center">
                <div class="fixed inset-0 bg-black opacity-50" x-on:click="show = false"></div>
                <div class="bg-white rounded-lg shadow-lg max-w-xl w-full mx-4 mt-24 p-6 z-50" role="dialog" aria-modal="true">
                    <h3 class="text-lg font-semibold mb-2">Action required</h3>
                    <p class="text-sm text-gray-700 mb-4">{{ session('info') }}</p>
                    <div class="flex justify-end">
                        <a href="{{ route('onboard.preferences') }}" class="px-4 py-2 bg-[#336d66] text-white rounded mr-2">Set Preferences</a>
                        <button @click="show = false" class="px-4 py-2 bg-gray-200 rounded">Close</button>
                    </div>
                </div>
            </div>
        @endif
    @endpush
    @stack('modals')
    @stack('scripts')

    @livewireScripts
</body>
<!-- Footer -->
<footer class="bg-white dark:bg-gray-900">
    <div class="mx-auto w-full max-w-screen-xl p-4 py-6 lg:py-8">
        <div class="md:flex md:justify-between">
            <div class="mb-6 md:mb-0">
                <a href="/" class="flex items-center space-x-3 mountain-logo group">
                    <div class="relative">
                        <img src="{{ asset('img/icon1.png') }}" alt="{{ config('app.name', 'HikeThere') }} logo" class="h-9 w-auto">
                    </div>
                    <span class="font-bold text-xl text-[#336d66]">{{ config('app.name', 'HikeThere') }}</span>
                </a>
            </div>

            <div class="grid grid-cols-2 gap-8 sm:gap-6 sm:grid-cols-3">
                <div>
                    <h2 class="mb-6 text-sm font-semibold text-gray-900 uppercase dark:text-white">Resources</h2>
                    <ul class="text-gray-500 dark:text-gray-400 font-medium">
                        <li class="mb-4">
                            <a href="/trails" class="hover:underline">Trails</a>
                        </li>
                        <li>
                            <a href="https://tailwindcss.com/" class="hover:underline">Tailwind CSS</a>
                        </li>
                    </ul>
                </div>
                <div>
                    <h2 class="mb-6 text-sm font-semibold text-gray-900 uppercase dark:text-white">Follow us</h2>
                    <ul class="text-gray-500 dark:text-gray-400 font-medium">
                        <li class="mb-4">
                            <a href="https://github.com/themesberg/flowbite" class="hover:underline ">Github</a>
                        </li>
                        <li>
                            <a href="https://discord.gg/4eeurUVvTy" class="hover:underline">Discord</a>
                        </li>
                    </ul>
                </div>
                <div>
                    <h2 class="mb-6 text-sm font-semibold text-gray-900 uppercase dark:text-white">Legal</h2>
                    <ul class="text-gray-500 dark:text-gray-400 font-medium">
                        <li class="mb-4">
                            <a href="{{ route('privacy') }}" class="hover:underline">Privacy Policy</a>
                        </li>
                        <li>
                            <a href="{{ route('terms') }}" class="hover:underline">Terms &amp; Conditions</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <hr class="my-6 border-gray-200 sm:mx-auto dark:border-gray-700 lg:my-8" />
        <div class="sm:flex sm:items-center sm:justify-between">
            <span class="text-sm text-gray-500 sm:text-center dark:text-gray-400">© {{ date('Y') }} <a href="/" class="hover:underline">{{ config('app.name', 'HikeThere') }}</a>. All Rights Reserved.
            </span>
            <div class="flex mt-4 sm:justify-center sm:mt-0">
                <a href="#" class="text-gray-500 hover:text-gray-900 dark:hover:text-white">
                    <svg class="w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 8 19">
                        <path fill-rule="evenodd" d="M6.135 3H8V0H6.135a4.147 4.147 0 0 0-4.142 4.142V6H0v3h2v9.938h3V9h2.021l.592-3H5V3.591A.6.6 0 0 1 5.592 3h.543Z" clip-rule="evenodd" />
                    </svg>
                    <span class="sr-only">Facebook page</span>
                </a>
                <a href="#" class="text-gray-500 hover:text-gray-900 dark:hover:text-white ms-5">
                    <svg class="w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 21 16">
                        <path d="M16.942 1.556a16.3 16.3 0 0 0-4.126-1.3 12.04 12.04 0 0 0-.529 1.1 15.175 15.175 0 0 0-4.573 0 11.585 11.585 0 0 0-.535-1.1 16.274 16.274 0 0 0-4.129 1.3A17.392 17.392 0 0 0 .182 13.218a15.785 15.785 0 0 0 4.963 2.521c.41-.564.773-1.16 1.084-1.785a10.63 10.63 0 0 1-1.706-.83c.143-.106.283-.217.418-.33a11.664 11.664 0 0 0 10.118 0c.137.113.277.224.418.33-.544.328-1.116.606-1.71.832a12.52 12.52 0 0 0 1.084 1.785 16.46 16.46 0 0 0 5.064-2.595 17.286 17.286 0 0 0-2.973-11.59ZM6.678 10.813a1.941 1.941 0 0 1-1.8-2.045 1.93 1.93 0 0 1 1.8-2.047 1.919 1.919 0 0 1 1.8 2.047 1.93 1.93 0 0 1-1.8 2.045Zm6.644 0a1.94 1.94 0 0 1-1.8-2.045 1.93 1.93 0 0 1 1.8-2.047 1.918 1.918 0 0 1 1.8 2.047 1.93 1.93 0 0 1-1.8 2.045Z" />
                    </svg>
                    <span class="sr-only">Discord community</span>
                </a>
                <a href="#" class="text-gray-500 hover:text-gray-900 dark:hover:text-white ms-5">
                    <svg class="w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 17">
                        <path fill-rule="evenodd" d="M20 1.892a8.178 8.178 0 0 1-2.355.635 4.074 4.074 0 0 0 1.8-2.235 8.344 8.344 0 0 1-2.605.98A4.13 4.13 0 0 0 13.85 0a4.068 4.068 0 0 0-4.1 4.038 4 4 0 0 0 .105.919A11.705 11.705 0 0 1 1.4.734a4.006 4.006 0 0 0 1.268 5.392 4.165 4.165 0 0 1-1.859-.5v.05A4.057 4.057 0 0 0 4.1 9.635a4.19 4.19 0 0 1-1.856.07 4.108 4.108 0 0 0 3.831 2.807A8.36 8.36 0 0 1 0 14.184 11.732 11.732 0 0 0 6.291 16 11.502 11.502 0 0 0 17.964 4.5c0-.177 0-.35-.012-.523A8.143 8.143 0 0 0 20 1.892Z" clip-rule="evenodd" />
                    </svg>
                    <span class="sr-only">Twitter page</span>
                </a>
                <a href="#" class="text-gray-500 hover:text-gray-900 dark:hover:text-white ms-5">
                    <svg class="w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 .333A9.911 9.911 0 0 0 6.866 19.65c.5.092.678-.215.678-.477 0-.237-.01-1.017-.014-1.845-2.757.6-3.338-1.169-3.338-1.169a2.627 2.627 0 0 0-1.1-1.451c-.9-.615.07-.6.07-.6a2.084 2.084 0 0 1 1.518 1.021 2.11 2.11 0 0 0 2.884.823c.044-.503.268-.973.63-1.325-2.2-.25-4.516-1.1-4.516-4.9A3.832 3.832 0 0 1 4.7 7.068a3.56 3.56 0 0 1 .095-2.623s.832-.266 2.726 1.016a9.409 9.409 0 0 1 4.962 0c1.89-1.282 2.717-1.016 2.717-1.016.366.83.402 1.768.1 2.623a3.827 3.827 0 0 1 1.02 2.659c0 3.807-2.319 4.644-4.525 4.889a2.366 2.366 0 0 1 .673 1.834c0 1.326-.012 2.394-.012 2.72 0 .263.18.572.681.475A9.911 9.911 0 0 0 10 .333Z" clip-rule="evenodd" />
                </a>
            </div>
        </div>
    </div>
</footer>

</html>
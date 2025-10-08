<div class="sticky top-0 z-50">
<nav x-data="{ open: false }" class="bg-white border-b border-gray-100 shadow-sm">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    @auth
                    @if(Auth::user()->user_type === 'organization')
                        <img src="{{ asset('img/icon1.png') }}" alt="Icon" class="h-9 w-auto">
                    </a>
                    @else
                    <a href="{{ route('dashboard') }}">
                        <img src="{{ asset('img/icon1.png') }}" alt="Icon" class="h-9 w-auto">
                    </a>
                    @endif
                    @else
                    <a href="{{ route('dashboard') }}">
                        <img src="{{ asset('img/icon1.png') }}" alt="Icon" class="h-9 w-auto">
                    </a>
                    @endauth
                </div>

                <!-- Navigation Links -->
                @auth
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    @if(Auth::user()->user_type === 'organization')
                    <x-nav-link href="{{ route('org.dashboard') }}" :active="request()->routeIs('org.dashboard')">
                        {{ __('Dashboard') }}
                    </x-nav-link>
                    
                    <!-- Trails Dropdown -->
                    <div class="hidden sm:flex sm:items-center sm:-my-px" x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false">
                        <div class="relative">
                            <a href="{{ route('org.trails.index') }}" class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('org.trails.*') || request()->routeIs('organization.emergency-readiness.*') || request()->routeIs('organization.safety-incidents.*') ? 'border-green-500 text-gray-900 focus:border-green-700' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:text-gray-700 focus:border-gray-300' }} text-sm font-medium leading-5 focus:outline-none transition duration-150 ease-in-out">
                                <div>{{ __('Trails') }}</div>
                                <div class="ms-1">
                                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </a>

                            <div x-show="open"
                                    x-transition:enter="transition ease-out duration-200"
                                    x-transition:enter-start="transform opacity-0 scale-95"
                                    x-transition:enter-end="transform opacity-100 scale-100"
                                    x-transition:leave="transition ease-in duration-75"
                                    x-transition:leave-start="transform opacity-100 scale-100"
                                    x-transition:leave-end="transform opacity-0 scale-95"
                                    class="absolute z-50 mt-2 w-60 rounded-md shadow-lg origin-top-left left-0"
                                    style="display: none;">
                                <div class="rounded-md ring-1 ring-black ring-opacity-5 py-1 bg-white">
                                    <a href="{{ route('org.trails.create') }}" class="block px-4 py-2 text-sm leading-5 text-gray-700 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 transition duration-150 ease-in-out">
                                        <div class="flex items-center">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                            </svg>
                                            {{ __('Add Trail') }}
                                        </div>
                                    </a>
                                    <a href="{{ route('org.trails.index') }}" class="block px-4 py-2 text-sm leading-5 text-gray-700 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 transition duration-150 ease-in-out">
                                        <div class="flex items-center">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                            </svg>
                                            {{ __('Manage Trails') }}
                                        </div>
                                    </a>
                                    <div class="border-t border-gray-200"></div>
                                    <a href="{{ route('organization.emergency-readiness.index') }}" class="block px-4 py-2 text-sm leading-5 text-gray-700 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 transition duration-150 ease-in-out">
                                        <div class="flex items-center">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            {{ __('Emergency Assessments') }}
                                        </div>
                                    </a>
                                    <a href="{{ route('organization.safety-incidents.index') }}" class="block px-4 py-2 text-sm leading-5 text-gray-700 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 transition duration-150 ease-in-out">
                                        <div class="flex items-center">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                            </svg>
                                            {{ __('Safety Incidents') }}
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <x-nav-link href="{{ route('org.events.index') }}" :active="request()->routeIs('org.events.*')">
                        {{ __('Events') }}
                    </x-nav-link>
                    <x-nav-link href="{{ route('org.bookings.index') }}" :active="request()->routeIs('org.bookings.*')">
                        {{ __('Bookings') }}
                    </x-nav-link>
                    <x-nav-link href="{{ route('community.index') }}" :active="request()->routeIs('community.index')">
                        {{ __('Posts') }}
                    </x-nav-link>
                    <x-nav-link href="{{ route('reports.index') }}" :active="request()->routeIs('reports.*')">
                        {{ __('Reports') }}
                    </x-nav-link>
                    @else
                    <x-nav-link href="{{ route('dashboard') }}" :active="request()->routeIs('dashboard')">
                        {{ __('Dashboard') }}
                    </x-nav-link>
                    <x-nav-link href="{{ route('explore') }}" :active="request()->routeIs('explore')">
                        {{ __('Explore') }}
                    </x-nav-link>
                    
                    <!-- Community Dropdown -->
                    <div class="hidden sm:flex sm:items-center sm:-my-px" x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false">
                        <div class="relative">
                            <a href="{{ route('community.index') }}" class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('community.*') || request()->routeIs('hiker.incidents.*') ? 'border-green-500 text-gray-900 focus:border-green-700' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:text-gray-700 focus:border-gray-300' }} text-sm font-medium leading-5 focus:outline-none transition duration-150 ease-in-out">
                                <div>{{ __('Community') }}</div>
                                <div class="ms-1">
                                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </a>

                            <div x-show="open"
                                    x-transition:enter="transition ease-out duration-200"
                                    x-transition:enter-start="transform opacity-0 scale-95"
                                    x-transition:enter-end="transform opacity-100 scale-100"
                                    x-transition:leave="transition ease-in duration-75"
                                    x-transition:leave-start="transform opacity-100 scale-100"
                                    x-transition:leave-end="transform opacity-0 scale-95"
                                    class="absolute z-50 mt-2 w-60 rounded-md shadow-lg origin-top-left left-0"
                                    style="display: none;">
                                <div class="rounded-md ring-1 ring-black ring-opacity-5 py-1 bg-white">
                                    <a href="{{ route('community.index') }}" class="block px-4 py-2 text-sm leading-5 text-gray-700 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 transition duration-150 ease-in-out">
                                        <div class="flex items-center">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                            </svg>
                                            {{ __('Discover Organizations') }}
                                        </div>
                                    </a>
                                    <a href="{{ route('community.index', ['tab' => 'events']) }}" class="block px-4 py-2 text-sm leading-5 text-gray-700 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 transition duration-150 ease-in-out">
                                        <div class="flex items-center">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                            {{ __('Events') }}
                                        </div>
                                    </a>
                                    <div class="border-t border-gray-200"></div>
                                    <a href="{{ route('hiker.incidents.index') }}" class="block px-4 py-2 text-sm leading-5 text-gray-700 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 transition duration-150 ease-in-out">
                                        <div class="flex items-center">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                            </svg>
                                            {{ __('My Safety Reports') }}
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <x-nav-link href="{{ route('map.index') }}" :active="request()->routeIs('map.*')">
                        {{ __('Map') }}
                    </x-nav-link>
                    <x-nav-link href="{{ route('hiking-tools') }}" :active="request()->routeIs('hiking-tools')">
                        <span class="animate-charcter special-nav-highlight">
                            {{ __('Hiking Tools') }}
                        </span>
                    </x-nav-link>
                    @endif
                </div>
                @endauth
            </div>

            @auth
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <!-- Notification Dropdown -->
                <x-notification-dropdown :userType="Auth::user()->user_type ?? 'hiker'" />

                <!-- Settings Dropdown -->
                <div class="ms-3 relative">
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="flex text-sm border-2 border-transparent rounded-full focus:outline-none focus:border-gray-300 transition sticky-header">
                                @if(Auth::user()->profile_picture)
                                    <img class="size-8 rounded-full object-cover js-profile-avatar" src="{{ Auth::user()->profile_picture_url }}" alt="{{ Auth::user()->display_name }}" />
                                @else
                                    <div role="img" aria-label="{{ Auth::user()->display_name }}'s avatar" class="size-8 w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center text-sm font-semibold text-gray-700 js-profile-avatar-placeholder">
                                        {{ strtoupper(substr(Auth::user()->display_name ?? 'U', 0, 1)) }}
                                    </div>
                                @endif
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <!-- Account Management -->
                            <div class="block px-4 py-2 text-xs text-gray-400">
                                {{ __('Manage Account') }}
                            </div>

                            @if(Auth::user()->user_type === 'organization')
                            <x-dropdown-link href="{{ route('org.profile.show') }}">
                                {{ __('Profile') }}
                            </x-dropdown-link>
                            <x-dropdown-link href="{{ route('org.profile.edit') }}">
                                {{ __('Edit Profile') }}
                            </x-dropdown-link>
                            <x-dropdown-link href="{{ route('org.account.settings') }}">
                                {{ __('Account Settings') }}
                            </x-dropdown-link>
                            @else
                            <x-dropdown-link href="{{ route('custom.profile.show') }}">
                                {{ __('Profile') }}
                            </x-dropdown-link>
                            <x-dropdown-link href="{{ route('profile.edit') }}">
                                {{ __('Edit Profile') }}
                            </x-dropdown-link>
                            <x-dropdown-link href="{{ route('account.settings') }}">
                                {{ __('Account Settings') }}
                            </x-dropdown-link>
                            @endif
                            @if(Auth::user()->user_type === 'organization')
                            <x-dropdown-link href="{{ route('org.payment.index') }}">
                                {{ __('Payment Setup') }}
                            </x-dropdown-link>
                            @endif

                            <div class="border-t border-gray-200"></div>

                            <!-- About Section -->
                            @if(Auth::user()->user_type === 'organization')
                            <x-dropdown-link href="{{ route('org.about') }}">
                                {{ __('About HikeThere') }}
                            </x-dropdown-link>
                            @else
                            <x-dropdown-link href="{{ route('about') }}">
                                {{ __('About HikeThere') }}
                            </x-dropdown-link>
                            @endif
                            <!-- Support Section -->
                            <x-dropdown-link href="{{ route('support.index') }}">
                                {{ __('Support') }}
                            </x-dropdown-link>

                            <!-- Legal Links -->
                            <x-dropdown-link href="{{ route('terms') }}">
                                {{ __('Terms & Conditions') }}
                            </x-dropdown-link>
                            <x-dropdown-link href="{{ route('privacy') }}">
                                {{ __('Privacy Policy') }}
                            </x-dropdown-link>

                            <div class="border-t border-gray-200"></div>

                            <!-- Authentication -->
                            <form method="POST" action="{{ route('logout') }}" x-data>
                                @csrf

                                <x-dropdown-link href="{{ route('logout') }}"
                                    @click.prevent="$root.submit();">
                                    {{ __('Log Out') }}
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                </div>
            </div>
            @else
            <!-- Login/Register Links for Guests -->
            <div class="hidden sm:flex sm:items-center sm:ms-6 space-x-4">
                <a href="{{ route('login') }}" class="text-gray-500 hover:text-gray-700">{{ __('Login') }}</a>
                <a href="{{ route('register') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">{{ __('Register') }}</a>
            </div>
            @endauth

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="size-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            @if(Auth::user()->user_type === 'organization')
            <x-responsive-nav-link href="{{ route('org.dashboard') }}" :active="request()->routeIs('org.dashboard')">
                {{ __('Organization Dashboard') }}
            </x-responsive-nav-link>
            
            <!-- Trails Section -->
            <div class="px-4 py-2 text-xs font-semibold text-gray-500 uppercase tracking-wider bg-gray-50">
                {{ __('Trails Management') }}
            </div>
            <x-responsive-nav-link href="{{ route('org.trails.create') }}" :active="request()->routeIs('org.trails.create')">
                <div class="flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    {{ __('Add Trail') }}
                </div>
            </x-responsive-nav-link>
            <x-responsive-nav-link href="{{ route('org.trails.index') }}" :active="request()->routeIs('org.trails.index')">
                <div class="flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                    {{ __('Manage Trails') }}
                </div>
            </x-responsive-nav-link>
            <x-responsive-nav-link href="{{ route('organization.emergency-readiness.index') }}" :active="request()->routeIs('organization.emergency-readiness.*')">
                <div class="flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    {{ __('Emergency Assessments') }}
                </div>
            </x-responsive-nav-link>
            <x-responsive-nav-link href="{{ route('organization.safety-incidents.index') }}" :active="request()->routeIs('organization.safety-incidents.*')">
                <div class="flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    {{ __('Safety Incidents') }}
                </div>
            </x-responsive-nav-link>
            
            <x-responsive-nav-link href="{{ route('org.events.index') }}" :active="request()->routeIs('org.events.*')">
                {{ __('Events') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link href="{{ route('org.bookings.index') }}" :active="request()->routeIs('org.bookings.*')">
                {{ __('Bookings') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link href="{{ route('community.index') }}" :active="request()->routeIs('community.index')">
                {{ __('Posts') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link href="{{ route('reports.index') }}" :active="request()->routeIs('reports.*')">
                {{ __('Reports') }}
            </x-responsive-nav-link>
            @else
            <x-responsive-nav-link href="{{ route('dashboard') }}" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link href="{{ route('explore') }}" :active="request()->routeIs('explore')">
                {{ __('Explore') }}
            </x-responsive-nav-link>
            
            <!-- Community Section -->
            <div class="px-4 py-2 text-xs font-semibold text-gray-500 uppercase tracking-wider bg-gray-50">
                {{ __('Community') }}
            </div>
            <x-responsive-nav-link href="{{ route('community.index') }}" :active="request()->routeIs('community.index')">
                <div class="flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    {{ __('Discover Organizations') }}
                </div>
            </x-responsive-nav-link>
            <x-responsive-nav-link href="{{ route('community.index', ['tab' => 'events']) }}" :active="request()->routeIs('community.*') && request()->get('tab') === 'events'">
                <div class="flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    {{ __('Events') }}
                </div>
            </x-responsive-nav-link>
            <x-responsive-nav-link href="{{ route('hiker.incidents.index') }}" :active="request()->routeIs('hiker.incidents.*')">
                <div class="flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    {{ __('My Safety Reports') }}
                </div>
            </x-responsive-nav-link>
            
            <x-responsive-nav-link href="{{ route('map.index') }}" :active="request()->routeIs('map.*')">
                {{ __('Map') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link href="{{ route('hiking-tools') }}" :active="request()->routeIs('hiking-tools')">
                {{ __('Hiking Tools') }}
            </x-responsive-nav-link>
            @endif
        </div>

        @auth
        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="flex items-center px-4">
                <div class="shrink-0 me-3">
                    @if(Auth::user()->profile_picture)
                        <img class="size-10 rounded-full object-cover js-profile-avatar" src="{{ Auth::user()->profile_picture_url }}" alt="{{ Auth::user()->display_name }}" />
                    @else
                        <div role="img" aria-label="{{ Auth::user()->display_name }}'s avatar" class="size-10 w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center text-base font-semibold text-gray-700 js-profile-avatar-placeholder">
                            {{ strtoupper(substr(Auth::user()->display_name ?? 'U', 0, 1)) }}
                        </div>
                    @endif
                </div>

                <div>
                    <div class="font-medium text-base text-gray-800">{{ Auth::user()->display_name }}</div>
                    <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
                </div>
            </div>

            <div class="mt-3 space-y-1">
                <!-- Account Management -->
                @if(Auth::user()->user_type === 'organization')
                <x-responsive-nav-link href="{{ route('org.profile.show') }}" :active="request()->routeIs('org.profile.show')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link href="{{ route('org.profile.edit') }}" :active="request()->routeIs('org.profile.edit')">
                    {{ __('Edit Profile') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link href="{{ route('org.account.settings') }}" :active="request()->routeIs('org.account.settings')">
                    {{ __('Account Settings') }}
                </x-responsive-nav-link>
                @else
                <x-responsive-nav-link href="{{ route('custom.profile.show') }}" :active="request()->routeIs('custom.profile.show')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link href="{{ route('profile.edit') }}" :active="request()->routeIs('profile.edit')">
                    {{ __('Edit Profile') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link href="{{ route('account.settings') }}" :active="request()->routeIs('account.settings')">
                    {{ __('Account Settings') }}
                </x-responsive-nav-link>
                @endif
                @if(Auth::user()->user_type === 'organization')
                <x-responsive-nav-link href="{{ route('org.payment.index') }}" :active="request()->routeIs('org.payment.*')">
                    {{ __('Payment Setup') }}
                </x-responsive-nav-link>
                @endif

                <!-- About HikeThere -->
                @if(Auth::user()->user_type === 'organization')
                <x-responsive-nav-link href="{{ route('org.about') }}" :active="request()->routeIs('org.about')">
                    {{ __('About HikeThere') }}
                </x-responsive-nav-link>
                @else
                <x-responsive-nav-link href="{{ route('about') }}" :active="request()->routeIs('about')">
                    {{ __('About HikeThere') }}
                </x-responsive-nav-link>
                @endif

                <!-- Support -->
                <x-responsive-nav-link href="{{ route('support.index') }}" :active="request()->routeIs('support.*')">
                    {{ __('Support') }}
                </x-responsive-nav-link>

                <!-- Legal Links -->
                <x-responsive-nav-link href="{{ route('terms') }}" :active="request()->routeIs('terms')">
                    {{ __('Terms & Conditions') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link href="{{ route('privacy') }}" :active="request()->routeIs('privacy')">
                    {{ __('Privacy Policy') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}" x-data>
                    @csrf

                    <x-responsive-nav-link href="{{ route('logout') }}"
                        @click.prevent="$root.submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>


            </div>
        </div>
        @else
        <!-- Mobile Login/Register for Guests -->
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="mt-3 space-y-1">
                <x-responsive-nav-link href="{{ route('login') }}">
                    {{ __('Login') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link href="{{ route('register') }}">
                    {{ __('Register') }}
                </x-responsive-nav-link>
            </div>
        </div>
        @endauth
    </div>
</nav>

<!-- Toast Notification Component -->
@auth
<x-toast-notification />
@endauth
</div>
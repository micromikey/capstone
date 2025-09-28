<nav x-data="{ open: false }" class="bg-white border-b border-gray-100 sticky top-0 z-50 shadow-sm">
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
                        {{ __('Organization Dashboard') }}
                    </x-nav-link>
                    <x-nav-link href="{{ route('org.trails.index') }}" :active="request()->routeIs('org.trails.*')">
                        {{ __('Trails') }}
                    </x-nav-link>
                    <x-nav-link href="{{ route('org.events.index') }}" :active="request()->routeIs('org.events.*')">
                        {{ __('Events') }}
                    </x-nav-link>
                    <x-nav-link href="{{ route('org.bookings.index') }}" :active="request()->routeIs('org.bookings.*')">
                        {{ __('Bookings') }}
                    </x-nav-link>
                    @else
                    <x-nav-link href="{{ route('dashboard') }}" :active="request()->routeIs('dashboard')">
                        {{ __('Dashboard') }}
                    </x-nav-link>
                    <x-nav-link href="{{ route('explore') }}" :active="request()->routeIs('explore')">
                        {{ __('Explore') }}
                    </x-nav-link>
                    <x-nav-link href="{{ route('community.index') }}" :active="request()->routeIs('community.*')">
                        {{ __('Community') }}
                    </x-nav-link>
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

                <!-- Settings Dropdown -->
                <div class="ms-3 relative">
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="flex text-sm border-2 border-transparent rounded-full focus:outline-none focus:border-gray-300 transition sticky-header">
                                @if(Auth::user()->profile_picture)
                                    <img class="size-8 rounded-full object-cover js-profile-avatar" src="{{ Auth::user()->profile_picture_url }}" alt="{{ Auth::user()->name }}" />
                                @else
                                    <div role="img" aria-label="{{ Auth::user()->name }}'s avatar" class="size-8 w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center text-sm font-semibold text-gray-700 js-profile-avatar-placeholder">
                                        {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}
                                    </div>
                                @endif
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <!-- Account Management -->
                            <div class="block px-4 py-2 text-xs text-gray-400">
                                {{ __('Manage Account') }}
                            </div>

                            <x-dropdown-link href="{{ route('custom.profile.show') }}">
                                {{ __('Profile') }}
                            </x-dropdown-link>
                            <x-dropdown-link href="{{ route('profile.edit') }}">
                                {{ __('Edit Profile') }}
                            </x-dropdown-link>
                            <x-dropdown-link href="{{ route('account.settings') }}">
                                {{ __('Account Settings') }}
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
            <x-responsive-nav-link href="{{ route('org.trails.index') }}" :active="request()->routeIs('org.trails.*')">
                {{ __('Trails') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link href="{{ route('org.events.index') }}" :active="request()->routeIs('org.events.*')">
                {{ __('Events') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link href="{{ route('org.bookings.index') }}" :active="request()->routeIs('org.bookings.*')">
                {{ __('Bookings') }}
            </x-responsive-nav-link>
            @else
            <x-responsive-nav-link href="{{ route('dashboard') }}" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link href="{{ route('explore') }}" :active="request()->routeIs('explore')">
                {{ __('Explore') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link href="{{ route('community.index') }}" :active="request()->routeIs('community.*')">
                {{ __('Community') }}
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
                    <img class="size-10 rounded-full object-cover js-profile-avatar" src="{{ Auth::user()->profile_picture_url }}" alt="{{ Auth::user()->name }}" />
                </div>

                <div>
                    <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                    <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
                </div>
            </div>

            <div class="mt-3 space-y-1">
                <!-- Account Management -->
                <x-responsive-nav-link href="{{ route('custom.profile.show') }}" :active="request()->routeIs('custom.profile.show')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link href="{{ route('profile.edit') }}" :active="request()->routeIs('profile.edit')">
                    {{ __('Edit Profile') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link href="{{ route('account.settings') }}" :active="request()->routeIs('account.settings')">
                    {{ __('Account Settings') }}
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
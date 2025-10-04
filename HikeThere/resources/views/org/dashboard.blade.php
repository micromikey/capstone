<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Dashboard') }}
            </h2>
            
            {{-- Search Bar --}}
            <form class="flex items-center max-w-2xl w-full relative" action="{{ route('org.search') }}" method="GET">   
                <label for="org-search" class="sr-only">Search</label>
                <div class="relative w-full">
                    <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                        <svg class="w-4 h-4 text-gray-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/>
                        </svg>
                    </div>
                    <input type="text" id="org-search-input" name="q" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[#336d66] focus:border-[#336d66] block w-full ps-10 p-3" placeholder="Search trails, events, bookings..." value="{{ request('q') }}" autocomplete="off" />
                    @include('partials.org-search-dropdown')
                </div>
                <button type="submit" class="inline-flex items-center py-2.5 px-3 ms-2 text-sm font-medium text-white bg-[#336d66] rounded-lg border border-[#336d66] hover:bg-[#20b6d2] focus:ring-4 focus:outline-none focus:ring-[#336d66]/30">
                    <svg class="w-4 h-4 me-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/>
                    </svg>Search
                </button>
            </form>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Welcome Section -->
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg mb-6">
                <div class="p-6 bg-gradient-to-r from-[#336d66] to-[#20b6d2] text-white">
                    <div class="flex items-center space-x-4">
                        <div class="bg-white/20 p-3 rounded-full">
                            <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                        </div>
                        <div>
                            <h1 class="text-2xl font-bold">Welcome, {{ Auth::user()->organization_name }}!</h1>
                            <p class="text-white/90">Your organization account has been verified and is now active.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-8 w-8 text-[#336d66]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500">Total Trails</p>
                                <p class="text-2xl font-semibold text-gray-900">{{ $totalTrails ?? 0 }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-8 w-8 text-[#20b6d2]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500">Active Events</p>
                                <p class="text-2xl font-semibold text-gray-900">{{ $activeEvents ?? 0 }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-8 w-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500">Status</p>
                                <p class="text-2xl font-semibold text-green-600">Verified</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Quick Actions</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <!-- Add New Trail -->
                        <a href="{{ route('org.trails.create') }}" class="group relative bg-white p-6 focus-within:ring-2 focus-within:ring-inset focus-within:ring-[#336d66] rounded-lg border border-gray-200 hover:border-[#336d66] transition-colors">
                            <div>
                                <span class="rounded-lg inline-flex p-3 bg-[#336d66]/10 text-[#336d66] ring-4 ring-white">
                                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                    </svg>
                                </span>
                            </div>
                            <div class="mt-4">
                                <h3 class="text-sm font-medium text-gray-900">
                                    <span class="absolute inset-0" aria-hidden="true"></span>
                                    Add New Trail
                                </h3>
                                <p class="mt-2 text-sm text-gray-500">Create and manage hiking trails for your organization.</p>
                            </div>
                        </a>

                        <!-- Add Event -->
                        <a href="{{ route('org.events.create') }}" class="group relative bg-white p-6 focus-within:ring-2 focus-within:ring-inset focus-within:ring-[#336d66] rounded-lg border border-gray-200 hover:border-[#336d66] transition-colors">
                            <div>
                                <span class="rounded-lg inline-flex p-3 bg-blue-500/10 text-blue-500 ring-4 ring-white">
                                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </span>
                            </div>
                            <div class="mt-4">
                                <h3 class="text-sm font-medium text-gray-900">
                                    <span class="absolute inset-0" aria-hidden="true"></span>
                                    Add Event
                                </h3>
                                <p class="mt-2 text-sm text-gray-500">Schedule new hiking events for your trails.</p>
                            </div>
                        </a>

                        <!-- Manage Trails -->
                        <a href="{{ route('org.trails.index') }}" class="group relative bg-white p-6 focus-within:ring-2 focus-within:ring-inset focus-within:ring-[#336d66] rounded-lg border border-gray-200 hover:border-[#336d66] transition-colors">
                            <div>
                                <span class="rounded-lg inline-flex p-3 bg-green-500/10 text-green-500 ring-4 ring-white">
                                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                                    </svg>
                                </span>
                            </div>
                            <div class="mt-4">
                                <h3 class="text-sm font-medium text-gray-900">
                                    <span class="absolute inset-0" aria-hidden="true"></span>
                                    Manage Trails
                                </h3>
                                <p class="mt-2 text-sm text-gray-500">View and manage all your hiking trails.</p>
                            </div>
                        </a>

                        <!-- Manage Bookings -->
                        <a href="{{ route('org.bookings.index') }}" class="group relative bg-white p-6 focus-within:ring-2 focus-within:ring-inset focus-within:ring-[#336d66] rounded-lg border border-gray-200 hover:border-[#336d66] transition-colors">
                            <div>
                                <span class="rounded-lg inline-flex p-3 bg-indigo-500/10 text-indigo-500 ring-4 ring-white">
                                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                    </svg>
                                </span>
                            </div>
                            <div class="mt-4">
                                <h3 class="text-sm font-medium text-gray-900">
                                    <span class="absolute inset-0" aria-hidden="true"></span>
                                    Manage Bookings
                                </h3>
                                <p class="mt-2 text-sm text-gray-500">Track and manage all your trail bookings.</p>
                            </div>
                        </a>

                        <!-- Emergency Assessments -->
                        <a href="{{ route('organization.emergency-readiness.index') }}" class="group relative bg-white p-6 focus-within:ring-2 focus-within:ring-inset focus-within:ring-[#336d66] rounded-lg border border-gray-200 hover:border-[#336d66] transition-colors">
                            <div>
                                <span class="rounded-lg inline-flex p-3 bg-red-500/10 text-red-500 ring-4 ring-white">
                                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                    </svg>
                                </span>
                            </div>
                            <div class="mt-4">
                                <h3 class="text-sm font-medium text-gray-900">
                                    <span class="absolute inset-0" aria-hidden="true"></span>
                                    Emergency Assessments
                                </h3>
                                <p class="mt-2 text-sm text-gray-500">View hiker feedback on emergency readiness.</p>
                            </div>
                        </a>

                        <!-- Safety Incidents -->
                        <a href="{{ route('organization.safety-incidents.index') }}" class="group relative bg-white p-6 focus-within:ring-2 focus-within:ring-inset focus-within:ring-[#336d66] rounded-lg border border-gray-200 hover:border-[#336d66] transition-colors">
                            <div>
                                <span class="rounded-lg inline-flex p-3 bg-orange-500/10 text-orange-500 ring-4 ring-white">
                                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                </span>
                            </div>
                            <div class="mt-4">
                                <h3 class="text-sm font-medium text-gray-900">
                                    <span class="absolute inset-0" aria-hidden="true"></span>
                                    Safety Incidents
                                </h3>
                                <p class="mt-2 text-sm text-gray-500">Track and manage safety incident reports.</p>
                            </div>
                        </a>

                        <!-- Manage Profile -->
                        <a href="{{ route('custom.profile.show') }}" class="group relative bg-white p-6 focus-within:ring-2 focus-within:ring-inset focus-within:ring-[#336d66] rounded-lg border border-gray-200 hover:border-[#336d66] transition-colors">
                            <div>
                                <span class="rounded-lg inline-flex p-3 bg-[#20b6d2]/10 text-[#20b6d2] ring-4 ring-white">
                                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                </span>
                            </div>
                            <div class="mt-4">
                                <h3 class="text-sm font-medium text-gray-900">
                                    <span class="absolute inset-0" aria-hidden="true"></span>
                                    Manage Profile
                                </h3>
                                <p class="mt-2 text-sm text-gray-500">Update your organization's information and settings.</p>
                            </div>
                        </a>

                        <!-- Support -->
                        <a href="{{ route('support.index') }}" class="group relative bg-white p-6 focus-within:ring-2 focus-within:ring-inset focus-within:ring-[#336d66] rounded-lg border border-gray-200 hover:border-[#336d66] transition-colors">
                            <div>
                                <span class="rounded-lg inline-flex p-3 bg-purple-500/10 text-purple-500 ring-4 ring-white">
                                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                    </svg>
                                </span>
                            </div>
                            <div class="mt-4">
                                <h3 class="text-sm font-medium text-gray-900">
                                    <span class="absolute inset-0" aria-hidden="true"></span>
                                    Support
                                </h3>
                                <p class="mt-2 text-sm text-gray-500">Get help and contact our support team.</p>
                            </div>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-medium text-gray-900">Recent Activity</h3>
                        @if(isset($recentActivity) && $recentActivity->total() > 0)
                            <span class="text-sm text-gray-500">{{ $recentActivity->total() }} {{ Str::plural('activity', $recentActivity->total()) }}</span>
                        @endif
                    </div>
                    <div class="border-t border-gray-200">
                        @if(isset($recentActivity) && $recentActivity->count() > 0)
                            @foreach($recentActivity as $activity)
                                <div class="py-4 border-b border-gray-100 last:border-b-0">
                                    <div class="flex items-center space-x-3">
                                        <div class="flex-shrink-0">
                                            <div class="h-8 w-8 rounded-full bg-{{ $activity['color'] }}-100 flex items-center justify-center">
                                                @if($activity['icon'] === 'trail')
                                                    <svg class="h-5 w-5 text-{{ $activity['color'] }}-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                                                    </svg>
                                                @elseif($activity['icon'] === 'calendar')
                                                    <svg class="h-5 w-5 text-{{ $activity['color'] }}-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                    </svg>
                                                @elseif($activity['icon'] === 'booking')
                                                    <svg class="h-5 w-5 text-{{ $activity['color'] }}-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                                    </svg>
                                                @elseif($activity['icon'] === 'shield')
                                                    <svg class="h-5 w-5 text-{{ $activity['color'] }}-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                                    </svg>
                                                @elseif($activity['icon'] === 'alert')
                                                    <svg class="h-5 w-5 text-{{ $activity['color'] }}-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                                    </svg>
                                                @else
                                                    <svg class="h-5 w-5 text-{{ $activity['color'] }}-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                    </svg>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <p class="text-sm font-medium text-gray-900">{{ $activity['title'] }}</p>
                                            <p class="text-sm text-gray-500">{{ $activity['description'] }}</p>
                                        </div>
                                        <div class="flex-shrink-0 text-sm text-gray-500">
                                            {{ $activity['timestamp']->diffForHumans() }}
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                            
                            <!-- Pagination -->
                            @if($recentActivity->hasPages())
                                <div class="mt-6 border-t border-gray-200 pt-4">
                                    {{ $recentActivity->links() }}
                                </div>
                            @endif
                        @else
                            <div class="py-4">
                                <div class="flex items-center space-x-3">
                                    <div class="flex-shrink-0">
                                        <div class="h-8 w-8 rounded-full bg-[#336d66]/10 flex items-center justify-center">
                                            <svg class="h-5 w-5 text-[#336d66]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <p class="text-sm text-gray-900">
                                            Your organization account was approved on {{ Auth::user()->approved_at ? Auth::user()->approved_at->format('F j, Y \a\t g:i A') : 'Recently' }}
                                        </p>
                                        <p class="text-sm text-gray-500">Welcome to HikeThere! Your account is now active.</p>
                                    </div>
                                    <div class="flex-shrink-0 text-sm text-gray-500">
                                        {{ Auth::user()->approved_at ? Auth::user()->approved_at->diffForHumans() : 'Just now' }}
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
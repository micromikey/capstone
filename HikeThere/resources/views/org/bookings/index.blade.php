<x-app-layout>
    <x-slot name="header">
        <div class="space-y-4">
            <x-trail-breadcrumb />
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div>
                    <h2 class="font-semibold text-2xl text-gray-800 leading-tight">{{ __('Manage Bookings') }}</h2>
                    <p class="text-sm text-gray-600 mt-1">Track and manage all your trail bookings</p>
                </div>
                <a href="{{ route('org.payment.index') }}" class="bg-[#336d66] hover:bg-[#2a5a54] text-white font-semibold py-2.5 px-5 rounded-lg transition-all duration-200 shadow-md hover:shadow-lg flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Payment Setup
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-6 bg-green-50 border-l-4 border-green-500 text-green-800 px-6 py-4 rounded-r-lg shadow-sm">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-3 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <span class="font-medium">{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            <!-- Floating Filters Sidebar (Left) -->
            <div id="floating-filters" class="fixed top-56 left-10 z-40 transition-all duration-300 transform hidden xl:block">
                <div class="bg-white/95 backdrop-blur-md rounded-xl shadow-lg border border-gray-200/50 p-4 w-64 max-h-[calc(100vh-14rem)] overflow-y-auto">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-sm font-semibold text-gray-800 flex items-center">
                            <svg class="w-4 h-4 mr-1.5 text-[#336d66]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                            </svg>
                            Filters
                        </h3>
                        @if(request()->hasAny(['mountain', 'price_min', 'price_max', 'party_min', 'party_max', 'sort_by', 'sort_order']))
                            <a href="{{ route('org.bookings.index') }}" class="text-xs text-red-600 hover:text-red-800 font-medium">Clear</a>
                        @endif
                    </div>

                    <form method="GET" action="{{ route('org.bookings.index') }}" id="bookingFilterForm" class="space-y-3">
                        <!-- Mountain Filter -->
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Mountain</label>
                            <select name="mountain" onchange="this.form.submit()" class="w-full border-gray-300 rounded-md shadow-sm focus:border-[#336d66] focus:ring focus:ring-[#336d66] focus:ring-opacity-50 text-xs py-1.5">
                                <option value="">All Mountains</option>
                                @foreach($mountains as $mountain)
                                    <option value="{{ $mountain }}" {{ request('mountain') == $mountain ? 'selected' : '' }}>{{ $mountain }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Price Range -->
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Price (₱)</label>
                            <div class="space-y-1.5">
                                <input type="number" name="price_min" placeholder="Min" value="{{ request('price_min') }}" onchange="this.form.submit()" class="w-full border-gray-300 rounded-md shadow-sm focus:border-[#336d66] focus:ring focus:ring-[#336d66] focus:ring-opacity-50 text-xs py-1.5">
                                <input type="number" name="price_max" placeholder="Max" value="{{ request('price_max') }}" onchange="this.form.submit()" class="w-full border-gray-300 rounded-md shadow-sm focus:border-[#336d66] focus:ring focus:ring-[#336d66] focus:ring-opacity-50 text-xs py-1.5">
                            </div>
                        </div>

                        <!-- Party Size Range -->
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Party Size</label>
                            <div class="space-y-1.5">
                                <input type="number" name="party_min" placeholder="Min" value="{{ request('party_min') }}" onchange="this.form.submit()" class="w-full border-gray-300 rounded-md shadow-sm focus:border-[#336d66] focus:ring focus:ring-[#336d66] focus:ring-opacity-50 text-xs py-1.5">
                                <input type="number" name="party_max" placeholder="Max" value="{{ request('party_max') }}" onchange="this.form.submit()" class="w-full border-gray-300 rounded-md shadow-sm focus:border-[#336d66] focus:ring focus:ring-[#336d66] focus:ring-opacity-50 text-xs py-1.5">
                            </div>
                        </div>

                        <hr class="my-3 border-gray-200">

                        <!-- Sort By -->
                        <div>
                            <label class="flex items-center text-xs font-medium text-gray-700 mb-1">
                                <svg class="w-3 h-3 mr-1 text-[#336d66]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h13M3 8h9m-9 4h6m4 0l4-4m0 0l4 4m-4-4v12" />
                                </svg>
                                Sort By
                            </label>
                            <select name="sort_by" onchange="this.form.submit()" class="w-full border-gray-300 rounded-md shadow-sm focus:border-[#336d66] focus:ring focus:ring-[#336d66] focus:ring-opacity-50 text-xs py-1.5">
                                <option value="date_booked" {{ request('sort_by') == 'date_booked' ? 'selected' : '' }}>Date Booked</option>
                                <option value="popularity" {{ request('sort_by') == 'popularity' ? 'selected' : '' }}>Popularity</option>
                                <option value="paid" {{ request('sort_by') == 'paid' ? 'selected' : '' }}>Payment Status</option>
                            </select>
                        </div>

                        <!-- Sort Direction -->
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Order</label>
                            <div class="grid grid-cols-2 gap-1.5">
                                <button type="submit" name="sort_order" value="asc" class="flex items-center justify-center px-2 py-1.5 text-xs font-medium rounded-md transition-colors {{ request('sort_order') == 'asc' ? 'bg-[#336d66] text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                                    <svg class="w-3 h-3 mr-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                                    </svg>
                                    Asc
                                </button>
                                <button type="submit" name="sort_order" value="desc" class="flex items-center justify-center px-2 py-1.5 text-xs font-medium rounded-md transition-colors {{ request('sort_order', 'desc') == 'desc' ? 'bg-[#336d66] text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                                    <svg class="w-3 h-3 mr-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                    Desc
                                </button>
                            </div>
                        </div>

                        <!-- Hidden inputs to preserve other sort parameters -->
                        <input type="hidden" name="sort_by" value="{{ request('sort_by', 'date_booked') }}">
                    </form>

                    <!-- Pagination Section -->
                    @if($bookings->hasPages())
                        <hr class="my-3 border-gray-200">
                        <div class="space-y-2">
                            <div class="flex items-center justify-between text-xs text-gray-600">
                                <span>Page {{ $bookings->currentPage() }} of {{ $bookings->lastPage() }}</span>
                                <span class="font-medium">{{ $bookings->total() }} total</span>
                            </div>
                            <div class="flex gap-2">
                                @if($bookings->onFirstPage())
                                    <button disabled class="flex-1 px-3 py-2 text-xs font-medium bg-gray-100 text-gray-400 rounded-md cursor-not-allowed">
                                        <svg class="w-3 h-3 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                                        </svg>
                                        Previous
                                    </button>
                                @else
                                    <a href="{{ $bookings->appends(request()->query())->previousPageUrl() }}" class="flex-1 px-3 py-2 text-xs font-medium bg-[#336d66] hover:bg-[#2a5a54] text-white rounded-md transition-colors text-center">
                                        <svg class="w-3 h-3 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                                        </svg>
                                        Previous
                                    </a>
                                @endif

                                @if($bookings->hasMorePages())
                                    <a href="{{ $bookings->appends(request()->query())->nextPageUrl() }}" class="flex-1 px-3 py-2 text-xs font-medium bg-[#336d66] hover:bg-[#2a5a54] text-white rounded-md transition-colors text-center">
                                        Next
                                        <svg class="w-3 h-3 inline ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                        </svg>
                                    </a>
                                @else
                                    <button disabled class="flex-1 px-3 py-2 text-xs font-medium bg-gray-100 text-gray-400 rounded-md cursor-not-allowed">
                                        Next
                                        <svg class="w-3 h-3 inline ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                        </svg>
                                    </button>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Floating Revenue Dashboard (Right) -->
            <div id="floating-dashboard" class="fixed top-56 right-10 z-40 transition-all duration-300 transform hidden xl:block">
                <div class="bg-white/95 backdrop-blur-md rounded-xl shadow-lg border border-gray-200/50 p-4 w-72 max-h-[calc(100vh-14rem)] overflow-y-auto">
                    <div class="flex items-center mb-4">
                        <svg class="w-5 h-5 mr-2 text-[#336d66]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                        <h3 class="text-sm font-semibold text-gray-800">Revenue Dashboard</h3>
                    </div>

                    <div class="space-y-3">
                        <!-- Total Bookings -->
                        <div class="bg-gradient-to-br from-white to-gray-50 rounded-lg border border-gray-100 p-3 hover:shadow-md transition-shadow">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 bg-[#336d66]/10 rounded-lg p-2">
                                    <svg class="h-5 w-5 text-[#336d66]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-1.447-.894L15 4m0 13V4m-13 0v9a1 1 0 001 1h1m8-4v1a1 1 0 01-1 1H9m13 0a1 1 0 01-1 1H9m13 0v-1a1 1 0 00-1-1H9" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-xs font-medium text-gray-600">Total Bookings</p>
                                    <p class="text-xl font-bold text-gray-900">{{ $bookings->total() }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Total Revenue -->
                        <div class="bg-gradient-to-br from-white to-green-50 rounded-lg border border-green-100 p-3 hover:shadow-md transition-shadow">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 bg-green-100 rounded-lg p-2">
                                    <svg class="h-5 w-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-xs font-medium text-gray-600">Total Revenue</p>
                                    <p class="text-xl font-bold text-green-600">₱{{ number_format($totalRevenue / 100, 2) }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Paid Bookings -->
                        <div class="bg-gradient-to-br from-white to-blue-50 rounded-lg border border-blue-100 p-3 hover:shadow-md transition-shadow">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 bg-blue-100 rounded-lg p-2">
                                    <svg class="h-5 w-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-xs font-medium text-gray-600">Paid Bookings</p>
                                    <p class="text-xl font-bold text-blue-600">{{ $paidBookings }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Pending Status -->
                        <div class="bg-gradient-to-br from-white to-yellow-50 rounded-lg border border-yellow-100 p-3 hover:shadow-md transition-shadow">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 bg-yellow-100 rounded-lg p-2">
                                    <svg class="h-5 w-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-xs font-medium text-gray-600">Pending</p>
                                    <p class="text-xl font-bold text-yellow-600">{{ $bookings->where('status','pending')->count() }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Awaiting Verification -->
                        <a href="{{ route('org.bookings.index', ['payment_status' => 'pending_verification']) }}" class="block">
                            <div class="bg-gradient-to-br from-white to-orange-50 rounded-lg border-2 border-orange-300 p-3 hover:shadow-md hover:border-orange-400 transition-all">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 bg-orange-100 rounded-lg p-2">
                                        <svg class="h-5 w-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-xs font-medium text-gray-600">Awaiting Verification</p>
                                        <p class="text-xl font-bold text-orange-600">{{ $pendingVerificationCount }}</p>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Mobile Revenue Dashboard (shown on smaller screens) -->
            <div class="xl:hidden mb-8">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-[#336d66]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                    Revenue Dashboard
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 lg:gap-6">
                    <div class="bg-gradient-to-br from-white to-gray-50 overflow-hidden shadow-lg sm:rounded-xl border border-gray-100 hover:shadow-xl transition-shadow duration-300">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 bg-[#336d66]/10 rounded-lg p-3">
                                    <svg class="h-8 w-8 text-[#336d66]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-1.447-.894L15 4m0 13V4m-13 0v9a1 1 0 001 1h1m8-4v1a1 1 0 01-1 1H9m13 0a1 1 0 01-1 1H9m13 0v-1a1 1 0 00-1-1H9" />
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <p class="text-xs font-medium text-gray-600 uppercase tracking-wide">Total Bookings</p>
                                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ $bookings->total() }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gradient-to-br from-white to-green-50 overflow-hidden shadow-lg sm:rounded-xl border border-green-100 hover:shadow-xl transition-shadow duration-300">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 bg-green-100 rounded-lg p-3">
                                    <svg class="h-8 w-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <p class="text-xs font-medium text-gray-600 uppercase tracking-wide">Total Revenue</p>
                                    <p class="text-2xl font-bold text-green-600 mt-1">₱{{ number_format($totalRevenue / 100, 2) }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gradient-to-br from-white to-blue-50 overflow-hidden shadow-lg sm:rounded-xl border border-blue-100 hover:shadow-xl transition-shadow duration-300">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 bg-blue-100 rounded-lg p-3">
                                    <svg class="h-8 w-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <p class="text-xs font-medium text-gray-600 uppercase tracking-wide">Paid Bookings</p>
                                    <p class="text-2xl font-bold text-blue-600 mt-1">{{ $paidBookings }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gradient-to-br from-white to-yellow-50 overflow-hidden shadow-lg sm:rounded-xl border border-yellow-100 hover:shadow-xl transition-shadow duration-300">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 bg-yellow-100 rounded-lg p-3">
                                    <svg class="h-8 w-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <p class="text-xs font-medium text-gray-600 uppercase tracking-wide">Pending</p>
                                    <p class="text-2xl font-bold text-yellow-600 mt-1">{{ $bookings->where('status','pending')->count() }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gradient-to-br from-white to-orange-50 overflow-hidden shadow-lg sm:rounded-xl border-2 border-orange-300 hover:shadow-xl transition-all duration-300 hover:border-orange-400">
                        <a href="{{ route('org.bookings.index', ['payment_status' => 'pending_verification']) }}" class="block p-6 hover:bg-orange-50 transition-colors">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 bg-orange-100 rounded-lg p-3">
                                    <svg class="h-8 w-8 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <p class="text-xs font-medium text-gray-600 uppercase tracking-wide">Awaiting Verification</p>
                                    <p class="text-2xl font-bold text-orange-600 mt-1">{{ $pendingVerificationCount }}</p>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Bookings Table -->
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-xl border border-gray-200">
                @if($bookings->count())
                    <div class="p-6">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                                    <tr>
                                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Booking</th>
                                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Hiker</th>
                                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Trail</th>
                                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Date</th>
                                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Party</th>
                                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Amount</th>
                                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Payment</th>
                                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Status</th>
                                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($bookings as $booking)
                                        <tr class="hover:bg-gray-50 transition-colors {{ $booking->isPaymentPendingVerification() ? 'bg-orange-50' : '' }}">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="text-sm font-bold text-gray-900">#{{ $booking->id }}</span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @php
                                                    $name = $booking->user->name ?? 'N/A';
                                                    $email = $booking->user->email ?? '';
                                                    
                                                    // Redact name - show first letter of first name and last name
                                                    if ($name !== 'N/A') {
                                                        $nameParts = explode(' ', $name);
                                                        if (count($nameParts) > 1) {
                                                            $redactedName = substr($nameParts[0], 0, 1) . str_repeat('*', max(3, strlen($nameParts[0]) - 1)) . ' ' . 
                                                                          substr($nameParts[count($nameParts) - 1], 0, 1) . str_repeat('*', max(3, strlen($nameParts[count($nameParts) - 1]) - 1));
                                                        } else {
                                                            $redactedName = substr($name, 0, 1) . str_repeat('*', max(3, strlen($name) - 1));
                                                        }
                                                    } else {
                                                        $redactedName = 'N/A';
                                                    }
                                                    
                                                    // Redact email - show first letter, stars, @ domain with stars
                                                    if (!empty($email)) {
                                                        $emailParts = explode('@', $email);
                                                        $localPart = $emailParts[0];
                                                        $domainPart = $emailParts[1] ?? '';
                                                        
                                                        $redactedEmail = substr($localPart, 0, 1) . str_repeat('*', min(4, strlen($localPart) - 1)) . '@';
                                                        if (!empty($domainPart)) {
                                                            $domainPieces = explode('.', $domainPart);
                                                            $redactedEmail .= substr($domainPieces[0], 0, 1) . str_repeat('*', min(3, strlen($domainPieces[0]) - 1));
                                                            if (count($domainPieces) > 1) {
                                                                $redactedEmail .= '.' . end($domainPieces);
                                                            }
                                                        }
                                                    } else {
                                                        $redactedEmail = '';
                                                    }
                                                @endphp
                                                <div class="flex items-center">
                                                    <div>
                                                        <div class="text-sm font-medium text-gray-900">{{ $redactedName }}</div>
                                                        <div class="text-xs text-gray-500">{{ $redactedEmail }}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900">{{ $booking->trail->trail_name ?? 'N/A' }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900">{{ \Carbon\Carbon::parse($booking->date)->format('M d, Y') }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                                <span class="inline-flex items-center justify-center px-3 py-1 text-sm font-semibold text-gray-900 bg-gray-100 rounded-full">
                                                    {{ $booking->party_size }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="text-sm font-bold text-gray-900">₱{{ number_format($booking->price_cents / 100, 2) }}</span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                @if($booking->usesManualPayment())
                                                    @if($booking->payment_status === 'pending')
                                                        <span class="inline-flex items-center px-2.5 py-1.5 text-xs font-semibold rounded-full bg-orange-100 text-orange-800 shadow-sm">
                                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                            </svg>
                                                            Verify Payment
                                                        </span>
                                                    @elseif($booking->payment_status === 'verified')
                                                        <span class="inline-flex items-center px-2.5 py-1.5 text-xs font-semibold rounded-full bg-green-100 text-green-800 shadow-sm">
                                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                            </svg>
                                                            Verified
                                                        </span>
                                                    @elseif($booking->payment_status === 'rejected')
                                                        <span class="inline-flex items-center px-2.5 py-1.5 text-xs font-semibold rounded-full bg-red-100 text-red-800 shadow-sm">Rejected</span>
                                                    @else
                                                        <span class="inline-flex px-2.5 py-1.5 text-xs font-semibold rounded-full bg-gray-100 text-gray-800 shadow-sm">{{ ucfirst($booking->payment_status ?? 'N/A') }}</span>
                                                    @endif
                                                @elseif($booking->payment)
                                                    @if($booking->payment->isPaid())
                                                        <span class="inline-flex items-center px-2.5 py-1.5 text-xs font-semibold rounded-full bg-green-100 text-green-800 shadow-sm">Paid</span>
                                                    @elseif($booking->payment->isPending())
                                                        <span class="inline-flex px-2.5 py-1.5 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800 shadow-sm">Pending</span>
                                                    @else
                                                        <span class="inline-flex px-2.5 py-1.5 text-xs font-semibold rounded-full bg-red-100 text-red-800 shadow-sm">{{ ucfirst($booking->payment->payment_status) }}</span>
                                                    @endif
                                                @else
                                                    <span class="inline-flex px-2.5 py-1.5 text-xs font-semibold rounded-full bg-gray-100 text-gray-800 shadow-sm">No Payment</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                <span class="inline-flex px-2.5 py-1.5 text-xs font-semibold rounded-full shadow-sm {{ $booking->status === 'confirmed' ? 'bg-green-100 text-green-800' : ($booking->status === 'cancelled' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                                    {{ ucfirst($booking->status) }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <div class="flex items-center gap-3">
                                                    <a href="{{ route('org.bookings.show', $booking) }}" class="text-[#336d66] hover:text-[#2a5a54] font-semibold hover:underline">View</a>
                                                    
                                                    @if($booking->isPaymentPendingVerification())
                                                        <form method="POST" action="{{ route('org.bookings.verify-payment', $booking) }}" class="inline">
                                                            @csrf
                                                            <button type="submit" class="text-green-600 hover:text-green-800 font-bold text-lg" onclick="return confirm('Verify this payment?')" title="Verify Payment">
                                                                ✓
                                                            </button>
                                                        </form>
                                                        <form method="POST" action="{{ route('org.bookings.reject-payment', $booking) }}" class="inline">
                                                            @csrf
                                                            <button type="submit" class="text-red-600 hover:text-red-800 font-bold text-lg" onclick="return confirm('Reject this payment?')" title="Reject Payment">
                                                                ✗
                                                            </button>
                                                        </form>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Enhanced Pagination -->
                        <div class="mt-8 border-t border-gray-200 pt-6">
                            <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                                <div class="text-sm text-gray-700">
                                    Showing <span class="font-semibold text-gray-900">{{ $bookings->firstItem() ?? 0 }}</span> 
                                    to <span class="font-semibold text-gray-900">{{ $bookings->lastItem() ?? 0 }}</span> 
                                    of <span class="font-semibold text-gray-900">{{ $bookings->total() }}</span> results
                                </div>
                                <div class="pagination-wrapper">
                                    {{ $bookings->appends(request()->query())->links() }}
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="p-12 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No bookings yet</h3>
                        <p class="mt-1 text-sm text-gray-500">Bookings will appear here once hikers book your trails.</p>
                        <div class="mt-6">
                            <a href="{{ route('org.trails.index') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-[#336d66] hover:bg-[#2a5a54] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#336d66]">
                                <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-1.447-.894L15 4m0 13V4m-13 0v9a1 1 0 001 1h1m8-4v1a1 1 0 01-1 1H9m13 0a1 1 0 01-1 1H9m13 0v-1a1 1 0 00-1-1H9" />
                                </svg>
                                View Your Trails
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
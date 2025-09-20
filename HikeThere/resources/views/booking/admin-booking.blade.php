<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-emerald-800 leading-tight tracking-tight drop-shadow-sm">
            {{ __('Hiking Booking Management') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-gradient-to-br from-emerald-50 via-white to-teal-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Success/Error Messages --}}
            @if (session('success'))
                <div class="mb-6 bg-green-100 border border-green-300 rounded-xl p-4 shadow-md">
                    <div class="flex items-center">
                        <svg class="flex-shrink-0 h-6 w-6 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <div class="ml-3">
                            <p class="text-base text-green-800 font-semibold">{{ session('success') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            @if (session('error'))
                <div class="mb-6 bg-red-100 border border-red-300 rounded-xl p-4 shadow-md">
                    <div class="flex items-center">
                        <svg class="flex-shrink-0 h-6 w-6 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                        <div class="ml-3">
                            <p class="text-base text-red-800 font-semibold">{{ session('error') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Page Header --}}
            <div class="mb-8">
                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center">
                    <div>
                        <h1 class="text-4xl font-extrabold text-emerald-900 drop-shadow-sm">Manage Hiking Bookings</h1>
                        <p class="text-lg text-emerald-700 mt-1">View and manage all hiking reservations</p>
                    </div>
                    <div class="mt-4 sm:mt-0 flex space-x-3">
                        <a href="#" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-3 rounded-xl shadow-md transition-colors duration-200 flex items-center font-semibold text-base">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            Export CSV
                        </a>
                        <a href="#" class="bg-emerald-600 hover:bg-emerald-700 text-white px-5 py-3 rounded-xl shadow-md transition-colors duration-200 flex items-center font-semibold text-base">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            New Booking
                        </a>
                    </div>
                </div>
            </div>

            {{-- Statistics Cards --}}
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-gradient-to-br from-green-100 to-white shadow-lg rounded-2xl hover:shadow-xl transition-shadow duration-200 border border-green-200">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-green-200 text-green-700 mr-4 shadow">
                                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="text-base font-semibold text-green-700">Total Bookings</p>
                                <p class="text-3xl font-extrabold text-emerald-900">{{ $totalBookings ?? 147 }}</p>
                                <p class="text-xs text-green-600 mt-1">+12% from last month</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gradient-to-br from-blue-100 to-white shadow-lg rounded-2xl hover:shadow-xl transition-shadow duration-200 border border-blue-200">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-blue-200 text-blue-700 mr-4 shadow">
                                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="text-base font-semibold text-blue-700">Confirmed</p>
                                <p class="text-3xl font-extrabold text-blue-900">{{ $confirmedCount ?? 89 }}</p>
                                <p class="text-xs text-blue-600 mt-1">{{ number_format(($confirmedCount ?? 89) / ($totalBookings ?? 147) * 100, 1) }}% of total</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gradient-to-br from-yellow-100 to-white shadow-lg rounded-2xl hover:shadow-xl transition-shadow duration-200 border border-yellow-200">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-yellow-200 text-yellow-700 mr-4 shadow">
                                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="text-base font-semibold text-yellow-700">Pending</p>
                                <p class="text-3xl font-extrabold text-yellow-900">{{ $pendingCount ?? 32 }}</p>
                                <p class="text-xs text-yellow-600 mt-1">Requires attention</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gradient-to-br from-purple-100 to-white shadow-lg rounded-2xl hover:shadow-xl transition-shadow duration-200 border border-purple-200">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-purple-200 text-purple-700 mr-4 shadow">
                                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="text-base font-semibold text-purple-700">Total Revenue</p>
                                <p class="text-3xl font-extrabold text-purple-900">₱{{ number_format($totalRevenue ?? 467300, 2) }}</p>
                                <p class="text-xs text-purple-600 mt-1">This month</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Quick Actions --}}
            <div class="bg-white overflow-hidden shadow-lg rounded-2xl mb-8 border border-emerald-100">
                <div class="p-6">
                    <h3 class="text-xl font-bold text-emerald-800 mb-4">Quick Actions</h3>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <button onclick="confirmPendingBookings()" class="flex items-center justify-center px-4 py-3 bg-blue-50 text-blue-700 rounded-xl hover:bg-blue-100 transition-colors duration-200 font-semibold shadow">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Confirm Pending
                        </button>
                        <button onclick="sendReminders()" class="flex items-center justify-center px-4 py-3 bg-green-50 text-green-700 rounded-xl hover:bg-green-100 transition-colors duration-200 font-semibold shadow">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                            Send Reminders
                        </button>
                        <button onclick="generateReport()" class="flex items-center justify-center px-4 py-3 bg-yellow-50 text-yellow-700 rounded-xl hover:bg-yellow-100 transition-colors duration-200 font-semibold shadow">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                            Generate Report
                        </button>
                        <a href="#" class="flex items-center justify-center px-4 py-3 bg-purple-50 text-purple-700 rounded-xl hover:bg-purple-100 transition-colors duration-200 font-semibold shadow">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path>
                            </svg>
                            Manage Trails
                        </a>
                    </div>
                </div>
            </div>

            {{-- Main Booking Table Card --}}
            <div class="bg-white overflow-hidden shadow-2xl rounded-2xl border border-emerald-100">
                <div class="p-6">
                    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center mb-6">
                        <h3 class="text-2xl font-bold text-emerald-900 mb-4 sm:mb-0">Recent Hiking Bookings</h3>
                        <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-2">
                            <select name="status_filter" onchange="filterByStatus(this.value)" class="border border-gray-300 rounded-lg px-4 py-2 bg-white text-base focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent shadow">
                                <option value="">All Status</option>
                                <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                            <select name="trail_filter" onchange="filterByTrail(this.value)" class="border border-gray-300 rounded-lg px-4 py-2 bg-white text-base focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent shadow">
                                <option value="">All Trails</option>
                                <option value="Mt. Kulis">Mt. Kulis</option>
                                <option value="Mt. Pulag">Mt. Pulag</option>
                                <option value="Mt. Batulao">Mt. Batulao</option>
                                <option value="Mt. Apo">Mt. Apo</option>
                                <option value="Mt. Malindig">Mt. Malindig</option>
                            </select>
                            <input type="date" name="date_filter" onchange="filterByDate(this.value)" value="{{ request('date') }}" class="border border-gray-300 rounded-lg px-4 py-2 text-base focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent shadow">
                        </div>
                    </div>

                    {{-- Search and Filter Form --}}
                    <form method="GET" action="{{ route('admin.bookings') }}" class="mb-6">
                        <div class="flex flex-col sm:flex-row gap-4">
                            <div class="flex-1">
                                <input type="text" 
                                       name="search" 
                                       value="{{ request('search') }}" 
                                       placeholder="Search by hiker name, email, or booking ID..." 
                                       class="w-full border border-gray-300 rounded-lg px-5 py-3 text-base focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent shadow">
                            </div>
                            <div class="flex space-x-2">
                                <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white px-5 py-3 rounded-lg transition-colors duration-200 flex items-center font-semibold shadow">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                    Search
                                </button>
                                @if(request('search') || request('status') || request('trail') || request('date'))
                                    <a href="{{ route('admin.bookings') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-5 py-3 rounded-lg transition-colors duration-200 flex items-center font-semibold shadow">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                        Clear
                                    </a>
                                @endif
                            </div>
                        </div>
                    </form>

                    {{-- Bookings Table --}}
                    <div class="overflow-x-auto rounded-xl border border-emerald-100 shadow">
                        <table class="min-w-full divide-y divide-emerald-100">
                            <thead class="bg-gradient-to-r from-emerald-100 to-teal-100">
                                <tr>
                                    <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-emerald-700 uppercase tracking-wider">
                                        <input type="checkbox" id="select-all" class="rounded border-gray-300 text-green-600 focus:ring-green-500">
                                    </th>
                                    <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-emerald-700 uppercase tracking-wider cursor-pointer hover:bg-emerald-50" onclick="sortTable('id')">
                                        Booking ID
                                    </th>
                                    <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-emerald-700 uppercase tracking-wider">
                                        Hiker Details
                                    </th>
                                    <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-emerald-700 uppercase tracking-wider cursor-pointer hover:bg-emerald-50" onclick="sortTable('trail')">
                                        Trail
                                    </th>
                                    <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-emerald-700 uppercase tracking-wider cursor-pointer hover:bg-emerald-50" onclick="sortTable('hike_date')">
                                        Hike Date
                                    </th>
                                    <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-emerald-700 uppercase tracking-wider">
                                        Participants
                                    </th>
                                    <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-emerald-700 uppercase tracking-wider">
                                        Status
                                    </th>
                                    <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-emerald-700 uppercase tracking-wider cursor-pointer hover:bg-emerald-50" onclick="sortTable('amount')">
                                        Amount
                                    </th>
                                    <th scope="col" class="relative px-6 py-4">
                                        <span class="sr-only">Actions</span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-emerald-100">
                                @php
                                    $dummyBookings = collect([
                                        (object)[
                                            'id' => 'HK-2024-001',
                                            'hiker_name' => 'Juan Miguel Santos',
                                            'hiker_email' => 'juan.santos@email.com',
                                            'hiker_phone' => '+63 917 123 4567',
                                            'trail_name' => 'Mt. Pulag',
                                            'hike_date' => '2024-03-15',
                                            'participants' => 4,
                                            'status' => 'confirmed',
                                            'amount' => 12500.00,
                                            'created_at' => '2024-03-01 10:30:00'
                                        ],
                                        (object)[
                                            'id' => 'HK-2024-002',
                                            'hiker_name' => 'Maria Clara Rodriguez',
                                            'hiker_email' => 'maria.rodriguez@email.com',
                                            'hiker_phone' => '+63 916 234 5678',
                                            'trail_name' => 'Mt. Batulao',
                                            'hike_date' => '2024-03-20',
                                            'participants' => 2,
                                            'status' => 'pending',
                                            'amount' => 6800.00,
                                            'created_at' => '2024-03-02 14:15:00'
                                        ],
                                        (object)[
                                            'id' => 'HK-2024-003',
                                            'hiker_name' => 'Carlos Antonio Mendoza',
                                            'hiker_email' => 'carlos.mendoza@email.com',
                                            'hiker_phone' => '+63 918 345 6789',
                                            'trail_name' => 'Mt. Apo',
                                            'hike_date' => '2024-03-25',
                                            'participants' => 6,
                                            'status' => 'confirmed',
                                            'amount' => 28800.00,
                                            'created_at' => '2024-03-03 09:45:00'
                                        ],
                                        (object)[
                                            'id' => 'HK-2024-004',
                                            'hiker_name' => 'Ana Sofia Reyes',
                                            'hiker_email' => 'ana.reyes@email.com',
                                            'hiker_phone' => '+63 919 456 7890',
                                            'trail_name' => 'Mt. Kulis',
                                            'hike_date' => '2024-03-18',
                                            'participants' => 3,
                                            'status' => 'cancelled',
                                            'amount' => 8700.00,
                                            'created_at' => '2024-02-28 16:20:00'
                                        ],
                                        (object)[
                                            'id' => 'HK-2024-005',
                                            'hiker_name' => 'Roberto Luis Garcia',
                                            'hiker_email' => 'roberto.garcia@email.com',
                                            'hiker_phone' => '+63 920 567 8901',
                                            'trail_name' => 'Mt. Malindig',
                                            'hike_date' => '2024-04-02',
                                            'participants' => 5,
                                            'status' => 'pending',
                                            'amount' => 18500.00,
                                            'created_at' => '2024-03-04 11:00:00'
                                        ],
                                        (object)[
                                            'id' => 'HK-2024-006',
                                            'hiker_name' => 'Isabella Marie Torres',
                                            'hiker_email' => 'isabella.torres@email.com',
                                            'hiker_phone' => '+63 921 678 9012',
                                            'trail_name' => 'Mt. Pulag',
                                            'hike_date' => '2024-03-22',
                                            'participants' => 2,
                                            'status' => 'confirmed',
                                            'amount' => 7200.00,
                                            'created_at' => '2024-03-05 13:30:00'
                                        ],
                                        (object)[
                                            'id' => 'HK-2024-007',
                                            'hiker_name' => 'Diego Fernando Cruz',
                                            'hiker_email' => 'diego.cruz@email.com',
                                            'hiker_phone' => '+63 922 789 0123',
                                            'trail_name' => 'Mt. Batulao',
                                            'hike_date' => '2024-04-05',
                                            'participants' => 4,
                                            'status' => 'confirmed',
                                            'amount' => 13600.00,
                                            'created_at' => '2024-03-06 08:45:00'
                                        ],
                                        (object)[
                                            'id' => 'HK-2024-008',
                                            'hiker_name' => 'Sophia Elena Morales',
                                            'hiker_email' => 'sophia.morales@email.com',
                                            'hiker_phone' => '+63 923 890 1234',
                                            'trail_name' => 'Mt. Apo',
                                            'hike_date' => '2024-04-10',
                                            'participants' => 8,
                                            'status' => 'pending',
                                            'amount' => 38400.00,
                                            'created_at' => '2024-03-07 15:15:00'
                                        ]
                                    ]);
                                @endphp

                                @forelse($bookings ?? $dummyBookings as $booking)
                                <tr class="hover:bg-emerald-50 transition-colors duration-200">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <input type="checkbox" class="rounded border-gray-300 text-green-600 focus:ring-green-500 booking-checkbox" value="{{ $booking->id }}">
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-base font-bold text-emerald-900">#{{ $booking->id }}</div>
                                        <div class="text-sm text-emerald-600">{{ \Carbon\Carbon::parse($booking->created_at)->format('M d, Y') }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-12 w-12">
                                                <div class="h-12 w-12 rounded-full bg-gradient-to-br from-emerald-400 to-teal-500 flex items-center justify-center text-white font-bold text-lg shadow-md">
                                                    {{ substr($booking->hiker_name, 0, 1) }}{{ substr(explode(' ', $booking->hiker_name)[1] ?? '', 0, 1) }}
                                                </div>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-base font-bold text-emerald-900">{{ $booking->hiker_name }}</div>
                                                <div class="text-sm text-emerald-600">{{ $booking->hiker_email }}</div>
                                                <div class="text-xs text-gray-500">{{ $booking->hiker_phone }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <svg class="w-5 h-5 text-emerald-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path>
                                            </svg>
                                            <div>
                                                <div class="text-base font-bold text-emerald-900">{{ $booking->trail_name }}</div>
                                                <div class="text-sm text-emerald-600">
                                                    @switch($booking->trail_name)
                                                        @case('Mt. Pulag')
                                                            Difficulty: Moderate | 3D/2N
                                                            @break
                                                        @case('Mt. Apo')
                                                            Difficulty: Hard | 4D/3N
                                                            @break
                                                        @case('Mt. Batulao')
                                                            Difficulty: Easy | Day Hike
                                                            @break
                                                        @case('Mt. Kulis')
                                                            Difficulty: Easy | Day Hike
                                                            @break
                                                        @case('Mt. Malindig')
                                                            Difficulty: Moderate | 2D/1N
                                                            @break
                                                        @default
                                                            Difficulty: Varies
                                                    @endswitch
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <svg class="w-5 h-5 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                            <div>
                                                <div class="text-base font-bold text-gray-900">{{ \Carbon\Carbon::parse($booking->hike_date)->format('M d, Y') }}</div>
                                                <div class="text-sm text-gray-600">{{ \Carbon\Carbon::parse($booking->hike_date)->format('l') }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <div class="flex items-center justify-center">
                                            <svg class="w-5 h-5 text-purple-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z"></path>
                                            </svg>
                                            <span class="text-2xl font-bold text-purple-900">{{ $booking->participants }}</span>
                                        </div>
                                        <div class="text-sm text-gray-500 mt-1">
                                            {{ $booking->participants == 1 ? 'person' : 'people' }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @switch($booking->status)
                                            @case('confirmed')
                                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-green-100 text-green-800 shadow">
                                                    <svg class="w-4 h-4 mr-1.5" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                    </svg>
                                                    Confirmed
                                                </span>
                                                @break
                                            @case('pending')
                                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-yellow-100 text-yellow-800 shadow">
                                                    <svg class="w-4 h-4 mr-1.5" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                                                    </svg>
                                                    Pending
                                                </span>
                                                @break
                                            @case('cancelled')
                                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-red-100 text-red-800 shadow">
                                                    <svg class="w-4 h-4 mr-1.5" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                                    </svg>
                                                    Cancelled
                                                </span>
                                                @break
                                        @endswitch
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-base font-bold text-emerald-900">₱{{ number_format($booking->amount, 2) }}</div>
                                        <div class="text-sm text-gray-600">₱{{ number_format($booking->amount / $booking->participants, 2) }} per person</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex items-center space-x-2">
                                            <button onclick="viewBooking('{{ $booking->id }}')" class="text-blue-600 hover:text-blue-900 p-2 rounded-lg hover:bg-blue-50 transition-colors duration-200" title="View Details">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                </svg>
                                            </button>
                                            <button onclick="editBooking('{{ $booking->id }}')" class="text-emerald-600 hover:text-emerald-900 p-2 rounded-lg hover:bg-emerald-50 transition-colors duration-200" title="Edit Booking">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                </svg>
                                            </button>
                                            @if($booking->status === 'pending')
                                                <button onclick="confirmBooking('{{ $booking->id }}')" class="text-green-600 hover:text-green-900 p-2 rounded-lg hover:bg-green-50 transition-colors duration-200" title="Confirm Booking">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                    </svg>
                                                </button>
                                            @endif
                                            <div class="relative inline-block text-left">
                                                <button onclick="toggleDropdown('{{ $booking->id }}')" class="text-gray-600 hover:text-gray-900 p-2 rounded-lg hover:bg-gray-50 transition-colors duration-200" title="More Actions">
                                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z"/>
                                                    </svg>
                                                </button>
                                                <div id="dropdown-{{ $booking->id }}" class="hidden origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none z-10">
                                                    <div class="py-1">
                                                        <a href="#" onclick="sendEmail('{{ $booking->id }}')" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                                            </svg>
                                                            Send Email
                                                        </a>
                                                        <a href="#" onclick="printBooking('{{ $booking->id }}')" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                                                            </svg>
                                                            Print Voucher
                                                        </a>
                                                        @if($booking->status !== 'cancelled')
                                                            <div class="border-t border-gray-100"></div>
                                                            <a href="#" onclick="cancelBooking('{{ $booking->id }}')" class="flex items-center px-4 py-2 text-sm text-red-700 hover:bg-red-50">
                                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                                </svg>
                                                                Cancel Booking
                                                            </a>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="9" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center">
                                            <svg class="w-12 h-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                            </svg>
                                            <h3 class="text-lg font-medium text-gray-900 mb-2">No bookings found</h3>
                                            <p class="text-gray-500 mb-4">No hiking bookings match your current filters.</p>
                                            <a href="#" class="bg-emerald-600 hover:bg-emerald-700 text-white px-5 py-3 rounded-lg transition-colors duration-200 font-semibold shadow">
                                                Create New Booking
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Bulk Actions --}}
                    <div id="bulk-actions" class="hidden mt-4 p-4 bg-emerald-50 rounded-xl shadow">
                        <div class="flex items-center space-x-4">
                            <span class="text-base font-semibold text-emerald-700">With selected:</span>
                            <button onclick="bulkConfirm()" class="text-green-700 hover:text-green-900 text-base font-semibold">Confirm</button>
                            <button onclick="bulkCancel()" class="text-red-700 hover:text-red-900 text-base font-semibold">Cancel</button>
                            <button onclick="bulkExport()" class="text-blue-700 hover:text-blue-900 text-base font-semibold">Export</button>
                            <button onclick="bulkEmail()" class="text-purple-700 hover:text-purple-900 text-base font-semibold">Send Email</button>
                        </div>
                    </div>

                    {{-- Pagination --}}
                    @if(isset($bookings) && method_exists($bookings, 'links'))
                    <div class="mt-6">
                        {{ $bookings->appends(request()->query())->links() }}
                    </div>
                    @else
                    <div class="mt-6 flex items-center justify-between">
                        <div class="text-base text-emerald-700">
                            Showing <span class="font-bold">1</span> to <span class="font-bold">8</span> of <span class="font-bold">147</span> results
                        </div>
                        <div class="flex space-x-2">
                            <button class="px-4 py-2 text-base font-semibold text-gray-500 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 disabled:opacity-50" disabled>Previous</button>
                            <button class="px-4 py-2 text-base font-semibold text-white bg-emerald-600 border border-transparent rounded-lg">1</button>
                            <button class="px-4 py-2 text-base font-semibold text-emerald-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">2</button>
                            <button class="px-4 py-2 text-base font-semibold text-emerald-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">3</button>
                            <button class="px-4 py-2 text-base font-semibold text-emerald-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">Next</button>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- JavaScript for Interactive Features --}}
    <script>
        // Toggle dropdown menus
        function toggleDropdown(bookingId) {
            const dropdown = document.getElementById(`dropdown-${bookingId}`);
            dropdown.classList.toggle('hidden');
            
            // Close other dropdowns
            document.querySelectorAll('[id^="dropdown-"]').forEach(el => {
                if (el.id !== `dropdown-${bookingId}`) {
                    el.classList.add('hidden');
                }
            });
        }

        // Close dropdowns when clicking outside
        document.addEventListener('click', function(event) {
            if (!event.target.closest('[onclick^="toggleDropdown"]')) {
                document.querySelectorAll('[id^="dropdown-"]').forEach(el => {
                    el.classList.add('hidden');
                });
            }
        });

        // Select all functionality
        document.getElementById('select-all').addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('.booking-checkbox');
            const bulkActions = document.getElementById('bulk-actions');
            
            checkboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            
            bulkActions.classList.toggle('hidden', !this.checked);
        });

        // Individual checkbox functionality
        document.querySelectorAll('.booking-checkbox').forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const checkedBoxes = document.querySelectorAll('.booking-checkbox:checked');
                const bulkActions = document.getElementById('bulk-actions');
                const selectAll = document.getElementById('select-all');
                
                bulkActions.classList.toggle('hidden', checkedBoxes.length === 0);
                selectAll.checked = checkedBoxes.length === document.querySelectorAll('.booking-checkbox').length;
            });
        });

        // Action functions (placeholder implementations)
        function viewBooking(bookingId) {
            alert(`View booking details for ${bookingId}`);
        }

        function editBooking(bookingId) {
            alert(`Edit booking ${bookingId}`);
        }

        function confirmBooking(bookingId) {
            if (confirm(`Confirm booking ${bookingId}?`)) {
                alert(`Booking ${bookingId} confirmed!`);
                location.reload();
            }
        }

        function cancelBooking(bookingId) {
            if (confirm(`Cancel booking ${bookingId}? This action cannot be undone.`)) {
                alert(`Booking ${bookingId} cancelled!`);
                location.reload();
            }
        }

        function sendEmail(bookingId) {
            alert(`Send email for booking ${bookingId}`);
        }

        function printBooking(bookingId) {
            alert(`Print voucher for booking ${bookingId}`);
        }

        // Filter functions
        function filterByStatus(status) {
            const url = new URL(window.location.href);
            if (status) {
                url.searchParams.set('status', status);
            } else {
                url.searchParams.delete('status');
            }
            window.location.href = url.toString();
        }

        function filterByTrail(trail) {
            const url = new URL(window.location.href);
            if (trail) {
                url.searchParams.set('trail', trail);
            } else {
                url.searchParams.delete('trail');
            }
            window.location.href = url.toString();
        }

        function filterByDate(date) {
            const url = new URL(window.location.href);
            if (date) {
                url.searchParams.set('date', date);
            } else {
                url.searchParams.delete('date');
            }
            window.location.href = url.toString();
        }

        // Quick action functions
        function confirmPendingBookings() {
            alert('Confirm all pending bookings');
        }

        function sendReminders() {
            alert('Send reminder emails');
        }

        function generateReport() {
            alert('Generate booking report');
        }

        // Bulk action functions
        function bulkConfirm() {
            const selected = document.querySelectorAll('.booking-checkbox:checked');
            if (selected.length > 0 && confirm(`Confirm ${selected.length} selected booking(s)?`)) {
                alert(`${selected.length} bookings confirmed!`);
                location.reload();
            }
        }

        function bulkCancel() {
            const selected = document.querySelectorAll('.booking-checkbox:checked');
            if (selected.length > 0 && confirm(`Cancel ${selected.length} selected booking(s)? This action cannot be undone.`)) {
                alert(`${selected.length} bookings cancelled!`);
                location.reload();
            }
        }

        function bulkExport() {
            const selected = document.querySelectorAll('.booking-checkbox:checked');
            if (selected.length > 0) {
                alert(`Export ${selected.length} selected booking(s)`);
            }
        }

        function bulkEmail() {
            const selected = document.querySelectorAll('.booking-checkbox:checked');
            if (selected.length > 0) {
                alert(`Send email to ${selected.length} selected booking(s)`);
            }
        }

        // Sort table function
        function sortTable(column) {
            alert(`Sort by ${column}`);
        }
    </script>
</x-app-layout>
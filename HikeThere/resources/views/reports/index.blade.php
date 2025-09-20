{{-- resources/views/reports/index.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-emerald-800 leading-tight tracking-tight drop-shadow-sm">
            {{ __('Report Generation System') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-gradient-to-br from-emerald-50 via-white to-teal-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white/90 shadow-2xl rounded-2xl border border-emerald-100 overflow-hidden">
                <div class="p-8 text-gray-900">
                    <!-- Enhanced Header with Statistics -->
                    <div class="mb-8 bg-gradient-to-r from-emerald-600 to-teal-600 text-white p-6 rounded-xl">
                        <h3 class="text-3xl font-extrabold mb-2">Report Generation System</h3>
                        <p class="text-lg opacity-90 mb-4">Generate, download, and email detailed reports for your hiking platform.</p>
                        
                        <!-- Quick Stats -->
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-6">
                            <div class="bg-white/20 rounded-lg p-3 text-center">
                                <div class="text-2xl font-bold">{{ $stats['total_users'] ?? 0 }}</div>
                                <div class="text-sm opacity-90">Total Users</div>
                            </div>
                            <div class="bg-white/20 rounded-lg p-3 text-center">
                                <div class="text-2xl font-bold">{{ $stats['total_trails'] ?? 0 }}</div>
                                <div class="text-sm opacity-90">Active Trails</div>
                            </div>
                            <div class="bg-white/20 rounded-lg p-3 text-center">
                                <div class="text-2xl font-bold">{{ $stats['total_bookings'] ?? 0 }}</div>
                                <div class="text-sm opacity-90">This Month</div>
                            </div>
                            <div class="bg-white/20 rounded-lg p-3 text-center">
                                <div class="text-2xl font-bold">{{ $stats['avg_rating'] ?? 0 }}</div>
                                <div class="text-sm opacity-90">Avg Rating</div>
                            </div>
                        </div>
                    </div>

                    <form id="reportForm" method="POST" action="{{ route('reports.generate') }}" class="space-y-10">
                        @csrf

                        <!-- Enhanced Report Type Selection with Icons -->
                        <div>
                            <h5 class="text-xl font-semibold text-emerald-800 mb-4 flex items-center">
                                <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                                Report Types
                            </h5>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                                <!-- User & Engagement Reports -->
                                <div class="rounded-2xl border border-blue-200 shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
                                    <div class="bg-gradient-to-r from-blue-500 to-blue-400 text-white px-4 py-3 rounded-t-2xl">
                                        <div class="flex items-center">
                                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                                            </svg>
                                            <h6 class="text-base font-bold">User & Engagement</h6>
                                        </div>
                                    </div>
                                    <div class="p-4 space-y-3 bg-blue-50/50">
                                        <label class="flex items-center space-x-3 p-2 rounded-lg hover:bg-blue-100 cursor-pointer transition">
                                            <input type="radio" name="report_type" value="login_trends" required class="text-blue-600">
                                            <span class="text-sm font-medium">Login Trends Analysis</span>
                                        </label>
                                        <label class="flex items-center space-x-3 p-2 rounded-lg hover:bg-blue-100 cursor-pointer transition">
                                            <input type="radio" name="report_type" value="user_engagement" class="text-blue-600">
                                            <span class="text-sm font-medium">User Engagement Levels</span>
                                        </label>
                                        <label class="flex items-center space-x-3 p-2 rounded-lg hover:bg-blue-100 cursor-pointer transition">
                                            <input type="radio" name="report_type" value="trail_popularity" class="text-blue-600">
                                            <span class="text-sm font-medium">Trail Popularity & Usage</span>
                                        </label>
                                    </div>
                                </div>

                                <!-- Booking & Operations Reports -->
                                <div class="rounded-2xl border border-green-200 shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
                                    <div class="bg-gradient-to-r from-green-500 to-green-400 text-white px-4 py-3 rounded-t-2xl">
                                        <div class="flex items-center">
                                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                            </svg>
                                            <h6 class="text-base font-bold">Booking & Operations</h6>
                                        </div>
                                    </div>
                                    <div class="p-4 space-y-3 bg-green-50/50">
                                        <label class="flex items-center space-x-3 p-2 rounded-lg hover:bg-green-100 cursor-pointer transition">
                                            <input type="radio" name="report_type" value="booking_volumes" class="text-green-600">
                                            <span class="text-sm font-medium">Booking Volumes Analysis</span>
                                        </label>
                                        <label class="flex items-center space-x-3 p-2 rounded-lg hover:bg-green-100 cursor-pointer transition">
                                            <input type="radio" name="report_type" value="emergency_readiness" class="text-green-600">
                                            <span class="text-sm font-medium">Emergency Readiness</span>
                                        </label>
                                    </div>
                                </div>

                                <!-- Feedback & Safety Reports -->
                                <div class="rounded-2xl border border-orange-200 shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
                                    <div class="bg-gradient-to-r from-orange-500 to-orange-400 text-white px-4 py-3 rounded-t-2xl">
                                        <div class="flex items-center">
                                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.464 0L4.268 18.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                            </svg>
                                            <h6 class="text-base font-bold">Feedback & Safety</h6>
                                        </div>
                                    </div>
                                    <div class="p-4 space-y-3 bg-orange-50/50">
                                        <label class="flex items-center space-x-3 p-2 rounded-lg hover:bg-orange-100 cursor-pointer transition">
                                            <input type="radio" name="report_type" value="feedback_summary" class="text-orange-600">
                                            <span class="text-sm font-medium">Feedback Summaries</span>
                                        </label>
                                        <label class="flex items-center space-x-3 p-2 rounded-lg hover:bg-orange-100 cursor-pointer transition">
                                            <input type="radio" name="report_type" value="safety_incidents" class="text-orange-600">
                                            <span class="text-sm font-medium">Safety Incident Reports</span>
                                        </label>
                                    </div>
                                </div>

                                <!-- Community Reports -->
                                <div class="rounded-2xl border border-purple-200 shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
                                    <div class="bg-gradient-to-r from-purple-500 to-purple-400 text-white px-4 py-3 rounded-t-2xl">
                                        <div class="flex items-center">
                                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                            </svg>
                                            <h6 class="text-base font-bold">Community</h6>
                                        </div>
                                    </div>
                                    <div class="p-4 space-y-3 bg-purple-50/50">
                                        <label class="flex items-center space-x-3 p-2 rounded-lg hover:bg-purple-100 cursor-pointer transition">
                                            <input type="radio" name="report_type" value="community_posts" class="text-purple-600">
                                            <span class="text-sm font-medium">Posts & Content Analysis</span>
                                        </label>
                                        <label class="flex items-center space-x-3 p-2 rounded-lg hover:bg-purple-100 cursor-pointer transition">
                                            <input type="radio" name="report_type" value="account_moderation" class="text-purple-600">
                                            <span class="text-sm font-medium">Account Moderation</span>
                                        </label>
                                        <label class="flex items-center space-x-3 p-2 rounded-lg hover:bg-purple-100 cursor-pointer transition">
                                            <input type="radio" name="report_type" value="content_trends" class="text-purple-600">
                                            <span class="text-sm font-medium">Content Trends Analysis</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Enhanced Filters & Customization -->
                        <div class="bg-gray-50 p-6 rounded-xl border">
                            <h5 class="text-xl font-semibold text-emerald-800 mb-4 flex items-center">
                                <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.414A1 1 0 013 6.707V4z"></path>
                                </svg>
                                Filters & Customization
                            </h5>
                            <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                                <div class="space-y-2">
                                    <label class="block text-sm font-medium text-gray-700" for="date_from">
                                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                        Date From
                                    </label>
                                    <input type="date" id="date_from" name="date_from" 
                                           class="w-full rounded-lg border-gray-300 focus:ring-emerald-500 focus:border-emerald-500 transition"/>
                                </div>
                                <div class="space-y-2">
                                    <label class="block text-sm font-medium text-gray-700" for="date_to">
                                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                        Date To
                                    </label>
                                    <input type="date" id="date_to" name="date_to" 
                                           class="w-full rounded-lg border-gray-300 focus:ring-emerald-500 focus:border-emerald-500 transition"/>
                                </div>
                                <div class="space-y-2">
                                    <label class="block text-sm font-medium text-gray-700" for="region_id">
                                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        </svg>
                                        Region
                                    </label>
                                    <select id="region_id" name="region_id" 
                                            class="w-full rounded-lg border-gray-300 focus:ring-emerald-500 focus:border-emerald-500 transition">
                                        <option value="">All Regions</option>
                                        @foreach($regions ?? [] as $region)
                                            <option value="{{ $region->id }}">{{ $region->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="space-y-2">
                                    <label class="block text-sm font-medium text-gray-700" for="trail_id">
                                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path>
                                        </svg>
                                        Trail
                                    </label>
                                    <select id="trail_id" name="trail_id" 
                                            class="w-full rounded-lg border-gray-300 focus:ring-emerald-500 focus:border-emerald-500 transition">
                                        <option value="">All Trails</option>
                                        @foreach($trails ?? [] as $trail)
                                            <option value="{{ $trail->id }}">{{ $trail->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="space-y-2">
                                    <label class="block text-sm font-medium text-gray-700" for="user_type">
                                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                        User Type
                                    </label>
                                    <select id="user_type" name="user_type" 
                                            class="w-full rounded-lg border-gray-300 focus:ring-emerald-500 focus:border-emerald-500 transition">
                                        <option value="">All Users</option>
                                        @foreach($userTypes ?? [] as $key => $type)
                                            <option value="{{ $key }}">{{ $type }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Enhanced Report Output Options -->
                        <div class="bg-gradient-to-r from-indigo-50 to-purple-50 p-6 rounded-xl border border-indigo-200">
                            <h5 class="text-xl font-semibold text-emerald-800 mb-4 flex items-center">
                                <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                Report Output Options
                            </h5>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                <div>
                                    <label class="block text-base font-medium text-gray-700 mb-3">Output Format</label>
                                    <div class="grid grid-cols-2 gap-3">
                                        <label class="flex items-center space-x-3 p-3 rounded-lg border-2 border-gray-200 hover:border-emerald-500 cursor-pointer transition bg-white">
                                            <input type="radio" name="output_format" value="screen" checked class="text-emerald-600">
                                            <div class="flex items-center">
                                                <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                                </svg>
                                                <span class="text-sm font-medium">On-screen</span>
                                            </div>
                                        </label>
                                        <label class="flex items-center space-x-3 p-3 rounded-lg border-2 border-gray-200 hover:border-emerald-500 cursor-pointer transition bg-white">
                                            <input type="radio" name="output_format" value="pdf" class="text-emerald-600">
                                            <div class="flex items-center">
                                                <svg class="w-5 h-5 mr-2 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                </svg>
                                                <span class="text-sm font-medium">PDF</span>
                                            </div>
                                        </label>
                                        <label class="flex items-center space-x-3 p-3 rounded-lg border-2 border-gray-200 hover:border-emerald-500 cursor-pointer transition bg-white">
                                            <input type="radio" name="output_format" value="excel" class="text-emerald-600">
                                            <div class="flex items-center">
                                                <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                </svg>
                                                <span class="text-sm font-medium">Excel</span>
                                            </div>
                                        </label>
                                        <label class="flex items-center space-x-3 p-3 rounded-lg border-2 border-gray-200 hover:border-emerald-500 cursor-pointer transition bg-white">
                                            <input type="radio" name="output_format" value="csv" class="text-emerald-600">
                                            <div class="flex items-center">
                                                <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                </svg>
                                                <span class="text-sm font-medium">CSV</span>
                                            </div>
                                        </label>
                                    </div>
                                </div>
                                <div>
                                    <label for="email_report" class="block text-base font-medium text-gray-700 mb-3">
                                        <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                        </svg>
                                        Email Report To
                                    </label>
                                    <input type="email" id="email_report" name="email_report" 
                                           class="w-full rounded-lg border-gray-300 focus:ring-emerald-500 focus:border-emerald-500 transition" 
                                           placeholder="Enter email address for delivery">
                                    <p class="mt-2 text-sm text-gray-500">Leave blank to skip email delivery</p>
                                </div>
                            </div>
                        </div>

                        <!-- Enhanced Action Buttons -->
                        <div class="flex flex-wrap gap-4 justify-center">
                            <button type="submit" class="inline-flex items-center px-8 py-4 border border-transparent text-lg font-semibold rounded-xl shadow-lg text-white bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-all duration-200 transform hover:scale-105">
                                <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                                Generate Report
                            </button>
                            <button type="reset" class="inline-flex items-center px-8 py-4 border border-gray-300 shadow-lg text-lg font-semibold rounded-xl text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-all duration-200 transform hover:scale-105">
                                <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                </svg>
                                Reset Form
                            </button>
                            <button type="button" id="scheduleReport" class="inline-flex items-center px-8 py-4 border border-indigo-300 shadow-lg text-lg font-semibold rounded-xl text-indigo-700 bg-indigo-50 hover:bg-indigo-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200 transform hover:scale-105">
                                <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Schedule Report
                            </button>
                        </div>
                    </form>

                    <!-- Enhanced Offline Sync Section -->
                    <div class="mt-12">
                        <div class="bg-gradient-to-r from-yellow-50 to-orange-50 border border-yellow-200 rounded-2xl shadow-lg">
                            <div class="bg-gradient-to-r from-yellow-500 to-orange-400 text-white px-6 py-4 rounded-t-2xl">
                                <div class="flex items-center">
                                    <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                    </svg>
                                    <h5 class="text-xl font-semibold">Offline Data Sync</h5>
                                </div>
                                <p class="mt-2 text-sm opacity-90">Upload and process offline log files from remote locations</p>
                            </div>
                            <div class="p-6">
                                <form id="offlineSyncForm" method="POST" action="{{ route('reports.offline-sync') }}" enctype="multipart/form-data">
                                    @csrf
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <div>
                                            <label for="log_file" class="block text-base font-medium text-gray-700 mb-3">
                                                <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                </svg>
                                                Upload Offline Log File
                                            </label>
                                            <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-yellow-500 transition">
                                                <input type="file" id="log_file" name="log_file" accept=".json,.csv,.txt"
                                                       class="hidden" onchange="updateFileName(this)">
                                                <label for="log_file" class="cursor-pointer">
                                                    <svg class="w-12 h-12 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                                    </svg>
                                                    <span class="text-lg font-medium text-gray-700">Click to upload file</span>
                                                    <p class="text-sm text-gray-500 mt-2">or drag and drop</p>
                                                </label>
                                                <p id="fileName" class="text-sm text-green-600 mt-2 hidden"></p>
                                            </div>
                                            <p class="mt-2 text-sm text-gray-500">Supported formats: JSON, CSV, TXT (Max: 10MB)</p>
                                        </div>
                                        <div class="space-y-4">
                                            <div class="bg-white p-4 rounded-lg border">
                                                <h6 class="font-semibold text-gray-700 mb-2">Supported Log Types</h6>
                                                <div class="space-y-2 text-sm">
                                                    <div class="flex items-center text-green-600">
                                                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                                        </svg>
                                                        Login Activities
                                                    </div>
                                                    <div class="flex items-center text-green-600">
                                                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                                        </svg>
                                                        Booking Records
                                                    </div>
                                                    <div class="flex items-center text-green-600">
                                                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                                        </svg>
                                                        Safety Incidents
                                                    </div>
                                                    <div class="flex items-center text-green-600">
                                                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                                        </svg>
                                                        User Feedback
                                                    </div>
                                                </div>
                                            </div>
                                            <button type="submit" class="w-full inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-semibold rounded-xl shadow-sm text-white bg-gradient-to-r from-yellow-600 to-orange-500 hover:from-yellow-700 hover:to-orange-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500 transition-all duration-200">
                                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                                </svg>
                                                Upload & Process Logs
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Enhanced Report Results Section -->
                    <div class="mt-12" id="reportResults" style="display: none;">
                        <div class="bg-white shadow-2xl rounded-2xl border border-emerald-100">
                            <div class="px-6 py-4 border-b border-emerald-100 bg-gradient-to-r from-emerald-50 to-teal-50">
                                <div class="flex items-center justify-between">
                                    <h4 class="text-xl font-semibold text-emerald-900 flex items-center">
                                        <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                        </svg>
                                        Report Results
                                    </h4>
                                    <div class="flex space-x-2">
                                        <button id="exportPDF" class="inline-flex items-center px-3 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-red-600 hover:bg-red-700 transition">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                            PDF
                                        </button>
                                        <button id="exportExcel" class="inline-flex items-center px-3 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-green-600 hover:bg-green-700 transition">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                            Excel
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="p-6" id="reportContent">
                                <!-- Report content will be loaded here -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Enhanced Loading Modal -->
    <div id="loadingModal" class="fixed inset-0 bg-gray-600 bg-opacity-75 flex items-center justify-center z-50" style="display: none;">
        <div class="p-8 border w-96 shadow-2xl rounded-2xl bg-white flex flex-col items-center">
            <div class="flex items-center justify-center h-20 w-20 rounded-full bg-gradient-to-r from-emerald-500 to-teal-500 mb-6">
                <svg class="animate-spin h-10 w-10 text-white" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </div>
            <h3 class="text-xl leading-6 font-bold text-gray-900 mb-2">Generating Report...</h3>
            <p class="text-base text-gray-500 text-center">Please wait while we process your request. This may take a few moments.</p>
            <div class="w-full bg-gray-200 rounded-full h-2 mt-4">
                <div class="bg-gradient-to-r from-emerald-600 to-teal-600 h-2 rounded-full animate-pulse" style="width: 45%"></div>
            </div>
        </div>
    </div>

    <!-- Schedule Modal -->
    <div id="scheduleModal" class="fixed inset-0 bg-gray-600 bg-opacity-75 flex items-center justify-center z-50" style="display: none;">
        <div class="bg-white p-6 rounded-2xl shadow-2xl max-w-md w-full">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Schedule Report</h3>
            <form id="scheduleForm">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Schedule Date & Time</label>
                    <input type="datetime-local" id="schedule_datetime" name="schedule_datetime" 
                           class="w-full rounded-lg border-gray-300 focus:ring-emerald-500 focus:border-emerald-500">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Recipient Email</label>
                    <input type="email" id="schedule_email" name="schedule_email" 
                           class="w-full rounded-lg border-gray-300 focus:ring-emerald-500 focus:border-emerald-500"
                           placeholder="Enter email address" required>
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button" id="cancelSchedule" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700">Schedule</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Enhanced form submission with better error handling
            document.getElementById('reportForm').addEventListener('submit', function(e) {
                e.preventDefault();

                const formData = new FormData(this);
                const emailReport = document.getElementById('email_report').value;

                // Validate form
                const reportType = document.querySelector('input[name="report_type"]:checked');
                if (!reportType) {
                    showAlert('error', 'Please select a report type');
                    return;
                }

                // Show loading modal with animation
                document.getElementById('loadingModal').style.display = 'flex';

                fetch('{{ route("reports.generate") }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    document.getElementById('loadingModal').style.display = 'none';

                    if (data.error) {
                    showAlert('error', data.message || 'Report generation failed.');
                    return;
                    }

                    if (data.download_url) {
                        window.open(data.download_url, '_blank');
                        showAlert('success', 'Report generated successfully!');
                    } else {
                        displayReport(data);
                        showAlert('success', 'Report generated successfully!');
                    }

                    if (emailReport) {
                        sendReportEmail(emailReport, data);
                    }
                })
                .catch(error => {
                    document.getElementById('loadingModal').style.display = 'none';
                    showAlert('error', 'Error generating report: ' + error.message);
                });
            });

            // Enhanced offline sync with progress
            document.getElementById('offlineSyncForm').addEventListener('submit', function(e) {
                e.preventDefault();

                const fileInput = document.getElementById('log_file');
                if (!fileInput.files[0]) {
                    showAlert('error', 'Please select a file to upload');
                    return;
                }

                const formData = new FormData(this);
                showAlert('info', 'Processing file...');

                fetch('{{ route("reports.offline-sync") }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        showAlert('error', data.error);
                        return;
                    }
                    
                    showAlert('success', `Sync completed! Processed ${data.processed_records || 0} records.`);
                    document.getElementById('offlineSyncForm').reset();
                    document.getElementById('fileName').classList.add('hidden');
                })
                .catch(error => {
                    showAlert('error', 'Error processing sync: ' + error.message);
                });
            });

            // Schedule report functionality
            document.getElementById('scheduleReport').addEventListener('click', function() {
                const reportType = document.querySelector('input[name="report_type"]:checked');
                if (!reportType) {
                    showAlert('error', 'Please select a report type first');
                    return;
                }
                document.getElementById('scheduleModal').style.display = 'flex';
            });

            document.getElementById('cancelSchedule').addEventListener('click', function() {
                document.getElementById('scheduleModal').style.display = 'none';
            });

            // Enhanced display function with better formatting
            function displayReport(data) {
                let html = `
                    <div class="mb-6 p-4 bg-gradient-to-r from-emerald-50 to-teal-50 rounded-lg border border-emerald-200">
                        <h5 class="text-2xl font-bold text-emerald-800 mb-2">${data.title}</h5>
                        <p class="text-emerald-700">${data.period || 'Generated on ' + new Date().toLocaleString()}</p>
                    </div>
                `;

                if (data.summary) {
                    html += `
                        <div class="mb-8 bg-gray-50 p-6 rounded-lg">
                            <h6 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                                Summary Statistics
                            </h6>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    `;

                    Object.entries(data.summary).forEach(([key, value]) => {
                        let displayValue = value;
                        if (typeof value === 'object') {
                            displayValue = JSON.stringify(value);
                        }
                        
                        html += `
                            <div class="bg-white p-4 rounded-lg shadow-sm border">
                                <div class="text-2xl font-bold text-emerald-600">${displayValue}</div>
                                <div class="text-sm text-gray-600 uppercase tracking-wide">${key.replace(/_/g, ' ')}</div>
                            </div>
                        `;
                    });

                    html += '</div></div>';
                }

                if (data.data && data.data.length > 0) {
                    html += `
                        <div class="mb-6">
                            <h6 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5a2 2 0 012-2h2a2 2 0 012 2v4H8V5z"></path>
                                </svg>
                                Detailed Data
                            </h6>
                            <div class="overflow-x-auto bg-white rounded-lg shadow">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                    `;

                    const headers = Object.keys(data.data[0]);
                    headers.forEach(header => {
                        html += `<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">${header.replace(/_/g, ' ')}</th>`;
                    });

                    html += `
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                    `;

                    data.data.slice(0, 50).forEach((row, index) => {
                        html += `<tr class="${index % 2 === 0 ? 'bg-white' : 'bg-gray-50'}">`;
                        headers.forEach(header => {
                            let cellValue = row[header] || '';
                            if (typeof cellValue === 'object') {
                                cellValue = JSON.stringify(cellValue);
                            }
                            html += `<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${cellValue}</td>`;
                        });
                        html += '</tr>';
                    });

                    html += `
                                    </tbody>
                                </table>
                            </div>
                    `;

                    if (data.data.length > 50) {
                        html += `<p class="text-sm text-gray-500 mt-4 text-center">Showing first 50 records of ${data.data.length} total records.</p>`;
                    }

                    html += '</div>';
                } else {
                    html += `
                        <div class="text-center py-12">
                            <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <p class="text-gray-500 text-lg">No data available for the selected criteria</p>
                        </div>
                    `;
                }

                document.getElementById('reportContent').innerHTML = html;
                document.getElementById('reportResults').style.display = 'block';
                document.getElementById('reportResults').scrollIntoView({ behavior: 'smooth' });
            }

            function sendReportEmail(email, reportData) {
                fetch('{{ route("reports.email") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        email: email,
                        report_data: reportData,
                        report_type: document.querySelector('input[name="report_type"]:checked')?.value
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        showAlert('error', 'Error sending email: ' + data.error);
                    } else {
                        showAlert('success', 'Report emailed successfully to ' + email);
                    }
                })
                .catch(error => {
                    showAlert('error', 'Error sending email: ' + error.message);
                });
            }

            function showAlert(type, message) {
                const alertColors = {
                    'success': 'bg-green-100 border-green-500 text-green-700',
                    'error': 'bg-red-100 border-red-500 text-red-700',
                    'info': 'bg-blue-100 border-blue-500 text-blue-700'
                };

                const alert = document.createElement('div');
                alert.className = `fixed top-4 right-4 z-50 max-w-sm w-full ${alertColors[type]} border-l-4 p-4 rounded-lg shadow-lg transform transition-all duration-300 translate-x-full`;
                alert.innerHTML = `
                    <div class="flex items-center">
                        <div class="flex-grow">
                            <p class="text-sm font-medium">${message}</p>
                        </div>
                        <div class="flex-shrink-0 ml-4">
                            <button class="text-gray-400 hover:text-gray-600 transition" onclick="this.parentElement.parentElement.parentElement.remove()">
                                <span class="sr-only">Close</span>
                                <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                `;
                
                document.body.appendChild(alert);
                
                // Animate in
                setTimeout(() => {
                    alert.classList.remove('translate-x-full');
                }, 100);
                
                // Auto remove after 5 seconds
                setTimeout(() => {
                    alert.classList.add('translate-x-full');
                    setTimeout(() => {
                        if (alert.parentNode) {
                            alert.parentNode.removeChild(alert);
                        }
                    }, 300);
                }, 5000);
            }

            // File name display for file upload
            window.updateFileName = function(input) {
                const fileName = document.getElementById('fileName');
                if (input.files[0]) {
                    fileName.textContent = input.files[0].name;
                    fileName.classList.remove('hidden');
                } else {
                    fileName.classList.add('hidden');
                }
            };

            // Set default dates (last 30 days)
            const today = new Date();
            const thirtyDaysAgo = new Date();
            thirtyDaysAgo.setDate(today.getDate() - 30);

            document.getElementById('date_from').value = thirtyDaysAgo.toISOString().split('T')[0];
            document.getElementById('date_to').value = today.toISOString().split('T')[0];

            // Export functionality
            document.getElementById('exportPDF').addEventListener('click', function() {
                exportReport('pdf');
            });

            document.getElementById('exportExcel').addEventListener('click', function() {
                exportReport('excel');
            });

            function exportReport(format) {
                const reportType = document.querySelector('input[name="report_type"]:checked');
                if (!reportType) {
                    showAlert('error', 'Please generate a report first');
                    return;
                }

                const formData = new FormData(document.getElementById('reportForm'));
                formData.set('output_format', format);

                showAlert('info', `Generating ${format.toUpperCase()} export...`);

                fetch('{{ route("reports.generate") }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.download_url) {
                        window.open(data.download_url, '_blank');
                        showAlert('success', `${format.toUpperCase()} export ready for download!`);
                    } else {
                        showAlert('error', 'Failed to generate export');
                    }
                })
                .catch(error => {
                    showAlert('error', 'Export failed: ' + error.message);
                });
            }
        });
    </script>
</x-app-layout>
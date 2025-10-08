{{-- resources/views/reports/index.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <div class="space-y-4">
            <x-trail-breadcrumb />
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div>
                    <h2 class="font-semibold text-2xl text-gray-800 leading-tight">
                        {{ __('Report Generation System') }}
                    </h2>
                    <p class="text-sm text-gray-600 mt-1">
                        @if(auth()->user()->user_type === 'organization')
                            Generate operational reports for your trails
                        @else
                            Generate comprehensive reports for the hiking platform
                        @endif
                    </p>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-12 bg-gradient-to-br from-emerald-50 via-white to-teal-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white/90 shadow-2xl rounded-2xl border border-emerald-100 overflow-hidden">
                <div class="p-8 text-gray-900">
                    <!-- Header with Statistics -->
                    <div class="mb-8 bg-gradient-to-r from-emerald-600 to-teal-600 text-white p-6 rounded-xl">
                        <h3 class="text-3xl font-extrabold mb-2">Report Generation System</h3>
                        <p class="text-lg opacity-90 mb-4">
                            @if(auth()->user()->user_type === 'organization')
                                Generate operational reports for your trails
                            @else
                                Generate comprehensive reports for the hiking platform
                            @endif
                        </p>
                        
                        <!-- Quick Stats -->
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-4 mt-6">
                            <div class="bg-white/20 rounded-lg p-3 text-center">
                                <div class="text-2xl font-bold">{{ $stats['total_trails'] ?? 0 }}</div>
                                <div class="text-sm opacity-90">
                                    @if(auth()->user()->user_type === 'organization')
                                        Your Trails
                                    @else
                                        Active Trails
                                    @endif
                                </div>
                            </div>
                            <div class="bg-white/20 rounded-lg p-3 text-center">
                                <div class="text-2xl font-bold">{{ $stats['total_bookings'] ?? 0 }}</div>
                                <div class="text-sm opacity-90">Bookings This Month</div>
                            </div>
                            <div class="bg-white/20 rounded-lg p-3 text-center">
                                <div class="text-2xl font-bold">{{ $stats['avg_rating'] ?? 0 }}</div>
                                <div class="text-sm opacity-90">Average Rating</div>
                            </div>
                        </div>
                    </div>

                    <form id="reportForm" method="POST" action="{{ route('reports.generate') }}" class="space-y-10">
                        @csrf

                        <!-- Report Type Selection -->
                        <div>
                            <h5 class="text-xl font-semibold text-emerald-800 mb-4 flex items-center">
                                <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                                Select Report Type
                            </h5>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                
                                <!-- Overall Transactions Report -->
                                <div class="rounded-2xl border border-indigo-200 shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
                                    <div class="bg-gradient-to-r from-indigo-600 to-indigo-500 text-white px-4 py-3 rounded-t-2xl">
                                        <div class="flex items-center">
                                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                            </svg>
                                            <h6 class="text-base font-bold">Overall Transactions</h6>
                                        </div>
                                    </div>
                                    <div class="p-4 bg-indigo-50/50">
                                        <label class="flex items-center space-x-3 p-3 rounded-lg hover:bg-indigo-100 cursor-pointer transition">
                                            <input type="radio" name="report_type" value="overall_transactions" required class="text-indigo-600">
                                            <span class="text-sm font-medium">Complete Transaction Overview</span>
                                        </label>
                                        <p class="text-xs text-gray-600 mt-2 px-3">Comprehensive view of all transactions and financial summary</p>
                                    </div>
                                </div>

                                <!-- Booking Volumes Report -->
                                <div class="rounded-2xl border border-green-200 shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
                                    <div class="bg-gradient-to-r from-green-500 to-green-400 text-white px-4 py-3 rounded-t-2xl">
                                        <div class="flex items-center">
                                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                            </svg>
                                            <h6 class="text-base font-bold">Booking Operations</h6>
                                        </div>
                                    </div>
                                    <div class="p-4 bg-green-50/50">
                                        <label class="flex items-center space-x-3 p-3 rounded-lg hover:bg-green-100 cursor-pointer transition">
                                            <input type="radio" name="report_type" value="booking_volumes" required class="text-green-600">
                                            <span class="text-sm font-medium">Booking Volumes & Revenue</span>
                                        </label>
                                        <p class="text-xs text-gray-600 mt-2 px-3">Track reservations, revenue, and booking trends</p>
                                    </div>
                                </div>

                                <!-- Trail Popularity Report -->
                                <div class="rounded-2xl border border-blue-200 shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
                                    <div class="bg-gradient-to-r from-blue-500 to-blue-400 text-white px-4 py-3 rounded-t-2xl">
                                        <div class="flex items-center">
                                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path>
                                            </svg>
                                            <h6 class="text-base font-bold">Trail Analytics</h6>
                                        </div>
                                    </div>
                                    <div class="p-4 bg-blue-50/50">
                                        <label class="flex items-center space-x-3 p-3 rounded-lg hover:bg-blue-100 cursor-pointer transition">
                                            <input type="radio" name="report_type" value="trail_popularity" class="text-blue-600">
                                            <span class="text-sm font-medium">Trail Popularity & Usage</span>
                                        </label>
                                        <p class="text-xs text-gray-600 mt-2 px-3">Understand visitor patterns and trail performance</p>
                                    </div>
                                </div>

                                <!-- Emergency Readiness Report -->
                                <div class="rounded-2xl border border-red-200 shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
                                    <div class="bg-gradient-to-r from-red-500 to-red-400 text-white px-4 py-3 rounded-t-2xl">
                                        <div class="flex items-center">
                                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                                            </svg>
                                            <h6 class="text-base font-bold">Safety & Readiness</h6>
                                        </div>
                                    </div>
                                    <div class="p-4 bg-red-50/50">
                                        <label class="flex items-center space-x-3 p-3 rounded-lg hover:bg-red-100 cursor-pointer transition">
                                            <input type="radio" name="report_type" value="emergency_readiness" class="text-red-600">
                                            <span class="text-sm font-medium">Emergency Readiness Status</span>
                                        </label>
                                        <p class="text-xs text-gray-600 mt-2 px-3">Monitor safety equipment and preparedness</p>
                                    </div>
                                </div>

                                <!-- Safety Incidents Report -->
                                <div class="rounded-2xl border border-orange-200 shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
                                    <div class="bg-gradient-to-r from-orange-500 to-orange-400 text-white px-4 py-3 rounded-t-2xl">
                                        <div class="flex items-center">
                                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.464 0L4.268 18.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                            </svg>
                                            <h6 class="text-base font-bold">Incident Tracking</h6>
                                        </div>
                                    </div>
                                    <div class="p-4 bg-orange-50/50">
                                        <label class="flex items-center space-x-3 p-3 rounded-lg hover:bg-orange-100 cursor-pointer transition">
                                            <input type="radio" name="report_type" value="safety_incidents" class="text-orange-600">
                                            <span class="text-sm font-medium">Safety Incident Reports</span>
                                        </label>
                                        <p class="text-xs text-gray-600 mt-2 px-3">Track and manage trail safety incidents</p>
                                    </div>
                                </div>

                                <!-- Feedback Summary Report -->
                                <div class="rounded-2xl border border-purple-200 shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
                                    <div class="bg-gradient-to-r from-purple-500 to-purple-400 text-white px-4 py-3 rounded-t-2xl">
                                        <div class="flex items-center">
                                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                                            </svg>
                                            <h6 class="text-base font-bold">Customer Feedback</h6>
                                        </div>
                                    </div>
                                    <div class="p-4 bg-purple-50/50">
                                        <label class="flex items-center space-x-3 p-3 rounded-lg hover:bg-purple-100 cursor-pointer transition">
                                            <input type="radio" name="report_type" value="feedback_summary" class="text-purple-600">
                                            <span class="text-sm font-medium">Feedback & Ratings Summary</span>
                                        </label>
                                        <p class="text-xs text-gray-600 mt-2 px-3">Anonymous customer feedback and sentiment</p>
                                    </div>
                                </div>

                            </div>

                            @if(auth()->user()->user_type === 'organization')
                            <div class="mt-4 p-4 bg-blue-50 border-l-4 border-blue-500 text-blue-700 rounded">
                                <p class="text-sm font-medium flex items-center">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    Privacy Notice: All reports are anonymized and show only data for your trails. Customer personal information is protected.
                                </p>
                            </div>
                            @endif
                        </div>

                        <!-- Filters & Customization -->
                        <div class="bg-gray-50 p-6 rounded-xl border">
                            <h5 class="text-xl font-semibold text-emerald-800 mb-4 flex items-center">
                                <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.414A1 1 0 013 6.707V4z"></path>
                                </svg>
                                Filters & Date Range
                            </h5>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div class="space-y-2">
                                    <label class="block text-sm font-medium text-gray-700" for="date_from">
                                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                        Date From
                                    </label>
                                    <input type="date" id="date_from" name="date_from" 
                                           value="{{ now()->subDays(30)->format('Y-m-d') }}"
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
                                           value="{{ now()->format('Y-m-d') }}"
                                           class="w-full rounded-lg border-gray-300 focus:ring-emerald-500 focus:border-emerald-500 transition"/>
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
                                        <option value="">All Your Trails</option>
                                        @foreach($trails ?? [] as $trail)
                                            <option value="{{ $trail->id }}">{{ $trail->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Report Output -->
                        <div class="bg-gradient-to-r from-indigo-50 to-purple-50 p-6 rounded-xl border border-indigo-200">
                            <h5 class="text-xl font-semibold text-emerald-800 mb-4 flex items-center">
                                <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                </svg>
                                Report Output Format
                            </h5>
                            <div class="space-y-3">
                                <!-- Screen View Option -->
                                <label class="flex items-center space-x-3 p-3 rounded-lg border-2 border-emerald-200 bg-white hover:bg-emerald-50 cursor-pointer transition">
                                    <input type="radio" name="output_format" value="screen" checked class="text-emerald-600">
                                    <div class="flex items-center">
                                        <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                        </svg>
                                        <span class="text-sm font-medium">View On-screen</span>
                                    </div>
                                </label>
                                
                                <!-- PDF Export Option -->
                                <label class="flex items-center space-x-3 p-3 rounded-lg border-2 border-red-200 bg-white hover:bg-red-50 cursor-pointer transition">
                                    <input type="radio" name="output_format" value="pdf" class="text-red-600">
                                    <div class="flex items-center">
                                        <svg class="w-5 h-5 mr-2 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                        </svg>
                                        <span class="text-sm font-medium">Export as PDF</span>
                                    </div>
                                </label>
                                
                                <!-- Excel/CSV Export Option -->
                                <label class="flex items-center space-x-3 p-3 rounded-lg border-2 border-green-200 bg-white hover:bg-green-50 cursor-pointer transition">
                                    <input type="radio" name="output_format" value="csv" class="text-green-600">
                                    <div class="flex items-center">
                                        <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                        <span class="text-sm font-medium">Export as Excel (CSV)</span>
                                    </div>
                                </label>
                                
                                <p class="mt-2 text-xs text-gray-500 italic">
                                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    PDF format is best for printing, CSV format works with Excel and Google Sheets
                                </p>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex flex-wrap gap-4 justify-center">
                            <button type="submit" class="inline-flex items-center px-8 py-4 border border-transparent text-lg font-semibold rounded-xl shadow-lg text-white bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-all duration-200 transform hover:scale-105">
                                <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                                Generate Report
                            </button>
                            <button type="reset" class="inline-flex items-center px-8 py-4 border border-gray-300 shadow-lg text-lg font-semibold rounded-xl text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-all duration-200">
                                <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                </svg>
                                Reset Form
                            </button>
                        </div>
                    </form>

                    <!-- Report Results Container -->
                    <div id="reportResults" class="hidden mt-10"></div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.getElementById('reportForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<svg class="animate-spin h-5 w-5 mr-3 inline" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Generating...';
            
            const formData = new FormData(this);
            const outputFormat = formData.get('output_format');
            
            try {
                // For PDF and CSV, download the file directly
                if (outputFormat === 'pdf' || outputFormat === 'csv') {
                    const response = await fetch('{{ route("reports.generate") }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: formData
                    });
                    
                    if (!response.ok) {
                        const errorData = await response.json();
                        alert('Error: ' + (errorData.message || 'Failed to generate report'));
                        return;
                    }
                    
                    // Get filename from Content-Disposition header
                    const contentDisposition = response.headers.get('Content-Disposition');
                    let filename = 'report.' + outputFormat;
                    if (contentDisposition) {
                        const filenameMatch = contentDisposition.match(/filename="?(.+)"?/);
                        if (filenameMatch) {
                            filename = filenameMatch[1];
                        }
                    }
                    
                    // Create blob and download
                    const blob = await response.blob();
                    const url = window.URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.style.display = 'none';
                    a.href = url;
                    a.download = filename;
                    document.body.appendChild(a);
                    a.click();
                    window.URL.revokeObjectURL(url);
                    document.body.removeChild(a);
                    
                    // Show success message
                    alert('Report downloaded successfully!');
                    
                } else {
                    // For screen output, display the JSON response
                    const response = await fetch('{{ route("reports.generate") }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: formData
                    });
                    
                    const data = await response.json();
                    
                    if (data.error) {
                        alert('Error: ' + data.message);
                    } else {
                        displayReport(data);
                    }
                }
            } catch (error) {
                alert('Failed to generate report: ' + error.message);
            } finally {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            }
        });

        function displayReport(data) {
            const container = document.getElementById('reportResults');
            container.classList.remove('hidden');
            
            let html = `
                <div class="bg-white rounded-xl shadow-lg p-8 border border-emerald-200">
                    <div class="border-b-2 border-emerald-500 pb-4 mb-6">
                        <h3 class="text-2xl font-bold text-emerald-800">${data.title}</h3>
                        <p class="text-gray-600">Period: ${data.period}</p>
                    </div>
                    
                    <!-- Summary Statistics -->
                    <div class="mb-8">
                        <h4 class="text-xl font-semibold text-gray-800 mb-4">Summary</h4>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            `;
            
            // Display summary
            for (const [key, value] of Object.entries(data.summary)) {
                if (typeof value === 'object') continue;
                const label = key.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
                html += `
                    <div class="bg-gradient-to-br from-emerald-50 to-teal-50 p-4 rounded-lg border border-emerald-200">
                        <div class="text-sm text-gray-600 mb-1">${label}</div>
                        <div class="text-2xl font-bold text-emerald-700">${value}</div>
                    </div>
                `;
            }
            
            html += `
                        </div>
                    </div>
                    
                    <!-- Detailed Data Table -->
                    <div class="overflow-x-auto">
                        <h4 class="text-xl font-semibold text-gray-800 mb-4">Detailed Data</h4>
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-emerald-100">
                                <tr>
            `;
            
            // Table headers
            if (data.data && data.data.length > 0) {
                for (const key of Object.keys(data.data[0])) {
                    const label = key.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
                    html += `<th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">${label}</th>`;
                }
                
                html += `</tr></thead><tbody class="bg-white divide-y divide-gray-200">`;
                
                // Table rows
                for (const row of data.data) {
                    html += '<tr class="hover:bg-emerald-50">';
                    for (const value of Object.values(row)) {
                        html += `<td class="px-4 py-3 text-sm text-gray-700">${value ?? 'N/A'}</td>`;
                    }
                    html += '</tr>';
                }
            } else {
                html += `<tr><td colspan="100%" class="px-4 py-8 text-center text-gray-500">No data available for the selected period</td></tr>`;
            }
            
            html += `
                            </tbody>
                        </table>
                    </div>
                </div>
            `;
            
            container.innerHTML = html;
            container.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    </script>
    @endpush
</x-app-layout>

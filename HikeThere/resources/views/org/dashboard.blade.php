<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Organization Dashboard') }}
        </h2>
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
                            <p class="text-white/90">Your organization account has been approved and is now active.</p>
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
                                <p class="text-2xl font-semibold text-gray-900">0</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-8 w-8 text-[#20b6d2]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500">Total Hikers</p>
                                <p class="text-2xl font-semibold text-gray-900">0</p>
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
                                <p class="text-2xl font-semibold text-green-600">Approved</p>
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
                        <a href="#" class="group relative bg-white p-6 focus-within:ring-2 focus-within:ring-inset focus-within:ring-[#336d66] rounded-lg border border-gray-200 hover:border-[#336d66] transition-colors">
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

                        <a href="#" class="group relative bg-white p-6 focus-within:ring-2 focus-within:ring-inset focus-within:ring-[#336d66] rounded-lg border border-gray-200 hover:border-[#336d66] transition-colors">
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

                        <a href="#" class="group relative bg-white p-6 focus-within:ring-2 focus-within:ring-inset focus-within:ring-[#336d66] rounded-lg border border-gray-200 hover:border-[#336d66] transition-colors">
                            <div>
                                <span class="rounded-lg inline-flex p-3 bg-green-500/10 text-green-500 ring-4 ring-white">
                                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                    </svg>
                                </span>
                            </div>
                            <div class="mt-4">
                                <h3 class="text-sm font-medium text-gray-900">
                                    <span class="absolute inset-0" aria-hidden="true"></span>
                                    View Reports
                                </h3>
                                <p class="mt-2 text-sm text-gray-500">Access analytics and reports about your trails.</p>
                            </div>
                        </a>

                        <a href="#" class="group relative bg-white p-6 focus-within:ring-2 focus-within:ring-inset focus-within:ring-[#336d66] rounded-lg border border-gray-200 hover:border-[#336d66] transition-colors">
                            <div>
                                <span class="rounded-lg inline-flex p-3 bg-purple-500/10 text-purple-500 ring-4 ring-white">
                                    <svg class="h-6 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                    </svg>
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
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Recent Activity</h3>
                    <div class="border-t border-gray-200">
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
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
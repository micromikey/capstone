<x-guest-layout>
    <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-[#336d66]/5 to-white py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8 bg-white/80 backdrop-blur-lg p-8 rounded-2xl shadow-xl">
            <div class="text-center">
                <a href="/" class="flex items-center justify-center space-x-3 mb-8">
                    <img src="{{ asset('img/icon1.png') }}" alt="HikeThere Logo" class="h-12 w-auto">
                    <span class="font-bold text-2xl text-[#336d66]">HikeThere</span>
                </a>
                <h2 class="text-3xl font-extrabold text-gray-900 mb-2">Choose Account Type</h2>
                <p class="text-gray-600">Select how you want to join HikeThere</p>
            </div>

            <div class="mt-8 space-y-4">
                <!-- Hiker Registration Card -->
                <a href="{{ route('register') }}" 
                   class="block w-full p-6 border border-gray-200 rounded-2xl hover:border-[#336d66] hover:bg-[#336d66]/5 transition-all duration-300">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <span class="inline-flex items-center justify-center h-12 w-12 rounded-xl bg-[#336d66]/10">
                                <svg class="h-6 w-6 text-[#336d66]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </span>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-medium text-gray-900">Join as a Hiker</h3>
                            <p class="mt-1 text-sm text-gray-500">
                                Create a personal account to explore trails and connect with other hikers
                            </p>
                        </div>
                        <div class="ml-auto">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </div>
                    </div>
                </a>

                <!-- Organization Registration Card -->
                <a href="{{ route('register.organization') }}" 
                   class="block w-full p-6 border border-gray-200 rounded-2xl hover:border-[#336d66] hover:bg-[#336d66]/5 transition-all duration-300">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <span class="inline-flex items-center justify-center h-12 w-12 rounded-xl bg-[#336d66]/10">
                                <svg class="h-6 w-6 text-[#336d66]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                            </span>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-medium text-gray-900">Register as an Organization</h3>
                            <p class="mt-1 text-sm text-gray-500">
                                Create a business account to manage trails and organize hiking events
                            </p>
                        </div>
                        <div class="ml-auto">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </div>
                    </div>
                </a>
            </div>

            <div class="text-center text-sm text-gray-600 mt-8">
                Already have an account? 
                <a href="{{ route('login') }}" class="font-medium text-[#20b6d2] hover:text-[#336d66] transition-colors">
                    Sign in
                </a>
            </div>
        </div>
    </div>
</x-guest-layout>
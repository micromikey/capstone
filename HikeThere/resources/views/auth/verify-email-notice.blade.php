<x-guest-layout>
    <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-[#336d66]/5 to-white py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8 bg-white/80 backdrop-blur-lg p-8 rounded-2xl shadow-xl">
            <div class="text-center">
                <a href="/" class="flex items-center justify-center space-x-3 mb-8">
                    <img src="{{ asset('img/icon1.png') }}" alt="HikeThere Logo" class="h-12 w-auto">
                    <span class="font-bold text-2xl text-[#336d66]">HikeThere</span>
                </a>
                <h2 class="text-3xl font-extrabold text-gray-900 mb-2">Check Your Email</h2>
                <p class="text-gray-600">Verify your account to get started</p>
            </div>

            <!-- Success Message -->
            @if (session('success'))
                <div class="mb-6 bg-green-50 border border-green-200 text-green-700 p-4 rounded-xl text-sm font-medium">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Main Message -->
            <div class="bg-blue-50 border border-blue-200 p-6 rounded-xl">
                <div class="flex items-start space-x-3">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-lg font-medium text-blue-900 mb-2">Verification Email Sent</h3>
                        <p class="text-blue-700 text-sm leading-relaxed">
                            We've sent a verification link to your email address. Please check your inbox and click the verification link to activate your account.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Instructions -->
            <div class="bg-gray-50 border border-gray-200 p-4 rounded-xl">
                <h4 class="font-medium text-gray-900 mb-2">What to do next:</h4>
                <ol class="list-decimal list-inside text-sm text-gray-700 space-y-1">
                    <li>Check your email inbox (and spam folder)</li>
                    <li>Click the verification link in the email</li>
                    <li>Return here to sign in to your account</li>
                </ol>
            </div>

            <!-- Action Buttons -->
            <div class="space-y-4">
                <!-- Sign In Button -->
                <a href="{{ route('login') }}" 
                   class="w-full flex justify-center items-center py-3 px-4 border border-transparent rounded-xl text-white bg-[#336d66] hover:bg-[#20b6d2] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#20b6d2] transition-colors duration-300 font-medium">
                    <svg class="h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                    </svg>
                    {{ __('Sign In') }}
                </a>

                <!-- Back to Home -->
                <a href="/" 
                   class="w-full flex justify-center items-center py-2 px-4 border border-gray-300 rounded-xl text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#20b6d2] transition-colors duration-300">
                    <svg class="h-4 w-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    {{ __('Back to Home') }}
                </a>
            </div>

            <!-- Help Text -->
            <div class="text-center text-sm text-gray-500 pt-4">
                <p>Didn't receive the email? Check your spam folder or</p>
                <a href="{{ route('login') }}" class="font-medium text-[#20b6d2] hover:text-[#336d66] transition-colors">
                    contact support
                </a>
            </div>
        </div>
    </div>
</x-guest-layout>

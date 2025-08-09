<x-guest-layout>
    <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-[#336d66]/5 to-white py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-lg w-full space-y-6 bg-white/80 backdrop-blur-lg p-10 rounded-2xl shadow-xl text-center">
            <!-- Logo and Header -->
            <div class="space-y-4">
                <img src="{{ asset('img/icon1.png') }}" alt="HikeThere Logo" class="h-16 w-auto mx-auto">
                <h2 class="text-3xl font-extrabold text-gray-900">Registration Pending</h2>
            </div>

            <!-- Alert Box -->
            <div class="bg-yellow-50 border border-yellow-200 p-6 rounded-xl">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-yellow-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-4 text-left">
                        <h3 class="text-lg font-semibold text-yellow-800 mb-2">
                            Approval Required
                        </h3>
                        <p class="text-sm text-yellow-700 leading-relaxed">
                            Your organization registration is pending approval from our admin team. We'll review your documents and get back to you soon.
                        </p>
                    </div>
                </div>
            </div>

            <!-- What happens next section -->
            <div class="space-y-6">
                <div class="pt-4">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">
                        What happens next?
                    </h3>
                </div>

                <!-- Steps list -->
                <div class="space-y-4">
                    <div class="flex items-start text-left">
                        <div class="flex-shrink-0 mt-1">
                            <svg class="h-5 w-5 text-[#336d66]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <p class="ml-3 text-sm text-gray-600 leading-relaxed">
                            Our team will review your submitted documents
                        </p>
                    </div>

                    <div class="flex items-start text-left">
                        <div class="flex-shrink-0 mt-1">
                            <svg class="h-5 w-5 text-[#336d66]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <p class="ml-3 text-sm text-gray-600 leading-relaxed">
                            You'll receive an email notification about the approval status
                        </p>
                    </div>

                    <div class="flex items-start text-left">
                        <div class="flex-shrink-0 mt-1">
                            <svg class="h-5 w-5 text-[#336d66]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <p class="ml-3 text-sm text-gray-600 leading-relaxed">
                            Once approved, you can log in to your account
                        </p>
                    </div>
                </div>
            </div>

            <!-- Additional info box -->
            <div class="bg-gray-50 border border-gray-200 p-4 rounded-lg">
                <p class="text-xs text-gray-500 flex items-center justify-center">
                    <svg class="h-4 w-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Review process typically takes 1-2 business days
                </p>
            </div>

            <!-- Back to login link -->
            <div class="pt-6 border-t border-gray-100">
                <a href="{{ route('login') }}" class="inline-flex items-center text-sm font-medium text-[#20b6d2] hover:text-[#336d66] transition-colors">
                    <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Back to login
                </a>
            </div>
        </div>
    </div>
</x-guest-layout>
<x-guest-layout>
    <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-[#336d66]/5 to-white py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8 bg-white/80 backdrop-blur-lg p-8 rounded-2xl shadow-xl">
            <div class="text-center">
                <a href="/" class="flex items-center justify-center space-x-3 mb-8">
                    <img src="{{ asset('img/icon1.png') }}" alt="HikeThere Logo" class="h-12 w-auto">
                    <span class="font-bold text-2xl text-[#336d66]">HikeThere</span>
                </a>
                <h2 class="text-3xl font-extrabold text-gray-900 mb-2">Create Hiker Account</h2>
                <p class="text-gray-600">Start your hiking journey today</p>
            </div>

            <x-validation-errors class="mb-4 bg-red-50 text-red-500 p-4 rounded-lg text-sm" />

            <form method="POST" action="{{ route('register') }}" class="mt-8 space-y-6">
                @csrf
                <input type="hidden" name="user_type" value="hiker">

                <div class="space-y-4">
                    <div>
                        <x-label for="name" value="{{ __('Name') }}" class="text-gray-700 font-medium" />
                        <div class="mt-1 relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </div>
                            <x-input id="name" class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-xl focus:ring-[#20b6d2] focus:border-[#20b6d2] transition duration-150" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" placeholder="Enter your full name" />
                        </div>
                    </div>

                    <div>
                        <x-label for="email" value="{{ __('Email') }}" class="text-gray-700 font-medium" />
                        <div class="mt-1 relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
                                </svg>
                            </div>
                            <x-input id="email" class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-xl focus:ring-[#20b6d2] focus:border-[#20b6d2] transition duration-150" type="email" name="email" :value="old('email')" required autocomplete="username" placeholder="Enter your email" />
                        </div>
                    </div>

                    <div>
                        <x-label for="password" value="{{ __('Password') }}" class="text-gray-700 font-medium" />
                        <div class="mt-1 relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                            </div>
                            <x-input id="password" class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-xl focus:ring-[#20b6d2] focus:border-[#20b6d2] transition duration-150" type="password" name="password" required autocomplete="new-password" placeholder="Create a password" />
                        </div>
                    </div>

                    <div>
                        <x-label for="password_confirmation" value="{{ __('Confirm Password') }}" class="text-gray-700 font-medium" />
                        <div class="mt-1 relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                            </div>
                            <x-input id="password_confirmation" class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-xl focus:ring-[#20b6d2] focus:border-[#20b6d2] transition duration-150" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="Confirm your password" />
                        </div>
                    </div>

                    <!-- Terms and Privacy Policy Section -->
                    <div class="space-y-4 pt-4 border-t border-gray-200">
                        <h4 class="font-medium text-gray-700">Terms & Agreements</h4>

                        <div class="bg-gray-50 p-4 rounded-xl space-y-4">
                            <!-- Terms Checkbox -->
                            <div class="flex items-start space-x-3">
                                <div class="flex items-center h-5">
                                    <input type="checkbox"
                                        id="terms"
                                        name="terms"
                                        required
                                        class="h-4 w-4 rounded border-gray-300 text-[#336d66] focus:ring-[#336d66]">
                                </div>
                                <div class="text-sm">
                                    <label for="terms" class="font-medium text-gray-700">
                                        I accept the terms of service and privacy policy
                                    </label>
                                    <p class="text-gray-500">
                                        By checking this box, you agree to our
                                        <a href="{{ route('terms') }}"
                                            target="_blank"
                                            class="text-[#20b6d2] hover:text-[#336d66] transition-colors">
                                            Terms of Service
                                        </a>
                                        and
                                        <a href="{{ route('privacy') }}"
                                            target="_blank"
                                            class="text-[#20b6d2] hover:text-[#336d66] transition-colors">
                                            Privacy Policy
                                        </a>
                                    </p>
                                </div>
                            </div>

                            <!-- Guidelines Checkbox -->
                            <div class="flex items-start space-x-3">
                                <div class="flex items-center h-5">
                                    <input type="checkbox"
                                        id="guidelines"
                                        name="guidelines"
                                        required
                                        class="h-4 w-4 rounded border-gray-300 text-[#336d66] focus:ring-[#336d66]">
                                </div>
                                <div class="text-sm">
                                    <label for="guidelines" class="font-medium text-gray-700">
                                        I agree to follow hiking safety guidelines
                                    </label>
                                    <p class="text-gray-500">
                                        I understand and will follow proper hiking etiquette and safety protocols
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div>
                        <button type="submit"
                            class="group relative w-full flex justify-center py-3 px-4 border border-transparent rounded-xl text-white bg-[#336d66] hover:bg-[#20b6d2] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#20b6d2] transition-colors duration-300 font-medium"
                            x-bind:disabled="!$refs.terms.checked || !$refs.guidelines.checked">
                            {{ __('Register') }}
                        </button>
                    </div>

                    <!-- Sign In Link -->
                    <div class="text-center text-sm text-gray-600">
                        Already have an account?
                        <a href="{{ route('login') }}" class="font-medium text-[#20b6d2] hover:text-[#336d66] transition-colors">
                            Sign in
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-guest-layout>
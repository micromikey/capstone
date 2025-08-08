<x-guest-layout>
    <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-[#336d66]/5 to-white py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8 bg-white/80 backdrop-blur-lg p-8 rounded-2xl shadow-xl">
            <div class="text-center">
                <a href="/" class="flex items-center justify-center space-x-3 mb-8">
                    <img src="{{ asset('img/icon1.png') }}" alt="HikeThere Logo" class="h-12 w-auto">
                    <span class="font-bold text-2xl text-[#336d66]">HikeThere</span>
                </a>
                <h2 class="text-3xl font-extrabold text-gray-900 mb-2">Welcome Back</h2>
                <p class="text-gray-600">Sign in to continue your adventure</p>
            </div>

            <x-validation-errors class="mb-4 bg-red-50 text-red-500 p-4 rounded-lg text-sm" />

            @session('status')
                <div class="mb-4 bg-green-50 text-green-600 p-4 rounded-lg text-sm font-medium">
                    {{ $value }}
                </div>
            @endsession

            <form method="POST" action="{{ route('login') }}" class="mt-8 space-y-6">
                @csrf

                <div class="space-y-4">
                    <div>
                        <x-label for="email" value="{{ __('Email') }}" class="text-gray-700 font-medium" />
                        <div class="mt-1 relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
                                </svg>
                            </div>
                            <x-input id="email" class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-xl focus:ring-[#20b6d2] focus:border-[#20b6d2] transition duration-150" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" placeholder="Enter your email" />
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
                            <x-input id="password" class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-xl focus:ring-[#20b6d2] focus:border-[#20b6d2] transition duration-150" type="password" name="password" required autocomplete="current-password" placeholder="Enter your password" />
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-between">
                    <label for="remember_me" class="flex items-center">
                        <x-checkbox id="remember_me" name="remember" class="text-[#336d66] focus:ring-[#20b6d2]" />
                        <span class="ml-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
                    </label>

                    @if (Route::has('password.request'))
                        <a class="text-sm text-[#20b6d2] hover:text-[#336d66] transition-colors" href="{{ route('password.request') }}">
                            {{ __('Forgot password?') }}
                        </a>
                    @endif
                </div>

                <div>
                    <button type="submit" class="group relative w-full flex justify-center py-3 px-4 border border-transparent rounded-xl text-white bg-[#336d66] hover:bg-[#20b6d2] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#20b6d2] transition-colors duration-300 font-medium">
                        {{ __('Sign in') }}
                    </button>
                </div>

                <div class="text-center text-sm text-gray-600">
                    Don't have an account? 
                    <a href="{{ route('register.select') }}" class="font-medium text-[#20b6d2] hover:text-[#336d66] transition-colors">
                        Sign up now
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-guest-layout>
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Account Settings') }}
        </h2>
    </x-slot>

    <div>
        <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
            <!-- Security Settings Section -->
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg mb-8">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">
                        <svg class="inline-block w-5 h-5 mr-2 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                        {{ __('Security Settings') }}
                    </h3>
                    <p class="mt-1 text-sm text-gray-600">
                        {{ __('Manage your account security settings including password, two-factor authentication, and login sessions.') }}
                    </p>
                </div>
                
                <div class="px-6 py-4 space-y-6">
                    <!-- Password Management -->
                    @if (Laravel\Fortify\Features::enabled(Laravel\Fortify\Features::updatePasswords()))
                        <div class="border border-gray-200 rounded-lg p-4">
                            <div class="mb-4">
                                <h4 class="text-md font-medium text-gray-900">{{ __('Password') }}</h4>
                                <p class="text-sm text-gray-600">{{ __('Update your account password to keep it secure.') }}</p>
                            </div>
                            @livewire('update-password-form')
                        </div>
                    @endif

                    <!-- Two-Factor Authentication -->
                    @if (Laravel\Fortify\Features::canManageTwoFactorAuthentication())
                        <div class="border border-gray-200 rounded-lg p-4">
                            @livewire('two-factor-authentication-form')
                        </div>
                    @endif

                    <!-- Browser Sessions -->
                    <div class="border border-gray-200 rounded-lg p-4">
                        @livewire('logout-other-browser-sessions-form')
                    </div>
                </div>
            </div>

            <!-- Account Management Section -->
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg mb-8">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">
                        <svg class="inline-block w-5 h-5 mr-2 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        {{ __('Account Management') }}
                    </h3>
                    <p class="mt-1 text-sm text-gray-600">
                        {{ __('Manage your account settings and preferences.') }}
                    </p>
                </div>
                
                <div class="px-6 py-4 space-y-6">
                    <!-- Email Verification -->
                    <div class="border border-gray-200 rounded-lg p-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <h4 class="text-md font-medium text-gray-900">{{ __('Email Verification') }}</h4>
                                <p class="text-sm text-gray-600">
                                    @if(Auth::user()->hasVerifiedEmail())
                                        {{ __('Your email address is verified.') }}
                                    @else
                                        {{ __('Please verify your email address.') }}
                                    @endif
                                </p>
                            </div>
                            @if(!Auth::user()->hasVerifiedEmail())
                                <form method="POST" action="{{ route('verification.send') }}">
                                    @csrf
                                    <button type="submit" class="inline-flex items-center justify-center w-32 px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                        {{ __('Resend') }}
                                    </button>
                                </form>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    {{ __('Verified') }}
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Notification Preferences -->
                    <div class="border border-gray-200 rounded-lg p-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <h4 class="text-md font-medium text-gray-900">{{ __('Notification Preferences') }}</h4>
                                <p class="text-sm text-gray-600">{{ __('Manage your email and push notification preferences.') }}</p>
                            </div>
                            <a href="{{ route('preferences.index') }}" class="inline-flex items-center justify-center w-32 px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                {{ __('Configure') }}
                            </a>
                        </div>
                    </div>

                    <!-- Privacy Settings -->
                    <div class="border border-gray-200 rounded-lg p-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <h4 class="text-md font-medium text-gray-900">{{ __('Privacy Settings') }}</h4>
                                <p class="text-sm text-gray-600">{{ __('Control your profile visibility and data sharing preferences.') }}</p>
                            </div>
                            <a href="{{ route('preferences.index') }}" class="inline-flex items-center justify-center w-32 px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                {{ __('Manage') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Hiking Preferences Section -->
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg mb-8">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">
                        <svg class="inline-block w-5 h-5 mr-2 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                        {{ __('Hiking Preferences') }}
                    </h3>
                    <p class="mt-1 text-sm text-gray-600">
                        {{ __('Set your fitness level and hiking preferences to get personalized itinerary recommendations.') }}
                    </p>
                </div>
                
                <div class="px-6 py-4">
                    <form action="{{ route('account.fitness.update') }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="border border-gray-200 rounded-lg p-4">
                            <div class="mb-4">
                                <h4 class="text-md font-medium text-gray-900">{{ __('Fitness Level') }}</h4>
                                <p class="text-sm text-gray-600 mb-4">
                                    {{ __('Your fitness level helps us adjust hiking times and recommend appropriate trails for you.') }}
                                </p>
                            </div>
                            
                            <div class="space-y-3">
                                <!-- Beginner Option -->
                                <label class="relative flex items-start p-4 rounded-lg border-2 cursor-pointer transition-all {{ Auth::user()->fitness_level === 'beginner' ? 'border-emerald-500 bg-emerald-50' : 'border-gray-200 hover:border-emerald-300 hover:bg-gray-50' }}">
                                    <input type="radio" name="fitness_level" value="beginner" 
                                        class="mt-1 h-4 w-4 text-emerald-600 focus:ring-emerald-500 border-gray-300" 
                                        {{ Auth::user()->fitness_level === 'beginner' ? 'checked' : '' }}>
                                    <span class="ml-3 flex flex-col">
                                        <span class="block text-sm font-medium text-gray-900">
                                            🥾 Beginner
                                        </span>
                                        <span class="block text-sm text-gray-500">
                                            New to hiking or prefer leisurely pace. Itineraries will include 30% more time for activities and frequent rest breaks.
                                        </span>
                                    </span>
                                </label>

                                <!-- Intermediate Option -->
                                <label class="relative flex items-start p-4 rounded-lg border-2 cursor-pointer transition-all {{ Auth::user()->fitness_level === 'intermediate' ? 'border-emerald-500 bg-emerald-50' : 'border-gray-200 hover:border-emerald-300 hover:bg-gray-50' }}">
                                    <input type="radio" name="fitness_level" value="intermediate" 
                                        class="mt-1 h-4 w-4 text-emerald-600 focus:ring-emerald-500 border-gray-300" 
                                        {{ Auth::user()->fitness_level === 'intermediate' || !Auth::user()->fitness_level ? 'checked' : '' }}>
                                    <span class="ml-3 flex flex-col">
                                        <span class="block text-sm font-medium text-gray-900">
                                            ⛰️ Intermediate
                                        </span>
                                        <span class="block text-sm text-gray-500">
                                            Regular hiker with moderate fitness. Itineraries will use standard hiking paces and moderate break schedules.
                                        </span>
                                    </span>
                                </label>

                                <!-- Advanced Option -->
                                <label class="relative flex items-start p-4 rounded-lg border-2 cursor-pointer transition-all {{ Auth::user()->fitness_level === 'advanced' ? 'border-emerald-500 bg-emerald-50' : 'border-gray-200 hover:border-emerald-300 hover:bg-gray-50' }}">
                                    <input type="radio" name="fitness_level" value="advanced" 
                                        class="mt-1 h-4 w-4 text-emerald-600 focus:ring-emerald-500 border-gray-300" 
                                        {{ Auth::user()->fitness_level === 'advanced' ? 'checked' : '' }}>
                                    <span class="ml-3 flex flex-col">
                                        <span class="block text-sm font-medium text-gray-900">
                                            🏔️ Advanced
                                        </span>
                                        <span class="block text-sm text-gray-500">
                                            Experienced hiker with excellent fitness. Itineraries will use faster paces (20% less time) and minimal rest breaks.
                                        </span>
                                    </span>
                                </label>
                            </div>

                            <div class="mt-6">
                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-emerald-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-emerald-700 focus:bg-emerald-700 active:bg-emerald-900 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    {{ __('Save Fitness Level') }}
                                </button>
                            </div>

                            @if (session('fitness_updated'))
                                <div class="mt-4 rounded-md bg-green-50 p-4">
                                    <div class="flex">
                                        <div class="flex-shrink-0">
                                            <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                            </svg>
                                        </div>
                                        <div class="ml-3">
                                            <p class="text-sm font-medium text-green-800">
                                                {{ session('fitness_updated') }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </form>
                </div>
            </div>

            <!-- Danger Zone Section -->
            @if (Laravel\Jetstream\Jetstream::hasAccountDeletionFeatures())
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200 bg-red-50">
                        <h3 class="text-lg font-medium text-red-900">
                            <svg class="inline-block w-5 h-5 mr-2 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                            </svg>
                            {{ __('Danger Zone') }}
                        </h3>
                        <p class="mt-1 text-sm text-red-700">
                            {{ __('Irreversible and destructive actions.') }}
                        </p>
                    </div>
                    
                    <div class="px-6 py-4">
                        <div class="border border-red-200 rounded-lg p-4 bg-red-50">
                            <div class="mb-4">
                                <h4 class="text-md font-medium text-red-900">{{ __('Delete Account') }}</h4>
                                <p class="text-sm text-red-700">{{ __('Permanently delete your account and all of its data.') }}</p>
                            </div>
                            @livewire('delete-user-form')
                        </div>
                    </div>
                </div>
            @endif

            <!-- Quick Actions -->
            <div class="mt-8 flex flex-col sm:flex-row gap-4">
                        <a href="{{ route('custom.profile.show') }}" class="inline-flex items-center justify-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    {{ __('View Full Profile') }}
                </a>
                
                <a href="{{ route('profile.edit') }}" class="inline-flex items-center justify-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    {{ __('Edit Profile') }}
                </a>
            </div>
        </div>
    </div>
</x-app-layout>

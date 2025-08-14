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
                        {{ __('Manage your organization\'s account security settings including password, two-factor authentication, and login sessions.') }}
                    </p>
                </div>
                
                <div class="px-6 py-4 space-y-6">
                    <!-- Password Management -->
                    @if (Laravel\Fortify\Features::enabled(Laravel\Fortify\Features::updatePasswords()))
                        <div class="border border-gray-200 rounded-lg p-4">
                            <div class="mb-4">
                                <h4 class="text-md font-medium text-gray-900">{{ __('Password') }}</h4>
                                <p class="text-sm text-gray-600">{{ __('Update your organization\'s account password to keep it secure.') }}</p>
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
                        {{ __('Manage your organization\'s account settings and preferences.') }}
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
                                        {{ __('Your organization\'s email address is verified.') }}
                                    @else
                                        {{ __('Please verify your organization\'s email address.') }}
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

                    <!-- Organization Approval Status -->
                    <div class="border border-gray-200 rounded-lg p-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <h4 class="text-md font-medium text-gray-900">{{ __('Approval Status') }}</h4>
                                <p class="text-sm text-gray-600">
                                    @if(Auth::user()->approval_status === 'approved')
                                        {{ __('Your organization is approved and can manage trails.') }}
                                    @elseif(Auth::user()->approval_status === 'pending')
                                        {{ __('Your organization is pending approval.') }}
                                    @else
                                        {{ __('Your organization\'s approval status.') }}
                                    @endif
                                </p>
                            </div>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                @if(Auth::user()->approval_status === 'approved') bg-green-100 text-green-800
                                @elseif(Auth::user()->approval_status === 'pending') bg-yellow-100 text-yellow-800
                                @else bg-gray-100 text-gray-800 @endif">
                                @if(Auth::user()->approval_status === 'approved')
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    {{ __('Approved') }}
                                @elseif(Auth::user()->approval_status === 'pending')
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                                    </svg>
                                    {{ __('Pending') }}
                                @else
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                    </svg>
                                    {{ __('Not Set') }}
                                @endif
                            </span>
                        </div>
                    </div>

                    <!-- Notification Preferences -->
                    <div class="border border-gray-200 rounded-lg p-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <h4 class="text-md font-medium text-gray-900">{{ __('Notification Preferences') }}</h4>
                                <p class="text-sm text-gray-600">{{ __('Manage your organization\'s email and push notification preferences.') }}</p>
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
                                <p class="text-sm text-gray-600">{{ __('Control your organization\'s profile visibility and data sharing preferences.') }}</p>
                            </div>
                            <a href="{{ route('preferences.index') }}" class="inline-flex items-center justify-center w-32 px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                {{ __('Manage') }}
                            </a>
                        </div>
                    </div>
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
                                <h4 class="text-md font-medium text-red-900">{{ __('Delete Organization Account') }}</h4>
                                <p class="text-sm text-red-700">{{ __('Permanently delete your organization account and all of its data including trails.') }}</p>
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

                <a href="{{ route('org.dashboard') }}" class="inline-flex items-center justify-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5a2 2 0 012-2h4a2 2 0 012 2v6H8V5z"></path>
                    </svg>
                    {{ __('Organization Dashboard') }}
                </a>
            </div>
        </div>
    </div>
</x-app-layout>

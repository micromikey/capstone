<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Account Preferences') }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('account.settings') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    {{ __('Back to Settings') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-6 bg-green-50 border border-green-200 rounded-lg p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            <form method="POST" action="{{ route('preferences.update') }}">
                @csrf
                
                <!-- Notification Preferences -->
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg mb-8">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">
                            <svg class="inline-block w-5 h-5 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM4.19 4.19A4 4 0 004 6v6a4 4 0 004 4h6a4 4 0 004-4V6a4 4 0 00-4-4H8a4 4 0 00-2.81 1.19z"></path>
                            </svg>
                            {{ __('Notification Preferences') }}
                        </h3>
                        <p class="mt-1 text-sm text-gray-600">{{ __('Choose how and when you want to be notified.') }}</p>
                    </div>
                    
                    <div class="px-6 py-4 space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <label class="flex items-center">
                                <input type="checkbox" name="email_notifications" value="1" 
                                    {{ $preferences['email_notifications'] ?? true ? 'checked' : '' }}
                                    class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                <span class="ml-2 text-sm text-gray-700">{{ __('Email Notifications') }}</span>
                            </label>
                            
                            <label class="flex items-center">
                                <input type="checkbox" name="push_notifications" value="1" 
                                    {{ $preferences['push_notifications'] ?? true ? 'checked' : '' }}
                                    class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                <span class="ml-2 text-sm text-gray-700">{{ __('Push Notifications') }}</span>
                            </label>
                            
                            <label class="flex items-center">
                                <input type="checkbox" name="trail_updates" value="1" 
                                    {{ $preferences['trail_updates'] ?? true ? 'checked' : '' }}
                                    class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                <span class="ml-2 text-sm text-gray-700">{{ __('Trail Updates') }}</span>
                            </label>
                            
                            <label class="flex items-center">
                                <input type="checkbox" name="security_alerts" value="1" 
                                    {{ $preferences['security_alerts'] ?? true ? 'checked' : '' }}
                                    class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                <span class="ml-2 text-sm text-gray-700">{{ __('Security Alerts') }}</span>
                            </label>
                            
                            <label class="flex items-center">
                                <input type="checkbox" name="newsletter" value="1" 
                                    {{ $preferences['newsletter'] ?? false ? 'checked' : '' }}
                                    class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                <span class="ml-2 text-sm text-gray-700">{{ __('Newsletter') }}</span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Privacy Settings -->
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg mb-8">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">
                            <svg class="inline-block w-5 h-5 mr-2 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                            {{ __('Privacy Settings') }}
                        </h3>
                        <p class="mt-1 text-sm text-gray-600">{{ __('Control who can see your profile information.') }}</p>
                    </div>
                    
                    <div class="px-6 py-4 space-y-4">
                        <div>
                            <label for="profile_visibility" class="block text-sm font-medium text-gray-700 mb-2">
                                {{ __('Profile Visibility') }}
                            </label>
                            <select name="profile_visibility" id="profile_visibility" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                                <option value="public" {{ ($preferences['profile_visibility'] ?? 'public') === 'public' ? 'selected' : '' }}>
                                    {{ __('Public - Anyone can see your profile') }}
                                </option>
                                <option value="friends" {{ ($preferences['profile_visibility'] ?? 'public') === 'friends' ? 'selected' : '' }}>
                                    {{ __('Friends Only - Only friends can see your profile') }}
                                </option>
                                <option value="private" {{ ($preferences['profile_visibility'] ?? 'public') === 'private' ? 'selected' : '' }}>
                                    {{ __('Private - Only you can see your profile') }}
                                </option>
                            </select>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <label class="flex items-center">
                                <input type="checkbox" name="show_email" value="1" 
                                    {{ $preferences['show_email'] ?? false ? 'checked' : '' }}
                                    class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                <span class="ml-2 text-sm text-gray-700">{{ __('Show Email Address') }}</span>
                            </label>
                            
                            <label class="flex items-center">
                                <input type="checkbox" name="show_phone" value="1" 
                                    {{ $preferences['show_phone'] ?? false ? 'checked' : '' }}
                                    class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                <span class="ml-2 text-sm text-gray-700">{{ __('Show Phone Number') }}</span>
                            </label>
                            
                            <label class="flex items-center">
                                <input type="checkbox" name="show_location" value="1" 
                                    {{ $preferences['show_location'] ?? true ? 'checked' : '' }}
                                    class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                <span class="ml-2 text-sm text-gray-700">{{ __('Show Location') }}</span>
                            </label>
                            
                            <label class="flex items-center">
                                <input type="checkbox" name="show_birth_date" value="1" 
                                    {{ $preferences['show_birth_date'] ?? false ? 'checked' : '' }}
                                    class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                <span class="ml-2 text-sm text-gray-700">{{ __('Show Birth Date') }}</span>
                            </label>
                            
                            <label class="flex items-center">
                                <input type="checkbox" name="show_hiking_preferences" value="1" 
                                    {{ $preferences['show_hiking_preferences'] ?? true ? 'checked' : '' }}
                                    class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                <span class="ml-2 text-sm text-gray-700">{{ __('Show Hiking Preferences') }}</span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Account Settings -->
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg mb-8">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">
                            <svg class="inline-block w-5 h-5 mr-2 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            {{ __('Account Settings') }}
                        </h3>
                        <p class="mt-1 text-sm text-gray-600">{{ __('Additional account configuration options.') }}</p>
                    </div>
                    
                    <div class="px-6 py-4 space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="timezone" class="block text-sm font-medium text-gray-700 mb-2">
                                    {{ __('Timezone') }}
                                </label>
                                <select name="timezone" id="timezone" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                                    <option value="UTC" {{ ($preferences['timezone'] ?? 'UTC') === 'UTC' ? 'selected' : '' }}>UTC</option>
                                    <option value="America/New_York" {{ ($preferences['timezone'] ?? 'UTC') === 'America/New_York' ? 'selected' : '' }}>Eastern Time</option>
                                    <option value="America/Chicago" {{ ($preferences['timezone'] ?? 'UTC') === 'America/Chicago' ? 'selected' : '' }}>Central Time</option>
                                    <option value="America/Denver" {{ ($preferences['timezone'] ?? 'UTC') === 'America/Denver' ? 'selected' : '' }}>Mountain Time</option>
                                    <option value="America/Los_Angeles" {{ ($preferences['timezone'] ?? 'UTC') === 'America/Los_Angeles' ? 'selected' : '' }}>Pacific Time</option>
                                    <option value="Europe/London" {{ ($preferences['timezone'] ?? 'UTC') === 'Europe/London' ? 'selected' : '' }}>London</option>
                                    <option value="Europe/Paris" {{ ($preferences['timezone'] ?? 'UTC') === 'Europe/Paris' ? 'selected' : '' }}>Paris</option>
                                    <option value="Asia/Tokyo" {{ ($preferences['timezone'] ?? 'UTC') === 'Asia/Tokyo' ? 'selected' : '' }}>Tokyo</option>
                                </select>
                            </div>
                            
                            <div>
                                <label for="language" class="block text-sm font-medium text-gray-700 mb-2">
                                    {{ __('Language') }}
                                </label>
                                <select name="language" id="language" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                                    <option value="en" {{ ($preferences['language'] ?? 'en') === 'en' ? 'selected' : '' }}>English</option>
                                    <option value="es" {{ ($preferences['language'] ?? 'en') === 'es' ? 'selected' : '' }}>Español</option>
                                    <option value="fr" {{ ($preferences['language'] ?? 'en') === 'fr' ? 'selected' : '' }}>Français</option>
                                    <option value="de" {{ ($preferences['language'] ?? 'en') === 'de' ? 'selected' : '' }}>Deutsch</option>
                                    <option value="it" {{ ($preferences['language'] ?? 'en') === 'it' ? 'selected' : '' }}>Italiano</option>
                                    <option value="pt" {{ ($preferences['language'] ?? 'en') === 'pt' ? 'selected' : '' }}>Português</option>
                                </select>
                            </div>
                        </div>

                        <label class="flex items-center">
                            <input type="checkbox" name="two_factor_required" value="1" 
                                {{ $preferences['two_factor_required'] ?? false ? 'checked' : '' }}
                                class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                            <span class="ml-2 text-sm text-gray-700">{{ __('Require Two-Factor Authentication for all logins') }}</span>
                        </label>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex justify-between items-center">
                    <div class="flex space-x-4">
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            {{ __('Save Preferences') }}
                        </button>
                        
                        <a href="{{ route('preferences.export') }}" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            {{ __('Export Data') }}
                        </a>
                    </div>
                    
                    <form method="POST" action="{{ route('preferences.reset') }}" class="inline" onsubmit="return confirm('{{ __('Are you sure you want to reset all preferences to defaults? This action cannot be undone.') }}')">
                        @csrf
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-900 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                            </svg>
                            {{ __('Reset to Defaults') }}
                        </button>
                    </form>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>

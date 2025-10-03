<x-app-layout>
    <x-slot name="header">
        <div class="space-y-2">
            <x-trail-breadcrumb />
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Payment Setup') }}</h2>
                    <p class="text-sm text-gray-600 mt-1">Configure how you receive payments from hikers</p>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-6 bg-green-100 border-l-4 border-green-500 text-green-700 px-6 py-4 rounded-r-lg shadow-sm" role="alert">
                    <div class="flex items-center">
                        <svg class="h-5 w-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <span class="font-medium">{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 bg-red-100 border-l-4 border-red-500 text-red-700 px-6 py-4 rounded-r-lg shadow-sm" role="alert">
                    <div class="flex items-center">
                        <svg class="h-5 w-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                        <span class="font-medium">{{ session('error') }}</span>
                    </div>
                </div>
            @endif

            <!-- Introduction -->
            <div class="bg-gradient-to-r from-[#336d66] to-[#2a5a54] text-white rounded-lg shadow-lg p-6 mb-8">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-semibold mb-2">Why Set Up Payment?</h3>
                        <p class="text-sm opacity-90 mb-3">
                            To receive payments directly from hikers booking your trails, you need to connect your own payment gateway account. This ensures:
                        </p>
                        <ul class="text-sm space-y-1 opacity-90">
                            <li class="flex items-center">
                                <svg class="h-4 w-4 mr-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                Payments go directly to your bank account
                            </li>
                            <li class="flex items-center">
                                <svg class="h-4 w-4 mr-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                You control your own payment processing
                            </li>
                            <li class="flex items-center">
                                <svg class="h-4 w-4 mr-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                Your credentials are encrypted and secure
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Current Status -->
            <div class="bg-white rounded-lg shadow-lg overflow-hidden mb-8">
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800">Current Payment Status</h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- PayMongo Status -->
                        <div class="border-2 {{ $credentials->hasPaymongoConfigured() ? 'border-green-500 bg-green-50' : 'border-gray-300 bg-gray-50' }} rounded-lg p-4">
                            <div class="flex items-start justify-between">
                                <div class="flex items-start">
                                    @if($credentials->hasPaymongoConfigured())
                                        <svg class="h-6 w-6 text-green-500 mt-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                    @else
                                        <svg class="h-6 w-6 text-gray-400 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    @endif
                                    <div class="ml-3">
                                        <h4 class="text-md font-semibold {{ $credentials->hasPaymongoConfigured() ? 'text-green-700' : 'text-gray-700' }}">PayMongo</h4>
                                        <p class="text-sm {{ $credentials->hasPaymongoConfigured() ? 'text-green-600' : 'text-gray-500' }} mt-1">
                                            @if($credentials->hasPaymongoConfigured())
                                                ✓ Connected and ready
                                            @else
                                                Not configured yet
                                            @endif
                                        </p>
                                    </div>
                                </div>
                                @if($credentials->active_gateway === 'paymongo')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        Active
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!-- Xendit Status -->
                        <div class="border-2 border-gray-300 bg-gray-50 rounded-lg p-4 opacity-75">
                            <div class="flex items-start justify-between">
                                <div class="flex items-start">
                                    <svg class="h-6 w-6 text-gray-400 mt-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                    <div class="ml-3">
                                        <h4 class="text-md font-semibold text-gray-700">Xendit</h4>
                                        <p class="text-sm text-gray-500 mt-1">
                                            Hardcoded (for testing)
                                        </p>
                                    </div>
                                </div>
                                @if($credentials->active_gateway === 'xendit')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        Active
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment Gateway Configuration Form -->
            <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800">Configure Payment Gateway</h3>
                    <p class="text-sm text-gray-600 mt-1">Choose which payment provider you want to use</p>
                </div>
                
                <form method="POST" action="{{ route('org.payment.update') }}" class="p-6">
                    @csrf
                    @method('PUT')

                    <!-- Active Gateway Selection -->
                    <div class="mb-8 bg-blue-50 border border-blue-200 rounded-lg p-5">
                        <label for="active_gateway" class="block text-md font-semibold text-gray-800 mb-3">
                            <svg class="inline-block h-5 w-5 mr-1 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                            Which payment gateway do you want to use?
                        </label>
                        <select id="active_gateway" name="active_gateway" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-[#336d66] focus:ring focus:ring-[#336d66] focus:ring-opacity-50 text-base py-3">
                            <option value="paymongo" {{ $credentials->active_gateway === 'paymongo' ? 'selected' : '' }}>
                                PayMongo (Recommended - Requires your account)
                            </option>
                            <option value="xendit" {{ $credentials->active_gateway === 'xendit' ? 'selected' : '' }}>
                                Xendit (Testing mode - Hardcoded)
                            </option>
                        </select>
                        <p class="mt-2 text-sm text-gray-600">This determines which service processes your payments</p>
                    </div>

                    <div class="border-t border-gray-200 pt-6"></div>

                    <div class="border-t border-gray-200 pt-6"></div>

                    <!-- PayMongo Configuration -->
                    <div class="mb-8">
                        <div class="flex items-center mb-4">
                            <div class="flex-shrink-0">
                                <div class="flex items-center justify-center h-10 w-10 rounded-full bg-purple-100">
                                    <svg class="h-6 w-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4">
                                <h4 class="text-lg font-semibold text-gray-800">PayMongo Account</h4>
                                <p class="text-sm text-gray-600">Connect your PayMongo account to accept payments</p>
                            </div>
                        </div>

                        @if(!$credentials->hasPaymongoConfigured())
                            <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-4">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm text-yellow-700">
                                            <strong>Action Required:</strong> You need to set up PayMongo to receive payments. Don't have an account? 
                                            <a href="https://dashboard.paymongo.com/signup" target="_blank" class="underline font-semibold">Create one here</a>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endif
                        
                        <div class="space-y-5 bg-gray-50 p-5 rounded-lg">
                            <div>
                                <label for="paymongo_secret_key" class="block text-sm font-semibold text-gray-700 mb-2">
                                    Secret Key <span class="text-red-500">*</span>
                                </label>
                                <input type="password" id="paymongo_secret_key" name="paymongo_secret_key" placeholder="sk_test_xxxxxxxxxxxxxx" 
                                    class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-[#336d66] focus:ring focus:ring-[#336d66] focus:ring-opacity-50 text-base py-2.5"
                                    value="{{ old('paymongo_secret_key') }}">
                                <div class="mt-2 flex items-start text-xs text-gray-600">
                                    <svg class="h-4 w-4 mr-1 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                    </svg>
                                    <span>Leave empty to keep your existing key. Starts with "sk_test_" or "sk_live_"</span>
                                </div>
                                @if($credentials->hasPaymongoConfigured())
                                    <p class="mt-1.5 text-xs text-green-600 font-medium flex items-center">
                                        <svg class="h-4 w-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                        Secret key is currently saved
                                    </p>
                                @endif
                            </div>

                            <div>
                                <label for="paymongo_public_key" class="block text-sm font-semibold text-gray-700 mb-2">
                                    Public Key <span class="text-red-500">*</span>
                                </label>
                                <input type="text" id="paymongo_public_key" name="paymongo_public_key" placeholder="pk_test_xxxxxxxxxxxxxx" 
                                    class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-[#336d66] focus:ring focus:ring-[#336d66] focus:ring-opacity-50 text-base py-2.5"
                                    value="{{ old('paymongo_public_key') }}">
                                <div class="mt-2 flex items-start text-xs text-gray-600">
                                    <svg class="h-4 w-4 mr-1 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                    </svg>
                                    <span>Leave empty to keep your existing key. Starts with "pk_test_" or "pk_live_"</span>
                                </div>
                                @if($credentials->hasPaymongoConfigured())
                                    <p class="mt-1.5 text-xs text-green-600 font-medium flex items-center">
                                        <svg class="h-4 w-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                        Public key is currently saved
                                    </p>
                                @endif
                            </div>

                            @if($credentials->hasPaymongoConfigured())
                                <div class="pt-3 border-t border-gray-200 flex flex-wrap gap-3">
                                    <button type="button" onclick="testConnection('paymongo')" class="inline-flex items-center px-4 py-2 border border-[#336d66] shadow-sm text-sm font-medium rounded-md text-[#336d66] bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#336d66]">
                                        <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        Test Connection
                                    </button>
                                    <form method="POST" action="{{ route('org.payment.clear') }}" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <input type="hidden" name="gateway" value="paymongo">
                                        <button type="submit" onclick="return confirm('Are you sure you want to remove your PayMongo credentials? You will need to re-enter them to receive payments.')" class="inline-flex items-center px-4 py-2 border border-red-300 shadow-sm text-sm font-medium rounded-md text-red-700 bg-white hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                            <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                            Remove Credentials
                                        </button>
                                    </form>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="border-t border-gray-200 pt-6 mb-6"></div>

                    <div class="border-t border-gray-200 pt-6 mb-6"></div>

                    <!-- Xendit Configuration -->
                    <div class="mb-6">
                        <div class="flex items-center mb-4">
                            <div class="flex-shrink-0">
                                <div class="flex items-center justify-center h-10 w-10 rounded-full bg-blue-100">
                                    <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4">
                                <h4 class="text-lg font-semibold text-gray-800">Xendit Account</h4>
                                <p class="text-sm text-gray-600">Testing mode - Credentials are hardcoded</p>
                            </div>
                        </div>
                        
                        <div class="bg-blue-50 border-l-4 border-blue-400 p-4 rounded-r-lg">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h5 class="text-sm font-semibold text-blue-800 mb-1">For Testing Only</h5>
                                    <p class="text-sm text-blue-700">
                                        Xendit is currently using system-wide credentials for testing purposes. You don't need to configure anything here. 
                                        In the future, you'll be able to connect your own Xendit account.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="flex items-center justify-between mt-8 pt-6 border-t border-gray-200">
                        <div class="text-sm text-gray-600">
                            <svg class="inline-block h-4 w-4 mr-1 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            Your credentials are encrypted and secure
                        </div>
                        <button type="submit" class="inline-flex items-center px-6 py-3 bg-[#336d66] border border-transparent rounded-lg font-semibold text-sm text-white uppercase tracking-wider hover:bg-[#2a5a54] focus:bg-[#2a5a54] active:bg-[#1f4740] focus:outline-none focus:ring-2 focus:ring-[#336d66] focus:ring-offset-2 transition ease-in-out duration-150 shadow-lg">
                            <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Save Configuration
                        </button>
                    </div>
                </form>
            </div>

            <!-- Help Section -->
            <div class="mt-8 bg-white rounded-lg shadow-lg overflow-hidden">
                <div class="px-6 py-4 bg-gradient-to-r from-[#336d66] to-[#2a5a54] text-white">
                    <h3 class="text-lg font-semibold flex items-center">
                        <svg class="h-6 w-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Need Help?
                    </h3>
                </div>
                
                <div class="p-6 space-y-6">
                    <div>
                        <h4 class="font-semibold text-[#336d66] text-base mb-3 flex items-center">
                            <span class="flex items-center justify-center h-6 w-6 rounded-full bg-[#336d66] text-white text-xs mr-2">1</span>
                            How to Get Your PayMongo API Keys
                        </h4>
                        <div class="pl-8 space-y-2">
                            <div class="flex items-start text-sm text-gray-700">
                                <span class="font-semibold mr-2 text-[#336d66]">•</span>
                                <div>
                                    <strong>Step 1:</strong> Go to <a href="https://dashboard.paymongo.com/" target="_blank" class="text-[#336d66] underline hover:text-[#2a5a54]">PayMongo Dashboard</a> and log in
                                </div>
                            </div>
                            <div class="flex items-start text-sm text-gray-700">
                                <span class="font-semibold mr-2 text-[#336d66]">•</span>
                                <div><strong>Step 2:</strong> Click on "Developers" in the left sidebar, then select "API Keys"</div>
                            </div>
                            <div class="flex items-start text-sm text-gray-700">
                                <span class="font-semibold mr-2 text-[#336d66]">•</span>
                                <div><strong>Step 3:</strong> Copy your <strong>Secret Key</strong> (starts with <code class="bg-gray-100 px-2 py-0.5 rounded text-xs">sk_test_</code> or <code class="bg-gray-100 px-2 py-0.5 rounded text-xs">sk_live_</code>)</div>
                            </div>
                            <div class="flex items-start text-sm text-gray-700">
                                <span class="font-semibold mr-2 text-[#336d66]">•</span>
                                <div><strong>Step 4:</strong> Copy your <strong>Public Key</strong> (starts with <code class="bg-gray-100 px-2 py-0.5 rounded text-xs">pk_test_</code> or <code class="bg-gray-100 px-2 py-0.5 rounded text-xs">pk_live_</code>)</div>
                            </div>
                            <div class="flex items-start text-sm text-gray-700">
                                <span class="font-semibold mr-2 text-[#336d66]">•</span>
                                <div><strong>Step 5:</strong> Paste both keys in the form above and click "Save Configuration"</div>
                            </div>
                        </div>
                    </div>

                    <div class="border-t border-gray-200 pt-4">
                        <h4 class="font-semibold text-[#336d66] text-base mb-3 flex items-center">
                            <span class="flex items-center justify-center h-6 w-6 rounded-full bg-[#336d66] text-white text-xs mr-2">2</span>
                            Test Mode vs Live Mode
                        </h4>
                        <div class="pl-8 bg-gray-50 rounded-lg p-4">
                            <div class="space-y-2 text-sm text-gray-700">
                                <p><strong class="text-gray-900">Test Mode (Recommended for now):</strong> Use test keys (<code class="bg-white px-2 py-0.5 rounded text-xs">sk_test_</code>, <code class="bg-white px-2 py-0.5 rounded text-xs">pk_test_</code>) to accept fake payments for testing. No real money involved.</p>
                                <p><strong class="text-gray-900">Live Mode (When ready):</strong> Use live keys (<code class="bg-white px-2 py-0.5 rounded text-xs">sk_live_</code>, <code class="bg-white px-2 py-0.5 rounded text-xs">pk_live_</code>) to accept real payments from customers.</p>
                            </div>
                        </div>
                    </div>

                    <div class="border-t border-gray-200 pt-4">
                        <h4 class="font-semibold text-[#336d66] text-base mb-3 flex items-center">
                            <span class="flex items-center justify-center h-6 w-6 rounded-full bg-[#336d66] text-white text-xs mr-2">?</span>
                            Frequently Asked Questions
                        </h4>
                        <div class="pl-8 space-y-3">
                            <div>
                                <p class="text-sm font-semibold text-gray-900">Q: Where does the money go?</p>
                                <p class="text-sm text-gray-700 mt-1">A: Payments go directly to YOUR PayMongo account, which is linked to your bank account.</p>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-gray-900">Q: Is my information secure?</p>
                                <p class="text-sm text-gray-700 mt-1">A: Yes! Your API keys are encrypted before being stored in our database.</p>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-gray-900">Q: Can I change my keys later?</p>
                                <p class="text-sm text-gray-700 mt-1">A: Absolutely! Just come back to this page and enter new keys anytime.</p>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-gray-900">Q: What about Xendit?</p>
                                <p class="text-sm text-gray-700 mt-1">A: Xendit is currently in testing mode with system-wide credentials. Full support coming soon!</p>
                            </div>
                        </div>
                    </div>

                    <div class="border-t border-gray-200 pt-4">
                        <div class="bg-blue-50 rounded-lg p-4">
                            <div class="flex">
                                <svg class="h-5 w-5 text-blue-600 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                </svg>
                                <div class="ml-3">
                                    <p class="text-sm text-blue-800">
                                        <strong>Don't have a PayMongo account yet?</strong><br>
                                        Sign up for free at <a href="https://dashboard.paymongo.com/signup" target="_blank" class="underline font-semibold hover:text-blue-900">dashboard.paymongo.com/signup</a>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function testConnection(gateway) {
            fetch('{{ route("org.payment.test") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ gateway: gateway })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('✓ ' + data.message);
                } else {
                    alert('✗ ' + data.message);
                }
            })
            .catch(error => {
                alert('Error testing connection: ' + error.message);
            });
        }
    </script>
</x-app-layout>

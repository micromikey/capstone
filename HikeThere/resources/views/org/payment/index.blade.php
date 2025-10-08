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

            <!-- Payment Method Toggle -->
            <div class="bg-gradient-to-r from-[#336d66] to-[#2a5a54] rounded-lg shadow-lg p-6 mb-6">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <h3 class="text-lg font-semibold text-white mb-2 flex items-center">
                            <svg class="h-6 w-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                            </svg>
                            Active Payment Method
                        </h3>
                        <p class="text-sm text-white/90">
                            Choose how hikers will pay for bookings. This determines what payment option they see during checkout.
                        </p>
                    </div>
                    <div class="ml-8 flex items-center bg-white/10 backdrop-blur-sm rounded-lg p-4">
                        <form method="POST" action="{{ route('org.payment.toggle-method') }}" id="payment-method-toggle-form">
                            @csrf
                            @method('PUT')
                            <label class="flex items-center cursor-pointer">
                                <div class="relative">
                                    <input type="checkbox" name="payment_method" value="automatic" class="sr-only" 
                                           id="payment-method-toggle"
                                           {{ $credentials->payment_method === 'automatic' ? 'checked' : '' }}
                                           onchange="this.form.submit()">
                                    <div class="block bg-gray-300 w-20 h-10 rounded-full transition-colors duration-300" id="toggle-bg"></div>
                                    <div class="dot absolute left-1 top-1 bg-white w-8 h-8 rounded-full transition-transform duration-300 flex items-center justify-center shadow-md" id="toggle-dot">
                                        <svg class="h-5 w-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" id="toggle-icon">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-4 text-white">
                                    <div class="text-sm font-semibold" id="toggle-label">
                                        {{ $credentials->payment_method === 'automatic' ? 'Automatic Payment' : 'Manual Payment' }}
                                    </div>
                                    <div class="text-xs opacity-90" id="toggle-description">
                                        {{ $credentials->payment_method === 'automatic' ? 'Using payment gateway' : 'Using QR code' }}
                                    </div>
                                </div>
                            </label>
                        </form>
                    </div>
                </div>
                
                <!-- Status Indicator -->
                <div class="mt-4 pt-4 border-t border-white/20">
                    <div class="flex items-center text-white/90 text-sm">
                        <svg class="h-5 w-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                        </svg>
                        <span id="payment-status-message">
                            @if($credentials->payment_method === 'manual')
                                Hikers will see your QR code and upload payment proof during checkout.
                                @if(!$credentials->hasManualPaymentConfigured())
                                    <strong class="text-yellow-300">⚠️ Please upload your QR code below to complete setup.</strong>
                                @endif
                            @else
                                Hikers will be directed to the payment gateway to complete their booking.
                                @if(!$credentials->hasPaymongoConfigured() && !$credentials->hasXenditConfigured())
                                    <strong class="text-yellow-300">⚠️ Please configure a payment gateway below to complete setup.</strong>
                                @endif
                            @endif
                        </span>
                    </div>
                </div>
            </div>

            <!-- Tabs Navigation -->
            <div class="bg-white rounded-t-lg shadow-lg">
                <div class="border-b border-gray-200">
                    <nav class="flex -mb-px" aria-label="Tabs">
                        <button onclick="switchTab('manual')" id="manual-tab" class="tab-button group inline-flex items-center py-4 px-8 border-b-2 font-medium text-sm transition-colors" aria-current="page">
                            <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                            </svg>
                            Manual Payment (QR Code)
                            <span class="ml-2 px-2 py-0.5 rounded text-xs font-bold bg-green-100 text-green-800">Recommended</span>
                        </button>
                        <button onclick="switchTab('automatic')" id="automatic-tab" class="tab-button group inline-flex items-center py-4 px-8 border-b-2 font-medium text-sm transition-colors">
                            <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                            </svg>
                            Automatic Payment (Gateway)
                            <span class="ml-2 px-2 py-0.5 rounded text-xs font-bold bg-blue-100 text-blue-800">Beta</span>
                        </button>
                    </nav>
                </div>
            </div>

            <!-- Manual Payment Tab Content -->
            <div id="manual-content" class="tab-content bg-white rounded-b-lg shadow-lg overflow-hidden">
                <div class="p-8">
                    <!-- Introduction -->
                    <div class="bg-gradient-to-r from-green-600 to-emerald-600 text-white rounded-lg shadow-lg p-6 mb-8">
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-lg font-semibold mb-2">Manual Payment with QR Code</h3>
                                <p class="text-sm opacity-90 mb-3">
                                    Simple and direct! Hikers scan your QR code, pay via their preferred mobile wallet (GCash, PayMaya, etc.), and upload proof of payment.
                                </p>
                                <ul class="text-sm space-y-1 opacity-90">
                                    <li class="flex items-center">
                                        <svg class="h-4 w-4 mr-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                        No gateway fees - Keep more of your money
                                    </li>
                                    <li class="flex items-center">
                                        <svg class="h-4 w-4 mr-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                        Easy setup - Just upload your QR code
                                    </li>
                                    <li class="flex items-center">
                                        <svg class="h-4 w-4 mr-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                        You verify each payment manually for security
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Current Status -->
                    <div class="mb-8 p-6 bg-gray-50 rounded-lg border border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Current Status</h3>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                @if($credentials->hasManualPaymentConfigured())
                                    <svg class="h-8 w-8 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                    <div class="ml-3">
                                        <p class="text-md font-semibold text-green-700">QR Code Configured</p>
                                        <p class="text-sm text-green-600">Your payment QR code is ready for hikers</p>
                                    </div>
                                @else
                                    <svg class="h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <div class="ml-3">
                                        <p class="text-md font-semibold text-gray-700">Not Configured Yet</p>
                                        <p class="text-sm text-gray-600">Upload your QR code below to get started</p>
                                    </div>
                                @endif
                            </div>
                            @if($credentials->payment_method === 'manual')
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                    Active
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Configuration Form -->
                    <form method="POST" action="{{ route('org.payment.update-manual') }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="space-y-6">
                            <!-- QR Code Upload -->
                            <div>
                                <label class="block text-md font-semibold text-gray-800 mb-3">
                                    <svg class="inline-block h-5 w-5 mr-1 text-[#336d66]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                                    </svg>
                                    Upload Your Payment QR Code <span class="text-red-500">*</span>
                                </label>
                                <p class="text-sm text-gray-600 mb-3">Upload the QR code for your GCash, PayMaya, or other e-wallet account</p>
                                
                                <!-- Preview Container -->
                                <div id="qr-preview-container" class="hidden mb-4">
                                    <div class="relative inline-block">
                                        <img id="qr-preview" src="" alt="QR Code Preview" class="mx-auto h-64 w-64 object-contain border-2 border-[#336d66] rounded-lg shadow-lg">
                                        <button type="button" onclick="clearQRPreview()" class="absolute top-2 right-2 bg-red-500 hover:bg-red-600 text-white rounded-full p-2 shadow-lg transition-colors">
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                            </svg>
                                        </button>
                                    </div>
                                    <p class="text-sm font-medium text-green-600 text-center mt-2">New QR Code Preview</p>
                                </div>
                                
                                <div id="qr-upload-area" class="mt-2 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg hover:border-[#336d66] transition-colors">
                                    <div class="space-y-1 text-center">
                                        @if($credentials->qr_code_path)
                                            <div class="mb-4" id="current-qr-display">
                                                <p class="text-sm font-medium text-gray-700 mb-2">Current QR Code:</p>
                                                @php
                                                    $qrUrl = $credentials->getQrCodeUrl();
                                                @endphp
                                                @if($qrUrl)
                                                    <img src="{{ $qrUrl }}" 
                                                         alt="Current QR Code" 
                                                         class="mx-auto h-48 w-48 object-contain border rounded shadow-md"
                                                         onerror="this.onerror=null; this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22200%22 height=%22200%22%3E%3Crect fill=%22%23f3f4f6%22 width=%22200%22 height=%22200%22/%3E%3Ctext x=%2250%25%22 y=%2250%25%22 font-size=%2214%22 text-anchor=%22middle%22 dy=%22.3em%22 fill=%22%23666%22%3EImage Not Found%3C/text%3E%3C/svg%3E'; this.parentElement.insertAdjacentHTML('afterend', '<p class=\'text-xs text-red-600 mt-2\'>Failed to load image. Please re-upload your QR code.</p>');">
                                                    <p class="text-xs text-gray-500 mt-2">{{ basename($credentials->qr_code_path) }}</p>
                                                @else
                                                    <div class="text-red-600">
                                                        <svg class="mx-auto h-12 w-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                                        </svg>
                                                        <p class="text-sm mt-2">Unable to load QR code</p>
                                                        <p class="text-xs mt-1">Please re-upload your QR code</p>
                                                    </div>
                                                @endif
                                            </div>
                                        @endif
                                        <svg id="upload-icon" class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                        <div class="flex text-sm text-gray-600">
                                            <label for="qr-code-upload" class="relative cursor-pointer bg-white rounded-md font-medium text-[#336d66] hover:text-[#2a5a54] focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-[#336d66]">
                                                <span>Upload a file</span>
                                                <input id="qr-code-upload" name="qr_code" type="file" accept="image/*" class="sr-only" onchange="previewQRCode(event)">
                                            </label>
                                            <p class="pl-1">or drag and drop</p>
                                        </div>
                                        <p class="text-xs text-gray-500">PNG, JPG, GIF up to 10MB</p>
                                    </div>
                                </div>
                                @error('qr_code')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Payment Instructions -->
                            <div>
                                <label for="manual_payment_instructions" class="block text-md font-semibold text-gray-800 mb-3">
                                    Payment Instructions (Optional)
                                </label>
                                <p class="text-sm text-gray-600 mb-2">Add any special instructions for hikers when making payments</p>
                                <textarea id="manual_payment_instructions" name="manual_payment_instructions" rows="4" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-[#336d66] focus:ring focus:ring-[#336d66] focus:ring-opacity-50" placeholder="Example: Please include your booking reference in the payment notes">{{ old('manual_payment_instructions', $credentials->manual_payment_instructions) }}</textarea>
                                <p class="mt-2 text-xs text-gray-500">This will be shown to hikers along with your QR code</p>
                            </div>

                            <!-- Submit Button -->
                            <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                                <div class="text-sm text-gray-600">
                                    <svg class="inline-block h-4 w-4 mr-1 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                    Simple and secure payment processing
                                </div>
                                <button type="submit" class="inline-flex items-center px-6 py-3 bg-[#336d66] border border-transparent rounded-lg font-semibold text-sm text-white uppercase tracking-wider hover:bg-[#2a5a54] focus:bg-[#2a5a54] active:bg-[#1f4740] focus:outline-none focus:ring-2 focus:ring-[#336d66] focus:ring-offset-2 transition ease-in-out duration-150 shadow-lg">
                                    <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    Activate Manual Payment
                                </button>
                            </div>
                        </div>
                    </form>

                    <!-- How It Works -->
                    <div class="mt-8 bg-blue-50 border border-blue-200 rounded-lg p-6">
                        <h4 class="font-semibold text-blue-900 text-lg mb-4 flex items-center">
                            <svg class="h-6 w-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            How Manual Payment Works
                        </h4>
                        <ol class="space-y-3 text-sm text-blue-900">
                            <li class="flex items-start">
                                <span class="flex items-center justify-center h-6 w-6 rounded-full bg-blue-200 text-blue-900 font-bold text-xs mr-3 flex-shrink-0">1</span>
                                <span><strong>Hiker Books:</strong> Hiker selects your trail and sees your QR code on the booking page</span>
                            </li>
                            <li class="flex items-start">
                                <span class="flex items-center justify-center h-6 w-6 rounded-full bg-blue-200 text-blue-900 font-bold text-xs mr-3 flex-shrink-0">2</span>
                                <span><strong>Hiker Pays:</strong> They scan your QR code and pay via GCash, PayMaya, or other e-wallet</span>
                            </li>
                            <li class="flex items-start">
                                <span class="flex items-center justify-center h-6 w-6 rounded-full bg-blue-200 text-blue-900 font-bold text-xs mr-3 flex-shrink-0">3</span>
                                <span><strong>Upload Proof:</strong> Hiker uploads screenshot of payment receipt and enters transaction number</span>
                            </li>
                            <li class="flex items-start">
                                <span class="flex items-center justify-center h-6 w-6 rounded-full bg-blue-200 text-blue-900 font-bold text-xs mr-3 flex-shrink-0">4</span>
                                <span><strong>You Verify:</strong> You check your e-wallet, verify the payment matches, and approve the booking</span>
                            </li>
                            <li class="flex items-start">
                                <span class="flex items-center justify-center h-6 w-6 rounded-full bg-blue-200 text-blue-900 font-bold text-xs mr-3 flex-shrink-0">5</span>
                                <span><strong>Booking Confirmed:</strong> Once approved, hiker receives booking confirmation!</span>
                            </li>
                        </ol>
                    </div>
                </div>
            </div>

            <!-- Automatic Payment Tab Content -->
            <div id="automatic-content" class="tab-content hidden bg-white rounded-b-lg shadow-lg overflow-hidden">
                <div class="p-8">
                    @include('org.payment.partials.automatic-payment-form')
                </div>
            </div>
        </div>
    </div>

    <style>
        .tab-button {
            border-color: transparent;
            color: #6b7280;
        }
        .tab-button:hover {
            border-color: #d1d5db;
            color: #374151;
        }
        .tab-button.active {
            border-color: #336d66;
            color: #336d66;
        }
        
        /* Toggle Switch Styles */
        #payment-method-toggle:checked ~ #toggle-bg {
            background-color: #10b981;
        }
        #payment-method-toggle:checked ~ #toggle-dot {
            transform: translateX(2.5rem);
        }
        #payment-method-toggle:not(:checked) ~ #toggle-bg {
            background-color: #f59e0b;
        }
    </style>

    <script>
        // Toggle switch icon and label update
        document.addEventListener('DOMContentLoaded', function() {
            const toggle = document.getElementById('payment-method-toggle');
            const toggleIcon = document.getElementById('toggle-icon');
            const toggleLabel = document.getElementById('toggle-label');
            const toggleDescription = document.getElementById('toggle-description');
            
            function updateToggleDisplay() {
                if (toggle.checked) {
                    // Automatic Payment
                    toggleIcon.innerHTML = `
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                    `;
                    toggleLabel.textContent = 'Automatic Payment';
                    toggleDescription.textContent = 'Using payment gateway';
                } else {
                    // Manual Payment
                    toggleIcon.innerHTML = `
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                    `;
                    toggleLabel.textContent = 'Manual Payment';
                    toggleDescription.textContent = 'Using QR code';
                }
            }
            
            // Initialize display
            updateToggleDisplay();
            
            // Update on change
            toggle.addEventListener('change', updateToggleDisplay);
        });

        function previewQRCode(event) {
            const file = event.target.files[0];
            if (file) {
                // Validate file type
                if (!file.type.startsWith('image/')) {
                    alert('Please upload an image file');
                    event.target.value = '';
                    return;
                }

                // Validate file size (10MB)
                if (file.size > 10 * 1024 * 1024) {
                    alert('File size must be less than 10MB');
                    event.target.value = '';
                    return;
                }

                const reader = new FileReader();
                reader.onload = function(e) {
                    // Show preview
                    const previewContainer = document.getElementById('qr-preview-container');
                    const previewImage = document.getElementById('qr-preview');
                    const currentDisplay = document.getElementById('current-qr-display');
                    const uploadIcon = document.getElementById('upload-icon');
                    
                    previewImage.src = e.target.result;
                    previewContainer.classList.remove('hidden');
                    
                    // Hide current QR and upload icon
                    if (currentDisplay) {
                        currentDisplay.style.display = 'none';
                    }
                    if (uploadIcon) {
                        uploadIcon.style.display = 'none';
                    }
                }
                reader.readAsDataURL(file);
            }
        }

        function clearQRPreview() {
            const previewContainer = document.getElementById('qr-preview-container');
            const fileInput = document.getElementById('qr-code-upload');
            const currentDisplay = document.getElementById('current-qr-display');
            const uploadIcon = document.getElementById('upload-icon');
            
            // Clear the file input
            fileInput.value = '';
            
            // Hide preview
            previewContainer.classList.add('hidden');
            
            // Show current QR and upload icon again
            if (currentDisplay) {
                currentDisplay.style.display = 'block';
            }
            if (uploadIcon) {
                uploadIcon.style.display = 'block';
            }
        }

        function switchTab(tab) {
            // Update tab buttons
            const manualTab = document.getElementById('manual-tab');
            const automaticTab = document.getElementById('automatic-tab');
            const manualContent = document.getElementById('manual-content');
            const automaticContent = document.getElementById('automatic-content');

            if (tab === 'manual') {
                manualTab.classList.add('active');
                automaticTab.classList.remove('active');
                manualContent.classList.remove('hidden');
                automaticContent.classList.add('hidden');
            } else {
                automaticTab.classList.add('active');
                manualTab.classList.remove('active');
                automaticContent.classList.remove('hidden');
                manualContent.classList.add('hidden');
            }
        }

        // Initialize - show manual tab by default
        document.addEventListener('DOMContentLoaded', function() {
            switchTab('{{ $credentials->payment_method === "automatic" ? "automatic" : "manual" }}');
        });
    </script>
</x-app-layout>

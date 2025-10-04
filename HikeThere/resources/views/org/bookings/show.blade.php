<x-app-layout>
    <x-slot name="header">
        <div class="space-y-4">
            <x-trail-breadcrumb />
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div>
                    <h2 class="font-semibold text-2xl text-gray-800 leading-tight">{{ __('Booking Details') }}</h2>
                    <p class="text-sm text-gray-600 mt-1">View and manage booking information</p>
                </div>
                <a href="{{ route('org.bookings.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 font-medium rounded-lg transition-colors shadow-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    Back to Bookings
                </a>
            </div>
        </div>
    </x-slot>

    <!-- Styled Confirmation Modal -->
    <div id="confirmModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4 backdrop-blur-sm">
        <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full transform transition-all duration-300 scale-95 opacity-0" id="confirmModalContent">
            <div id="confirmModalHeader" class="px-6 py-4 rounded-t-2xl"></div>
            <div class="p-6">
                <div id="confirmModalIcon" class="flex justify-center mb-4"></div>
                <h3 id="confirmModalTitle" class="text-xl font-bold text-gray-900 mb-3 text-center"></h3>
                <div id="confirmModalMessage" class="text-sm text-gray-700 space-y-2 mb-6"></div>
                <div id="confirmModalButtons" class="flex gap-3"></div>
            </div>
        </div>
    </div>

    <style>
        @keyframes modalSlideIn {
            from {
                transform: scale(0.95);
                opacity: 0;
            }
            to {
                transform: scale(1);
                opacity: 1;
            }
        }
        
        .modal-show #confirmModalContent {
            animation: modalSlideIn 0.3s ease-out forwards;
        }
    </style>

    <script>
        let confirmResolve = null;

        function showStyledModal(config) {
            return new Promise((resolve) => {
                confirmResolve = resolve;
                const modal = document.getElementById('confirmModal');
                const content = document.getElementById('confirmModalContent');
                const header = document.getElementById('confirmModalHeader');
                const icon = document.getElementById('confirmModalIcon');
                const title = document.getElementById('confirmModalTitle');
                const message = document.getElementById('confirmModalMessage');
                const buttons = document.getElementById('confirmModalButtons');

                // Set header color
                header.className = `px-6 py-4 rounded-t-2xl ${config.headerClass}`;
                
                // Set icon
                icon.innerHTML = config.icon;
                
                // Set title
                title.textContent = config.title;
                
                // Set message
                message.innerHTML = config.message;
                
                // Set buttons
                buttons.innerHTML = config.buttons;
                
                // Show modal
                modal.classList.remove('hidden');
                modal.classList.add('modal-show');
                setTimeout(() => {
                    content.style.transform = 'scale(1)';
                    content.style.opacity = '1';
                }, 10);
            });
        }

        function hideModal(result) {
            const modal = document.getElementById('confirmModal');
            const content = document.getElementById('confirmModalContent');
            
            content.style.transform = 'scale(0.95)';
            content.style.opacity = '0';
            
            setTimeout(() => {
                modal.classList.add('hidden');
                modal.classList.remove('modal-show');
                if (confirmResolve) {
                    confirmResolve(result);
                    confirmResolve = null;
                }
            }, 200);
        }
    </script>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-6 bg-green-50 border-l-4 border-green-500 text-green-800 px-6 py-4 rounded-r-lg shadow-sm">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-3 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <span class="font-medium">{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            <!-- Security Notice Banner -->
            <div class="mb-6 bg-blue-50 border-l-4 border-blue-500 text-blue-900 px-6 py-4 rounded-r-lg shadow-sm">
                <div class="flex items-start">
                    <svg class="w-6 h-6 mr-3 text-blue-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                    <div>
                        <h4 class="font-semibold text-sm">Privacy & Security Protected</h4>
                        <p class="text-sm mt-1">All sensitive payment information is encrypted and securely stored. Only authorized staff can access payment verification details. Hiker personal data is protected and redacted from public view.</p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 relative">
                <!-- Main Booking Information -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Booking Overview Card -->
                    <div class="bg-white overflow-hidden shadow-xl sm:rounded-xl border border-gray-200">
                        <div class="bg-gradient-to-r from-[#336d66] to-[#2a5a54] px-6 py-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h3 class="text-xl font-bold text-white">Booking #{{ $booking->id }}</h3>
                                    <p class="text-sm text-gray-100 mt-1">Created: {{ \Carbon\Carbon::parse($booking->created_at)->format('M d, Y h:i A') }}</p>
                                </div>
                                <div class="bg-white/20 rounded-lg p-3">
                                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <div class="p-6">
                            @php
                                $name = $booking->user->name ?? 'N/A';
                                $email = $booking->user->email ?? '';
                                
                                // Redact hiker information for security
                                if ($name !== 'N/A') {
                                    $nameParts = explode(' ', $name);
                                    if (count($nameParts) > 1) {
                                        $redactedName = substr($nameParts[0], 0, 1) . str_repeat('*', max(3, strlen($nameParts[0]) - 1)) . ' ' . 
                                                      substr($nameParts[count($nameParts) - 1], 0, 1) . str_repeat('*', max(3, strlen($nameParts[count($nameParts) - 1]) - 1));
                                    } else {
                                        $redactedName = substr($name, 0, 1) . str_repeat('*', max(3, strlen($name) - 1));
                                    }
                                } else {
                                    $redactedName = 'N/A';
                                }
                                
                                if (!empty($email)) {
                                    $emailParts = explode('@', $email);
                                    $localPart = $emailParts[0];
                                    $domainPart = $emailParts[1] ?? '';
                                    
                                    $redactedEmail = substr($localPart, 0, 1) . str_repeat('*', min(4, strlen($localPart) - 1)) . '@';
                                    if (!empty($domainPart)) {
                                        $domainPieces = explode('.', $domainPart);
                                        $redactedEmail .= substr($domainPieces[0], 0, 1) . str_repeat('*', min(3, strlen($domainPieces[0]) - 1));
                                        if (count($domainPieces) > 1) {
                                            $redactedEmail .= '.' . end($domainPieces);
                                        }
                                    }
                                } else {
                                    $redactedEmail = '';
                                }
                            @endphp

                            <!-- Hiker Information with Privacy Protection -->
                            <div class="bg-gradient-to-br from-gray-50 to-gray-100 rounded-lg p-4 mb-6 border border-gray-200">
                                <div class="flex items-center mb-3">
                                    <svg class="w-5 h-5 text-gray-700 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                    <h4 class="font-semibold text-gray-900">Hiker Information</h4>
                                    <span class="ml-auto inline-flex items-center px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                        </svg>
                                        Protected
                                    </span>
                                </div>
                                <div class="space-y-2">
                                    <div class="flex items-start">
                                        <span class="text-sm font-medium text-gray-600 w-24">Name:</span>
                                        <span class="text-sm text-gray-900 font-medium">{{ $redactedName }}</span>
                                    </div>
                                    <div class="flex items-start">
                                        <span class="text-sm font-medium text-gray-600 w-24">Email:</span>
                                        <span class="text-sm text-gray-900">{{ $redactedEmail }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Trail & Booking Details -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                                <div class="bg-white border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                                    <div class="flex items-center mb-2">
                                        <svg class="w-5 h-5 text-[#336d66] mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-1.447-.894L15 4m0 13V4m-13 0v9a1 1 0 001 1h1m8-4v1a1 1 0 01-1 1H9m13 0a1 1 0 01-1 1H9m13 0v-1a1 1 0 00-1-1H9" />
                                        </svg>
                                        <span class="text-xs font-semibold text-gray-500 uppercase">Trail</span>
                                    </div>
                                    <p class="text-lg font-bold text-gray-900">{{ $booking->trail->trail_name ?? 'N/A' }}</p>
                                </div>

                                <div class="bg-white border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                                    <div class="flex items-center mb-2">
                                        <svg class="w-5 h-5 text-[#336d66] mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        <span class="text-xs font-semibold text-gray-500 uppercase">Hiking Date</span>
                                    </div>
                                    <p class="text-lg font-bold text-gray-900">{{ \Carbon\Carbon::parse($booking->date)->format('M d, Y') }}</p>
                                </div>

                                <div class="bg-white border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                                    <div class="flex items-center mb-2">
                                        <svg class="w-5 h-5 text-[#336d66] mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                        </svg>
                                        <span class="text-xs font-semibold text-gray-500 uppercase">Party Size</span>
                                    </div>
                                    <p class="text-lg font-bold text-gray-900">{{ $booking->party_size }} {{ Str::plural('person', $booking->party_size) }}</p>
                                </div>

                                <div class="bg-white border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                                    <div class="flex items-center mb-2">
                                        <svg class="w-5 h-5 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <span class="text-xs font-semibold text-gray-500 uppercase">Total Amount</span>
                                    </div>
                                    <p class="text-lg font-bold text-green-600">₱{{ number_format($booking->price_cents / 100, 2) }}</p>
                                </div>
                            </div>

                            <!-- Booking Notes -->
                            @if($booking->notes)
                                <div class="bg-amber-50 border border-amber-200 rounded-lg p-4">
                                    <div class="flex items-start">
                                        <svg class="w-5 h-5 text-amber-600 mr-2 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
                                        </svg>
                                        <div>
                                            <h5 class="font-semibold text-amber-900 text-sm">Booking Notes</h5>
                                            <p class="text-sm text-amber-800 mt-1">{{ $booking->notes }}</p>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Payment Information Card -->
                    <div class="bg-white overflow-hidden shadow-xl sm:rounded-xl border border-gray-200">
                        <div class="bg-gradient-to-r from-blue-600 to-blue-500 px-6 py-4">
                            <div class="flex items-center">
                                <svg class="w-6 h-6 text-white mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                </svg>
                                <h4 class="text-lg font-bold text-white">Payment Information</h4>
                            </div>
                        </div>

                        <div class="p-6">
                            <!-- Security Notice -->
                            <div class="mb-6 bg-green-50 border-l-4 border-green-500 p-4 rounded-r-lg">
                                <div class="flex items-start">
                                    <svg class="w-5 h-5 text-green-600 mr-3 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                    </svg>
                                    <div>
                                        <p class="text-sm font-semibold text-green-900">Secure Payment Processing</p>
                                        <p class="text-xs text-green-800 mt-1">All payment data is encrypted and handled securely. Transaction details are protected and only accessible by authorized personnel.</p>
                                    </div>
                                </div>
                            </div>

                            <div class="space-y-4">
                                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                    <span class="text-sm font-semibold text-gray-700">Total Amount:</span>
                                    <span class="text-xl font-bold text-green-600">₱{{ number_format($booking->price_cents / 100, 2) }}</span>
                                </div>
                                
                                @if($booking->usesManualPayment())
                                    <div class="border border-blue-200 rounded-lg p-4 bg-blue-50">
                                        <div class="flex items-center mb-3">
                                            <svg class="w-5 h-5 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                                            </svg>
                                            <span class="font-semibold text-blue-900">Payment Method</span>
                                        </div>
                                        <span class="inline-flex items-center px-3 py-1.5 text-sm font-semibold rounded-lg bg-blue-600 text-white shadow-sm">
                                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                                            </svg>
                                            Manual Payment (QR Code)
                                        </span>
                                    </div>
                                    
                                    @if($booking->transaction_number)
                                        <div class="border-2 border-orange-200 rounded-lg p-4 bg-gradient-to-br from-orange-50 to-yellow-50">
                                            <div class="flex items-center justify-between mb-3">
                                                <div class="flex items-center">
                                                    <svg class="w-5 h-5 text-orange-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                    </svg>
                                                    <span class="text-sm font-bold text-orange-900">Transaction Number</span>
                                                </div>
                                                <span class="inline-flex items-center px-2 py-0.5 text-xs font-bold rounded-full bg-orange-600 text-white">
                                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                                    </svg>
                                                    SECURED
                                                </span>
                                            </div>
                                            <div class="relative group">
                                                <div class="bg-white border-2 border-orange-300 rounded-lg p-3 shadow-sm">
                                                    <div class="flex items-center justify-between mb-2">
                                                        <span class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Reference ID:</span>
                                                        <button type="button" onclick="toggleTransactionNumber(this)" class="text-xs font-medium px-2 py-1 bg-orange-100 hover:bg-orange-200 text-orange-800 rounded transition-colors">
                                                            <svg class="w-3 h-3 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                            </svg>
                                                            <span class="button-text">Show</span>
                                                        </button>
                                                    </div>
                                                    <div class="transaction-number font-mono text-lg font-bold text-gray-900 tracking-wider select-all blur-sm transition-all duration-300">
                                                        {{ $booking->transaction_number }}
                                                    </div>
                                                </div>
                                                <p class="text-xs text-orange-700 mt-2 flex items-center">
                                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                                    </svg>
                                                    Click "Show" to reveal sensitive transaction reference
                                                </p>
                                            </div>
                                            <script>
                                                function toggleTransactionNumber(button) {
                                                    const container = button.closest('.bg-white');
                                                    const transactionDiv = container.querySelector('.transaction-number');
                                                    const buttonText = button.querySelector('.button-text');
                                                    
                                                    if (transactionDiv) {
                                                        transactionDiv.classList.toggle('blur-sm');
                                                        buttonText.textContent = transactionDiv.classList.contains('blur-sm') ? 'Show' : 'Hide';
                                                    }
                                                }
                                            </script>
                                        </div>
                                    @endif
                                    
                                    @if($booking->payment_notes)
                                        <div class="border border-gray-200 rounded-lg p-4 bg-gray-50">
                                            <span class="text-sm font-semibold text-gray-700 block mb-2">Payment Notes:</span>
                                            <p class="text-sm text-gray-900">{{ $booking->payment_notes }}</p>
                                        </div>
                                    @endif
                                    
                                    <div class="border-t border-gray-200 pt-4 mt-4">
                                        <div class="flex items-center justify-between mb-2">
                                            <span class="text-sm font-semibold text-gray-700">Payment Status:</span>
                                            @if($booking->payment_status === 'pending')
                                                <span class="inline-flex items-center px-3 py-1.5 text-sm font-semibold rounded-lg bg-orange-100 text-orange-800 shadow-sm">
                                                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                    </svg>
                                                    Pending Verification
                                                </span>
                                            @elseif($booking->payment_status === 'verified')
                                                <span class="inline-flex items-center px-3 py-1.5 text-sm font-semibold rounded-lg bg-green-100 text-green-800 shadow-sm">
                                                    <svg class="w-4 h-4 mr-1.5" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                    </svg>
                                                    Verified
                                                </span>
                                            @elseif($booking->payment_status === 'rejected')
                                                <span class="inline-flex items-center px-3 py-1.5 text-sm font-semibold rounded-lg bg-red-100 text-red-800 shadow-sm">
                                                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                    </svg>
                                                    Rejected
                                                </span>
                                            @else
                                                <span class="inline-flex px-3 py-1.5 text-sm font-semibold rounded-lg bg-gray-100 text-gray-800 shadow-sm">{{ ucfirst($booking->payment_status ?? 'N/A') }}</span>
                                            @endif
                                        </div>
                                        
                                        @if($booking->payment_verified_at)
                                            <div class="text-xs text-gray-600 mt-2">
                                                <svg class="inline w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                Verified on {{ \Carbon\Carbon::parse($booking->payment_verified_at)->format('M d, Y h:i A') }}
                                            </div>
                                        @endif
                                    </div>
                                    
                                    <!-- Payment Proof Display with Enhanced Security -->
                                    @if($booking->payment_proof_path)
                                        <div class="border-t border-gray-200 pt-4 mt-4">
                                            <div class="bg-red-50 border-l-4 border-red-500 rounded-r-lg p-4 mb-4">
                                                <div class="flex items-start">
                                                    <svg class="w-6 h-6 text-red-600 mr-3 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                                    </svg>
                                                    <div class="flex-1">
                                                        <h5 class="text-sm font-bold text-red-900 mb-1">⚠️ Highly Confidential Payment Proof</h5>
                                                        <p class="text-xs text-red-800 mb-2">This image contains <strong>sensitive financial and personal information</strong>. Unauthorized access, disclosure, or distribution is strictly prohibited.</p>
                                                        <div class="flex items-start mt-2">
                                                            <svg class="w-4 h-4 text-red-700 mr-2 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                                                <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
                                                            </svg>
                                                            <p class="text-xs text-red-800 font-medium">Protected by privacy regulations • View only for verification purposes</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="bg-gradient-to-br from-gray-900 to-gray-800 rounded-lg p-4 mb-4">
                                                <div class="flex items-center justify-between">
                                                    <div class="flex items-center">
                                                        <div class="bg-yellow-400 rounded-full p-2 mr-3">
                                                            <svg class="w-5 h-5 text-gray-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                                                            </svg>
                                                        </div>
                                                        <div>
                                                            <h6 class="text-sm font-bold text-white">Secure Payment Proof</h6>
                                                            <p class="text-xs text-gray-300">Click to view in secure mode</p>
                                                        </div>
                                                    </div>
                                                    <span class="inline-flex items-center px-3 py-1 text-xs font-bold rounded-full bg-red-600 text-white">
                                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                                        </svg>
                                                        RESTRICTED
                                                    </span>
                                                </div>
                                            </div>
                                            
                                            <a href="{{ asset('storage/' . $booking->payment_proof_path) }}" target="_blank" class="block group relative">
                                                <!-- Blur Overlay -->
                                                <div class="relative overflow-hidden rounded-lg border-4 border-red-400 shadow-xl">
                                                    <!-- Secure Badge Overlay -->
                                                    <div class="absolute top-0 left-0 right-0 bg-gradient-to-b from-black/80 to-transparent z-10 p-3">
                                                        <div class="flex items-center justify-between">
                                                            <span class="inline-flex items-center px-2 py-1 text-xs font-bold rounded bg-red-600 text-white">
                                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                                </svg>
                                                                CONFIDENTIAL
                                                            </span>
                                                            <span class="text-xs font-mono text-white bg-black/50 px-2 py-1 rounded">ID: {{ $booking->id }}</span>
                                                        </div>
                                                    </div>
                                                    
                                                    <!-- Blurred Image -->
                                                    <img src="{{ asset('storage/' . $booking->payment_proof_path) }}" alt="Secure Payment Proof" class="w-full h-auto blur-sm group-hover:blur-none transition-all duration-500">
                                                    
                                                    <!-- Center Overlay -->
                                                    <div class="absolute inset-0 bg-black/60 group-hover:bg-black/20 transition-all duration-500 flex items-center justify-center z-20 group-hover:opacity-0">
                                                        <div class="text-center">
                                                            <div class="bg-white rounded-full p-6 mb-3 inline-block">
                                                                <svg class="w-12 h-12 text-gray-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                                </svg>
                                                            </div>
                                                            <p class="text-white font-bold text-sm mb-1">Hover to Preview</p>
                                                            <p class="text-gray-300 text-xs">Click to open securely</p>
                                                        </div>
                                                    </div>
                                                    
                                                    <!-- Bottom Security Info -->
                                                    <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/80 to-transparent z-10 p-3">
                                                        <div class="flex items-center justify-between">
                                                            <span class="text-xs text-white font-medium flex items-center">
                                                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                                    <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
                                                                </svg>
                                                                Encrypted Storage
                                                            </span>
                                                            <span class="text-xs text-gray-300 font-mono">{{ \Carbon\Carbon::now()->format('Y-m-d') }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </a>
                                            
                                            <div class="mt-4 bg-gray-50 border border-gray-200 rounded-lg p-3">
                                                <div class="flex items-start">
                                                    <svg class="w-5 h-5 text-blue-600 mr-2 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                    </svg>
                                                    <div class="text-xs text-gray-700 leading-relaxed">
                                                        <p class="font-semibold mb-1">Viewing Instructions:</p>
                                                        <ul class="list-disc list-inside space-y-0.5 text-gray-600">
                                                            <li>Image is blurred by default for security</li>
                                                            <li>Hover over the image to preview content</li>
                                                            <li>Click to open in new secure tab for full verification</li>
                                                            <li>Do not screenshot or share this information</li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                    
                                @elseif($booking->payment)
                                    <div class="border border-purple-200 rounded-lg p-4 bg-purple-50">
                                        <div class="flex items-center mb-3">
                                            <svg class="w-5 h-5 text-purple-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                            </svg>
                                            <span class="font-semibold text-purple-900">Payment Method</span>
                                        </div>
                                        <span class="inline-flex items-center px-3 py-1.5 text-sm font-semibold rounded-lg bg-purple-600 text-white shadow-sm">
                                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                            </svg>
                                            Automatic Payment Gateway
                                        </span>
                                    </div>
                                    
                                    <div class="border-t border-gray-200 pt-4 mt-4">
                                        <div class="flex items-center justify-between mb-2">
                                            <span class="text-sm font-semibold text-gray-700">Payment Status:</span>
                                            @if($booking->payment->isPaid())
                                                <span class="inline-flex items-center px-3 py-1.5 text-sm font-semibold rounded-lg bg-green-100 text-green-800 shadow-sm">
                                                    <svg class="w-4 h-4 mr-1.5" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                    </svg>
                                                    Paid
                                                </span>
                                            @elseif($booking->payment->isPending())
                                                <span class="inline-flex items-center px-3 py-1.5 text-sm font-semibold rounded-lg bg-yellow-100 text-yellow-800 shadow-sm">Pending</span>
                                            @else
                                                <span class="inline-flex items-center px-3 py-1.5 text-sm font-semibold rounded-lg bg-red-100 text-red-800 shadow-sm">{{ ucfirst($booking->payment->payment_status) }}</span>
                                            @endif
                                        </div>
                                        
                                        @if($booking->payment->isPaid() && $booking->payment->paid_at)
                                            <div class="text-xs text-gray-600 mt-2">
                                                <svg class="inline w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                Paid on {{ $booking->payment->paid_at->format('M d, Y h:i A') }}
                                            </div>
                                        @endif
                                    </div>
                                    
                                    @if($booking->payment->paymongo_payment_id)
                                        <div class="border border-gray-200 rounded-lg p-4 bg-gray-50 mt-4">
                                            <div class="flex items-center justify-between">
                                                <span class="text-sm font-semibold text-gray-700">Payment ID:</span>
                                                <span class="font-mono text-xs bg-white px-3 py-1 rounded border border-gray-300 text-gray-900">{{ Str::limit($booking->payment->paymongo_payment_id, 20) }}</span>
                                            </div>
                                        </div>
                                    @endif
                                    
                                @else
                                    <div class="border border-gray-300 rounded-lg p-4 bg-gray-50 text-center">
                                        <svg class="w-12 h-12 text-gray-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <span class="inline-flex px-3 py-1.5 text-sm font-semibold rounded-lg bg-gray-200 text-gray-800 shadow-sm">No Payment Record</span>
                                        <p class="text-xs text-gray-600 mt-2">Payment information not yet available</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar - Status Management (Sticky) -->
                <div class="lg:col-span-1">
                    <div class="bg-white overflow-hidden shadow-xl sm:rounded-xl border border-gray-200 sticky" style="top: 14rem;">
                        <div class="bg-gradient-to-r from-gray-700 to-gray-600 px-6 py-4">
                            <h4 class="font-bold text-white flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                </svg>
                                Booking Status
                            </h4>
                        </div>

                        <div class="p-6">
                            <div class="mb-6">
                                <p class="text-sm font-medium text-gray-700 mb-3">Current Status</p>
                                <div class="flex justify-center">
                                    <span class="inline-flex px-4 py-2 text-sm font-bold rounded-lg shadow-md {{ $booking->status === 'confirmed' ? 'bg-green-100 text-green-800 border-2 border-green-300' : ($booking->status === 'cancelled' ? 'bg-red-100 text-red-800 border-2 border-red-300' : ($booking->status === 'completed' ? 'bg-blue-100 text-blue-800 border-2 border-blue-300' : 'bg-yellow-100 text-yellow-800 border-2 border-yellow-300')) }}">
                                        {{ ucfirst($booking->status) }}
                                    </span>
                                </div>
                            </div>

                            <form id="status-update-form" method="POST" action="{{ route('org.bookings.update-status', $booking) }}" class="space-y-4" onsubmit="return confirmStatusChange(event)">
                                @csrf
                                @method('PATCH')

                                <div>
                                    <label for="status" class="block text-sm font-semibold text-gray-700 mb-2">Update Status</label>
                                    <select id="status" name="status" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-[#336d66] focus:ring-2 focus:ring-[#336d66] transition-all">
                                        <option value="pending" {{ $booking->status === 'pending' ? 'selected' : '' }}>⏳ Pending</option>
                                        <option value="confirmed" {{ $booking->status === 'confirmed' ? 'selected' : '' }}>✅ Confirmed</option>
                                        <option value="cancelled" {{ $booking->status === 'cancelled' ? 'selected' : '' }}>❌ Cancelled</option>
                                        <option value="completed" {{ $booking->status === 'completed' ? 'selected' : '' }}>🎉 Completed</option>
                                    </select>
                                </div>

                                <button type="submit" class="w-full inline-flex items-center justify-center px-5 py-3 border border-transparent shadow-md text-sm font-semibold rounded-lg text-white bg-[#336d66] hover:bg-[#2a5a54] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#336d66] transition-all">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                    </svg>
                                    Update Status
                                </button>
                            </form>

                            <script>
                                async function confirmStatusChange(event) {
                                    event.preventDefault();
                                    
                                    const select = document.getElementById('status');
                                    const newStatus = select.value;
                                    const currentStatus = '{{ $booking->status }}';
                                    
                                    if (newStatus === currentStatus) {
                                        await showStyledModal({
                                            headerClass: 'bg-gradient-to-r from-yellow-500 to-orange-500',
                                            icon: '<div class="w-16 h-16 bg-yellow-100 rounded-full flex items-center justify-center mx-auto"><svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg></div>',
                                            title: '⚠️ Status Unchanged',
                                            message: '<p class="text-center">The selected status is the same as the current status.<br>No changes will be made.</p>',
                                            buttons: '<button onclick="hideModal(false)" class="w-full px-6 py-3 bg-gray-600 hover:bg-gray-700 text-white font-semibold rounded-lg transition-all">OK</button>'
                                        });
                                        return false;
                                    }
                                    
                                    let statusEmoji = '', statusName = '', warningMessage = '', iconColor = '', headerClass = '';
                                    
                                    switch(newStatus) {
                                        case 'pending':
                                            statusEmoji = '⏳';
                                            statusName = 'PENDING';
                                            warningMessage = 'The booking will be marked as awaiting confirmation.';
                                            iconColor = 'yellow';
                                            headerClass = 'bg-gradient-to-r from-yellow-500 to-yellow-600';
                                            break;
                                        case 'confirmed':
                                            statusEmoji = '✅';
                                            statusName = 'CONFIRMED';
                                            warningMessage = 'The hiker will be notified that their booking is confirmed.<br>This indicates the booking is approved and ready.';
                                            iconColor = 'green';
                                            headerClass = 'bg-gradient-to-r from-green-500 to-green-600';
                                            break;
                                        case 'cancelled':
                                            statusEmoji = '❌';
                                            statusName = 'CANCELLED';
                                            warningMessage = '<strong class="text-red-600">⚠️ WARNING:</strong> This action will cancel the booking.<br>The hiker will be notified of the cancellation.<br>This action should only be used for cancellations.';
                                            iconColor = 'red';
                                            headerClass = 'bg-gradient-to-r from-red-500 to-red-600';
                                            break;
                                        case 'completed':
                                            statusEmoji = '🎉';
                                            statusName = 'COMPLETED';
                                            warningMessage = 'The booking will be marked as completed.<br>This indicates the hike has been finished.';
                                            iconColor = 'blue';
                                            headerClass = 'bg-gradient-to-r from-blue-500 to-blue-600';
                                            break;
                                    }
                                    
                                    const result = await showStyledModal({
                                        headerClass: headerClass,
                                        icon: `<div class="w-20 h-20 bg-${iconColor}-100 rounded-full flex items-center justify-center mx-auto text-4xl">${statusEmoji}</div>`,
                                        title: `Change Booking Status`,
                                        message: `
                                            <div class="bg-gray-50 rounded-lg p-4 mb-4">
                                                <div class="flex justify-between mb-2">
                                                    <span class="font-semibold text-gray-600">Current Status:</span>
                                                    <span class="font-bold text-gray-900 uppercase">${currentStatus}</span>
                                                </div>
                                                <div class="flex justify-between">
                                                    <span class="font-semibold text-gray-600">New Status:</span>
                                                    <span class="font-bold text-${iconColor}-600 uppercase">${statusName}</span>
                                                </div>
                                            </div>
                                            <p class="text-sm mb-3">${warningMessage}</p>
                                            <div class="bg-blue-50 border-l-4 border-blue-500 p-3 text-xs">
                                                <p><strong>Booking ID:</strong> #{{ $booking->id }}</p>
                                                <p><strong>Trail:</strong> {{ $booking->trail->trail_name ?? 'N/A' }}</p>
                                                <p><strong>Date:</strong> {{ \Carbon\Carbon::parse($booking->date)->format('M d, Y') }}</p>
                                            </div>
                                            <p class="text-center font-semibold mt-4 text-gray-700">Are you sure you want to proceed?</p>
                                        `,
                                        buttons: `
                                            <button onclick="hideModal(false)" class="flex-1 px-6 py-3 bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold rounded-lg transition-all">
                                                Cancel
                                            </button>
                                            <button onclick="hideModal(true)" class="flex-1 px-6 py-3 bg-${iconColor}-600 hover:bg-${iconColor}-700 text-white font-semibold rounded-lg transition-all">
                                                Confirm Change
                                            </button>
                                        `
                                    });
                                    
                                    if (result) {
                                        event.target.submit();
                                    }
                                    
                                    return false;
                                }
                            </script>

                            <div class="mt-6 pt-6 border-t border-gray-200">
                                <p class="text-xs text-gray-600 leading-relaxed">
                                    <svg class="inline w-4 h-4 mr-1 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Changes to booking status will be immediately reflected and may trigger notifications to the hiker.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Verification Actions (Below Booking Status - Sticky) -->
                    @if($booking->isPaymentPendingVerification())
                        <div class="bg-orange-50 overflow-hidden shadow-xl sm:rounded-xl border-2 border-orange-200 mt-6 sticky" style="top: 42rem;">
                            <div class="bg-gradient-to-r from-orange-600 to-orange-500 px-6 py-4">
                                <div class="flex items-center">
                                    <svg class="w-6 h-6 text-white mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                    <h4 class="text-lg font-bold text-white">Action Required</h4>
                                </div>
                            </div>
                            
                            <div class="p-6">
                                <p class="text-sm text-orange-900 font-medium mb-4">Please verify the payment proof and transaction details before approving.</p>
                                
                                <div class="space-y-3">
                                    <form id="verify-payment-form" method="POST" action="{{ route('org.bookings.verify-payment', $booking) }}" onsubmit="return confirmPaymentVerification(event)">
                                        @csrf
                                        <button type="submit" class="w-full px-5 py-3 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg transition-all shadow-md hover:shadow-lg flex items-center justify-center">
                                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            Verify Payment
                                        </button>
                                    </form>
                                    
                                    <form id="reject-payment-form" method="POST" action="{{ route('org.bookings.reject-payment', $booking) }}" onsubmit="return confirmPaymentRejection(event)">
                                        @csrf
                                        <button type="submit" class="w-full px-5 py-3 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-lg transition-all shadow-md hover:shadow-lg flex items-center justify-center">
                                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            Reject Payment
                                        </button>
                                    </form>
                                </div>
                            </div>
                            
                            <script>
                                async function confirmPaymentVerification(event) {
                                    event.preventDefault();
                                    
                                    const result = await showStyledModal({
                                        headerClass: 'bg-gradient-to-r from-green-500 to-green-600',
                                        icon: '<div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto"><svg class="w-10 h-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div>',
                                        title: '✅ Verify Payment',
                                        message: `
                                            <div class="bg-yellow-50 border-l-4 border-yellow-500 p-4 mb-4">
                                                <p class="font-bold text-yellow-900 mb-2">⚠️ IMPORTANT: Please confirm you have:</p>
                                                <ul class="text-sm text-yellow-800 space-y-1 ml-4">
                                                    <li>✓ Reviewed the payment proof image</li>
                                                    <li>✓ Verified the transaction number matches</li>
                                                    <li>✓ Confirmed the payment amount is correct</li>
                                                    <li>✓ Checked the payment date is valid</li>
                                                </ul>
                                            </div>
                                            <div class="bg-gray-50 rounded-lg p-4 mb-4">
                                                <p class="font-semibold text-gray-700 mb-2">Booking Details:</p>
                                                <div class="text-sm space-y-1">
                                                    <p><strong>Booking ID:</strong> #{{ $booking->id }}</p>
                                                    <p><strong>Amount:</strong> <span class="text-green-600 font-bold">₱{{ number_format($booking->price_cents / 100, 2) }}</span></p>
                                                    <p><strong>Trail:</strong> {{ $booking->trail->trail_name ?? 'N/A' }}</p>
                                                    <p><strong>Hike Date:</strong> {{ \Carbon\Carbon::parse($booking->date)->format('M d, Y') }}</p>
                                                </div>
                                            </div>
                                            <div class="bg-green-50 border border-green-200 rounded-lg p-3 text-sm">
                                                <p class="font-semibold text-green-900 mb-1">This action will:</p>
                                                <ul class="text-green-800 space-y-0.5 ml-4">
                                                    <li>✓ APPROVE the payment</li>
                                                    <li>✓ Notify the hiker of verification</li>
                                                    <li>✓ Mark the booking as PAID</li>
                                                </ul>
                                            </div>
                                            <p class="text-center font-semibold mt-4 text-gray-700">Are you absolutely sure?</p>
                                        `,
                                        buttons: `
                                            <button onclick="hideModal(false)" class="flex-1 px-6 py-3 bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold rounded-lg transition-all">
                                                Cancel
                                            </button>
                                            <button onclick="hideModal(true)" class="flex-1 px-6 py-3 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg transition-all">
                                                Verify Payment
                                            </button>
                                        `
                                    });
                                    
                                    if (result) {
                                        event.target.submit();
                                    }
                                    
                                    return false;
                                }
                                
                                async function confirmPaymentRejection(event) {
                                    event.preventDefault();
                                    
                                    const firstConfirm = await showStyledModal({
                                        headerClass: 'bg-gradient-to-r from-red-500 to-red-600',
                                        icon: '<div class="w-20 h-20 bg-red-100 rounded-full flex items-center justify-center mx-auto"><svg class="w-10 h-10 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg></div>',
                                        title: '❌ Reject Payment',
                                        message: `
                                            <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-4">
                                                <p class="font-bold text-red-900 mb-1">⚠️ WARNING: This is a critical action!</p>
                                                <p class="text-sm text-red-700">Please ensure you have valid reasons for rejection.</p>
                                            </div>
                                            <div class="bg-gray-50 rounded-lg p-4 mb-4">
                                                <p class="font-semibold text-gray-700 mb-2">Booking Details:</p>
                                                <div class="text-sm space-y-1">
                                                    <p><strong>Booking ID:</strong> #{{ $booking->id }}</p>
                                                    <p><strong>Amount:</strong> <span class="text-red-600 font-bold">₱{{ number_format($booking->price_cents / 100, 2) }}</span></p>
                                                    <p><strong>Trail:</strong> {{ $booking->trail->trail_name ?? 'N/A' }}</p>
                                                    <p><strong>Hike Date:</strong> {{ \Carbon\Carbon::parse($booking->date)->format('M d, Y') }}</p>
                                                </div>
                                            </div>
                                            <div class="bg-red-50 border border-red-200 rounded-lg p-3 text-sm mb-3">
                                                <p class="font-semibold text-red-900 mb-1">Consequences of Rejection:</p>
                                                <ul class="text-red-800 space-y-0.5 ml-4">
                                                    <li>✗ Payment will be marked as REJECTED</li>
                                                    <li>✗ Hiker will be notified immediately</li>
                                                    <li>✗ Hiker must submit NEW payment proof</li>
                                                    <li>✗ Booking will remain unconfirmed</li>
                                                </ul>
                                            </div>
                                            <div class="bg-amber-50 border border-amber-200 rounded-lg p-3 text-xs">
                                                <p class="font-semibold text-amber-900 mb-1">Common rejection reasons:</p>
                                                <ul class="text-amber-800 space-y-0.5 ml-4">
                                                    <li>• Incorrect payment amount</li>
                                                    <li>• Invalid transaction number</li>
                                                    <li>• Unclear payment proof image</li>
                                                    <li>• Payment date mismatch</li>
                                                </ul>
                                            </div>
                                        `,
                                        buttons: `
                                            <button onclick="hideModal(false)" class="flex-1 px-6 py-3 bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold rounded-lg transition-all">
                                                Cancel
                                            </button>
                                            <button onclick="hideModal(true)" class="flex-1 px-6 py-3 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-lg transition-all">
                                                Continue
                                            </button>
                                        `
                                    });
                                    
                                    if (!firstConfirm) {
                                        return false;
                                    }
                                    
                                    // Double confirmation
                                    const finalConfirm = await showStyledModal({
                                        headerClass: 'bg-gradient-to-r from-red-600 to-red-700',
                                        icon: '<div class="w-20 h-20 bg-red-100 rounded-full flex items-center justify-center mx-auto animate-pulse"><svg class="w-10 h-10 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div>',
                                        title: '⚠️ Final Confirmation',
                                        message: `
                                            <p class="text-center text-gray-700 mb-4">This will reject the payment and notify the hiker.</p>
                                            <div class="bg-red-100 border-2 border-red-500 rounded-lg p-4 text-center">
                                                <p class="font-bold text-red-900 text-lg">Are you absolutely sure?</p>
                                                <p class="text-sm text-red-700 mt-2">This action cannot be easily undone.</p>
                                            </div>
                                        `,
                                        buttons: `
                                            <button onclick="hideModal(false)" class="flex-1 px-6 py-3 bg-gray-600 hover:bg-gray-700 text-white font-semibold rounded-lg transition-all">
                                                Go Back
                                            </button>
                                            <button onclick="hideModal(true)" class="flex-1 px-6 py-3 bg-red-600 hover:bg-red-700 text-white font-bold rounded-lg transition-all">
                                                Reject Payment
                                            </button>
                                        `
                                    });
                                    
                                    if (finalConfirm) {
                                        event.target.submit();
                                    }
                                    
                                    return false;
                                }
                            </script>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- AJAX Handler for Organization Booking Actions --}}
    <script>
        // Override form submissions to use AJAX
        document.addEventListener('DOMContentLoaded', function() {
            // Handle status update form
            const statusForm = document.getElementById('status-update-form');
            if (statusForm) {
                const originalSubmit = statusForm.onsubmit;
                statusForm.onsubmit = async function(e) {
                    // Call the confirmation first
                    const confirmed = await originalSubmit.call(this, e);
                    if (confirmed === false) return false;
                    
                    e.preventDefault();
                    handleAjaxFormSubmit(this, 'Status updated successfully!');
                    return false;
                };
            }

            // Handle verify payment form
            const verifyForm = document.getElementById('verify-payment-form');
            if (verifyForm) {
                const originalSubmit = verifyForm.onsubmit;
                verifyForm.onsubmit = async function(e) {
                    const confirmed = await originalSubmit.call(this, e);
                    if (confirmed === false) return false;
                    
                    e.preventDefault();
                    handleAjaxFormSubmit(this, 'Payment verified successfully!');
                    return false;
                };
            }

            // Handle reject payment form
            const rejectForm = document.getElementById('reject-payment-form');
            if (rejectForm) {
                const originalSubmit = rejectForm.onsubmit;
                rejectForm.onsubmit = async function(e) {
                    const confirmed = await originalSubmit.call(this, e);
                    if (confirmed === false) return false;
                    
                    e.preventDefault();
                    handleAjaxFormSubmit(this, 'Payment rejected. Hiker will be notified.');
                    return false;
                };
            }
        });

        function handleAjaxFormSubmit(form, successMessage) {
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalBtnHtml = submitBtn.innerHTML;
            
            // Show loading state
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<svg class="animate-spin h-5 w-5 mr-2 inline-block" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Processing...';
            
            // Prepare form data
            const formData = new FormData(form);
            
            // Submit via AJAX
            fetch(form.action, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Show success message
                    showToast('success', data.message || successMessage);
                    
                    // Reload page after short delay to reflect changes
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                } else {
                    throw new Error(data.message || 'Action failed');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('error', error.message || 'An error occurred. Please try again.');
                
                // Reset button
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalBtnHtml;
            });
        }

        function showToast(type, message) {
            const toastDiv = document.createElement('div');
            const bgColor = type === 'success' ? 'bg-green-50 border-green-500' : 'bg-red-50 border-red-500';
            const iconColor = type === 'success' ? 'text-green-500' : 'text-red-500';
            const textColor = type === 'success' ? 'text-green-800' : 'text-red-800';
            const icon = type === 'success' 
                ? '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>'
                : '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>';
            
            toastDiv.className = `fixed top-4 right-4 z-50 p-4 ${bgColor} border-l-4 rounded-r-lg shadow-lg max-w-md`;
            toastDiv.innerHTML = `
                <div class="flex items-center">
                    <svg class="w-6 h-6 ${iconColor} mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        ${icon}
                    </svg>
                    <div class="flex-1">
                        <p class="${textColor} font-semibold">${message}</p>
                    </div>
                    <button onclick="this.parentElement.parentElement.remove()" class="ml-4 ${textColor} hover:${textColor} opacity-70 hover:opacity-100">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            `;
            document.body.appendChild(toastDiv);
            
            // Auto-remove after 5 seconds
            setTimeout(() => toastDiv.remove(), 5000);
        }
    </script>
</x-app-layout>
<x-app-layout>
    <div class="bg-gradient-to-br from-slate-50 via-blue-50 to-emerald-50 min-h-screen py-12">
        <div class="max-w-6xl mx-auto px-4 md:px-8">
            <!-- Header Section -->
            <div class="text-center mb-10 animate-fade-in">
                <div class="inline-block mb-4">
                    <div class="bg-gradient-to-r from-emerald-500 to-blue-600 text-white px-4 py-1.5 rounded-full text-sm font-semibold shadow-lg">
                        Reservation Details
                    </div>
                </div>
                <h1 class="text-4xl md:text-3xl font-extrabold bg-gradient-to-r from-gray-800 via-gray-700 to-gray-800 bg-clip-text text-transparent mb-3">
                    Booking Details
                </h1>
                <p class="text-gray-600 text-lg max-w-2xl mx-auto">Review your reservation and payment information</p>
            </div>

            <div class="bg-white rounded-3xl shadow-2xl overflow-hidden border border-gray-200 p-8 max-w-4xl mx-auto transform hover:shadow-3xl transition-all duration-300">
                <!-- Payment Status Alert -->
                @if($booking->usesManualPayment())
                    @if($booking->payment_status === 'pending')
                        <div class="mb-8 bg-gradient-to-r from-orange-50 to-amber-50 border-l-4 border-orange-500 p-6 rounded-xl shadow-md hover:shadow-lg transition-shadow duration-300">
                            <div class="flex items-start">
                                <div class="flex-shrink-0 bg-orange-100 rounded-full p-2 mr-4">
                                    <svg class="h-7 w-7 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <h3 class="text-base font-bold text-orange-900 mb-2 flex items-center">
                                        Payment Pending Verification
                                        <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-200 text-orange-800 animate-pulse">
                                            Awaiting Review
                                        </span>
                                    </h3>
                                    <p class="text-sm text-orange-800 leading-relaxed">Your payment proof has been submitted and is awaiting verification by the organization. You will be notified once it's approved.</p>
                                </div>
                            </div>
                        </div>
                    @elseif($booking->payment_status === 'verified')
                        <div class="mb-8 bg-gradient-to-r from-green-50 to-emerald-50 border-l-4 border-green-500 p-6 rounded-xl shadow-md hover:shadow-lg transition-shadow duration-300">
                            <div class="flex items-start">
                                <div class="flex-shrink-0 bg-green-100 rounded-full p-2 mr-4">
                                    <svg class="h-7 w-7 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <h3 class="text-base font-bold text-green-900 mb-2 flex items-center">
                                        Payment Verified - Booking Confirmed! 
                                        <span class="ml-2 inline-flex items-center">
                                            <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                            </svg>
                                        </span>
                                    </h3>
                                    <p class="text-sm text-green-800 leading-relaxed mb-2">Your payment has been verified. Your booking is confirmed and ready. Enjoy your hike!</p>
                                    @if($booking->payment_verified_at)
                                        <p class="text-xs text-green-700 font-medium bg-green-100 inline-block px-3 py-1 rounded-full">
                                            ✓ Verified on {{ \Carbon\Carbon::parse($booking->payment_verified_at)->format('F j, Y g:i A') }}
                                        </p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @elseif($booking->payment_status === 'rejected')
                        <div class="mb-8 bg-gradient-to-r from-red-50 to-rose-50 border-l-4 border-red-500 p-6 rounded-xl shadow-md hover:shadow-lg transition-shadow duration-300">
                            <div class="flex items-start">
                                <div class="flex-shrink-0 bg-red-100 rounded-full p-2 mr-4">
                                    <svg class="h-7 w-7 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <h3 class="text-base font-bold text-red-900 mb-2 flex items-center">
                                        Payment Rejected - Action Required
                                        <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-200 text-red-800">
                                            Action Needed
                                        </span>
                                    </h3>
                                    <p class="text-sm text-red-800 leading-relaxed mb-4">Unfortunately, your payment proof was not accepted. Please submit a new payment proof or contact the organization for assistance.</p>
                                    <div class="mt-3">
                                        <a href="{{ route('booking.edit', $booking) }}" class="inline-flex items-center px-5 py-2.5 bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 text-white text-sm font-semibold rounded-lg shadow-md hover:shadow-lg transition-all duration-300 transform hover:-translate-y-0.5">
                                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                                            </svg>
                                            Resubmit Payment Proof
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                @endif

                <!-- Booking Header -->
                <div class="mb-8 pb-6 border-b-2 border-gray-100">
                    <div class="flex items-start justify-between">
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900 mb-2 flex items-center">
                                <svg class="w-7 h-7 mr-3 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                {{ $booking->trail?->trail_name ?? 'N/A' }}
                            </h2>
                            <div class="flex items-center space-x-3 text-sm text-gray-600">
                                <span class="inline-flex items-center font-mono bg-gray-100 px-3 py-1 rounded-full">
                                    <svg class="w-4 h-4 mr-1.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"/>
                                    </svg>
                                    {{ $booking->id }}
                                </span>
                                <span class="inline-flex items-center px-3 py-1 rounded-full font-semibold
                                    @if($booking->status === 'confirmed') bg-green-100 text-green-800
                                    @elseif($booking->status === 'pending') bg-yellow-100 text-yellow-800
                                    @elseif($booking->status === 'cancelled') bg-red-100 text-red-800
                                    @else bg-gray-100 text-gray-800
                                    @endif">
                                    <span class="w-2 h-2 rounded-full mr-2
                                        @if($booking->status === 'confirmed') bg-green-500
                                        @elseif($booking->status === 'pending') bg-yellow-500
                                        @elseif($booking->status === 'cancelled') bg-red-500
                                        @else bg-gray-500
                                        @endif"></span>
                                    {{ ucfirst($booking->status) }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Booking Details Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                    <!-- Date Card -->
                    <div class="p-6 border-2 border-gray-200 rounded-xl hover:border-blue-300 hover:shadow-lg transition-all duration-300 bg-gradient-to-br from-white to-blue-50">
                        <div class="flex items-center mb-3">
                            <div class="bg-blue-100 rounded-lg p-2 mr-3">
                                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <div class="text-sm font-semibold text-gray-600 uppercase tracking-wide">Date</div>
                        </div>
                        <div class="text-xl font-bold text-gray-900">{{ $booking->date ?? 'N/A' }}</div>
                    </div>

                    <!-- Party Size Card -->
                    <div class="p-6 border-2 border-gray-200 rounded-xl hover:border-emerald-300 hover:shadow-lg transition-all duration-300 bg-gradient-to-br from-white to-emerald-50">
                        <div class="flex items-center mb-3">
                            <div class="bg-emerald-100 rounded-lg p-2 mr-3">
                                <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                            </div>
                            <div class="text-sm font-semibold text-gray-600 uppercase tracking-wide">Party Size</div>
                        </div>
                        <div class="text-xl font-bold text-gray-900">{{ $booking->party_size }} {{ $booking->party_size == 1 ? 'Person' : 'People' }}</div>
                    </div>
                </div>

                <!-- Notes Section -->
                <div class="mb-8 p-6 border-2 border-gray-200 rounded-xl bg-gradient-to-br from-white to-gray-50 hover:shadow-lg transition-all duration-300">
                    <div class="flex items-center mb-3">
                        <div class="bg-purple-100 rounded-lg p-2 mr-3">
                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <div class="text-sm font-semibold text-gray-600 uppercase tracking-wide">Additional Notes</div>
                    </div>
                    <div class="text-gray-800 leading-relaxed">{{ $booking->notes ?? 'No additional notes provided' }}</div>
                </div>

                    <!-- Display payment information for manual payments -->
                    @if($booking->usesManualPayment())
                        <div class="p-6 border-2 border-blue-300 rounded-xl bg-gradient-to-br from-blue-50 via-white to-indigo-50 shadow-md hover:shadow-xl transition-all duration-300">
                            <div class="flex items-center mb-4">
                                <div class="bg-blue-600 rounded-lg p-2.5 mr-3 shadow-md">
                                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                                    </svg>
                                </div>
                                <h3 class="text-lg font-bold text-gray-900">Payment Information</h3>
                            </div>
                            <div class="space-y-4">
                                <div class="flex items-center justify-between p-3 bg-white rounded-lg border border-blue-100">
                                    <span class="text-sm font-medium text-gray-600">Payment Method:</span> 
                                    <span class="font-semibold text-gray-900 flex items-center">
                                        <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                                        </svg>
                                        Manual Payment (QR Code)
                                    </span>
                                </div>
                                @if($booking->transaction_number)
                                    <div class="flex items-center justify-between p-3 bg-white rounded-lg border border-blue-100">
                                        <span class="text-sm font-medium text-gray-600">Transaction Number:</span> 
                                        <span class="font-mono font-bold text-blue-700 bg-blue-100 px-3 py-1 rounded-md">{{ $booking->transaction_number }}</span>
                                    </div>
                                @endif
                                <div class="flex items-center justify-between p-3 bg-white rounded-lg border border-blue-100">
                                    <span class="text-sm font-medium text-gray-600">Payment Status:</span> 
                                    @if($booking->payment_status === 'pending')
                                        <span class="inline-flex items-center px-3 py-1.5 text-xs font-bold rounded-full bg-orange-100 text-orange-800 border border-orange-300">
                                            <span class="w-2 h-2 rounded-full bg-orange-500 mr-2 animate-pulse"></span>
                                            Pending Verification
                                        </span>
                                    @elseif($booking->payment_status === 'verified')
                                        <span class="inline-flex items-center px-3 py-1.5 text-xs font-bold rounded-full bg-green-100 text-green-800 border border-green-300">
                                            <svg class="w-4 h-4 mr-1.5" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                            </svg>
                                            Verified
                                        </span>
                                    @elseif($booking->payment_status === 'rejected')
                                        <span class="inline-flex items-center px-3 py-1.5 text-xs font-bold rounded-full bg-red-100 text-red-800 border border-red-300">
                                            <svg class="w-4 h-4 mr-1.5" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                            </svg>
                                            Rejected
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-3 py-1.5 text-xs font-bold rounded-full bg-gray-100 text-gray-800 border border-gray-300">
                                            {{ ucfirst($booking->payment_status ?? 'Unknown') }}
                                        </span>
                                    @endif
                                </div>
                                @if($booking->payment_proof_path)
                                    <div class="p-4 bg-white rounded-lg border-2 border-dashed border-blue-300">
                                        <span class="text-sm font-medium text-gray-600 block mb-2">Payment Proof:</span>
                                        <a href="{{ $booking->getPaymentProofUrl() }}" target="_blank" class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-semibold rounded-lg shadow-md hover:shadow-lg transition-all duration-300 transform hover:-translate-y-0.5">
                                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                            View Payment Proof
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row items-center justify-between gap-4 pt-6 border-t-2 border-gray-100">
                    <a href="{{ route('booking.index') }}" class="inline-flex items-center px-6 py-3 bg-white hover:bg-gray-50 text-purple-700 font-semibold rounded-xl border-2 border-purple-200 hover:border-purple-300 shadow-sm hover:shadow-md transition-all duration-300 transform hover:-translate-y-0.5">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Back to Bookings
                    </a>
                    <a href="#" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-emerald-600 to-green-600 hover:from-emerald-700 hover:to-green-700 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-0.5">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                        Contact Support
                    </a>
                </div>
            </div>
        </div>
    </div>

    <style>
        @keyframes fade-in {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fade-in {
            animation: fade-in 0.6s ease-out;
        }

        /* Smooth hover effects */
        .hover\:shadow-3xl:hover {
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        }
    </style>
</x-app-layout>


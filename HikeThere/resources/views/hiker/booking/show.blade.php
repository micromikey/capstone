<x-app-layout>
    <div class="bg-gradient-to-br from-slate-50 via-blue-50 to-emerald-50 min-h-screen py-10">
        <div class="max-w-6xl mx-auto px-4 md:px-8">
            <div class="text-center mb-8">
                <h1 class="text-3xl md:text-4xl font-bold text-gray-800 mb-2">Booking Details</h1>
                <p class="text-gray-600 max-w-2xl mx-auto">Review the details of your reservation below.</p>
            </div>

            <div class="bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-100 p-6 max-w-3xl mx-auto">
                <!-- Payment Status Alert -->
                @if($booking->usesManualPayment())
                    @if($booking->payment_status === 'pending')
                        <div class="mb-6 bg-orange-50 border-l-4 border-orange-400 p-4 rounded-lg">
                            <div class="flex items-start">
                                <svg class="h-6 w-6 text-orange-400 mt-0.5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <div>
                                    <h3 class="text-sm font-semibold text-orange-800">Payment Pending Verification</h3>
                                    <p class="mt-1 text-sm text-orange-700">Your payment proof has been submitted and is awaiting verification by the organization. You will be notified once it's approved.</p>
                                </div>
                            </div>
                        </div>
                    @elseif($booking->payment_status === 'verified')
                        <div class="mb-6 bg-green-50 border-l-4 border-green-400 p-4 rounded-lg">
                            <div class="flex items-start">
                                <svg class="h-6 w-6 text-green-400 mt-0.5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <div>
                                    <h3 class="text-sm font-semibold text-green-800">Payment Verified - Booking Confirmed!</h3>
                                    <p class="mt-1 text-sm text-green-700">Your payment has been verified. Your booking is confirmed and ready. Enjoy your hike!</p>
                                    @if($booking->payment_verified_at)
                                        <p class="mt-1 text-xs text-green-600">Verified on {{ \Carbon\Carbon::parse($booking->payment_verified_at)->format('F j, Y g:i A') }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @elseif($booking->payment_status === 'rejected')
                        <div class="mb-6 bg-red-50 border-l-4 border-red-400 p-4 rounded-lg">
                            <div class="flex items-start">
                                <svg class="h-6 w-6 text-red-400 mt-0.5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <div>
                                    <h3 class="text-sm font-semibold text-red-800">Payment Rejected - Action Required</h3>
                                    <p class="mt-1 text-sm text-red-700">Unfortunately, your payment proof was not accepted. Please submit a new payment proof or contact the organization for assistance.</p>
                                    <div class="mt-3">
                                        <a href="{{ route('booking.edit', $booking) }}" class="inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg transition-colors">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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

                <div class="mb-4">
                    <div class="text-lg font-semibold text-gray-800">{{ $booking->trail?->trail_name ?? 'N/A' }}</div>
                    <div class="text-sm text-gray-500">Booking #{{ $booking->id }} · Status: <span class="font-medium">{{ ucfirst($booking->status) }}</span></div>
                </div>

                <div class="grid grid-cols-1 gap-4">
                    <div class="p-4 border rounded">
                        <div class="text-sm text-gray-600">Date</div>
                        <div class="font-medium">{{ $booking->date ?? 'N/A' }}</div>
                    </div>
                    <div class="p-4 border rounded">
                        <div class="text-sm text-gray-600">Party size</div>
                        <div class="font-medium">{{ $booking->party_size }}</div>
                    </div>
                    <div class="p-4 border rounded">
                        <div class="text-sm text-gray-600">Notes</div>
                        <div class="font-medium">{{ $booking->notes ?? '—' }}</div>
                    </div>

                    <!-- Display payment information for manual payments -->
                    @if($booking->usesManualPayment())
                        <div class="p-4 border rounded bg-blue-50">
                            <div class="text-sm text-gray-600 mb-2">Payment Information</div>
                            <div class="space-y-2">
                                <div class="text-sm">
                                    <span class="text-gray-600">Payment Method:</span> 
                                    <span class="font-medium">Manual Payment (QR Code)</span>
                                </div>
                                @if($booking->transaction_number)
                                    <div class="text-sm">
                                        <span class="text-gray-600">Transaction #:</span> 
                                        <span class="font-mono font-medium">{{ $booking->transaction_number }}</span>
                                    </div>
                                @endif
                                <div class="text-sm">
                                    <span class="text-gray-600">Payment Status:</span> 
                                    @if($booking->payment_status === 'pending')
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-orange-100 text-orange-800">Pending Verification</span>
                                    @elseif($booking->payment_status === 'verified')
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Verified</span>
                                    @elseif($booking->payment_status === 'rejected')
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">Rejected</span>
                                    @else
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">{{ ucfirst($booking->payment_status ?? 'Unknown') }}</span>
                                    @endif
                                </div>
                                @if($booking->payment_proof_path)
                                    <div class="text-sm mt-3">
                                        <span class="text-gray-600">Payment Proof:</span>
                                        <a href="{{ asset('storage/' . $booking->payment_proof_path) }}" target="_blank" class="inline-flex items-center text-blue-600 hover:text-blue-800 font-medium">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                            View Proof
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>

                <div class="flex justify-between mt-6">
                    <a href="{{ route('booking.index') }}" class="text-purple-600 font-medium">Back to bookings</a>
                    <div>
                        <a href="#" class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-green-600 to-green-700 text-white rounded-xl">Contact Support</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>


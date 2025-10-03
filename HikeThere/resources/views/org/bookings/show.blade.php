<x-app-layout>
    <x-slot name="header">
        <div class="space-y-4">
            <x-trail-breadcrumb />
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Booking Details') }}</h2>
                <div></div>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">{{ session('success') }}</div>
            @endif

            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                <div class="flex items-start gap-6">
                    <div class="flex-1">
                        <h3 class="text-lg font-semibold">Booking #{{ $booking->id }}</h3>
                        <p class="text-sm text-gray-600">Hiker: {{ $booking->user->name ?? 'N/A' }} <span class="text-xs text-gray-400">{{ $booking->user->email ?? '' }}</span></p>

                        <div class="mt-4 grid grid-cols-1 gap-2 text-sm">
                            <div><strong>Trail:</strong> {{ $booking->trail->trail_name ?? 'N/A' }}</div>
                            <div><strong>Date:</strong> {{ $booking->date }}</div>
                            <div><strong>Party Size:</strong> {{ $booking->party_size }}</div>
                            <div><strong>Notes:</strong> {{ $booking->notes ?? '-' }}</div>
                        </div>

                        <!-- Payment Information -->
                        <div class="mt-6 pt-6 border-t border-gray-200">
                            <h4 class="text-md font-semibold mb-3">Payment Information</h4>
                            <div class="bg-gray-50 p-4 rounded">
                                <div class="grid grid-cols-1 gap-2 text-sm">
                                    <div><strong>Amount:</strong> ₱{{ number_format($booking->price_cents / 100, 2) }}</div>
                                    
                                    @if($booking->usesManualPayment())
                                        <div><strong>Payment Method:</strong> <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">Manual Payment (QR Code)</span></div>
                                        
                                        @if($booking->transaction_number)
                                            <div><strong>Transaction Number:</strong> <span class="font-mono text-xs">{{ $booking->transaction_number }}</span></div>
                                        @endif
                                        
                                        @if($booking->payment_notes)
                                            <div><strong>Payment Notes:</strong> {{ $booking->payment_notes }}</div>
                                        @endif
                                        
                                        <div><strong>Payment Status:</strong> 
                                            @if($booking->payment_status === 'pending')
                                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-orange-100 text-orange-800">Pending Verification</span>
                                            @elseif($booking->payment_status === 'verified')
                                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Verified</span>
                                            @elseif($booking->payment_status === 'rejected')
                                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">Rejected</span>
                                            @else
                                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">{{ ucfirst($booking->payment_status ?? 'N/A') }}</span>
                                            @endif
                                        </div>
                                        
                                        @if($booking->payment_verified_at)
                                            <div><strong>Verified At:</strong> {{ \Carbon\Carbon::parse($booking->payment_verified_at)->format('M d, Y h:i A') }}</div>
                                        @endif
                                        
                                        <!-- Payment Proof Display -->
                                        @if($booking->payment_proof_path)
                                            <div class="mt-4 pt-4 border-t border-gray-300">
                                                <strong class="block mb-2">Payment Proof:</strong>
                                                <a href="{{ asset('storage/' . $booking->payment_proof_path) }}" target="_blank" class="inline-block">
                                                    <img src="{{ asset('storage/' . $booking->payment_proof_path) }}" alt="Payment Proof" class="max-w-sm rounded-lg border border-gray-300 shadow hover:shadow-lg transition-shadow cursor-pointer">
                                                </a>
                                                <p class="text-xs text-gray-500 mt-2">Click image to view full size</p>
                                            </div>
                                        @endif
                                        
                                        <!-- Verification Actions -->
                                        @if($booking->isPaymentPendingVerification())
                                            <div class="mt-4 pt-4 border-t border-gray-300">
                                                <strong class="block mb-3 text-orange-700">Action Required: Verify or Reject Payment</strong>
                                                <div class="flex gap-3">
                                                    <form method="POST" action="{{ route('org.bookings.verify-payment', $booking) }}" class="inline">
                                                        @csrf
                                                        <button type="submit" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition-colors" onclick="return confirm('Are you sure you want to verify this payment?')">
                                                            <svg class="inline-block w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                            </svg>
                                                            Verify Payment
                                                        </button>
                                                    </form>
                                                    <form method="POST" action="{{ route('org.bookings.reject-payment', $booking) }}" class="inline">
                                                        @csrf
                                                        <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg transition-colors" onclick="return confirm('Are you sure you want to reject this payment? The hiker will need to resubmit.')">
                                                            <svg class="inline-block w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                            </svg>
                                                            Reject Payment
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        @endif
                                        
                                    @elseif($booking->payment)
                                        <div><strong>Payment Method:</strong> <span class="px-2 py-1 text-xs font-semibold rounded-full bg-purple-100 text-purple-800">Automatic Payment Gateway</span></div>
                                        <div><strong>Payment Status:</strong> 
                                            @if($booking->payment->isPaid())
                                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Paid</span>
                                            @elseif($booking->payment->isPending())
                                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">Pending</span>
                                            @else
                                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">{{ ucfirst($booking->payment->payment_status) }}</span>
                                            @endif
                                        </div>
                                        @if($booking->payment->isPaid() && $booking->payment->paid_at)
                                            <div><strong>Paid At:</strong> {{ $booking->payment->paid_at->format('M d, Y h:i A') }}</div>
                                        @endif
                                        @if($booking->payment->paymongo_payment_id)
                                            <div><strong>Payment ID:</strong> <span class="text-xs font-mono">{{ $booking->payment->paymongo_payment_id }}</span></div>
                                        @endif
                                    @else
                                        <div><strong>Payment Status:</strong> 
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">No Payment Record</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="w-64">
                        <div class="bg-gray-50 p-4 rounded">
                            <p class="text-sm font-medium">Status</p>
                            <p class="mt-2"><span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $booking->status === 'confirmed' ? 'bg-green-100 text-green-800' : ($booking->status === 'cancelled' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">{{ ucfirst($booking->status) }}</span></p>

                            <form method="POST" action="{{ route('org.bookings.update-status', $booking) }}" class="mt-4">
                                @csrf
                                @method('PATCH')

                                <label for="status" class="block text-sm font-medium text-gray-700">Change status</label>
                                <select id="status" name="status" class="mt-1 block w-full">
                                    <option value="pending" {{ $booking->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="confirmed" {{ $booking->status === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                                    <option value="cancelled" {{ $booking->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                    <option value="completed" {{ $booking->status === 'completed' ? 'selected' : '' }}>Completed</option>
                                </select>

                                <button type="submit" class="mt-3 inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-[#336d66] hover:bg-[#2a5a54]">Update</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-6">
                <a href="{{ route('org.bookings.index') }}" class="text-sm text-gray-600">&larr; Back to bookings</a>
            </div>
        </div>
    </div>
</x-app-layout>
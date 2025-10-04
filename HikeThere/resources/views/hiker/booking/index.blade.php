<x-app-layout>
    <div class="bg-gradient-to-br from-slate-50 via-blue-50 to-emerald-50 min-h-screen py-10">
        <div class="max-w-7xl mx-auto px-4 md:px-8">
            <!-- Header Section -->
            <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4">
                <div>
                    <h1 class="text-3xl md:text-4xl font-bold text-gray-800 mb-2 flex items-center gap-3">
                        <svg class="w-8 h-8 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                        </svg>
                        My Bookings
                    </h1>
                    <p class="text-gray-600">Manage your hiking reservations, payments, and track your adventures.</p>
                </div>
                <div class="flex items-center gap-3">
                    <div class="px-4 py-2 bg-white rounded-lg shadow-sm border border-gray-200">
                        <span class="text-sm text-gray-600">Total Bookings: </span>
                        <strong class="text-lg text-emerald-600">{{ $bookings->count() }}</strong>
                    </div>
                    <a href="{{ route('booking.create') }}" class="inline-flex items-center px-5 py-2.5 bg-gradient-to-r from-emerald-500 to-emerald-600 text-white rounded-lg shadow-lg hover:from-emerald-600 hover:to-emerald-700 transition-all hover:shadow-xl transform hover:-translate-y-0.5">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        New Booking
                    </a>
                </div>
            </div>

            <!-- Bookings Grid -->
            <div class="grid grid-cols-1 gap-6">
                @if($bookings->isEmpty())
                    <!-- Empty State -->
                    <div class="bg-white rounded-2xl shadow-lg overflow-hidden border border-dashed border-gray-300 p-12 text-center">
                        <div class="max-w-md mx-auto">
                            <div class="w-24 h-24 mx-auto mb-6 bg-emerald-50 rounded-full flex items-center justify-center">
                                <svg class="w-12 h-12 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                </svg>
                            </div>
                            <h3 class="text-xl font-semibold text-gray-800 mb-2">No bookings yet</h3>
                            <p class="text-gray-600 mb-6">Start your hiking adventure by creating your first booking!</p>
                            <a href="{{ route('booking.create') }}" class="inline-flex items-center px-6 py-3 bg-emerald-600 text-white rounded-lg shadow-lg hover:bg-emerald-700 transition-all transform hover:-translate-y-0.5">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                                Create Your First Booking
                            </a>
                        </div>
                    </div>
                @else
                    <!-- Bookings Grid -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6">
                        @foreach($bookings as $booking)
                            @php
                                $isPending = $booking->status === 'pending';
                                $isConfirmed = $booking->status === 'confirmed';
                                $isCancelled = $booking->status === 'cancelled';
                                $bookingDate = $booking->date ? \Carbon\Carbon::parse($booking->date) : null;
                                $isCompleted = $isConfirmed && $bookingDate && $bookingDate->isPast();
                                
                                // Check payment status
                                $paymentStatus = $booking->payment_status ?? 'pending';
                                $isPaymentVerified = $paymentStatus === 'verified';
                                $isPaymentPending = $paymentStatus === 'pending';
                                $isPaymentRejected = $paymentStatus === 'rejected';
                                
                                // Check if manual payment proof was uploaded (means under verification)
                                $hasPaymentProof = !empty($booking->payment_proof_path);
                                $isUnderVerification = $isPaymentPending && $hasPaymentProof;
                            @endphp

                            <div class="bg-white rounded-2xl shadow-md hover:shadow-xl transition-all duration-300 overflow-hidden border border-gray-100 group flex flex-col h-full">
                                <!-- Trail Image Header -->
                                <div class="relative h-48 overflow-hidden bg-gradient-to-br from-emerald-400 to-blue-500 flex-shrink-0">
                                    @if($booking->trail?->image_url)
                                        <img src="{{ $booking->trail->image_url }}" alt="{{ $booking->trail->trail_name }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center">
                                            <svg class="w-20 h-20 text-white opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"></path>
                                            </svg>
                                        </div>
                                    @endif
                                    
                                    <!-- Status Badge -->
                                    <div class="absolute top-3 right-3">
                                        @if($isCompleted)
                                            <span class="px-3 py-1.5 bg-blue-600 text-white text-xs font-semibold rounded-full shadow-lg flex items-center gap-1">
                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                                </svg>
                                                Completed
                                            </span>
                                        @elseif($isCancelled)
                                            <span class="px-3 py-1.5 bg-red-600 text-white text-xs font-semibold rounded-full shadow-lg flex items-center gap-1">
                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                                </svg>
                                                Cancelled
                                            </span>
                                        @elseif($isConfirmed)
                                            <span class="px-3 py-1.5 bg-emerald-600 text-white text-xs font-semibold rounded-full shadow-lg flex items-center gap-1">
                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                                </svg>
                                                Confirmed
                                            </span>
                                        @else
                                            <span class="px-3 py-1.5 bg-amber-500 text-white text-xs font-semibold rounded-full shadow-lg flex items-center gap-1">
                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                                                </svg>
                                                Pending
                                            </span>
                                        @endif
                                    </div>

                                    <!-- Payment Status Badge -->
                                    @if(!$isCancelled)
                                        <div class="absolute top-3 left-3">
                                            @if($isPaymentVerified)
                                                <span class="px-3 py-1.5 bg-green-600 text-white text-xs font-semibold rounded-full shadow-lg flex items-center gap-1">
                                                    <span class="text-sm font-bold">₱</span>
                                                    Payment Verified
                                                </span>
                                            @elseif($isUnderVerification)
                                                <span class="px-3 py-1.5 bg-blue-600 text-white text-xs font-semibold rounded-full shadow-lg flex items-center gap-1">
                                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                                                    </svg>
                                                    Under Verification
                                                </span>
                                            @elseif($isPaymentRejected)
                                                <span class="px-3 py-1.5 bg-red-600 text-white text-xs font-semibold rounded-full shadow-lg flex items-center gap-1">
                                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                                    </svg>
                                                    Payment Rejected
                                                </span>
                                            @else
                                                <span class="px-3 py-1.5 bg-orange-500 text-white text-xs font-semibold rounded-full shadow-lg flex items-center gap-1">
                                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                                                    </svg>
                                                    Payment Pending
                                                </span>
                                            @endif
                                        </div>
                                    @endif
                                </div>

                                <!-- Booking Details -->
                                <div class="p-5 flex flex-col flex-grow">
                                    <h3 class="text-xl font-bold text-gray-800 mb-3 truncate">
                                        {{ $booking->trail?->trail_name ?? 'Trail Booking' }}
                                    </h3>

                                    <div class="space-y-2.5 mb-4">
                                        <!-- Date & Time -->
                                        <div class="flex items-center text-sm text-gray-600">
                                            <svg class="w-5 h-5 mr-3 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                            <span class="font-medium text-gray-700">
                                                {{ $bookingDate ? $bookingDate->format('F j, Y') : 'Date TBD' }}
                                                @if($booking->batch && $booking->batch->starts_at)
                                                    <span class="text-emerald-600 ml-2">
                                                        • {{ $booking->batch->starts_at->format('g:i A') }}
                                                    </span>
                                                @endif
                                            </span>
                                        </div>

                                        <!-- Party Size -->
                                        <div class="flex items-center text-sm text-gray-600">
                                            <svg class="w-5 h-5 mr-3 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                            </svg>
                                            <span><strong class="text-gray-700">{{ $booking->party_size }}</strong> {{ Str::plural('person', $booking->party_size) }}</span>
                                        </div>

                                        <!-- Amount -->
                                        @if($booking->payment)
                                            <div class="flex items-center text-sm text-gray-600">
                                                <div class="w-5 h-5 mr-3 flex items-center justify-center">
                                                    <span class="text-lg font-bold text-green-500">₱</span>
                                                </div>
                                                <span><strong class="text-gray-700">₱{{ number_format($booking->payment->amount ?? 0, 2) }}</strong></span>
                                            </div>
                                        @endif

                                        <!-- Booking ID -->
                                        <div class="flex items-center text-xs text-gray-500">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"></path>
                                            </svg>
                                            Booking #{{ $booking->id }}
                                        </div>
                                    </div>

                                    <!-- Notes Preview -->
                                    @if($booking->notes)
                                        <p class="text-sm text-gray-600 mb-4 line-clamp-2">
                                            {{ Str::limit($booking->notes, 100) }}
                                        </p>
                                    @endif

                                    <!-- Action Buttons -->
                                    <div class="pt-4 border-t border-gray-100 space-y-2 mt-auto">
                                        @if($isPending && !$isPaymentVerified)
                                            @if($isUnderVerification)
                                                <!-- Under Verification Status -->
                                                <div class="w-full inline-flex items-center justify-center px-4 py-2.5 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-lg shadow-md font-semibold">
                                                    <svg class="w-5 h-5 mr-2 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                    </svg>
                                                    Under Verification
                                                </div>
                                            @elseif($isPaymentRejected)
                                                <!-- Retry Payment Button for Rejected -->
                                                <a href="{{ route('booking.payment', $booking->id) }}" class="w-full inline-flex items-center justify-center px-4 py-2.5 bg-gradient-to-r from-red-500 to-red-600 text-white rounded-lg shadow-md hover:from-red-600 hover:to-red-700 transition-all transform hover:-translate-y-0.5 font-semibold">
                                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                                    </svg>
                                                    Retry Payment
                                                </a>
                                            @else
                                                <!-- Pay Now Button for Pending Bookings -->
                                                <a href="{{ route('booking.payment', $booking->id) }}" class="w-full inline-flex items-center justify-center px-4 py-2.5 bg-gradient-to-r from-amber-500 to-orange-500 text-white rounded-lg shadow-md hover:from-amber-600 hover:to-orange-600 transition-all transform hover:-translate-y-0.5 font-semibold">
                                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                                    </svg>
                                                    Pay Now
                                                </a>
                                            @endif
                                        @elseif($isConfirmed && $isPaymentVerified)
                                            <!-- View Details & Download Reservation Slip for Confirmed Bookings -->
                                            <div class="grid grid-cols-2 gap-2">
                                                <a href="{{ route('booking.show', $booking) }}" class="inline-flex items-center justify-center px-3 py-2 bg-gradient-to-r from-emerald-600 to-emerald-700 text-white rounded-lg hover:from-emerald-700 hover:to-emerald-800 transition-all text-sm font-semibold shadow-md hover:shadow-lg transform hover:-translate-y-0.5">
                                                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                    </svg>
                                                    Details
                                                </a>
                                                <a href="{{ route('booking.download-slip', $booking) }}" class="inline-flex items-center justify-center px-3 py-2 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-lg hover:from-blue-700 hover:to-indigo-700 transition-all text-sm font-semibold shadow-md hover:shadow-lg transform hover:-translate-y-0.5 group" download>
                                                    <svg class="w-4 h-4 mr-1.5 group-hover:animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                    </svg>
                                                    Download
                                                </a>
                                            </div>
                                        @endif

                                        <!-- Standard Actions Row -->
                                        <div class="flex items-center justify-between gap-2">
                                            <div class="flex items-center gap-2">
                                                <a href="{{ route('booking.show', $booking) }}" class="text-sm text-emerald-600 hover:text-emerald-700 font-medium flex items-center">
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                    </svg>
                                                    View
                                                </a>
                                                @if(!$isCancelled && !$isCompleted)
                                                    <span class="text-gray-300">|</span>
                                                    <a href="{{ route('booking.edit', $booking) }}" class="text-sm text-gray-600 hover:text-gray-800 font-medium flex items-center">
                                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                        </svg>
                                                        Edit
                                                    </a>
                                                @endif
                                            </div>
                                            <span class="text-xs text-gray-500">{{ $booking->created_at->diffForHumans() }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Real-time Booking Status Updates via AJAX --}}
    <script>
        let pollingInterval = null;
        let bookingStates = {};

        // Initialize booking states on page load
        document.addEventListener('DOMContentLoaded', function() {
            // Store initial booking states
            @foreach($bookings as $booking)
                bookingStates[{{ $booking->id }}] = {
                    status: '{{ $booking->status }}',
                    payment_status: '{{ $booking->payment_status ?? "pending" }}',
                    has_payment_proof: {{ !empty($booking->payment_proof_path) ? 'true' : 'false' }}
                };
            @endforeach

            // Start polling for updates every 30 seconds
            startPolling();
        });

        function startPolling() {
            // Poll immediately on load
            checkForUpdates();
            
            // Then poll every 30 seconds
            pollingInterval = setInterval(checkForUpdates, 30000);
        }

        function stopPolling() {
            if (pollingInterval) {
                clearInterval(pollingInterval);
                pollingInterval = null;
            }
        }

        async function checkForUpdates() {
            try {
                const response = await fetch('{{ route("booking.index") }}', {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    }
                });

                if (!response.ok) return;

                const data = await response.json();
                
                if (data.bookings) {
                    processBookingUpdates(data.bookings);
                }
            } catch (error) {
                console.error('Error checking for booking updates:', error);
            }
        }

        function processBookingUpdates(bookings) {
            let hasChanges = false;

            bookings.forEach(booking => {
                const bookingId = booking.id;
                const oldState = bookingStates[bookingId];

                if (!oldState) return; // New booking, will appear on refresh

                // Check if status changed
                if (oldState.status !== booking.status) {
                    hasChanges = true;
                    showUpdateNotification(booking, 'status', oldState.status, booking.status);
                    bookingStates[bookingId].status = booking.status;
                }

                // Check if payment status changed
                if (oldState.payment_status !== booking.payment_status) {
                    hasChanges = true;
                    showUpdateNotification(booking, 'payment', oldState.payment_status, booking.payment_status);
                    bookingStates[bookingId].payment_status = booking.payment_status;
                }
            });

            // If there are changes, refresh the page after showing notification
            if (hasChanges) {
                setTimeout(() => {
                    window.location.reload();
                }, 3000);
            }
        }

        function showUpdateNotification(booking, type, oldValue, newValue) {
            const notificationDiv = document.createElement('div');
            notificationDiv.className = 'fixed top-4 right-4 z-50 p-4 bg-blue-50 border-l-4 border-blue-500 rounded-r-lg shadow-xl max-w-md animate-slide-in';
            
            let title, message, icon;
            
            if (type === 'status') {
                title = 'Booking Status Updated';
                message = `Your booking for <strong>${booking.trail_name || 'Trail'}</strong> has been updated from <strong>${oldValue}</strong> to <strong>${newValue}</strong>.`;
                icon = getStatusIcon(newValue);
            } else if (type === 'payment') {
                title = 'Payment Status Updated';
                message = `Payment status for <strong>${booking.trail_name || 'Trail'}</strong> is now <strong>${newValue}</strong>.`;
                icon = getPaymentIcon(newValue);
            }
            
            notificationDiv.innerHTML = `
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        ${icon}
                    </div>
                    <div class="ml-3 flex-1">
                        <p class="text-sm font-semibold text-blue-900">${title}</p>
                        <p class="text-sm text-blue-700 mt-1">${message}</p>
                        <p class="text-xs text-blue-600 mt-2">Refreshing page...</p>
                    </div>
                    <button onclick="this.parentElement.parentElement.remove()" class="ml-4 text-blue-500 hover:text-blue-700">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            `;
            
            document.body.appendChild(notificationDiv);
            
            // Auto-remove after 8 seconds
            setTimeout(() => notificationDiv.remove(), 8000);
        }

        function getStatusIcon(status) {
            switch(status) {
                case 'confirmed':
                    return `<svg class="w-6 h-6 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>`;
                case 'cancelled':
                    return `<svg class="w-6 h-6 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>`;
                case 'completed':
                    return `<svg class="w-6 h-6 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>`;
                default:
                    return `<svg class="w-6 h-6 text-amber-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                    </svg>`;
            }
        }

        function getPaymentIcon(status) {
            switch(status) {
                case 'verified':
                case 'paid':
                    return `<svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>`;
                case 'rejected':
                    return `<svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>`;
                default:
                    return `<svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>`;
            }
        }

        // Stop polling when page is hidden to save resources
        document.addEventListener('visibilitychange', function() {
            if (document.hidden) {
                stopPolling();
            } else {
                startPolling();
            }
        });

        // Clean up on page unload
        window.addEventListener('beforeunload', function() {
            stopPolling();
        });
    </script>

    <style>
        @keyframes slide-in {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
        
        .animate-slide-in {
            animation: slide-in 0.3s ease-out;
        }
    </style>
</x-app-layout>

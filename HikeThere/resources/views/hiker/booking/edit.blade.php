<x-app-layout>
    <div class="bg-gradient-to-br from-slate-50 via-blue-50 to-emerald-50 min-h-screen py-10">
        <div class="max-w-6xl mx-auto px-4 md:px-8">
            <div class="text-center mb-8">
                <h1 class="text-3xl md:text-4xl font-bold text-gray-800 mb-2">Edit Booking</h1>
                <p class="text-gray-600 max-w-2xl mx-auto">Update your reservation details below.</p>
            </div>

            @if (session('error'))
                <div class="max-w-3xl mx-auto mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl">
                    {{ session('error') }}
                </div>
            @endif

            <div class="bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-100 p-6 max-w-3xl mx-auto">
                <div class="mb-6">
                    <div class="text-lg font-semibold text-gray-800">{{ $booking->trail?->trail_name ?? 'N/A' }}</div>
                    <div class="text-sm text-gray-500">Booking #{{ $booking->id }} · Status: <span class="font-medium">{{ $booking->status }}</span></div>
                </div>

                <form id="edit-booking-form" action="{{ route('booking.update', $booking) }}" method="POST">
                    @csrf
                    @method('PATCH')

                    <div class="space-y-6">
                        <!-- Date -->
                        <div>
                            <label for="date" class="block text-sm font-medium text-gray-700 mb-2">
                                Date <span class="text-red-500">*</span>
                            </label>
                            <input 
                                type="date" 
                                id="date" 
                                name="date" 
                                value="{{ old('date', $booking->date) }}" 
                                min="{{ now()->addDay()->format('Y-m-d') }}"
                                class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent @error('date') border-red-500 @enderror"
                                required
                            >
                            @error('date')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Party Size -->
                        <div>
                            <label for="party_size" class="block text-sm font-medium text-gray-700 mb-2">
                                Party Size <span class="text-red-500">*</span>
                            </label>
                            <input 
                                type="number" 
                                id="party_size" 
                                name="party_size" 
                                value="{{ old('party_size', $booking->party_size) }}" 
                                min="1" 
                                max="50"
                                class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent @error('party_size') border-red-500 @enderror"
                                required
                            >
                            @error('party_size')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-xs text-gray-500">Number of people in your group</p>
                        </div>

                        <!-- Notes -->
                        <div>
                            <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                                Notes (Optional)
                            </label>
                            <textarea 
                                id="notes" 
                                name="notes" 
                                rows="4"
                                maxlength="500"
                                class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent @error('notes') border-red-500 @enderror"
                                placeholder="Any special requests or information..."
                            >{{ old('notes', $booking->notes) }}</textarea>
                            @error('notes')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Price Information (if available) -->
                        @if($booking->price_cents)
                            <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
                                <div class="flex justify-between items-center">
                                    <span class="text-sm font-medium text-gray-700">Current Total Amount:</span>
                                    <span class="text-lg font-bold text-blue-600">₱{{ number_format($booking->getAmountInPesos(), 2) }}</span>
                                </div>
                                @if($booking->trail && $booking->trail->price)
                                    <p class="text-xs text-gray-600 mt-2">
                                        ₱{{ number_format($booking->trail->price, 2) }} per person × {{ $booking->party_size }} participant(s)
                                    </p>
                                @endif
                                <p class="text-xs text-yellow-600 mt-2">
                                    ⚠️ Note: If you change the party size, the total amount will be recalculated.
                                </p>
                            </div>
                        @endif

                        <!-- Slot Availability (if batch info available) -->
                        @if($booking->batch)
                            <div class="bg-gray-50 border border-gray-200 rounded-xl p-4">
                                <div class="text-sm font-medium text-gray-700 mb-2">Batch Information:</div>
                                <div class="text-xs text-gray-600">
                                    <div>Date: {{ $booking->batch->starts_at->format('M d, Y - h:i A') }}</div>
                                    <div>Available Slots: {{ $booking->batch->getAvailableSlots() }} / {{ $booking->batch->capacity }}</div>
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex flex-col sm:flex-row gap-3 justify-between mt-8">
                        <a href="{{ route('booking.show', $booking) }}" class="px-6 py-2 border border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 transition text-center">
                            Cancel
                        </a>
                        <button type="submit" class="px-6 py-2 bg-gradient-to-r from-purple-600 to-purple-700 text-white rounded-xl hover:from-purple-700 hover:to-purple-800 transition">
                            Update Booking
                        </button>
                    </div>
                </form>

                <script>
                    // AJAX form submission for edit booking
                    document.getElementById('edit-booking-form')?.addEventListener('submit', function(e) {
                        e.preventDefault();
                        
                        const form = this;
                        const submitBtn = form.querySelector('button[type="submit"]');
                        const originalText = submitBtn.innerHTML;
                        
                        // Show loading state
                        submitBtn.disabled = true;
                        submitBtn.innerHTML = '<svg class="animate-spin h-5 w-5 mr-2 inline-block" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Updating...';
                        
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
                                const successDiv = document.createElement('div');
                                successDiv.className = 'fixed top-4 right-4 z-50 p-4 bg-green-50 border-l-4 border-green-500 rounded-r-lg shadow-lg';
                                successDiv.innerHTML = `
                                    <div class="flex items-center">
                                        <svg class="w-6 h-6 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <div>
                                            <p class="text-green-800 font-semibold">${data.message}</p>
                                        </div>
                                    </div>
                                `;
                                document.body.appendChild(successDiv);
                                
                                // Redirect after short delay
                                setTimeout(() => {
                                    window.location.href = "{{ route('booking.show', $booking) }}";
                                }, 1500);
                            } else {
                                throw new Error(data.message || 'Update failed');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            
                            // Show error message
                            const errorDiv = document.createElement('div');
                            errorDiv.className = 'fixed top-4 right-4 z-50 p-4 bg-red-50 border-l-4 border-red-500 rounded-r-lg shadow-lg';
                            errorDiv.innerHTML = `
                                <div class="flex items-center">
                                    <svg class="w-6 h-6 text-red-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <div>
                                        <p class="text-red-800 font-semibold">${error.message || 'Unable to update booking. Please try again.'}</p>
                                    </div>
                                </div>
                            `;
                            document.body.appendChild(errorDiv);
                            
                            // Reset button
                            submitBtn.disabled = false;
                            submitBtn.innerHTML = originalText;
                            
                            // Auto-remove error after 5 seconds
                            setTimeout(() => errorDiv.remove(), 5000);
                        });
                    });
                </script>

                <!-- Cancel Booking Section -->
                @if($booking->canBeCancelled())
                    <div class="mt-8 pt-8 border-t border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Cancel Booking</h3>
                        <p class="text-sm text-gray-600 mb-4">
                            If you need to cancel this booking, you can do so below. 
                            @if($booking->status === 'confirmed')
                                Your reserved slots will be released and made available for other users.
                            @endif
                        </p>
                        <form action="{{ route('booking.destroy', $booking) }}" method="POST" onsubmit="return confirm('Are you sure you want to cancel this booking? This action cannot be undone.');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="px-6 py-2 bg-red-600 text-white rounded-xl hover:bg-red-700 transition">
                                Cancel Booking
                            </button>
                        </form>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>

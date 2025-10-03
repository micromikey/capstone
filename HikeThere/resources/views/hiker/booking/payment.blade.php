<x-app-layout>
    <div class="bg-gradient-to-br from-slate-50 via-blue-50 to-purple-50 min-h-screen py-10">
        <div class="max-w-4xl mx-auto px-4 md:px-8">
            <!-- Header -->
            <div class="text-center mb-8">
                <h1 class="text-3xl md:text-4xl font-bold text-gray-800 mb-2">Complete Your Payment</h1>
                <p class="text-gray-600 max-w-2xl mx-auto">Your booking has been created. Please complete payment to confirm your reservation.</p>
            </div>

            @if(session('success'))
                <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    {{ session('success') }}
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Payment Form Section -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-100 p-6">
                        <h2 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                            <svg class="w-6 h-6 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                            Payment Information
                        </h2>

                        <form method="POST" action="{{ route('booking.payment.submit', $booking) }}" enctype="multipart/form-data">
                            @csrf

                            @if($credentials->payment_method === 'manual' && $credentials->hasManualPaymentConfigured())
                                <!-- Manual Payment Section -->
                                <input type="hidden" name="payment_method" value="manual">
                                
                                <div class="space-y-6">
                                    <!-- QR Code Display -->
                                    <div class="bg-gradient-to-r from-green-50 to-emerald-50 border-2 border-green-200 rounded-lg p-6">
                                        <h3 class="font-semibold text-gray-800 mb-4 flex items-center">
                                            <svg class="w-5 h-5 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                                            </svg>
                                            Scan QR Code to Pay
                                        </h3>
                                        <p class="text-sm text-gray-700 mb-4">Scan this QR code with your mobile wallet (GCash, PayMaya, etc.) to complete payment of <strong>₱{{ number_format($booking->price_cents / 100, 2) }}</strong></p>
                                        
                                        <div class="flex justify-center mb-4 bg-white p-4 rounded-lg">
                                            <img src="{{ asset('storage/' . $credentials->qr_code_path) }}" alt="Payment QR Code" class="max-w-xs w-full border-2 border-gray-300 rounded-lg shadow-md">
                                        </div>

                                        @if($credentials->manual_payment_instructions)
                                            <div class="bg-blue-50 p-4 rounded border border-blue-200">
                                                <p class="text-sm font-semibold text-blue-900 mb-1">Payment Instructions:</p>
                                                <p class="text-sm text-blue-800">{{ $credentials->manual_payment_instructions }}</p>
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Upload Payment Proof -->
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-800 mb-2">
                                            Upload Payment Receipt <span class="text-red-500">*</span>
                                        </label>
                                        <p class="text-xs text-gray-600 mb-3">Take a screenshot of your payment confirmation and upload it here</p>
                                        
                                        <div id="payment-proof-preview-container" class="hidden mb-4">
                                            <div class="relative inline-block">
                                                <img id="payment-proof-preview" src="" alt="Payment Proof Preview" class="mx-auto h-48 object-contain border-2 border-green-500 rounded-lg shadow-lg">
                                                <button type="button" onclick="clearPreview()" class="absolute top-2 right-2 bg-red-500 hover:bg-red-600 text-white rounded-full p-2 shadow-lg transition-colors">
                                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>

                                        <div class="mt-2 flex justify-center px-4 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg hover:border-blue-400 transition-colors">
                                            <div class="space-y-1 text-center">
                                                <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                                </svg>
                                                <div class="flex text-sm text-gray-600">
                                                    <label for="payment-proof-upload" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none">
                                                        <span>Upload a file</span>
                                                        <input id="payment-proof-upload" name="payment_proof" type="file" accept="image/*" class="sr-only" required onchange="previewPaymentProof(event)">
                                                    </label>
                                                    <p class="pl-1">or drag and drop</p>
                                                </div>
                                                <p class="text-xs text-gray-500">PNG, JPG, GIF up to 10MB</p>
                                            </div>
                                        </div>
                                        @error('payment_proof')
                                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <!-- Transaction Number -->
                                    <div>
                                        <label for="transaction_number" class="block text-sm font-semibold text-gray-800 mb-2">
                                            Transaction/Reference Number <span class="text-red-500">*</span>
                                        </label>
                                        <input type="text" id="transaction_number" name="transaction_number" placeholder="Enter the transaction number from your payment" class="block w-full px-4 py-3 bg-white border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 text-gray-900 font-medium" required>
                                        @error('transaction_number')
                                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <!-- Payment Notes -->
                                    <div>
                                        <label for="payment_notes" class="block text-sm font-semibold text-gray-800 mb-2">
                                            Payment Notes (Optional)
                                        </label>
                                        <textarea id="payment_notes" name="payment_notes" rows="3" class="block w-full px-4 py-3 bg-white border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 text-gray-900 resize-none" placeholder="Any additional information about your payment..."></textarea>
                                    </div>

                                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                                        <div class="flex">
                                            <svg class="h-5 w-5 text-yellow-600 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                            </svg>
                                            <div>
                                                <p class="text-sm font-medium text-yellow-800">Payment Verification Required</p>
                                                <p class="text-xs text-yellow-700 mt-1">The organization will verify your payment proof. You'll receive a confirmation once it's approved.</p>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Submit Button -->
                                    <div class="flex gap-4">
                                        <a href="{{ route('booking.index') }}" class="flex-1 inline-flex justify-center items-center px-6 py-3 border border-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-50 transition-colors">
                                            Cancel
                                        </a>
                                        <button type="submit" class="flex-1 inline-flex justify-center items-center px-6 py-3 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg transition-colors shadow-lg">
                                            Submit Payment Proof
                                        </button>
                                    </div>
                                </div>

                            @else
                                <!-- Automatic Payment Section -->
                                <input type="hidden" name="payment_method" value="automatic">
                                
                                <div class="space-y-6">
                                    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border-2 border-blue-200 rounded-lg p-6 text-center">
                                        <svg class="mx-auto h-16 w-16 text-blue-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                                        </svg>
                                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Secure Payment Gateway</h3>
                                        <p class="text-sm text-gray-700 mb-4">
                                            You'll be redirected to a secure payment page to complete your payment of <strong>₱{{ number_format($booking->price_cents / 100, 2) }}</strong>
                                        </p>
                                        <p class="text-xs text-gray-600">
                                            We accept credit/debit cards and e-wallets through our secure payment partner.
                                        </p>
                                    </div>

                                    <div class="flex gap-4">
                                        <a href="{{ route('booking.index') }}" class="flex-1 inline-flex justify-center items-center px-6 py-3 border border-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-50 transition-colors">
                                            Cancel
                                        </a>
                                        <button type="submit" class="flex-1 inline-flex justify-center items-center px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition-colors shadow-lg">
                                            Proceed to Payment
                                        </button>
                                    </div>
                                </div>
                            @endif
                        </form>
                    </div>
                </div>

                <!-- Booking Summary Sidebar -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-100 p-6 sticky top-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Booking Summary</h3>
                        
                        <div class="space-y-4">
                            <div>
                                <p class="text-sm text-gray-600">Trail</p>
                                <p class="font-semibold text-gray-900">{{ $booking->trail->trail_name }}</p>
                            </div>

                            <div class="flex gap-4">
                                <div class="flex-1">
                                    <p class="text-sm text-gray-600">Date</p>
                                    <p class="font-semibold text-gray-900">{{ \Carbon\Carbon::parse($booking->date)->format('M d, Y') }}</p>
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm text-gray-600">Party Size</p>
                                    <p class="font-semibold text-gray-900">{{ $booking->party_size }} {{ Str::plural('person', $booking->party_size) }}</p>
                                </div>
                            </div>

                            @if($booking->notes)
                                <div>
                                    <p class="text-sm text-gray-600">Notes</p>
                                    <p class="text-sm text-gray-900">{{ Str::limit($booking->notes, 100) }}</p>
                                </div>
                            @endif

                            <div class="pt-4 border-t border-gray-200">
                                <div class="flex justify-between items-center mb-2">
                                    <span class="text-sm text-gray-600">Subtotal</span>
                                    <span class="font-semibold text-gray-900">₱{{ number_format($booking->price_cents / 100, 2) }}</span>
                                </div>
                                <div class="flex justify-between items-center pt-2 border-t border-gray-200">
                                    <span class="text-lg font-bold text-gray-900">Total</span>
                                    <span class="text-lg font-bold text-green-600">₱{{ number_format($booking->price_cents / 100, 2) }}</span>
                                </div>
                            </div>

                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
                                <p class="text-xs text-blue-800">
                                    <strong>Booking #{{ $booking->id }}</strong><br>
                                    Created: {{ $booking->created_at->format('M d, Y g:i A') }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function previewPaymentProof(event) {
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
                    const previewContainer = document.getElementById('payment-proof-preview-container');
                    const previewImage = document.getElementById('payment-proof-preview');
                    
                    previewImage.src = e.target.result;
                    previewContainer.classList.remove('hidden');
                }
                reader.readAsDataURL(file);
            }
        }

        function clearPreview() {
            const previewContainer = document.getElementById('payment-proof-preview-container');
            const fileInput = document.getElementById('payment-proof-upload');
            
            fileInput.value = '';
            previewContainer.classList.add('hidden');
        }
    </script>
</x-app-layout>

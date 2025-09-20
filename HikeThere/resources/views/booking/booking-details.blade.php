
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HikeThere</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .step-indicator.active {
            background: linear-gradient(135deg, #059669, #0d9488);
            box-shadow: 0 4px 20px rgba(5, 150, 105, 0.4);
            transform: scale(1.1);
        }
        .progress-bar-fill {
            background: linear-gradient(90deg, #059669, #0d9488);
            transition: width 0.8s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .form-field:focus {
            box-shadow: 0 0 0 3px rgba(5, 150, 105, 0.15);
            transform: translateY(-1px);
            transition: all 0.2s ease;
        }
        .glass-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        .gradient-bg {
            background: linear-gradient(135deg, #0f766e 0%, #059669 50%, #065f46 100%);
        }
        .payment-btn.selected {
            background: linear-gradient(135deg, #d1fae5, #a7f3d0);
            border-color: #059669;
            transform: scale(1.05);
            box-shadow: 0 8px 25px rgba(5, 150, 105, 0.25);
        }
        .animate-pulse-soft {
            animation: pulse-soft 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }
        @keyframes pulse-soft {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.7; }
        }
        .step-content {
            opacity: 0;
            transform: translateX(20px);
            transition: all 0.5s ease-in-out;
        }
        .step-content.active {
            opacity: 1;
            transform: translateX(0);
        }
    </style>
</head>
<body>
<x-app-layout>
    <div class="min-h-screen bg-gradient-to-br from-slate-50 via-emerald-50 to-teal-50 py-4">
        <div class="max-w-4xl mx-auto px-4 md:px-4">
            
            <!--  Progress Bar -->
            <div class="mb-6 glass-card rounded-xl shadow-lg p-4 border border-emerald-100">
                <div class="flex justify-between items-center mb-4">
                    <div>
                        <span id="step-text" class="text-lg font-bold text-gray-800">Step 1 of 3</span>
                        <span id="progress-label" class="block text-sm text-emerald-600 mt-1 font-medium">Booking Details</span>
                    </div>
                    <div class="text-right">
                        <div class="text-xl font-bold text-emerald-600" id="completion-percentage">33%</div>
                        <div class="text-xs text-gray-500">Complete</div>
                    </div>
                </div>
                
                <!--  Progress Bar -->
                <div class="relative">
                    <div class="w-full bg-gray-200 rounded-full h-2 mb-6 overflow-hidden">
                        <div id="progress-bar" class="progress-bar-fill h-2 rounded-full" style="width: 33%"></div>
                    </div>
                </div>
                
                <!--  Step Indicators -->
                <div class="flex justify-between relative">
                    <div class="absolute top-4 left-0 right-0 h-1 bg-gradient-to-r from-emerald-200 to-gray-200" style="z-index: 0;"></div>
                    
                    <div class="flex flex-col items-center relative z-10">
                        <div id="step1-indicator" class="step-indicator active w-8 h-8 rounded-full bg-emerald-500 text-white flex items-center justify-center text-sm font-bold shadow-lg transition-all duration-300">1</div>
                        <span class="text-xs mt-2 font-bold text-emerald-600">Details</span>
                    </div>
                    <div class="flex flex-col items-center relative z-10">
                        <div id="step2-indicator" class="step-indicator w-8 h-8 rounded-full bg-gray-300 text-gray-500 flex items-center justify-center text-sm font-bold shadow-md transition-all duration-300">2</div>
                        <span class="text-xs mt-2 font-semibold text-gray-500">Review</span>
                    </div>
                    <div class="flex flex-col items-center relative z-10">
                        <div id="step3-indicator" class="step-indicator w-8 h-8 rounded-full bg-gray-300 text-gray-500 flex items-center justify-center text-sm font-bold shadow-md transition-all duration-300">3</div>
                        <span class="text-xs mt-2 font-semibold text-gray-500">Payment</span>
                    </div>
                </div>
            </div>

            <div class="glass-card rounded-xl shadow-xl border border-emerald-100 overflow-hidden">
                <form id="bookingForm" method="POST" action="{{ route('bookings.store') }}">
                    @csrf

                    <!-- Step 1:  Booking Details -->
                    <div id="step1" class="step-content active p-4 space-y-6">
                        <div class="text-center mb-8">
                            <div class="text-2xl mb-3">⛰️</div>
                            <h2 class="text-2xl font-bold bg-gradient-to-r from-emerald-600 to-teal-600 bg-clip-text text-transparent mb-2">Plan Your Adventure</h2>
                            <p class="text-base text-gray-600">Select your trail and provide your information</p>
                        </div>

                        <!--  Trail Selection -->
                        <div class="bg-gradient-to-br from-emerald-50 via-emerald-25 to-teal-50 rounded-lg p-4 border border-emerald-200 shadow-md">
                            <h3 class="text-lg font-bold text-emerald-800 mb-4 flex items-center">
                                <span class="text-xl mr-2">🏔️</span>
                                Choose Your Trail
                            </h3>
                            <div>
                                <label class="block mb-3 text-gray-700 font-bold text-sm">Mountain/Trail</label>
                                <select name="trail" id="trail" class="form-field w-full border-2 border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-sm font-medium shadow-sm" required>
                                    <option value="">Select Your Adventure</option>
                                    <option value="mt_kulis" data-price="1500" {{ old('trail') == 'mt_kulis' ? 'selected' : '' }}>Mt. Kulis - ₱1,500</option>
                                    <option value="mt_mariglem" data-price="1500" {{ old('trail') == 'mt_mariglem' ? 'selected' : '' }}>Mt. Mariglem - ₱1,500</option>
                                    <option value="mt_tagapo" data-price="1500" {{ old('trail') == 'mt_tagapo' ? 'selected' : '' }}>Mt. Tagapo - ₱1,500</option>
                                    <option value="mt_batulao" data-price="1500" {{ old('trail') == 'mt_batulao' ? 'selected' : '' }}>Mt. Batulao - ₱1,500</option>
                                    <option value="mt_387" data-price="1500" {{ old('trail') == 'mt_387' ? 'selected' : '' }}>Mt. 387 - ₱1,500</option>
                                    <option value="mt_pulag" data-price="4900" {{ old('trail') == 'mt_pulag' ? 'selected' : '' }}>Mt. Pulag - ₱4,900</option>
                                    <option value="mt_fato" data-price="3900" {{ old('trail') == 'mt_fato' ? 'selected' : '' }}>Mt. Fato - ₱3,900</option>
                                    <option value="mt_malindig" data-price="5900" {{ old('trail') == 'mt_malindig' ? 'selected' : '' }}>Mt. Malindig - ₱5,900</option>
                                    <option value="mt_guiting" data-price="6500" {{ old('trail') == 'mt_guiting' ? 'selected' : '' }}>Mt. Guiting - ₱6,500</option>
                                    <option value="mt_apo" data-price="7500" {{ old('trail') == 'mt_apo' ? 'selected' : '' }}>Mt. Apo - ₱7,500</option>
                                </select>
                                @error('trail')
                                    <p class="mt-2 text-xs text-red-600 flex items-center bg-red-50 p-2 rounded-md">
                                        <span class="mr-1">⚠️</span>{{ $message }}
                                    </p>
                                @enderror
                            </div>
                        </div>

                        <!--  Date and Participants -->
                        <div class="grid md:grid-cols-2 gap-6">
                            <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-lg p-4 border border-blue-200 shadow-md">
                                <label class="block mb-3 text-gray-700 font-bold text-sm flex items-center">
                                    <span class="text-lg mr-2">📅</span>
                                    Adventure Date
                                </label>
                                <input type="date" name="hike_date" id="hike_date" value="{{ old('hike_date') }}" class="form-field w-full border-2 border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-sm shadow-sm" required>
                                @error('hike_date')
                                    <p class="mt-2 text-xs text-red-600 flex items-center bg-red-50 p-2 rounded-md">
                                        <span class="mr-1">⚠️</span>{{ $message }}
                                    </p>
                                @enderror
                            </div>
                            <div class="bg-gradient-to-br from-purple-50 to-pink-50 rounded-lg p-4 border border-purple-200 shadow-md">
                                <label class="block mb-3 text-gray-700 font-bold text-sm flex items-center">
                                    <span class="text-lg mr-2">👥</span>
                                    Adventurers Count
                                </label>
                                <input type="number" name="participants" id="participants" min="1" max="15" value="{{ old('participants', 1) }}" class="form-field w-full border-2 border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-sm shadow-sm" required>
                                @error('participants')
                                    <p class="mt-2 text-xs text-red-600 flex items-center bg-red-50 p-2 rounded-md">
                                        <span class="mr-1">⚠️</span>{{ $message }}
                                    </p>
                                @enderror
                            </div>
                        </div>

                        <!--  Customer Details -->
                        <div class="bg-gradient-to-br from-gray-50 to-slate-50 rounded-lg p-4 border border-gray-200 shadow-md">
                            <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                                <span class="text-xl mr-2">👤</span>
                                Your Information
                            </h3>
                            <div class="grid md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block mb-2 text-gray-700 font-bold text-sm">Full Name</label>
                                    <input type="text" name="fullname" id="fullname" value="{{ old('fullname') }}" class="form-field w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-sm shadow-sm" required>
                                    @error('fullname')
                                        <p class="mt-1 text-xs text-red-600 flex items-center bg-red-50 p-2 rounded-md">
                                            <span class="mr-1">⚠️</span>{{ $message }}
                                        </p>
                                    @enderror
                                </div>
                                <div>
                                    <label class="block mb-2 text-gray-700 font-bold text-sm">Email Address</label>
                                    <input type="email" name="email" id="email" value="{{ old('email') }}" class="form-field w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-sm shadow-sm" required>
                                    @error('email')
                                        <p class="mt-1 text-xs text-red-600 flex items-center bg-red-50 p-2 rounded-md">
                                            <span class="mr-1">⚠️</span>{{ $message }}
                                        </p>
                                    @enderror
                                </div>
                            </div>
                            <div class="grid md:grid-cols-2 gap-4 mt-4">
                                <div>
                                    <label class="block mb-2 text-gray-700 font-bold text-sm">Phone Number</label>
                                    <input type="tel" name="phone" id="phone" value="{{ old('phone') }}" class="form-field w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-sm shadow-sm" required>
                                    @error('phone')
                                        <p class="mt-1 text-xs text-red-600 flex items-center bg-red-50 p-2 rounded-md">
                                            <span class="mr-1">⚠️</span>{{ $message }}
                                        </p>
                                    @enderror
                                </div>
                                <div>
                                    <label class="block mb-2 text-gray-700 font-bold text-sm">Emergency Contact</label>
                                    <input type="text" name="emergency_contact" id="emergency_contact" value="{{ old('emergency_contact') }}" class="form-field w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-sm shadow-sm" placeholder="Name & Phone" required>
                                    @error('emergency_contact')
                                        <p class="mt-1 text-xs text-red-600 flex items-center bg-red-50 p-2 rounded-md">
                                            <span class="mr-1">⚠️</span>{{ $message }}
                                        </p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!--  Price Display -->
                        <div class="gradient-bg rounded-lg p-4 shadow-lg text-white">
                            <div class="flex justify-between items-center mb-3">
                                <span class="font-bold text-lg">Total Investment:</span>
                                <span id="total-amount" class="text-xl font-bold animate-pulse-soft">₱0</span>
                            </div>
                            <div class="flex justify-between items-center pt-3 border-t border-emerald-400">
                                <span class="text-base font-semibold">Downpayment Required (50%):</span>
                                <span id="down-payment" class="text-xl font-bold text-emerald-200">₱0</span>
                            </div>
                            <div class="mt-3 text-center">
                                <p class="text-emerald-100 text-xs">* Remaining balance due on hike day</p>
                            </div>
                        </div>

                        <div class="flex justify-end pt-4">
                            <button type="button" id="reviewBtn" class="bg-gradient-to-r from-emerald-600 to-teal-600 text-white font-bold py-3 px-8 rounded-lg hover:from-emerald-700 hover:to-teal-700 transform hover:scale-105 transition-all duration-300 shadow-lg text-sm">
                                Review Booking →
                            </button>
                        </div>
                    </div>

                    <!-- Step 2:  Review -->
                    <div id="step2" class="step-content hidden p-4 space-y-6">
                        <div class="text-center mb-8">
                            <div class="text-2xl mb-3">📋</div>
                            <h2 class="text-2xl font-bold bg-gradient-to-r from-emerald-600 to-teal-600 bg-clip-text text-transparent mb-2">Review Your Adventure</h2>
                            <p class="text-base text-gray-600">Verify all details before proceeding to payment</p>
                        </div>

                        <div class="grid md:grid-cols-2 gap-6">
                            <div class="bg-gradient-to-br from-emerald-50 to-teal-50 rounded-lg p-4 border border-emerald-200 shadow-lg">
                                <h3 class="text-lg font-bold text-emerald-800 mb-4 flex items-center">
                                    <span class="text-xl mr-2">🏔️</span>
                                    Adventure Details
                                </h3>
                                <div class="space-y-3">
                                    <div class="flex justify-between items-center bg-white p-3 rounded-lg shadow-sm">
                                        <span class="font-bold text-gray-700 text-sm">Trail:</span>
                                        <span id="review-trail" class="font-bold text-emerald-700 text-sm"></span>
                                    </div>
                                    <div class="flex justify-between items-center bg-white p-3 rounded-lg shadow-sm">
                                        <span class="font-bold text-gray-700 text-sm">Date:</span>
                                        <span id="review-date" class="font-bold text-gray-800 text-sm"></span>
                                    </div>
                                    <div class="flex justify-between items-center bg-white p-3 rounded-lg shadow-sm">
                                        <span class="font-bold text-gray-700 text-sm">Participants:</span>
                                        <span id="review-participants" class="font-bold text-purple-700 text-sm"></span>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-lg p-4 border border-blue-200 shadow-lg">
                                <h3 class="text-lg font-bold text-blue-800 mb-4 flex items-center">
                                    <span class="text-xl mr-2">👤</span>
                                    Contact Information
                                </h3>
                                <div class="space-y-3">
                                    <div class="flex justify-between items-center bg-white p-3 rounded-lg shadow-sm">
                                        <span class="font-bold text-gray-700 text-sm">Name:</span>
                                        <span id="review-name" class="font-bold text-gray-800 text-sm"></span>
                                    </div>
                                    <div class="flex justify-between items-center bg-white p-3 rounded-lg shadow-sm">
                                        <span class="font-bold text-gray-700 text-sm">Email:</span>
                                        <span id="review-email" class="font-bold text-blue-700 text-xs"></span>
                                    </div>
                                    <div class="flex justify-between items-center bg-white p-3 rounded-lg shadow-sm">
                                        <span class="font-bold text-gray-700 text-sm">Phone:</span>
                                        <span id="review-phone" class="font-bold text-gray-800 text-sm"></span>
                                    </div>
                                    <div class="flex justify-between items-center bg-white p-3 rounded-lg shadow-sm">
                                        <span class="font-bold text-gray-700 text-sm">Emergency:</span>
                                        <span id="review-emergency" class="font-bold text-red-600 text-xs"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                      
                        <div class="gradient-bg rounded-lg p-4 shadow-lg text-white">
                            <div class="flex justify-between items-center mb-3">
                                <span class="font-bold text-lg">Total Amount:</span>
                                <span id="review-total" class="text-xl font-bold"></span>
                            </div>
                            <div class="flex justify-between items-center pt-3 border-t border-emerald-400">
                                <span class="text-base font-semibold">Downpayment Required:</span>
                                <span id="review-down" class="text-xl font-bold text-emerald-200"></span>
                            </div>
                        </div>

                        <div class="flex justify-between pt-4">
                            <button type="button" id="backBtn" class="bg-gray-600 text-white font-bold py-3 px-8 rounded-lg hover:bg-gray-700 transform hover:scale-105 transition-all duration-300 shadow-lg text-sm">
                                ← Back to Edit
                            </button>
                            <button type="button" id="proceedBtn" class="bg-gradient-to-r from-emerald-600 to-teal-600 text-white font-bold py-3 px-8 rounded-lg hover:from-emerald-700 hover:to-teal-700 transform hover:scale-105 transition-all duration-300 shadow-lg text-sm">
                                Proceed to Payment →
                            </button>
                        </div>
                    </div>

                    <!-- Step 3:  Payment -->
                    <div id="step3" class="step-content hidden p-4 space-y-6">
                        <div class="text-center mb-8">
                            <div class="text-2xl mb-3">💳</div>
                            <h2 class="text-2xl font-bold bg-gradient-to-r from-emerald-600 to-teal-600 bg-clip-text text-transparent mb-2">Secure Your Spot</h2>
                            <p class="text-base text-gray-600">Choose your payment method and complete booking</p>
                        </div>

                        <!--  Payment Option Selection -->
                        <div class="bg-gradient-to-br from-yellow-50 to-orange-50 rounded-lg p-4 border border-yellow-200 shadow-md">
                            <label class="block text-base font-bold text-gray-800 mb-4">Payment Option</label>
                            <div class="grid md:grid-cols-2 gap-4">
                                <label class="payment-option-card cursor-pointer">
                                    <input type="radio" name="payment_option" value="downpayment" class="sr-only" checked>
                                    <div class="border-2 border-gray-300 rounded-lg p-4 hover:border-emerald-500 hover:bg-emerald-50 transition-all duration-300 shadow-sm">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <div class="text-base font-bold text-emerald-600 mb-1">💰 Downpayment</div>
                                                <div class="text-gray-600 text-sm">Pay 50% now, balance on hike day</div>
                                                <div class="text-sm font-bold text-emerald-700 mt-1">Amount: <span id="downpayment-amount">₱0</span></div>
                                            </div>
                                            <div class="payment-option-radio w-5 h-5 rounded-full border-2 border-emerald-500 bg-emerald-500"></div>
                                        </div>
                                    </div>
                                </label>
                                <label class="payment-option-card cursor-pointer">
                                    <input type="radio" name="payment_option" value="full" class="sr-only">
                                    <div class="border-2 border-gray-300 rounded-lg p-4 hover:border-emerald-500 hover:bg-emerald-50 transition-all duration-300 shadow-sm">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <div class="text-base font-bold text-blue-600 mb-1">💳 Full Payment</div>
                                                <div class="text-gray-600 text-sm">Pay complete amount now</div>
                                                <div class="text-sm font-bold text-blue-700 mt-1">Amount: <span id="fullpayment-amount">₱0</span></div>
                                            </div>
                                            <div class="payment-option-radio w-5 h-5 rounded-full border-2 border-gray-300"></div>
                                        </div>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <!--  Payment Methods -->
                        <div class="bg-gradient-to-br from-blue-50 to-purple-50 rounded-lg p-4 border border-blue-200 shadow-md">
                            <h3 class="text-lg font-bold text-blue-800 mb-4 flex items-center">
                                <span class="text-xl mr-2">💳</span>
                                Select Payment Method
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <button type="button" class="payment-btn border-2 border-gray-300 rounded-lg p-4 hover:border-emerald-500 hover:bg-emerald-50 transform hover:scale-105 transition-all duration-300 shadow-md" data-method="gcash">
                                    <div class="text-blue-600 font-bold text-lg mb-2">💰</div>
                                    <div class="text-base font-bold text-gray-800 mb-1">GCash</div>
                                    <div class="text-xs text-gray-600">Digital wallet payment</div>
                                </button>
                                <button type="button" class="payment-btn border-2 border-gray-300 rounded-lg p-4 hover:border-emerald-500 hover:bg-emerald-50 transform hover:scale-105 transition-all duration-300 shadow-md" data-method="bank">
                                    <div class="text-blue-800 font-bold text-lg mb-2">🏦</div>
                                    <div class="text-base font-bold text-gray-800 mb-1">Bank Transfer</div>
                                    <div class="text-xs text-gray-600">Direct bank payment</div>
                                </button>
                                <button type="button" class="payment-btn border-2 border-gray-300 rounded-lg p-4 hover:border-emerald-500 hover:bg-emerald-50 transform hover:scale-105 transition-all duration-300 shadow-md" data-method="paypal">
                                    <div class="text-blue-500 font-bold text-lg mb-2">💳</div>
                                    <div class="text-base font-bold text-gray-800 mb-1">PayPal</div>
                                    <div class="text-xs text-gray-600">Secure online payment</div>
                                </button>
                            </div>
                            <input type="hidden" name="payment_method" id="payment_method">
                        </div>







{{-- GCash Payment Modal --}}
<div id="gcashModal" class="hidden fixed inset-0 bg-black bg-opacity-60 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl max-w-lg w-full max-h-[95vh] overflow-hidden shadow-2xl">
        <div class="bg-gradient-to-r from-blue-600 to-blue-700 p-4 text-white">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <span class="text-2xl mr-3">💰</span>
                    <div>
                        <h3 class="text-xl font-bold">GCash Payment</h3>
                        <p class="text-blue-100 text-sm">Secure digital wallet payment</p>
                    </div>
                </div>
                <button id="closeGcashModal" type="button" class="text-white hover:text-gray-200 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>
        
        <form action="{{ route('booking.package-details') }}" method="GET" id="gcashPaymentForm" class="h-full">
            @csrf
            <input type="hidden" name="payment_method" value="gcash">
            <input type="hidden" name="booking_id" value="{{ $booking->id ?? '' }}">
            <input type="hidden" name="amount" id="gcashHiddenAmount" value="">
            
            <div class="p-6 space-y-6 max-h-[calc(95vh-160px)] overflow-y-auto">
                <!-- Payment Amount -->
                <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg p-4 border border-blue-200">
                    <div class="text-center">
                        <div class="text-sm font-semibold text-gray-600 mb-1">Amount to Pay</div>
                        <div id="gcashAmount" class="text-3xl font-bold text-blue-600">₱{{ number_format($total ?? 0, 2) }}</div>
                    </div>
                </div>

                <!-- Recipient Information -->
                <div class="bg-gray-50 rounded-lg p-4 border">
                    <h4 class="font-bold text-gray-800 mb-3 flex items-center">
                        <span class="text-lg mr-2">📱</span>
                        Send Payment to:
                    </h4>
                    <div class="space-y-3">
                        <div class="bg-white p-3 rounded-lg border-l-4 border-blue-500">
                            <div class="text-sm font-semibold text-gray-600">GCash Number</div>
                            <div class="text-lg font-bold text-gray-800">{{ config('payment.gcash.number', '+639 123 456 789') }}</div>
                        </div>
                        <div class="bg-white p-3 rounded-lg border-l-4 border-blue-500">
                            <div class="text-sm font-semibold text-gray-600">Account Name</div>
                            <div class="text-lg font-bold text-gray-800">{{ config('payment.gcash.account_name', 'Adventure Hiking Co.') }}</div>
                        </div>
                    </div>
                </div>

                <!-- Sender Information -->
                <div class="space-y-4">
                    <h5 class="font-bold text-gray-800 flex items-center">
                        <span class="text-lg mr-2">👤</span>
                        Your GCash Details
                    </h5>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="gcash_sender_name" class="block text-sm font-semibold text-gray-700 mb-2">
                                Full Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="gcash_sender_name" id="gcash_sender_name" 
                                class="w-full border-2 border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm @error('gcash_sender_name') border-red-500 @enderror" 
                                placeholder="Enter your full name" 
                                value="{{ old('gcash_sender_name', $user->name ?? '') }}" required>
                            @error('gcash_sender_name')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="gcash_sender_number" class="block text-sm font-semibold text-gray-700 mb-2">
                                Your GCash Number <span class="text-red-500">*</span>
                            </label>
                            <input type="tel" name="gcash_sender_number" id="gcash_sender_number" 
                                class="w-full border-2 border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm @error('gcash_sender_number') border-red-500 @enderror" 
                                placeholder="+639XXXXXXXXX" 
                                value="{{ old('gcash_sender_number', $user->phone ?? '') }}" required>
                            @error('gcash_sender_number')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Transaction Details -->
                <div class="space-y-4">
                    <h5 class="font-bold text-gray-800 flex items-center">
                        <span class="text-lg mr-2">🧾</span>
                        Transaction Information
                    </h5>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="gcash_reference" class="block text-sm font-semibold text-gray-700 mb-2">
                                GCash Reference Number <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="gcash_reference" id="gcash_reference" 
                                class="w-full border-2 border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm @error('gcash_reference') border-red-500 @enderror" 
                                placeholder="Enter transaction reference" 
                                value="{{ old('gcash_reference') }}" required>
                            @error('gcash_reference')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="gcash_transaction_date" class="block text-sm font-semibold text-gray-700 mb-2">
                                Transaction Date <span class="text-red-500">*</span>
                            </label>
                            <input type="datetime-local" name="gcash_transaction_date" id="gcash_transaction_date" 
                                class="w-full border-2 border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm @error('gcash_transaction_date') border-red-500 @enderror" 
                                value="{{ old('gcash_transaction_date', date('Y-m-d\TH:i')) }}" required>
                            @error('gcash_transaction_date')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    
                    <div>
                        <label for="gcash_notes" class="block text-sm font-semibold text-gray-700 mb-2">
                            Additional Notes (Optional)
                        </label>
                        <textarea name="gcash_notes" id="gcash_notes" rows="3" 
                            class="w-full border-2 border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm @error('gcash_notes') border-red-500 @enderror" 
                            placeholder="Any additional information about the transaction">{{ old('gcash_notes') }}</textarea>
                        @error('gcash_notes')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Instructions -->
                <div class="bg-amber-50 border border-amber-200 rounded-lg p-4">
                    <h5 class="font-bold text-amber-800 mb-3 text-sm flex items-center">
                        <span class="mr-2">ℹ️</span>
                        Payment Instructions:
                    </h5>
                    <ol class="space-y-2 text-amber-700 text-sm">
                        <li class="flex items-start">
                            <span class="font-bold mr-2">1.</span>
                            <span>Open your GCash app and select "Send Money"</span>
                        </li>
                        <li class="flex items-start">
                            <span class="font-bold mr-2">2.</span>
                            <span>Enter the mobile number: {{ config('payment.gcash.number', '+639 123 456 789') }}</span>
                        </li>
                        <li class="flex items-start">
                            <span class="font-bold mr-2">3.</span>
                            <span>Enter the exact amount: <strong id="gcashAmountText">₱{{ number_format($total ?? 0, 2) }}</strong></span>
                        </li>
                        <li class="flex items-start">
                            <span class="font-bold mr-2">4.</span>
                            <span>Add reference: {{ $booking->reference ?? 'Your booking reference' }}</span>
                        </li>
                        <li class="flex items-start">
                            <span class="font-bold mr-2">5.</span>
                            <span>Complete the transaction and fill the form above</span>
                        </li>
                    </ol>
                </div>
            </div>

            <div class="p-4 border-t border-gray-200 bg-gray-50 flex justify-between">
                <button type="button" id="cancelGcash" class="bg-gray-500 text-white font-semibold py-3 px-6 rounded-lg hover:bg-gray-600 transition-colors text-sm">
                    Cancel
                </button>
                <button type="submit" class="bg-gradient-to-r from-blue-600 to-blue-700 text-white font-semibold py-3 px-6 rounded-lg hover:from-blue-700 hover:to-blue-800 transition-all text-sm">
                    Confirm GCash Payment
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Bank Transfer Modal --}}
<div id="bankModal" class="hidden fixed inset-0 bg-black bg-opacity-60 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl max-w-lg w-full max-h-[95vh] overflow-hidden shadow-2xl">
        <div class="bg-gradient-to-r from-green-600 to-emerald-700 p-4 text-white">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <span class="text-2xl mr-3">🏦</span>
                    <div>
                        <h3 class="text-xl font-bold">Bank Transfer</h3>
                        <p class="text-green-100 text-sm">Direct bank account transfer</p>
                    </div>
                </div>
                <button id="closeBankModal" type="button" class="text-white hover:text-gray-200 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>
        
        <form action="{{ route('booking.package-details') }}" method="GET" id="bankPaymentForm"> 
            @csrf
            <input type="hidden" name="payment_method" value="bank_transfer">
            <input type="hidden" name="booking_id" value="{{ $booking->id ?? '' }}">
            <input type="hidden" name="amount" id="bankHiddenAmount" value="">
            
            <div class="p-6 space-y-6 max-h-[calc(95vh-160px)] overflow-y-auto">
                <!-- Payment Amount -->
                <div class="bg-gradient-to-r from-green-50 to-emerald-50 rounded-lg p-4 border border-green-200">
                    <div class="text-center">
                        <div class="text-sm font-semibold text-gray-600 mb-1">Amount to Transfer</div>
                        <div id="bankAmount" class="text-3xl font-bold text-green-600">₱{{ number_format($total ?? 0, 2) }}</div>
                    </div>
                </div>

                <!-- Bank Account Details -->
                <div class="bg-gray-50 rounded-lg p-4 border">
                    <h4 class="font-bold text-gray-800 mb-3 flex items-center">
                        <span class="text-lg mr-2">🏛️</span>
                        Transfer to Account:
                    </h4>
                    <div class="space-y-3">
                        <div class="bg-white p-3 rounded-lg border-l-4 border-green-500">
                            <div class="text-sm font-semibold text-gray-600">Bank Name</div>
                            <div class="text-lg font-bold text-gray-800">{{ config('payment.bank.name', 'BPI (Bank of the Philippine Islands)') }}</div>
                        </div>
                        <div class="bg-white p-3 rounded-lg border-l-4 border-green-500">
                            <div class="text-sm font-semibold text-gray-600">Account Number</div>
                            <div class="text-lg font-bold text-gray-800">{{ config('payment.bank.account_number', '1234-5678-90') }}</div>
                        </div>
                        <div class="bg-white p-3 rounded-lg border-l-4 border-green-500">
                            <div class="text-sm font-semibold text-gray-600">Account Name</div>
                            <div class="text-lg font-bold text-gray-800">{{ config('payment.bank.account_name', 'Adventure Hiking Co.') }}</div>
                        </div>
                    </div>
                </div>

                <!-- Sender Information -->
                <div class="space-y-4">
                    <h5 class="font-bold text-gray-800 flex items-center">
                        <span class="text-lg mr-2">👤</span>
                        Your Bank Details
                    </h5>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="bank_sender_name" class="block text-sm font-semibold text-gray-700 mb-2">
                                Account Holder Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="bank_sender_name" id="bank_sender_name" 
                                class="w-full border-2 border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-green-500 focus:border-green-500 text-sm @error('bank_sender_name') border-red-500 @enderror" 
                                placeholder="Enter account holder name" 
                                value="{{ old('bank_sender_name', $user->name ?? '') }}" required>
                            @error('bank_sender_name')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="bank_sender_account" class="block text-sm font-semibold text-gray-700 mb-2">
                                Your Account Number <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="bank_sender_account" id="bank_sender_account" 
                                class="w-full border-2 border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-green-500 focus:border-green-500 text-sm @error('bank_sender_account') border-red-500 @enderror" 
                                placeholder="XXXX-XXXX-XX" 
                                value="{{ old('bank_sender_account') }}" required>
                            @error('bank_sender_account')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    
                    <div>
                        <label for="bank_sender_bank" class="block text-sm font-semibold text-gray-700 mb-2">
                            Your Bank Name <span class="text-red-500">*</span>
                        </label>
                        <select name="bank_sender_bank" id="bank_sender_bank" 
                            class="w-full border-2 border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-green-500 focus:border-green-500 text-sm @error('bank_sender_bank') border-red-500 @enderror" required>
                            <option value="">Select your bank</option>
                            @foreach(config('payment.banks', ['BPI', 'BDO', 'Metrobank', 'Security Bank', 'PNB', 'UnionBank', 'RCBC', 'EastWest Bank']) as $bank)
                                <option value="{{ $bank }}" {{ old('bank_sender_bank') == $bank ? 'selected' : '' }}>{{ $bank }}</option>
                            @endforeach
                            <option value="Other" {{ old('bank_sender_bank') == 'Other' ? 'selected' : '' }}>Other</option>
                        </select>
                        @error('bank_sender_bank')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Transaction Details -->
                <div class="space-y-4">
                    <h5 class="font-bold text-gray-800 flex items-center">
                        <span class="text-lg mr-2">🧾</span>
                        Transaction Information
                    </h5>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="bank_reference" class="block text-sm font-semibold text-gray-700 mb-2">
                                Bank Reference Number <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="bank_reference" id="bank_reference" 
                                class="w-full border-2 border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-green-500 focus:border-green-500 text-sm @error('bank_reference') border-red-500 @enderror" 
                                placeholder="Enter transaction reference" 
                                value="{{ old('bank_reference') }}" required>
                            @error('bank_reference')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="bank_transaction_date" class="block text-sm font-semibold text-gray-700 mb-2">
                                Transaction Date <span class="text-red-500">*</span>
                            </label>
                            <input type="datetime-local" name="bank_transaction_date" id="bank_transaction_date" 
                                class="w-full border-2 border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-green-500 focus:border-green-500 text-sm @error('bank_transaction_date') border-red-500 @enderror" 
                                value="{{ old('bank_transaction_date', date('Y-m-d\TH:i')) }}" required>
                            @error('bank_transaction_date')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    
                    <div>
                        <label for="bank_notes" class="block text-sm font-semibold text-gray-700 mb-2">
                            Additional Notes (Optional)
                        </label>
                        <textarea name="bank_notes" id="bank_notes" rows="3" 
                            class="w-full border-2 border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-green-500 focus:border-green-500 text-sm @error('bank_notes') border-red-500 @enderror" 
                            placeholder="Any additional information about the transfer">{{ old('bank_notes') }}</textarea>
                        @error('bank_notes')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Instructions -->
                <div class="bg-amber-50 border border-amber-200 rounded-lg p-4">
                    <h5 class="font-bold text-amber-800 mb-3 text-sm flex items-center">
                        <span class="mr-2">ℹ️</span>
                        Transfer Instructions:
                    </h5>
                    <ol class="space-y-2 text-amber-700 text-sm">
                        <li class="flex items-start">
                            <span class="font-bold mr-2">1.</span>
                            <span>Log in to your online banking or visit the bank</span>
                        </li>
                        <li class="flex items-start">
                            <span class="font-bold mr-2">2.</span>
                            <span>Select "Fund Transfer" or "Send Money"</span>
                        </li>
                        <li class="flex items-start">
                            <span class="font-bold mr-2">3.</span>
                            <span>Enter recipient account details above</span>
                        </li>
                        <li class="flex items-start">
                            <span class="font-bold mr-2">4.</span>
                            <span>Transfer amount: <strong id="bankAmountText">₱{{ number_format($total ?? 0, 2) }}</strong></span>
                        </li>
                        <li class="flex items-start">
                            <span class="font-bold mr-2">5.</span>
                            <span>Use reference: {{ $booking->reference ?? 'Your booking reference' }}</span>
                        </li>
                    </ol>
                </div>
            </div>

            <div class="p-4 border-t border-gray-200 bg-gray-50 flex justify-between">
                <button type="button" id="cancelBank" class="bg-gray-500 text-white font-semibold py-3 px-6 rounded-lg hover:bg-gray-600 transition-colors text-sm">
                    Cancel
                </button>
                <button type="submit" class="bg-gradient-to-r from-green-600 to-emerald-700 text-white font-semibold py-3 px-6 rounded-lg hover:from-green-700 hover:to-emerald-800 transition-all text-sm">
                    Confirm Bank Transfer
                </button>
            </div>
        </form>
    </div>
</div>

{{-- PayPal Modal --}}
<div id="paypalModal" class="hidden fixed inset-0 bg-black bg-opacity-60 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl max-w-lg w-full max-h-[95vh] overflow-hidden shadow-2xl">
        <div class="bg-gradient-to-r from-purple-600 to-indigo-700 p-4 text-white">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <span class="text-2xl mr-3">💳</span>
                    <div>
                        <h3 class="text-xl font-bold">PayPal Payment</h3>
                        <p class="text-purple-100 text-sm">Digital wallet & card payment</p>
                    </div>
                </div>
                <button id="closePaypalModal" type="button" class="text-white hover:text-gray-200 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>
        
        <form action="{{ route('booking.package-details') }}" method="GET" id="paypalPaymentForm">
            @csrf
            <input type="hidden" name="payment_method" value="paypal">
            <input type="hidden" name="booking_id" value="{{ $booking->id ?? '' }}">
            <input type="hidden" name="amount" id="paypalHiddenAmount" value="">
            
            <div class="p-6 space-y-6 max-h-[calc(95vh-160px)] overflow-y-auto">
                <!-- Payment Amount -->
                <div class="bg-gradient-to-r from-purple-50 to-indigo-50 rounded-lg p-4 border border-purple-200">
                    <div class="text-center">
                        <div class="text-sm font-semibold text-gray-600 mb-1">Amount to Pay</div>
                        <div id="paypalAmount" class="text-3xl font-bold text-purple-600">₱{{ number_format($total ?? 0, 2) }}</div>
                    </div>
                </div>

                <!-- PayPal Account Details -->
                <div class="bg-gray-50 rounded-lg p-4 border">
                    <h4 class="font-bold text-gray-800 mb-3 flex items-center">
                        <span class="text-lg mr-2">📧</span>
                        Send Payment to:
                    </h4>
                    <div class="space-y-3">
                        <div class="bg-white p-3 rounded-lg border-l-4 border-purple-500">
                            <div class="text-sm font-semibold text-gray-600">PayPal Email</div>
                            <div class="text-lg font-bold text-gray-800">{{ config('payment.paypal.email', 'payments@adventurehiking.com') }}</div>
                        </div>
                        <div class="bg-white p-3 rounded-lg border-l-4 border-purple-500">
                            <div class="text-sm font-semibold text-gray-600">Account Name</div>
                            <div class="text-lg font-bold text-gray-800">{{ config('payment.paypal.account_name', 'Adventure Hiking Co.') }}</div>
                        </div>
                    </div>
                </div>

                <!-- Sender Information -->
                <div class="space-y-4">
                    <h5 class="font-bold text-gray-800 flex items-center">
                        <span class="text-lg mr-2">👤</span>
                        Your PayPal Details
                    </h5>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="paypal_sender_name" class="block text-sm font-semibold text-gray-700 mb-2">
                                Full Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="paypal_sender_name" id="paypal_sender_name" 
                                class="w-full border-2 border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm @error('paypal_sender_name') border-red-500 @enderror" 
                                placeholder="Enter your full name" 
                                value="{{ old('paypal_sender_name', $user->name ?? '') }}" required>
                            @error('paypal_sender_name')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="paypal_sender_email" class="block text-sm font-semibold text-gray-700 mb-2">
                                Your PayPal Email <span class="text-red-500">*</span>
                            </label>
                            <input type="email" name="paypal_sender_email" id="paypal_sender_email" 
                                class="w-full border-2 border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm @error('paypal_sender_email') border-red-500 @enderror" 
                                placeholder="your.email@example.com" 
                                value="{{ old('paypal_sender_email', $user->email ?? '') }}" required>
                            @error('paypal_sender_email')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Transaction Details -->
                <div class="space-y-4">
                    <h5 class="font-bold text-gray-800 flex items-center">
                        <span class="text-lg mr-2">🧾</span>
                        Transaction Information
                    </h5>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="paypal_transaction_id" class="block text-sm font-semibold text-gray-700 mb-2">
                                PayPal Transaction ID <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="paypal_transaction_id" id="paypal_transaction_id" 
                                class="w-full border-2 border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm @error('paypal_transaction_id') border-red-500 @enderror" 
                                placeholder="Enter PayPal transaction ID" 
                                value="{{ old('paypal_transaction_id') }}" required>
                            @error('paypal_transaction_id')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="paypal_transaction_date" class="block text-sm font-semibold text-gray-700 mb-2">
                                Transaction Date <span class="text-red-500">*</span>
                            </label>
                            <input type="datetime-local" name="paypal_transaction_date" id="paypal_transaction_date" 
                                class="w-full border-2 border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm @error('paypal_transaction_date') border-red-500 @enderror" 
                                value="{{ old('paypal_transaction_date', date('Y-m-d\TH:i')) }}" required>
                            @error('paypal_transaction_date')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    
                    <div>
                        <label for="paypal_payment_type" class="block text-sm font-semibold text-gray-700 mb-2">
                            Payment Type <span class="text-red-500">*</span>
                        </label>
                        <select name="paypal_payment_type" id="paypal_payment_type" 
                            class="w-full border-2 border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm @error('paypal_payment_type') border-red-500 @enderror" required>
                            <option value="">Select payment type</option>
                            <option value="paypal_balance" {{ old('paypal_payment_type') == 'paypal_balance' ? 'selected' : '' }}>PayPal Balance</option>
                            <option value="bank_account" {{ old('paypal_payment_type') == 'bank_account' ? 'selected' : '' }}>Bank Account</option>
                            <option value="credit_card" {{ old('paypal_payment_type') == 'credit_card' ? 'selected' : '' }}>Credit/Debit Card</option>
                        </select>
                        @error('paypal_payment_type')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="paypal_notes" class="block text-sm font-semibold text-gray-700 mb-2">
                            Payment Message/Notes (Optional)
                        </label>
                        <textarea name="paypal_notes" id="paypal_notes" rows="3" 
                            class="w-full border-2 border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm @error('paypal_notes') border-red-500 @enderror" 
                            placeholder="Any message sent with the payment">{{ old('paypal_notes') }}</textarea>
                        @error('paypal_notes')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Instructions -->
                <div class="bg-amber-50 border border-amber-200 rounded-lg p-4">
                    <h5 class="font-bold text-amber-800 mb-3 text-sm flex items-center">
                        <span class="mr-2">ℹ️</span>
                        Payment Instructions:
                    </h5>
                    <ol class="space-y-2 text-amber-700 text-sm">
                        <li class="flex items-start">
                            <span class="font-bold mr-2">1.</span>
                            <span>Open your PayPal app or visit paypal.com</span>
                        </li>
                        <li class="flex items-start">
                            <span class="font-bold mr-2">2.</span>
                            <span>Click "Send & Request" then "Send Money"</span>
                        </li>
                        <li class="flex items-start">
                            <span class="font-bold mr-2">3.</span>
                            <span>Enter email: {{ config('payment.paypal.email', 'payments@adventurehiking.com') }}</span>
                        </li>
                        <li class="flex items-start">
                            <span class="font-bold mr-2">4.</span>
                            <span>Send amount: <strong id="paypalAmountText">₱{{ number_format($total ?? 0, 2) }}</strong></span>
                        </li>
                        <li class="flex items-start">
                            <span class="font-bold mr-2">5.</span>
                            <span>Add note: {{ $booking->reference ?? 'Your booking reference' }}</span>
                        </li>
                        <li class="flex items-start">
                            <span class="font-bold mr-2">6.</span>
                            <span>Complete payment and fill the form above with transaction details</span>
                        </li>
                    </ol>
                </div>
            </div>

            <div class="p-4 border-t border-gray-200 bg-gray-50 flex justify-between">
                <button type="button" id="cancelPaypal" class="bg-gray-500 text-white font-semibold py-3 px-6 rounded-lg hover:bg-gray-600 transition-colors text-sm">
                    Cancel
                </button>
                <button type="submit" class="bg-gradient-to-r from-purple-600 to-indigo-700 text-white font-semibold py-3 px-6 rounded-lg hover:from-purple-700 hover:to-indigo-800 transition-all text-sm">
                    Confirm PayPal Payment
                </button>
            </div>
        </form>
    </div>
</div>







                        <!--  Payment Summary -->
                        <div class="bg-gradient-to-r from-emerald-100 to-teal-100 rounded-lg p-4 border-2 border-emerald-300 shadow-lg">
                            <h4 class="font-bold text-emerald-800 mb-4 text-lg flex items-center">
                                <span class="text-xl mr-2">📋</span>
                                Payment Summary
                            </h4>
                            <div class="grid md:grid-cols-2 gap-4">
                                <div class="bg-white rounded-lg p-4 shadow-sm">
                                    <div class="text-sm font-bold text-gray-700 mb-1">Amount Due Today:</div>
                                    <div id="paymentAmount" class="text-2xl font-bold text-emerald-600">₱0</div>
                                </div>
                                <div class="bg-white rounded-lg p-4 shadow-sm">
                                    <div class="text-sm font-bold text-gray-700 mb-1">Remaining Balance:</div>
                                    <div id="remainingBalance" class="text-2xl font-bold text-orange-600">₱0</div>
                                </div>
                            </div>
                            
                            <div class="mt-4">
                                <div class="bg-amber-50 border border-amber-200 rounded-lg p-4">
                                    <h5 class="font-bold text-amber-800 mb-3 text-sm">Payment Instructions:</h5>
                                    <ul class="space-y-1 text-gray-700 text-xs">
                                        <li class="flex items-center"><span class="text-emerald-600 mr-2">✓</span> Complete payment within 24 hours to secure booking</li>
                                        <li class="flex items-center"><span class="text-emerald-600 mr-2">✓</span> Confirmation email will be sent after payment</li>
                                        <li class="flex items-center"><span class="text-emerald-600 mr-2">✓</span> Bring valid ID and remaining balance on hike day</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <!--  Terms and Conditions -->
                        <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 shadow-md">
                            <div class="flex items-start">
                                <input type="checkbox" id="terms" name="terms" class="mr-3 mt-1 w-4 h-4 text-emerald-600 border-2 border-gray-300 rounded focus:ring-emerald-500 focus:ring-2" required>
                                <div class="flex-1">
                                    <label for="terms" class="text-gray-800 font-bold text-sm cursor-pointer">I agree to the terms and conditions</label>
                                    <div class="mt-3">
                                        <button type="button" id="showTerms" class="bg-emerald-600 hover:bg-emerald-700 text-white font-medium px-4 py-2 rounded-lg transition-colors duration-200 shadow-sm text-xs">
                                            📄 Read Terms & Conditions
                                        </button>
                                    </div>
                                </div>
                            </div>
                            @error('terms')
                                <p class="mt-2 text-xs text-red-600 flex items-center bg-red-50 p-2 rounded-md">
                                    <span class="mr-1">⚠️</span>{{ $message }}
                                </p>
                            @enderror
                        </div>

                        <div class="flex justify-between pt-4">
                            <button type="button" id="backToReviewBtn" class="bg-gray-600 text-white font-bold py-3 px-8 rounded-lg hover:bg-gray-700 transform hover:scale-105 transition-all duration-300 shadow-lg text-sm">
                                ← Back to Review
                            </button>
                            <button type="submit" id="confirmBtn" class="bg-gradient-to-r from-emerald-600 to-teal-600 text-white font-bold py-3 px-8 rounded-lg hover:from-emerald-700 hover:to-teal-700 transform hover:scale-105 transition-all duration-300 shadow-lg disabled:bg-gray-400 disabled:transform-none text-sm" disabled>
                                Confirm Booking ✓
                            </button>
                        </div>
                    </div>

                    <!--  Success Message -->
                    <div id="success" class="step-content hidden text-center py-12">
                        <div class="text-4xl mb-6">🎉</div>
                        <h2 class="text-2xl font-bold bg-gradient-to-r from-emerald-600 to-teal-600 bg-clip-text text-transparent mb-4">Adventure Booked!</h2>
                        <p class="text-lg text-gray-600 mb-8">Your hiking adventure is confirmed. Get ready for an amazing experience!</p>
                        <div class="bg-gradient-to-r from-emerald-100 to-teal-100 rounded-xl p-8 max-w-xl mx-auto border-2 border-emerald-300 shadow-xl">
                            <div class="text-2xl mb-3">⛰️</div>
                            <div class="font-bold text-emerald-800 text-xl mb-3">See You on the Trails!</div>
                            <div class="text-emerald-700 text-base">Check your email for booking confirmation and detailed instructions.</div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!--  Terms and Conditions Modal -->
    <div id="termsModal" class="hidden fixed inset-0 bg-black bg-opacity-60 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-xl max-w-3xl w-full max-h-[85vh] overflow-hidden shadow-2xl">
            <div class="gradient-bg p-4 text-white">
                <h3 class="text-xl font-bold">Terms and Conditions</h3>
                <p class="text-emerald-100 mt-1 text-sm">Please read carefully before proceeding</p>
            </div>
            <div class="p-4 space-y-4 text-gray-700 overflow-y-auto max-h-96 text-sm">
                <div>
                    <h4 class="font-bold text-base text-emerald-800 mb-2">1. Booking and Payment Policy</h4>
                    <ul class="space-y-1 ml-4 text-sm">
                        <li>• Downpayment of 50% is required to secure your booking</li>
                        <li>• Full payment must be completed 48 hours before the hike date</li>
                        <li>• All payments are non-refundable unless cancelled by the organizer</li>
                        <li>• Payment confirmation email will be sent within 24 hours</li>
                    </ul>
                </div>
                
                <div>
                    <h4 class="font-bold text-base text-emerald-800 mb-2">2. Cancellation Policy</h4>
                    <ul class="space-y-1 ml-4 text-sm">
                        <li>• Cancellations made 7+ days before hike: 80% refund of downpayment</li>
                        <li>• Cancellations made 3-6 days before hike: 50% refund of downpayment</li>
                        <li>• Cancellations made less than 3 days before: No refund</li>
                        <li>• Weather-related cancellations: Full refund or reschedule option</li>
                    </ul>
                </div>
                
                <div>
                    <h4 class="font-bold text-base text-emerald-800 mb-2">3. Safety Requirements</h4>
                    <ul class="space-y-1 ml-4 text-sm">
                        <li>• Participants must be physically fit and medically cleared for hiking</li>
                        <li>• Mandatory to follow guide instructions at all times</li>
                        <li>• Proper hiking gear and equipment are required</li>
                        <li>• Age restrictions: 12+ years old, minors must be accompanied by adults</li>
                    </ul>
                </div>
                
                <div>
                    <h4 class="font-bold text-base text-emerald-800 mb-2">4. Liability and Insurance</h4>
                    <ul class="space-y-1 ml-4 text-sm">
                        <li>• Participation is at your own risk and responsibility</li>
                        <li>• Company is not liable for accidents, injuries, or loss of personal items</li>
                        <li>• Travel and medical insurance is highly recommended</li>
                        <li>• Emergency contact information is mandatory</li>
                    </ul>
                </div>
                
                <div>
                    <h4 class="font-bold text-base text-emerald-800 mb-2">5. Weather and Environmental Conditions</h4>
                    <ul class="space-y-1 ml-4 text-sm">
                        <li>• Hikes may be cancelled due to severe weather conditions</li>
                        <li>• Alternative dates will be offered or full refund provided</li>
                        <li>• Participants must be prepared for changing weather conditions</li>
                        <li>• Leave No Trace principles must be followed</li>
                    </ul>
                </div>
            </div>
            <div class="p-4 border-t border-gray-200 flex justify-end bg-gray-50">
                <button id="closeTerms" class="bg-emerald-600 text-white font-bold py-3 px-6 rounded-lg hover:bg-emerald-700 transition-colors duration-200 shadow-md text-sm">
                    I Understand
                </button>
            </div>
        </div>
    </div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // --- Variables ---
    const trail = document.getElementById('trail');
    const participants = document.getElementById('participants');
    const totalAmount = document.getElementById('total-amount');
    const downPayment = document.getElementById('down-payment');
    const fullname = document.getElementById('fullname');
    const email = document.getElementById('email');
    const phone = document.getElementById('phone');
    const emergencyContact = document.getElementById('emergency_contact');
    const paymentOptions = document.querySelectorAll('input[name="payment_option"]');
    const downpaymentAmountEl = document.getElementById('downpayment-amount');
    const fullpaymentAmountEl = document.getElementById('fullpayment-amount');
    const paymentAmountEl = document.getElementById('paymentAmount');
    const remainingBalanceEl = document.getElementById('remainingBalance');
    const step1 = document.getElementById('step1');
    const step2 = document.getElementById('step2');
    const step3 = document.getElementById('step3');
    const success = document.getElementById('success');
    const progressBar = document.getElementById('progress-bar');
    const stepText = document.getElementById('step-text');
    const progressLabel = document.getElementById('progress-label');
    const completionPercentage = document.getElementById('completion-percentage');
    const stepIndicators = [
        document.getElementById('step1-indicator'),
        document.getElementById('step2-indicator'),
        document.getElementById('step3-indicator')
    ];
    const reviewBtn = document.getElementById('reviewBtn');
    const backBtn = document.getElementById('backBtn');
    const proceedBtn = document.getElementById('proceedBtn');
    const backToReviewBtn = document.getElementById('backToReviewBtn');
    const confirmBtn = document.getElementById('confirmBtn');
    const paymentBtns = document.querySelectorAll('.payment-btn');
    const termsCheckbox = document.getElementById('terms');
    const paymentInput = document.getElementById('payment_method');
    const termsModal = document.getElementById('termsModal');
    const showTermsBtn = document.getElementById('showTerms');
    const closeTermsBtn = document.getElementById('closeTerms');
    // --- Modal Elements ---
    const gcashModal = document.getElementById('gcashModal');
    const bankModal = document.getElementById('bankModal');
    const paypalModal = document.getElementById('paypalModal');
    const closeGcashModal = document.getElementById('closeGcashModal');
    const closeBankModal = document.getElementById('closeBankModal');
    const closePaypalModal = document.getElementById('closePaypalModal');
    const cancelGcash = document.getElementById('cancelGcash');
    const cancelBank = document.getElementById('cancelBank');
    const cancelPaypal = document.getElementById('cancelPaypal');
    const gcashForm = document.getElementById('gcashPaymentForm');
    const bankForm = document.getElementById('bankPaymentForm');
    const paypalForm = document.getElementById('paypalPaymentForm');
    // --- Price Calculation ---
    let currentTotal = 0;
    let currentDownPayment = 0;
    function updatePrice() {
        const selectedTrail = trail.selectedOptions[0];
        const price = selectedTrail && selectedTrail.dataset.price ? parseInt(selectedTrail.dataset.price) : 0;
        const participantCount = parseInt(participants.value) || 1;
        currentTotal = price * participantCount;
        currentDownPayment = Math.ceil(currentTotal * 0.5);
        totalAmount.textContent = '₱' + currentTotal.toLocaleString();
        downPayment.textContent = '₱' + currentDownPayment.toLocaleString();
        if (downpaymentAmountEl) downpaymentAmountEl.textContent = '₱' + currentDownPayment.toLocaleString();
        if (fullpaymentAmountEl) fullpaymentAmountEl.textContent = '₱' + currentTotal.toLocaleString();
        updatePaymentSummary();
    }
    function updatePaymentSummary() {
        const selectedOption = document.querySelector('input[name="payment_option"]:checked');
        if (selectedOption) {
            if (selectedOption.value === 'downpayment') {
                paymentAmountEl.textContent = '₱' + currentDownPayment.toLocaleString();
                remainingBalanceEl.textContent = '₱' + (currentTotal - currentDownPayment).toLocaleString();
            } else {
                paymentAmountEl.textContent = '₱' + currentTotal.toLocaleString();
                remainingBalanceEl.textContent = '₱0';
            }
        }
    }
    // --- Progress Bar ---
    function updateProgress(step) {
        const progressPercentages = [0, 33, 66, 100];
        const progress = progressPercentages[step - 1];
        progressBar.style.width = progress + '%';
        stepText.textContent = `Step ${step} of 3`;
        completionPercentage.textContent = progress + '%';
        const labels = ['Booking Details', 'Review Booking', 'Payment Method'];
        progressLabel.textContent = labels[step - 1];
        stepIndicators.forEach((indicator, index) => {
            const stepNum = index + 1;
            const textEl = indicator.nextElementSibling;
            indicator.className = 'step-indicator w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold shadow-lg transition-all duration-300';
            if (stepNum < step) {
                indicator.classList.add('bg-emerald-500', 'text-white');
                indicator.innerHTML = '✓';
                textEl.className = 'text-xs mt-2 font-bold text-emerald-600';
            } else if (stepNum === step) {
                indicator.classList.add('active', 'bg-emerald-500', 'text-white');
                indicator.innerHTML = stepNum;
                textEl.className = 'text-xs mt-2 font-bold text-emerald-600';
            } else {
                indicator.classList.add('bg-gray-300', 'text-gray-500');
                indicator.innerHTML = stepNum;
                textEl.className = 'text-xs mt-2 font-semibold text-gray-500';
            }
        });
    }
    function showStep(stepNum) {
        [step1, step2, step3, success].forEach(el => {
            el.classList.remove('active');
            el.classList.add('hidden');
        });
        setTimeout(() => {
            if (stepNum === 1) {
                step1.classList.remove('hidden');
                setTimeout(() => step1.classList.add('active'), 50);
            } else if (stepNum === 2) {
                step2.classList.remove('hidden');
                setTimeout(() => step2.classList.add('active'), 50);
            } else if (stepNum === 3) {
                step3.classList.remove('hidden');
                setTimeout(() => step3.classList.add('active'), 50);
            } else if (stepNum === 4) {
                success.classList.remove('hidden');
                setTimeout(() => success.classList.add('active'), 50);
                document.querySelector('.max-w-4xl > div:first-child').style.display = 'none';
            }
            if (stepNum <= 3) {
                updateProgress(stepNum);
                document.querySelector('.max-w-4xl > div:first-child').style.display = 'block';
            }
        }, 100);
    }
    // --- Step 1 Validation ---
    function validateStep1() {
        const required = [
            { el: trail, label: 'Trail selection' },
            { el: document.getElementById('hike_date'), label: 'Hike date' },
            { el: participants, label: 'Number of participants' },
            { el: fullname, label: 'Full name' },
            { el: email, label: 'Email address' },
            { el: phone, label: 'Phone number' },
            { el: emergencyContact, label: 'Emergency contact' }
        ];
        let valid = true;
        let firstInvalid = null;
        required.forEach(field => {
            if (!field.el.value.trim()) {
                field.el.classList.add('border-red-500', 'bg-red-50');
                field.el.classList.remove('border-gray-300');
                if (!firstInvalid) firstInvalid = field.el;
                valid = false;
            } else {
                field.el.classList.remove('border-red-500', 'bg-red-50');
                field.el.classList.add('border-gray-300');
            }
        });
        if (email.value && !email.value.match(/^[^\s@]+@[^\s@]+\.[^\s@]+$/)) {
            email.classList.add('border-red-500', 'bg-red-50');
            valid = false;
            if (!firstInvalid) firstInvalid = email;
        }
        if (phone.value && !phone.value.match(/^[\d\s\-\+\(\)]{10,}$/)) {
            phone.classList.add('border-red-500', 'bg-red-50');
            valid = false;
            if (!firstInvalid) firstInvalid = phone;
        }
        const hikeDate = document.getElementById('hike_date');
        if (hikeDate.value) {
            const selectedDate = new Date(hikeDate.value);
            const today = new Date();
            today.setHours(0, 0, 0, 0);
            if (selectedDate <= today) {
                hikeDate.classList.add('border-red-500', 'bg-red-50');
                valid = false;
                if (!firstInvalid) firstInvalid = hikeDate;
            }
        }
        if (firstInvalid) {
            firstInvalid.focus();
            firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
        return valid;
    }
    // --- Review Population ---
    function populateReview() {
        const trailText = trail.selectedOptions[0]?.text || '';
        const hikeDate = document.getElementById('hike_date').value;
        const dateObj = new Date(hikeDate);
        const formattedDate = dateObj.toLocaleDateString('en-US', { 
            weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' 
        });
        document.getElementById('review-trail').textContent = trailText;
        document.getElementById('review-date').textContent = formattedDate;
        document.getElementById('review-participants').textContent = participants.value + ' person(s)';
        document.getElementById('review-name').textContent = fullname.value;
        document.getElementById('review-email').textContent = email.value;
        document.getElementById('review-phone').textContent = phone.value;
        document.getElementById('review-emergency').textContent = emergencyContact.value;
        document.getElementById('review-total').textContent = totalAmount.textContent;
        document.getElementById('review-down').textContent = downPayment.textContent;
    }
    // --- Payment Option Radios ---
    function updatePaymentOptionRadios() {
        document.querySelectorAll('.payment-option-card').forEach(card => {
            const radio = card.querySelector('input[type="radio"]');
            const radioEl = card.querySelector('.payment-option-radio');
            if (radio.checked) {
                card.querySelector('div').classList.add('border-emerald-500', 'bg-emerald-50');
                radioEl.classList.remove('border-gray-300');
                radioEl.classList.add('border-emerald-500', 'bg-emerald-500');
            } else {
                card.querySelector('div').classList.remove('border-emerald-500', 'bg-emerald-50');
                radioEl.classList.add('border-gray-300');
                radioEl.classList.remove('border-emerald-500', 'bg-emerald-500');
            }
        });
    }
    // --- Confirm Button State ---
    function checkConfirmBtn() {
        const paymentSelected = paymentInput.value;
        const termsAccepted = termsCheckbox.checked;
        const isEnabled = paymentSelected && termsAccepted;
        confirmBtn.disabled = !isEnabled;
        if (isEnabled) {
            confirmBtn.classList.remove('bg-gray-400', 'cursor-not-allowed');
            confirmBtn.classList.add('bg-gradient-to-r', 'from-emerald-600', 'to-teal-600', 'hover:from-emerald-700', 'hover:to-teal-700');
        } else {
            confirmBtn.classList.add('bg-gray-400', 'cursor-not-allowed');
            confirmBtn.classList.remove('bg-gradient-to-r', 'from-emerald-600', 'to-teal-600', 'hover:from-emerald-700', 'hover:to-teal-700');
        }
    }
    // --- Notification System ---
    function showNotification(message, type = 'error') {
        document.querySelectorAll('.notification').forEach(n => n.remove());
        const notification = document.createElement('div');
        notification.className = `notification fixed top-4 right-4 p-6 rounded-2xl shadow-2xl z-50 transition-all duration-500 transform translate-x-full ${
            type === 'success' 
                ? 'bg-gradient-to-r from-emerald-500 to-teal-500 text-white' 
                : 'bg-gradient-to-r from-red-500 to-pink-500 text-white'
        }`;
        notification.innerHTML = `
            <div class="flex items-center">
                <span class="mr-3 text-2xl">${type === 'success' ? '✅' : '⚠️'}</span>
                <span class="font-semibold">${message}</span>
            </div>
        `;
        document.body.appendChild(notification);
        setTimeout(() => {
            notification.classList.remove('translate-x-full');
        }, 100);
        setTimeout(() => {
            notification.classList.add('translate-x-full', 'opacity-0');
            setTimeout(() => notification.remove(), 500);
        }, 4000);
    }
    // --- Payment Method Selection ---
    paymentBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            paymentBtns.forEach(b => b.classList.remove('selected', 'border-emerald-500', 'bg-emerald-50'));
            btn.classList.add('selected', 'border-emerald-500', 'bg-emerald-50');
            paymentInput.value = btn.dataset.method;
            checkConfirmBtn();
            showNotification(`${btn.querySelector('div:nth-child(2)').textContent} selected successfully!`, 'success');
        });
    });
    // --- Event Listeners ---
    trail.addEventListener('change', updatePrice);
    participants.addEventListener('input', updatePrice);
    paymentOptions.forEach(option => {
        option.addEventListener('change', () => {
            updatePaymentSummary();
            updatePaymentOptionRadios();
        });
    });
    reviewBtn.addEventListener('click', () => {
        if (validateStep1()) {
            populateReview();
            showStep(2);
            showNotification('Booking details validated successfully!', 'success');
        } else {
            showNotification('Please fill in all required fields correctly.');
        }
    });
    backBtn.addEventListener('click', () => showStep(1));
    proceedBtn.addEventListener('click', () => showStep(3));
    backToReviewBtn.addEventListener('click', () => showStep(2));
    termsCheckbox.addEventListener('change', checkConfirmBtn);
    showTermsBtn.addEventListener('click', (e) => {
        e.preventDefault();
        termsModal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    });
    closeTermsBtn.addEventListener('click', () => {
        termsModal.classList.add('hidden');
        document.body.style.overflow = 'auto';
    });
    termsModal.addEventListener('click', (e) => {
        if (e.target === termsModal) {
            termsModal.classList.add('hidden');
            document.body.style.overflow = 'auto';
        }
    });
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && !termsModal.classList.contains('hidden')) {
            termsModal.classList.add('hidden');
            document.body.style.overflow = 'auto';
        }
    });
    // --- Form Submission ---
    document.getElementById('bookingForm').addEventListener('submit', function(e) {
        e.preventDefault();
        if (step3.classList.contains('hidden')) return false;
        if (!paymentInput.value) {
            showNotification('Please select a payment method.');
            return false;
        }
        if (!termsCheckbox.checked) {
            showNotification('Please accept the terms and conditions.');
            return false;
        }
        const originalText = confirmBtn.innerHTML;
        confirmBtn.innerHTML = `
            <div class="flex items-center justify-center">
                <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-white mr-3"></div>
                <span>Processing Your Booking...</span>
            </div>
        `;
        confirmBtn.disabled = true;
        setTimeout(() => {
            showStep(4);
            showNotification('🎉 Booking confirmed successfully! Adventure awaits!', 'success');
            setTimeout(() => {
                document.getElementById('bookingForm').reset();
                updatePrice();
                updatePaymentOptionRadios();
                paymentBtns.forEach(btn => {
                    btn.classList.remove('selected', 'border-emerald-500', 'bg-emerald-50');
                    btn.classList.add('border-gray-300');
                });
                paymentInput.value = '';
                showStep(1);
                confirmBtn.innerHTML = originalText;
                confirmBtn.disabled = false;
                checkConfirmBtn();
            }, 8000);
        }, 2500);
    });
    // --- Form Field Interactions ---
    document.querySelectorAll('.form-field').forEach(field => {
        field.addEventListener('focus', () => {
            field.classList.add('transform', 'scale-105');
        });
        field.addEventListener('blur', () => {
            field.classList.remove('transform', 'scale-105');
        });
    });
    // --- Set Minimum Date ---
    const tomorrow = new Date();
    tomorrow.setDate(tomorrow.getDate() + 1);
    document.getElementById('hike_date').min = tomorrow.toISOString().split('T')[0];



        // --- Payment Modal Logic ---
    function openModal(modal) {
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }
    function closeModal(modal) {
        modal.classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    // Payment method button triggers
    paymentBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            paymentBtns.forEach(b => b.classList.remove('selected', 'border-emerald-500', 'bg-emerald-50'));
            btn.classList.add('selected', 'border-emerald-500', 'bg-emerald-50');
            paymentInput.value = btn.dataset.method;
            checkConfirmBtn();
            showNotification(`${btn.querySelector('div:nth-child(2)').textContent} selected successfully!`, 'success');

            // Open the corresponding modal
            if (btn.dataset.method === 'gcash') openModal(gcashModal);
            if (btn.dataset.method === 'bank') openModal(bankModal);
            if (btn.dataset.method === 'paypal') openModal(paypalModal);
        });
    });

    // Modal close buttons
    if (closeGcashModal) closeGcashModal.addEventListener('click', () => closeModal(gcashModal));
    if (closeBankModal) closeBankModal.addEventListener('click', () => closeModal(bankModal));
    if (closePaypalModal) closePaypalModal.addEventListener('click', () => closeModal(paypalModal));
    if (cancelGcash) cancelGcash.addEventListener('click', () => closeModal(gcashModal));
    if (cancelBank) cancelBank.addEventListener('click', () => closeModal(bankModal));
    if (cancelPaypal) cancelPaypal.addEventListener('click', () => closeModal(paypalModal));

    // Clicking outside modal closes it
    [gcashModal, bankModal, paypalModal].forEach(modal => {
        if (modal) {
            modal.addEventListener('click', function(e) {
                if (e.target === modal) closeModal(modal);
            });
        }
    });

    // --- Confirm Booking Button Fix ---
    document.getElementById('bookingForm').addEventListener('submit', function(e) {
        e.preventDefault();
        if (step3.classList.contains('hidden')) return false;
        if (!paymentInput.value) {
            showNotification('Please select a payment method.');
            return false;
        }
        if (!termsCheckbox.checked) {
            showNotification('Please accept the terms and conditions.');
            return false;
        }
        // Optionally, check if payment modal form is filled before proceeding
        // You can add more validation here if needed

        const originalText = confirmBtn.innerHTML;
        confirmBtn.innerHTML = `
            <div class="flex items-center justify-center">
                <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-white mr-3"></div>
                <span>Processing Your Booking...</span>
            </div>
        `;
        confirmBtn.disabled = true;
        setTimeout(() => {
            showStep(4);
            showNotification('🎉 Booking confirmed successfully! Adventure awaits!', 'success');
            setTimeout(() => {
                document.getElementById('bookingForm').reset();
                updatePrice();
                updatePaymentOptionRadios();
                paymentBtns.forEach(btn => {
                    btn.classList.remove('selected', 'border-emerald-500', 'bg-emerald-50');
                    btn.classList.add('border-gray-300');
                });
                paymentInput.value = '';
                showStep(1);
                confirmBtn.innerHTML = originalText;
                confirmBtn.disabled = false;
                checkConfirmBtn();
            }, 8000);
        }, 2500);
    });


    // --- Initialize ---
    updatePrice();
    showStep(1);
    checkConfirmBtn();
    updatePaymentOptionRadios();



});
</script>


<style>
/* Overlay (full screen) */
.modal-overlay {
    position: fixed;
    inset: 0; /* shorthand for top:0; right:0; bottom:0; left:0 */
    background: rgba(0, 0, 0, 0.6);
    z-index: 9999;
    display: flex;              /* ✅ center with flex */
    align-items: center;
    justify-content: center;
    animation: fadeIn 0.3s ease-in-out;
    padding: 1rem; 
    overflow-y: auto;
}

/* Modal content (centered box) */
.modal-content {
    background: #fff;
    border-radius: 1rem;
    padding: 1.5rem;
    width: 100%;
    max-width: 500px;
    max-height: 90vh;           /* ✅ stops modal from overflowing screen */
    overflow-y: auto;           /* ✅ scroll only inside */
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.25);
    animation: slideUp 0.35s ease-out;
    position: relative;         /* keep relative for close button */
}

/* Close button */
.modal-close {
    position: absolute;
    top: 0.75rem;
    right: 0.75rem;
    color: #6b7280;
    cursor: pointer;
    transition: color 0.2s ease;
}
.modal-close:hover {
    color: #374151;
}

/* Animations */
@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}
@keyframes slideUp {
    from { transform: translateY(30px); opacity: 0; }
    to { transform: translateY(0); opacity: 1; }
}



</style>



</x-app-layout>
</body>
</html>
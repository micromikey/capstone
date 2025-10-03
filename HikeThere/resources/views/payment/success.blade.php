<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Payment Successful') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6 sm:p-8 text-center">
                    <!-- Success Icon -->
                    <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-green-100 mb-6">
                        <svg class="h-10 w-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>

                    <!-- Success Message -->
                    <h3 class="text-2xl font-bold text-gray-900 mb-2">Payment Successful!</h3>
                    <p class="text-gray-600 mb-8">
                        Your payment has been processed successfully. You will receive a confirmation email shortly.
                    </p>

                    @if($payment || $booking)
                        <!-- Booking Details -->
                        <div class="bg-gray-50 rounded-lg p-6 mb-8 text-left">
                            <h4 class="text-lg font-semibold text-gray-900 mb-4">Booking Details</h4>
                            <dl class="space-y-3">
                                @if($payment)
                                    <div class="flex justify-between">
                                        <dt class="text-sm font-medium text-gray-500">Payment ID:</dt>
                                        <dd class="text-sm text-gray-900 font-semibold">#{{ $payment->id }}</dd>
                                    </div>
                                @endif
                                @if($payment && $payment->booking)
                                    <div class="flex justify-between">
                                        <dt class="text-sm font-medium text-gray-500">Booking ID:</dt>
                                        <dd class="text-sm text-gray-900 font-semibold">#{{ $payment->booking->id }}</dd>
                                    </div>
                                @endif
                                @if($payment)
                                    <div class="flex justify-between">
                                        <dt class="text-sm font-medium text-gray-500">Name:</dt>
                                        <dd class="text-sm text-gray-900">{{ $payment->fullname }}</dd>
                                    </div>
                                    <div class="flex justify-between">
                                        <dt class="text-sm font-medium text-gray-500">Email:</dt>
                                        <dd class="text-sm text-gray-900">{{ $payment->email }}</dd>
                                    </div>
                                    <div class="flex justify-between">
                                        <dt class="text-sm font-medium text-gray-500">Mountain/Trail:</dt>
                                        <dd class="text-sm text-gray-900">{{ $payment->mountain }}</dd>
                                    </div>
                                    <div class="flex justify-between">
                                        <dt class="text-sm font-medium text-gray-500">Hike Date:</dt>
                                        <dd class="text-sm text-gray-900">{{ $payment->hike_date->format('F d, Y') }}</dd>
                                    </div>
                                    <div class="flex justify-between">
                                        <dt class="text-sm font-medium text-gray-500">Participants:</dt>
                                        <dd class="text-sm text-gray-900">{{ $payment->participants }}</dd>
                                    </div>
                                    <div class="flex justify-between pt-3 border-t border-gray-200">
                                        <dt class="text-base font-semibold text-gray-900">Total Paid:</dt>
                                        <dd class="text-base font-bold text-green-600">₱{{ number_format($payment->amount, 2) }}</dd>
                                    </div>
                                    <div class="flex justify-between">
                                        <dt class="text-sm font-medium text-gray-500">Status:</dt>
                                        <dd class="text-sm">
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                                {{ $payment->isPaid() ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                                {{ ucfirst($payment->payment_status) }}
                                            </span>
                                        </dd>
                                    </div>
                                @endif
                            </dl>
                        </div>
                    @endif

                    <!-- Next Steps -->
                    <div class="bg-blue-50 rounded-lg p-6 mb-8 text-left">
                        <h4 class="text-lg font-semibold text-blue-900 mb-3">What's Next?</h4>
                        <ul class="space-y-2 text-sm text-blue-800">
                            <li class="flex items-start">
                                <svg class="h-5 w-5 text-blue-600 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span>Check your email for payment confirmation and booking details</span>
                            </li>
                            <li class="flex items-start">
                                <svg class="h-5 w-5 text-blue-600 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span>The organizing group will contact you with further instructions</span>
                            </li>
                            <li class="flex items-start">
                                <svg class="h-5 w-5 text-blue-600 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span>Prepare your hiking gear and check the weather before your trip</span>
                            </li>
                        </ul>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex flex-col sm:flex-row gap-3 justify-center">
                        <a href="{{ route('dashboard') }}"
                           class="inline-flex justify-center items-center rounded-md border border-gray-300 bg-white px-6 py-3 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                            <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                            </svg>
                            Back to Dashboard
                        </a>
                        <a href="{{ route('explore') }}"
                           class="inline-flex justify-center items-center rounded-md border border-transparent bg-indigo-600 px-6 py-3 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                            <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                            Explore More Trails
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

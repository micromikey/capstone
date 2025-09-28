<x-app-layout>
    <div class="bg-gradient-to-br from-slate-50 via-blue-50 to-emerald-50 min-h-screen py-10">
        <div class="max-w-6xl mx-auto px-4 md:px-8">
            <div class="text-center mb-8">
                <h1 class="text-3xl md:text-4xl font-bold text-gray-800 mb-2">Booking Details</h1>
                <p class="text-gray-600 max-w-2xl mx-auto">Review the details of your reservation below.</p>
            </div>

            <div class="bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-100 p-6 max-w-3xl mx-auto">
                <div class="mb-4">
                    <div class="text-lg font-semibold text-gray-800">{{ $booking->trail?->trail_name ?? 'N/A' }}</div>
                    <div class="text-sm text-gray-500">Booking #{{ $booking->id }} · Status: <span class="font-medium">{{ $booking->status }}</span></div>
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

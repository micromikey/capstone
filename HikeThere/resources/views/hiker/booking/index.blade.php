<x-app-layout>
    <div class="bg-gradient-to-br from-slate-50 via-blue-50 to-emerald-50 min-h-screen py-10">
        <div class="max-w-6xl mx-auto px-4 md:px-8">
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h1 class="text-3xl md:text-4xl font-bold text-gray-800 mb-1">My Bookings</h1>
                    <p class="text-gray-600">Manage your hiking reservations — campsites, guided hikes, and packages you've booked.</p>
                </div>
                <div class="flex items-center gap-3">
                    <div class="text-sm text-gray-600">You have <strong class="text-gray-800">{{ $bookings->count() }}</strong></div>
                    <a href="{{ route('booking.create') }}" class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-emerald-500 to-emerald-600 text-white rounded-lg shadow hover:from-emerald-600 hover:to-emerald-700">Create Booking</a>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-6">
                @if($bookings->isEmpty())
                    <div class="bg-white rounded-2xl shadow-lg overflow-hidden border border-dashed border-gray-200 p-10 text-center">
                        <img src="{{ asset('img/empty-booking.svg') }}" alt="No bookings" class="mx-auto h-32 mb-6" />
                        <div class="text-gray-700">No bookings yet.</div>
                        <a href="{{ route('booking.create') }}" class="mt-4 inline-block px-5 py-2 bg-emerald-600 text-white rounded-lg">Create your first booking</a>
                    </div>
                @else
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($bookings as $booking)
                            <div class="bg-white rounded-2xl shadow-md p-4 border border-gray-100 hover:shadow-lg transition">
                                <div class="flex items-start gap-3">
                                    <div class="w-16 h-16 rounded-lg bg-gray-100 overflow-hidden flex-shrink-0">
                                        <img src="{{ $booking->trail?->image_url ?? asset('img/default-trail.jpg') }}" alt="{{ $booking->trail?->trail_name ?? 'Trail' }}" class="w-full h-full object-cover">
                                    </div>
                                    <div class="flex-1">
                                        <div class="flex items-center justify-between">
                                            <h3 class="text-lg font-semibold text-gray-800">{{ $booking->trail?->trail_name ?? 'Trail' }}</h3>
                                            <span class="text-xs font-medium px-2 py-1 rounded-full {{ $booking->status === 'cancelled' ? 'bg-red-100 text-red-700' : 'bg-emerald-100 text-emerald-800' }}">{{ ucfirst($booking->status ?? 'booked') }}</span>
                                        </div>
                                            <div class="text-sm text-gray-500 mt-1">
                                                <div>Date: <strong class="text-gray-700">{{ $booking->date ? \Carbon\Carbon::parse($booking->date)->format('M j, Y') : 'TBD' }}</strong></div>
                                                <div>Party: <strong class="text-gray-700">{{ $booking->party_size }}</strong></div>
                                            </div>
                                            <p class="text-sm text-gray-600 mt-2 truncate">{{ \Illuminate\Support\Str::limit($booking->notes ?? '', 120) }}</p>
                                    </div>
                                </div>

                                <div class="mt-4 flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <a href="{{ route('booking.show', $booking) }}" class="text-sm text-emerald-600 font-medium">View</a>
                                        <a href="{{ route('booking.edit', $booking) }}" class="text-sm text-gray-600">Edit</a>
                                    </div>
                                    <div class="text-sm text-gray-500">{{ $booking->created_at->diffForHumans() }}</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>

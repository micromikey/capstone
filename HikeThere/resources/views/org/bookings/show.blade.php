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
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
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
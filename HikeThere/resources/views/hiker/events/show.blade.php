<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $event->title }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white shadow rounded-lg p-6">
                <div class="flex items-start gap-6">
                    <div class="flex-1">
                        <h1 class="text-2xl font-bold text-gray-900">{{ $event->title }}</h1>
                        <p class="text-sm text-gray-600 mt-1">by {{ optional($event->user)->display_name ?? 'Organization' }}</p>
                        <p class="text-sm text-gray-500 mt-2">@if(!empty($event->always_available)) Always Open @else {{ $event->start_at ? $event->start_at->format('M d, Y h:i A') : 'Date/Time: TBA' }} @endif</p>

                        @if($event->trail)
                            <p class="mt-3 text-sm text-gray-700">Trail: <a href="{{ route('trails.show', $event->trail->slug) }}" class="text-emerald-600 hover:underline">{{ $event->trail->trail_name }}</a></p>
                        @endif

                        @if($event->description)
                            <div class="mt-4 text-sm text-gray-700 leading-relaxed">{!! nl2br(e($event->description)) !!}</div>
                        @endif

                        <div class="mt-6">
                            @if($event->capacity)
                                <span class="inline-block px-3 py-1 text-xs bg-gray-100 rounded">Capacity: {{ $event->capacity }}</span>
                            @endif
                            @if($event->always_available)
                                <span class="inline-block px-3 py-1 text-xs bg-green-100 rounded ml-2">Always Available</span>
                            @endif
                        </div>

                        <div class="mt-6">
                            <a href="{{ route('community.index') }}#events" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-emerald-700 bg-emerald-100 hover:bg-emerald-200">Back</a>
                            @php
                                $bookingUrl = route('booking.create');
                                // If event has a trail and user/organization info, prefill booking form
                                if(isset($event->trail) && $event->trail) {
                                    $params = http_build_query([
                                        'organization_id' => $event->user?->id,
                                        'trail_id' => $event->trail->id,
                                        'date' => $event->start_at ? $event->start_at->toDateString() : null,
                                        'event_id' => $event->id,
                                    ]);
                                    $bookingUrl .= '?' . $params;
                                }
                            @endphp
                            <a href="{{ $bookingUrl }}" class="ml-2 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-emerald-600 hover:bg-emerald-700">Book Now</a>
                        </div>
                    </div>
                    <div class="w-56">
                        <div class="bg-gray-50 p-4 rounded-md">
                            <h4 class="text-sm font-semibold text-gray-800">Event Details</h4>
                            <dl class="mt-2 text-sm text-gray-600">
                                <dt class="font-medium">Starts</dt>
                                <dd class="mb-2">@if(!empty($event->always_available)) Always Open @else {{ $event->start_at ? $event->start_at->format('M d, Y h:i A') : 'TBA' }} @endif</dd>
                                <dt class="font-medium">Ends</dt>
                                <dd class="mb-2">@if(!empty($event->always_available)) Always Open @else {{ $event->end_at ? $event->end_at->format('M d, Y h:i A') : 'TBA' }} @endif</dd>
                                <dt class="font-medium">Batches</dt>
                                <dd class="mb-2">@if(!empty($event->always_available)) Undated (Always Open) @else {{ $event->batch_count ?? '-' }} @endif</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

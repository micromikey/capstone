<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Event Details</h2>
            <a href="{{ route('community.index') }}#events" class="text-sm text-emerald-600 hover:text-emerald-700 font-medium">
                ← Back to Events
            </a>
        </div>
    </x-slot>

    <div class="py-8 sm:py-12">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Hero Section with Gradient Background -->
            <div class="bg-gradient-to-br from-emerald-500 to-teal-600 rounded-2xl shadow-xl overflow-hidden mb-8">
                <div class="p-8 sm:p-12">
                    <div class="flex items-center gap-3 mb-4">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        @if($event->always_available)
                            <span class="inline-flex items-center px-4 py-1.5 text-sm font-semibold bg-white/20 backdrop-blur-sm text-white rounded-full border border-white/30">
                                🌟 Always Available
                            </span>
                        @endif
                    </div>
                    <h1 class="text-3xl sm:text-4xl lg:text-5xl font-bold text-white mb-3 leading-tight">
                        {{ $event->title }}
                    </h1>
                    <div class="flex flex-wrap items-center gap-4 text-white/90">
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            <span class="font-medium">{{ optional($event->user)->display_name ?? 'Organization' }}</span>
                        </div>
                        @if(!empty($event->always_available))
                            <div class="flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span>Open Anytime</span>
                            </div>
                        @else
                            @if($event->hiking_start_time)
                                <div class="flex items-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <span>Hike starts at {{ \Carbon\Carbon::parse($event->hiking_start_time)->format('g:i A') }}</span>
                                </div>
                            @else
                                <div class="flex items-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <span>{{ $event->start_at ? $event->start_at->format('M d, Y h:i A') : 'Date/Time: TBA' }}</span>
                                </div>
                            @endif
                        @endif
                    </div>
                </div>
            </div>

            <!-- Main Content Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Main Content -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Trail Information Card -->
                    @if($event->trail)
                        <div class="bg-white shadow-lg rounded-xl p-6 border border-gray-100 hover:shadow-xl transition-shadow duration-300">
                            <div class="flex items-start gap-4">
                                <div class="flex-shrink-0 w-12 h-12 bg-emerald-100 rounded-lg flex items-center justify-center">
                                    <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path>
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-1">Featured Trail</h3>
                                    <a href="{{ route('trails.show', $event->trail->slug) }}" 
                                       class="text-xl font-bold text-emerald-600 hover:text-emerald-700 hover:underline inline-flex items-center gap-2 group">
                                        {{ $event->trail->trail_name }}
                                        <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                                        </svg>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Description Card -->
                    @if($event->description)
                        <div class="bg-white shadow-lg rounded-xl p-8 border border-gray-100">
                            <h3 class="text-2xl font-bold text-gray-900 mb-4 flex items-center gap-2">
                                <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"></path>
                                </svg>
                                About This Event
                            </h3>
                            <div class="prose prose-emerald max-w-none">
                                <div class="text-gray-700 leading-relaxed whitespace-pre-line">
                                    {!! nl2br(e($event->description)) !!}
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Event Stats -->
                    <div class="bg-white shadow-lg rounded-xl p-6 border border-gray-100">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Event Information</h3>
                        <div class="flex flex-wrap gap-3">
                            @if($event->capacity)
                                <div class="flex items-center gap-2 px-4 py-2.5 bg-blue-50 border border-blue-200 rounded-lg">
                                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                    </svg>
                                    <span class="font-semibold text-blue-900">Capacity: {{ $event->capacity }}</span>
                                </div>
                            @endif
                            @if($event->always_available)
                                <div class="flex items-center gap-2 px-4 py-2.5 bg-green-50 border border-green-200 rounded-lg">
                                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <span class="font-semibold text-green-900">Always Available</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex flex-wrap gap-4">
                        <a href="{{ route('community.index') }}#events" 
                           class="inline-flex items-center gap-2 px-6 py-3 border-2 border-gray-300 text-base font-semibold rounded-lg text-gray-700 bg-white hover:bg-gray-50 hover:border-gray-400 transition-all duration-200 shadow-sm">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                            Back to Events
                        </a>
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
                        <a href="{{ $bookingUrl }}" 
                           class="inline-flex items-center gap-2 px-8 py-3 border border-transparent text-base font-semibold rounded-lg text-white bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Book This Event
                        </a>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="lg:col-span-1">
                    <div class="sticky top-8 space-y-6">
                        <!-- Event Details Card -->
                        <div class="bg-gradient-to-br from-gray-50 to-gray-100 shadow-xl rounded-xl overflow-hidden border border-gray-200">
                            <div class="bg-gradient-to-r from-emerald-600 to-teal-600 px-6 py-4">
                                <h4 class="text-lg font-bold text-white flex items-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    Event Details
                                </h4>
                            </div>
                            <div class="p-6">
                                <dl class="space-y-5">
                                    <div class="flex items-start gap-3">
                                        <div class="flex-shrink-0 w-10 h-10 bg-emerald-100 rounded-lg flex items-center justify-center">
                                            <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                        </div>
                                        <div class="flex-1">
                                            <dt class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Start Date</dt>
                                            <dd class="text-sm font-bold text-gray-900">
                                                @if(!empty($event->always_available))
                                                    <span class="text-emerald-600">Always Open</span>
                                                @else
                                                    {{ $event->start_at ? $event->start_at->format('M d, Y') : 'TBA' }}
                                                    @if($event->start_at)
                                                        <span class="block text-xs text-gray-600 font-normal mt-0.5">{{ $event->start_at->format('h:i A') }}</span>
                                                    @endif
                                                @endif
                                            </dd>
                                        </div>
                                    </div>
                                    
                                    <div class="border-t border-gray-200 pt-5"></div>
                                    
                                    @if($event->hiking_start_time)
                                    <div class="flex items-start gap-3">
                                        <div class="flex-shrink-0 w-10 h-10 bg-emerald-100 rounded-lg flex items-center justify-center">
                                            <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                        </div>
                                        <div class="flex-1">
                                            <dt class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Hiking Start Time</dt>
                                            <dd class="text-sm font-bold text-emerald-700">
                                                {{ \Carbon\Carbon::parse($event->hiking_start_time)->format('g:i A') }}
                                            </dd>
                                        </div>
                                    </div>
                                    
                                    <div class="border-t border-gray-200 pt-5"></div>
                                    @endif
                                    
                                    <div class="flex items-start gap-3">
                                        <div class="flex-shrink-0 w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                        </div>
                                        <div class="flex-1">
                                            <dt class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">End Date</dt>
                                            <dd class="text-sm font-bold text-gray-900">
                                                @if(!empty($event->always_available))
                                                    <span class="text-emerald-600">Always Open</span>
                                                @else
                                                    {{ $event->end_at ? $event->end_at->format('M d, Y') : 'TBA' }}
                                                    @if($event->end_at)
                                                        <span class="block text-xs text-gray-600 font-normal mt-0.5">{{ $event->end_at->format('h:i A') }}</span>
                                                    @endif
                                                @endif
                                            </dd>
                                        </div>
                                    </div>
                                    
                                    <div class="border-t border-gray-200 pt-5"></div>
                                    
                                    <div class="flex items-start gap-3">
                                        <div class="flex-shrink-0 w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                                            <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                            </svg>
                                        </div>
                                        <div class="flex-1">
                                            <dt class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Batches</dt>
                                            <dd class="text-sm font-bold text-gray-900">
                                                @if(!empty($event->always_available))
                                                    <span class="text-gray-600">Undated</span>
                                                @else
                                                    {{ $event->batch_count ?? '-' }}
                                                @endif
                                            </dd>
                                        </div>
                                    </div>
                                </dl>
                            </div>
                        </div>

                        <!-- Quick Actions Card -->
                        <div class="bg-white shadow-lg rounded-xl p-6 border border-gray-100">
                            <h4 class="text-sm font-bold text-gray-900 mb-4 flex items-center gap-2">
                                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                </svg>
                                Quick Actions
                            </h4>
                            <div class="space-y-3">
                                <a href="{{ $bookingUrl }}" 
                                   class="w-full inline-flex items-center justify-center gap-2 px-4 py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-lg transition-colors duration-200 shadow-md hover:shadow-lg">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                    </svg>
                                    Book Now
                                </a>
                                <a href="{{ route('community.index') }}#events" 
                                   class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-lg transition-colors duration-200">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                                    </svg>
                                    Browse Events
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

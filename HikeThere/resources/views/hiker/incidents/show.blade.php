<x-app-layout>
    <div class="bg-gradient-to-br from-slate-50 via-blue-50 to-emerald-50 min-h-screen py-10">
        <div class="max-w-4xl mx-auto px-4 md:px-8">
            <!-- Back Button -->
            <div class="mb-6">
                <a href="{{ route('hiker.incidents.index') }}" class="inline-flex items-center text-red-600 hover:text-red-700 font-medium transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                    Back to My Incidents
                </a>
            </div>

            <!-- Main Card -->
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-100">
                <!-- Header -->
                <div class="bg-gradient-to-r from-red-600 to-red-700 p-6 text-white">
                    <div class="flex items-start justify-between">
                        <div>
                            <h1 class="text-3xl font-bold mb-2">Safety Incident Report</h1>
                            <p class="text-red-100">Reported on {{ $incident->created_at->format('F d, Y \a\t g:i A') }}</p>
                        </div>
                        
                        <!-- Status Badge -->
                        @php
                            $statusColors = [
                                'reported' => 'bg-blue-500',
                                'open' => 'bg-yellow-500',
                                'in progress' => 'bg-orange-500',
                                'resolved' => 'bg-green-500',
                                'closed' => 'bg-gray-500',
                            ];
                            $statusColor = $statusColors[strtolower($incident->status)] ?? 'bg-gray-500';
                        @endphp
                        <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium {{ $statusColor }} text-white">
                            {{ ucfirst($incident->status) }}
                        </span>
                    </div>
                </div>

                <!-- Content -->
                <div class="p-6 space-y-6">
                    <!-- Trail Information -->
                    <div class="bg-emerald-50 border-l-4 border-emerald-500 p-4 rounded-lg">
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Trail Information</h3>
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-900 font-medium">{{ $incident->trail->name }}</p>
                                <p class="text-sm text-gray-600">{{ $incident->trail->location }}</p>
                            </div>
                            <a href="{{ route('trails.show', $incident->trail->slug) }}" class="text-emerald-600 hover:text-emerald-700 text-sm font-medium">
                                View Trail →
                            </a>
                        </div>
                    </div>

                    <!-- Incident Details Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Incident Type -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Incident Type</label>
                            <div class="flex items-center">
                                <span class="inline-flex items-center px-3 py-2 rounded-lg bg-gray-100 text-gray-800 font-medium">
                                    {{ ucfirst($incident->incident_type ?? 'General') }}
                                </span>
                            </div>
                        </div>

                        <!-- Severity -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Severity Level</label>
                            @php
                                $severityColors = [
                                    'critical' => 'bg-red-600 text-white',
                                    'high' => 'bg-orange-500 text-white',
                                    'medium' => 'bg-yellow-500 text-white',
                                    'low' => 'bg-green-500 text-white',
                                ];
                                $severityColor = $severityColors[strtolower($incident->severity)] ?? 'bg-gray-500 text-white';
                            @endphp
                            <div class="flex items-center">
                                <span class="inline-flex items-center px-3 py-2 rounded-lg {{ $severityColor }} font-medium">
                                    {{ ucfirst($incident->severity) }}
                                </span>
                            </div>
                        </div>

                        <!-- Incident Date -->
                        @if($incident->incident_date)
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Date of Incident</label>
                            <div class="flex items-center text-gray-900">
                                <svg class="w-5 h-5 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                {{ \Carbon\Carbon::parse($incident->incident_date)->format('F d, Y') }}
                                @if($incident->incident_time)
                                    at {{ \Carbon\Carbon::parse($incident->incident_time)->format('g:i A') }}
                                @endif
                            </div>
                        </div>
                        @endif

                        <!-- Location -->
                        @if($incident->location)
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Location</label>
                            <div class="flex items-center text-gray-900">
                                <svg class="w-5 h-5 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                {{ $incident->location }}
                            </div>
                        </div>
                        @endif
                    </div>

                    <!-- Description -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Incident Description</label>
                        <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                            <p class="text-gray-900 whitespace-pre-line">{{ $incident->description }}</p>
                        </div>
                    </div>

                    <!-- Organization Response -->
                    @if($incident->resolution_notes)
                    <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded-lg">
                        <h3 class="text-lg font-semibold text-gray-900 mb-2 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
                            </svg>
                            Organization Response
                        </h3>
                        <p class="text-gray-700 whitespace-pre-line">{{ $incident->resolution_notes }}</p>
                        @if($incident->resolved_at)
                            <p class="text-sm text-gray-600 mt-2">
                                Responded on {{ $incident->resolved_at->format('F d, Y \a\t g:i A') }}
                            </p>
                        @endif
                    </div>
                    @else
                    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded-lg">
                        <div class="flex">
                            <svg class="w-5 h-5 text-yellow-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                            </svg>
                            <div>
                                <p class="text-sm text-yellow-800 font-medium">Pending Organization Response</p>
                                <p class="text-sm text-yellow-700 mt-1">The organization has been notified and will review your report.</p>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Meta Information -->
                    <div class="border-t pt-6">
                        <h3 class="text-sm font-medium text-gray-700 mb-3">Additional Information</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                            <div class="flex items-center text-gray-600">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                                <strong>Organization:</strong>&nbsp;{{ $incident->organization->display_name }}
                            </div>
                            <div class="flex items-center text-gray-600">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"></path>
                                </svg>
                                <strong>Report ID:</strong>&nbsp;#{{ str_pad($incident->id, 6, '0', STR_PAD_LEFT) }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action to Report Another -->
            <div class="mt-6 text-center">
                <a href="{{ route('trails.index') }}" class="inline-flex items-center px-6 py-3 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition-colors font-medium shadow-lg hover:shadow-xl">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                    </svg>
                    Browse Trails
                </a>
            </div>
        </div>
    </div>
</x-app-layout>

<x-app-layout>
    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900">My Reported Safety Issues</h1>
                <p class="mt-2 text-gray-600">Track the status of safety incidents you've reported</p>
            </div>

            @if($incidents->isEmpty())
                <!-- Empty State -->
                <div class="bg-white rounded-lg shadow-sm p-12 text-center">
                    <svg class="w-24 h-24 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-1.732-1.333-2.464 0L4.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No Incidents Reported</h3>
                    <p class="text-gray-500 mb-6">You haven't reported any safety incidents yet.</p>
                    <a href="{{ route('trails.index') }}" class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                        </svg>
                        Browse Trails
                    </a>
                </div>
            @else
                <!-- Incidents List -->
                <div class="space-y-4">
                    @foreach($incidents as $incident)
                        <div class="bg-white rounded-lg shadow-sm hover:shadow-md transition-shadow">
                            <a href="{{ route('hiker.incidents.show', $incident) }}" class="block p-6">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <!-- Trail and Type -->
                                        <div class="flex items-center gap-3 mb-3">
                                            <h3 class="text-lg font-semibold text-gray-900">
                                                {{ $incident->trail->name }}
                                            </h3>
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                {{ ucfirst($incident->incident_type ?? 'General') }}
                                            </span>
                                        </div>

                                        <!-- Location -->
                                        @if($incident->location)
                                            <div class="flex items-center text-sm text-gray-600 mb-2">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                </svg>
                                                {{ $incident->location }}
                                            </div>
                                        @endif

                                        <!-- Description Preview -->
                                        <p class="text-gray-700 mb-3 line-clamp-2">
                                            {{ Str::limit($incident->description, 200) }}
                                        </p>

                                        <!-- Date -->
                                        <div class="flex items-center text-sm text-gray-500">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                            Reported on {{ $incident->created_at->format('M d, Y') }}
                                            @if($incident->incident_date)
                                                · Occurred on {{ \Carbon\Carbon::parse($incident->incident_date)->format('M d, Y') }}
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Severity & Status Badges -->
                                    <div class="ml-6 flex flex-col items-end gap-2">
                                        <!-- Severity Badge -->
                                        @php
                                            $severityColors = [
                                                'critical' => 'bg-red-600 text-white',
                                                'high' => 'bg-orange-500 text-white',
                                                'medium' => 'bg-yellow-500 text-white',
                                                'low' => 'bg-green-500 text-white',
                                            ];
                                            $severityColor = $severityColors[strtolower($incident->severity)] ?? 'bg-gray-500 text-white';
                                        @endphp
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ $severityColor }}">
                                            {{ ucfirst($incident->severity) }}
                                        </span>

                                        <!-- Status Badge -->
                                        @php
                                            $statusColors = [
                                                'reported' => 'bg-blue-100 text-blue-800',
                                                'open' => 'bg-red-100 text-red-800',
                                                'in progress' => 'bg-yellow-100 text-yellow-800',
                                                'resolved' => 'bg-green-100 text-green-800',
                                                'closed' => 'bg-gray-100 text-gray-800',
                                            ];
                                            $statusColor = $statusColors[strtolower($incident->status)] ?? 'bg-gray-100 text-gray-800';
                                        @endphp
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ $statusColor }}">
                                            {{ ucfirst($incident->status) }}
                                        </span>
                                    </div>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="mt-6">
                    {{ $incidents->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>

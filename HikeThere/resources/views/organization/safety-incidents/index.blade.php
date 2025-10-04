{{-- resources/views/organization/safety-incidents/index.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <div class="space-y-4">
            <x-trail-breadcrumb />
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div>
                    <h2 class="font-semibold text-2xl text-gray-800 leading-tight">
                        {{ __('Safety Incident Reports') }}
                    </h2>
                    <p class="text-sm text-gray-600 mt-1">View safety incidents reported by hikers on your trails</p>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-12 bg-gradient-to-br from-emerald-50 via-white to-teal-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            <!-- Floating Statistics Dashboard (Right) -->
            <div id="floating-dashboard" class="fixed top-56 right-10 z-40 transition-all duration-300 transform hidden xl:block">
                <div class="bg-white/95 backdrop-blur-md rounded-xl shadow-lg border border-gray-200/50 p-4 w-72 max-h-[calc(100vh-14rem)] overflow-y-auto">
                    <div class="flex items-center mb-4">
                        <svg class="w-5 h-5 mr-2 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-1.732-1.333-2.464 0L4.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                        <h3 class="text-sm font-semibold text-gray-800">Incident Statistics</h3>
                    </div>

                    <div class="space-y-3">
                        <!-- Total Incidents -->
                        <div class="bg-gradient-to-br from-white to-gray-50 rounded-lg border border-gray-100 p-3 hover:shadow-md transition-shadow">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 bg-gray-100 rounded-lg p-2">
                                    <svg class="h-5 w-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-xs font-medium text-gray-600">Total Reports</p>
                                    <p class="text-xl font-bold text-gray-900">{{ $stats['total_incidents'] }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Open Incidents -->
                        <div class="bg-gradient-to-br from-white to-red-50 rounded-lg border border-red-100 p-3 hover:shadow-md transition-shadow">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 bg-red-100 rounded-lg p-2">
                                    <svg class="h-5 w-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-xs font-medium text-gray-600">Open Incidents</p>
                                    <p class="text-xl font-bold text-red-600">{{ $stats['open_incidents'] }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Critical Incidents -->
                        <div class="bg-gradient-to-br from-white to-orange-50 rounded-lg border border-orange-100 p-3 hover:shadow-md transition-shadow">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 bg-orange-100 rounded-lg p-2">
                                    <svg class="h-5 w-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-1.732-1.333-2.464 0L4.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-xs font-medium text-gray-600">Critical</p>
                                    <p class="text-xl font-bold text-orange-600">{{ $stats['critical_incidents'] }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Resolved This Month -->
                        <div class="bg-gradient-to-br from-white to-green-50 rounded-lg border border-green-100 p-3 hover:shadow-md transition-shadow">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 bg-green-100 rounded-lg p-2">
                                    <svg class="h-5 w-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-xs font-medium text-gray-600">Resolved This Month</p>
                                    <p class="text-xl font-bold text-green-600">{{ $stats['resolved_this_month'] }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Incidents List -->
            <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                <div class="px-6 py-4 bg-gradient-to-r from-red-600 to-orange-600 text-white">
                    <h3 class="text-xl font-bold">Hiker-Reported Safety Incidents</h3>
                    <p class="text-sm opacity-90">Safety incidents reported by hikers who visited your trails</p>
                </div>

                <div class="p-6">
                    @if($incidents->count() > 0)
                        <div class="space-y-4">
                            @foreach($incidents as $incident)
                                <div class="border rounded-lg p-4 hover:shadow-md transition">
                                    <div class="flex justify-between items-start">
                                        <div class="flex-1">
                                            <div class="flex items-center space-x-3 mb-2">
                                                <h4 class="text-lg font-semibold text-gray-900">{{ $incident->trail->trail_name }}</h4>
                                                
                                                <!-- Incident Type Badge (if available) -->
                                                @if($incident->incident_type)
                                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                                        {{ ucfirst($incident->incident_type) }}
                                                    </span>
                                                @endif
                                                
                                                <!-- Severity Badge -->
                                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $incident->severity_badge_color }}">
                                                    {{ ucfirst($incident->severity) }}
                                                </span>
                                                
                                                <!-- Status Badge -->
                                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $incident->status_badge_color }}">
                                                    {{ ucfirst($incident->status) }}
                                                </span>
                                            </div>
                                            
                                            <!-- Reporter & Location Info -->
                                            <div class="flex items-center space-x-4 text-sm text-gray-600 mb-2">
                                                @if($incident->reported_by && $incident->reporter)
                                                    <span class="flex items-center">
                                                        <svg class="w-4 h-4 mr-1 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                                        </svg>
                                                        Reported by: <strong class="ml-1">{{ $incident->reporter->display_name }}</strong>
                                                    </span>
                                                @endif
                                                @if($incident->location)
                                                    <span>•</span>
                                                    <span class="flex items-center">
                                                        <svg class="w-4 h-4 mr-1 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                        </svg>
                                                        {{ $incident->location }}
                                                    </span>
                                                @endif
                                            </div>
                                            
                                            <!-- Description -->
                                            <p class="text-sm text-gray-600 mb-2">{{ \Illuminate\Support\Str::limit($incident->description, 200) }}</p>
                                            
                                            <!-- Date Information -->
                                            <div class="flex items-center space-x-4 text-xs text-gray-500">
                                                @if($incident->incident_date)
                                                    <span>Occurred: {{ \Carbon\Carbon::parse($incident->incident_date)->format('M d, Y') }}
                                                        @if($incident->incident_time)
                                                            at {{ \Carbon\Carbon::parse($incident->incident_time)->format('g:i A') }}
                                                        @endif
                                                    </span>
                                                @elseif($incident->occurred_at)
                                                    <span>Occurred: {{ $incident->occurred_at->format('M d, Y H:i') }}</span>
                                                @endif
                                                <span>•</span>
                                                <span>Reported: {{ $incident->created_at->format('M d, Y') }}</span>
                                                @if($incident->resolved_at)
                                                    <span>•</span>
                                                    <span>Resolved: {{ $incident->resolved_at->format('M d, Y') }}</span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="flex space-x-2">
                                            <a href="{{ route('organization.safety-incidents.show', $incident) }}" class="text-blue-600 hover:text-blue-900 text-sm font-medium">View Details</a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-4">
                            {{ $incidents->links() }}
                        </div>
                    @else
                        <div class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No incidents reported yet</h3>
                            <p class="mt-1 text-sm text-gray-500">When hikers report safety incidents on your trails, they will appear here.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

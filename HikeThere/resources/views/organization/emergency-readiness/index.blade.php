{{-- resources/views/organization/emergency-readiness/index.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <div class="space-y-4">
            <x-trail-breadcrumb />
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div>
                    <h2 class="font-semibold text-2xl text-gray-800 leading-tight">
                        {{ __('Emergency Readiness Feedback') }}
                    </h2>
                    <p class="text-sm text-gray-600 mt-1">View safety feedback from hikers who completed your trails</p>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-12 bg-gradient-to-br from-emerald-50 via-white to-teal-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            <!-- Floating Statistics Dashboard (Right) -->
            <div id="floating-dashboard" class="fixed top-56 right-10 z-40 transition-all duration-300 transform hidden xl:block">
                <div class="bg-white/95 backdrop-blur-md rounded-xl shadow-lg border border-gray-200/50 p-4 w-72 max-h-[calc(100vh-14rem)] overflow-y-auto">
                    <div class="flex items-center mb-4">
                        <svg class="w-5 h-5 mr-2 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                        </svg>
                        <h3 class="text-sm font-semibold text-gray-800">Readiness Statistics</h3>
                    </div>

                    <div class="space-y-3">
                        <!-- Total Assessments -->
                        <div class="bg-gradient-to-br from-white to-blue-50 rounded-lg border border-blue-100 p-3 hover:shadow-md transition-shadow">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 bg-blue-100 rounded-lg p-2">
                                    <svg class="h-5 w-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-xs font-medium text-gray-600">Total Feedback</p>
                                    <p class="text-xl font-bold text-gray-900">{{ $stats['total_assessments'] }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Average Score -->
                        <div class="bg-gradient-to-br from-white to-green-50 rounded-lg border border-green-100 p-3 hover:shadow-md transition-shadow">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 bg-green-100 rounded-lg p-2">
                                    <svg class="h-5 w-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-xs font-medium text-gray-600">Average Score</p>
                                    <p class="text-xl font-bold text-green-600">{{ $stats['avg_score'] }}%</p>
                                </div>
                            </div>
                        </div>

                        <!-- Excellent Count -->
                        <div class="bg-gradient-to-br from-white to-emerald-50 rounded-lg border border-emerald-100 p-3 hover:shadow-md transition-shadow">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 bg-emerald-100 rounded-lg p-2">
                                    <svg class="h-5 w-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-xs font-medium text-gray-600">Excellent</p>
                                    <p class="text-xl font-bold text-emerald-600">{{ $stats['excellent_count'] }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Needs Improvement -->
                        <div class="bg-gradient-to-br from-white to-red-50 rounded-lg border border-red-100 p-3 hover:shadow-md transition-shadow">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 bg-red-100 rounded-lg p-2">
                                    <svg class="h-5 w-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-1.732-1.333-2.464 0L4.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-xs font-medium text-gray-600">Needs Improvement</p>
                                    <p class="text-xl font-bold text-red-600">{{ $stats['needs_improvement'] }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Assessments List -->
            <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                <div class="px-6 py-4 bg-gradient-to-r from-emerald-600 to-teal-600 text-white">
                    <h3 class="text-xl font-bold">Readiness Assessments</h3>
                    <p class="text-sm opacity-90">Track and manage emergency preparedness for your trails</p>
                </div>

                <div class="p-6">
                    @if($assessments->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Trail</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Submitted By</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">First Aid</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Communication</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Equipment</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Staff Training</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Overall</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($assessments as $assessment)
                                        <tr class="hover:bg-gray-50 transition">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900">{{ $assessment->trail->trail_name }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900">{{ $assessment->submitter->first_name }} {{ $assessment->submitter->last_name }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    <div class="text-sm font-bold text-gray-900">{{ $assessment->first_aid_score }}</div>
                                                    <div class="ml-2 w-16 bg-gray-200 rounded-full h-2">
                                                        <div class="bg-red-500 h-2 rounded-full" style="width: {{ $assessment->first_aid_score }}%"></div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    <div class="text-sm font-bold text-gray-900">{{ $assessment->communication_score }}</div>
                                                    <div class="ml-2 w-16 bg-gray-200 rounded-full h-2">
                                                        <div class="bg-blue-500 h-2 rounded-full" style="width: {{ $assessment->communication_score }}%"></div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    <div class="text-sm font-bold text-gray-900">{{ $assessment->equipment_score }}</div>
                                                    <div class="ml-2 w-16 bg-gray-200 rounded-full h-2">
                                                        <div class="bg-purple-500 h-2 rounded-full" style="width: {{ $assessment->equipment_score }}%"></div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    <div class="text-sm font-bold text-gray-900">{{ $assessment->staff_training_score }}</div>
                                                    <div class="ml-2 w-16 bg-gray-200 rounded-full h-2">
                                                        <div class="bg-green-500 h-2 rounded-full" style="width: {{ $assessment->staff_training_score }}%"></div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-bold text-gray-900">{{ number_format($assessment->overall_score, 1) }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                    @if($assessment->readiness_level === 'Excellent') bg-green-100 text-green-800
                                                    @elseif($assessment->readiness_level === 'Good') bg-blue-100 text-blue-800
                                                    @elseif($assessment->readiness_level === 'Fair') bg-yellow-100 text-yellow-800
                                                    @elseif($assessment->readiness_level === 'Needs Improvement') bg-orange-100 text-orange-800
                                                    @else bg-red-100 text-red-800
                                                    @endif">
                                                    {{ $assessment->readiness_level }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $assessment->assessment_date->format('M d, Y') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                <a href="{{ route('organization.emergency-readiness.show', $assessment) }}" class="text-blue-600 hover:text-blue-900">View Details</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-4">
                            {{ $assessments->links() }}
                        </div>
                    @else
                        <div class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No feedback received yet</h3>
                            <p class="mt-1 text-sm text-gray-500">Once hikers complete your trails, they can submit emergency readiness feedback which will appear here.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

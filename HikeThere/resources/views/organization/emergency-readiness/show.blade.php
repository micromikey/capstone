@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Breadcrumb -->
    <nav class="mb-6 text-sm">
        <ol class="flex items-center space-x-2">
            <li><a href="{{ route('organization.dashboard') }}" class="text-emerald-600 hover:text-emerald-700">Dashboard</a></li>
            <li class="text-gray-400">/</li>
            <li><a href="{{ route('organization.emergency-readiness.index') }}" class="text-emerald-600 hover:text-emerald-700">Emergency Readiness Feedback</a></li>
            <li class="text-gray-400">/</li>
            <li class="text-gray-600">View Feedback</li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Emergency Readiness Feedback</h1>
            <p class="mt-2 text-gray-600">Detailed safety feedback from a hiker who completed your trail</p>
        </div>
        <a href="{{ route('organization.emergency-readiness.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Back to Feedback List
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <!-- Trail & Submitter Information -->
        <div class="border-b border-gray-200 bg-gray-50 px-6 py-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <h3 class="text-sm font-medium text-gray-500">Trail</h3>
                    <p class="mt-1 text-lg font-semibold text-gray-900">{{ $emergencyReadiness->trail->trail_name }}</p>
                </div>
                <div>
                    <h3 class="text-sm font-medium text-gray-500">Submitted By</h3>
                    <p class="mt-1 text-lg font-semibold text-gray-900">{{ $emergencyReadiness->submitter->first_name }} {{ $emergencyReadiness->submitter->last_name }}</p>
                </div>
                <div>
                    <h3 class="text-sm font-medium text-gray-500">Date Submitted</h3>
                    <p class="mt-1 text-lg font-semibold text-gray-900">{{ $emergencyReadiness->assessment_date->format('M d, Y') }}</p>
                </div>
            </div>
        </div>

        <!-- Overall Score -->
        <div class="px-6 py-8 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Overall Assessment</h2>
            <div class="flex items-center justify-between bg-gradient-to-r from-emerald-50 to-emerald-100 rounded-lg p-6">
                <div>
                    <p class="text-sm font-medium text-gray-600">Overall Readiness Score</p>
                    <p class="text-4xl font-bold text-emerald-600 mt-2">{{ number_format($emergencyReadiness->overall_score, 1) }}/100</p>
                </div>
                <div class="text-right">
                    <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-semibold
                        @if($emergencyReadiness->readiness_level === 'Excellent') bg-green-100 text-green-800
                        @elseif($emergencyReadiness->readiness_level === 'Good') bg-blue-100 text-blue-800
                        @elseif($emergencyReadiness->readiness_level === 'Fair') bg-yellow-100 text-yellow-800
                        @elseif($emergencyReadiness->readiness_level === 'Needs Improvement') bg-orange-100 text-orange-800
                        @else bg-red-100 text-red-800
                        @endif">
                        {{ $emergencyReadiness->readiness_level }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Detailed Scores -->
        <div class="px-6 py-8">
            <h2 class="text-xl font-semibold text-gray-900 mb-6">Detailed Safety Categories</h2>
            <div class="space-y-6">
                <!-- First Aid Score -->
                <div class="bg-gray-50 rounded-lg p-5">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-base font-semibold text-gray-900 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            First Aid Equipment
                        </h3>
                        <span class="text-2xl font-bold text-red-600">{{ $emergencyReadiness->first_aid_score }}/100</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-3">
                        <div class="bg-red-500 h-3 rounded-full" style="width: {{ $emergencyReadiness->first_aid_score }}%"></div>
                    </div>
                </div>

                <!-- Communication Score -->
                <div class="bg-gray-50 rounded-lg p-5">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-base font-semibold text-gray-900 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                            </svg>
                            Communication Systems
                        </h3>
                        <span class="text-2xl font-bold text-blue-600">{{ $emergencyReadiness->communication_score }}/100</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-3">
                        <div class="bg-blue-500 h-3 rounded-full" style="width: {{ $emergencyReadiness->communication_score }}%"></div>
                    </div>
                </div>

                <!-- Equipment Score -->
                <div class="bg-gray-50 rounded-lg p-5">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-base font-semibold text-gray-900 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                            Safety Equipment
                        </h3>
                        <span class="text-2xl font-bold text-purple-600">{{ $emergencyReadiness->equipment_score }}/100</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-3">
                        <div class="bg-purple-500 h-3 rounded-full" style="width: {{ $emergencyReadiness->equipment_score }}%"></div>
                    </div>
                </div>

                <!-- Staff Training Score -->
                <div class="bg-gray-50 rounded-lg p-5">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-base font-semibold text-gray-900 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                            </svg>
                            Staff Training
                        </h3>
                        <span class="text-2xl font-bold text-green-600">{{ $emergencyReadiness->staff_training_score }}/100</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-3">
                        <div class="bg-green-500 h-3 rounded-full" style="width: {{ $emergencyReadiness->staff_training_score }}%"></div>
                    </div>
                </div>

                <!-- Emergency Access Score -->
                <div class="bg-gray-50 rounded-lg p-5">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-base font-semibold text-gray-900 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            Emergency Access
                        </h3>
                        <span class="text-2xl font-bold text-orange-600">{{ $emergencyReadiness->emergency_access_score }}/100</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-3">
                        <div class="bg-orange-500 h-3 rounded-full" style="width: {{ $emergencyReadiness->emergency_access_score }}%"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Comments -->
        @if($emergencyReadiness->comments)
        <div class="px-6 py-8 border-t border-gray-200 bg-gray-50">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Additional Comments</h2>
            <div class="bg-white rounded-lg p-5 border border-gray-200">
                <p class="text-gray-700 leading-relaxed">{{ $emergencyReadiness->comments }}</p>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

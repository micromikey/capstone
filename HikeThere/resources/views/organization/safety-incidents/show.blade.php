@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Breadcrumb -->
    <nav class="mb-6 text-sm">
        <ol class="flex items-center space-x-2">
            <li><a href="{{ route('organization.dashboard') }}" class="text-emerald-600 hover:text-emerald-700">Dashboard</a></li>
            <li class="text-gray-400">/</li>
            <li><a href="{{ route('organization.safety-incidents.index') }}" class="text-emerald-600 hover:text-emerald-700">Safety Incidents</a></li>
            <li class="text-gray-400">/</li>
            <li class="text-gray-600">View Incident</li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Safety Incident Report</h1>
            <p class="mt-2 text-gray-600">Incident reported by a hiker on your trail</p>
        </div>
        <a href="{{ route('organization.safety-incidents.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Back to Incidents
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <!-- Status Banner -->
        <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r 
            @if($safetyIncident->severity === 'critical' || $safetyIncident->severity === 'Critical') from-red-50 to-red-100
            @elseif($safetyIncident->severity === 'high' || $safetyIncident->severity === 'High') from-orange-50 to-orange-100
            @elseif($safetyIncident->severity === 'medium' || $safetyIncident->severity === 'Medium') from-yellow-50 to-yellow-100
            @else from-blue-50 to-blue-100
            @endif">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <span class="px-4 py-2 rounded-full text-sm font-semibold
                        @if($safetyIncident->severity === 'critical' || $safetyIncident->severity === 'Critical') bg-red-500 text-white
                        @elseif($safetyIncident->severity === 'high' || $safetyIncident->severity === 'High') bg-orange-500 text-white
                        @elseif($safetyIncident->severity === 'medium' || $safetyIncident->severity === 'Medium') bg-yellow-500 text-white
                        @else bg-blue-500 text-white
                        @endif">
                        {{ ucfirst($safetyIncident->severity) }} Severity
                    </span>
                    <span class="px-4 py-2 rounded-full text-sm font-semibold
                        @if($safetyIncident->status === 'reported' || $safetyIncident->status === 'Open') bg-yellow-100 text-yellow-800
                        @elseif($safetyIncident->status === 'investigating' || $safetyIncident->status === 'In Progress') bg-blue-100 text-blue-800
                        @else bg-green-100 text-green-800
                        @endif">
                        {{ ucfirst($safetyIncident->status) }}
                    </span>
                    @if($safetyIncident->incident_type)
                        <span class="px-4 py-2 rounded-full text-sm font-semibold bg-purple-100 text-purple-800">
                            {{ ucfirst(str_replace('_', ' ', $safetyIncident->incident_type)) }}
                        </span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Trail & Reporter Information -->
        <div class="border-b border-gray-200 bg-gray-50 px-6 py-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <h3 class="text-sm font-medium text-gray-500">Trail</h3>
                    <p class="mt-1 text-lg font-semibold text-gray-900">{{ $safetyIncident->trail->trail_name }}</p>
                </div>
                <div>
                    <h3 class="text-sm font-medium text-gray-500">Reported By</h3>
                    <p class="mt-1 text-lg font-semibold text-gray-900">
                        @if($safetyIncident->reporter)
                            {{ $safetyIncident->reporter->first_name }} {{ $safetyIncident->reporter->last_name }}
                        @else
                            Anonymous
                        @endif
                    </p>
                </div>
                <div>
                    <h3 class="text-sm font-medium text-gray-500">Date Reported</h3>
                    <p class="mt-1 text-lg font-semibold text-gray-900">{{ $safetyIncident->created_at->format('M d, Y') }}</p>
                </div>
            </div>
        </div>

        <!-- Incident Details -->
        <div class="px-6 py-8">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Incident Information</h2>
            
            <!-- Incident Date & Time -->
            <div class="mb-6">
                <h3 class="text-sm font-medium text-gray-700 mb-2">When did this incident occur?</h3>
                <div class="flex items-center space-x-4">
                    @if($safetyIncident->incident_date)
                        <div class="flex items-center text-gray-900">
                            <svg class="w-5 h-5 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            {{ \Carbon\Carbon::parse($safetyIncident->incident_date)->format('F d, Y') }}
                        </div>
                    @elseif($safetyIncident->occurred_at)
                        <div class="flex items-center text-gray-900">
                            <svg class="w-5 h-5 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            {{ $safetyIncident->occurred_at->format('F d, Y') }}
                        </div>
                    @endif
                    
                    @if($safetyIncident->incident_time)
                        <div class="flex items-center text-gray-900">
                            <svg class="w-5 h-5 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            {{ \Carbon\Carbon::parse($safetyIncident->incident_time)->format('g:i A') }}
                        </div>
                    @endif
                </div>
            </div>

            <!-- Location -->
            @if($safetyIncident->location)
            <div class="mb-6">
                <h3 class="text-sm font-medium text-gray-700 mb-2">Location on Trail</h3>
                <div class="flex items-center text-gray-900">
                    <svg class="w-5 h-5 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    {{ $safetyIncident->location }}
                </div>
            </div>
            @endif

            <!-- Description -->
            <div class="mb-6">
                <h3 class="text-sm font-medium text-gray-700 mb-2">Incident Description</h3>
                <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                    <p class="text-gray-900 leading-relaxed whitespace-pre-line">{{ $safetyIncident->description }}</p>
                </div>
            </div>

            <!-- Affected Parties -->
            @if($safetyIncident->affected_parties_count)
            <div class="mb-6">
                <h3 class="text-sm font-medium text-gray-700 mb-2">People Affected</h3>
                <div class="flex items-center text-gray-900">
                    <svg class="w-5 h-5 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    {{ $safetyIncident->affected_parties_count }} {{ $safetyIncident->affected_parties_count == 1 ? 'person' : 'people' }} affected
                </div>
            </div>
            @endif

            <!-- Witnesses -->
            @if($safetyIncident->witnesses)
            <div class="mb-6">
                <h3 class="text-sm font-medium text-gray-700 mb-2">Witnesses Present</h3>
                <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                    <p class="text-gray-900">{{ $safetyIncident->witnesses ? 'Yes' : 'No' }}</p>
                </div>
            </div>
            @endif
        </div>

        <!-- Resolution Notes (if provided by hiker) -->
        @if($safetyIncident->resolution_notes)
        <div class="px-6 py-8 border-t border-gray-200 bg-gray-50">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Additional Notes</h2>
            <div class="bg-white rounded-lg p-5 border border-gray-200">
                <p class="text-gray-700 leading-relaxed whitespace-pre-line">{{ $safetyIncident->resolution_notes }}</p>
            </div>
        </div>
        @endif

        <!-- Contact Information (if provided) -->
        @if($safetyIncident->contact_info)
        <div class="px-6 py-4 border-t border-gray-200 bg-blue-50">
            <div class="flex items-start">
                <svg class="w-5 h-5 mr-3 text-blue-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div>
                    <h3 class="text-sm font-medium text-blue-900">Reporter Contact Information</h3>
                    <p class="text-sm text-blue-700 mt-1">{{ $safetyIncident->contact_info }}</p>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

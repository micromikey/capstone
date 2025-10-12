<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('My Itineraries') }}
            </h2>
            <a href="{{ route('itinerary.build') }}" class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition-colors shadow-md hover:shadow-lg">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                Create New Itinerary
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if($itineraries->count() > 0)
                <!-- Stats Overview -->
                <div class="mb-8 grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl p-6 text-white shadow-xl">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-blue-100 text-sm font-medium mb-1">Total Itineraries</p>
                                <p class="text-4xl font-bold">{{ $itineraries->total() }}</p>
                            </div>
                            <div class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center">
                                <span class="text-3xl">🗺️</span>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gradient-to-br from-green-500 to-emerald-600 rounded-2xl p-6 text-white shadow-xl">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-green-100 text-sm font-medium mb-1">Latest Itinerary</p>
                                <p class="text-lg font-bold truncate">{{ $itineraries->first()->title ?? 'N/A' }}</p>
                            </div>
                            <div class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center">
                                <span class="text-3xl">⭐</span>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gradient-to-br from-purple-500 to-indigo-600 rounded-2xl p-6 text-white shadow-xl">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-purple-100 text-sm font-medium mb-1">Created</p>
                                <p class="text-lg font-bold">{{ $itineraries->first()->created_at->diffForHumans() ?? 'N/A' }}</p>
                            </div>
                            <div class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center">
                                <span class="text-3xl">📅</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Itineraries Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                    @foreach($itineraries as $itinerary)
                        <div class="bg-white overflow-hidden shadow-xl rounded-2xl border border-gray-200 hover:shadow-2xl transition-all duration-300 hover:scale-105 flex flex-col">
                            <!-- Header with gradient -->
                            <div class="relative p-6 bg-gradient-to-r from-blue-500 to-indigo-600 text-white overflow-hidden">
                                <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -translate-y-16 translate-x-16"></div>
                                <div class="relative z-10">
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="text-xs text-blue-100 bg-white/20 px-3 py-1 rounded-full font-medium">
                                            {{ $itinerary->created_at->format('M d, Y') }}
                                        </span>
                                        @if($itinerary->duration_days)
                                            <span class="text-xs text-blue-100 bg-white/20 px-3 py-1 rounded-full font-medium">
                                                {{ $itinerary->duration_days }} {{ $itinerary->duration_days == 1 ? 'day' : 'days' }}
                                            </span>
                                        @endif
                                    </div>
                                    <h3 class="text-xl font-bold text-white mb-2 line-clamp-2">{{ $itinerary->title }}</h3>
                                    <p class="text-blue-100 text-sm flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        </svg>
                                        {{ $itinerary->trail_name ?? 'N/A' }}
                                    </p>
                                </div>
                            </div>

                            <!-- Content -->
                            <div class="p-6 flex-grow flex flex-col">
                                <!-- Quick Stats -->
                                <div class="grid grid-cols-3 gap-3 mb-6">
                                    <div class="text-center p-3 bg-gradient-to-br from-gray-50 to-gray-100 rounded-xl border border-gray-200">
                                        <div class="text-sm font-bold text-gray-800 mb-1">{{ $itinerary->difficulty_level ?? 'N/A' }}</div>
                                        <div class="text-xs text-gray-600 font-medium">Difficulty</div>
                                    </div>
                                    <div class="text-center p-3 bg-gradient-to-br from-gray-50 to-gray-100 rounded-xl border border-gray-200">
                                        <div class="text-sm font-bold text-gray-800 mb-1">{{ $itinerary->distance ?? 'N/A' }}</div>
                                        <div class="text-xs text-gray-600 font-medium">Distance</div>
                                    </div>
                                    <div class="text-center p-3 bg-gradient-to-br from-gray-50 to-gray-100 rounded-xl border border-gray-200">
                                        <div class="text-sm font-bold text-gray-800 mb-1">
                                            @if($itinerary->start_date)
                                                {{ \Carbon\Carbon::parse($itinerary->start_date)->format('M d') }}
                                            @else
                                                N/A
                                            @endif
                                        </div>
                                        <div class="text-xs text-gray-600 font-medium">Start Date</div>
                                    </div>
                                </div>

                                <!-- Additional Info -->
                                @if($itinerary->number_of_people)
                                <div class="mb-6">
                                    <div class="flex items-center text-sm text-gray-600">
                                        <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                        </svg>
                                        <span class="font-medium">{{ $itinerary->number_of_people }} {{ $itinerary->number_of_people == 1 ? 'person' : 'people' }}</span>
                                    </div>
                                </div>
                                @endif

                                <!-- Action Buttons -->
                                <div class="space-y-2 mt-auto">
                                    <a href="{{ route('itinerary.show', $itinerary) }}" class="block w-full text-center bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white py-2.5 px-4 rounded-lg font-semibold text-sm transition-all duration-200 transform hover:scale-105 shadow-md">
                                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                        View Details
                                    </a>
                                    <a href="{{ route('itinerary.print', $itinerary) }}" target="_blank" class="block w-full text-center bg-blue-600 hover:bg-blue-700 text-white py-2.5 px-4 rounded-lg font-semibold text-sm transition-all duration-200 transform hover:scale-105 shadow-md">
                                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                                        </svg>
                                        Print Itinerary
                                    </a>
                                </div>
                            </div>

                            <!-- Footer with timestamp -->
                            <div class="px-6 py-3 bg-gray-50 border-t border-gray-200">
                                <p class="text-xs text-gray-500 flex items-center">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Created {{ $itinerary->created_at->diffForHumans() }}
                                </p>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="mt-8">
                    {{ $itineraries->links() }}
                </div>

            @else
                <!-- Empty State -->
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-2xl">
                    <div class="p-12 text-center">
                        <div class="w-32 h-32 bg-gradient-to-br from-blue-100 to-indigo-100 rounded-full flex items-center justify-center mx-auto mb-6">
                            <span class="text-6xl">🗺️</span>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 mb-3">No Itineraries Yet</h3>
                        <p class="text-gray-600 mb-8 max-w-md mx-auto">
                            You haven't created any itineraries yet. Start planning your next hiking adventure by creating your first personalized itinerary!
                        </p>
                        <div class="space-y-3 max-w-sm mx-auto">
                            <a href="{{ route('itinerary.build') }}" class="block w-full text-center bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 text-white py-3 px-6 rounded-xl font-semibold transition-all duration-200 transform hover:scale-105 shadow-lg">
                                <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                </svg>
                                Create Your First Itinerary
                            </a>
                            <a href="{{ route('dashboard') }}" class="block w-full text-center bg-white hover:bg-gray-50 text-gray-700 py-3 px-6 rounded-xl font-semibold border-2 border-gray-300 transition-all duration-200">
                                Back to Dashboard
                            </a>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>

<x-app-layout>
    <x-slot name="header">
        <div class="space-y-4">
            <x-trail-breadcrumb currentPage="Trail Details" />
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('Trail Details') }}
                </h2>
                <div class="flex space-x-3">
                    <a href="{{ route('org.trails.edit', $trail) }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg transition-colors">
                        <svg class="inline-block w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        Edit Trail
                    </a>
                    <a href="{{ route('org.trails.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded-lg transition-colors">
                        <svg class="inline-block w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Back to Trails
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <!-- Trail Header -->
                <div class="p-6 bg-gradient-to-r from-[#336d66] to-[#20b6d2] text-white">
                    <div class="flex items-center space-x-4">
                        <div class="bg-white/20 p-3 rounded-full">
                            <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-1.447-.894L15 4m0 13V4m-13 0v9a1 1 0 001 1h1m8-4v1a1 1 0 01-1 1H9m13 0a1 1 0 01-1 1H9m13 0v-1a1 1 0 00-1-1H9" />
                            </svg>
                        </div>
                        <div>
                            <h1 class="text-3xl font-bold">{{ $trail->trail_name }}</h1>
                            <p class="text-xl text-white/90">{{ $trail->mountain_name }}</p>
                            <div class="flex items-center space-x-4 mt-2">
                                <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full bg-white/20">
                                    {{ ucfirst($trail->difficulty) }}
                                </span>
                                <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full bg-white/20">
                                    {{ $trail->duration }}
                                </span>
                                <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full bg-white/20">
                                    ₱{{ number_format($trail->price, 2) }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Trail Content -->
                <div class="p-6">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                        <!-- Left Column -->
                        <div class="space-y-6">
                            <!-- Basic Information -->
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">Basic Information</h3>
                                <div class="bg-gray-50 rounded-lg p-4 space-y-3">
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Location:</span>
                                        <span class="font-medium">{{ $trail->location->name ?? 'N/A' }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Best Season:</span>
                                        <span class="font-medium">{{ $trail->best_season }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Departure Point:</span>
                                        <span class="font-medium">{{ $trail->departure_point }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Transport Options:</span>
                                        <span class="font-medium">{{ $trail->transport_options }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Package Inclusions -->
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">Package Inclusions</h3>
                                <div class="bg-gray-50 rounded-lg p-4">
                                    <p class="text-gray-700">{{ $trail->package_inclusions }}</p>
                                </div>
                            </div>

                            <!-- Terrain & Requirements -->
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">Terrain & Requirements</h3>
                                <div class="bg-gray-50 rounded-lg p-4 space-y-3">
                                    <div>
                                        <span class="text-gray-600 font-medium">Terrain Notes:</span>
                                        <p class="text-gray-700 mt-1">{{ $trail->terrain_notes }}</p>
                                    </div>
                                    @if($trail->other_trail_notes)
                                    <div>
                                        <span class="text-gray-600 font-medium">Additional Notes:</span>
                                        <p class="text-gray-700 mt-1">{{ $trail->other_trail_notes }}</p>
                                    </div>
                                    @endif
                                    @if($trail->requirements)
                                    <div>
                                        <span class="text-gray-600 font-medium">Requirements:</span>
                                        <p class="text-gray-700 mt-1">{{ $trail->requirements }}</p>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Right Column -->
                        <div class="space-y-6">
                            <!-- Permits & Safety -->
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">Permits & Safety</h3>
                                <div class="bg-gray-50 rounded-lg p-4 space-y-3">
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Permit Required:</span>
                                        <span class="font-medium">{{ $trail->permit_required ? 'Yes' : 'No' }}</span>
                                    </div>
                                    @if($trail->permit_process)
                                    <div>
                                        <span class="text-gray-600 font-medium">Permit Process:</span>
                                        <p class="text-gray-700 mt-1">{{ $trail->permit_process }}</p>
                                    </div>
                                    @endif
                                    <div>
                                        <span class="text-gray-600 font-medium">Emergency Contacts:</span>
                                        <p class="text-gray-700 mt-1">{{ $trail->emergency_contacts }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Health & Fitness -->
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">Health & Fitness</h3>
                                <div class="bg-gray-50 rounded-lg p-4 space-y-3">
                                    <div>
                                        <span class="text-gray-600 font-medium">Health & Fitness Requirements:</span>
                                        <p class="text-gray-700 mt-1">{{ $trail->health_fitness }}</p>
                                    </div>
                                    <div>
                                        <span class="text-gray-600 font-medium">Packing List:</span>
                                        <p class="text-gray-700 mt-1">{{ $trail->packing_list }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Additional Information -->
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">Additional Information</h3>
                                <div class="bg-gray-50 rounded-lg p-4 space-y-3">
                                    @if($trail->side_trips)
                                    <div>
                                        <span class="text-gray-600 font-medium">Side Trips:</span>
                                        <p class="text-gray-700 mt-1">{{ $trail->side_trips }}</p>
                                    </div>
                                    @endif
                                    @if($trail->campsite_info)
                                    <div>
                                        <span class="text-gray-600 font-medium">Campsite Information:</span>
                                        <p class="text-gray-700 mt-1">{{ $trail->campsite_info }}</p>
                                    </div>
                                    @endif
                                    @if($trail->guide_info)
                                    <div>
                                        <span class="text-gray-600 font-medium">Guide Information:</span>
                                        <p class="text-gray-700 mt-1">{{ $trail->guide_info }}</p>
                                    </div>
                                    @endif
                                    @if($trail->environmental_practices)
                                    <div>
                                        <span class="text-gray-600 font-medium">Environmental Practices:</span>
                                        <p class="text-gray-700 mt-1">{{ $trail->environmental_practices }}</p>
                                    </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Feedback & Testimonials -->
                            @if($trail->customers_feedback || $trail->testimonials_faqs)
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">Customer Feedback</h3>
                                <div class="bg-gray-50 rounded-lg p-4 space-y-3">
                                    @if($trail->customers_feedback)
                                    <div>
                                        <span class="text-gray-600 font-medium">Customer Feedback:</span>
                                        <p class="text-gray-700 mt-1">{{ $trail->customers_feedback }}</p>
                                    </div>
                                    @endif
                                    @if($trail->testimonials_faqs)
                                    <div>
                                        <span class="text-gray-600 font-medium">Testimonials & FAQs:</span>
                                        <p class="text-gray-700 mt-1">{{ $trail->testimonials_faqs }}</p>
                                    </div>
                                    @endif
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Trail Status -->
                    <div class="mt-8 pt-6 border-t border-gray-200">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <span class="text-gray-600 font-medium">Trail Status:</span>
                                <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full
                                    @if($trail->is_active) bg-green-100 text-green-800 @else bg-red-100 text-red-800 @endif">
                                    {{ $trail->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </div>
                            <form method="POST" action="{{ route('org.trails.toggle-status', $trail) }}" class="inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="bg-yellow-600 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded-lg transition-colors">
                                    {{ $trail->is_active ? 'Deactivate Trail' : 'Activate Trail' }}
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

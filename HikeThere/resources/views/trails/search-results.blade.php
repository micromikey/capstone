<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Search Results for "{{ $query }}"
            </h2>
            
            <a href="{{ route('explore') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white text-sm font-medium rounded-lg transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Explore
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if($trails->count() > 0)
                <div class="mb-6">
                    <p class="text-gray-600">Found {{ $trails->count() }} trail(s) matching "{{ $query }}"</p>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($trails as $trail)
                        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow">
                            <!-- Trail Image -->
                            <div class="relative h-48 bg-gray-200">
                                @if($trail->primaryImage)
                                    <img src="{{ $trail->primaryImage->url }}" alt="{{ $trail->trail_name }}" class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-gray-400">
                                        <svg class="w-16 h-16" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                @endif
                                
                                <!-- Difficulty Badge -->
                                <div class="absolute top-3 left-3">
                                    @php
                                        $difficultyColors = [
                                            'beginner' => 'bg-green-500',
                                            'intermediate' => 'bg-yellow-500',
                                            'advanced' => 'bg-red-500'
                                        ];
                                        $difficultyLabels = [
                                            'beginner' => 'Beginner',
                                            'intermediate' => 'Moderate',
                                            'advanced' => 'Advanced'
                                        ];
                                        $color = $difficultyColors[$trail->difficulty] ?? 'bg-gray-500';
                                        $label = $trail->difficulty_label;
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium text-white {{ $color }}">
                                        {{ $label }}
                                    </span>
                                </div>

                                <!-- Price Badge -->
                                <div class="absolute top-3 right-3">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium text-white bg-green-600">
                                        ₱{{ number_format(optional($trail->package)->price ?? $trail->price ?? 0, 2) }}
                                    </span>
                                </div>
                            </div>

                            <!-- Trail Info -->
                            <div class="p-4">
                                <h3 class="text-lg font-semibold text-gray-900 mb-1">{{ $trail->trail_name }}</h3>
                                <p class="text-sm text-gray-600 mb-2">{{ $trail->mountain_name }}</p>
                                
                                <!-- Location -->
                                <div class="flex items-center text-sm text-gray-500 mb-3">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"></path>
                                    </svg>
                                    {{ $trail->location->name }}, {{ $trail->location->province }}
                                </div>

                                <!-- Stats -->
                                <div class="grid grid-cols-3 gap-4 text-center mb-4">
                                    <div>
                                        <div class="text-sm font-semibold text-gray-900">{{ $trail->length }} km</div>
                                        <div class="text-xs text-gray-500">Length</div>
                                    </div>
                                    <div>
                                        <div class="text-sm font-semibold text-gray-900">{{ $trail->elevation_gain }} m</div>
                                        <div class="text-xs text-gray-500">Elevation</div>
                                    </div>
                                    <div>
                                        <div class="text-sm font-semibold text-gray-900">{{ optional($trail->package)->duration ?? $trail->duration }}</div>
                                        <div class="text-xs text-gray-500">Duration</div>
                                    </div>
                                </div>

                                <!-- Organization -->
                                <div class="flex items-center justify-between mb-4">
                                    <div class="flex items-center text-sm text-gray-600">
                                        <svg class="w-4 h-4 mr-1 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"></path>
                                        </svg>
                                        {{ $trail->user->display_name }}
                                    </div>
                                </div>

                                <!-- Actions -->
                                <div class="flex gap-2">
                                    <a href="{{ route('trails.show', $trail) }}" 
                                       class="flex-1 bg-blue-600 hover:bg-blue-700 text-white text-center py-2 px-4 rounded-lg text-sm font-medium transition-colors">
                                        View Details
                                    </a>
                                    <a href="{{ route('explore') }}?location={{ $trail->location->slug }}" 
                                       class="bg-gray-100 hover:bg-gray-200 text-gray-700 py-2 px-4 rounded-lg text-sm font-medium transition-colors">
                                        Explore
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-12 text-center">
                    <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"></path>
                    </svg>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No trails found</h3>
                    <p class="text-gray-500 mb-6">We couldn't find any trails matching "{{ $query }}". Try searching with different keywords or browse all available trails.</p>
                    <a href="{{ route('explore') }}" class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition-colors">
                        Browse All Trails
                    </a>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>

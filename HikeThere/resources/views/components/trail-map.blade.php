@props([
    'height' => '400px',
    'showControls' => true,
    'showSearch' => true,
    'showFilters' => true,
    'showActions' => true,
    'trailId' => null,
    'centerLat' => null,
    'centerLng' => null,
    'zoom' => null
])

<div class="trail-map-component" 
     data-height="{{ $height }}"
     data-show-controls="{{ $showControls ? 'true' : 'false' }}"
     data-show-search="{{ $showSearch ? 'true' : 'false' }}"
     data-show-filters="{{ $showFilters ? 'true' : 'false' }}"
     data-show-actions="{{ $showActions ? 'true' : 'false' }}"
     @if($trailId) data-trail-id="{{ $trailId }}" @endif
     @if($centerLat) data-center-lat="{{ $centerLat }}" @endif
     @if($centerLng) data-center-lng="{{ $centerLng }}" @endif
     @if($zoom) data-zoom="{{ $zoom }}" @endif>
    
    @if($showControls)
    <div class="mb-4">
        @if($showSearch)
        <div class="mb-4">
            <label for="embedded-map-search" class="block text-sm font-medium text-gray-700 mb-2">Search Trails</label>
            <div class="relative">
                <input type="text" id="embedded-map-search" placeholder="Search by trail name, mountain, or location..." 
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500">
                <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
            </div>
        </div>
        @endif

        @if($showFilters)
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div>
                <label for="embedded-difficulty-filter" class="block text-sm font-medium text-gray-700 mb-2">Difficulty</label>
                <select id="embedded-difficulty-filter" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500">
                    <option value="">All Difficulties</option>
                    <option value="beginner">Beginner</option>
                    <option value="intermediate">Intermediate</option>
                    <option value="advanced">Advanced</option>
                </select>
            </div>
            <div>
                <label for="embedded-radius-filter" class="block text-sm font-medium text-gray-700 mb-2">Search Radius</label>
                <select id="embedded-radius-filter" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500">
                    <option value="10">10 km</option>
                    <option value="25" selected>25 km</option>
                    <option value="50">50 km</option>
                    <option value="100">100 km</option>
                </select>
            </div>
        </div>
        @endif

        @if($showActions)
        <div class="flex flex-wrap gap-3">
            <button id="embedded-use-location-btn" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
                Use My Location
            </button>
            
            <button id="embedded-reset-map-btn" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
                Reset Map
            </button>

            <button id="embedded-cluster-toggle-btn" class="inline-flex items-center px-4 py-2 bg-purple-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-purple-700 focus:bg-purple-700 active:bg-purple-900 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                </svg>
                Toggle Clustering
            </button>
        </div>
        @endif
    </div>
    @endif

    <!-- Map Container -->
    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
        <div id="embedded-map-{{ $attributes->get('id', uniqid()) }}" class="w-full" style="height: {{ $height }};"></div>
    </div>

    <!-- Trail Info Panel -->
    <div id="embedded-trail-info-panel-{{ $attributes->get('id', uniqid()) }}" class="fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 shadow-lg transform translate-y-full transition-transform duration-300 ease-in-out z-50 max-h-[60vh] overflow-y-auto">
        <div class="p-4">
            <div class="flex items-center justify-between mb-4">
                <h3 id="embedded-trail-info-title-{{ $attributes->get('id', uniqid()) }}" class="text-lg font-semibold text-gray-900">Trail Information</h3>
                <button id="embedded-close-trail-info-{{ $attributes->get('id', uniqid()) }}" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div id="embedded-trail-info-content-{{ $attributes->get('id', uniqid()) }}" class="space-y-4">
                <!-- Trail information will be populated here -->
            </div>
        </div>
    </div>

    <!-- Loading Spinner -->
    <div id="embedded-loading-spinner-{{ $attributes->get('id', uniqid()) }}" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-lg p-6 flex items-center space-x-3">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-green-600"></div>
            <span class="text-gray-700">Loading trails...</span>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize embedded map
    const mapComponent = document.querySelector('.trail-map-component');
    if (mapComponent) {
        const mapId = mapComponent.querySelector('[id^="embedded-map-"]').id;
        const uniqueId = mapId.replace('embedded-map-', '');
        
        // Initialize embedded map functionality
        initEmbeddedMap(uniqueId, mapComponent.dataset);
    }
});

function initEmbeddedMap(uniqueId, config) {
    // Create embedded map instance
    const embeddedMap = new HikeThereMap({
        mapElementId: `embedded-map-${uniqueId}`,
        isEmbedded: true,
        config: config
    });
    
    // Store reference for global access
    window[`embeddedMap_${uniqueId}`] = embeddedMap;
}
</script>
@endpush

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Map Component Demo') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Full Map View -->
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg mb-8">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Full Map View</h3>
                    <p class="text-gray-600 mb-4">This is the complete map view with all controls and features.</p>
                    <a href="{{ route('map.index') }}" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        View Full Map
                    </a>
                </div>
            </div>

            <!-- Embedded Map Examples -->
            <div class="space-y-8">
                <!-- Example 1: Full Featured Map -->
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Full Featured Embedded Map</h3>
                        <p class="text-gray-600 mb-4">This embedded map includes all controls: search, filters, and action buttons.</p>
                        
                        <x-trail-map 
                            height="500px"
                            :showControls="true"
                            :showSearch="true"
                            :showFilters="true"
                            :showActions="true"
                            id="demo-map-1" />
                    </div>
                </div>

                <!-- Example 2: Search Only Map -->
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Search Only Map</h3>
                        <p class="text-gray-600 mb-4">This embedded map only shows the search functionality.</p>
                        
                        <x-trail-map 
                            height="400px"
                            :showControls="true"
                            :showSearch="true"
                            :showFilters="false"
                            :showActions="false"
                            id="demo-map-2" />
                    </div>
                </div>

                <!-- Example 3: Minimal Map -->
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Minimal Map</h3>
                        <p class="text-gray-600 mb-4">This embedded map shows only the map without any controls.</p>
                        
                        <x-trail-map 
                            height="350px"
                            :showControls="false"
                            :showSearch="false"
                            :showFilters="false"
                            :showActions="false"
                            id="demo-map-3" />
                    </div>
                </div>

                <!-- Example 4: Centered Map -->
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Centered Map (Mt. Pulag)</h3>
                        <p class="text-gray-600 mb-4">This embedded map is centered on Mt. Pulag with a specific zoom level.</p>
                        
                        <x-trail-map 
                            height="450px"
                            :showControls="true"
                            :showSearch="true"
                            :showFilters="true"
                            :showActions="true"
                            centerLat="16.5969"
                            centerLng="120.8958"
                            :zoom="10"
                            id="demo-map-4" />
                    </div>
                </div>
            </div>

            <!-- Usage Instructions -->
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg mt-8">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">How to Use the Trail Map Component</h3>
                    
                    <div class="prose max-w-none">
                        <h4 class="text-md font-semibold text-gray-800 mb-2">Basic Usage</h4>
                        <pre class="bg-gray-100 p-4 rounded-lg text-sm overflow-x-auto"><code>&lt;x-trail-map /&gt;</code></pre>
                        
                        <h4 class="text-md font-semibold text-gray-800 mb-2 mt-4">With Custom Height</h4>
                        <pre class="bg-gray-100 p-4 rounded-lg text-sm overflow-x-auto"><code>&lt;x-trail-map height="600px" /&gt;</code></pre>
                        
                        <h4 class="text-md font-semibold text-gray-800 mb-2 mt-4">With Custom Controls</h4>
                        <pre class="bg-gray-100 p-4 rounded-lg text-sm overflow-x-auto"><code>&lt;x-trail-map 
    :showSearch="true"
    :showFilters="false"
    :showActions="true"
    height="500px" /&gt;</code></pre>
                        
                        <h4 class="text-md font-semibold text-gray-800 mb-2 mt-4">With Custom Center and Zoom</h4>
                        <pre class="bg-gray-100 p-4 rounded-lg text-sm overflow-x-auto"><code>&lt;x-trail-map 
    centerLat="16.5969"
    centerLng="120.8958"
    :zoom="12"
    height="400px" /&gt;</code></pre>
                        
                        <h4 class="text-md font-semibold text-gray-800 mb-2 mt-4">Available Props</h4>
                        <ul class="list-disc list-inside space-y-1 text-sm text-gray-600">
                            <li><strong>height</strong>: Map height (default: "400px")</li>
                            <li><strong>showControls</strong>: Show/hide all controls (default: true)</li>
                            <li><strong>showSearch</strong>: Show/hide search box (default: true)</li>
                            <li><strong>showFilters</strong>: Show/hide difficulty and radius filters (default: true)</li>
                            <li><strong>showActions</strong>: Show/hide action buttons (default: true)</li>
                            <li><strong>centerLat</strong>: Custom center latitude</li>
                            <li><strong>centerLng</strong>: Custom center longitude</li>
                            <li><strong>zoom</strong>: Custom zoom level</li>
                            <li><strong>id</strong>: Unique identifier for the map</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

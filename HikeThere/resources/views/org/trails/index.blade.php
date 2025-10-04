<x-app-layout>
    <x-slot name="header">
        <div class="space-y-4">
            <x-trail-breadcrumb />
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('Manage Trails') }}
                </h2>
                <a href="{{ route('org.trails.create') }}" class="bg-[#336d66] hover:bg-[#2a5a54] text-white font-bold py-2 px-4 rounded-lg transition-colors">
                    <svg class="inline-block w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    Add New Trail
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

            <!-- Floating Filters Sidebar -->
            <div id="floating-filters" class="fixed top-56 left-10 z-40 transition-all duration-300 transform hidden lg:block">
                <div class="bg-white/95 backdrop-blur-md rounded-xl shadow-lg border border-gray-200/50 p-4 w-64 max-h-[calc(100vh-14rem)] overflow-y-auto">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-sm font-semibold text-gray-800 flex items-center">
                            <svg class="w-4 h-4 mr-1.5 text-[#336d66]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                            </svg>
                            Filters
                        </h3>
                        @if(request()->hasAny(['mountain', 'difficulty', 'price_min', 'price_max', 'sort_by', 'sort_order']))
                            <a href="{{ route('org.trails.index') }}" class="text-xs text-red-600 hover:text-red-800 font-medium">Clear</a>
                        @endif
                    </div>

                    <form method="GET" action="{{ route('org.trails.index') }}" id="trailFilterForm" class="space-y-3">
                        <!-- Mountain Filter -->
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Mountain</label>
                            <select name="mountain" onchange="this.form.submit()" class="w-full border-gray-300 rounded-md shadow-sm focus:border-[#336d66] focus:ring focus:ring-[#336d66] focus:ring-opacity-50 text-xs py-1.5">
                                <option value="">All Mountains</option>
                                @foreach($mountains as $mountain)
                                    <option value="{{ $mountain }}" {{ request('mountain') == $mountain ? 'selected' : '' }}>{{ $mountain }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Difficulty Filter -->
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Difficulty</label>
                            <select name="difficulty" onchange="this.form.submit()" class="w-full border-gray-300 rounded-md shadow-sm focus:border-[#336d66] focus:ring focus:ring-[#336d66] focus:ring-opacity-50 text-xs py-1.5">
                                <option value="">All Difficulties</option>
                                <option value="beginner" {{ request('difficulty') == 'beginner' ? 'selected' : '' }}>Beginner</option>
                                <option value="intermediate" {{ request('difficulty') == 'intermediate' ? 'selected' : '' }}>Intermediate</option>
                                <option value="advanced" {{ request('difficulty') == 'advanced' ? 'selected' : '' }}>Advanced</option>
                            </select>
                        </div>

                        <!-- Price Range -->
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Price (₱)</label>
                            <div class="space-y-1.5">
                                <input type="number" name="price_min" placeholder="Min" value="{{ request('price_min') }}" onchange="this.form.submit()" class="w-full border-gray-300 rounded-md shadow-sm focus:border-[#336d66] focus:ring focus:ring-[#336d66] focus:ring-opacity-50 text-xs py-1.5">
                                <input type="number" name="price_max" placeholder="Max" value="{{ request('price_max') }}" onchange="this.form.submit()" class="w-full border-gray-300 rounded-md shadow-sm focus:border-[#336d66] focus:ring focus:ring-[#336d66] focus:ring-opacity-50 text-xs py-1.5">
                            </div>
                        </div>

                        <hr class="my-3 border-gray-200">

                        <!-- Sort By -->
                        <div>
                            <label class="flex items-center text-xs font-medium text-gray-700 mb-1">
                                <svg class="w-3 h-3 mr-1 text-[#336d66]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h13M3 8h9m-9 4h6m4 0l4-4m0 0l4 4m-4-4v12" />
                                </svg>
                                Sort By
                            </label>
                            <select name="sort_by" onchange="this.form.submit()" class="w-full border-gray-300 rounded-md shadow-sm focus:border-[#336d66] focus:ring focus:ring-[#336d66] focus:ring-opacity-50 text-xs py-1.5">
                                <option value="created_at" {{ request('sort_by') == 'created_at' ? 'selected' : '' }}>Date Added</option>
                                <option value="updated_at" {{ request('sort_by') == 'updated_at' ? 'selected' : '' }}>Date Modified</option>
                                <option value="popularity" {{ request('sort_by') == 'popularity' ? 'selected' : '' }}>Popularity</option>
                                <option value="price" {{ request('sort_by') == 'price' ? 'selected' : '' }}>Price</option>
                                <option value="length" {{ request('sort_by') == 'length' ? 'selected' : '' }}>Length</option>
                                <option value="name" {{ request('sort_by') == 'name' ? 'selected' : '' }}>Name</option>
                            </select>
                        </div>

                        <!-- Sort Direction -->
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Order</label>
                            <div class="grid grid-cols-2 gap-1.5">
                                <button type="submit" name="sort_order" value="asc" class="flex items-center justify-center px-2 py-1.5 text-xs font-medium rounded-md transition-colors {{ request('sort_order') == 'asc' ? 'bg-[#336d66] text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                                    <svg class="w-3 h-3 mr-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                                    </svg>
                                    Asc
                                </button>
                                <button type="submit" name="sort_order" value="desc" class="flex items-center justify-center px-2 py-1.5 text-xs font-medium rounded-md transition-colors {{ request('sort_order', 'desc') == 'desc' ? 'bg-[#336d66] text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                                    <svg class="w-3 h-3 mr-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                    Desc
                                </button>
                            </div>
                        </div>

                        <!-- Hidden inputs to preserve other sort parameters -->
                        <input type="hidden" name="sort_by" value="{{ request('sort_by', 'created_at') }}">
                    </form>

                    <!-- Pagination Section -->
                    @if($trails->hasPages())
                        <hr class="my-3 border-gray-200">
                        <div class="space-y-2">
                            <div class="flex items-center justify-between text-xs text-gray-600">
                                <span>Page {{ $trails->currentPage() }} of {{ $trails->lastPage() }}</span>
                                <span class="font-medium">{{ $trails->total() }} total</span>
                            </div>
                            <div class="flex gap-2">
                                @if($trails->onFirstPage())
                                    <button disabled class="flex-1 px-3 py-2 text-xs font-medium bg-gray-100 text-gray-400 rounded-md cursor-not-allowed">
                                        <svg class="w-3 h-3 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                                        </svg>
                                        Previous
                                    </button>
                                @else
                                    <a href="{{ $trails->appends(request()->query())->previousPageUrl() }}" class="flex-1 px-3 py-2 text-xs font-medium bg-[#336d66] hover:bg-[#2a5a54] text-white rounded-md transition-colors text-center">
                                        <svg class="w-3 h-3 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                                        </svg>
                                        Previous
                                    </a>
                                @endif

                                @if($trails->hasMorePages())
                                    <a href="{{ $trails->appends(request()->query())->nextPageUrl() }}" class="flex-1 px-3 py-2 text-xs font-medium bg-[#336d66] hover:bg-[#2a5a54] text-white rounded-md transition-colors text-center">
                                        Next
                                        <svg class="w-3 h-3 inline ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                        </svg>
                                    </a>
                                @else
                                    <button disabled class="flex-1 px-3 py-2 text-xs font-medium bg-gray-100 text-gray-400 rounded-md cursor-not-allowed">
                                        Next
                                        <svg class="w-3 h-3 inline ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                        </svg>
                                    </button>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Trail Stats -->
            <!-- Floating Statistics Dashboard (Right) -->
            <div id="floating-stats" class="fixed top-56 right-10 z-40 transition-all duration-300 transform hidden xl:block">
                <div class="bg-white/95 backdrop-blur-md rounded-xl shadow-lg border border-gray-200/50 p-4 w-72 max-h-[calc(100vh-14rem)] overflow-y-auto">
                    <div class="flex items-center mb-4">
                        <svg class="w-5 h-5 mr-2 text-[#336d66]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-1.447-.894L15 4m0 13V4m-13 0v9a1 1 0 001 1h1m8-4v1a1 1 0 01-1 1H9m13 0a1 1 0 01-1 1H9m13 0v-1a1 1 0 00-1-1H9" />
                        </svg>
                        <h3 class="text-sm font-semibold text-gray-800">Trail Statistics</h3>
                    </div>

                    <div class="space-y-3">
                        <!-- Total Trails -->
                        <div class="bg-gradient-to-br from-white to-emerald-50 rounded-lg border border-emerald-100 p-3 hover:shadow-md transition-shadow">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 bg-[#336d66]/10 rounded-lg p-2">
                                    <svg class="h-5 w-5 text-[#336d66]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-1.447-.894L15 4m0 13V4m-13 0v9a1 1 0 001 1h1m8-4v1a1 1 0 01-1 1H9m13 0a1 1 0 01-1 1H9m13 0v-1a1 1 0 00-1-1H9" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-xs font-medium text-gray-600">Total Trails</p>
                                    <p class="text-xl font-bold text-gray-900">{{ $trails->total() }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Active Trails -->
                        <div class="bg-gradient-to-br from-white to-green-50 rounded-lg border border-green-100 p-3 hover:shadow-md transition-shadow">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 bg-green-100 rounded-lg p-2">
                                    <svg class="h-5 w-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-xs font-medium text-gray-600">Active Trails</p>
                                    <p class="text-xl font-bold text-green-600">{{ $trails->where('is_active', true)->count() }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Inactive Trails -->
                        <div class="bg-gradient-to-br from-white to-yellow-50 rounded-lg border border-yellow-100 p-3 hover:shadow-md transition-shadow">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 bg-yellow-100 rounded-lg p-2">
                                    <svg class="h-5 w-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-xs font-medium text-gray-600">Inactive Trails</p>
                                    <p class="text-xl font-bold text-yellow-600">{{ $trails->where('is_active', false)->count() }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Current Page Count -->
                        <div class="bg-gradient-to-br from-white to-blue-50 rounded-lg border border-blue-100 p-3 hover:shadow-md transition-shadow">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 bg-blue-100 rounded-lg p-2">
                                    <svg class="h-5 w-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-xs font-medium text-gray-600">Showing</p>
                                    <p class="text-xl font-bold text-blue-600">{{ $trails->count() }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @if($trails->count() > 0)
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="p-6">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Trail Details
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Location
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Price
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Difficulty
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Status
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Actions
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($trails as $trail)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div>
                                                    <div class="text-sm font-medium text-gray-900">
                                                        {{ $trail->trail_name }}
                                                    </div>
                                                    <div class="text-sm text-gray-500">
                                                        {{ $trail->mountain_name }}
                                                    </div>
                                                    <div class="text-xs text-gray-400">
                                                        Duration: {{ optional($trail->package)->duration ?? $trail->duration }}
                                                    </div>
                                                        <div class="mt-1 flex flex-wrap gap-1">
                                                            @if($trail->coordinate_generation_method)
                                                                <span class="px-2 py-0.5 text-[10px] font-semibold rounded bg-indigo-100 text-indigo-700" title="Coordinate Source">
                                                                    {{ str_replace('_',' ',$trail->coordinate_generation_method) }}
                                                                </span>
                                                            @endif
                                                            @if($trail->metrics_confidence)
                                                                @php
                                                                    $confColors = [
                                                                        'high' => 'bg-green-100 text-green-700',
                                                                        'medium' => 'bg-yellow-100 text-yellow-700',
                                                                        'low' => 'bg-red-100 text-red-700'
                                                                    ];
                                                                @endphp
                                                                <span class="px-2 py-0.5 text-[10px] font-semibold rounded {{ $confColors[$trail->metrics_confidence] ?? 'bg-gray-100 text-gray-600' }}" title="Metrics Confidence">
                                                                    {{ ucfirst($trail->metrics_confidence) }} confidence
                                                                </span>
                                                            @endif
                                                            @if($trail->length)
                                                                <span class="px-2 py-0.5 text-[10px] rounded bg-blue-50 text-blue-700" title="Length (km)">
                                                                    {{ number_format($trail->length,2) }} km
                                                                </span>
                                                            @endif
                                                            @if($trail->elevation_gain)
                                                                <span class="px-2 py-0.5 text-[10px] rounded bg-purple-50 text-purple-700" title="Elevation Gain (m)">
                                                                    +{{ $trail->elevation_gain }} m
                                                                </span>
                                                            @endif
                                                        </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900">
                                                    {{ $trail->location->name ?? 'N/A' }}
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900">
                                                    ₱{{ number_format(optional($trail->package)->price ?? $trail->price ?? 0, 2) }}
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @php
                                                    $diffColors = [
                                                        'beginner' => 'bg-green-100 text-green-800',
                                                        'intermediate' => 'bg-yellow-100 text-yellow-800',
                                                        'advanced' => 'bg-red-100 text-red-800'
                                                    ];
                                                    $diffClass = $diffColors[$trail->difficulty] ?? 'bg-gray-100 text-gray-700';
                                                @endphp
                                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $diffClass }}">
                                                    {{ $trail->difficulty_label }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @php $statusClass = $trail->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; @endphp
                                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $statusClass }}">
                                                    {{ $trail->is_active ? 'Active' : 'Inactive' }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <div class="flex space-x-2">
                                                    <a href="{{ route('org.trails.show', $trail) }}" class="text-[#336d66] hover:text-[#2a5a54]">
                                                        View
                                                    </a>
                                                    <a href="{{ route('org.trails.edit', $trail) }}" class="text-blue-600 hover:text-blue-900">
                                                        Edit
                                                    </a>
                                                    <form method="POST" action="{{ route('org.trails.toggle-status', $trail) }}" class="inline">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" class="text-yellow-600 hover:text-yellow-900">
                                                            {{ $trail->is_active ? 'Deactivate' : 'Activate' }}
                                                        </button>
                                                    </form>
                                                    <form method="POST" action="{{ route('org.trails.destroy', $trail) }}" class="inline" onsubmit="return confirm('Are you sure you want to delete this trail?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="text-red-600 hover:text-red-900">
                                                            Delete
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="mt-6">
                            {{ $trails->links() }}
                        </div>
                    </div>
                </div>
            @else
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="p-12 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-1.447-.894L15 4m0 13V4m-13 0v9a1 1 0 001 1h1m8-4v1a1 1 0 01-1 1H9m13 0a1 1 0 01-1 1H9m13 0v-1a1 1 0 00-1-1H9" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No trails yet</h3>
                        <p class="mt-1 text-sm text-gray-500">Get started by creating your first hiking trail.</p>
                        <div class="mt-6">
                            <a href="{{ route('org.trails.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-[#336d66] hover:bg-[#2a5a54] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#336d66]">
                                <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                </svg>
                                Add New Trail
                            </a>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

    <!-- Create Event Modal -->
    @if(session('show_event_prompt'))
    <div id="createEventModal" class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center" aria-labelledby="modal-title" role="dialog" aria-modal="true" style="display: flex;">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <!-- Background overlay -->
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="closeModal()"></div>

            <!-- Center modal -->
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6 relative z-10">
                <div>
                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100">
                        <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                    <div class="mt-3 text-center sm:mt-5">
                        <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                            Trail Created Successfully!
                        </h3>
                        <div class="mt-2">
                            <p class="text-sm text-gray-500">
                                Your trail "<strong>{{ session('new_trail_name') }}</strong>" has been created. Would you like to create an event for this trail now?
                            </p>
                        </div>
                    </div>
                </div>
                <div class="mt-5 sm:mt-6 sm:grid sm:grid-cols-2 sm:gap-3 sm:grid-flow-row-dense">
                    <a href="{{ route('org.events.create', ['trail_id' => session('new_trail_id')]) }}" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-[#336d66] text-base font-medium text-white hover:bg-[#2a5a54] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#336d66] sm:col-start-2 sm:text-sm">
                        Create Event
                    </a>
                    <button type="button" onclick="closeModal()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#336d66] sm:mt-0 sm:col-start-1 sm:text-sm">
                        Maybe Later
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function closeModal() {
            const modal = document.getElementById('createEventModal');
            if (modal) {
                modal.style.display = 'none';
            }
        }

        // Close modal on escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeModal();
            }
        });
    </script>
    @endif
</x-app-layout>

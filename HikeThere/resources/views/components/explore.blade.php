<div class="relative p-6 lg:p-10 bg-gradient-to-r from-green-100 via-white to-white border-b border-gray-200 rounded-b-xl shadow-sm overflow-hidden min-h-[300px]">
    <div x-data="trailExplorer()" x-init="init()" class="flex flex-col lg:flex-row gap-6 max-w-7xl mx-auto">

        <!-- Sidebar -->
        <aside class="w-full lg:w-80 bg-white rounded-lg shadow-sm border border-gray-200">
            <!-- Location Selector -->
            <div class="p-4 border-b border-gray-200">
                <div class="flex items-center gap-2 text-gray-700">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"></path>
                    </svg>
                    <select x-model="selectedLocation" @change="fetchTrails()"
                        class="text-sm font-medium bg-white border border-gray-300 rounded px-2 py-1 w-full focus:ring-0 focus:outline-none">
                        <option value="" disabled>Select a location</option>
                        <template x-for="location in locations" :key="location.id">
                            <option :value="location.slug" x-text="location.name.toUpperCase()"></option>
                        </template>
                    </select>

                </div>
            </div>

            <!-- Trail List -->
            <div class="p-4 space-y-2">
                <div class="flex items-center gap-2 text-gray-700 mb-2">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 10a1 1 0 011-1h6a1 1 0 110 2H4a1 1 0 01-1-1zM3 16a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z"></path>
                    </svg>
                    <span class="text-sm font-semibold uppercase tracking-wide">Trail List</span>
                </div>

                <!-- Loading Skeleton -->
                <div x-show="loading" class="space-y-3 animate-pulse">
                    <template x-for="i in 3">
                        <div class="flex items-center gap-3 p-3">
                            <div class="w-10 h-10 bg-gray-200 rounded-full"></div>
                            <div class="flex-1">
                                <div class="h-4 bg-gray-200 rounded w-3/4 mb-2"></div>
                                <div class="h-3 bg-gray-200 rounded w-1/2"></div>
                            </div>
                        </div>
                    </template>
                </div>

                <!-- Trail Cards -->
                <template x-if="!loading && trails.length > 0">
                    <div class="space-y-2">
                        <template x-for="trail in trails" :key="trail.id">
                            <div @click="selectTrail(trail)"
                                class="flex items-center gap-3 p-3 rounded-lg cursor-pointer transition-all duration-200"
                                :class="trail.id === selectedTrail?.id 
                                    ? 'bg-green-50 border-l-4 border-green-500' 
                                    : 'hover:bg-gray-50 border-l-4 border-transparent'">

                                <div class="w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0"
                                    :class="getDifficultyColor(trail.difficulty)">
                                    <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"></path>
                                    </svg>
                                </div>

                                <div class="flex-1 min-w-0">
                                    <h3 class="font-medium text-gray-900 truncate" x-text="trail.name"></h3>
                                    <p class="text-sm text-gray-500">
                                        Difficulty: <span x-text="trail.difficulty"></span>
                                    </p>
                                </div>
                            </div>
                        </template>
                    </div>
                </template>

                <!-- Empty State -->
                <div x-show="!loading && trails.length === 0" class="text-center py-12 text-gray-500">
                    <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                    </svg>
                    <p>No trails found for this location.</p>
                </div>
            </div>
        </aside>

        <!-- Main Trail Details -->
        <main class="flex-1 bg-white rounded-lg shadow-sm border border-gray-200" x-show="selectedTrail">
            <div class="p-6 space-y-4">
                <div class="flex justify-between items-start">
                    <div>
                        <h1 class="text-2xl font-bold" x-text="selectedTrail.name"></h1>
                        <div class="text-sm text-gray-600 mt-1">
                            ⭐ <span x-text="selectedTrail.average_rating || 'N/A'"></span> ·
                            <span x-text="selectedTrail.difficulty || 'N/A'"></span> ·
                            <span x-text="selectedTrail.location || 'Unknown'"></span>
                        </div>
                    </div>
                    <div class="flex gap-2">
                        <button @click="downloadTrail()" class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-lg text-sm">
                            Download Trail
                        </button>
                        <button @click="viewFullTrail()" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg text-sm">
                            View Details
                        </button>
                    </div>
                </div>

                <!-- Images -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <img :src="selectedTrail.primary_image || 'https://via.placeholder.com/600x300'" class="rounded-lg object-cover h-64 w-full" alt="">
                    <img :src="selectedTrail.map_image || 'https://via.placeholder.com/600x300'" class="rounded-lg object-cover h-64 w-full" alt="">
                </div>

                <!-- Stats -->
                <div class="grid grid-cols-3 gap-4 text-center">
                    <div>
                        <div class="text-lg font-semibold" x-text="selectedTrail.length + ' km'"></div>
                        <div class="text-sm text-gray-500">Length</div>
                    </div>
                    <div>
                        <div class="text-lg font-semibold" x-text="selectedTrail.elevation_gain + ' m'"></div>
                        <div class="text-sm text-gray-500">Elevation Gain</div>
                    </div>
                    <div>
                        <div class="text-lg font-semibold" x-text="selectedTrail.estimated_time"></div>
                        <div class="text-sm text-gray-500">Estimated Time</div>
                    </div>
                </div>

                <!-- Description -->
                <div class="text-sm text-gray-700 leading-relaxed mt-2">
                    <p x-text="selectedTrail.summary || 'No description available.'"></p>
                </div>
            </div>
        </main>

        <!-- Initial Empty Prompt -->
        <main x-show="!selectedTrail && !loading" class="flex-1 bg-white rounded-lg shadow-sm border border-gray-200 flex items-center justify-center">
            <div class="text-center py-12">
                <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"></path>
                </svg>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Select a Trail</h3>
                <p class="text-gray-500 text-sm">Choose a trail from the sidebar to view details</p>
            </div>
        </main>
    </div>
</div>

<script>
    function trailExplorer() {
        return {
            locations: [],
            trails: [],
            selectedLocation: '',
            selectedTrail: null,
            loading: false,
            showFullDescription: false,

            async init() {
                await this.fetchLocations();
                if (this.locations.length > 0) {
                    this.selectedLocation = this.locations[0].slug;
                    await this.fetchTrails();
                }
            },

            async fetchLocations() {
                try {
                    const response = await fetch('/api/locations');
                    if (response.ok) {
                        this.locations = await response.json();
                    }
                } catch (error) {
                    console.error('Error fetching locations:', error);
                    this.showError('Failed to load locations');
                }
            },

            async fetchTrails() {
                this.loading = true;
                this.selectedTrail = null;

                try {
                    const params = new URLSearchParams();
                    if (this.selectedLocation) {
                        params.append('location', this.selectedLocation);
                    }

                    const response = await fetch(`/api/trails?${params}`);
                    if (response.ok) {
                        this.trails = await response.json();

                        // Auto-select first trail if available
                        if (this.trails.length > 0) {
                            this.selectedTrail = this.trails[0];
                        }
                    } else {
                        throw new Error('Failed to fetch trails');
                    }
                } catch (error) {
                    console.error('Error fetching trails:', error);
                    this.showError('Failed to load trails');
                    this.trails = [];
                } finally {
                    this.loading = false;
                }
            },

            selectTrail(trail) {
                this.selectedTrail = trail;
                this.showFullDescription = false;
            },

            getDifficultyColor(difficulty) {
                const colors = {
                    'Beginner Friendly': 'bg-green-500',
                    'Beginner-Friendly': 'bg-green-500',
                    'Moderate': 'bg-yellow-500',
                    'Moderate To Challenging': 'bg-orange-500',
                    'Challenging': 'bg-red-500',
                    'Strenuous': 'bg-red-700'
                };
                return colors[difficulty] || 'bg-gray-500';
            },

            async downloadTrail() {
                if (!this.selectedTrail) return;

                try {
                    // If trail has GPX file, download it
                    const response = await fetch(`/api/trails/${this.selectedTrail.slug}`);
                    if (response.ok) {
                        const trailDetails = await response.json();
                        if (trailDetails.gpx_file) {
                            window.open(trailDetails.gpx_file, '_blank');
                        } else {
                            this.showError('No GPX file available for this trail');
                        }
                    }
                } catch (error) {
                    console.error('Error downloading trail:', error);
                    this.showError('Failed to download trail file');
                }
            },

            viewFullTrail() {
                if (this.selectedTrail) {
                    window.location.href = `/trails/${this.selectedTrail.slug}`;
                }
            },

            showError(message) {
                // You can implement a toast notification here
                alert(message);
            }
        }
    }
</script>
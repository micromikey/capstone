<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-yellow-100 rounded-lg">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 2a10 10 0 100 20 10 10 0 000-20zm3.94 6.06l-2.12 5.66a1 1 0 01-.52.52l-5.66 2.12a.25.25 0 01-.32-.32l2.12-5.66a1 1 0 01.52-.52l5.66-2.12a.25.25 0 01.32.32z" />
                    </svg>
                </div>
                <div>
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                        {{ __('Explore') }}
                    </h2>
                    <p class="text-sm text-gray-600">Find your next hiking destination.</p>
                </div>
            </div>
                {{-- Search Bar --}}
                <form class="flex items-center max-w-2xl w-full relative" action="{{ route('trails.search') }}" method="GET">
                    <label for="trail-search" class="sr-only">Search Trails</label>
                    <div class="relative w-full">
                        <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                            <svg class="w-4 h-4 text-gray-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 21 21">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.15 5.6h.01m3.337 1.913h.01m-6.979 0h.01M5.541 11h.01M15 15h2.706a1.957 1.957 0 0 0 1.883-1.325A9 9 0 1 0 2.043 11.89 9.1 9.1 0 0 0 7.2 19.1a8.62 8.62 0 0 0 3.769.9A2.013 2.013 0 0 0 13 18v-.857A2.034 2.034 0 0 1 15 15Z" />
                            </svg>
                        </div>
                        <input type="text" id="header-search-input" name="q" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-green-500 focus:border-green-500 block w-full ps-10 p-3" placeholder="Search trails, mountains, locations..." value="{{ request('q') }}" autocomplete="off" />
                        @include('partials.header-search-dropdown')
                    </div>
                    <button type="submit" class="inline-flex items-center py-2.5 px-3 ms-2 text-sm font-medium text-white bg-green-700 rounded-lg border border-green-700 hover:bg-green-800 focus:ring-4 focus:outline-none focus:ring-green-300">
                        <svg class="w-4 h-4 me-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
                        </svg>Search
                    </button>
                </form>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <x-explore :user="$user" :followedTrails="$followedTrails" :followingCount="$followingCount" />
            </div>
        </div>
    </div>

    <script>
        function trailExplorer() {
            return {
                locations: [],
                trails: [],
                filteredTrails: [],
                selectedLocation: '',
                selectedTrail: null,
                loading: false,
                searchQuery: '',
                searchTimeout: null,
                sortBy: 'name',
                filters: {
                    difficulty: '',
                    season: '',
                    priceRange: ''
                },

                async init() {
                    await this.fetchLocations();
                    await this.fetchTrails();
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

                getValidLocations() {
                    return this.locations.filter(location => location.name && location.slug);
                },

                async fetchTrails() {
                    this.loading = true;
                    this.selectedTrail = null;

                    try {
                        const params = new URLSearchParams();
                        if (this.selectedLocation) {
                            params.append('location', this.selectedLocation);
                        }
                        if (this.filters.difficulty) {
                            params.append('difficulty', this.filters.difficulty);
                        }
                        if (this.searchQuery) {
                            params.append('search', this.searchQuery);
                        }

                        const response = await fetch(`/api/trails?${params}`);
                        if (response.ok) {
                            this.trails = await response.json();
                            this.applyFiltersAndSort();
                        } else {
                            throw new Error('Failed to fetch trails');
                        }
                    } catch (error) {
                        console.error('Error fetching trails:', error);
                        this.showError('Failed to load trails');
                        this.trails = [];
                        this.filteredTrails = [];
                    } finally {
                        this.loading = false;
                    }
                },

                debounceSearch() {
                    if (this.searchTimeout) {
                        clearTimeout(this.searchTimeout);
                    }
                    this.searchTimeout = setTimeout(() => {
                        this.fetchTrails();
                    }, 300);
                },

                applyFiltersAndSort() {
                    let filtered = [...this.trails];

                    // Apply season filter
                    if (this.filters.season) {
                        filtered = filtered.filter(trail => {
                            if (!trail.best_season) return this.filters.season === 'year-round';
                            return trail.best_season.toLowerCase().includes(this.filters.season);
                        });
                    }

                    // Sort trails
                    this.sortTrails(filtered);
                    this.filteredTrails = filtered;
                },

                sortTrails(trails = null) {
                    const trailsToSort = trails || this.filteredTrails;

                    trailsToSort.sort((a, b) => {
                        switch (this.sortBy) {
                            case 'name':
                                return a.name.localeCompare(b.name);
                            case 'difficulty':
                                const difficultyOrder = {
                                    'beginner': 1,
                                    'intermediate': 2,
                                    'advanced': 3
                                };
                                return (difficultyOrder[a.difficulty] || 4) - (difficultyOrder[b.difficulty] || 4);
                            case 'length':
                                return parseFloat(a.length) - parseFloat(b.length);
                            case 'rating':
                                return parseFloat(b.average_rating) - parseFloat(a.average_rating);
                            case 'price':
                                return parseFloat(a.price) - parseFloat(b.price);
                            default:
                                return 0;
                        }
                    });

                    if (!trails) {
                        this.filteredTrails = trailsToSort;
                    }
                },

                resetFilters() {
                    this.selectedLocation = '';
                    this.searchQuery = '';
                    this.filters = {
                        difficulty: '',
                        season: '',
                        priceRange: ''
                    };
                    this.sortBy = 'name';
                    this.fetchTrails();
                },

                getDifficultyBadgeClass(difficulty) {
                    const colors = {
                        'beginner': 'bg-green-500',
                        'intermediate': 'bg-yellow-500',
                        'advanced': 'bg-red-500'
                    };
                    return colors[difficulty] || 'bg-gray-500';
                },

                getDifficultyLabel(difficulty) {
                    const labels = {
                        'beginner': 'Beginner',
                        'intermediate': 'Intermediate',
                        'advanced': 'Advanced'
                    };
                    return labels[difficulty] || difficulty;
                },

                formatPrice(price) {
                    if (!price || price === 0) return 'Free';
                    return new Intl.NumberFormat('en-PH', {
                        style: 'currency',
                        currency: 'PHP'
                    }).format(price);
                },

                getTrailImage(trail) {
                    return trail.primary_image || '/img/default-trail.jpg';
                },

                showTrailDetails(trail) {
                    this.viewFullTrail(trail);
                },

                viewFullTrail(trail) {
                    if (trail && trail.slug) {
                        window.location.href = `/trails/${trail.slug}`;
                    }
                },

                async downloadTrail(trail) {
                    if (!trail) return;

                    try {
                        if (trail.gpx_file) {
                            window.open(trail.gpx_file, '_blank');
                        } else {
                            // Try to get full trail details
                            const response = await fetch(`/api/trails/${trail.slug}`);
                            if (response.ok) {
                                const trailDetails = await response.json();
                                if (trailDetails.gpx_file) {
                                    window.open(trailDetails.gpx_file, '_blank');
                                } else {
                                    this.showError('No GPX file available for this trail');
                                }
                            } else {
                                this.showError('No GPX file available for this trail');
                            }
                        }
                    } catch (error) {
                        console.error('Error downloading trail:', error);
                        this.showError('Failed to download trail file');
                    }
                },

                showError(message) {
                    // Create a simple toast notification
                    const toast = document.createElement('div');
                    toast.className = 'fixed top-4 right-4 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg z-50';
                    toast.textContent = message;
                    document.body.appendChild(toast);

                    setTimeout(() => {
                        toast.remove();
                    }, 5000);
                }
            }
        }
    </script>
</x-app-layout>
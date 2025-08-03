<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Explore') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <x-explore />
            </div>
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
</x-app-layout>
<x-app-layout>
    @php
        // Initialize the TrailImageService for dynamic images
        $imageService = app('App\Services\TrailImageService');
    @endphp

    <div class="py-6">
        <!-- Header Section -->
        <div class="bg-white shadow-lg border-b border-gray-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-4xl font-bold text-gray-900 mb-2">Community</h1>
                        <p class="text-lg text-gray-600">Discover hiking organizations and connect with fellow adventurers</p>
                    </div>
                    <div class="hidden md:flex items-center space-x-4 bg-emerald-50 px-6 py-4 rounded-xl">
                        <div class="text-center">
                            <div class="text-2xl font-bold text-emerald-600" id="following-count">{{ count($followingIds ?? []) }}</div>
                            <div class="text-sm text-gray-600">Following</div>
                        </div>
                        <div class="w-px h-12 bg-gray-300"></div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-emerald-600" id="trails-count">{{ $followedTrails ? $followedTrails->count() : 0 }}</div>
                            <div class="text-sm text-gray-600">New Trails</div>
                        </div>
                        <div class="w-px h-12 bg-gray-300"></div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-emerald-600" id="total-trails-count">{{ $totalTrails ?? 0 }}</div>
                            <div class="text-sm text-gray-600">Total Trails</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <!-- Navigation Tabs -->
            <div class="mb-8">
                <nav class="flex space-x-8" aria-label="Tabs">
                    <button id="tab-discover" class="tab-button active px-6 py-3 text-sm font-medium rounded-full transition-all duration-200 bg-emerald-600 text-white shadow-lg">
                        Discover Organizations
                    </button>
                    <button id="tab-following" class="tab-button px-6 py-3 text-sm font-medium rounded-full transition-all duration-200 bg-white text-gray-600 hover:bg-gray-50 shadow-md">
                        Following ({{ count($followingIds ?? []) }})
                    </button>
                    <button id="tab-trails" class="tab-button px-6 py-3 text-sm font-medium rounded-full transition-all duration-200 bg-white text-gray-600 hover:bg-gray-50 shadow-md">
                        Latest Trails
                    </button>
                </nav>
            </div>

            <!-- Discover Organizations Tab -->
            <div id="content-discover" class="tab-content">
                <div class="mb-6">
                    <h2 class="text-2xl font-semibold text-gray-900 mb-4">Approved Organizations</h2>
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-blue-700">
                                    Follow organizations to see their trails and submit reviews for trails from organizations you follow.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                
                @if($organizations->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($organizations as $organization)
                            <div class="organization-card bg-white rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
                                <div class="relative">
                                    <img src="{{ $organization->profile_picture_url }}" alt="{{ $organization->display_name }}" 
                                         class="w-full h-48 object-cover rounded-t-xl">
                                    <div class="absolute top-4 right-4">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            Verified
                                        </span>
                                    </div>
                                </div>
                                
                                <div class="p-6">
                                    <div class="flex items-center justify-between mb-3">
                                        <h3 class="text-xl font-semibold text-gray-900 truncate">{{ $organization->display_name }}</h3>
                                    </div>
                                    
                                    @if($organization->bio)
                                        <p class="text-gray-600 text-sm mb-4 line-clamp-3">{{ Str::limit($organization->bio, 100) }}</p>
                                    @endif
                                    
                                    <div class="flex items-center justify-between text-sm text-gray-500 mb-4">
                                        <span class="flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"></path>
                                            </svg>
                                            {{ $organization->location ?? 'Location not set' }}
                                        </span>
                                    </div>
                                    
                                    <div class="flex items-center justify-between text-sm text-gray-500 mb-6">
                                        <span>{{ $organization->followers_count ?? 0 }} follower{{ ($organization->followers_count ?? 0) !== 1 ? 's' : '' }}</span>
                                        <span>{{ $organization->organizationTrails ? $organization->organizationTrails->where('is_active', true)->count() : 0 }} trails</span>
                                    </div>
                                    
                                    <button class="follow-btn w-full py-2 px-4 rounded-lg font-medium transition-all duration-200 
                                        {{ in_array($organization->id, $followingIds) ? 'bg-gray-200 text-gray-700 hover:bg-gray-300' : 'bg-emerald-600 text-white hover:bg-emerald-700' }}"
                                        data-organization-id="{{ $organization->id }}"
                                        data-organization-name="{{ $organization->display_name }}">
                                        @if(in_array($organization->id, $followingIds))
                                            <span class="flex items-center justify-center">
                                                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                                </svg>
                                                Following
                                            </span>
                                        @else
                                            <span class="flex items-center justify-center">
                                                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd"></path>
                                                </svg>
                                                Follow
                                            </span>
                                        @endif
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 48 48">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M34 40h10v-4a6 6 0 00-10.712-3.714M34 40H14m20 0v-4a9.971 9.971 0 00-.712-3.714M14 40H4v-4a6 6 0 0110.712-3.714M14 40v-4a9.971 9.971 0 01.712-3.714m0 0A9.971 9.971 0 0118 32a9.971 9.971 0 013.288 4.286M30 20a6 6 0 11-12 0 6 6 0 0112 0zm12 0a6 6 0 11-12 0 6 6 0 0112 0zm-12 0a6 6 0 11-12 0 6 6 0 0112 0z"></path>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No organizations found</h3>
                        <p class="mt-1 text-sm text-gray-500">There are currently no approved organizations available.</p>
                    </div>
                @endif
            </div>

            <!-- Following Tab -->
            <div id="content-following" class="tab-content hidden">
                <div class="mb-6">
                    <h2 class="text-2xl font-semibold text-gray-900 mb-4">Organizations You Follow</h2>
                </div>
                
                <div id="following-organizations">
                    <!-- Content loaded via AJAX -->
                    <div class="text-center py-8">
                        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-emerald-600 mx-auto"></div>
                        <p class="mt-2 text-gray-600">Loading...</p>
                    </div>
                </div>
            </div>

            <!-- Latest Trails Tab -->
            <div id="content-trails" class="tab-content hidden">
                <div class="mb-6">
                    <h2 class="text-2xl font-semibold text-gray-900 mb-4">Latest Trails from Organizations You Follow</h2>
                </div>
                
                @if($followedTrails && $followedTrails->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" id="followed-trails">
                        @foreach($followedTrails as $trail)
                            <div class="bg-white rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
                                <div class="relative">
                                    @php
                                        // Get dynamic image from TrailImageService
                                        $trailImage = $imageService->getTrailImage($trail, 'primary', 'medium');
                                    @endphp
                                    <img src="{{ $trailImage }}" 
                                         alt="{{ $trail->trail_name }}" 
                                         class="w-full h-48 object-cover rounded-t-xl">
                                    <div class="absolute top-4 right-4">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $trail->difficulty === 'easy' ? 'bg-green-100 text-green-800' : ($trail->difficulty === 'moderate' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                            {{ ucfirst($trail->difficulty) }}
                                        </span>
                                    </div>
                                </div>
                                
                                <div class="p-6">
                                    <div class="flex items-center justify-between mb-2">
                                        <h3 class="text-lg font-semibold text-gray-900 truncate">{{ $trail->trail_name }}</h3>
                                    </div>
                                    
                                                                         <p class="text-sm text-gray-600 mb-2">by {{ $trail->user->display_name }}</p>
                                    
                                    <div class="flex items-center text-sm text-gray-500 mb-4">
                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"></path>
                                        </svg>
                                        {{ $trail->location->name ?? 'Location not set' }}
                                    </div>
                                    
                                    <div class="flex items-center justify-between text-sm text-gray-500">
                                        <div class="flex items-center">
                                            <svg class="w-4 h-4 mr-1 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                            </svg>
                                            {{ number_format($trail->average_rating, 1) }} ({{ $trail->total_reviews }})
                                        </div>
                                        <span>₱{{ number_format($trail->price, 0) }}</span>
                                    </div>
                                    
                                    <div class="mt-4">
                                        <a href="{{ route('trails.show', $trail->slug) }}" 
                                           class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-emerald-600 hover:bg-emerald-700 transition-colors duration-200">
                                            View Trail
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 48 48">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5l7-7 7 7M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No trails available</h3>
                        <p class="mt-1 text-sm text-gray-500">Follow some organizations to see their latest trails here.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Success Toast -->
    <div id="success-toast" class="fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg transform translate-x-full transition-transform duration-300 z-50">
        <div class="flex items-center">
            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
            </svg>
            <span id="success-message"></span>
        </div>
    </div>

    <!-- Error Toast -->
    <div id="error-toast" class="fixed top-4 right-4 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg transform translate-x-full transition-transform duration-300 z-50">
        <div class="flex items-center">
            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
            </svg>
            <span id="error-message"></span>
        </div>
    </div>

    @push('scripts')
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Tab switching functionality
        const tabButtons = document.querySelectorAll('.tab-button');
        const tabContents = document.querySelectorAll('.tab-content');
        
        tabButtons.forEach(button => {
            button.addEventListener('click', () => {
                const tabId = button.id.replace('tab-', '');
                
                // Update button states
                tabButtons.forEach(btn => {
                    btn.classList.remove('active', 'bg-emerald-600', 'text-white', 'shadow-lg');
                    btn.classList.add('bg-white', 'text-gray-600', 'shadow-md');
                });
                
                button.classList.add('active', 'bg-emerald-600', 'text-white', 'shadow-lg');
                button.classList.remove('bg-white', 'text-gray-600', 'shadow-md');
                
                // Update content visibility
                tabContents.forEach(content => {
                    content.classList.add('hidden');
                });
                
                const activeContent = document.getElementById('content-' + tabId);
                if (activeContent) {
                    activeContent.classList.remove('hidden');
                    
                    // Load content for following tab if not loaded yet
                    if (tabId === 'following' && !activeContent.dataset.loaded) {
                        loadFollowingOrganizations();
                        activeContent.dataset.loaded = 'true';
                    }
                }
            });
        });
        
        // Follow/Unfollow functionality
        const followButtons = document.querySelectorAll('.follow-btn');
        followButtons.forEach(button => {
            button.addEventListener('click', handleFollowClick);
        });
        
        function handleFollowClick(e) {
            const button = e.currentTarget;
            const organizationId = button.dataset.organizationId;
            const organizationName = button.dataset.organizationName;
            const isFollowing = button.textContent.trim().includes('Following');
            
            // Disable button during request
            button.disabled = true;
            
            const url = isFollowing ? '{{ route("api.community.unfollow") }}' : '{{ route("api.community.follow") }}';
            
            fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({
                    organization_id: organizationId
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update button appearance
                    updateFollowButton(button, data.is_following);
                    
                    // Show success message
                    showToast('success', data.message);
                    
                    // Update counters
                    updateFollowingCount();
                    
                    // Update follower count in the card (if available)
                    const card = button.closest('.organization-card');
                    const followerSpan = card.querySelector('.text-gray-500');
                    if (followerSpan && followerSpan.textContent.includes('follower')) {
                        followerSpan.textContent = `${data.follower_count} follower${data.follower_count !== 1 ? 's' : ''}`;
                    }
                    
                } else {
                    showToast('error', data.message || 'An error occurred');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('error', 'An error occurred. Please try again.');
            })
            .finally(() => {
                button.disabled = false;
            });
        }
        
        function updateFollowButton(button, isFollowing) {
            if (isFollowing) {
                button.className = 'follow-btn w-full py-2 px-4 rounded-lg font-medium transition-all duration-200 bg-gray-200 text-gray-700 hover:bg-gray-300';
                button.innerHTML = `
                    <span class="flex items-center justify-center">
                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        Following
                    </span>
                `;
            } else {
                button.className = 'follow-btn w-full py-2 px-4 rounded-lg font-medium transition-all duration-200 bg-emerald-600 text-white hover:bg-emerald-700';
                button.innerHTML = `
                    <span class="flex items-center justify-center">
                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd"></path>
                        </svg>
                        Follow
                    </span>
                `;
            }
        }
        
        function updateFollowingCount() {
            // Count current following buttons
            const followingButtons = document.querySelectorAll('.follow-btn:not([disabled])');
            let followingCount = 0;
            
            followingButtons.forEach(button => {
                if (button.textContent.includes('Following')) {
                    followingCount++;
                }
            });
            
            // Update count displays
            const countElement = document.getElementById('following-count');
            if (countElement) {
                countElement.textContent = followingCount;
            }
            
            // Update tab text
            const followingTab = document.getElementById('tab-following');
            if (followingTab) {
                followingTab.textContent = `Following (${followingCount})`;
            }
        }
        
        function loadFollowingOrganizations() {
            // This would load the organizations the user is following
            // For now, we'll filter from existing organizations
            const followingContainer = document.getElementById('following-organizations');
            const followingCards = document.querySelectorAll('.organization-card').forEach(card => {
                const followBtn = card.querySelector('.follow-btn');
                if (followBtn && followBtn.textContent.includes('Following')) {
                    // Clone and add to following container
                    const clonedCard = card.cloneNode(true);
                    followingContainer.appendChild(clonedCard);
                }
            });
        }
        
        function showToast(type, message) {
            const toast = document.getElementById(type + '-toast');
            const messageSpan = document.getElementById(type + '-message');
            
            messageSpan.textContent = message;
            toast.classList.remove('translate-x-full');
            
            setTimeout(() => {
                toast.classList.add('translate-x-full');
            }, 3000);
        }
    });
    </script>
    @endpush
</x-app-layout>

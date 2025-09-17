<x-app-layout>
    @php
        // Initialize the TrailImageService for dynamic images
        $imageService = app('App\Services\TrailImageService');
    @endphp
    <div>
        <!-- Header Section -->
        <div class="bg-white shadow-lg border-b border-gray-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
                <!-- Back Button -->
                <div>
                    <a href="{{ route('community.index') }}" class="inline-flex items-center text-emerald-600 hover:text-emerald-700 transition-colors duration-200">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Back to Community
                    </a>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Organization Profile Section -->
            <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
                <!-- Organization Basic Info -->
                <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
                    <div class="flex items-center space-x-6">
                        <div class="flex-shrink-0">
                            <img src="{{ $organization->profile_picture_url }}" 
                                 alt="{{ $organization->display_name }}" 
                                 class="w-24 h-24 rounded-full object-cover border-4 border-white shadow-lg">
                        </div>
                        <div>
                            <div class="flex items-center space-x-3 mb-2">
                                <h1 class="text-4xl font-bold text-gray-900">{{ $organization->display_name }}</h1>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.238.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                    </svg>
                                    Verified Organization
                                </span>
                            </div>
                            @if($organization->bio)
                                <p class="text-lg text-gray-600 mb-3">{{ $organization->bio }}</p>
                            @endif
                            @if($organization->location)
                                <div class="flex items-center text-gray-500">
                                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"></path>
                                    </svg>
                                    {{ $organization->location }}
                                </div>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Action Buttons and Stats -->
                    <div class="mt-6 md:mt-0 flex flex-col items-start md:items-end space-y-4">
                        <!-- Follow Button -->
                        <button class="follow-btn px-8 py-3 rounded-lg font-medium transition-all duration-200 
                            {{ $isFollowing ? 'bg-gray-200 text-gray-700 hover:bg-gray-300' : 'bg-emerald-600 text-white hover:bg-emerald-700' }}"
                            data-organization-id="{{ $organization->id }}"
                            data-organization-name="{{ $organization->display_name }}">
                            @if($isFollowing)
                                <span class="flex items-center">
                                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    Following
                                </span>
                            @else
                                <span class="flex items-center">
                                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd"></path>
                                    </svg>
                                    Follow
                                </span>
                            @endif
                        </button>

                        <!-- Stats -->
                        <div class="flex items-center space-x-6 bg-emerald-50 px-6 py-3 rounded-xl">
                            <div class="text-center">
                                <div class="text-2xl font-bold text-emerald-600" id="followers-count">{{ $organization->followers_count ?? 0 }}</div>
                                <div class="text-sm text-gray-600">Followers</div>
                            </div>
                            <div class="w-px h-8 bg-gray-300"></div>
                            <div class="text-center">
                                <div class="text-2xl font-bold text-emerald-600">{{ $trails->total() }}</div>
                                <div class="text-sm text-gray-600">Trails</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Organization Details Section -->
                @if($organization->organizationProfile)
                    <div class="border-t pt-6">
                        <h2 class="text-2xl font-semibold text-gray-900 mb-4">About {{ $organization->display_name }}</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            @if($organization->organizationProfile->description)
                                <div>
                                    <h3 class="text-lg font-medium text-gray-900 mb-2">Description</h3>
                                    <p class="text-gray-600">{{ $organization->organizationProfile->description }}</p>
                                </div>
                            @endif
                            @if($organization->organizationProfile->website)
                                <div>
                                    <h3 class="text-lg font-medium text-gray-900 mb-2">Website</h3>
                                    <a href="{{ $organization->organizationProfile->website }}" 
                                       target="_blank" 
                                       class="text-emerald-600 hover:text-emerald-700 flex items-center">
                                        {{ $organization->organizationProfile->website }}
                                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                        </svg>
                                    </a>
                                </div>
                            @endif
                            @if($organization->organizationProfile->contact_person)
                                <div>
                                    <h3 class="text-lg font-medium text-gray-900 mb-2">Contact Person</h3>
                                    <p class="text-gray-600">{{ $organization->organizationProfile->contact_person }}</p>
                                </div>
                            @endif
                            @if($organization->organizationProfile->phone_number)
                                <div>
                                    <h3 class="text-lg font-medium text-gray-900 mb-2">Phone Number</h3>
                                    <a href="tel:{{ $organization->organizationProfile->phone_number }}" 
                                       class="text-emerald-600 hover:text-emerald-700">
                                        {{ $organization->organizationProfile->phone_number }}
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
            </div>

            <!-- Trails Section -->
            <div class="mb-6">
                <h2 class="text-2xl font-semibold text-gray-900 mb-4">Trails by {{ $organization->display_name }}</h2>
            </div>

            @if($trails->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                    @foreach($trails as $trail)
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
                                <h3 class="text-lg font-semibold text-gray-900 mb-2 truncate">{{ $trail->trail_name }}</h3>
                                
                                <div class="flex items-center text-sm text-gray-500 mb-4">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"></path>
                                    </svg>
                                    {{ $trail->location->name ?? 'Location not set' }}
                                </div>
                                
                                <div class="flex items-center justify-between text-sm text-gray-500 mb-4">
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 mr-1 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                        </svg>
                                        {{ number_format($trail->average_rating, 1) }} ({{ $trail->total_reviews }})
                                    </div>
                                    <span class="font-medium">₱{{ number_format($trail->price, 0) }}</span>
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

                <!-- Pagination -->
                <div class="flex justify-center">
                    {{ $trails->links() }}
                </div>
            @else
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 48 48">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5l7-7 7 7M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No trails available</h3>
                    <p class="mt-1 text-sm text-gray-500">This organization hasn't published any trails yet.</p>
                </div>
            @endif
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
        // Follow/Unfollow functionality
        const followButton = document.querySelector('.follow-btn');
        if (followButton) {
            followButton.addEventListener('click', handleFollowClick);
        }
        
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
                    
                    // Update follower count
                    const followerCount = document.getElementById('followers-count');
                    if (followerCount && data.follower_count !== undefined) {
                        followerCount.textContent = data.follower_count;
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
                button.className = 'follow-btn px-8 py-3 rounded-lg font-medium transition-all duration-200 bg-gray-200 text-gray-700 hover:bg-gray-300';
                button.innerHTML = `
                    <span class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        Following
                    </span>
                `;
            } else {
                button.className = 'follow-btn px-8 py-3 rounded-lg font-medium transition-all duration-200 bg-emerald-600 text-white hover:bg-emerald-700';
                button.innerHTML = `
                    <span class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd"></path>
                        </svg>
                        Follow
                    </span>
                `;
            }
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
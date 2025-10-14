{{-- 
    Example Integration: org/trails/index.blade.php
    This shows how to integrate 3D trail previews into your existing trail listing page
--}}

{{-- Add this in your trail cards section --}}

@foreach($trails as $trail)
    <div class="trail-card-container mb-6">
        {{-- Option 1: Replace existing trail card with 3D preview --}}
        <x-trail-3d-preview 
            :trail="$trail" 
            mode="thumbnail"
        />
        
        {{-- Option 2: Add 3D view alongside existing card --}}
        {{-- 
        <div class="grid md:grid-cols-2 gap-4">
            <!-- Your existing trail card -->
            <div class="trail-card">
                <img src="{{ $trail->featured_image }}" alt="{{ $trail->trail_name }}">
                <h3>{{ $trail->trail_name }}</h3>
                <!-- etc -->
            </div>
            
            <!-- New 3D preview -->
            <x-trail-3d-preview 
                :trail="$trail" 
                mode="thumbnail"
            />
        </div>
        --}}
        
        {{-- Trail info section (below 3D preview) --}}
        <div class="mt-4 p-4 bg-white rounded-lg shadow">
            <div class="flex justify-between items-start mb-2">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">
                        {{ $trail->trail_name }}
                    </h3>
                    <p class="text-sm text-gray-600">{{ $trail->mountain_name }}</p>
                </div>
                
                <span class="px-3 py-1 rounded-full text-xs font-semibold
                    {{ $trail->difficulty === 'beginner' ? 'bg-green-100 text-green-800' : '' }}
                    {{ $trail->difficulty === 'intermediate' ? 'bg-yellow-100 text-yellow-800' : '' }}
                    {{ $trail->difficulty === 'advanced' ? 'bg-red-100 text-red-800' : '' }}">
                    {{ ucfirst($trail->difficulty) }}
                </span>
            </div>
            
            <div class="flex items-center gap-4 text-sm text-gray-600 mb-3">
                @if($trail->distance)
                <div class="flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                    </svg>
                    {{ number_format($trail->distance, 2) }} km
                </div>
                @endif
                
                @if($trail->price)
                <div class="flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                    </svg>
                    ₱{{ number_format($trail->price, 2) }}
                </div>
                @endif
            </div>
            
            <div class="flex gap-2">
                <a href="{{ route('trails.show', $trail->slug) }}" 
                   class="flex-1 bg-[#336d66] hover:bg-[#2a5a54] text-white text-center py-2 px-4 rounded-lg font-semibold transition-colors">
                    View Details
                </a>
                
                @auth
                    @if(auth()->user()->user_type === 'hiker')
                    <a href="{{ route('trails.show', $trail->slug) }}#booking" 
                       class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-900 text-center py-2 px-4 rounded-lg font-semibold transition-colors">
                        Book Trail
                    </a>
                    @endif
                @endauth
            </div>
        </div>
    </div>
@endforeach

{{-- Alternative: Grid Layout with 3D Previews --}}
{{--
<div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
    @foreach($trails as $trail)
        <div class="trail-grid-item">
            <x-trail-3d-preview 
                :trail="$trail" 
                mode="thumbnail"
            />
            
            <div class="mt-3">
                <h3 class="font-semibold text-gray-900">{{ $trail->trail_name }}</h3>
                <p class="text-sm text-gray-600">{{ $trail->mountain_name }}</p>
                
                <div class="mt-2 flex justify-between items-center">
                    <span class="text-sm font-semibold text-[#336d66]">
                        ₱{{ number_format($trail->price, 2) }}
                    </span>
                    <a href="{{ route('trails.show', $trail->slug) }}" 
                       class="text-sm text-[#336d66] hover:underline font-medium">
                        View Details →
                    </a>
                </div>
            </div>
        </div>
    @endforeach
</div>
--}}

@push('scripts')
<script>
// Optional: Add custom behavior for 3D previews
document.addEventListener('DOMContentLoaded', function() {
    // Track 3D preview interactions
    document.querySelectorAll('[data-trail-3d-map]').forEach(preview => {
        preview.addEventListener('click', function() {
            const trailId = this.dataset.trailId;
            console.log('3D preview clicked:', trailId);
            
            // Analytics tracking (optional)
            // gtag('event', '3d_preview_view', { trail_id: trailId });
        });
    });
    
    // Add loading indicator observer
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
            }
        });
    }, { threshold: 0.1 });
    
    document.querySelectorAll('.trail-3d-preview').forEach(el => {
        observer.observe(el);
    });
});
</script>
@endpush

@push('styles')
<style>
/* Optional: Add stagger animation for 3D previews loading */
.trail-3d-preview {
    opacity: 0;
    transform: translateY(20px);
    transition: opacity 0.5s, transform 0.5s;
}

.trail-3d-preview.visible {
    opacity: 1;
    transform: translateY(0);
}

/* Stagger delay for grid items */
.trail-grid-item:nth-child(1) .trail-3d-preview { transition-delay: 0s; }
.trail-grid-item:nth-child(2) .trail-3d-preview { transition-delay: 0.1s; }
.trail-grid-item:nth-child(3) .trail-3d-preview { transition-delay: 0.2s; }
.trail-grid-item:nth-child(4) .trail-3d-preview { transition-delay: 0.3s; }
.trail-grid-item:nth-child(5) .trail-3d-preview { transition-delay: 0.4s; }
.trail-grid-item:nth-child(6) .trail-3d-preview { transition-delay: 0.5s; }
</style>
@endpush

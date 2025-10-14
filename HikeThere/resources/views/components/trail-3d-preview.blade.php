{{-- 
    3D Trail Preview Component
    Displays interactive 3D terrain visualization for trails
    
    Usage:
    <x-trail-3d-preview :trail="$trail" mode="thumbnail" />
    <x-trail-3d-preview :trail="$trail" mode="full" />
    
    Props:
    - trail: Trail model instance
    - mode: 'thumbnail' (small preview) or 'full' (full viewer)
    - autoTour: Enable auto-rotation (default: false)
    - showControls: Show 3D controls (default: true)
--}}

@props([
    'trail',
    'mode' => 'thumbnail', 
    'autoTour' => false,
    'showControls' => true,
    'mapId' => config('services.google.maps_3d_id', null)
])

@php
    $uniqueId = 'trail-3d-' . $trail->id . '-' . uniqid();
    $isThumbnail = $mode === 'thumbnail';
    $height = $isThumbnail ? '300px' : '600px';
    $coordinates = $trail->path_coordinates ?? $trail->coordinates ?? [];
@endphp

<div class="trail-3d-preview {{ $isThumbnail ? 'thumbnail-mode' : 'full-mode' }}" data-trail-id="{{ $trail->id }}">
    {{-- Map Container --}}
    <div 
        id="{{ $uniqueId }}"
        class="trail-3d-map-container"
        data-trail-3d-map
        data-trail-id="{{ $trail->id }}"
        data-enable-3d="true"
        data-auto-tour="{{ $autoTour ? 'true' : 'false' }}"
        data-map-id="{{ $mapId }}"
        style="width: 100%; height: {{ $height }}; border-radius: 8px; overflow: hidden; position: relative;"
    >
        {{-- Loading State --}}
        <div class="loading-overlay" style="position: absolute; inset: 0; background: linear-gradient(135deg, #336d66 0%, #2a5a54 100%); display: flex; align-items: center; justify-content: center; z-index: 10;">
            <div style="text-align: center; color: white;">
                <svg class="animate-spin" style="width: 48px; height: 48px; margin: 0 auto 16px;" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <div style="font-weight: 600; font-size: 16px; margin-bottom: 4px;">Loading 3D Terrain</div>
                <div style="font-size: 12px; opacity: 0.8;">{{ $trail->trail_name }}</div>
            </div>
        </div>
    </div>
    
    @if($isThumbnail)
        {{-- Thumbnail Overlay Info --}}
        <div class="thumbnail-overlay" style="position: absolute; bottom: 0; left: 0; right: 0; background: linear-gradient(to top, rgba(0,0,0,0.8), transparent); padding: 16px 12px 12px; pointer-events: none;">
            <div style="color: white;">
                <h4 style="font-weight: 600; font-size: 14px; margin-bottom: 4px;">{{ $trail->trail_name }}</h4>
                <p style="font-size: 12px; opacity: 0.9;">{{ $trail->mountain_name }}</p>
            </div>
            
            {{-- 3D Badge --}}
            <div style="position: absolute; top: -40px; right: 12px; background: rgba(255,255,255,0.95); padding: 6px 12px; border-radius: 20px; font-size: 11px; font-weight: 700; color: #336d66; display: flex; align-items: center; gap: 4px;">
                <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10l-2 1m0 0l-2-1m2 1v2.5M20 7l-2 1m2-1l-2-1m2 1v2.5M14 4l-2-1-2 1M4 7l2-1M4 7l2 1M4 7v2.5M12 21l-2-1m2 1l2-1m-2 1v-2.5M6 18l-2-1v-2.5M18 18l2-1v-2.5" />
                </svg>
                3D View
            </div>
        </div>
    @endif
    
    @if(!$isThumbnail && $showControls)
        {{-- Full Viewer Controls --}}
        <div class="trail-3d-info-panel" style="position: absolute; top: 16px; right: 16px; background: white; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); padding: 16px; max-width: 300px; z-index: 100;">
            <h3 style="font-weight: 700; font-size: 16px; color: #1f2937; margin-bottom: 8px;">
                {{ $trail->trail_name }}
            </h3>
            
            <div style="display: flex; flex-direction: column; gap: 8px; font-size: 13px; color: #6b7280;">
                <div style="display: flex; align-items: center; gap: 8px;">
                    <svg style="width: 16px; height: 16px; color: #336d66;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <span>{{ $trail->mountain_name }}</span>
                </div>
                
                @if($trail->distance)
                <div style="display: flex; align-items: center; gap: 8px;">
                    <svg style="width: 16px; height: 16px; color: #336d66;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                    </svg>
                    <span>{{ number_format($trail->distance, 2) }} km</span>
                </div>
                @endif
                
                @if($trail->difficulty)
                <div style="display: flex; align-items: center; gap: 8px;">
                    <svg style="width: 16px; height: 16px; color: #336d66;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                    <span class="capitalize">{{ $trail->difficulty }}</span>
                </div>
                @endif
            </div>
            
            {{-- Action Buttons --}}
            <div style="margin-top: 16px; display: flex; gap: 8px;">
                <a href="{{ route('trails.show', $trail->slug) }}" 
                   class="btn-primary"
                   style="flex: 1; background: #336d66; color: white; text-align: center; padding: 8px 12px; border-radius: 6px; text-decoration: none; font-size: 13px; font-weight: 600; transition: background 0.3s;"
                   onmouseover="this.style.background='#2a5a54'"
                   onmouseout="this.style.background='#336d66'">
                    View Details
                </a>
                
                @auth
                    @if(auth()->user()->user_type === 'hiker')
                    <a href="{{ route('trails.show', $trail->slug) }}#booking" 
                       class="btn-secondary"
                       style="flex: 1; background: #f3f4f6; color: #1f2937; text-align: center; padding: 8px 12px; border-radius: 6px; text-decoration: none; font-size: 13px; font-weight: 600; transition: background 0.3s;"
                       onmouseover="this.style.background='#e5e7eb'"
                       onmouseout="this.style.background='#f3f4f6'">
                        Book Now
                    </a>
                    @endif
                @endauth
            </div>
        </div>
    @endif
</div>

@once
    @push('styles')
    <style>
        .trail-3d-preview {
            position: relative;
            border-radius: 8px;
            overflow: hidden;
        }
        
        .trail-3d-preview.thumbnail-mode {
            cursor: pointer;
            transition: transform 0.3s, box-shadow 0.3s;
        }
        
        .trail-3d-preview.thumbnail-mode:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 24px rgba(0,0,0,0.15);
        }
        
        .trail-3d-preview.thumbnail-mode:hover .thumbnail-overlay {
            background: linear-gradient(to top, rgba(51,109,102,0.9), transparent);
        }
        
        .trail-3d-map-container {
            transition: opacity 0.3s;
        }
        
        .trail-3d-map-container .loading-overlay {
            transition: opacity 0.3s;
        }
        
        .trail-3d-map-container.loaded .loading-overlay {
            opacity: 0;
            pointer-events: none;
        }
        
        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
        
        .animate-spin {
            animation: spin 1s linear infinite;
        }
        
        /* Mobile responsive */
        @media (max-width: 640px) {
            .trail-3d-info-panel {
                position: static !important;
                margin-top: 12px;
                max-width: 100% !important;
            }
        }
    </style>
    @endpush
    
    @push('scripts')
    <script>
        // Hide loading overlay when map loads
        document.addEventListener('DOMContentLoaded', function() {
            // Wait for maps to initialize
            setTimeout(() => {
                document.querySelectorAll('.trail-3d-map-container').forEach(container => {
                    container.classList.add('loaded');
                });
            }, 2000);
        });
        
        // Thumbnail click handler
        document.querySelectorAll('.trail-3d-preview.thumbnail-mode').forEach(preview => {
            preview.addEventListener('click', function(e) {
                // Don't trigger if clicking on a link
                if (e.target.tagName === 'A') return;
                
                const trailId = this.dataset.trailId;
                // Open 3D viewer modal or navigate to trail page
                window.open(`/trails/${trailId}/3d-view`, '_blank');
            });
        });
    </script>
    @endpush
@endonce

{{-- Pass trail data to JavaScript --}}
<script>
    // Store trail data for 3D map initialization
    window.trail3DData = window.trail3DData || {};
    window.trail3DData['{{ $uniqueId }}'] = {
        id: {{ $trail->id }},
        name: "{{ $trail->trail_name }}",
        mountain: "{{ $trail->mountain_name }}",
        coordinates: @json($coordinates),
        distance: {{ $trail->distance ?? 0 }},
        difficulty: "{{ $trail->difficulty ?? 'beginner' }}"
    };
</script>

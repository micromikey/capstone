<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>3D Trail Viewer Demo - HikeThere</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .demo-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 1.5rem;
        }
        
        @media (max-width: 640px) {
            .demo-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body class="bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 py-12">
        <!-- Header -->
        <div class="text-center mb-12">
            <h1 class="text-4xl font-bold text-gray-900 mb-4">
                🏔️ 3D Trail Visualization Demo
            </h1>
            <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                Experience trails in immersive 3D with photorealistic terrain, interactive controls, and Street View integration.
            </p>
            
            <!-- Setup Status -->
            <div class="mt-6 inline-flex items-center gap-2 px-4 py-2 bg-yellow-50 border border-yellow-200 rounded-lg">
                <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <span class="text-sm font-medium text-yellow-800">
                    Setup Required: Please follow <a href="/MAP_TILES_QUICK_START.md" class="underline">Quick Start Guide</a>
                </span>
            </div>
        </div>
        
        <!-- Features Grid -->
        <div class="mb-12">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Key Features</h2>
            <div class="grid md:grid-cols-3 gap-6">
                <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
                    <div class="text-3xl mb-3">🗺️</div>
                    <h3 class="font-semibold text-gray-900 mb-2">Photorealistic 3D</h3>
                    <p class="text-sm text-gray-600">Real terrain with actual imagery, lighting, and shadows for authentic mountain visualization.</p>
                </div>
                
                <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
                    <div class="text-3xl mb-3">🎮</div>
                    <h3 class="font-semibold text-gray-900 mb-2">Interactive Controls</h3>
                    <p class="text-sm text-gray-600">Rotate, tilt, zoom, and auto-tour features for exploring trails from every angle.</p>
                </div>
                
                <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
                    <div class="text-3xl mb-3">📍</div>
                    <h3 class="font-semibold text-gray-900 mb-2">Trail Overlays</h3>
                    <p class="text-sm text-gray-600">Trail paths rendered on 3D terrain with start/end markers and waypoints.</p>
                </div>
            </div>
        </div>
        
        <!-- Demo Examples -->
        <div class="mb-12">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Implementation Examples</h2>
            
            <!-- Example 1: Thumbnail Mode -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-xl font-semibold text-gray-900">Thumbnail Mode</h3>
                    <span class="px-3 py-1 bg-green-100 text-green-800 text-xs font-semibold rounded-full">Trail Cards</span>
                </div>
                <p class="text-gray-600 mb-4">Perfect for trail listing pages. Compact 3D previews with hover effects.</p>
                
                <div class="demo-grid">
                    <!-- Mock Trail Card 1 -->
                    <div class="relative bg-gray-200 rounded-lg overflow-hidden" style="height: 300px;">
                        <div class="absolute inset-0 flex items-center justify-center text-gray-400">
                            <div class="text-center">
                                <svg class="w-16 h-16 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10l-2 1m0 0l-2-1m2 1v2.5M20 7l-2 1m2-1l-2-1m2 1v2.5M14 4l-2-1-2 1M4 7l2-1M4 7l2 1M4 7v2.5M12 21l-2-1m2 1l2-1m-2 1v-2.5M6 18l-2-1v-2.5M18 18l2-1v-2.5" />
                                </svg>
                                <div class="font-semibold">Mount Pulag</div>
                                <div class="text-sm">Ambangeg Trail</div>
                            </div>
                        </div>
                        <div class="absolute top-3 right-3 bg-white/90 px-3 py-1 rounded-full text-xs font-bold text-[#336d66]">
                            3D View
                        </div>
                        <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/80 to-transparent p-4 text-white">
                            <div class="font-semibold">Ambangeg Trail</div>
                            <div class="text-sm opacity-90">Mount Pulag</div>
                        </div>
                    </div>
                    
                    <!-- Mock Trail Card 2 -->
                    <div class="relative bg-gray-200 rounded-lg overflow-hidden" style="height: 300px;">
                        <div class="absolute inset-0 flex items-center justify-center text-gray-400">
                            <div class="text-center">
                                <svg class="w-16 h-16 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10l-2 1m0 0l-2-1m2 1v2.5M20 7l-2 1m2-1l-2-1m2 1v2.5M14 4l-2-1-2 1M4 7l2-1M4 7l2 1M4 7v2.5M12 21l-2-1m2 1l2-1m-2 1v-2.5M6 18l-2-1v-2.5M18 18l2-1v-2.5" />
                                </svg>
                                <div class="font-semibold">Mount Apo</div>
                                <div class="text-sm">Main Trail</div>
                            </div>
                        </div>
                        <div class="absolute top-3 right-3 bg-white/90 px-3 py-1 rounded-full text-xs font-bold text-[#336d66]">
                            3D View
                        </div>
                        <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/80 to-transparent p-4 text-white">
                            <div class="font-semibold">Main Trail</div>
                            <div class="text-sm opacity-90">Mount Apo</div>
                        </div>
                    </div>
                </div>
                
                <div class="mt-4 bg-gray-50 border border-gray-200 rounded p-3">
                    <code class="text-xs text-gray-700">
                        &lt;x-trail-3d-preview :trail="$trail" mode="thumbnail" /&gt;
                    </code>
                </div>
            </div>
            
            <!-- Example 2: Full Viewer Mode -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-xl font-semibold text-gray-900">Full Viewer Mode</h3>
                    <span class="px-3 py-1 bg-blue-100 text-blue-800 text-xs font-semibold rounded-full">Detail Pages</span>
                </div>
                <p class="text-gray-600 mb-4">Immersive full-screen experience with complete controls and trail information.</p>
                
                <div class="relative bg-gray-200 rounded-lg overflow-hidden" style="height: 600px;">
                    <div class="absolute inset-0 flex items-center justify-center text-gray-400">
                        <div class="text-center">
                            <svg class="w-24 h-24 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                            </svg>
                            <div class="text-xl font-semibold mb-2">3D Trail Viewer</div>
                            <div class="text-sm">Interactive terrain with full controls</div>
                        </div>
                    </div>
                    
                    <!-- Mock Info Panel -->
                    <div class="absolute top-4 right-4 bg-white rounded-lg shadow-lg p-4 max-w-xs">
                        <h4 class="font-bold text-gray-900 mb-2">Ambangeg Trail</h4>
                        <div class="space-y-2 text-sm text-gray-600">
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-[#336d66]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                </svg>
                                Mount Pulag
                            </div>
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-[#336d66]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                                </svg>
                                14.6 km
                            </div>
                        </div>
                        <div class="mt-3 flex gap-2">
                            <button class="flex-1 bg-[#336d66] text-white text-xs py-2 rounded font-semibold">
                                View Details
                            </button>
                        </div>
                    </div>
                    
                    <!-- Mock Control Panel -->
                    <div class="absolute left-4 top-4 bg-white rounded-lg shadow-lg p-2 space-y-2">
                        <button class="w-full bg-[#336d66] text-white px-3 py-2 rounded text-xs font-semibold">
                            3D View
                        </button>
                        <button class="w-full bg-[#336d66] text-white px-3 py-2 rounded text-xs font-semibold">
                            🎬 Tour
                        </button>
                        <button class="w-full bg-[#336d66] text-white px-3 py-2 rounded text-xs font-semibold">
                            🔄 Reset
                        </button>
                    </div>
                </div>
                
                <div class="mt-4 bg-gray-50 border border-gray-200 rounded p-3">
                    <code class="text-xs text-gray-700">
                        &lt;x-trail-3d-preview :trail="$trail" mode="full" :autoTour="true" /&gt;
                    </code>
                </div>
            </div>
        </div>
        
        <!-- Setup Instructions -->
        <div class="bg-gradient-to-br from-[#336d66] to-[#2a5a54] rounded-lg shadow-lg p-8 text-white">
            <h2 class="text-2xl font-bold mb-4">🚀 Ready to Get Started?</h2>
            <p class="mb-6 opacity-90">Follow our comprehensive setup guide to enable 3D trail visualization in your HikeThere application.</p>
            
            <div class="flex flex-wrap gap-4">
                <a href="/MAP_TILES_QUICK_START.md" class="inline-flex items-center gap-2 bg-white text-[#336d66] px-6 py-3 rounded-lg font-semibold hover:bg-gray-100 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                    Quick Start Guide
                </a>
                
                <a href="/MAP_TILES_3D_IMPLEMENTATION.md" class="inline-flex items-center gap-2 bg-white/10 backdrop-blur px-6 py-3 rounded-lg font-semibold hover:bg-white/20 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Full Documentation
                </a>
            </div>
        </div>
        
        <!-- Technical Notes -->
        <div class="mt-12 border-t border-gray-200 pt-8">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Technical Notes</h3>
            <div class="grid md:grid-cols-2 gap-6 text-sm text-gray-600">
                <div>
                    <h4 class="font-semibold text-gray-900 mb-2">Browser Support</h4>
                    <ul class="space-y-1">
                        <li>✅ Chrome 90+</li>
                        <li>✅ Firefox 88+</li>
                        <li>✅ Safari 15+</li>
                        <li>✅ Edge 90+</li>
                    </ul>
                </div>
                
                <div>
                    <h4 class="font-semibold text-gray-900 mb-2">Requirements</h4>
                    <ul class="space-y-1">
                        <li>• WebGL 2.0 support</li>
                        <li>• Google Maps API key</li>
                        <li>• Map ID from Cloud Console</li>
                        <li>• Map Tiles API enabled</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

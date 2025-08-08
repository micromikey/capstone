<x-guest-layout>
    <!-- Navigation -->
    <nav class="nav-container nav-blur fixed w-full top-0 left-0 z-50 shadow-lg">
        <div class="max-w-7xl mx-auto px-6 flex justify-between items-center h-20">
            <a href="/" class="flex items-center space-x-3 mountain-logo group">
                <div class="relative">
                    <img src="{{ asset('img/icon1.png') }}" alt="Icon" class="h-9 w-auto">
                </div>
                <span class="font-bold text-xl text-[#336d66]">HikeThere</span>
            </a>

            <div class="hidden md:flex items-center space-x-8">
                <div class="flex space-x-8 font-medium text-gray-700">
                    <a href="#plan" class="relative nav-link hover:text-[#20b6d2] transition-all duration-300">
                        Plan Trip
                        <span class="nav-underline"></span>
                    </a>
                    <a href="#features" class="relative nav-link hover:text-[#20b6d2] transition-all duration-300">
                        Features
                        <span class="nav-underline"></span>
                    </a>
                    <a href="#community" class="relative nav-link hover:text-[#20b6d2] transition-all duration-300">
                        Community
                        <span class="nav-underline"></span>
                    </a>
                </div>
                <div class="flex items-center space-x-4 ml-8">
                    <a href="{{ route('login') }}" class="text-gray-700 hover:text-[#20b6d2] font-medium transition-colors duration-300">
                        Login
                    </a>
                    <a href="{{ route('register.select') }}" class="btn-mountain-outline">
                        Get Started
                    </a>
                </div>
            </div>

            <button id="mobile-menu-btn" class="md:hidden p-3 text-gray-600 hover:text-[#20b6d2] transition-colors duration-300">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                </svg>
            </button>
        </div>

        <!-- Mobile Menu -->
        <div id="mobile-menu" class="hidden md:hidden bg-white/95 backdrop-blur-lg border-t border-gray-200">
            <div class="px-4 py-6 space-y-4">
                <a href="#plan" class="block py-3 px-4 hover:bg-gray-50 rounded-lg transition-colors">Plan Trip</a>
                <a href="#features" class="block py-3 px-4 hover:bg-gray-50 rounded-lg transition-colors">Features</a>
                <a href="#community" class="block py-3 px-4 hover:bg-gray-50 rounded-lg transition-colors">Community</a>
                <hr class="border-gray-200 my-4">
                <a href="{{ route('login') }}" class="block py-3 px-4 text-gray-700 hover:bg-gray-50 rounded-lg transition-colors">Login</a>
                <a href="{{ route('register.select') }}" class="block w-full btn-mountain text-center">Get Started</a>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-container bg-mountain-gradient pt-32 pb-24 relative overflow-hidden">
        <div class="absolute inset-0">
            <div class="absolute top-20 left-10 w-72 h-72 bg-white/10 rounded-full blur-3xl animate-pulse"></div>
            <div class="absolute bottom-20 right-10 w-96 h-96 bg-[#20b6d2]/10 rounded-full blur-3xl animate-pulse delay-1000"></div>
        </div>

        <div class="max-w-6xl mx-auto px-6 text-center relative z-10">
            <div class="hero-content space-y-8 animate-fade-in">
                <div class="inline-flex items-center space-x-2 bg-white/90 backdrop-blur-sm rounded-full px-6 py-3 text-sm font-medium text-[#336d66] shadow-lg">
                    <span class="iconify text-[#e3a746]" data-icon="mdi:mountain" style="font-size: 1.5rem;"></span>
                    <span>Your Adventure Starts Here</span>
                </div>

                <h1 class="text-5xl md:text-7xl font-extrabold leading-tight">
                    <span class="block text-white mb-2">Explore Trails</span>
                    <span class="block text-[#e3a746]">Plan Safely</span>
                    <span class="block text-white">Hike Confidently</span>
                </h1>

                <p class="text-xl md:text-2xl text-white/90 max-w-3xl mx-auto leading-relaxed">
                    Your all-in-one hiking companion for safe, enjoyable, and well-prepared adventures in nature's most beautiful places.
                </p>

                <div class="flex flex-col sm:flex-row items-center justify-center space-y-4 sm:space-y-0 sm:space-x-6 pt-6">
                    <a href="#plan" class="btn-mountain-large group">
                        <span>Start Planning</span>
                        <svg class="w-5 h-5 ml-2 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                        </svg>
                    </a>
                    <button class="btn-video group">
                        <svg class="w-6 h-6 mr-3" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M8 5v14l11-7L8 5z" />
                        </svg>
                        <span>Watch Demo</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Scroll Indicator -->
        <div class="absolute bottom-8 left-1/2 transform -translate-x-1/2 animate-bounce">
            <svg class="w-6 h-6 text-white/70" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
            </svg>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="py-16 bg-white">
        <div class="max-w-6xl mx-auto px-6">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
                <div class="stats-item">
                    <div class="text-3xl md:text-4xl font-bold text-[#336d66] mb-2">10k+</div>
                    <div class="text-gray-600">Trails Mapped</div>
                </div>
                <div class="stats-item">
                    <div class="text-3xl md:text-4xl font-bold text-[#20b6d2] mb-2">50k+</div>
                    <div class="text-gray-600">Happy Hikers</div>
                </div>
                <div class="stats-item">
                    <div class="text-3xl md:text-4xl font-bold text-[#e3a746] mb-2">99.9%</div>
                    <div class="text-gray-600">Uptime</div>
                </div>
                <div class="stats-item">
                    <div class="text-3xl md:text-4xl font-bold text-[#dfa648] mb-2">24/7</div>
                    <div class="text-gray-600">Support</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Plan Trip Section -->
    <section id="plan" class="plan-trip-container py-20 bg-gradient-to-br from-[#aec896]/5 to-white">
        <div class="max-w-6xl mx-auto px-6">
            <div class="text-center mb-16">
                <h2 class="text-4xl md:text-5xl font-bold text-[#336d66] mb-6">Plan Your Perfect Adventure</h2>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                    Easily build your itinerary, check real-time trail conditions, and prepare for your next unforgettable hiking experience.
                </p>
            </div>

            <div class="planner-showcase p-8 md:p-12 mb-12">
                <div class="max-w-2xl mx-auto">
                    <div class="relative">
                        <input
                            type="text"
                            placeholder="Search trails, locations, or difficulty levels..."
                            class="mountain-search w-full p-4 pl-12 pr-16 text-lg rounded-2xl border-2 border-gray-200 focus:ring-4 focus:ring-[#20b6d2]/20 focus:border-[#20b6d2] transition-all duration-300 shadow-lg" />
                        <svg class="absolute left-4 top-1/2 transform -translate-y-1/2 w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        <button class="absolute right-2 top-1/2 transform -translate-y-1/2 bg-[#336d66] text-white px-6 py-2 rounded-xl hover:bg-[#20b6d2] transition-colors duration-300">
                            Search
                        </button>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-12">
                    <div class="quick-search-card">
                        <div class="w-12 h-12 bg-[#336d66]/10 rounded-xl flex items-center justify-center mb-4">
                            <span class="iconify text-blue-500" data-icon="heroicons:sparkles-solid" style="font-size:1.5rem;"></span>
                        </div>
                        <h3 class="font-semibold text-lg mb-2">Beginner Trails</h3>
                        <p class="text-gray-600 text-sm">Perfect for first-time hikers</p>
                    </div>

                    <div class="quick-search-card">
                        <div class="w-12 h-12 bg-[#20b6d2]/10 rounded-xl flex items-center justify-center mb-4">
                            <span class="iconify text-yellow-500" data-icon="heroicons:star-solid" style="font-size:1.5rem;"></span>
                        </div>
                        <h3 class="font-semibold text-lg mb-2">Popular Routes</h3>
                        <p class="text-gray-600 text-sm">Community favorites</p>
                    </div>

                    <div class="quick-search-card">
                        <div class="w-12 h-12 bg-[#e3a746]/10 rounded-xl flex items-center justify-center mb-4">
                            <span class="iconify text-red-500" data-icon="heroicons:map-pin-solid" style="font-size:1.5rem;"></span>
                        </div>
                        <h3 class="font-semibold text-lg mb-2">Nearby Trails</h3>
                        <p class="text-gray-600 text-sm">Close to your location</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="features-container py-20 bg-white">
        <div class="max-w-7xl mx-auto px-6">
            <div class="text-center mb-16">
                <h2 class="text-4xl md:text-5xl font-bold text-[#336d66] mb-6">Why Choose HikeThere?</h2>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                    Everything you need to plan, track, and enjoy your hiking adventures safely and confidently.
                </p>
            </div>

            <div class="grid gap-8 md:grid-cols-2 lg:grid-cols-3">
                <div class="feature-showcase group">
                    <div class="feature-icon bg-[#e3a746]/10">
                        <span class="iconify text-green-500" data-icon="fa6-solid:thumbs-up" style="font-size:1.5rem;"></span>
                    </div>
                    <h3 class="font-bold text-xl mb-3 text-gray-800">Smart Trail Recommendations</h3>
                    <p class="text-gray-600 leading-relaxed mb-4">Get personalized suggestions based on your skill level, fitness, and adventure preferences using our AI-powered matching system.</p>
                    <div class="text-[#e3a746] font-medium text-sm">Learn More →</div>
                </div>

                <div class="feature-showcase group">
                    <div class="feature-icon bg-[#20b6d2]/10">
                        <span class="iconify text-blue-500" data-icon="mdi:weather-partly-cloudy" style="font-size:1.5rem;"></span>
                    </div>
                    <h3 class="font-bold text-xl mb-3 text-gray-800">Real-time Trail Conditions</h3>
                    <p class="text-gray-600 leading-relaxed mb-4">Stay informed with live trail alerts, weather updates, route closures, and safety notifications from local authorities.</p>
                    <div class="text-[#20b6d2] font-medium text-sm">Learn More →</div>
                </div>

                <div class="feature-showcase group">
                    <div class="feature-icon bg-[#dfa648]/10">
                        <span class="iconify text-indigo-500" data-icon="heroicons:user-group-solid" style="font-size:1.5rem;"></span>
                    </div>
                    <h3 class="font-bold text-xl mb-3 text-gray-800">Community & Safety Network</h3>
                    <p class="text-gray-600 leading-relaxed mb-4">Connect with fellow hikers, share experiences, get local insights, and access emergency support when you need it most.</p>
                    <div class="text-[#dfa648] font-medium text-sm">Learn More →</div>
                </div>

                <div class="feature-showcase group">
                    <div class="feature-icon bg-[#336d66]/10">
                        <span class="iconify text-green-600" data-icon="mdi:map" style="font-size:1.5rem;"></span>
                    </div>
                    <h3 class="font-bold text-xl mb-3 text-gray-800">Offline Maps & GPS</h3>
                    <p class="text-gray-600 leading-relaxed mb-4">Download detailed topographic maps for offline use and never lose your way with precise GPS tracking, even without cell service.</p>
                    <div class="text-[#336d66] font-medium text-sm">Learn More →</div>
                </div>

                <div class="feature-showcase group">
                    <div class="feature-icon bg-[#aec896]/20">
                        <span class="iconify text-purple-500" data-icon="heroicons:calendar-solid" style="font-size:1.5rem;"></span>
                    </div>
                    <h3 class="font-bold text-xl mb-3 text-gray-800">Trip Planning Tools</h3>
                    <p class="text-gray-600 leading-relaxed mb-4">Plan your entire adventure with packing lists, weather forecasts, difficulty assessments, and estimated timing calculations.</p>
                    <div class="text-[#336d66] font-medium text-sm">Learn More →</div>
                </div>

                <div class="feature-showcase group">
                    <div class="feature-icon bg-[#20b6d2]/10">
                      <span class="iconify text-red-600" data-icon="fa6-solid:phone" style="font-size:1.5rem;"></span>
                    </div>
                    <h3 class="font-bold text-xl mb-3 text-gray-800">Safety Framework</h3> 
                    <p class="text-gray-600 leading-relaxed mb-4">HikeThere’s safety framework is a structured set of guidelines and tools that help ensure every trip is planned and enjoyed with minimal risk to hikers and the environment.</p>
                    <div class="text-[#20b6d2] font-medium text-sm">Learn More →</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Community Section -->
    <section id="community" class="community-container py-20 bg-gradient-to-br from-[#336d66]/5 to-white">
        <div class="max-w-6xl mx-auto px-6">
            <div class="text-center mb-16">
                <h2 class="text-4xl md:text-5xl font-bold text-[#336d66] mb-6">Join Our Hiking Community</h2>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                    Be part of a passionate network of hikers who value safety, preparedness, and the joy of exploring nature together.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-12">
                <div class="community-feature text-center">
                    <div class="w-16 h-16 bg-[#336d66]/10 rounded-2xl flex items-center justify-center mx-auto mb-6">
                        <span class="iconify text-blue-500" data-icon="mdi:share-variant" style="font-size:1.5rem;"></span>
                    </div>
                    <h3 class="text-xl font-bold mb-3 text-gray-800">Share Adventures</h3>
                    <p class="text-gray-600">Post photos, tips, and stories from your hiking experiences to inspire others.</p>
                </div>

                <div class="community-feature text-center">
                    <div class="w-16 h-16 bg-[#20b6d2]/10 rounded-2xl flex items-center justify-center mx-auto mb-6">
                        <span class="iconify text-green-600" data-icon="heroicons:user-group-solid" style="font-size:1.5rem;"></span>
                    </div>
                    <h3 class="text-xl font-bold mb-3 text-gray-800">Find Hiking Buddies</h3>
                    <p class="text-gray-600">Connect with like-minded hikers in your area and plan group adventures.</p>
                </div>

                <div class="community-feature text-center">
                    <div class="w-16 h-16 bg-[#e3a746]/10 rounded-2xl flex items-center justify-center mx-auto mb-6">
                       <span class="iconify text-amber-500" data-icon="mdi:account-tie" style="font-size:1.5rem;"></span>
                    </div>
                    <h3 class="text-xl font-bold mb-3 text-gray-800">Expert Guidance</h3>
                    <p class="text-gray-600">Get advice from experienced hikers and certified outdoor professionals.</p>
                </div>
            </div>

            <div class="text-center">
                <a href="{{ route('register.select') }}" class="btn-mountain-large">
                    <span>Join Our Community</span>
                    <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                    </svg>
                </a>
                <p class="mt-4 text-gray-600">Free to join • No credit card required</p>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer-container bg-white py-12 border-t border-gray-200">
        <div class="max-w-7xl mx-auto px-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-8">
                <div class="col-span-1 md:col-span-2">
                    <a href="/" class="flex items-center space-x-3 mb-6">
                        <svg class="w-8 h-8 text-[#336d66]" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2L2 22h20L12 2z" />
                        </svg>
                        <span class="font-bold text-xl text-[#336d66]">HikeThere</span>
                    </a>
                    <p class="text-gray-600 mb-6 max-w-md">
                        Your trusted companion for safe, enjoyable, and well-prepared hiking adventures. Explore with confidence.
                    </p>
                    <div class="flex space-x-4">
                        <a href="#" class="social-link">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z" />
                            </svg>
                        </a>
                        <a href="#" class="social-link">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M22.46 6c-.77.35-1.6.58-2.46.69.88-.53 1.56-1.37 1.88-2.38-.83.5-1.75.85-2.72 1.05C18.37 4.5 17.26 4 16 4c-2.35 0-4.27 1.92-4.27 4.29 0 .34.04.67.11.98C8.28 9.09 5.11 7.38 3 4.79c-.37.63-.58 1.37-.58 2.15 0 1.49.75 2.81 1.91 3.56-.71 0-1.37-.2-1.95-.5v.03c0 2.08 1.48 3.82 3.44 4.21a4.22 4.22 0 0 1-1.93.07 4.28 4.28 0 0 0 4 2.98 8.521 8.521 0 0 1-5.33 1.84c-.35 0-.69-.02-1.02-.06C3.44 20.29 5.7 21 8.12 21 16 21 20.33 14.46 20.33 8.79c0-.19 0-.37-.01-.56.84-.6 1.56-1.36 2.14-2.23z" />
                            </svg>
                        </a>
                        <a href="#" class="social-link">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12.017 0C5.396 0 .029 5.367.029 11.987c0 5.079 3.158 9.417 7.618 11.174-.105-.949-.199-2.403.041-3.439.219-.937 1.406-5.957 1.406-5.957s-.359-.72-.359-1.781c0-1.663.967-2.911 2.168-2.911 1.024 0 1.518.769 1.518 1.688 0 1.029-.653 2.567-.992 3.992-.285 1.193.6 2.165 1.775 2.165 2.128 0 3.768-2.245 3.768-5.487 0-2.861-2.063-4.869-5.008-4.869-3.41 0-5.409 2.562-5.409 5.199 0 1.033.394 2.143.889 2.75.097.118.11.22.082.341-.09.369-.293 1.199-.334 1.363-.053.225-.172.271-.402.165-1.495-.69-2.433-2.878-2.433-4.646 0-3.776 2.748-7.252 7.92-7.252 4.158 0 7.392 2.967 7.392 6.923 0 4.135-2.607 7.462-6.233 7.462-1.214 0-2.357-.629-2.75-1.378l-.748 2.853c-.271 1.043-1.002 2.35-1.492 3.146C9.57 23.812 10.763 24.009 12.017 24c6.624 0 11.99-5.367 11.99-11.988C24.007 5.367 18.641.001.012.001z" />
                            </svg>
                        </a>
                        <a href="#" class="social-link">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z" />
                            </svg>
                        </a>
                    </div>
                </div>

                <div>
                    <h4 class="font-bold text-gray-800 mb-4">Product</h4>
                    <ul class="space-y-3 text-gray-600">
                        <li><a href="#" class="hover:text-[#20b6d2] transition-colors">Features</a></li>
                        <li><a href="#" class="hover:text-[#20b6d2] transition-colors">Pricing</a></li>
                        <li><a href="#" class="hover:text-[#20b6d2] transition-colors">Mobile App</a></li>
                        <li><a href="#" class="hover:text-[#20b6d2] transition-colors">API</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="font-bold text-gray-800 mb-4">Support</h4>
                    <ul class="space-y-3 text-gray-600">
                        <li><a href="#" class="hover:text-[#20b6d2] transition-colors">Help Center</a></li>
                        <li><a href="#" class="hover:text-[#20b6d2] transition-colors">Safety Guidelines</a></li>
                        <li><a href="#" class="hover:text-[#20b6d2] transition-colors">Contact Us</a></li>
                        <li><a href="#" class="hover:text-[#20b6d2] transition-colors">Community Forum</a></li>
                    </ul>
                </div>
            </div>

            <div class="border-t border-gray-200 pt-8 flex flex-col md:flex-row justify-between items-center">
                <p class="text-gray-600 text-sm mb-4 md:mb-0">
                    &copy; {{ date('Y') }} HikeThere. All rights reserved.
                </p>
                <div class="flex space-x-6 text-sm text-gray-600">
                    <a href="#" class="hover:text-[#20b6d2] transition-colors">Privacy Policy</a>
                    <a href="#" class="hover:text-[#20b6d2] transition-colors">Terms of Service</a>
                    <a href="#" class="hover:text-[#20b6d2] transition-colors">Cookie Policy</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Mobile Menu Toggle Script -->
    <script>
        document.getElementById('mobile-menu-btn').addEventListener('click', function() {
            const mobileMenu = document.getElementById('mobile-menu');
            mobileMenu.classList.toggle('hidden');
        });

        // Smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                    // Close mobile menu if open
                    document.getElementById('mobile-menu').classList.add('hidden');
                }
            });
        });

        // Navbar scroll effect
        window.addEventListener('scroll', function() {
            const nav = document.querySelector('.nav-container');
            if (window.scrollY > 100) {
                nav.classList.add('scrolled');
            } else {
                nav.classList.remove('scrolled');
            }
        });
    </script>
</x-guest-layout>
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('About HikeThere') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6 lg:p-8">
                    <!-- Hero Section -->
                    <div class="text-center mb-12">
                        <img src="{{ asset('img/icon1.png') }}" alt="HikeThere Logo" class="h-24 w-auto mx-auto mb-6">
                        <h1 class="text-4xl font-bold text-gray-900 mb-4">Welcome to HikeThere</h1>
                        <p class="text-xl text-gray-600 max-w-3xl mx-auto mb-6">
                            Your ultimate companion for discovering, planning, and experiencing unforgettable hiking adventures across the Philippines and beyond.
                        </p>
                    </div>

                    <!-- Mission Section -->
                    <div class="mb-12 bg-gradient-to-r from-green-50 to-blue-50 p-8 rounded-lg">
                        <h2 class="text-3xl font-bold text-gray-900 mb-4">Our Mission</h2>
                        <p class="text-lg text-gray-700 leading-relaxed mb-4">
                            HikeThere is dedicated to connecting outdoor enthusiasts with the best hiking trails and experiences. 
                            We believe that everyone should have access to nature's wonders, and we're here to make that journey 
                            easier, safer, and more enjoyable.
                        </p>
                        <p class="text-lg text-gray-700 leading-relaxed">
                            Founded with the vision of promoting responsible outdoor recreation, HikeThere serves as a bridge between 
                            passionate hikers and trusted trail organizations. We leverage technology to enhance your hiking experience 
                            while preserving the natural beauty and cultural heritage of every trail.
                        </p>
                    </div>

                    <!-- Story Section -->
                    <div class="mb-12">
                        <h2 class="text-3xl font-bold text-gray-900 mb-6 text-center">Our Story</h2>
                        <div class="bg-white border border-gray-200 rounded-lg p-8 max-w-4xl mx-auto shadow-sm">
                            <p class="text-gray-700 mb-4 leading-relaxed">
                                HikeThere was born from a simple observation: while the Philippines is blessed with stunning mountains, 
                                lush forests, and breathtaking trails, finding reliable information about them was often challenging. 
                                Hikers struggled to discover new trails, organizations found it difficult to reach their audience, and 
                                the booking process was fragmented and unreliable.
                            </p>
                            <p class="text-gray-700 mb-4 leading-relaxed">
                                We set out to change that. Our team of outdoor enthusiasts, developers, and conservationists came together 
                                to create a comprehensive platform that would serve both hikers and trail organizations. Today, HikeThere 
                                stands as the Philippines' leading hiking platform, trusted by thousands of adventurers and hundreds of 
                                organizations nationwide.
                            </p>
                            <p class="text-gray-700 leading-relaxed">
                                But we're more than just a booking platform. We're a community of people who believe in the transformative 
                                power of nature, the importance of safety and preparation, and the value of sustainable tourism practices 
                                that benefit local communities and protect our environment.
                            </p>
                        </div>
                    </div>

                    <!-- Safety & Preparation Section -->
                    <div class="mb-12 bg-red-50 border-l-4 border-red-600 p-8 rounded-lg">
                        <h2 class="text-3xl font-bold text-gray-900 mb-6">Safety is Our Priority</h2>
                        <p class="text-gray-700 mb-6 leading-relaxed">
                            At HikeThere, we believe that adventure and safety go hand in hand. Every feature on our platform 
                            is designed with your well-being in mind.
                        </p>
                        <div class="grid md:grid-cols-2 gap-6">
                            <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100">
                                <h3 class="text-xl font-semibold mb-3 text-red-700">Comprehensive Trail Information</h3>
                                <p class="text-gray-600">
                                    Detailed difficulty ratings, terrain descriptions, elevation profiles, and potential hazards 
                                    are provided for every trail to help you make informed decisions.
                                </p>
                            </div>
                            <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100">
                                <h3 class="text-xl font-semibold mb-3 text-red-700">Readiness Assessment Tool</h3>
                                <p class="text-gray-600">
                                    Our AI-powered assessment evaluates your gear, fitness level, health status, and preparedness 
                                    to provide personalized safety recommendations.
                                </p>
                            </div>
                            <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100">
                                <h3 class="text-xl font-semibold mb-3 text-red-700">Real-Time Weather Updates</h3>
                                <p class="text-gray-600">
                                    Stay informed with accurate weather forecasts and alerts specific to trail locations, 
                                    helping you plan the safest hiking schedule.
                                </p>
                            </div>
                            <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100">
                                <h3 class="text-xl font-semibold mb-3 text-red-700">Emergency Preparedness</h3>
                                <p class="text-gray-600">
                                    Access emergency contact information, first-aid guides, and safety protocols for every trail 
                                    directly from our platform.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Environmental Commitment -->
                    <div class="mb-12">
                        <h2 class="text-3xl font-bold text-gray-900 mb-6 text-center">Our Environmental Commitment</h2>
                        <div class="bg-gradient-to-r from-green-100 to-emerald-100 p-8 rounded-lg">
                            <div class="max-w-4xl mx-auto">
                                <p class="text-gray-800 mb-6 text-lg leading-relaxed">
                                    We are deeply committed to environmental conservation and sustainable tourism. Our platform 
                                    actively promotes the Leave No Trace principles and partners exclusively with organizations 
                                    that demonstrate environmental responsibility.
                                </p>
                                <div class="grid md:grid-cols-3 gap-6 mb-6">
                                    <div class="text-center">
                                        <div class="text-4xl mb-2">🌱</div>
                                        <h4 class="font-semibold text-gray-800 mb-2">Leave No Trace</h4>
                                        <p class="text-gray-700 text-sm">
                                            Promoting responsible hiking practices that minimize environmental impact
                                        </p>
                                    </div>
                                    <div class="text-center">
                                        <div class="text-4xl mb-2">♻️</div>
                                        <h4 class="font-semibold text-gray-800 mb-2">Trail Conservation</h4>
                                        <p class="text-gray-700 text-sm">
                                            Supporting trail maintenance and restoration initiatives
                                        </p>
                                    </div>
                                    <div class="text-center">
                                        <div class="text-4xl mb-2">🏔️</div>
                                        <h4 class="font-semibold text-gray-800 mb-2">Community Support</h4>
                                        <p class="text-gray-700 text-sm">
                                            Contributing to local communities and indigenous peoples
                                        </p>
                                    </div>
                                </div>
                                <p class="text-gray-800 text-center italic">
                                    "Take only memories, leave only footprints" — Every hike with HikeThere supports conservation efforts.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Technology & Innovation -->
                    <div class="mb-12 bg-gray-50 p-8 rounded-lg">
                        <h2 class="text-3xl font-bold text-gray-900 mb-6 text-center">Technology That Empowers</h2>
                        <p class="text-gray-700 text-center mb-8 max-w-3xl mx-auto">
                            We leverage cutting-edge technology to make hiking more accessible, enjoyable, and safe for everyone.
                        </p>
                        <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6">
                            <div class="bg-white p-6 rounded-lg text-center border border-gray-200">
                                <div class="text-blue-600 mb-3">
                                    <svg class="w-12 h-12 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"></path>
                                    </svg>
                                </div>
                                <h3 class="font-semibold mb-2">AI-Powered Planning</h3>
                                <p class="text-gray-600 text-sm">Smart itinerary generation based on your preferences and capabilities</p>
                            </div>
                            <div class="bg-white p-6 rounded-lg text-center border border-gray-200">
                                <div class="text-green-600 mb-3">
                                    <svg class="w-12 h-12 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                </div>
                                <h3 class="font-semibold mb-2">GPS Integration</h3>
                                <p class="text-gray-600 text-sm">Precise trail mapping with GPX file support and route visualization</p>
                            </div>
                            <div class="bg-white p-6 rounded-lg text-center border border-gray-200">
                                <div class="text-purple-600 mb-3">
                                    <svg class="w-12 h-12 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z"></path>
                                    </svg>
                                </div>
                                <h3 class="font-semibold mb-2">Weather API</h3>
                                <p class="text-gray-600 text-sm">Real-time meteorological data for accurate forecasting and alerts</p>
                            </div>
                            <div class="bg-white p-6 rounded-lg text-center border border-gray-200">
                                <div class="text-yellow-600 mb-3">
                                    <svg class="w-12 h-12 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                                <h3 class="font-semibold mb-2">Mobile Optimized</h3>
                                <p class="text-gray-600 text-sm">Seamless experience across all devices, especially mobile</p>
                            </div>
                        </div>
                    </div>

                    <!-- Features Grid -->
                    <div class="mb-12">
                        <h2 class="text-3xl font-bold text-gray-900 mb-8 text-center">What We Offer</h2>
                        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                            <!-- Feature 1 -->
                            <div class="bg-white border border-gray-200 rounded-lg p-6 hover:shadow-lg transition duration-300">
                                <div class="text-green-600 mb-4">
                                    <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path>
                                    </svg>
                                </div>
                                <h3 class="text-xl font-semibold mb-2">Explore Trails</h3>
                                <p class="text-gray-600">
                                    Discover thousands of trails with detailed information, difficulty ratings, and beautiful imagery.
                                </p>
                            </div>

                            <!-- Feature 2 -->
                            <div class="bg-white border border-gray-200 rounded-lg p-6 hover:shadow-lg transition duration-300">
                                <div class="text-blue-600 mb-4">
                                    <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                                <h3 class="text-xl font-semibold mb-2">Book Events</h3>
                                <p class="text-gray-600">
                                    Join organized hiking events and book your spot with trusted organizations.
                                </p>
                            </div>

                            <!-- Feature 3 -->
                            <div class="bg-white border border-gray-200 rounded-lg p-6 hover:shadow-lg transition duration-300">
                                <div class="text-purple-600 mb-4">
                                    <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                    </svg>
                                </div>
                                <h3 class="text-xl font-semibold mb-2">Join Community</h3>
                                <p class="text-gray-600">
                                    Connect with fellow hikers, share experiences, and follow your favorite organizations.
                                </p>
                            </div>

                            <!-- Feature 4 -->
                            <div class="bg-white border border-gray-200 rounded-lg p-6 hover:shadow-lg transition duration-300">
                                <div class="text-yellow-600 mb-4">
                                    <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                                    </svg>
                                </div>
                                <h3 class="text-xl font-semibold mb-2">Plan Itineraries</h3>
                                <p class="text-gray-600">
                                    Create detailed hiking itineraries with AI assistance for a safer and more organized experience.
                                </p>
                            </div>

                            <!-- Feature 5 -->
                            <div class="bg-white border border-gray-200 rounded-lg p-6 hover:shadow-lg transition duration-300">
                                <div class="text-red-600 mb-4">
                                    <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                                    </svg>
                                </div>
                                <h3 class="text-xl font-semibold mb-2">Readiness Assessment</h3>
                                <p class="text-gray-600">
                                    Take our comprehensive hiking readiness assessment to ensure you're prepared for your adventure.
                                </p>
                            </div>

                            <!-- Feature 6 -->
                            <div class="bg-white border border-gray-200 rounded-lg p-6 hover:shadow-lg transition duration-300">
                                <div class="text-indigo-600 mb-4">
                                    <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2zm9-13.5V9"></path>
                                    </svg>
                                </div>
                                <h3 class="text-xl font-semibold mb-2">Hiking Tools</h3>
                                <p class="text-gray-600">
                                    Access essential hiking tools including weather forecasts, trail maps, and safety guides.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Values Section -->
                    <div class="mb-12 bg-gray-50 p-8 rounded-lg">
                        <h2 class="text-3xl font-bold text-gray-900 mb-6 text-center">Our Values</h2>
                        <div class="grid md:grid-cols-3 gap-8">
                            <div class="text-center">
                                <h3 class="text-xl font-semibold mb-3 text-green-700">Safety First</h3>
                                <p class="text-gray-600">
                                    We prioritize your safety with comprehensive trail information, weather updates, and readiness assessments.
                                </p>
                            </div>
                            <div class="text-center">
                                <h3 class="text-xl font-semibold mb-3 text-green-700">Community Driven</h3>
                                <p class="text-gray-600">
                                    Built by hikers, for hikers. We value community feedback and shared experiences.
                                </p>
                            </div>
                            <div class="text-center">
                                <h3 class="text-xl font-semibold mb-3 text-green-700">Sustainable Tourism</h3>
                                <p class="text-gray-600">
                                    We promote responsible hiking practices to preserve nature for future generations.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- FAQ Section -->
                    <div class="mb-12">
                        <h2 class="text-3xl font-bold text-gray-900 mb-8 text-center">Frequently Asked Questions</h2>
                        <div class="max-w-4xl mx-auto space-y-4">
                            <div class="bg-white border border-gray-200 rounded-lg p-6">
                                <h3 class="text-lg font-semibold text-gray-900 mb-2">Is HikeThere free to use?</h3>
                                <p class="text-gray-600">
                                    Yes! Creating an account, exploring trails, and accessing most features on HikeThere is completely free. 
                                    You only pay when booking organized events or tours through our partner organizations.
                                </p>
                            </div>
                            <div class="bg-white border border-gray-200 rounded-lg p-6">
                                <h3 class="text-lg font-semibold text-gray-900 mb-2">How do I book a hiking event?</h3>
                                <p class="text-gray-600">
                                    Browse trails or events, select your preferred date and batch, review the details and pricing, 
                                    then complete the booking form. You'll receive payment instructions and a confirmation email once your booking is processed.
                                </p>
                            </div>
                            <div class="bg-white border border-gray-200 rounded-lg p-6">
                                <h3 class="text-lg font-semibold text-gray-900 mb-2">What payment methods do you accept?</h3>
                                <p class="text-gray-600">
                                    We support GCash payments and manual bank transfers, depending on the organization's setup. 
                                    Payment options will be clearly displayed during the booking process.
                                </p>
                            </div>
                            <div class="bg-white border border-gray-200 rounded-lg p-6">
                                <h3 class="text-lg font-semibold text-gray-900 mb-2">Can I cancel my booking?</h3>
                                <p class="text-gray-600">
                                    Cancellation policies vary by organization. Please review the specific cancellation terms during booking. 
                                    Contact the organization directly through our messaging system for cancellation requests.
                                </p>
                            </div>
                            <div class="bg-white border border-gray-200 rounded-lg p-6">
                                <h3 class="text-lg font-semibold text-gray-900 mb-2">How accurate is the trail difficulty rating?</h3>
                                <p class="text-gray-600">
                                    Trail difficulty ratings are provided by verified organizations and experienced hikers. They consider factors 
                                    like elevation gain, trail length, terrain type, and technical challenges. We also recommend using our 
                                    Readiness Assessment tool for personalized evaluation.
                                </p>
                            </div>
                            <div class="bg-white border border-gray-200 rounded-lg p-6">
                                <h3 class="text-lg font-semibold text-gray-900 mb-2">Can I hike trails independently without booking an event?</h3>
                                <p class="text-gray-600">
                                    Many trails can be hiked independently, but some require permits or have organized-group-only policies. 
                                    Trail details will specify access requirements. We always recommend informing someone of your hiking plans and 
                                    checking local regulations.
                                </p>
                            </div>
                            <div class="bg-white border border-gray-200 rounded-lg p-6">
                                <h3 class="text-lg font-semibold text-gray-900 mb-2">How do I use the AI itinerary planner?</h3>
                                <p class="text-gray-600">
                                    Navigate to the Hiking Tools section, select "Build Itinerary," choose your trail and preferences, 
                                    and our AI will generate a detailed day-by-day plan including timing, packing lists, and safety considerations.
                                </p>
                            </div>
                            <div class="bg-white border border-gray-200 rounded-lg p-6">
                                <h3 class="text-lg font-semibold text-gray-900 mb-2">Are the organizations on HikeThere verified?</h3>
                                <p class="text-gray-600">
                                    Yes! All organizations undergo a verification process before being approved on our platform. We verify business 
                                    registration, safety standards, and environmental compliance to ensure you're booking with trustworthy providers.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Getting Started Guide -->
                    <div class="mb-12">
                        <h2 class="text-3xl font-bold text-gray-900 mb-6 text-center">Getting Started with HikeThere</h2>
                        <div class="bg-white border-2 border-gray-200 rounded-lg p-8 max-w-4xl mx-auto">
                            <div class="space-y-6">
                                <div class="flex items-start">
                                    <div class="flex-shrink-0 w-10 h-10 bg-gradient-to-r from-green-500 to-blue-500 text-white rounded-full flex items-center justify-center font-bold mr-4">
                                        1
                                    </div>
                                    <div>
                                        <h3 class="text-xl font-semibold mb-2">Set Up Your Profile</h3>
                                        <p class="text-gray-600">
                                            Complete your profile with hiking preferences, fitness level, and interests. This helps us 
                                            recommend trails that match your abilities and aspirations.
                                        </p>
                                    </div>
                                </div>
                                <div class="flex items-start">
                                    <div class="flex-shrink-0 w-10 h-10 bg-gradient-to-r from-green-500 to-blue-500 text-white rounded-full flex items-center justify-center font-bold mr-4">
                                        2
                                    </div>
                                    <div>
                                        <h3 class="text-xl font-semibold mb-2">Take the Readiness Assessment</h3>
                                        <p class="text-gray-600">
                                            Complete our comprehensive hiking readiness assessment to understand your preparedness level 
                                            and receive personalized safety recommendations.
                                        </p>
                                    </div>
                                </div>
                                <div class="flex items-start">
                                    <div class="flex-shrink-0 w-10 h-10 bg-gradient-to-r from-green-500 to-blue-500 text-white rounded-full flex items-center justify-center font-bold mr-4">
                                        3
                                    </div>
                                    <div>
                                        <h3 class="text-xl font-semibold mb-2">Explore Trails</h3>
                                        <p class="text-gray-600">
                                            Browse our extensive trail database, filter by difficulty, location, or features. Save your 
                                            favorite trails for future reference.
                                        </p>
                                    </div>
                                </div>
                                <div class="flex items-start">
                                    <div class="flex-shrink-0 w-10 h-10 bg-gradient-to-r from-green-500 to-blue-500 text-white rounded-full flex items-center justify-center font-bold mr-4">
                                        4
                                    </div>
                                    <div>
                                        <h3 class="text-xl font-semibold mb-2">Plan Your Adventure</h3>
                                        <p class="text-gray-600">
                                            Use our AI-powered itinerary builder to create detailed hiking plans. Get weather updates, 
                                            packing lists, and safety tips tailored to your chosen trail.
                                        </p>
                                    </div>
                                </div>
                                <div class="flex items-start">
                                    <div class="flex-shrink-0 w-10 h-10 bg-gradient-to-r from-green-500 to-blue-500 text-white rounded-full flex items-center justify-center font-bold mr-4">
                                        5
                                    </div>
                                    <div>
                                        <h3 class="text-xl font-semibold mb-2">Book or Hike</h3>
                                        <p class="text-gray-600">
                                            Book organized events for guided experiences, or use the information to plan independent hikes. 
                                            Always follow safety protocols and Leave No Trace principles.
                                        </p>
                                    </div>
                                </div>
                                <div class="flex items-start">
                                    <div class="flex-shrink-0 w-10 h-10 bg-gradient-to-r from-green-500 to-blue-500 text-white rounded-full flex items-center justify-center font-bold mr-4">
                                        6
                                    </div>
                                    <div>
                                        <h3 class="text-xl font-semibold mb-2">Share Your Experience</h3>
                                        <p class="text-gray-600">
                                            After your hike, leave a review to help other hikers. Share photos, tips, and trail conditions 
                                            to contribute to our growing community knowledge base.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Call to Action -->
                    <div class="text-center bg-gradient-to-r from-green-600 to-blue-600 text-white p-12 rounded-lg mb-12">
                        <h2 class="text-3xl font-bold mb-4">Ready to Start Your Adventure?</h2>
                        <p class="text-xl mb-8 opacity-90">
                            Explore thousands of trails and join a community of passionate hikers today.
                        </p>
                        <div class="flex flex-wrap justify-center gap-4">
                            <a href="{{ route('explore') }}" class="bg-white text-green-600 px-8 py-3 rounded-lg font-semibold hover:bg-gray-100 transition duration-300">
                                Explore Trails
                            </a>
                            <a href="{{ route('community.index') }}" class="bg-transparent border-2 border-white text-white px-8 py-3 rounded-lg font-semibold hover:bg-white hover:text-green-600 transition duration-300">
                                Join Community
                            </a>
                        </div>
                    </div>

                    <!-- Contact/Support Section -->
                    <div class="text-center">
                        <h3 class="text-2xl font-bold text-gray-900 mb-4">Need Help?</h3>
                        <p class="text-gray-600 mb-6">
                            Have questions or feedback? We'd love to hear from you!
                        </p>
                        
                        <!-- Support System Card -->
                        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border-2 border-blue-200 rounded-lg p-6 mb-6 max-w-2xl mx-auto">
                            <div class="flex items-center justify-center mb-4">
                                <svg class="w-12 h-12 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                </svg>
                            </div>
                            <h4 class="text-xl font-bold text-gray-900 mb-2">Get Support</h4>
                            <p class="text-gray-700 mb-4">
                                Need assistance? Our support team is here to help! Create a support ticket and we'll respond as soon as possible.
                            </p>
                            <a href="{{ route('support.create') }}" class="inline-flex items-center px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg shadow-md transition duration-150">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
                                </svg>
                                Create Support Ticket
                            </a>
                            <p class="text-sm text-gray-600 mt-3">
                                Or view your existing tickets in the <a href="{{ route('support.index') }}" class="text-blue-600 hover:text-blue-800 font-semibold underline">Support Center</a>
                            </p>
                        </div>

                        <p class="text-gray-700">
                            Visit your <a href="{{ route('account.settings') }}" class="text-blue-600 hover:text-blue-800 font-semibold">Account Settings</a> to manage your preferences and notifications.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

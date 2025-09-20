<x-app-layout>
    <x-slot name="header">
        <div class="bg-gradient-to-r from-emerald-900 via-green-700 to-teal-800 shadow-lg">
            <div class="max-w-6xl mx-auto px-6 py-12 flex flex-col items-center">
                <span class="text-7xl mb-4 animate-float drop-shadow-xl">⛰️</span>
                <h1 class="text-5xl font-extrabold text-white mb-2 tracking-tight">
                    Mt. Pulag Adventure Booking
                </h1>
                <p class="text-emerald-100 text-xl font-light">
                    Experience the "Playground of the Gods"
                </p>
            </div>
        </div>
    </x-slot>

    <div class="bg-gradient-to-br from-slate-50 via-blue-50 to-emerald-50 min-h-screen py-12">
        <div class="max-w-6xl mx-auto px-4 md:px-8 space-y-6">

            <!-- Mountain Overview Section -->
            <div class="bg-white rounded-3xl shadow-2xl border border-emerald-100 p-10 md:p-14">
                <div class="text-center mb-8">
                    <h2 class="text-3xl font-bold text-emerald-800 mb-4">Mount Pulag Overview</h2>
                    <div class="w-24 h-1 bg-emerald-600 mx-auto rounded-full mb-6"></div>
                </div>
                <div class="flex flex-col lg:flex-row gap-10 items-center">
                    <div class="flex-1">
                        <div class="bg-gradient-to-br from-emerald-50 to-blue-50 rounded-2xl p-8 shadow-lg">
                            <p class="text-lg text-gray-700 leading-relaxed mb-6">
                                Known as the <strong class="text-emerald-700">"Playground of the Gods,"</strong> Mount Pulag is the 3rd highest mountain in the Philippines and a must-visit for hikers. Famous for its breathtaking sea of clouds, sunrise views, and grassland summit, Mt. Pulag offers an unforgettable hiking experience suitable for both beginners and experienced trekkers depending on the chosen trail.
                            </p>
                            <div class="grid grid-cols-2 gap-4">
                                <div class="bg-white rounded-xl p-4 shadow">
                                    <div class="text-emerald-600 text-2xl mb-2">🏔️</div>
                                    <h4 class="font-semibold text-gray-800">Elevation</h4>
                                    <p class="text-sm text-gray-600">2,922 MASL</p>
                                </div>
                                <div class="bg-white rounded-xl p-4 shadow">
                                    <div class="text-emerald-600 text-2xl mb-2">🌅</div>
                                    <h4 class="font-semibold text-gray-800">Famous For</h4>
                                    <p class="text-sm text-gray-600">Sea of Clouds</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="flex-1">
                        <img src="pulag-1.jpg" alt="Mount Pulag Overview" class="rounded-2xl shadow-xl object-cover w-full h-96">
                    </div>
                </div>
            </div>

            <!-- Booking Card -->
            <div class="bg-white rounded-3xl shadow-2xl border border-emerald-100 p-10 md:p-14">
                <div class="flex flex-col lg:flex-row gap-12">
                    <!-- Price & Quick Details -->
                    <div class="lg:w-1/3">
                        <div class="bg-gradient-to-br from-emerald-600 to-teal-700 rounded-2xl p-8 text-white text-center shadow-xl mb-8">
                            <h3 class="text-lg font-semibold mb-2">Package Price</h3>
                            <div class="text-5xl font-extrabold mb-2 tracking-tight">₱4,900</div>
                            <p class="text-emerald-100 mb-4">per person</p>
                            <div class="bg-emerald-800 bg-opacity-30 rounded-lg p-4">
                                <p class="text-sm"><span class="font-semibold">Down payment:</span> ₱1,500</p>
                                <p class="text-sm"><span class="font-semibold">Balance:</span> Pay on departure</p>
                            </div>
                        </div>

                        <div class="bg-gradient-to-br from-blue-50 to-emerald-50 rounded-2xl p-6 shadow-lg mb-8">
                            <h4 class="text-lg font-bold text-emerald-800 mb-4 flex items-center">
                                <span class="mr-2">📋</span> Trip Details
                            </h4>
                            <div class="space-y-3">
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600">Duration</span>
                                    <span class="font-semibold text-gray-800">2 days, 1 night</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600">Difficulty</span>
                                    <span class="font-semibold text-orange-600">Moderate to Difficult</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600">Best Season</span>
                                    <span class="font-semibold text-gray-800">January to May</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600">Permit Required</span>
                                    <span class="font-semibold text-red-600">Yes</span>
                                </div>
                            </div>
                        </div>

                        @auth
                            <button class="w-full bg-gradient-to-r from-emerald-600 to-teal-600 text-white font-bold py-4 rounded-xl hover:from-emerald-700 hover:to-teal-700 transition duration-300 shadow-lg text-lg transform hover:scale-105">
                                Book Your Adventure
                            </button>
                        @else
                            <a href="{{ route('login') }}" class="block w-full bg-gradient-to-r from-emerald-600 to-teal-600 text-white font-bold py-4 rounded-xl hover:from-emerald-700 hover:to-teal-700 transition duration-300 shadow-lg text-lg text-center transform hover:scale-105">
                                Login to Book
                            </a>
                        @endauth
                    </div>

                    <!-- Booking Form -->
                    <div class="lg:w-2/3">
                        <form action="#" method="POST" class="space-y-4">
                            <div class="text-center mb-8">
                                <h2 class="text-3xl font-bold text-emerald-800 mb-2">Reserve Your Slot</h2>
                                <p class="text-gray-600">Fill out the form below to secure your mountain adventure</p>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="trail_id" class="block text-sm font-semibold text-gray-700 mb-2">Select Trail</label>
                                    <select id="trail_id" name="trail_id" class="w-full px-4 py-3 border border-gray-300 rounded-xl bg-white shadow-sm focus:border-emerald-500 focus:ring-emerald-200 focus:ring-2 transition duration-200" required>
                                        <option value="">Choose your trail...</option>
                                        <option value="1">Ambangeg Trail (Easiest)</option>
                                        <option value="2">Akiki Trail (Killer Trail)</option>
                                        <option value="3">Tawangan Trail (Moderate)</option>
                                    </select>
                                </div>
                                <div>
                                    <label for="date" class="block text-sm font-semibold text-gray-700 mb-2">Preferred Date</label>
                                    <input type="date" id="date" name="date" class="w-full px-4 py-3 border border-gray-300 rounded-xl shadow-sm focus:border-emerald-500 focus:ring-emerald-200 focus:ring-2 transition duration-200" required>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="pax" class="block text-sm font-semibold text-gray-700 mb-2">Number of Climbers</label>
                                    <select id="pax" name="pax" class="w-full px-4 py-3 border border-gray-300 rounded-xl shadow-sm focus:border-emerald-500 focus:ring-emerald-200 focus:ring-2 transition duration-200" required>
                                        <option value="">Select number</option>
                                        @for($i = 1; $i <= 15; $i++)
                                            <option value="{{ $i }}">{{ $i }} {{ $i === 1 ? 'person' : 'people' }}</option>
                                        @endfor
                                    </select>
                                </div>
                                <div>
                                    <label for="full_name" class="block text-sm font-semibold text-gray-700 mb-2">Lead Climber Name</label>
                                    <input type="text" id="full_name" name="full_name" placeholder="Enter full name" class="w-full px-4 py-3 border border-gray-300 rounded-xl shadow-sm focus:border-emerald-500 focus:ring-emerald-200 focus:ring-2 transition duration-200" required>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">Email Address</label>
                                    <input type="email" id="email" name="email" placeholder="your.email@example.com" class="w-full px-4 py-3 border border-gray-300 rounded-xl shadow-sm focus:border-emerald-500 focus:ring-emerald-200 focus:ring-2 transition duration-200" required>
                                </div>
                                <div>
                                    <label for="cell_no" class="block text-sm font-semibold text-gray-700 mb-2">Contact Number</label>
                                    <input type="text" id="cell_no" name="cell_no" placeholder="+639xxxxxxxxx" class="w-full px-4 py-3 border border-gray-300 rounded-xl shadow-sm focus:border-emerald-500 focus:ring-emerald-200 focus:ring-2 transition duration-200" required>
                                </div>
                            </div>

                            <div class="bg-amber-50 border border-amber-200 rounded-xl p-4">
                                <div class="flex items-start space-x-3">
                                    <input type="checkbox" id="terms" name="terms" required class="mt-1 h-4 w-4 text-emerald-600 border-gray-300 rounded focus:ring-emerald-500">
                                    <label for="terms" class="text-sm text-gray-700">
                                        I acknowledge that I have read and agree to the <a href="#" class="text-emerald-600 underline font-semibold">Terms and Conditions</a>, understand the risks involved in mountain climbing, and confirm that I am physically fit for this adventure.
                                    </label>
                                </div>
                            </div>

                            <button type="submit" class="w-full bg-gradient-to-r from-emerald-600 to-teal-600 text-white font-bold py-4 rounded-xl hover:from-emerald-700 hover:to-teal-700 transition duration-300 shadow-lg text-lg transform hover:scale-105">
                                🏔️ Reserve My Slot Now
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Package Inclusions -->
            <div class="bg-white rounded-3xl shadow-xl border border-emerald-100 p-10">
                <div class="text-center mb-8">
                    <h3 class="text-3xl font-bold text-emerald-800 mb-4">Package Inclusions</h3>
                    <div class="w-24 h-1 bg-emerald-600 mx-auto rounded-full"></div>
                </div>
                <div class="grid md:grid-cols-3 gap-8">
                    <div class="bg-gradient-to-br from-emerald-50 to-green-50 rounded-2xl p-6 shadow-lg">
                        <div class="text-emerald-600 text-3xl mb-4">🚐</div>
                        <h4 class="font-bold text-gray-800 mb-3">Transportation & Logistics</h4>
                        <ul class="text-gray-700 space-y-2">
                            <li>• Van Transportation (Roundtrip)</li>
                            <li>• Entrance fee</li>
                            <li>• Admin fee</li>
                            <li>• LGU fee</li>
                            <li>• Camp fee</li>
                        </ul>
                    </div>
                    <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-2xl p-6 shadow-lg">
                        <div class="text-blue-600 text-3xl mb-4">👨‍🏫</div>
                        <h4 class="font-bold text-gray-800 mb-3">Professional Support</h4>
                        <ul class="text-gray-700 space-y-2">
                            <li>• Certified Guides (1-2)</li>
                            <li>• Food porter (1)</li>
                            <li>• Coordinator & Medic (1-2)</li>
                            <li>• Digital climb certificate</li>
                        </ul>
                    </div>
                    <div class="bg-gradient-to-br from-orange-50 to-red-50 rounded-2xl p-6 shadow-lg">
                        <div class="text-orange-600 text-3xl mb-4">🍽️</div>
                        <h4 class="font-bold text-gray-800 mb-3">Meals & Extras</h4>
                        <ul class="text-gray-700 space-y-2">
                            <li>• Dinner (Day 1)</li>
                            <li>• Breakfast (Day 2)</li>
                            <li>• All cooking equipment</li>
                            <li>• Group camping gear</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Trail Information -->
            <div class="bg-white rounded-3xl shadow-xl border border-blue-100 p-10">
                <div class="text-center mb-8">
                    <h3 class="text-3xl font-bold text-blue-800 mb-4">Trail Information</h3>
                    <div class="w-24 h-1 bg-blue-600 mx-auto rounded-full"></div>
                </div>
                <div class="grid md:grid-cols-2 gap-10">
                    <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-2xl p-8 shadow-lg">
                        <h4 class="font-bold text-gray-800 mb-4 flex items-center">
                            <span class="text-2xl mr-3">🌄</span> Trail Highlights
                        </h4>
                        <ul class="text-gray-700 space-y-3">
                            <li class="flex items-start">
                                <span class="text-blue-600 mr-2">•</span>
                                <span>Breathtaking sea of clouds views</span>
                            </li>
                            <li class="flex items-start">
                                <span class="text-blue-600 mr-2">•</span>
                                <span>Spectacular sunrise from the summit</span>
                            </li>
                            <li class="flex items-start">
                                <span class="text-blue-600 mr-2">•</span>
                                <span>Unique grassland summit experience</span>
                            </li>
                        </ul>
                    </div>
                    <div class="bg-gradient-to-br from-green-50 to-emerald-50 rounded-2xl p-8 shadow-lg">
                        <h4 class="font-bold text-gray-800 mb-4 flex items-center">
                            <span class="text-2xl mr-3">🗺️</span> Terrain Details
                        </h4>
                        <ul class="text-gray-700 space-y-3">
                            <li class="flex items-start">
                                <span class="text-emerald-600 mr-2">•</span>
                                <span>Mossy forest sections</span>
                            </li>
                            <li class="flex items-start">
                                <span class="text-emerald-600 mr-2">•</span>
                                <span>Rolling grasslands</span>
                            </li>
                            <li class="flex items-start">
                                <span class="text-emerald-600 mr-2">•</span>
                                <span>Rocky terrain with steep sections</span>
                            </li>
                            <li class="flex items-start">
                                <span class="text-amber-600 mr-2">⚠️</span>
                                <span>Muddy and slippery during rainy season</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Transportation & Departure -->
            <div class="bg-white rounded-3xl shadow-xl border border-indigo-100 p-10">
                <div class="text-center mb-8">
                    <h3 class="text-3xl font-bold text-indigo-800 mb-4">Transportation & Departure</h3>
                    <div class="w-24 h-1 bg-indigo-600 mx-auto rounded-full"></div>
                </div>
                <div class="grid md:grid-cols-2 gap-10">
                    <div class="bg-gradient-to-br from-indigo-50 to-purple-50 rounded-2xl p-8 shadow-lg">
                        <h4 class="font-bold text-gray-800 mb-4 flex items-center">
                            <span class="text-2xl mr-3">📍</span> Departure Points
                        </h4>
                        <div class="space-y-4">
                            <div class="bg-white rounded-lg p-4 shadow">
                                <h5 class="font-semibold text-gray-800">Primary Departure</h5>
                                <p class="text-gray-600">Mayflower Parking, Greenfield Shaw</p>
                            </div>
                            <div class="bg-white rounded-lg p-4 shadow">
                                <h5 class="font-semibold text-gray-800">Alternative Pick-up</h5>
                                <p class="text-gray-600">Any approved point along the route</p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gradient-to-br from-green-50 to-emerald-50 rounded-2xl p-8 shadow-lg">
                        <h4 class="font-bold text-gray-800 mb-4 flex items-center">
                            <span class="text-2xl mr-3">🚗</span> Transport Details
                        </h4>
                        <div class="space-y-4">
                            <div class="bg-white rounded-lg p-4 shadow">
                                <h5 class="font-semibold text-gray-800">To DENR Office</h5>
                                <p class="text-gray-600">Private van transportation</p>
                            </div>
                            <div class="bg-white rounded-lg p-4 shadow">
                                <h5 class="font-semibold text-gray-800">To Ranger Station</h5>
                                <p class="text-gray-600">Private jeep transfer</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Permit Requirements -->
            <div class="bg-white rounded-3xl shadow-xl border border-red-100 p-10">
                <div class="text-center mb-8">
                    <h3 class="text-3xl font-bold text-red-800 mb-4">Permit & Requirements</h3>
                    <div class="w-24 h-1 bg-red-600 mx-auto rounded-full"></div>
                </div>
                <div class="bg-red-50 border-l-4 border-red-400 rounded-r-xl p-6 mb-8">
                    <div class="flex items-center">
                        <div class="text-red-600 text-3xl mr-4">⚠️</div>
                        <div>
                            <h4 class="font-bold text-red-800 mb-2">Important Notice</h4>
                            <p class="text-red-700">A climbing permit is mandatory for all hikers. No entry without valid permit and proper documentation.</p>
                        </div>
                    </div>
                </div>
                <div class="grid md:grid-cols-2 gap-10">
                    <div class="bg-gradient-to-br from-red-50 to-pink-50 rounded-2xl p-8 shadow-lg">
                        <h4 class="font-bold text-gray-800 mb-4 flex items-center">
                            <span class="text-2xl mr-3">📋</span> Required Documents
                        </h4>
                        <ul class="space-y-3">
                            <li class="flex items-center bg-white rounded-lg p-3 shadow">
                                <span class="text-green-600 mr-3">✓</span>
                                <span class="text-gray-700">Valid Government ID</span>
                            </li>
                            <li class="flex items-center bg-white rounded-lg p-3 shadow">
                                <span class="text-green-600 mr-3">✓</span>
                                <span class="text-gray-700">Climbing Permit</span>
                            </li>
                            <li class="flex items-center bg-white rounded-lg p-3 shadow">
                                <span class="text-green-600 mr-3">✓</span>
                                <span class="text-gray-700">Signed Waiver Form</span>
                            </li>
                            <li class="flex items-center bg-white rounded-lg p-3 shadow">
                                <span class="text-green-600 mr-3">✓</span>
                                <span class="text-gray-700">Medical Certificate (Fit to Climb)</span>
                            </li>
                        </ul>
                    </div>
                    <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-2xl p-8 shadow-lg">
                        <h4 class="font-bold text-gray-800 mb-4 flex items-center">
                            <span class="text-2xl mr-3">💪</span> Health & Fitness
                        </h4>
                        <ul class="space-y-3">
                            <li class="flex items-center bg-white rounded-lg p-3 shadow">
                                <span class="text-blue-600 mr-3">•</span>
                                <span class="text-gray-700">Moderate physical fitness required</span>
                            </li>
                            <li class="flex items-center bg-white rounded-lg p-3 shadow">
                                <span class="text-blue-600 mr-3">•</span>
                                <span class="text-gray-700">Prior hiking experience beneficial</span>
                            </li>
                            <li class="flex items-center bg-white rounded-lg p-3 shadow">
                                <span class="text-blue-600 mr-3">•</span>
                                <span class="text-gray-700">Altitude acclimatization advised</span>
                            </li>
                        </ul>
                        <div class="mt-4 p-4 bg-blue-100 rounded-lg">
                            <p class="text-blue-800 text-sm font-medium">
                                <strong>Permit Process:</strong> Contact our tour organizer to reserve your slot and secure permits in advance.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Packing List -->
            <div class="bg-white rounded-3xl shadow-xl border border-green-100 p-10">
                <div class="text-center mb-8">
                    <h3 class="text-3xl font-bold text-emerald-800 mb-4">Essential Packing List</h3>
                    <div class="w-24 h-1 bg-emerald-600 mx-auto rounded-full"></div>
                </div>
                <div class="grid md:grid-cols-3 gap-8">
                    <div class="bg-gradient-to-br from-emerald-50 to-green-50 rounded-2xl p-6 shadow-lg">
                        <h4 class="font-bold text-emerald-800 mb-4 flex items-center">
                            <span class="text-2xl mr-3">🏕️</span> Camping Essentials
                        </h4>
                        <ul class="text-gray-700 space-y-2">
                            <li>• Personal tent</li>
                            <li>• Tarp/groundsheet</li>
                            <li>• Sleeping bag</li>
                            <li>• Sleeping pad</li>
                            <li>• Headlamp with extra batteries</li>
                            <li>• Whistle (emergency)</li>
                        </ul>
                    </div>
                    <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-2xl p-6 shadow-lg">
                        <h4 class="font-bold text-blue-800 mb-4 flex items-center">
                            <span class="text-2xl mr-3">👕</span> Clothing & Protection
                        </h4>
                        <ul class="text-gray-700 space-y-2">
                            <li>• Moisture-wicking clothes</li>
                            <li>• Warm jacket/fleece</li>
                            <li>• Rain gear/poncho</li>
                            <li>• Hiking gloves</li>
                            <li>• Hat/headgear</li>
                            <li>• Sunglasses</li>
                            <li>• Face towel</li>
                        </ul>
                    </div>
                    <div class="bg-gradient-to-br from-orange-50 to-red-50 rounded-2xl p-6 shadow-lg">
                        <h4 class="font-bold text-orange-800 mb-4 flex items-center">
                            <span class="text-2xl mr-3">🍽️</span> Personal Items
                        </h4>
                        <ul class="text-gray-700 space-y-2">
                            <li>• Water bottles/hydration</li>
                            <li>• Personal trail food/snacks</li>
                            <li>• Eating utensils</li>
                            <li>• Toiletries & personal hygiene</li>
                            <li>• First aid kit (IFAK)</li>
                            <li>• Valid IDs</li>
                            <li>• Electronics (power bank, etc.)</li>
                            <li>• Sunscreen & insect repellent</li>
                            <li>• Baby oil (for muscles)</li>
                        </ul>
                    </div>
                </div>
                <div class="mt-8 p-6 bg-gradient-to-r from-amber-50 to-orange-50 rounded-2xl border border-amber-200">
                    <div class="flex items-start">
                        <span class="text-3xl mr-4">💡</span>
                        <div>
                            <h4 class="font-bold text-amber-800 mb-2">Pro Packing Tips</h4>
                            <p class="text-amber-700">Pack light but smart. Weather can change rapidly at high altitude. Use layering system for temperature regulation. Waterproof your electronics and important documents.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Guide Information & Environmental Practices -->
            <div class="grid md:grid-cols-2 gap-8">
                <div class="bg-white rounded-3xl shadow-xl border border-emerald-100 p-10">
                    <h3 class="text-2xl font-bold text-emerald-800 mb-6 flex items-center">
                        <span class="text-3xl mr-3">👨‍🏫</span> Professional Guides
                    </h3>
                    <div class="space-y-4">
                        <div class="bg-emerald-50 rounded-xl p-4">
                            <h4 class="font-semibold text-emerald-800 mb-2">Certified & Experienced</h4>
                            <p class="text-emerald-700 text-sm">Our guides are certified mountaineers with extensive knowledge of Mt. Pulag's trails, weather patterns, and safety protocols.</p>
                        </div>
                        <div class="bg-emerald-50 rounded-xl p-4">
                            <h4 class="font-semibold text-emerald-800 mb-2">Comprehensive Services</h4>
                            <ul class="text-emerald-700 text-sm space-y-1">
                                <li>• Trail navigation and route planning</li>
                                <li>• Safety briefings and risk assessment</li>
                                <li>• Group management during the hike</li>
                                <li>• Emergency response coordination</li>
                                <li>• Environmental education</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-3xl shadow-xl border border-green-100 p-10">
                    <h3 class="text-2xl font-bold text-green-800 mb-6 flex items-center">
                        <span class="text-3xl mr-3">🌿</span> Environmental Practices
                    </h3>
                    <div class="bg-green-50 border-l-4 border-green-400 rounded-r-xl p-4 mb-4">
                        <h4 class="font-semibold text-green-800 mb-2">Leave No Trace Principles</h4>
                        <p class="text-green-700 text-sm">Help us preserve Mt. Pulag's pristine environment for future generations.</p>
                    </div>
                    <div class="space-y-3">
                        <div class="flex items-center bg-green-50 rounded-lg p-3">
                            <span class="text-green-600 mr-3">✓</span>
                            <span class="text-gray-700 text-sm">Pack out all trash and waste</span>
                        </div>
                        <div class="flex items-center bg-green-50 rounded-lg p-3">
                            <span class="text-green-600 mr-3">✓</span>
                            <span class="text-gray-700 text-sm">Respect wildlife and flora</span>
                        </div>
                        <div class="flex items-center bg-green-50 rounded-lg p-3">
                            <span class="text-green-600 mr-3">✓</span>
                            <span class="text-gray-700 text-sm">Use designated trails and campsites</span>
                        </div>
                        <div class="flex items-center bg-green-50 rounded-lg p-3">
                            <span class="text-green-600 mr-3">✓</span>
                            <span class="text-gray-700 text-sm">Minimize campfire impact</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Customer Feedback -->
            <div class="bg-white rounded-3xl shadow-xl border border-blue-100 p-10">
                <div class="text-center mb-8">
                    <h3 class="text-3xl font-bold text-blue-800 mb-4">What Our Climbers Say</h3>
                    <div class="w-24 h-1 bg-blue-600 mx-auto rounded-full"></div>
                </div>
                <div class="grid md:grid-cols-3 gap-8">
                    <div class="bg-gradient-to-br from-green-50 to-emerald-50 rounded-2xl p-6 shadow-lg">
                        <div class="text-green-600 text-3xl mb-4">⭐</div>
                        <h4 class="font-bold text-green-800 mb-3">Positive Highlights</h4>
                        <ul class="text-gray-700 space-y-2 text-sm">
                            <li>• Breathtaking sunrise views</li>
                            <li>• Unforgettable sea of clouds</li>
                            <li>• Well-organized tours</li>
                            <li>• Professional guide service</li>
                            <li>• Amazing photo opportunities</li>
                        </ul>
                    </div>
                    <div class="bg-gradient-to-br from-amber-50 to-orange-50 rounded-2xl p-6 shadow-lg">
                        <div class="text-amber-600 text-3xl mb-4">⚠️</div>
                        <h4 class="font-bold text-amber-800 mb-3">Important Considerations</h4>
                        <ul class="text-gray-700 space-y-2 text-sm">
                            <li>• Weather can be unpredictable</li>
                            <li>• Trail difficulty increases during rainy season</li>
                            <li>• Physical preparation is essential</li>
                            <li>• Proper gear is crucial</li>
                        </ul>
                    </div>
                    <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-2xl p-6 shadow-lg">
                        <div class="text-blue-600 text-3xl mb-4">💡</div>
                        <h4 class="font-bold text-blue-800 mb-3">Recommendations</h4>
                        <ul class="text-gray-700 space-y-2 text-sm">
                            <li>• Train physically before the climb</li>
                            <li>• Bring proper hiking gear</li>
                            <li>• Follow guide instructions</li>
                            <li>• Start early for best sunrise views</li>
                            <li>• Check weather conditions</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- FAQ Section -->
            <div class="bg-white rounded-3xl shadow-xl border border-purple-100 p-10">
                <div class="text-center mb-8">
                    <h3 class="text-3xl font-bold text-purple-800 mb-4">Frequently Asked Questions</h3>
                    <div class="w-24 h-1 bg-purple-600 mx-auto rounded-full"></div>
                </div>
                <div class="space-y-4">
                    <div class="bg-gradient-to-r from-purple-50 to-indigo-50 rounded-2xl p-6 shadow-lg">
                        <h4 class="font-bold text-purple-800 mb-3 flex items-center">
                            <span class="text-2xl mr-3">👶</span> Is it safe for children or elderly?
                        </h4>
                        <p class="text-gray-700 pl-11">Suitable for physically fit children with hiking experience. Elderly climbers should obtain medical clearance and assess their fitness level carefully before attempting this climb.</p>
                    </div>
                    <div class="bg-gradient-to-r from-blue-50 to-cyan-50 rounded-2xl p-6 shadow-lg">
                        <h4 class="font-bold text-blue-800 mb-3 flex items-center">
                            <span class="text-2xl mr-3">🥾</span> Do I need prior hiking experience?
                        </h4>
                        <p class="text-gray-700 pl-11">While hiking experience is helpful and recommended, our professional guides make the climb manageable for motivated beginners. However, good physical fitness is essential.</p>
                    </div>
                    <div class="bg-gradient-to-r from-green-50 to-emerald-50 rounded-2xl p-6 shadow-lg">
                        <h4 class="font-bold text-green-800 mb-3 flex items-center">
                            <span class="text-2xl mr-3">🚶</span> Can I climb alone without a guide?
                        </h4>
                        <p class="text-gray-700 pl-11">No, solo climbing is not permitted. Professional guides are mandatory for all climbers for safety reasons and to ensure compliance with park regulations.</p>
                    </div>
                </div>
            </div>

            <!-- Emergency Contacts -->
            <div class="bg-white rounded-3xl shadow-xl border border-red-100 p-10">
                <div class="text-center mb-8">
                    <h3 class="text-3xl font-bold text-red-800 mb-4">Emergency Information</h3>
                    <div class="w-24 h-1 bg-red-600 mx-auto rounded-full"></div>
                </div>
                <div class="grid md:grid-cols-2 gap-10">
                    <div class="bg-gradient-to-br from-red-50 to-pink-50 rounded-2xl p-8 shadow-lg">
                        <h4 class="font-bold text-red-800 mb-6 flex items-center">
                            <span class="text-3xl mr-3">🚨</span> Emergency Contacts
                        </h4>
                        <div class="bg-white rounded-xl p-6 shadow">
                            <div class="flex items-center mb-4">
                                <div class="bg-red-100 rounded-full p-3 mr-4">
                                    <span class="text-red-600 text-xl">📞</span>
                                </div>
                                <div>
                                    <h5 class="font-semibold text-gray-800">DENR Office</h5>
                                    <p class="text-red-700 font-mono text-lg">09612499071</p>
                                </div>
                            </div>
                            <div class="text-sm text-gray-600">
                                <p>Available 24/7 for emergency situations, permit inquiries, and trail condition updates.</p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-2xl p-8 shadow-lg">
                        <h4 class="font-bold text-blue-800 mb-6 flex items-center">
                            <span class="text-3xl mr-3">⛑️</span> Safety Measures
                        </h4>
                        <ul class="space-y-4">
                            <li class="flex items-center bg-white rounded-lg p-4 shadow">
                                <span class="text-blue-600 mr-3">•</span>
                                <span class="text-gray-700">Professional medic accompanies each group</span>
                            </li>
                            <li class="flex items-center bg-white rounded-lg p-4 shadow">
                                <span class="text-blue-600 mr-3">•</span>
                                <span class="text-gray-700">Emergency communication equipment available</span>
                            </li>
                            <li class="flex items-center bg-white rounded-lg p-4 shadow">
                                <span class="text-blue-600 mr-3">•</span>
                                <span class="text-gray-700">Evacuation procedures established</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Traveler Photos -->
            <div class="bg-white rounded-3xl shadow-xl border border-emerald-100 p-10">
                <div class="text-center mb-8">
                    <h3 class="text-3xl font-bold text-emerald-800 mb-4">Captured Memories</h3>
                    <div class="w-24 h-1 bg-emerald-600 mx-auto rounded-full"></div>
                    <p class="text-gray-600 mt-4">See what awaits you on your Mt. Pulag adventure</p>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="relative group overflow-hidden rounded-2xl shadow-lg">
                        <img src="/images/pulag-sea-of-clouds.jpg" alt="Mt. Pulag Sea of Clouds" class="w-full h-64 object-cover transition duration-300 group-hover:scale-110">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent opacity-0 group-hover:opacity-100 transition duration-300">
                            <div class="absolute bottom-4 left-4 text-white">
                                <h4 class="font-semibold">Sea of Clouds</h4>
                                <p class="text-sm opacity-90">Breathtaking morning views</p>
                            </div>
                        </div>
                    </div>
                    <div class="relative group overflow-hidden rounded-2xl shadow-lg">
                        <img src="/images/pulag-sunrise.jpg" alt="Mt. Pulag Sunrise" class="w-full h-64 object-cover transition duration-300 group-hover:scale-110">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent opacity-0 group-hover:opacity-100 transition duration-300">
                            <div class="absolute bottom-4 left-4 text-white">
                                <h4 class="font-semibold">Golden Sunrise</h4>
                                <p class="text-sm opacity-90">Spectacular dawn experience</p>
                            </div>
                        </div>
                    </div>
                    <div class="relative group overflow-hidden rounded-2xl shadow-lg">
                        <img src="/images/pulag-hikers.jpg" alt="Mt. Pulag Hikers" class="w-full h-64 object-cover transition duration-300 group-hover:scale-110">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent opacity-0 group-hover:opacity-100 transition duration-300">
                            <div class="absolute bottom-4 left-4 text-white">
                                <h4 class="font-semibold">Adventure Awaits</h4>
                                <p class="text-sm opacity-90">Join our happy climbers</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Call to Action -->
            <div class="bg-gradient-to-r from-emerald-600 to-teal-700 rounded-3xl shadow-2xl p-12 text-center text-white">
                <h2 class="text-4xl font-bold mb-4">Ready for Your Mt. Pulag Adventure?</h2>
                <p class="text-xl text-emerald-100 mb-8">Join thousands of satisfied climbers who have experienced the magic of the "Playground of the Gods"</p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
                    @auth
                        <button class="bg-white text-emerald-700 font-bold py-4 px-8 rounded-xl hover:bg-emerald-50 transition duration-300 shadow-lg text-lg transform hover:scale-105">
                            🏔️ Book Your Adventure Now
                        </button>
                    @else
                        <a href="{{ route('login') }}" class="bg-white text-emerald-700 font-bold py-4 px-8 rounded-xl hover:bg-emerald-50 transition duration-300 shadow-lg text-lg transform hover:scale-105">
                            🏔️ Login to Start Booking
                        </a>
                    @endauth
                    <p class="text-emerald-100 text-sm">
                        Questions? Contact us for personalized assistance
                    </p>
                </div>
            </div>

        </div>
    </div>

    @push('styles')
    <style>
        .animate-float {
            animation: float 3s ease-in-out infinite;
        }
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }
        
        /* Smooth scrolling */
        html {
            scroll-behavior: smooth;
        }
        
        /* Custom gradient backgrounds */
        .bg-gradient-emerald {
            background: linear-gradient(135deg, #059669 0%, #047857 100%);
        }
        
        /* Hover effects for cards */
        .card-hover {
            transition: all 0.3s ease;
        }
        .card-hover:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
        }
    </style>
    @endpush
</x-app-layout>
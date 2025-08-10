<x-app-layout>
<x-slot name="header">
        <div class="flex justify-between items-center">
            <div class="space-y-2">
                <h2 class="font-bold text-2xl text-gray-900 leading-tight tracking-tight">
                    PRE-HIKE SELF-ASSESSMENT
                </h2>
                <h1 class="text-gray-600 text-lg font-medium flex items-center gap-2">
                    <span class="inline-flex items-center justify-center w-6 h-6 bg-blue-100 text-blue-800 text-sm font-bold rounded-full">1</span>
                    Gear Checklist
                </h1>
            </div>
        </div>
    </x-slot>
    
    <div class="py-8 px-4 sm:px-6 lg:px-8 max-w-6xl mx-auto">
        <form action="{{ route('assessment.gear.store') }}" method="POST" class="space-y-4">
            @csrf
            
            <!-- Progress Indicator -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 mb-6">
                <div class="flex items-center justify-between text-sm text-gray-600 mb-2">
                    <span>Progress</span>
                    <span id="progress-text">0 of 11 items checked</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div id="progress-bar" class="bg-gradient-to-r from-blue-500 to-green-500 h-2 rounded-full transition-all duration-300" style="width: 0%"></div>
                </div>
            </div>

            <!-- Header Card -->
            <div class="bg-gradient-to-r from-blue-100 to-indigo-100 rounded-xl p-6 border border-blue-100">
                <div class="flex items-start gap-4">
                    <div class="p-3 bg-blue-100 rounded-lg flex-shrink-0">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-xl font-bold text-gray-800 mb-2">Essential Hiking Gear</h3>
                        <p class="text-blue-700 text-sm mb-4">Check off each item you have prepared for your hike</p>
                        
                        <!-- Info Section -->
                        <div class="bg-white/60 rounded-lg p-4 border border-green-100">
                            <div class="flex items-start gap-3">
                                <div class="p-1.5 bg-green-500 rounded-full flex-shrink-0 mt-0.5">
                                    <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div class="text-xs text-gray-700 leading-relaxed">
                                    <p class="font-medium text-gray-800 mb-1">Gear Readiness Review</p>
                                    <p>Your responses help us confirm that you're properly equipped for the trail. This gear assessment aligns with adventure tourism safety standards to reduce risks and ensure a safe, prepared, and enjoyable hiking experience.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Gear Items -->
            <div class="space-y-3">
                <!-- Backpack -->
                <div class="gear-item bg-white rounded-xl shadow-sm border border-gray-200 hover:shadow-md transition-all duration-200">
                    <div class="p-4">
                        <label class="flex items-center cursor-pointer">
                            <div class="flex items-center gap-4 flex-1">
                                <div class="p-2 bg-orange-50 rounded-lg">
                                    <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <h4 class="font-semibold text-gray-800">Backpack</h4>
                                    <p class="text-sm text-gray-500 mb-1">Waterproof or with dry bag</p>
                                </div>
                            </div>
                            <input type="checkbox" name="backpack" 
                                class="gear-checkbox w-5 h-5 text-green-600 border-2 border-gray-300 rounded focus:ring-green-500 focus:ring-2 transition-all duration-150">
                        </label>
                        <div class="mt-3 pl-12">
                            <div class="bg-orange-50 rounded-lg p-3 text-xs text-gray-700">
                                <p class="font-medium text-orange-800 mb-1">💡 Safety Tip:</p>
                                <p>Choose a backpack that's 20-30% of your body weight when loaded. Waterproof protection prevents gear failure in wet conditions - a critical safety requirement per ISO 21101 risk management protocols.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Water -->
                <div class="gear-item bg-white rounded-xl shadow-sm border border-gray-200 hover:shadow-md transition-all duration-200">
                    <div class="p-4">
                        <label class="flex items-center cursor-pointer">
                            <div class="flex items-center gap-4 flex-1">
                                <div class="p-2 bg-blue-50 rounded-lg">
                                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <h4 class="font-semibold text-gray-800">Water</h4>
                                    <p class="text-sm text-gray-500 mb-1">2L minimum</p>
                                </div>
                            </div>
                            <input type="checkbox" name="water" 
                                class="gear-checkbox w-5 h-5 text-green-600 border-2 border-gray-300 rounded focus:ring-green-500 focus:ring-2 transition-all duration-150">
                        </label>
                        <div class="mt-3 pl-12">
                            <div class="bg-blue-50 rounded-lg p-3 text-xs text-gray-700">
                                <p class="font-medium text-blue-800 mb-1">💧 Hydration Critical:</p>
                                <p>Dehydration is a leading cause of hiking emergencies. The 2L minimum follows international adventure tourism safety protocols - you'll need 0.5L per hour of hiking plus emergency reserves.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Food -->
                <div class="gear-item bg-white rounded-xl shadow-sm border border-gray-200 hover:shadow-md transition-all duration-200">
                    <div class="p-4">
                        <label class="flex items-center cursor-pointer">
                            <div class="flex items-center gap-4 flex-1">
                                <div class="p-2 bg-green-50 rounded-lg">
                                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 15.546c-.523 0-1.046.151-1.5.454a2.704 2.704 0 01-3 0 2.704 2.704 0 00-3 0 2.704 2.704 0 01-3 0 2.704 2.704 0 00-3 0 2.704 2.704 0 01-3 0A2.718 2.718 0 003 15.546V9c0-.546.454-1 1-1h16c.546 0 1 .454 1 1v6.546zM7 14c0-.546.454-1 1-1h8c.546 0 1 .454 1 1" />
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <h4 class="font-semibold text-gray-800">Food</h4>
                                    <p class="text-sm text-gray-500 mb-1">High-energy: nuts, dried mangoes, rice cakes</p>
                                </div>
                            </div>
                            <input type="checkbox" name="food" 
                                class="gear-checkbox w-5 h-5 text-green-600 border-2 border-gray-300 rounded focus:ring-green-500 focus:ring-2 transition-all duration-150">
                        </label>
                        <div class="mt-3 pl-12">
                            <div class="bg-green-50 rounded-lg p-3 text-xs text-gray-700">
                                <p class="font-medium text-green-800 mb-1">🍎 Energy Management:</p>
                                <p>Maintain blood sugar levels with complex carbs and proteins. Pack 200-300 calories per hour of hiking. Quick-energy foods prevent fatigue-related accidents and maintain decision-making capacity.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- First Aid Kit -->
                <div class="gear-item bg-white rounded-xl shadow-sm border border-gray-200 hover:shadow-md transition-all duration-200">
                    <div class="p-4">
                        <label class="flex items-center cursor-pointer">
                            <div class="flex items-center gap-4 flex-1">
                                <div class="p-2 bg-red-50 rounded-lg">
                                    <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <h4 class="font-semibold text-gray-800">First-Aid Kit</h4>
                                    <p class="text-sm text-gray-500 mb-1">Bandages, antiseptic, meds for diarrhea/allergies</p>
                                </div>
                            </div>
                            <input type="checkbox" name="first_aid_kit" 
                                class="gear-checkbox w-5 h-5 text-green-600 border-2 border-gray-300 rounded focus:ring-green-500 focus:ring-2 transition-all duration-150">
                        </label>
                        <div class="mt-3 pl-12">
                            <div class="bg-red-50 rounded-lg p-3 text-xs text-gray-700">
                                <p class="font-medium text-red-800 mb-1">🏥 Emergency Preparedness:</p>
                                <p>ISO 21101 mandates emergency response capabilities. Your first-aid kit should include: wound care, pain relief, anti-diarrheal medication, antihistamines, and any personal medications.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Headlight/Flashlamp -->
                <div class="gear-item bg-white rounded-xl shadow-sm border border-gray-200 hover:shadow-md transition-all duration-200">
                    <div class="p-4">
                        <label class="flex items-center cursor-pointer">
                            <div class="flex items-center gap-4 flex-1">
                                <div class="p-2 bg-yellow-50 rounded-lg">
                                    <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <h4 class="font-semibold text-gray-800">Headlight/Flashlamp</h4>
                                    <p class="text-sm text-gray-500 mb-1">No flashlight? Use phone flashlight + power bank, or buy a cheap candle from sari-sari store</p>
                                </div>
                            </div>
                            <input type="checkbox" name="headlight_flashlamp" 
                                class="gear-checkbox w-5 h-5 text-green-600 border-2 border-gray-300 rounded focus:ring-green-500 focus:ring-2 transition-all duration-150">
                        </label>
                        <div class="mt-3 pl-12">
                            <div class="bg-yellow-50 rounded-lg p-3 text-xs text-gray-700">
                                <p class="font-medium text-yellow-800 mb-1">🔦 Communication Protocol:</p>
                                <p>Light sources serve dual purposes: navigation safety and emergency signaling. ISO standards require reliable illumination for participant safety and rescue visibility in low-light conditions.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Whistle -->
                <div class="gear-item bg-white rounded-xl shadow-sm border border-gray-200 hover:shadow-md transition-all duration-200">
                    <div class="p-4">
                        <label class="flex items-center cursor-pointer">
                            <div class="flex items-center gap-4 flex-1">
                                <div class="p-2 bg-purple-50 rounded-lg">
                                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z" />
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <h4 class="font-semibold text-gray-800">Whistle</h4>
                                    <p class="text-sm text-gray-500 mb-1">No whistle? Use a metal spoon to bang on your bottle</p>
                                </div>
                            </div>
                            <input type="checkbox" name="whistle" 
                                class="gear-checkbox w-5 h-5 text-green-600 border-2 border-gray-300 rounded focus:ring-green-500 focus:ring-2 transition-all duration-150">
                        </label>
                        <div class="mt-3 pl-12">
                            <div class="bg-purple-50 rounded-lg p-3 text-xs text-gray-700">
                                <p class="font-medium text-purple-800 mb-1">📯 Emergency Communication:</p>
                                <p>Sound travels farther than voice calls in outdoor environments. Three sharp blasts is the international distress signal - a core requirement in ISO 21101 emergency communication protocols.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Extra Clothes -->
                <div class="gear-item bg-white rounded-xl shadow-sm border border-gray-200 hover:shadow-md transition-all duration-200">
                    <div class="p-4">
                        <label class="flex items-center cursor-pointer">
                            <div class="flex items-center gap-4 flex-1">
                                <div class="p-2 bg-indigo-50 rounded-lg">
                                    <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <h4 class="font-semibold text-gray-800">Extra Clothes</h4>
                                    <p class="text-sm text-gray-500 mb-1">Quick dry fabric</p>
                                </div>
                            </div>
                            <input type="checkbox" name="extra_clothes" 
                                class="gear-checkbox w-5 h-5 text-green-600 border-2 border-gray-300 rounded focus:ring-green-500 focus:ring-2 transition-all duration-150">
                        </label>
                        <div class="mt-3 pl-12">
                            <div class="bg-indigo-50 rounded-lg p-3 text-xs text-gray-700">
                                <p class="font-medium text-indigo-800 mb-1">🧥 Temperature Regulation:</p>
                                <p>Weather can change rapidly in mountainous terrain. Dry extra clothes prevent hypothermia and maintain core body temperature. Synthetic materials dry faster than cotton and retain warmth when wet.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Rain Gear -->
                <div class="gear-item bg-white rounded-xl shadow-sm border border-gray-200 hover:shadow-md transition-all duration-200">
                    <div class="p-4">
                        <label class="flex items-center cursor-pointer">
                            <div class="flex items-center gap-4 flex-1">
                                <div class="p-2 bg-cyan-50 rounded-lg">
                                    <svg class="w-5 h-5 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z" />
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <h4 class="font-semibold text-gray-800">Rain Gear</h4>
                                    <p class="text-sm text-gray-500 mb-1">No raincoat? Use large plastic trash bag as poncho</p>
                                </div>
                            </div>
                            <input type="checkbox" name="rain_gear" 
                                class="gear-checkbox w-5 h-5 text-green-600 border-2 border-gray-300 rounded focus:ring-green-500 focus:ring-2 transition-all duration-150">
                        </label>
                        <div class="mt-3 pl-12">
                            <div class="bg-cyan-50 rounded-lg p-3 text-xs text-gray-700">
                                <p class="font-medium text-cyan-800 mb-1">☔ Weather Protection:</p>
                                <p>Wet clothing leads to rapid heat loss and hypothermia risk. Waterproof protection keeps your core dry and maintains body temperature. Even a plastic bag can be life-saving in unexpected storms.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sun Protection -->
                <div class="gear-item bg-white rounded-xl shadow-sm border border-gray-200 hover:shadow-md transition-all duration-200">
                    <div class="p-4">
                        <label class="flex items-center cursor-pointer">
                            <div class="flex items-center gap-4 flex-1">
                                <div class="p-2 bg-amber-50 rounded-lg">
                                    <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <h4 class="font-semibold text-gray-800">Sun Protection</h4>
                                    <p class="text-sm text-gray-500 mb-1">Cap, sunscreen, sunglasses</p>
                                </div>
                            </div>
                            <input type="checkbox" name="sun_protection" 
                                class="gear-checkbox w-5 h-5 text-green-600 border-2 border-gray-300 rounded focus:ring-green-500 focus:ring-2 transition-all duration-150">
                        </label>
                        <div class="mt-3 pl-12">
                            <div class="bg-amber-50 rounded-lg p-3 text-xs text-gray-700">
                                <p class="font-medium text-amber-800 mb-1">☀️ UV Protection Critical:</p>
                                <p>High altitude increases UV exposure by 10-12% per 1000m. Sunburn and heat exhaustion can quickly become medical emergencies. Protect eyes, face, and exposed skin at all times during daylight hiking.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Map/Compass -->
                <div class="gear-item bg-white rounded-xl shadow-sm border border-gray-200 hover:shadow-md transition-all duration-200">
                    <div class="p-4">
                        <label class="flex items-center cursor-pointer">
                            <div class="flex items-center gap-4 flex-1">
                                <div class="p-2 bg-teal-50 rounded-lg">
                                    <svg class="w-5 h-5 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <h4 class="font-semibold text-gray-800">Map/Compass</h4>
                                    <p class="text-sm text-gray-500 mb-1">Offline map downloaded on phone + portable charger</p>
                                </div>
                            </div>
                            <input type="checkbox" name="map_compass" 
                                class="gear-checkbox w-5 h-5 text-green-600 border-2 border-gray-300 rounded focus:ring-green-500 focus:ring-2 transition-all duration-150">
                        </label>
                        <div class="mt-3 pl-12">
                            <div class="bg-teal-50 rounded-lg p-3 text-xs text-gray-700">
                                <p class="font-medium text-teal-800 mb-1">🧭 Navigation Essential:</p>
                                <p>Getting lost is a major hiking emergency cause. Offline maps work without cell service. Know your route, identify landmarks, and have backup navigation methods. GPS batteries can fail when you need them most.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Powerbank/Batteries -->
                <div class="gear-item bg-white rounded-xl shadow-sm border border-gray-200 hover:shadow-md transition-all duration-200">
                    <div class="p-4">
                        <label class="flex items-center cursor-pointer">
                            <div class="flex items-center gap-4 flex-1">
                                <div class="p-2 bg-emerald-50 rounded-lg">
                                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h18v5h-18v-5zm0 7h18v11h-18v-11zm4-5v3m4-3v3m4-3v3" />
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <h4 class="font-semibold text-gray-800">Powerbank/Batteries</h4>
                                    <p class="text-sm text-gray-500 mb-1">Essential for phone and devices</p>
                                </div>
                            </div>
                            <input type="checkbox" name="powerbank_batteries" 
                                class="gear-checkbox w-5 h-5 text-green-600 border-2 border-gray-300 rounded focus:ring-green-500 focus:ring-2 transition-all duration-150">
                        </label>
                        <div class="mt-3 pl-12">
                            <div class="bg-emerald-50 rounded-lg p-3 text-xs text-gray-700">
                                <p class="font-medium text-emerald-800 mb-1">🔋 Power Security:</p>
                                <p>Your phone is your primary emergency communication device, GPS navigator, and flashlight backup. Cold weather drains batteries 50% faster. Carry enough power capacity for your entire hike plus emergency reserves.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <!-- Action Section -->
            <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-6 mt-8">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                    <div class="flex-1">
                        <p class="text-sm text-gray-600">
                            <span id="completion-message">Complete your gear checklist to proceed to the fitness assessment.</span>
                        </p>
                    </div>
                    <div class="flex-shrink-0">
                        <button type="submit" 
                            class="inline-flex items-center px-8 py-4 text-white font-semibold text-base rounded-xl shadow-lg transition-all duration-200 hover:shadow-xl hover:scale-105 focus:outline-none focus:ring-4 focus:ring-yellow-200 disabled:opacity-50 disabled:cursor-not-allowed"
                            style="background: linear-gradient(135deg, #e3a746 0%, #f4c430 100%);">
                            <span>Next: Fitness Assessment</span>
                            <svg class="ml-2 w-5 h-5 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <style>
        /* Refined styling for Gear Checklist */
        .gear-item {
            transition: box-shadow 0.2s, border-color 0.2s, background 0.2s;
        }
        .gear-item:hover {
            box-shadow: 0 4px 16px 0 rgba(59,130,246,0.08);
            border-color: #60a5fa;
            background: linear-gradient(90deg, #f0f9ff 0%, #f3f4f6 100%);
        }
        .gear-checkbox:focus {
            outline: none;
            box-shadow: 0 0 0 2px #f4c430;
            border-color: #f4c430;
        }
        .gear-item .font-semibold {
            letter-spacing: 0.02em;
        }
        .gear-item .text-sm {
            font-size: 0.95rem;
        }
        .gear-item .bg-white {
            background: linear-gradient(90deg, #f8fafc 0%, #f3f4f6 100%);
        }
        .gear-item .rounded-xl {
            border-radius: 1rem;
        }
        .gear-item .p-4 {
            padding: 1.25rem;
        }
        .gear-item .pl-12 {
            padding-left: 3rem;
        }
        .gear-item .bg-green-50 {
            background: linear-gradient(90deg, #e6fffa 0%, #f0fff4 100%);
        }
        .gear-item .ring-green-300 {
            box-shadow: 0 0 0 2px #6ee7b7;
        }
        .gear-item .border-green-300 {
            border-color: #6ee7b7 !important;
        }
        .gear-item .hover\:shadow-md:hover {
            box-shadow: 0 6px 24px 0 rgba(59,130,246,0.12);
        }
        /* Progress bar refinement */
        #progress-bar {
            background: linear-gradient(90deg, #3b82f6 0%, #34d399 100%);
            box-shadow: 0 2px 8px 0 rgba(59,130,246,0.10);
        }
        /* Button refinement */
        button[type="submit"] {
            font-size: 1.05rem;
            letter-spacing: 0.01em;
            box-shadow: 0 4px 16px 0 rgba(244,196,48,0.10);
        }
        button[type="submit"]:hover {
            filter: brightness(1.08);
            box-shadow: 0 8px 32px 0 rgba(244,196,48,0.18);
        }
        /* Info card refinement */
        .bg-white\/60 {
            background: rgba(255,255,255,0.85);
            border-radius: 0.75rem;
            border: 1px solid #dbeafe;
        }
        /* Standards card refinement */
        .bg-gray-50 {
            background: linear-gradient(90deg, #f9fafb 0%, #f3f4f6 100%);
            border-radius: 0.75rem;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const checkboxes = document.querySelectorAll('.gear-checkbox');
            const progressBar = document.getElementById('progress-bar');
            const progressText = document.getElementById('progress-text');
            const completionMessage = document.getElementById('completion-message');
            const totalItems = checkboxes.length;

            function updateProgress() {
                const checkedItems = document.querySelectorAll('.gear-checkbox:checked').length;
                const percentage = Math.round((checkedItems / totalItems) * 100);
                
                progressBar.style.width = percentage + '%';
                progressText.textContent = `${checkedItems} of ${totalItems} items checked`;
                
                // Update completion message
                if (checkedItems === 0) {
                    completionMessage.textContent = 'Complete your gear checklist to proceed to the fitness assessment.';
                } else if (checkedItems < totalItems) {
                    completionMessage.textContent = `Great progress! You have ${totalItems - checkedItems} items left to check.`;
                } else {
                    completionMessage.textContent = '🎉 Excellent! All gear items checked. Ready for the next step!';
                }
            }

            // Add visual feedback for checked items
            checkboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    const gearItem = this.closest('.gear-item');
                    if (this.checked) {
                        gearItem.classList.add('ring-2', 'ring-green-300', 'bg-green-50', 'border-green-300');
                        gearItem.classList.remove('border-gray-200');
                    } else {
                        gearItem.classList.remove('ring-2', 'ring-green-300', 'bg-green-50', 'border-green-300');
                        gearItem.classList.add('border-gray-200');
                    }
                    updateProgress();
                });
            });

            // Initial progress update
            updateProgress();
        });
    </script>
</x-app-layout>
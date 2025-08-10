<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div class="space-y-2">
                <h2 class="font-bold text-2xl text-gray-900 leading-tight tracking-tight">
                    PRE-HIKE SELF-ASSESSMENT
                </h2>
                <h1 class="text-gray-600 text-lg font-medium flex items-center gap-2">
                    <span class="inline-flex items-center justify-center w-6 h-6 bg-blue-100 text-blue-800 text-sm font-bold rounded-full">4</span>
                    Date and Weather
                </h1>
            </div>
        </div>
    </x-slot>

    <div class="py-8 px-4 sm:px-6 lg:px-8 max-w-6xl mx-auto">
        <form action="{{ route('assessment.weather.store') }}" method="POST" class="space-y-4">
            @csrf

            @php
                $radioClasses = "w-5 h-5 text-yellow-500 border-gray-300 focus:ring-yellow-400";
                $labelClasses = "flex items-center space-x-3 cursor-pointer hover:bg-yellow-50 rounded px-2 py-1.5";
            @endphp

            <!-- Header Card -->
            <div class="bg-gradient-to-r from-blue-50 to-sky-50 rounded-xl p-6 border border-blue-100">
                <div class="flex items-start gap-4">
                    <div class="p-3 bg-blue-100 rounded-lg flex-shrink-0">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z" />
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-xl font-bold text-gray-800 mb-2">Weather & Date Assessment</h3>
                        <p class="text-blue-700 text-sm mb-4">Plan your hike with proper weather awareness and preparation</p>
                        
                        <!-- Info Section -->
                        <div class="bg-white/60 rounded-lg p-4 border border-blue-100">
                            <div class="flex items-start gap-3">
                                <div class="p-1.5 bg-blue-500 rounded-full flex-shrink-0 mt-0.5">
                                    <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div class="text-xs text-gray-700 leading-relaxed">
                                    <p class="font-medium text-gray-800 mb-1">Weather Safety Planning</p>
                                    <p>Mountain weather can change rapidly and differs significantly from valley conditions. Your awareness and preparation for weather conditions is crucial for a safe hiking experience.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Questions -->
            <div class="space-y-3">

                <!-- Hike Date -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 hover:shadow-md transition-all duration-200">
                    <div class="p-4">
                        <div class="flex items-start gap-4 mb-4">
                            <div class="p-2 bg-green-50 rounded-lg">
                                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <div class="flex-1">
                                <label for="hike_date" class="block font-semibold text-gray-800 text-base mb-2">Planned Hike Date:</label>
                            </div>
                        </div>
                        <div class="pl-11">
                            <input type="date" id="hike_date" name="hike_date" 
                                   class="w-full border-2 border-gray-300 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-200 hover:border-gray-400">
                        </div>
                    </div>
                </div>

                <!-- Weather Notification -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 hover:shadow-md transition-all duration-200">
                    <div class="p-4">
                        <div class="flex items-start gap-4 mb-4">
                            <div class="p-2 bg-amber-50 rounded-lg">
                                <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM4.01 9h12.99M4.01 15h4.99M4.01 21h4.99" />
                                </svg>
                            </div>
                            <div class="flex-1">
                                <label class="block font-semibold text-gray-800 text-base leading-relaxed">
                                    Do you want to receive a weather update 24 hours before your selected hike date?
                                </label>
                            </div>
                        </div>
                        <div class="pl-11">
                            <div class="flex gap-6">
                                <label class="flex items-center space-x-2">
                                    <input type="radio" name="want_weather_notification" value="yes"
                                        class="appearance-none w-5 h-5 rounded-full border-2 border-gray-400 checked:bg-amber-500 checked:border-amber-500 focus:outline-none transition duration-150">
                                    <span class="text-sm text-gray-700">Yes</span>
                                </label>
                                <label class="flex items-center space-x-2">
                                    <input type="radio" name="want_weather_notification" value="no"
                                        class="appearance-none w-5 h-5 rounded-full border-2 border-gray-400 checked:bg-amber-500 checked:border-amber-500 focus:outline-none transition duration-150">
                                    <span class="text-sm text-gray-700">No</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Weather Forecast Knowledge and Preparation -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 hover:shadow-md transition-all duration-200">
                    <div class="p-4">
                        <div class="flex items-start gap-4 mb-4">
                            <div class="p-2 bg-blue-50 rounded-lg">
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                </svg>
                            </div>
                            <label class="block font-semibold text-gray-800 text-base leading-relaxed">
                                Do you know how to check accurate mountain weather forecasts specific to your hiking area?
                            </label>
                        </div>
                        <div class="pl-11">
                            <x-likert-scale name="know_mountain_forecast" />
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-200 hover:shadow-md transition-all duration-200">
                    <div class="p-4">
                        <div class="flex items-start gap-4 mb-4">
                            <div class="p-2 bg-indigo-50 rounded-lg">
                                <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                                </svg>
                            </div>
                            <label class="block font-semibold text-gray-800 text-base leading-relaxed">
                                Have you checked the weather forecast for your hike date and the day before?
                            </label>
                        </div>
                        <div class="pl-11">
                            <x-likert-scale name="checked_forecast" />
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-200 hover:shadow-md transition-all duration-200">
                    <div class="p-4">
                        <div class="flex items-start gap-4 mb-4">
                            <div class="p-2 bg-purple-50 rounded-lg">
                                <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3l6 6 6-6" />
                                </svg>
                            </div>
                            <label class="block font-semibold text-gray-800 text-base leading-relaxed">
                                Are you aware that mountain weather can change rapidly and differ significantly from valley weather?
                            </label>
                        </div>
                        <div class="pl-11">
                            <x-likert-scale name="aware_mountain_weather_changes" />
                        </div>
                    </div>
                </div>

               

                <!-- Rain and Precipitation Preparedness -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 hover:shadow-md transition-all duration-200">
                    <div class="p-4">
                        <div class="flex items-start gap-4 mb-4">
                            <div class="p-2 bg-cyan-50 rounded-lg">
                                <svg class="w-5 h-5 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z" />
                                </svg>
                            </div>
                            <label class="block font-semibold text-gray-800 text-base leading-relaxed">
                                Do you have waterproof rain gear (jacket and pants) suitable for hiking?
                            </label>
                        </div>
                        <div class="pl-11">
                            <x-likert-scale name="have_rain_gear" />
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-200 hover:shadow-md transition-all duration-200">
                    <div class="p-4">
                        <div class="flex items-start gap-4 mb-4">
                            <div class="p-2 bg-orange-50 rounded-lg">
                                <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                            </div>
                            <label class="block font-semibold text-gray-800 text-base leading-relaxed">
                                Are you prepared to turn back if weather conditions become unsafe during your hike?
                            </label>
                        </div>
                        <div class="pl-11">
                            <x-likert-scale name="willing_turn_back" />
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-200 hover:shadow-md transition-all duration-200">
                    <div class="p-4">
                        <div class="flex items-start gap-4 mb-4">
                            <div class="p-2 bg-red-50 rounded-lg">
                                <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                </svg>
                            </div>
                            <label class="block font-semibold text-gray-800 text-base leading-relaxed">
                                Do you know how to identify signs of approaching storms or dangerous weather while hiking?
                            </label>
                        </div>
                        <div class="pl-11">
                            <x-likert-scale name="identify_storm_signs" />
                        </div>
                    </div>
                </div>

                <!-- Temperature and Layering -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 hover:shadow-md transition-all duration-200">
                    <div class="p-4">
                        <div class="flex items-start gap-4 mb-4">
                            <div class="p-2 bg-emerald-50 rounded-lg">
                                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                </svg>
                            </div>
                            <label class="block font-semibold text-gray-800 text-base leading-relaxed">
                                Do you understand how to dress in layers for changing mountain temperatures?
                            </label>
                        </div>
                        <div class="pl-11">
                            <x-likert-scale name="understand_layering" />
                        </div>
                    </div>
                </div>


                <!-- Backup Plans -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 hover:shadow-md transition-all duration-200">
                    <div class="p-4">
                        <div class="flex items-start gap-4 mb-4">
                            <div class="p-2 bg-slate-50 rounded-lg">
                                <svg class="w-5 h-5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                            <div class="flex-1">
                                <label for="weather_backup_plan" class="block font-semibold text-gray-800 text-base mb-2">
                                    What is your backup plan if weather conditions become unsafe?
                                </label>
                            </div>
                        </div>
                        <div class="pl-11">
                            <textarea name="weather_backup_plan" id="weather_backup_plan" rows="3" 
                                      class="w-full border-2 border-gray-300 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-slate-500 focus:border-slate-500 transition-all duration-200 hover:border-gray-400"
                                      placeholder="Describe your escape routes, shelter options, or alternative plans"></textarea>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Action Section -->
            <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-6 mt-8">
                <div class="flex justify-between items-center gap-4 flex-wrap">
                    <!-- Back Button -->
                    <div class="flex-shrink-0">
                        <a href="{{ route('assessment.health') }}" 
                            class="inline-flex items-center px-8 py-4 text-white font-semibold text-base rounded-xl shadow-lg transition-all duration-200 hover:shadow-xl hover:scale-105 focus:outline-none focus:ring-4 focus:ring-yellow-200"
                            style="background: linear-gradient(135deg, #e3a746 0%, #f4c430 100%);">
                            <svg class="mr-2 w-5 h-5 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                            </svg>
                            <span>Back: Health Screen</span>
                        </a>
                    </div>

                    <!-- Message -->
                    <div class="flex-1 text-center">
                        <p class="text-sm text-gray-600">
                            Complete your weather assessment to proceed to the emergency preparedness section.
                        </p>
                    </div>

                    <!-- Next Button -->
                    <div class="flex-shrink-0">
                        <button type="submit" 
                            class="inline-flex items-center px-8 py-4 text-white font-semibold text-base rounded-xl shadow-lg transition-all duration-200 hover:shadow-xl hover:scale-105 focus:outline-none focus:ring-4 focus:ring-yellow-200"
                            style="background: linear-gradient(135deg, #e3a746 0%, #f4c430 100%);">
                            <span>Next: Emergency</span>
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
    textarea {
        resize: none;
        width: 80%;
        min-height: 50px;
    }
    </style>

</x-app-layout>
<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div class="space-y-2">
                <h2 class="font-bold text-2xl text-gray-900 leading-tight tracking-tight">
                    PRE-HIKE SELF-ASSESSMENT
                </h2>
                <h1 class="text-gray-600 text-lg font-medium flex items-center gap-2">
                    <span class="inline-flex items-center justify-center w-6 h-6 bg-blue-100 text-blue-800 text-sm font-bold rounded-full">2</span>
                    Fitness History
                </h1>
            </div>
        </div>
    </x-slot>

    <div class="py-8 px-4 sm:px-6 lg:px-8 max-w-6xl mx-auto">
        <form action="{{ route('assessment.fitness.store') }}" method="POST" class="space-y-4">
            @csrf

            @php
                $radioClasses = "env-radio w-5 h-5 border-2 border-yellow-400 text-yellow-500 rounded-full focus:ring-2 focus:ring-yellow-400 transition-all duration-150";
                $labelClasses = "flex items-center space-x-3 cursor-pointer hover:bg-yellow-50 rounded px-2 py-1.5";
            @endphp

            <!-- Header Card -->
            <div class="bg-gradient-to-r from-green-50 to-emerald-50 rounded-xl p-6 border border-green-100">
                <div class="flex items-start gap-4">
                    <div class="p-3 bg-green-100 rounded-lg flex-shrink-0">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </div>

                    
                    <div class="flex-1">
                        <h3 class="text-xl font-bold text-gray-800 mb-2">Fitness & Experience Assessment</h3>
                        <p class="text-green-700 text-sm mb-4">Help us understand your physical readiness and hiking experience</p>
                        
                        <!-- Info Section -->
                        <div class="bg-white/60 rounded-lg p-4 border border-green-100">
                            <div class="flex items-start gap-3">
                                <div class="p-1.5 bg-green-500 rounded-full flex-shrink-0 mt-0.5">
                                    <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div class="text-xs text-gray-700 leading-relaxed">
                                    <p class="font-medium text-gray-800 mb-1">Honest Self-Assessment</p>
                                    <p>Your responses help us ensure you're matched with appropriate hiking challenges. This assessment follows adventure tourism safety standards to minimize risk and maximize your enjoyment.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Questions -->
            <div class="space-y-3">

                <!-- Hiking Experience (Frequency) -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 hover:shadow-md transition-all duration-200">
                    <div class="p-4">
                        <div class="flex items-start gap-4 mb-4">
                            <div class="p-2 bg-blue-50 rounded-lg">
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3l6 6 6-6" />
                                </svg>
                            </div>
                            <label class="block font-semibold text-gray-800 text-base leading-relaxed">
                                How often have you gone hiking before?
                            </label>
                        </div>
                        <div class="pl-11">
                            <x-likert-scale name="experienced_hiking" type="frequency" :radioClasses="$radioClasses" :labelClasses="$labelClasses" />
                        </div>
                    </div>
                </div>

                <!-- Last Hike Time (Dropdown) -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 hover:shadow-md transition-all duration-200">
                    <div class="p-4">
                        <div class="flex items-start gap-4 mb-4">
                            <div class="p-2 bg-purple-50 rounded-lg">
                                <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <label class="block font-semibold text-gray-800 text-base">When was your last hike?</label>
                        </div>
                        <div class="pl-11">
                            <select name="last_hike" class="w-full border-2 border-gray-300 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 hover:border-gray-400" required>
                                <option disabled selected>Select your last hiking experience</option>
                                <option value="within_past_month">Within the past month</option>
                                <option value="within_past_6_months">Within the past 6 months</option>
                                <option value="over_a_year_ago">Over a year ago</option>
                                <option value="never">Never</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Difficulty of Last Hike (Dropdown) -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 hover:shadow-md transition-all duration-200">
                    <div class="p-4">
                        <div class="flex items-start gap-4 mb-4">
                            <div class="p-2 bg-orange-50 rounded-lg">
                                <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                                </svg>
                            </div>
                            <label class="block font-semibold text-gray-800 text-base">What was the difficulty of your last hike?</label>
                        </div>
                        <div class="pl-11">
                            <select name="last_hike_difficulty" class="w-full border-2 border-gray-300 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 hover:border-gray-400" required>
                                <option disabled selected>Select difficulty level</option>
                                <option value="easy">Easy</option>
                                <option value="moderate">Moderate</option>
                                <option value="challenging">Challenging</option>
                                <option value="difficult">Difficult</option>
                                <option value="extreme">Extreme</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Jogging Confidence -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 hover:shadow-md transition-all duration-200">
                    <div class="p-4">
                        <div class="flex items-start gap-4 mb-4">
                            <div class="p-2 bg-green-50 rounded-lg">
                                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 4v12l-4-2-4 2V4" />
                                </svg>
                            </div>
                            <label class="block font-semibold text-gray-800 text-base leading-relaxed">
                                I am confident I can jog continuously for 1 km.
                            </label>
                        </div>
                        <div class="pl-11">
                            <x-likert-scale name="jog_1km" :radioClasses="$radioClasses" :labelClasses="$labelClasses" />
                        </div>
                    </div>
                </div>


                <!-- Stairs Capability -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 hover:shadow-md transition-all duration-200">
                    <div class="p-4">
                        <div class="flex items-start gap-4 mb-4">
                            <div class="p-2 bg-indigo-50 rounded-lg">
                                <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12" />
                                </svg>
                            </div>
                            <label class="block font-semibold text-gray-800 text-base leading-relaxed">
                                I can climb 3 or more flights of stairs without resting.
                            </label>
                        </div>
                        <div class="pl-11">
                            <x-likert-scale name="stairs_no_rest" :radioClasses="$radioClasses" :labelClasses="$labelClasses" />
                        </div>
                    </div>
                </div>

                <!-- Backpack Carrying -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 hover:shadow-md transition-all duration-200">
                    <div class="p-4">
                        <div class="flex items-start gap-4 mb-4">
                            <div class="p-2 bg-yellow-50 rounded-lg">
                                <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                </svg>
                            </div>
                            <label class="block font-semibold text-gray-800 text-base leading-relaxed">
                                I can comfortably carry a 5 kg backpack for at least 2 hours.
                            </label>
                        </div>
                        <div class="pl-11">
                            <x-likert-scale name="carry_5kg" :radioClasses="$radioClasses" :labelClasses="$labelClasses" />
                        </div>
                    </div>
                </div>

                <!-- Joint Pain -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 hover:shadow-md transition-all duration-200">
                    <div class="p-4">
                        <div class="flex items-start gap-4 mb-4">
                            <div class="p-2 bg-pink-50 rounded-lg">
                                <svg class="w-5 h-5 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                            </div>
                            <label class="block font-semibold text-gray-800 text-base leading-relaxed">
                                I experience knee or joint pain during uphill or downhill walks.
                            </label>
                        </div>
                        <div class="pl-11">
                            <x-likert-scale name="joint_pain" :radioClasses="$radioClasses" :labelClasses="$labelClasses" />
                        </div>
                    </div>
                </div>

                <!-- Chronic Injuries -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 hover:shadow-md transition-all duration-200">
                    <div class="p-4">
                        <div class="flex items-start gap-4 mb-4">
                            <div class="p-2 bg-red-50 rounded-lg">
                                <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                            </div>
                            <label class="block font-semibold text-gray-800 text-base leading-relaxed">
                                I have chronic injuries or physical limitations.
                            </label>
                        </div>
                        <div class="pl-11">
                            <x-likert-scale name="chronic_injuries" :radioClasses="$radioClasses" :labelClasses="$labelClasses" />
                        </div>
                    </div>
                </div>


                <!-- High Altitude Experience -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 hover:shadow-md transition-all duration-200">
                    <div class="p-4">
                        <div class="flex items-start gap-4 mb-4">
                            <div class="p-2 bg-gray-50 rounded-lg">
                                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3l6 6 6-6" />
                                </svg>
                            </div>
                            <label class="block font-semibold text-gray-800 text-base leading-relaxed">
                                How often have you hiked at altitudes over 2,000 meters?
                            </label>
                        </div>
                        <div class="pl-11">
                            <x-likert-scale name="high_altitude" type="frequency" :radioClasses="$radioClasses" :labelClasses="$labelClasses" />
                        </div>
                    </div>
                </div>

                

                <!-- Endurance Activities -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 hover:shadow-md transition-all duration-200">
                    <div class="p-4">
                        <div class="flex items-start gap-4 mb-4">
                            <div class="p-2 bg-emerald-50 rounded-lg">
                                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                </svg>
                            </div>
                            <label class="block font-semibold text-gray-800 text-base leading-relaxed">
                                How often have you completed physically demanding activities such as sports training or endurance challenges?
                            </label>
                        </div>
                        <div class="pl-11">
                            <x-likert-scale name="endurance_activity" type="frequency" :radioClasses="$radioClasses" :labelClasses="$labelClasses" />
                        </div>
                    </div>
                </div>

            </div>

            <!-- Action Section -->
            <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-6 mt-8">
                <div class="flex justify-between items-center gap-4 flex-wrap">
                    <!-- Back Button -->
                    <div class="flex-shrink-0">
                        <a href="{{ route('assessment.weather') }}"
                            class="env-btn inline-flex items-center px-8 py-4 font-semibold text-base rounded-xl shadow-lg transition-all duration-200"
                        >
                            <svg class="mr-2 w-5 h-5 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                            </svg>
                            <span>Back: Gear Checklist</span>
                        </a>
                    </div>
                    <!-- Message -->
                    <div class="flex-1 text-center">
                        <p class="text-sm text-gray-600">
                            Complete your fitness assessment to finish your pre-hike safety check.
                        </p>
                    </div>
                    <!-- Next Button -->
                    <div class="flex-shrink-0">
                        <button type="submit" 
                            class="env-btn inline-flex items-center px-8 py-4 font-semibold text-base rounded-xl shadow-lg transition-all duration-200"
                        >
                            <span>Next: Health Assessment</span>
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
        /* Button styling matches environment file */
        .env-btn {
            color: #fff !important;
            background: linear-gradient(135deg, #e3a746 0%, #f4c430 100%);
            box-shadow: 0 4px 16px 0 rgba(244,196,48,0.10);
            font-size: 1.05rem;
            letter-spacing: 0.01em;
            border: none;
        }
        .env-btn:hover, .env-btn:focus {
            filter: brightness(1.08);
            box-shadow: 0 8px 32px 0 rgba(244,196,48,0.18);
            outline: none;
        }
        .env-btn:active {
            filter: brightness(0.98);
        }
        /* Radio button styling matches environment file */
        .env-radio {
            accent-color: #f4c430;
            border-color: #e3a746;
            background-color: #fff;
            transition: box-shadow 0.2s;
        }
        .env-radio:focus {
            box-shadow: 0 0 0 2px #f4c430;
            outline: none;
        }
        /* Card refinement */
        .bg-white\/60 {
            background: rgba(255,255,255,0.85);
            border-radius: 0.75rem;
            border: 1px solid #d1fae5;
        }
        .bg-gradient-to-r {
            background: linear-gradient(90deg, #f0fdf4 0%, #ecfdf5 100%);
        }
        .rounded-xl {
            border-radius: 1rem;
        }
        .shadow-lg {
            box-shadow: 0 8px 32px 0 rgba(59,130,246,0.10);
        }
        /* Question card hover */
        .hover\:shadow-md:hover {
            box-shadow: 0 6px 24px 0 rgba(59,130,246,0.12);
        }
        /* Dropdown refinement */
        select {
            background: #f9fafb;
        }
    </style>
</x-app-layout>
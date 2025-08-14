<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div class="space-y-2">
                <h2 class="font-bold text-2xl text-gray-900 leading-tight tracking-tight">
                    PRE-HIKE SELF-ASSESSMENT
                </h2>
                <h1 class="text-gray-600 text-2xl font-medium flex items-center gap-2">
                    <span class="inline-flex items-center justify-center w-10 h-10 bg-red-100 text-red-800 text-xl font-bold rounded-full">5</span>
                    Emergencies
                </h1>
            </div>
        </div>
    </x-slot>

    <div class="py-8 px-4 sm:px-6 lg:px-8 max-w-6xl mx-auto">
        <form action="{{ route('assessment.emergency.store') }}" method="POST" class="space-y-4">
            @csrf

            @php
                $radioClasses = "env-radio w-6 h-6 border-2 border-yellow-400 text-yellow-500 rounded-full focus:ring-2 focus:ring-yellow-400 transition-all duration-150";
                $labelClasses = "flex items-center space-x-3 cursor-pointer hover:bg-yellow-50 rounded px-3 py-2 transition-all duration-150";
            @endphp

            <!-- Header Card -->
            <div class="bg-gradient-to-r from-red-50 to-orange-50 rounded-xl p-8 border border-red-100">
                <div class="flex items-start gap-4">
                    <div class="p-4 bg-red-100 rounded-lg flex-shrink-0">
                        <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-1.414 1.414A9 9 0 105.636 18.364l1.414-1.414A7 7 0 1116.95 7.05z"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-2xl font-bold text-gray-800 mb-3">Emergency Preparedness Assessment</h3>
                        <p class="text-red-700 text-lg mb-4">Be ready for emergencies—your safety depends on your preparation and knowledge.</p>
                        
                        <!-- Info Section -->
                        <div class="bg-white/60 rounded-lg p-5 border border-red-100">
                            <div class="flex items-start gap-3">
                                <div class="p-2 bg-red-500 rounded-full flex-shrink-0 mt-0.5">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <div class="text-base text-gray-700 leading-relaxed">
                                    <p class="font-medium text-gray-800 mb-2 text-base">Emergency Planning</p>
                                    <p class="text-base">Knowing what to do and having the right equipment can save lives in the outdoors. Review your emergency skills and gear before every hike.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Questions -->
            <div class="space-y-4">

                <!-- National Emergency Contacts -->
                <div class="bg-gradient-to-r from-red-100 to-pink-100 rounded-xl p-6 border border-red-100">
                    <div class="p-6">
                        <div class="flex items-start gap-4 mb-4">
                            <div class="p-3 bg-red-50 rounded-lg">
                                <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-1.414 1.414A9 9 0 105.636 18.364l1.414-1.414A7 7 0 1116.95 7.05z"/>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <h3 class="text-xl font-semibold text-gray-700 mb-3">National Emergency Contacts</h3>
                                <ul class="list-disc list-inside text-gray-700 space-y-2 ml-4 text-base">
                                    <li><strong>Philippine National Police (PNP):</strong> 117</li>
                                    <li><strong>Bureau of Fire Protection:</strong> 160</li>
                                    <li><strong>Philippine Red Cross:</strong> 143</li>
                                    <li><strong>Emergency Mountain Rescue (MMDA/SAGIP):</strong> 136</li>
                                    <li><strong>NDRRMC:</strong> (02) 8911-1406</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Preconditions Checklist -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 hover:shadow-md transition-all duration-200">
                    <div class="p-6">
                        <div class="flex items-start gap-4 mb-4">
                            <div class="p-3 bg-purple-50 rounded-lg">
                                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                            <div class="flex-1">
                                <h4 class="font-semibold text-gray-800 text-lg mb-3">Preconditions</h4>
                                <p class="text-base text-gray-600 mb-4">Please select any of the following that currently applies to you:</p>
                            </div>
                        </div>

                        <div class="pl-14">
                            @php
                                $emergencyConditions = [
                                    'first_aid' => 'I have basic first-aid training',
                                    'signals' => 'I know how to use emergency tools and signals (whistle, mirror)',
                                    'hiking_injuries' => 'I can treat common hiking injuries (cuts, burns, sprains)',
                                    'emergency_gear' => 'I carry emergency gear (whistle, mirror, bright cloth/flair)',
                                    'informed_person' => 'I informed someone of my itinerary',
                                    'emergency_plan' => 'I have a complete emergency action plan'
                                ];
                            @endphp

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-3 mb-4">
                                @foreach ($emergencyConditions as $value => $label)
                                    <label class="inline-flex items-center text-base">
                                        <input type="checkbox" name="health_conditions[]" value="{{ $value }}" class="mr-3 w-4 h-4 text-purple-600 border-gray-300 rounded focus:ring-purple-500">
                                        {{ $label }}
                                    </label>
                                @endforeach
                            </div>

                            
                        </div>
                    </div>
                </div>



                <!-- Emergency Knowledge and Skills Assessment -->
                @foreach([
                    ['label' => 'Do you have current first aid training or certification?', 'type' => 'radio', 'name' => 'first_aid_training'],
                    ['label' => 'Do you know how to recognize and treat shock in an injured person?', 'type' => 'likert', 'name' => 'recognize_treat_shock'],
                    ['label' => 'Do you carry a comprehensive first aid kit appropriate for your hike duration?', 'type' => 'likert', 'name' => 'carry_first_aid_kit'],
                    ['label' => 'Do you have reliable communication devices (satellite communicator, emergency beacon, or cell phone with backup power)?', 'type' => 'likert', 'name' => 'emergency_communication'],
                    ['label' => 'Can you use a map and compass to navigate back to safety without GPS?', 'type' => 'likert', 'name' => 'navigate_without_gps'],
                    ['label' => 'Do you know how to create visible signals for search and rescue teams?', 'type' => 'likert', 'name' => 'create_rescue_signals'],
                    ['label' => 'Can you recognize and respond to signs of altitude sickness, hypothermia, or heat stroke?', 'type' => 'likert', 'name' => 'recognize_environmental_illness'],
                    ['label' => 'Do you know the international distress signals (whistle patterns, ground signals, etc.)?', 'type' => 'likert', 'name' => 'know_distress_signals'],
                    ['label' => 'Are you mentally prepared to make decisions under stress in emergencies?', 'type' => 'likert', 'name' => 'mentally_prepared_emergency'],
                ] as $q)
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 hover:shadow-md transition-all duration-200">
                    <div class="p-4">
                        <div class="flex items-start gap-4 mb-2">
                            <div class="p-2 bg-orange-50 rounded-lg">
                                <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <label class="block font-semibold text-gray-800 text-lg leading-relaxed flex-1">
                                {{ $q['label'] }}
                            </label>
                        </div>
                        <div class="pl-11">
                            @if($q['type'] === 'radio')
                                <div class="flex gap-6">
                                    <label class="flex items-center space-x-2">
                                        <input type="radio" name="{{ $q['name'] }}" value="yes"
                                            class="appearance-none w-5 h-5 rounded-full border-2 border-gray-400 checked:bg-yellow-500 checked:border-yellow-500 focus:outline-none transition duration-150"
                                            {{ isset($emergencyData[$q['name']]) && $emergencyData[$q['name']] == 'yes' ? 'checked' : '' }}>
                                        <span class="text-base text-gray-700">Yes</span>
                                    </label>
                                    <label class="flex items-center space-x-2">
                                        <input type="radio" name="{{ $q['name'] }}" value="none"
                                            class="appearance-none w-5 h-5 rounded-full border-2 border-gray-400 checked:bg-yellow-500 checked:border-yellow-500 focus:outline-none transition duration-150"
                                            {{ isset($emergencyData[$q['name']]) && $emergencyData[$q['name']] == 'none' ? 'checked' : '' }}>
                                        <span class="text-base text-gray-700">None</span>
                                    </label>
                                </div>
                            @else
                                <x-likert-scale name="{{ $q['name'] }}" type="frequency" :value="$emergencyData[$q['name']] ?? null" />
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach

                <!-- Emergency Contact Details -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 hover:shadow-md transition-all duration-200">
                    <div class="p-6">
                        <h3 class="text-xl font-semibold text-gray-700 mb-4">Your Emergency Contact Details</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-base font-medium text-gray-700 mb-2">Name of Emergency Contact</label>
                                <input type="text" name="emergency_name" value="{{ $emergencyData['emergency_name'] ?? '' }}" class="mt-1 block w-full border-2 border-gray-300 rounded-xl px-4 py-3 text-base focus:ring-2 focus:ring-yellow-200" required>
                            </div>
                            <div>
                                <label class="block text-base font-medium text-gray-700 mb-2">Relationship</label>
                                <input type="text" name="emergency_relationship" value="{{ $emergencyData['emergency_relationship'] ?? '' }}" class="mt-1 block w-full border-2 border-gray-300 rounded-xl px-4 py-3 text-base focus:ring-2 focus:ring-yellow-200" required>
                            </div>
                            <div>
                                <label class="block text-base font-medium text-gray-700 mb-2">Primary Contact Number</label>
                                <input type="tel" name="emergency_number" value="{{ $emergencyData['emergency_number'] ?? '' }}" class="mt-1 block w-full border-2 border-gray-300 rounded-xl px-4 py-3 text-base focus:ring-2 focus:ring-yellow-200" required>
                            </div>
                            <div>
                                <label class="block text-base font-medium text-gray-700 mb-2">Alternative Contact (Optional)</label>
                                <input type="tel" name="emergency_alt" value="{{ $emergencyData['emergency_alt'] ?? '' }}" class="mt-1 block w-full border-2 border-gray-300 rounded-xl px-4 py-3 text-base focus:ring-2 focus:ring-yellow-200">
                            </div>
                        </div>
                        
                    </div>
                </div>
            </div>

            <!-- Action Section -->
            <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-8 mt-8">
                <div class="flex justify-between items-center gap-4 flex-wrap">
                    <!-- Back Button -->
                    <div class="flex-shrink-0">
                        <a href="{{ route('assessment.weather') }}"
                            class="env-btn inline-flex items-center px-10 py-5 font-semibold text-lg rounded-xl shadow-lg transition-all duration-200">
                            <svg class="mr-3 w-6 h-6 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                            </svg>
                            <span>Back: Weather Assessment</span>
                        </a>
                    </div>
                    <!-- Message -->
                    <div class="flex-1 text-center">
                        <p class="text-base text-gray-600">
                            Complete your emergency assessment to continue with environmental awareness.
                        </p>
                    </div>

                    <!-- Next Button -->
                    <div class="flex-shrink-0">
                        <button type="submit" 
                            class="env-btn inline-flex items-center px-10 py-5 font-semibold text-lg rounded-xl shadow-lg transition-all duration-200">
                            <span>Next: Environment Assessment</span>
                            <svg class="ml-3 w-6 h-6 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>


        </form>
    </div>

    <style>
        /* Button styling matches other assessment pages */
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
        /* Radio button styling matches other assessment pages */
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
            border: 1px solid #fed7d7;
        }
        .bg-gradient-to-r {
            background: linear-gradient(90deg, #fed7d7 0%, #feb2b2 100%);
        }
        .rounded-xl {
            border-radius: 1rem;
        }
        .shadow-lg {
            box-shadow: 0 8px 32px 0 rgba(239,68,68,0.10);
        }
        /* Question card hover */
        .hover\:shadow-md:hover {
            box-shadow: 0 6px 24px 0 rgba(239,68,68,0.12);
        }
        /* Input styling */
        input[type="text"], input[type="tel"] {
            background: #f9fafb;
        }
    </style>
</x-app-layout>
<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div class="space-y-2">
                <h2 class="font-bold text-2xl text-gray-900 leading-tight tracking-tight">
                    PRE-HIKE SELF-ASSESSMENT
                </h2>
                <h1 class="text-gray-600 text-lg font-medium flex items-center gap-2">
                    <span class="inline-flex items-center justify-center w-6 h-6 bg-red-100 text-red-800 text-sm font-bold rounded-full">5</span>
                    Emergencies
                </h1>
            </div>
        </div>
    </x-slot>

    <div class="py-8 px-4 sm:px-6 lg:px-8 max-w-6xl mx-auto">
        <form action="{{ route('assessment.emergency.store') }}" method="POST" class="space-y-4">
            @csrf

            @php
                $radioClasses = "w-5 h-5 text-yellow-500 border-gray-300 focus:ring-yellow-400";
                $labelClasses = "flex items-center space-x-3 cursor-pointer hover:bg-yellow-50 rounded px-2 py-1.5";
            @endphp

            <!-- Header Card -->
            <div class="bg-gradient-to-r from-red-50 to-orange-50 rounded-xl p-6 border border-red-100">
                <div class="flex items-start gap-4">
                    <div class="p-3 bg-red-100 rounded-lg flex-shrink-0">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-1.414 1.414A9 9 0 105.636 18.364l1.414-1.414A7 7 0 1116.95 7.05z"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-xl font-bold text-gray-800 mb-2">Emergency Preparedness Assessment</h3>
                        <p class="text-red-700 text-sm mb-4">Be ready for emergencies—your safety depends on your preparation and knowledge.</p>
                        <div class="bg-white/60 rounded-lg p-4 border border-red-100">
                            <div class="flex items-start gap-3">
                                <div class="p-1.5 bg-red-500 rounded-full flex-shrink-0 mt-0.5">
                                    <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <div class="text-xs text-gray-700 leading-relaxed">
                                    <p class="font-medium text-gray-800 mb-1">Emergency Planning</p>
                                    <p>Knowing what to do and having the right equipment can save lives in the outdoors. Review your emergency skills and gear before every hike.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Questions -->
            <div class="space-y-3">

                <!-- National Emergency Contacts -->
                <div class="bg-gradient-to-r from-red-100 to-pink-100 rounded-xl p-6 border border-red-100">
                    <div class="p-4">
                        <div class="flex items-start gap-4 mb-4">
                            <div class="p-2 bg-red-50 rounded-lg">
                                <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-1.414 1.414A9 9 0 105.636 18.364l1.414-1.414A7 7 0 1116.95 7.05z"/>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <h3 class="text-lg font-semibold text-gray-700 mb-2">National Emergency Contacts</h3>
                                <ul class="list-disc list-inside text-gray-700 space-y-1 ml-4">
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
                    <div class="p-4">
                        <div class="flex items-start gap-4 mb-4">
                            <div class="p-2 bg-purple-50 rounded-lg">
                                <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                            <div class="flex-1">
                                <h4 class="font-semibold text-gray-800 text-base mb-2">Preconditions</h4>
                                <p class="text-sm text-gray-600 mb-4">Please select any of the following that currently applies to you:</p>
                            </div>
                        </div>

                        <div class="pl-11">
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
                                    <label class="inline-flex items-center text-sm">
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
                            <label class="block font-semibold text-gray-800 text-base leading-relaxed flex-1">
                                {{ $q['label'] }}
                            </label>
                        </div>
                        <div class="pl-11">
                            @if($q['type'] === 'radio')
                                <div class="flex gap-6">
                                    <label class="flex items-center space-x-2">
                                        <input type="radio" name="{{ $q['name'] }}" value="yes"
                                            class="appearance-none w-5 h-5 rounded-full border-2 border-gray-400 checked:bg-yellow-500 checked:border-yellow-500 focus:outline-none transition duration-150">
                                        <span class="text-sm text-gray-700">Yes</span>
                                    </label>
                                    <label class="flex items-center space-x-2">
                                        <input type="radio" name="{{ $q['name'] }}" value="none"
                                            class="appearance-none w-5 h-5 rounded-full border-2 border-gray-400 checked:bg-yellow-500 checked:border-yellow-500 focus:outline-none transition duration-150">
                                        <span class="text-sm text-gray-700">None</span>
                                    </label>
                                </div>
                            @else
                                <x-likert-scale name="{{ $q['name'] }}" />
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach

                <!-- Emergency Contact Details -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 hover:shadow-md transition-all duration-200">
                    <div class="p-4">
                        <h3 class="text-lg font-semibold text-gray-700 mb-4">Your Emergency Contact Details</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Name of Emergency Contact</label>
                                <input type="text" name="emergency_name" class="mt-1 block w-full border-2 border-gray-300 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-yellow-200" required>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Relationship</label>
                                <input type="text" name="emergency_relationship" class="mt-1 block w-full border-2 border-gray-300 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-yellow-200" required>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Primary Contact Number</label>
                                <input type="tel" name="emergency_number" class="mt-1 block w-full border-2 border-gray-300 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-yellow-200" required>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Alternative Contact (Optional)</label>
                                <input type="tel" name="emergency_alt" class="mt-1 block w-full border-2 border-gray-300 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-yellow-200">
                            </div>
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
                            class="inline-flex items-center px-8 py-4 text-white font-semibold text-base rounded-xl shadow-lg transition-all duration-200 hover:shadow-xl hover:scale-105 focus:outline-none focus:ring-4 focus:ring-yellow-200"
                            style="background: linear-gradient(135deg, #e3a746 0%, #f4c430 100%);">
                            <svg class="mr-2 w-5 h-5 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                            </svg>
                            <span>Back: Weather Screen</span>
                        </a>
                    </div>
                    <!-- Message -->
                    <div class="flex-1 text-center">
                        <p class="text-sm text-gray-600">
                            Complete your emergency assessment to finish your pre-hike safety check.
                        </p>
                    </div>

                    <!-- Next Button -->
            
       <button type="submit" 
        class="inline-flex items-center px-8 py-4 text-white font-semibold text-base rounded-xl shadow-lg transition-all duration-200 hover:shadow-xl hover:scale-105 focus:outline-none focus:ring-4 focus:ring-yellow-200"
        style="background: linear-gradient(135deg, #e3a746 0%, #f4c430 100%);">
        <span>Next: Environment Assessment</span>
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
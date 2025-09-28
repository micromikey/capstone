<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div class="space-y-2">
                <h2 class="font-bold text-2xl text-gray-900 leading-tight tracking-tight">
                    PRE-HIKE SELF-ASSESSMENT
                </h2>
                <h1 class="text-gray-600 text-xl font-medium flex items-center gap-2">
                    <span class="inline-flex items-center justify-center w-8 h-8 bg-blue-100 text-blue-800 text-lg font-bold rounded-full">3</span>
                    Health Screen
                </h1>
            </div>
        </div>
    </x-slot>

    <div class="py-8 px-4 sm:px-6 lg:px-8 max-w-6xl mx-auto">
        <form action="{{ route('assessment.health.store') }}" method="POST" class="space-y-4">
            @csrf

            @php
                $radioClasses = "w-6 h-6 text-yellow-500 border-gray-300 focus:ring-yellow-400";
                $labelClasses = "flex items-center space-x-3 cursor-pointer hover:bg-yellow-50 rounded px-3 py-2";
            @endphp

            <!-- Header Card -->
            <div class="bg-gradient-to-r from-red-50 to-orange-50 rounded-xl p-8 border border-red-100">
                <div class="flex items-start gap-4">
                    <div class="p-4 bg-red-100 rounded-lg flex-shrink-0">
                        <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                        </svg>
                    </div>
                    
                    <div class="flex-1">
                        <h3 class="text-2xl font-bold text-gray-800 mb-3">Health & Medical Assessment</h3>
                        <p class="text-red-700 text-lg mb-4">Help us understand your health status for a safe hiking experience</p>
                        
                        <!-- Info Section -->
                        <div class="bg-white/60 rounded-lg p-5 border border-red-100">
                            <div class="flex items-start gap-3">
                                <div class="p-2 bg-red-500 rounded-full flex-shrink-0 mt-0.5">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div class="text-sm text-gray-700 leading-relaxed">
                                    <p class="font-medium text-gray-800 mb-2 text-base">Medical Safety Assessment</p>
                                    <p class="text-base">Your honest responses help us ensure appropriate safety measures and emergency preparedness. All medical information is kept confidential and used solely for safety planning purposes.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Questions -->
            <div class="space-y-4">

                <!-- Health Conditions Checklist -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 hover:shadow-md transition-all duration-200">
                    <div class="p-6">
                        <div class="flex items-start gap-4 mb-4">
                            <div class="p-3 bg-purple-50 rounded-lg">
                                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                            <div class="flex-1">
                                <h4 class="font-semibold text-gray-800 text-lg mb-2">Health Conditions</h4>
                                <p class="text-base text-gray-600 mb-4">Please select any of the following health conditions that apply to you:</p>
                            </div>
                        </div>

                        <div class="pl-14">
                            @php
                                $healthConditions = [
                                    'asthma' => 'Asthma',
                                    'chronic' => 'Chronic Diseases (e.g., diabetes, heart condition)',
                                    'allergies' => 'Allergies',
                                    'blood_pressure' => 'High/Low Blood Pressure',
                                    'epilepsy' => 'Epilepsy or Seizure Disorder',
                                    'heart_murmur' => 'Heart Murmur or Irregular Heartbeat',
                                    'lung_disorder' => 'Lung Disorder (e.g., bronchitis, COPD)',
                                    'recent_surgery' => 'Recent Surgery or Hospitalization',
                                    'migraine' => 'Migraine or Frequent Headaches',
                                    'mobility' => 'Mobility Impairment',
                                    'none' => 'None of the above'
                                ];
                            @endphp

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-3 mb-4">
                                @foreach ($healthConditions as $value => $label)
                                    <label class="inline-flex items-center text-base">
                                        <input type="checkbox" name="health_conditions[]" value="{{ $value }}" class="mr-3 w-6 h-6 text-purple-600 border-gray-300 rounded focus:ring-purple-500"
                                            {{ isset($healthData['health_conditions']) && in_array($value, $healthData['health_conditions']) ? 'checked' : '' }}>
                                        {{ $label }}
                                    </label>
                                @endforeach
                            </div>

                            <!-- Others -->
                            <div class="mt-4">
                                <label for="health_other" class="block text-gray-700 font-medium mb-2 text-sm">Others, please specify:</label>
                                <input type="text" name="health_other" id="health_other" value="{{ $healthData['health_other'] ?? '' }}" class="w-full border-2 border-gray-300 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200 hover:border-gray-400">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Cardiovascular Health Assessment -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 hover:shadow-md transition-all duration-200">
                    <div class="p-4">
                        <div class="flex items-start gap-4 mb-4">
                            <div class="p-2 bg-red-50 rounded-lg">
                                <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                </svg>
                            </div>
                            <label class="block font-semibold text-gray-800 text-lg leading-relaxed">
                                Do you experience chest pain, pressure, or discomfort during physical activity?
                            </label>
                        </div>
                        <div class="pl-11">
                            <x-likert-scale name="chest_pain_activity" type="frequency" :value="$healthData['chest_pain_activity'] ?? null" />
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-200 hover:shadow-md transition-all duration-200">
                    <div class="p-4">
                        <div class="flex items-start gap-4 mb-4">
                            <div class="p-2 bg-blue-50 rounded-lg">
                                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                                </svg>
                            </div>
                            <label class="block font-semibold text-gray-800 text-lg leading-relaxed">
                                Do you experience shortness of breath when walking uphill or climbing stairs?
                            </label>
                        </div>
                        <div class="pl-11">
                            <x-likert-scale name="breath_uphill" type="frequency" :value="$healthData['breath_uphill'] ?? null" />
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-200 hover:shadow-md transition-all duration-200">
                    <div class="p-4">
                        <div class="flex items-start gap-4 mb-4">
                            <div class="p-2 bg-yellow-50 rounded-lg">
                                <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                                </svg>
                            </div>
                            <label class="block font-semibold text-gray-800 text-lg leading-relaxed">
                                Do you experience dizziness or lightheadedness during or after exercise?
                            </label>
                        </div>
                        <div class="pl-11">
                            <x-likert-scale name="dizziness_exercise" type="frequency" :value="$healthData['dizziness_exercise'] ?? null" />
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-200 hover:shadow-md transition-all duration-200">
                    <div class="p-4">
                        <div class="flex items-start gap-4 mb-4">
                            <div class="p-2 bg-red-50 rounded-lg">
                                <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                </svg>
                            </div>
                            <label class="block font-semibold text-gray-800 text-lg leading-relaxed">
                                Do you experience heart palpitations or irregular heartbeat during physical activity?
                            </label>
                        </div>
                        <div class="pl-11">
                            <x-likert-scale name="heart_palpitations" type="frequency" :value="$healthData['heart_palpitations'] ?? null" />
                        </div>
                    </div>
                </div>

                <!-- Respiratory Health -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 hover:shadow-md transition-all duration-200">
                    <div class="p-4">
                        <div class="flex items-start gap-4 mb-4">
                            <div class="p-2 bg-green-50 rounded-lg">
                                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                                </svg>
                            </div>
                            <label class="block font-semibold text-gray-800 text-lg leading-relaxed">
                                Do you experience wheezing or difficulty breathing during moderate exercise?
                            </label>
                        </div>
                        <div class="pl-11">
                            <x-likert-scale name="wheezing_exercise" type="frequency" :value="$healthData['wheezing_exercise'] ?? null" />
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-200 hover:shadow-md transition-all duration-200">
                    <div class="p-4">
                        <div class="flex items-start gap-4 mb-4">
                            <div class="p-2 bg-orange-50 rounded-lg">
                                <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                                </svg>
                            </div>
                            <label class="block font-semibold text-gray-800 text-lg leading-relaxed">
                                How often do you experience persistent coughing, especially in cold or dusty environments?
                            </label>
                        </div>
                        <div class="pl-11">
                            <x-likert-scale name="persistent_cough" type="frequency" :value="$healthData['persistent_cough'] ?? null" />
                        </div>
                    </div>
                </div>

                <!-- Musculoskeletal Health -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 hover:shadow-md transition-all duration-200">
                    <div class="p-4">
                        <div class="flex items-start gap-4 mb-4">
                            <div class="p-2 bg-indigo-50 rounded-lg">
                                <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12" />
                                </svg>
                            </div>
                            <label class="block font-semibold text-gray-800 text-lg leading-relaxed">
                                Do you experience joint pain (knees, ankles, hips) during or after walking on uneven terrain?
                            </label>
                        </div>
                        <div class="pl-11">
                            <x-likert-scale name="joint_pain_terrain" type="frequency" :value="$healthData['joint_pain_terrain'] ?? null" />
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-200 hover:shadow-md transition-all duration-200">
                    <div class="p-4">
                        <div class="flex items-start gap-4 mb-4">
                            <div class="p-2 bg-emerald-50 rounded-lg">
                                <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                </svg>
                            </div>
                            <label class="block font-semibold text-gray-800 text-lg leading-relaxed">
                                Do you experience muscle cramps or fatigue more quickly than expected during physical activity?
                            </label>
                        </div>
                        <div class="pl-11">
                            <x-likert-scale name="muscle_fatigue" type="frequency" :value="$healthData['muscle_fatigue'] ?? null" />
                        </div>
                    </div>
                </div>

                <!-- Neurological and Balance -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 hover:shadow-md transition-all duration-200">
                    <div class="p-4">
                        <div class="flex items-start gap-4 mb-4">
                            <div class="p-2 bg-teal-50 rounded-lg">
                                <svg class="w-6 h-6 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                                </svg>
                            </div>
                            <label class="block font-semibold text-gray-800 text-lg leading-relaxed">
                                Do you experience balance problems or feel unsteady on uneven surfaces?
                            </label>
                        </div>
                        <div class="pl-11">
                            <x-likert-scale name="balance_problems" type="frequency" :value="$healthData['balance_problems'] ?? null" />
                        </div>
                    </div>
                </div>


                <!-- Heat and Hydration Related -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 hover:shadow-md transition-all duration-200">
                    <div class="p-4">
                        <div class="flex items-start gap-4 mb-4">
                            <div class="p-2 bg-amber-50 rounded-lg">
                                <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                                </svg>
                            </div>
                            <label class="block font-semibold text-gray-800 text-lg leading-relaxed">
                                Have you experienced heat exhaustion, heat stroke, or severe dehydration during outdoor activities?
                            </label>
                        </div>
                        <div class="pl-11">
                            <x-likert-scale name="heat_exhaustion_history" type="frequency" :value="$healthData['heat_exhaustion_history'] ?? null" />
                        </div>
                    </div>
                </div>

                <!-- Medication and Sleep -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 hover:shadow-md transition-all duration-200">
                    <div class="p-4">
                        <div class="flex items-start gap-4 mb-4">
                            <div class="p-2 bg-violet-50 rounded-lg">
                                <svg class="w-6 h-6 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                                </svg>
                            </div>
                            <label class="block font-semibold text-gray-800 text-lg leading-relaxed">
                                Are you currently taking medications that may affect your balance, hydration, or physical performance?
                            </label>
                        </div>
                        <div class="pl-11">
                            <x-likert-scale name="medication_effects" type="standard" :value="$healthData['medication_effects'] ?? null" />
                        </div>
                    </div>
                </div>

                

                <!-- Additional Medical Information -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 hover:shadow-md transition-all duration-200">
                    <div class="p-6">
                        <div class="flex items-start gap-4 mb-4">
                            <div class="p-3 bg-fuchsia-50 rounded-lg">
                                <svg class="w-6 h-6 text-fuchsia-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                            <div class="flex-1">
                                <label for="current_medications" class="block font-semibold text-gray-800 text-lg mb-2">
                                    Please list any current medications or medical treatments:
                                </label>
                            </div>
                        </div>
                        <div class="pl-14">
                            <textarea name="current_medications" id="current_medications" rows="3" 
                                      class="w-full border-2 border-gray-300 rounded-xl px-4 py-3 text-base focus:ring-2 focus:ring-fuchsia-500 focus:border-fuchsia-500 transition-all duration-200 hover:border-gray-400"
                                      placeholder="Include dosage and frequency if relevant to physical activity"></textarea>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-200 hover:shadow-md transition-all duration-200">
                    <div class="p-6">
                        <div class="flex items-start gap-4 mb-4">
                            <div class="p-3 bg-neutral-50 rounded-lg">
                                <svg class="w-6 h-6 text-neutral-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                            <div class="flex-1">
                                <label for="medical_concerns" class="block font-semibold text-gray-800 text-lg mb-2">
                                    Any additional health concerns or medical history relevant to hiking:
                                </label>
                            </div>
                        </div>
                        <div class="pl-14">
                            <textarea name="medical_concerns" id="medical_concerns" rows="3" 
                                      class="w-full border-2 border-gray-300 rounded-xl px-4 py-3 text-base focus:ring-2 focus:ring-neutral-500 focus:border-neutral-500 transition-all duration-200 hover:border-gray-400"
                                      placeholder="Include any recent injuries, surgeries, or health changes"></textarea>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Action Section -->
            <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-8 mt-8">
                <div class="flex justify-between items-center gap-4 flex-wrap">
                    <!-- Back Button -->
                    <div class="flex-shrink-0">
                        <a href="{{ route('assessment.fitness') }}" 
                            class="inline-flex items-center px-10 py-5 text-white font-semibold text-lg rounded-xl shadow-lg transition-all duration-200 hover:shadow-xl hover:scale-105 focus:outline-none focus:ring-4 focus:ring-yellow-200"
                            style="background: linear-gradient(135deg, #e3a746 0%, #f4c430 100%);">
                            <svg class="mr-3 w-6 h-6 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                            </svg>
                            <span>Back: Fitness Assessment</span>
                        </a>
                    </div>

                    <!-- Message -->
                    <div class="flex-1 text-center">
                        <p class="text-base text-gray-600">
                            Complete your health screening to proceed to the weather assessment.
                        </p>
                    </div>

                    <!-- Next Button -->
                    <div class="flex-shrink-0">
                        <button type="submit" 
                            class="inline-flex items-center px-10 py-5 text-white font-semibold text-lg rounded-xl shadow-lg transition-all duration-200 hover:shadow-xl hover:scale-105 focus:outline-none focus:ring-4 focus:ring-yellow-200"
                            style="background: linear-gradient(135deg, #e3a746 0%, #f4c430 100%);">
                            <span>Next: Weather Assessment</span>
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
    textarea {
        resize: none;
        width: 80%;
        min-height: 50px;
    }
    </style>

</x-app-layout>
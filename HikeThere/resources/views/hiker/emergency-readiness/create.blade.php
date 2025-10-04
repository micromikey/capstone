<x-app-layout>
    <div class="py-12 bg-gradient-to-br from-blue-50 via-white to-green-50 min-h-screen">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Back Button -->
            <div class="mb-6">
                <a href="{{ route('dashboard') }}" class="inline-flex items-center text-green-600 hover:text-green-700 transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                    Back to Dashboard
                </a>
            </div>

            <!-- Header Card -->
            <div class="bg-white rounded-lg shadow-lg overflow-hidden mb-6">
                <div class="bg-gradient-to-r from-blue-600 to-green-600 p-6 text-white">
                    <h1 class="text-3xl font-bold mb-2">Emergency Readiness Feedback</h1>
                    <p class="text-blue-100">Help us improve trail safety by rating your experience</p>
                </div>
                
                <!-- Trail Info -->
                <div class="p-6 bg-blue-50 border-b">
                    <div class="flex items-start">
                        <svg class="w-6 h-6 text-blue-600 mt-1 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">{{ $booking->trail->name }}</h3>
                            <p class="text-sm text-gray-600">{{ $booking->trail->location }}</p>
                            @if($booking->batch)
                                <p class="text-sm text-gray-500 mt-1">
                                    Hike Date: {{ $booking->batch->starts_at->format('F d, Y') }}
                                </p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Feedback Form -->
            <form id="readinessFeedbackForm" onsubmit="submitFeedback(event)" class="bg-white rounded-lg shadow-lg p-6 space-y-8">
                <input type="hidden" name="booking_id" value="{{ $booking->id }}">
                
                <p class="text-gray-700 bg-blue-50 p-4 rounded-lg border-l-4 border-blue-500">
                    <strong>Your feedback matters!</strong> Please rate the emergency preparedness you experienced during your hike. 
                    Your honest assessment helps organizations improve safety for all hikers.
                </p>

                <!-- First Aid Readiness -->
                <div class="space-y-3">
                    <label class="block text-lg font-semibold text-gray-900">
                        First Aid & Medical Preparedness
                        <span class="text-red-500">*</span>
                    </label>
                    <p class="text-sm text-gray-600">Availability and quality of first aid kits, medical supplies, and emergency medical protocols</p>
                    <div class="flex items-center space-x-4">
                        <input type="range" name="first_aid_score" id="first_aid_score" min="0" max="100" value="50" 
                               class="flex-1 h-3 bg-gray-200 rounded-lg appearance-none cursor-pointer range-slider"
                               oninput="updateSliderValue('first_aid', this.value)">
                        <span id="first_aid_value" class="text-2xl font-bold text-blue-600 min-w-[60px] text-right">50</span>
                    </div>
                    <span id="first_aid_score_error" class="error-message text-red-500 text-sm"></span>
                </div>

                <!-- Communication Systems -->
                <div class="space-y-3">
                    <label class="block text-lg font-semibold text-gray-900">
                        Communication Systems
                        <span class="text-red-500">*</span>
                    </label>
                    <p class="text-sm text-gray-600">Availability of radios, mobile signal, emergency contact systems, and communication protocols</p>
                    <div class="flex items-center space-x-4">
                        <input type="range" name="communication_score" id="communication_score" min="0" max="100" value="50" 
                               class="flex-1 h-3 bg-gray-200 rounded-lg appearance-none cursor-pointer range-slider"
                               oninput="updateSliderValue('communication', this.value)">
                        <span id="communication_value" class="text-2xl font-bold text-blue-600 min-w-[60px] text-right">50</span>
                    </div>
                    <span id="communication_score_error" class="error-message text-red-500 text-sm"></span>
                </div>

                <!-- Safety Equipment -->
                <div class="space-y-3">
                    <label class="block text-lg font-semibold text-gray-900">
                        Safety Equipment
                        <span class="text-red-500">*</span>
                    </label>
                    <p class="text-sm text-gray-600">Quality and availability of safety gear, rescue equipment, and protective equipment</p>
                    <div class="flex items-center space-x-4">
                        <input type="range" name="equipment_score" id="equipment_score" min="0" max="100" value="50" 
                               class="flex-1 h-3 bg-gray-200 rounded-lg appearance-none cursor-pointer range-slider"
                               oninput="updateSliderValue('equipment', this.value)">
                        <span id="equipment_value" class="text-2xl font-bold text-blue-600 min-w-[60px] text-right">50</span>
                    </div>
                    <span id="equipment_score_error" class="error-message text-red-500 text-sm"></span>
                </div>

                <!-- Staff Training -->
                <div class="space-y-3">
                    <label class="block text-lg font-semibold text-gray-900">
                        Staff Training & Competence
                        <span class="text-red-500">*</span>
                    </label>
                    <p class="text-sm text-gray-600">Staff knowledge, emergency response training, and professionalism</p>
                    <div class="flex items-center space-x-4">
                        <input type="range" name="staff_training_score" id="staff_training_score" min="0" max="100" value="50" 
                               class="flex-1 h-3 bg-gray-200 rounded-lg appearance-none cursor-pointer range-slider"
                               oninput="updateSliderValue('staff_training', this.value)">
                        <span id="staff_training_value" class="text-2xl font-bold text-blue-600 min-w-[60px] text-right">50</span>
                    </div>
                    <span id="staff_training_score_error" class="error-message text-red-500 text-sm"></span>
                </div>

                <!-- Emergency Access -->
                <div class="space-y-3">
                    <label class="block text-lg font-semibold text-gray-900">
                        Emergency Access & Evacuation
                        <span class="text-red-500">*</span>
                    </label>
                    <p class="text-sm text-gray-600">Ease of emergency evacuation, access routes, and rescue accessibility</p>
                    <div class="flex items-center space-x-4">
                        <input type="range" name="emergency_access_score" id="emergency_access_score" min="0" max="100" value="50" 
                               class="flex-1 h-3 bg-gray-200 rounded-lg appearance-none cursor-pointer range-slider"
                               oninput="updateSliderValue('emergency_access', this.value)">
                        <span id="emergency_access_value" class="text-2xl font-bold text-blue-600 min-w-[60px] text-right">50</span>
                    </div>
                    <span id="emergency_access_score_error" class="error-message text-red-500 text-sm"></span>
                </div>

                <!-- Additional Comments -->
                <div class="space-y-3">
                    <label for="comments" class="block text-lg font-semibold text-gray-900">
                        Additional Comments
                    </label>
                    <p class="text-sm text-gray-600">Share any specific observations, suggestions, or concerns</p>
                    <textarea name="comments" id="comments" rows="4" 
                              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                              placeholder="Your detailed feedback helps improve safety measures..."></textarea>
                    <span id="comments_error" class="error-message text-red-500 text-sm"></span>
                </div>

                <!-- Rating Guide -->
                <div class="bg-gray-50 p-4 rounded-lg border">
                    <h4 class="font-semibold text-gray-900 mb-2">Rating Guide:</h4>
                    <ul class="text-sm text-gray-600 space-y-1">
                        <li><strong>90-100:</strong> Excellent - Exceeds safety standards</li>
                        <li><strong>75-89:</strong> Good - Meets safety standards well</li>
                        <li><strong>60-74:</strong> Adequate - Meets minimum standards</li>
                        <li><strong>40-59:</strong> Needs Improvement - Below standards</li>
                        <li><strong>0-39:</strong> Critical - Major safety concerns</li>
                    </ul>
                </div>

                <!-- Submit Button -->
                <div class="flex gap-4 pt-4 border-t">
                    <button type="button" onclick="window.history.back()" class="flex-1 px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-medium transition-colors">
                        Cancel
                    </button>
                    <button type="submit" id="submitBtn" class="flex-1 px-6 py-3 bg-gradient-to-r from-blue-600 to-green-600 text-white rounded-lg hover:from-blue-700 hover:to-green-700 font-medium transition-colors">
                        Submit Feedback
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function updateSliderValue(name, value) {
            document.getElementById(name + '_value').textContent = value;
            
            // Update slider color based on value
            const slider = document.getElementById(name + '_score');
            const percentage = (value / 100) * 100;
            
            let color;
            if (value >= 75) color = 'linear-gradient(90deg, rgb(34 197 94) 0%, rgb(34 197 94) ' + percentage + '%, rgb(229 231 235) ' + percentage + '%)';
            else if (value >= 50) color = 'linear-gradient(90deg, rgb(59 130 246) 0%, rgb(59 130 246) ' + percentage + '%, rgb(229 231 235) ' + percentage + '%)';
            else if (value >= 25) color = 'linear-gradient(90deg, rgb(251 146 60) 0%, rgb(251 146 60) ' + percentage + '%, rgb(229 231 235) ' + percentage + '%)';
            else color = 'linear-gradient(90deg, rgb(239 68 68) 0%, rgb(239 68 68) ' + percentage + '%, rgb(229 231 235) ' + percentage + '%)';
            
            slider.style.background = color;
        }

        // Initialize slider colors
        document.addEventListener('DOMContentLoaded', function() {
            ['first_aid', 'communication', 'equipment', 'staff_training', 'emergency_access'].forEach(name => {
                updateSliderValue(name, 50);
            });
        });

        async function submitFeedback(event) {
            event.preventDefault();
            
            const form = event.target;
            const submitBtn = document.getElementById('submitBtn');
            const originalBtnText = submitBtn.innerHTML;
            
            // Disable submit button
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<svg class="animate-spin h-5 w-5 mx-auto" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>';
            
            // Clear previous errors
            document.querySelectorAll('.error-message').forEach(el => el.textContent = '');
            
            try {
                const formData = new FormData(form);
                
                const response = await fetch('{{ route("hiker.readiness.store", $booking) }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    },
                    body: formData
                });
                
                const data = await response.json();
                
                if (response.ok) {
                    // Show success message
                    alert(data.message);
                    // Redirect to show page
                    if (data.redirect_url) {
                        window.location.href = data.redirect_url;
                    } else {
                        window.location.href = '{{ route("dashboard") }}';
                    }
                } else {
                    // Handle validation errors
                    if (data.errors) {
                        Object.keys(data.errors).forEach(key => {
                            const errorEl = document.getElementById(key + '_error');
                            if (errorEl) {
                                errorEl.textContent = data.errors[key][0];
                            }
                        });
                    } else {
                        alert(data.message || 'An error occurred while submitting your feedback.');
                    }
                }
            } catch (error) {
                console.error('Error:', error);
                alert('An error occurred while submitting your feedback. Please try again.');
            } finally {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalBtnText;
            }
        }
    </script>

    <style>
        .range-slider::-webkit-slider-thumb {
            -webkit-appearance: none;
            appearance: none;
            width: 24px;
            height: 24px;
            border-radius: 50%;
            background: #3b82f6;
            cursor: pointer;
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }

        .range-slider::-moz-range-thumb {
            width: 24px;
            height: 24px;
            border-radius: 50%;
            background: #3b82f6;
            cursor: pointer;
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
            border: none;
        }
    </style>
</x-app-layout>

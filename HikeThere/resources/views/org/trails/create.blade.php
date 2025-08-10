<x-app-layout>
    <x-slot name="header">
        <div class="space-y-4">
            <x-trail-breadcrumb currentPage="Create New Trail" />
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('Create New Trail') }}
                </h2>
                <a href="{{ route('org.trails.index') }}" class="text-gray-600 hover:text-gray-900">
                    <svg class="inline-block w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Back to Trails
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <!-- Step Navigation -->
                <div class="border-b border-gray-200">
                    <nav class="-mb-px flex space-x-8 px-6" aria-label="Tabs">
                        <button type="button" onclick="showStep(1)" class="step-nav whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm step-1-nav border-[#336d66] text-[#336d66]">
                            <span class="flex items-center">
                                <span class="w-8 h-8 rounded-full bg-[#336d66] text-white flex items-center justify-center text-sm font-medium mr-2">1</span>
                                Basic Info
                            </span>
                        </button>
                        <button type="button" onclick="showStep(2)" class="step-nav whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm step-2-nav border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300">
                            <span class="flex items-center">
                                <span class="w-8 h-8 rounded-full bg-gray-300 text-gray-600 flex items-center justify-center text-sm font-medium mr-2">2</span>
                                Trail Details
                            </span>
                        </button>
                        <button type="button" onclick="showStep(3)" class="step-nav whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm step-3-nav border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300">
                            <span class="flex items-center">
                                <span class="w-8 h-8 rounded-full bg-gray-300 text-gray-600 flex items-center justify-center text-sm font-medium mr-2">3</span>
                                Access & Safety
                            </span>
                        </button>
                        <button type="button" onclick="showStep(4)" class="step-nav whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm step-4-nav border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300">
                            <span class="flex items-center">
                                <span class="w-8 h-8 rounded-full bg-gray-300 text-gray-600 flex items-center justify-center text-sm font-medium mr-2">4</span>
                                Additional Info
                            </span>
                        </button>
                    </nav>
                </div>

                <form method="POST" action="{{ route('org.trails.store') }}" class="p-6" id="trailForm">
                    @csrf
                    
                    <!-- Step 1: Basic Information -->
                    <div id="step-1" class="step-content">
                        <div class="mb-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Step 1: Basic Information</h3>
                            <p class="text-gray-600 text-sm">Start with the essential details about your trail.</p>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <x-label for="mountain_name" value="Mountain Name *" />
                                <x-input id="mountain_name" type="text" name="mountain_name" class="mt-1 block w-full" required />
                                <x-input-error for="mountain_name" class="mt-2" />
                            </div>

                            <div>
                                <x-label for="trail_name" value="Trail Name *" />
                                <x-input id="trail_name" type="text" name="trail_name" class="mt-1 block w-full" required />
                                <x-input-error for="trail_name" class="mt-2" />
                            </div>

                            <div>
                                <x-label for="location_id" value="Location *" />
                                <select id="location_id" name="location_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-[#336d66] focus:border-[#336d66]" required>
                                    <option value="">Select Location</option>
                                    @foreach($locations as $location)
                                        <option value="{{ $location->id }}">{{ $location->name }}</option>
                                    @endforeach
                                </select>
                                <x-input-error for="location_id" class="mt-2" />
                            </div>

                            <div>
                                <x-label for="price" value="Price (₱) *" />
                                <x-input id="price" type="number" name="price" step="0.01" min="0" class="mt-1 block w-full" required />
                                <x-input-error for="price" class="mt-2" />
                            </div>

                            <div class="md:col-span-2">
                                <x-label for="package_inclusions" value="Package Inclusions *" />
                                <textarea id="package_inclusions" name="package_inclusions" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-[#336d66] focus:border-[#336d66]" placeholder="e.g., Guide, Meals, Environmental Fee" required></textarea>
                                <x-input-error for="package_inclusions" class="mt-2" />
                            </div>
                        </div>

                        <div class="mt-8 flex justify-end">
                            <button type="button" onclick="nextStep(2)" class="bg-[#336d66] hover:bg-[#2a5a54] text-white font-bold py-2 px-6 rounded-lg transition-colors">
                                Next: Trail Details
                                <svg class="inline-block w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Step 2: Trail Details -->
                    <div id="step-2" class="step-content hidden">
                        <div class="mb-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Step 2: Trail Details</h3>
                            <p class="text-gray-600 text-sm">Define the difficulty, duration, and terrain characteristics.</p>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <x-label for="difficulty" value="Difficulty Level *" />
                                <select id="difficulty" name="difficulty" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-[#336d66] focus:border-[#336d66]" required>
                                    <option value="">Select Difficulty</option>
                                    <option value="beginner">Beginner</option>
                                    <option value="intermediate">Intermediate</option>
                                    <option value="advanced">Advanced</option>
                                </select>
                                <x-input-error for="difficulty" class="mt-2" />
                            </div>

                            <div>
                                <x-label for="duration" value="Duration *" />
                                <x-input id="duration" type="text" name="duration" class="mt-1 block w-full" placeholder="e.g., 3-4 hours" required />
                                <x-input-error for="duration" class="mt-2" />
                            </div>

                            <div>
                                <x-label for="best_season" value="Best Season *" />
                                <x-input id="best_season" type="text" name="best_season" class="mt-1 block w-full" placeholder="e.g., November to March" required />
                                <x-input-error for="best_season" class="mt-2" />
                            </div>

                            <div class="md:col-span-2">
                                <x-label for="difficulty_description" value="Difficulty Description" />
                                <textarea id="difficulty_description" name="difficulty_description" rows="2" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-[#336d66] focus:border-[#336d66]" placeholder="Describe what makes this trail beginner/intermediate/advanced"></textarea>
                                <x-input-error for="difficulty_description" class="mt-2" />
                            </div>

                            <div class="md:col-span-2">
                                <x-label for="terrain_notes" value="Terrain Notes *" />
                                <textarea id="terrain_notes" name="terrain_notes" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-[#336d66] focus:border-[#336d66]" placeholder="e.g., Rocky, River Crossings, Dense Forest" required></textarea>
                                <x-input-error for="terrain_notes" class="mt-2" />
                            </div>

                            <div class="md:col-span-2">
                                <x-label for="other_trail_notes" value="Other Trail Notes" />
                                <textarea id="other_trail_notes" name="other_trail_notes" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-[#336d66] focus:border-[#336d66]" placeholder="e.g., limited hikers, hike cut-off time, curfew, trail rules, or safety reminders"></textarea>
                                <x-input-error for="other_trail_notes" class="mt-2" />
                            </div>
                        </div>

                        <div class="mt-8 flex justify-between">
                            <button type="button" onclick="prevStep(1)" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-6 rounded-lg transition-colors">
                                <svg class="inline-block w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                                </svg>
                                Previous
                            </button>
                            <button type="button" onclick="nextStep(3)" class="bg-[#336d66] hover:bg-[#2a5a54] text-white font-bold py-2 px-6 rounded-lg transition-colors">
                                Next: Access & Safety
                                <svg class="inline-block w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Step 3: Access & Safety -->
                    <div id="step-3" class="step-content hidden">
                        <div class="mb-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Step 3: Access & Safety</h3>
                            <p class="text-gray-600 text-sm">Provide transportation details and safety information.</p>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <x-label for="departure_point" value="Departure Point *" />
                                <x-input id="departure_point" type="text" name="departure_point" class="mt-1 block w-full" placeholder="e.g., Cubao Terminal" required />
                                <x-input-error for="departure_point" class="mt-2" />
                            </div>

                            <div>
                                <x-label for="transport_options" value="Transport Options *" />
                                <textarea id="transport_options" name="transport_options" rows="2" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-[#336d66] focus:border-[#336d66]" placeholder="e.g., Bus to Tanay, Jeep to Jump-off" required></textarea>
                                <x-input-error for="transport_options" class="mt-2" />
                            </div>

                            <div class="md:col-span-2">
                                <x-label for="side_trips" value="Side Trips" />
                                <textarea id="side_trips" name="side_trips" rows="2" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-[#336d66] focus:border-[#336d66]" placeholder="e.g., Tinipak River or enter N/A if none"></textarea>
                                <x-input-error for="side_trips" class="mt-2" />
                            </div>

                            <div class="md:col-span-2">
                                <x-label for="emergency_contacts" value="Emergency Contacts *" />
                                <textarea id="emergency_contacts" name="emergency_contacts" rows="2" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-[#336d66] focus:border-[#336d66]" placeholder="e.g., Barangay Rescue – 0917xxxxxxx" required></textarea>
                                <x-input-error for="emergency_contacts" class="mt-2" />
                            </div>

                            <div>
                                <x-label for="campsite_info" value="Campsite Information" />
                                <textarea id="campsite_info" name="campsite_info" rows="2" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-[#336d66] focus:border-[#336d66]" placeholder="e.g., Tent area at summit or No campsite"></textarea>
                                <x-input-error for="campsite_info" class="mt-2" />
                            </div>

                            <div>
                                <x-label for="guide_info" value="Guide Information" />
                                <textarea id="guide_info" name="guide_info" rows="2" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-[#336d66] focus:border-[#336d66]" placeholder="Information about guides"></textarea>
                                <x-input-error for="guide_info" class="mt-2" />
                            </div>

                            <div class="md:col-span-2">
                                <x-label for="environmental_practices" value="Environmental Practices" />
                                <textarea id="environmental_practices" name="environmental_practices" rows="2" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-[#336d66] focus:border-[#336d66]" placeholder="e.g., Leave No Trace"></textarea>
                                <x-input-error for="environmental_practices" class="mt-2" />
                            </div>
                        </div>

                        <div class="mt-8 flex justify-between">
                            <button type="button" onclick="prevStep(2)" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-6 rounded-lg transition-colors">
                                <svg class="inline-block w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                                </svg>
                                Previous
                            </button>
                            <button type="button" onclick="nextStep(4)" class="bg-[#336d66] hover:bg-[#2a5a54] text-white font-bold py-2 px-6 rounded-lg transition-colors">
                                Next: Additional Info
                                <svg class="inline-block w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Step 4: Additional Information -->
                    <div id="step-4" class="step-content hidden">
                        <div class="mb-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Step 4: Additional Information</h3>
                            <p class="text-gray-600 text-sm">Complete the trail profile with permits, requirements, and feedback.</p>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="md:col-span-2">
                                <div class="flex items-center">
                                    <input id="permit_required" type="checkbox" name="permit_required" value="1" class="h-4 w-4 text-[#336d66] focus:ring-[#336d66] border-gray-300 rounded">
                                    <x-label for="permit_required" value="Permit Required?" class="ml-2" />
                                </div>
                            </div>

                            <div class="md:col-span-2">
                                <x-label for="permit_process" value="Permit Process" />
                                <textarea id="permit_process" name="permit_process" rows="2" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-[#336d66] focus:border-[#336d66]" placeholder="e.g., Apply at Municipal Hall / Online LGU Form"></textarea>
                                <x-input-error for="permit_process" class="mt-2" />
                            </div>

                            <div class="md:col-span-2">
                                <x-label for="packing_list" value="Packing List *" />
                                <textarea id="packing_list" name="packing_list" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-[#336d66] focus:border-[#336d66]" placeholder="e.g., Water, Flashlight, Raincoat" required></textarea>
                                <x-input-error for="packing_list" class="mt-2" />
                            </div>

                            <div class="md:col-span-2">
                                <x-label for="health_fitness" value="Health/Fitness Requirements *" />
                                <textarea id="health_fitness" name="health_fitness" rows="2" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-[#336d66] focus:border-[#336d66]" placeholder="e.g., Moderate fitness recommended, Beginner-friendly" required></textarea>
                                <x-input-error for="health_fitness" class="mt-2" />
                            </div>

                            <div class="md:col-span-2">
                                <x-label for="requirements" value="Other Requirements" />
                                <textarea id="requirements" name="requirements" rows="2" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-[#336d66] focus:border-[#336d66]" placeholder="Any other specific requirements"></textarea>
                                <x-input-error for="requirements" class="mt-2" />
                            </div>

                            <div class="md:col-span-2">
                                <x-label for="customers_feedback" value="Customers Feedback" />
                                <textarea id="customers_feedback" name="customers_feedback" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-[#336d66] focus:border-[#336d66]" placeholder="e.g., Juan Dela Cruz: Sobrang ganda ng tanawin paakyat, I'm definitely going back here!"></textarea>
                                <x-input-error for="customers_feedback" class="mt-2" />
                            </div>

                            <div class="md:col-span-2">
                                <x-label for="testimonials_faqs" value="Testimonials / Common FAQs" />
                                <textarea id="testimonials_faqs" name="testimonials_faqs" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-[#336d66] focus:border-[#336d66]" placeholder="Most frequently asked questions from hikers, especially beginners"></textarea>
                                <x-input-error for="testimonials_faqs" class="mt-2" />
                            </div>
                        </div>

                        <div class="mt-8 flex justify-between">
                            <button type="button" onclick="prevStep(3)" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-6 rounded-lg transition-colors">
                                <svg class="inline-block w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                                </svg>
                                Previous
                            </button>
                            <button type="submit" class="bg-[#336d66] hover:bg-[#2a5a54] text-white font-bold py-2 px-6 rounded-lg transition-colors">
                                <svg class="inline-block w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                Create Trail
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        let currentStep = 1;
        const totalSteps = 4;

        function showStep(step) {
            // Hide all steps
            document.querySelectorAll('.step-content').forEach(content => {
                content.classList.add('hidden');
            });
            
            // Show selected step
            document.getElementById(`step-${step}`).classList.remove('hidden');
            
            // Update navigation styling
            updateNavigation(step);
            
            currentStep = step;
        }

        function nextStep(step) {
            if (step <= totalSteps) {
                showStep(step);
            }
        }

        function prevStep(step) {
            if (step >= 1) {
                showStep(step);
            }
        }

        function updateNavigation(activeStep) {
            // Reset all navigation
            document.querySelectorAll('.step-nav').forEach((nav, index) => {
                const stepNumber = index + 1;
                const circle = nav.querySelector('span span');
                const text = nav.querySelector('span');
                
                if (stepNumber <= activeStep) {
                    nav.classList.remove('border-transparent', 'text-gray-500');
                    nav.classList.add('border-[#336d66]', 'text-[#336d66]');
                    circle.classList.remove('bg-gray-300', 'text-gray-600');
                    circle.classList.add('bg-[#336d66]', 'text-white');
                } else {
                    nav.classList.remove('border-[#336d66]', 'text-[#336d66]');
                    nav.classList.add('border-transparent', 'text-gray-500');
                    circle.classList.remove('bg-[#336d66]', 'text-white');
                    circle.classList.add('bg-gray-300', 'text-gray-600');
                }
            });
        }

        // Initialize first step
        document.addEventListener('DOMContentLoaded', function() {
            showStep(1);
        });
    </script>
</x-app-layout>

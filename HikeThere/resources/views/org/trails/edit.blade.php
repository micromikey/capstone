<x-app-layout>
    <x-slot name="header">
        <div class="space-y-4">
            <x-trail-breadcrumb currentPage="Edit Trail" />
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('Edit Trail') }}: {{ $trail->trail_name }}
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
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <form method="POST" action="{{ route('org.trails.update', $trail) }}" class="p-6">
                    @csrf
                    @method('PUT')
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Basic Information -->
                        <div class="md:col-span-2">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Basic Information</h3>
                        </div>

                        <div>
                            <x-label for="mountain_name" value="Mountain Name *" />
                            <x-input id="mountain_name" type="text" name="mountain_name" class="mt-1 block w-full" value="{{ old('mountain_name', $trail->mountain_name) }}" required />
                            <x-input-error for="mountain_name" class="mt-2" />
                        </div>

                        <div>
                            <x-label for="trail_name" value="Trail Name *" />
                            <x-input id="trail_name" type="text" name="trail_name" class="mt-1 block w-full" value="{{ old('trail_name', $trail->trail_name) }}" required />
                            <x-input-error for="trail_name" class="mt-2" />
                        </div>

                        <div>
                            <x-label for="location_id" value="Location *" />
                            <select id="location_id" name="location_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-[#336d66] focus:border-[#336d66]" required>
                                <option value="">Select Location</option>
                                @foreach($locations as $location)
                                    <option value="{{ $location->id }}" {{ old('location_id', $trail->location_id) == $location->id ? 'selected' : '' }}>
                                        {{ $location->name }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error for="location_id" class="mt-2" />
                        </div>

                        <div>
                            <x-label for="price" value="Price (₱) *" />
                            <x-input id="price" type="number" name="price" step="0.01" min="0" class="mt-1 block w-full" value="{{ old('price', $trail->price) }}" required />
                            <x-input-error for="price" class="mt-2" />
                        </div>

                        <div class="md:col-span-2">
                            <x-label for="package_inclusions" value="Package Inclusions *" />
                            <textarea id="package_inclusions" name="package_inclusions" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-[#336d66] focus:border-[#336d66]" placeholder="e.g., Guide, Meals, Environmental Fee" required>{{ old('package_inclusions', $trail->package_inclusions) }}</textarea>
                            <x-input-error for="package_inclusions" class="mt-2" />
                        </div>

                        <!-- Difficulty & Duration -->
                        <div class="md:col-span-2">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Difficulty & Duration</h3>
                        </div>

                        <div>
                            <x-label for="difficulty" value="Difficulty Level *" />
                            <select id="difficulty" name="difficulty" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-[#336d66] focus:border-[#336d66]" required>
                                <option value="">Select Difficulty</option>
                                <option value="beginner" {{ old('difficulty', $trail->difficulty) == 'beginner' ? 'selected' : '' }}>Beginner</option>
                                <option value="intermediate" {{ old('difficulty', $trail->difficulty) == 'intermediate' ? 'selected' : '' }}>Intermediate</option>
                                <option value="advanced" {{ old('difficulty', $trail->difficulty) == 'advanced' ? 'selected' : '' }}>Advanced</option>
                            </select>
                            <x-input-error for="difficulty" class="mt-2" />
                        </div>

                        <div>
                            <x-label for="difficulty_description" value="Difficulty Description" />
                            <textarea id="difficulty_description" name="difficulty_description" rows="2" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-[#336d66] focus:border-[#336d66]" placeholder="Describe what makes this trail beginner/intermediate/advanced">{{ old('difficulty_description', $trail->difficulty_description) }}</textarea>
                            <x-input-error for="difficulty_description" class="mt-2" />
                        </div>

                        <div>
                            <x-label for="duration" value="Duration *" />
                            <x-input id="duration" type="text" name="duration" class="mt-1 block w-full" value="{{ old('duration', $trail->duration) }}" placeholder="e.g., 3-4 hours" required />
                            <x-input-error for="duration" class="mt-2" />
                        </div>

                        <div>
                            <x-label for="best_season" value="Best Season *" />
                            <x-input id="best_season" type="text" name="best_season" class="mt-1 block w-full" value="{{ old('best_season', $trail->best_season) }}" placeholder="e.g., November to March" required />
                            <x-input-error for="best_season" class="mt-2" />
                        </div>

                        <!-- Trail Details -->
                        <div class="md:col-span-2">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Trail Details</h3>
                        </div>

                        <div class="md:col-span-2">
                            <x-label for="terrain_notes" value="Terrain Notes *" />
                            <textarea id="terrain_notes" name="terrain_notes" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-[#336d66] focus:border-[#336d66]" placeholder="e.g., Rocky, River Crossings, Dense Forest" required>{{ old('terrain_notes', $trail->terrain_notes) }}</textarea>
                            <x-input-error for="terrain_notes" class="mt-2" />
                        </div>

                        <div class="md:col-span-2">
                            <x-label for="other_trail_notes" value="Other Trail Notes" />
                            <textarea id="other_trail_notes" name="other_trail_notes" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-[#336d66] focus:border-[#336d66]" placeholder="e.g., limited hikers, hike cut-off time, curfew, trail rules, or safety reminders">{{ old('other_trail_notes', $trail->other_trail_notes) }}</textarea>
                            <x-input-error for="other_trail_notes" class="mt-2" />
                        </div>

                        <!-- Permit Information -->
                        <div class="md:col-span-2">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Permit Information</h3>
                        </div>

                        <div class="flex items-center">
                            <input id="permit_required" type="checkbox" name="permit_required" value="1" class="h-4 w-4 text-[#336d66] focus:ring-[#336d66] border-gray-300 rounded" {{ old('permit_required', $trail->permit_required) ? 'checked' : '' }}>
                            <x-label for="permit_required" value="Permit Required?" class="ml-2" />
                        </div>

                        <div class="md:col-span-2">
                            <x-label for="permit_process" value="Permit Process" />
                            <textarea id="permit_process" name="permit_process" rows="2" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-[#336d66] focus:border-[#336d66]" placeholder="e.g., Apply at Municipal Hall / Online LGU Form">{{ old('permit_process', $trail->permit_process) }}</textarea>
                            <x-input-error for="permit_process" class="mt-2" />
                        </div>

                        <!-- Transportation -->
                        <div class="md:col-span-2">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Transportation & Access</h3>
                        </div>

                        <div>
                            <x-label for="departure_point" value="Departure Point *" />
                            <x-input id="departure_point" type="text" name="departure_point" class="mt-1 block w-full" value="{{ old('departure_point', $trail->departure_point) }}" placeholder="e.g., Cubao Terminal" required />
                            <x-input-error for="departure_point" class="mt-2" />
                        </div>

                        <div>
                            <x-label for="transport_options" value="Transport Options *" />
                            <textarea id="transport_options" name="transport_options" rows="2" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-[#336d66] focus:border-[#336d66]" placeholder="e.g., Bus to Tanay, Jeep to Jump-off" required>{{ old('transport_options', $trail->transport_options) }}</textarea>
                            <x-input-error for="transport_options" class="mt-2" />
                        </div>

                        <div class="md:col-span-2">
                            <x-label for="side_trips" value="Side Trips" />
                            <textarea id="side_trips" name="side_trips" rows="2" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-[#336d66] focus:border-[#336d66]" placeholder="e.g., Tinipak River or enter N/A if none">{{ old('side_trips', $trail->side_trips) }}</textarea>
                            <x-input-error for="side_trips" class="mt-2" />
                        </div>

                        <!-- Hiker Information -->
                        <div class="md:col-span-2">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Hiker Information</h3>
                        </div>

                        <div class="md:col-span-2">
                            <x-label for="packing_list" value="Packing List *" />
                            <textarea id="packing_list" name="packing_list" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-[#336d66] focus:border-[#336d66]" placeholder="e.g., Water, Flashlight, Raincoat" required>{{ old('packing_list', $trail->packing_list) }}</textarea>
                            <x-input-error for="packing_list" class="mt-2" />
                        </div>

                        <div class="md:col-span-2">
                            <x-label for="health_fitness" value="Health/Fitness Requirements *" />
                            <textarea id="health_fitness" name="health_fitness" rows="2" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-[#336d66] focus:border-[#336d66]" placeholder="e.g., Moderate fitness recommended, Beginner-friendly" required>{{ old('health_fitness', $trail->health_fitness) }}</textarea>
                            <x-input-error for="health_fitness" class="mt-2" />
                        </div>

                        <div class="md:col-span-2">
                            <x-label for="requirements" value="Other Requirements" />
                            <textarea id="requirements" name="requirements" rows="2" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-[#336d66] focus:border-[#336d66]" placeholder="Any other specific requirements">{{ old('requirements', $trail->requirements) }}</textarea>
                            <x-input-error for="requirements" class="mt-2" />
                        </div>

                        <!-- Safety & Support -->
                        <div class="md:col-span-2">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Safety & Support</h3>
                        </div>

                        <div class="md:col-span-2">
                            <x-label for="emergency_contacts" value="Emergency Contacts *" />
                            <textarea id="emergency_contacts" name="emergency_contacts" rows="2" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-[#336d66] focus:border-[#336d66]" placeholder="e.g., Barangay Rescue – 0917xxxxxxx" required>{{ old('emergency_contacts', $trail->emergency_contacts) }}</textarea>
                            <x-input-error for="emergency_contacts" class="mt-2" />
                        </div>

                        <div>
                            <x-label for="campsite_info" value="Campsite Information" />
                            <textarea id="campsite_info" name="campsite_info" rows="2" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-[#336d66] focus:border-[#336d66]" placeholder="e.g., Tent area at summit or No campsite">{{ old('campsite_info', $trail->campsite_info) }}</textarea>
                            <x-input-error for="campsite_info" class="mt-2" />
                        </div>

                        <div>
                            <x-label for="guide_info" value="Guide Information" />
                            <textarea id="guide_info" name="guide_info" rows="2" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-[#336d66] focus:border-[#336d66]" placeholder="Information about guides">{{ old('guide_info', $trail->guide_info) }}</textarea>
                            <x-input-error for="guide_info" class="mt-2" />
                        </div>

                        <div class="md:col-span-2">
                            <x-label for="environmental_practices" value="Environmental Practices" />
                            <textarea id="environmental_practices" name="environmental_practices" rows="2" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-[#336d66] focus:border-[#336d66]" placeholder="e.g., Leave No Trace">{{ old('environmental_practices', $trail->environmental_practices) }}</textarea>
                            <x-input-error for="environmental_practices" class="mt-2" />
                        </div>

                        <!-- Feedback & Testimonials -->
                        <div class="md:col-span-2">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Feedback & Testimonials</h3>
                        </div>

                        <div class="md:col-span-2">
                            <x-label for="customers_feedback" value="Customers Feedback" />
                            <textarea id="customers_feedback" name="customers_feedback" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-[#336d66] focus:border-[#336d66]" placeholder="e.g., Juan Dela Cruz: Sobrang ganda ng tanawin paakyat, I'm definitely going back here!">{{ old('customers_feedback', $trail->customers_feedback) }}</textarea>
                            <x-input-error for="customers_feedback" class="mt-2" />
                        </div>

                        <div class="md:col-span-2">
                            <x-label for="testimonials_faqs" value="Testimonials / Common FAQs" />
                            <textarea id="testimonials_faqs" name="testimonials_faqs" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-[#336d66] focus:border-[#336d66]" placeholder="Most frequently asked questions from hikers, especially beginners">{{ old('testimonials_faqs', $trail->testimonials_faqs) }}</textarea>
                            <x-input-error for="testimonials_faqs" class="mt-2" />
                        </div>
                    </div>

                    <div class="mt-8 flex justify-end space-x-3">
                        <a href="{{ route('org.trails.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded-lg transition-colors">
                            Cancel
                        </a>
                        <button type="submit" class="bg-[#336d66] hover:bg-[#2a5a54] text-white font-bold py-2 px-4 rounded-lg transition-colors">
                            Update Trail
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>

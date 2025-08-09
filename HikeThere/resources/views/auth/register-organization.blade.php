<x-guest-layout>
    <div x-data="{ 
        step: 1,
        termsChecked: false,
        documentationConfirmed: false,
        formData: {
            organization_name: '',
            organization_description: '',
            email: '',
            phone: '',
            name: '',
            business_permit: '',
            government_id: '',
            additional_docs: []
        }
    }" class="min-h-screen flex items-center justify-center bg-gradient-to-br from-[#336d66]/5 to-white py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-6xl w-full space-y-8 bg-white/80 backdrop-blur-lg p-8 rounded-2xl shadow-xl">
            <!-- Header section remains the same -->
            <div class="text-center">
                <a href="/" class="flex items-center justify-center space-x-3 mb-8">
                    <img src="{{ asset('img/icon1.png') }}" alt="HikeThere Logo" class="h-12 w-auto">
                    <span class="font-bold text-2xl text-[#336d66]">HikeThere</span>
                </a>
                <h2 class="text-3xl font-extrabold text-gray-900 mb-2">Register Organization</h2>
                <p class="text-gray-600">Create your organization's account</p>
            </div>

            <!-- Progress Bar -->
            <div class="relative pt-4">
                <div class="flex items-center justify-between mb-2">
                    <div class="w-1/3 text-center">
                        <button @click="step = 1" :class="{'text-[#336d66] font-medium': step >= 1, 'text-gray-400': step < 1}">
                            Organization Info
                        </button>
                    </div>
                    <div class="w-1/3 text-center">
                        <button @click="step = 2" :class="{'text-[#336d66] font-medium': step >= 2, 'text-gray-400': step < 2}">
                            Documentation
                        </button>
                    </div>
                    <div class="w-1/3 text-center">
                        <button @click="step = 3" :class="{'text-[#336d66] font-medium': step >= 3, 'text-gray-400': step < 3}">
                            Review & Submit
                        </button>
                    </div>
                </div>
                <div class="overflow-hidden h-2 mb-4 flex rounded-full bg-gray-100">
                    <div class="transition-all duration-500 ease-in-out bg-[#336d66]"
                        :class="{
                            'w-1/3': step === 1,
                            'w-2/3': step === 2,
                            'w-full': step === 3
                         }">
                    </div>
                </div>
            </div>

            <x-validation-errors class="mb-4 bg-red-50 text-red-500 p-4 rounded-lg text-sm" />

            <form method="POST" action="{{ route('register.organization') }}" class="mt-8" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="user_type" value="organization">

                <!-- Step 1: Organization Information -->
                <div x-show="step === 1">
                    <div class="space-y-6">
                        <h3 class="text-lg font-semibold text-gray-900">Organization Information</h3>

                        <!-- Organization Name -->
                        <div>
                            <x-label for="organization_name" value="{{ __('Organization Name') }}" />
                            <div class="mt-1 relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                    </svg>
                                </div>
                                <x-input id="organization_name" x-model="formData.organization_name" class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-xl" type="text" name="organization_name" :value="old('organization_name')" required autofocus />
                            </div>
                        </div>

                        <!-- Organization Description -->
                        <div>
                            <x-label for="organization_description" value="{{ __('Organization Description') }}" />
                            <textarea id="organization_description" x-model="formData.organization_description" name="organization_description" rows="4" class="block w-full pl-3 pr-3 py-2 border border-gray-300 rounded-xl" required>{{ old('organization_description') }}</textarea>
                        </div>

                        <!-- Contact Information -->
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <x-label for="email" value="{{ __('Contact Email') }}" />
                                <x-input id="email" x-model="formData.email" class="block w-full" type="email" name="email" :value="old('email')" required />
                            </div>
                            <div>
                                <x-label for="phone" value="{{ __('Contact Phone') }}" />
                                <x-input id="phone" x-model="formData.phone" class="block w-full" type="tel" name="phone" :value="old('phone')" required />
                            </div>
                        </div>

                        <!-- Representative Name -->
                        <div>
                            <x-label for="name" value="{{ __('Representative Name') }}" />
                            <x-input id="name" x-model="formData.name" class="block w-full" type="text" name="name" :value="old('name')" required />
                        </div>

                        <!-- Password Fields - Moved here -->
                        <div class="space-y-4 pt-4 border-t">
                            <h4 class="font-medium text-gray-700">Account Security</h4>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <x-label for="password" value="{{ __('Password') }}" class="mb-1" />
                                    <x-input id="password" class="block w-full" type="password" name="password" required autocomplete="new-password" />
                                </div>
                                <div>
                                    <x-label for="password_confirmation" value="{{ __('Confirm Password') }}" class="mb-1" />
                                    <x-input id="password_confirmation" class="block w-full" type="password" name="password_confirmation" required autocomplete="new-password" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-8 flex justify-end">
                        <button type="button" @click="step = 2"
                            class="px-6 py-2 bg-[#336d66] text-white rounded-xl hover:bg-[#20b6d2] transition-colors">
                            Next Step &rarr;
                        </button>
                    </div>
                </div>

                <!-- Step 2: Documentation -->
                <div x-show="step === 2" x-cloak>
                    <div class="space-y-6">
                        <h3 class="text-lg font-semibold text-gray-900">Required Documentation</h3>

                        <!-- Business Permit -->
                        <div class="space-y-2 bg-gray-50 p-4 rounded-xl">
                            <x-label for="business_permit" value="{{ __('Business Permit') }}" class="mb-1" />
                            <input type="file" id="business_permit" name="business_permit"
                                @change="formData.business_permit = $event.target.files[0]?.name"
                                class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-[#336d66]/10 file:text-[#336d66] hover:file:bg-[#336d66]/20 cursor-pointer"
                                required
                                accept=".pdf,.jpg,.jpeg,.png" />
                            <p class="text-sm text-gray-500">Upload a scanned copy of your business permit (PDF, JPG, PNG)</p>
                        </div>

                        <!-- Government ID -->
                        <div class="space-y-2 bg-gray-50 p-4 rounded-xl">
                            <x-label for="government_id" value="{{ __('Government ID') }}" class="mb-1" />
                            <input type="file" id="government_id" name="government_id"
                                @change="formData.government_id = $event.target.files[0]?.name"
                                class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-[#336d66]/10 file:text-[#336d66] hover:file:bg-[#336d66]/20 cursor-pointer"
                                required
                                accept=".pdf,.jpg,.jpeg,.png" />
                            <p class="text-sm text-gray-500">Upload a valid government ID of the representative (PDF, JPG, PNG)</p>
                        </div>

                        <!-- Additional Documents -->
                        <div class="space-y-2 bg-gray-50 p-4 rounded-xl">
                            <x-label for="additional_docs" value="{{ __('Additional Supporting Documents') }}" class="mb-1" />
                            <input type="file" id="additional_docs" name="additional_docs[]"
                                @change="formData.additional_docs = Array.from($event.target.files)"
                                class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-[#336d66]/10 file:text-[#336d66] hover:file:bg-[#336d66]/20 cursor-pointer"
                                multiple
                                accept=".pdf,.jpg,.jpeg,.png" />
                            <p class="text-sm text-gray-500">Optional: Any additional documents to support your application</p>
                        </div>
                    </div>

                    <div class="mt-8 flex justify-between">
                        <button type="button" @click="step = 1"
                            class="px-6 py-2 text-gray-600 hover:text-[#336d66] transition-colors">
                            &larr; Previous Step
                        </button>
                        <button type="button" @click="step = 3"
                            class="px-6 py-2 bg-[#336d66] text-white rounded-xl hover:bg-[#20b6d2] transition-colors">
                            Next Step &rarr;
                        </button>
                    </div>
                </div>

                <!-- Step 3: Review & Submit -->
                <div x-show="step === 3" x-cloak>
                    <div class="space-y-6">
                        <h3 class="text-lg font-semibold text-gray-900">Review & Submit</h3>

                        <!-- Organization Information Review -->
                        <div class="bg-gray-50 p-6 rounded-xl space-y-4">
                            <h4 class="font-medium text-gray-900">Organization Information</h4>

                            <div class="grid grid-cols-2 gap-6">
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Organization Name</p>
                                    <p class="mt-1" x-text="formData.organization_name || 'Not filled'"></p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Representative Name</p>
                                    <p class="mt-1" x-text="formData.name || 'Not filled'"></p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Contact Email</p>
                                    <p class="mt-1" x-text="formData.email || 'Not filled'"></p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Contact Phone</p>
                                    <p class="mt-1" x-text="formData.phone || 'Not filled'"></p>
                                </div>
                            </div>

                            <div class="pt-4">
                                <p class="text-sm font-medium text-gray-500">Organization Description</p>
                                <p class="mt-1" x-text="formData.organization_description || 'Not filled'"></p>
                            </div>
                        </div>

                        <!-- Document Review -->
                        <div class="bg-gray-50 p-6 rounded-xl space-y-4">
                            <h4 class="font-medium text-gray-900">Uploaded Documents</h4>

                            <div class="space-y-3">
                                <div class="flex items-center space-x-2">
                                    <svg class="h-5 w-5" :class="formData.business_permit ? 'text-[#336d66]' : 'text-gray-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span class="text-sm" x-text="formData.business_permit || 'No business permit uploaded'"></span>
                                </div>

                                <div class="flex items-center space-x-2">
                                    <svg class="h-5 w-5" :class="formData.government_id ? 'text-[#336d66]' : 'text-gray-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span class="text-sm" x-text="formData.government_id || 'No government ID uploaded'"></span>
                                </div>

                                <template x-if="formData.additional_docs.length > 0">
                                    <div class="pl-7">
                                        <p class="text-sm font-medium text-gray-500 mb-2">Additional Documents:</p>
                                        <template x-for="file in formData.additional_docs" :key="file.name">
                                            <div class="text-sm text-gray-600" x-text="file.name"></div>
                                        </template>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <!-- Terms and Privacy Policy -->
                        <div class="max-w-2xl mx-auto space-y-4">
                            <div class="bg-gray-50 p-6 rounded-xl">
                                <h4 class="font-medium text-gray-900 mb-4">Terms & Agreements</h4>

                                <div class="space-y-4">
                                    <div class="flex items-start space-x-3">
                                        <div class="flex items-center h-5">
                                            <input type="checkbox"
                                                id="terms"
                                                name="terms"
                                                required
                                                x-model="termsChecked"
                                                class="h-4 w-4 rounded border-gray-300 text-[#336d66] focus:ring-[#336d66]">
                                        </div>
                                        <div class="text-sm">
                                            <label for="terms" class="font-medium text-gray-700">
                                                I accept the terms of service and privacy policy
                                            </label>
                                            <p class="text-gray-500">
                                                By checking this box, you agree to our
                                                <a href="{{ route('terms.show') }}"
                                                    target="_blank"
                                                    class="text-[#20b6d2] hover:text-[#336d66] transition-colors">
                                                    Terms of Service
                                                </a>
                                                and
                                                <a href="{{ route('policy.show') }}"
                                                    target="_blank"
                                                    class="text-[#20b6d2] hover:text-[#336d66] transition-colors">
                                                    Privacy Policy
                                                </a>
                                            </p>
                                        </div>
                                    </div>

                                    <div class="flex items-start space-x-3">
                                        <div class="flex items-center h-5">
                                            <input type="checkbox"
                                                id="documentation_confirm"
                                                name="documentation_confirm"
                                                required
                                                x-model="documentationConfirmed"
                                                class="h-4 w-4 rounded border-gray-300 text-[#336d66] focus:ring-[#336d66]">
                                        </div>
                                        <div class="text-sm">
                                            <label for="documentation_confirm" class="font-medium text-gray-700">
                                                I confirm all provided information and documents are accurate
                                            </label>
                                            <p class="text-gray-500">
                                                The submitted information will be reviewed by our admin team for verification
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-4 text-sm text-gray-500 bg-gray-100 p-3 rounded-lg">
                                    <p class="flex items-center">
                                        <svg class="h-5 w-5 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        Your account will be pending approval until documents are verified
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="mt-8 flex justify-between">
                            <button type="button"
                                @click="step = 2"
                                class="px-6 py-2 text-gray-600 hover:text-[#336d66] transition-colors">
                                &larr; Previous Step
                            </button>
                            <button type="submit"
                                class="px-8 py-3 bg-[#336d66] text-white rounded-xl hover:bg-[#20b6d2] transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                                :disabled="!termsChecked || !documentationConfirmed">
                                Submit Registration
                            </button>
                        </div>
                    </div>
                </div>
            </form>

            <div class="text-center text-sm text-gray-600 mt-8">
                Already have an account?
                <a href="{{ route('login') }}" class="font-medium text-[#20b6d2] hover:text-[#336d66] transition-colors">
                    Sign in
                </a>
            </div>
        </div>
    </div>

    <!-- Add Alpine.js styles -->
    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
</x-guest-layout>
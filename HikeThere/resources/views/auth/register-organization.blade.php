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
            address: '',
            name: '',
            password: '',
            password_confirmation: '',
            business_permit: '',
            government_id: '',
            additional_docs: []
        },
        validateStep1() {
            if (!this.formData.organization_name.trim()) {
                alert('Please enter Organization Name');
                return false;
            }
            if (!this.formData.organization_description.trim()) {
                alert('Please enter Organization Description');
                return false;
            }
            if (!this.formData.name.trim()) {
                alert('Please enter Representative Name');
                return false;
            }
            if (!this.formData.password || this.formData.password.length < 8) {
                alert('Password must be at least 8 characters');
                return false;
            }
            if (this.formData.password !== this.formData.password_confirmation) {
                alert('Passwords do not match');
                return false;
            }
            return true;
        },
        validateStep2() {
            if (!this.formData.email.trim()) {
                alert('Please enter Email Address');
                return false;
            }
            // Basic email validation
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(this.formData.email)) {
                alert('Please enter a valid email address');
                return false;
            }
            if (!this.formData.phone.trim()) {
                alert('Please enter Phone Number');
                return false;
            }
            if (!this.formData.address.trim()) {
                alert('Please enter Address');
                return false;
            }
            return true;
        },
        validateStep3() {
            const businessPermit = document.getElementById('business_permit');
            const governmentId = document.getElementById('government_id');
            
            console.log('Business Permit input:', businessPermit);
            console.log('Business Permit files:', businessPermit.files);
            console.log('Government ID input:', governmentId);
            console.log('Government ID files:', governmentId.files);
            
            if (!businessPermit.files || businessPermit.files.length === 0) {
                alert('Please upload Business Permit');
                return false;
            }
            
            const businessFile = businessPermit.files[0];
            console.log('Business file details:', {
                name: businessFile.name,
                type: businessFile.type,
                size: businessFile.size
            });
            
            if (!governmentId.files || governmentId.files.length === 0) {
                alert('Please upload Government-issued ID');
                return false;
            }
            
            const govFile = governmentId.files[0];
            console.log('Government ID file details:', {
                name: govFile.name,
                type: govFile.type,
                size: govFile.size
            });
            
            if (!this.documentationConfirmed) {
                alert('Please confirm that all uploaded documents are clear and readable');
                return false;
            }
            return true;
        },
        goToStep(nextStep) {
            if (nextStep === 2 && !this.validateStep1()) return;
            if (nextStep === 3 && !this.validateStep2()) return;
            if (nextStep === 4 && !this.validateStep3()) return;
            this.step = nextStep;
        }
    }" 
    x-init="console.log('Form initialized:', $data)"
    class="min-h-screen flex items-center justify-center bg-gradient-to-br from-[#336d66]/5 to-white py-12 px-4 sm:px-6 lg:px-8">
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
                    <div class="w-1/4 text-center">
                        <button type="button" @click="step = 1" :class="{'text-[#336d66] font-medium': step >= 1, 'text-gray-400': step < 1}">
                            Organization Info
                        </button>
                    </div>
                    <div class="w-1/4 text-center">
                        <button type="button" @click="if(step >= 2 || validateStep1()) goToStep(2)" :class="{'text-[#336d66] font-medium': step >= 2, 'text-gray-400': step < 2, 'cursor-not-allowed': step < 2}">
                            Contact & Address
                        </button>
                    </div>
                    <div class="w-1/4 text-center">
                        <button type="button" @click="if(step >= 3 || (validateStep1() && validateStep2())) goToStep(3)" :class="{'text-[#336d66] font-medium': step >= 3, 'text-gray-400': step < 3, 'cursor-not-allowed': step < 3}">
                            Documentation
                        </button>
                    </div>
                    <div class="w-1/4 text-center">
                        <button type="button" @click="if(step >= 4 || (validateStep1() && validateStep2() && validateStep3())) goToStep(4)" :class="{'text-[#336d66] font-medium': step >= 4, 'text-gray-400': step < 4, 'cursor-not-allowed': step < 4}">
                            Review & Submit
                        </button>
                    </div>
                </div>
                <div class="overflow-hidden h-2 mb-4 flex rounded-full bg-gray-100">
                    <div class="transition-all duration-500 ease-in-out bg-[#336d66]"
                        :class="{
                            'w-1/4': step === 1,
                            'w-2/4': step === 2,
                            'w-3/4': step === 3,
                            'w-full': step === 4
                         }">
                    </div>
                </div>
            </div>

            <x-validation-errors class="mb-4 bg-red-50 text-red-500 p-4 rounded-lg text-sm" />

            <form method="POST" action="{{ route('register.organization.store') }}" class="mt-8" enctype="multipart/form-data" @submit="console.log('Form submitting with data:', formData)">
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
                                <x-input id="organization_name" x-model="formData.organization_name" @input="console.log('Organization name changed:', formData.organization_name)" class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-xl" type="text" name="organization_name" required autofocus />
                            </div>
                        </div>

                        <!-- Organization Description -->
                        <div>
                            <x-label for="organization_description" value="{{ __('Organization Description') }}" />
                            <textarea id="organization_description" x-model="formData.organization_description" @input="console.log('Organization description changed:', formData.organization_description)" name="organization_description" rows="4" class="block w-full pl-3 pr-3 py-2 border border-gray-300 rounded-xl" required></textarea>
                        </div>

                        <!-- Representative Name -->
                        <div>
                            <x-label for="name" value="{{ __('Representative Name') }}" />
                            <x-input id="name" x-model="formData.name" @input="console.log('Name changed:', formData.name)" class="block w-full" type="text" name="name" required />
                        </div>

                        <!-- Password Fields -->
                        <div class="space-y-4 pt-4 border-t">
                            <h4 class="font-medium text-gray-700">Account Security</h4>
                            <div class="space-y-4">
                                <div>
                                    <x-label for="password" value="{{ __('Password') }}" class="mb-1" />
                                    <x-input id="password" x-model="formData.password" class="block w-full" type="password" name="password" required autocomplete="new-password" oninput="checkPasswordStrength(this.value)" />
                                    
                                    <!-- Password Strength Indicator -->
                                    <div id="password-strength" class="mt-2 hidden">
                                        <div class="flex items-center gap-2 mb-2">
                                            <div class="flex-1 h-2 bg-gray-200 rounded-full overflow-hidden">
                                                <div id="strength-bar" class="h-full transition-all duration-300 rounded-full" style="width: 0%"></div>
                                            </div>
                                            <span id="strength-text" class="text-xs font-medium"></span>
                                        </div>
                                        <div class="text-xs space-y-1">
                                            <div id="req-length" class="flex items-center gap-1 text-gray-500">
                                                <span class="requirement-icon">○</span> At least 8 characters
                                            </div>
                                            <div id="req-uppercase" class="flex items-center gap-1 text-gray-500">
                                                <span class="requirement-icon">○</span> One uppercase letter
                                            </div>
                                            <div id="req-lowercase" class="flex items-center gap-1 text-gray-500">
                                                <span class="requirement-icon">○</span> One lowercase letter
                                            </div>
                                            <div id="req-number" class="flex items-center gap-1 text-gray-500">
                                                <span class="requirement-icon">○</span> One number
                                            </div>
                                            <div id="req-special" class="flex items-center gap-1 text-gray-500">
                                                <span class="requirement-icon">○</span> One special character (!@#$%^&*)
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div>
                                    <x-label for="password_confirmation" value="{{ __('Confirm Password') }}" class="mb-1" />
                                    <x-input id="password_confirmation" 
                                        x-model="formData.password_confirmation" 
                                        @input="checkPasswordMatch()"
                                        class="block w-full" 
                                        type="password" 
                                        name="password_confirmation" 
                                        required 
                                        autocomplete="new-password" />
                                    
                                    <!-- Password Match Indicator -->
                                    <div x-show="formData.password_confirmation.length > 0" 
                                         x-cloak 
                                         class="mt-2 text-sm">
                                        <div x-show="formData.password === formData.password_confirmation && formData.password_confirmation.length > 0" 
                                             class="flex items-center gap-2 text-green-600">
                                            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                            </svg>
                                            <span class="font-medium">Passwords match</span>
                                        </div>
                                        <div x-show="formData.password !== formData.password_confirmation" 
                                             class="flex items-center gap-2 text-red-600">
                                            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                            </svg>
                                            <span class="font-medium">Passwords do not match</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-8 flex justify-end">
                        <button type="button" @click="goToStep(2)"
                            class="px-6 py-2 bg-[#336d66] text-white rounded-xl hover:bg-[#20b6d2] transition-colors">
                            Next Step &rarr;
                        </button>
                    </div>
                </div>

                <!-- Step 2: Contact Information & Address -->
                <div x-show="step === 2" x-cloak>
                    <div class="space-y-6">
                        <h3 class="text-lg font-semibold text-gray-900">Contact Information & Address</h3>

                        <!-- Contact Information -->
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <x-label for="email" value="{{ __('Contact Email') }}" />
                                <x-input id="email" x-model="formData.email" @input="console.log('Email changed:', formData.email)" class="block w-full" type="email" name="email" required />
                            </div>
                            <div>
                                <x-label for="phone" value="{{ __('Contact Phone') }}" />
                                <x-input id="phone" x-model="formData.phone" @input="console.log('Phone changed:', formData.phone)" class="block w-full" type="tel" name="phone" required />
                            </div>
                        </div>

                        <!-- Address Field -->
                        <div>
                            <x-label for="address" value="{{ __('Address') }}" />
                            <textarea id="address" x-model="formData.address" @input="console.log('Address changed:', formData.address)" name="address" rows="3" class="block w-full pl-3 pr-3 py-2 border border-gray-300 rounded-xl" placeholder="Enter your complete address" required></textarea>
                        </div>
                    </div>

                    <div class="mt-8 flex justify-between">
                        <button type="button" @click="step = 1"
                            class="px-6 py-2 text-gray-600 hover:text-[#336d66] transition-colors">
                            &larr; Previous Step
                        </button>
                        <button type="button" @click="goToStep(3)"
                            class="px-6 py-2 bg-[#336d66] text-white rounded-xl hover:bg-[#20b6d2] transition-colors">
                            Next Step &rarr;
                        </button>
                    </div>
                </div>

                <!-- Step 3: Documentation -->
                <div x-show="step === 3" x-cloak>
                    <div class="space-y-6">
                        <h3 class="text-lg font-semibold text-gray-900">Required Documentation</h3>

                        <!-- Business Permit -->
                        <div class="space-y-2 bg-gray-50 p-4 rounded-xl">
                            <x-label for="business_permit" value="{{ __('Business Permit') }}" class="mb-1" />
                            <input type="file" id="business_permit" name="business_permit"
                                @change="formData.business_permit = $event.target.files[0]?.name || ''; console.log('Business permit file:', $event.target.files[0])"
                                class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-[#336d66]/10 file:text-[#336d66] hover:file:bg-[#336d66]/20 cursor-pointer"
                                accept=".pdf,.jpg,.jpeg,.png,.doc,.docx" />
                            <p class="text-sm text-gray-500">Upload a scanned copy of your business permit (PDF, JPG, PNG, DOC, DOCX - Max 10MB)</p>
                            <p x-show="formData.business_permit" class="text-xs text-green-600 mt-1">
                                ✓ File selected: <span x-text="formData.business_permit"></span>
                            </p>
                        </div>

                        <!-- Government ID -->
                        <div class="space-y-2 bg-gray-50 p-4 rounded-xl">
                            <x-label for="government_id" value="{{ __('Government ID') }}" class="mb-1" />
                            <input type="file" id="government_id" name="government_id"
                                @change="formData.government_id = $event.target.files[0]?.name || ''; console.log('Government ID file:', $event.target.files[0])"
                                class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-[#336d66]/10 file:text-[#336d66] hover:file:bg-[#336d66]/20 cursor-pointer"
                                accept=".pdf,.jpg,.jpeg,.png,.doc,.docx" />
                            <p class="text-sm text-gray-500">Upload a valid government ID of the representative (PDF, JPG, PNG, DOC, DOCX - Max 10MB)</p>
                            <p x-show="formData.government_id" class="text-xs text-green-600 mt-1">
                                ✓ File selected: <span x-text="formData.government_id"></span>
                            </p>
                        </div>

                        <!-- Additional Documents -->
                        <div class="space-y-2 bg-gray-50 p-4 rounded-xl">
                            <x-label for="additional_docs" value="{{ __('Additional Supporting Documents') }}" class="mb-1" />
                            <input type="file" id="additional_documents" name="additional_documents[]"
                                @change="formData.additional_docs = Array.from($event.target.files).map(f => f.name); console.log('Additional docs selected:', Array.from($event.target.files).map(f => f.name))"
                                class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-[#336d66]/10 file:text-[#336d66] hover:file:bg-[#336d66]/20 cursor-pointer"
                                multiple
                                accept=".pdf,.jpg,.jpeg,.png,.doc,.docx" />
                            <p class="text-sm text-gray-500">Optional: Any additional documents to support your application (PDF, JPG, PNG, DOC, DOCX - Max 10MB each)</p>
                        </div>

                        <!-- Document Confirmation Checkbox -->
                        <div class="bg-blue-50 border border-blue-200 p-4 rounded-xl mt-6">
                            <div class="flex items-start space-x-3">
                                <div class="flex items-center h-5 mt-0.5">
                                    <input type="checkbox"
                                        id="documentation_confirm_step3"
                                        name="documentation_confirm"
                                        x-model="documentationConfirmed"
                                        @change="console.log('Documentation checkbox changed:', documentationConfirmed)"
                                        class="h-4 w-4 rounded border-gray-300 text-[#336d66] focus:ring-[#336d66]">
                                </div>
                                <div class="text-sm">
                                    <label for="documentation_confirm_step3" class="font-medium text-gray-700 cursor-pointer">
                                        I confirm that all uploaded documents are clear and readable
                                    </label>
                                    <p class="text-gray-600 mt-1">
                                        Please ensure all documents are legible and properly scanned before proceeding.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-8 flex justify-between">
                        <button type="button" @click="step = 2"
                            class="px-6 py-2 text-gray-600 hover:text-[#336d66] transition-colors">
                            &larr; Previous Step
                        </button>
                        <button type="button" @click="goToStep(4)"
                            class="px-6 py-2 bg-[#336d66] text-white rounded-xl hover:bg-[#20b6d2] transition-colors">
                            Next Step &rarr;
                        </button>
                    </div>
                </div>

                <!-- Step 4: Review & Submit -->
                <div x-show="step === 4" x-cloak>
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
                                <p class="text-sm font-medium text-gray-500">Address</p>
                                <p class="mt-1" x-text="formData.address || 'Not filled'"></p>
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
                                                @change="console.log('Terms checkbox changed:', termsChecked)"
                                                class="h-4 w-4 rounded border-gray-300 text-[#336d66] focus:ring-[#336d66]">
                                        </div>
                                        <div class="text-sm">
                                            <label for="terms" class="font-medium text-gray-700">
                                                I accept the terms of service and privacy policy
                                            </label>
                                            <p class="text-gray-500">
                                                By checking this box, you agree to our
                                                <a href="{{ route('terms') }}"
                                                    target="_blank"
                                                    class="text-[#20b6d2] hover:text-[#336d66] transition-colors">
                                                    Terms of Service
                                                </a>
                                                and
                                                <a href="{{ route('privacy') }}"
                                                    target="_blank"
                                                    class="text-[#20b6d2] hover:text-[#336d66] transition-colors">
                                                    Privacy Policy
                                                </a>
                                            </p>
                                        </div>
                                    </div>

                                    <!-- Documentation confirmation note - already checked in Step 3 -->
                                    <div class="flex items-start space-x-3">
                                        <div class="flex items-center h-5">
                                            <svg class="h-5 w-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                        <div class="text-sm">
                                            <p class="font-medium text-gray-700">
                                                Documentation confirmed
                                            </p>
                                            <p class="text-gray-500">
                                                You confirmed all provided documents are clear and readable
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

                        <!-- Form Status Indicator -->
                        <div class="mt-4 p-4 bg-gray-50 rounded-xl">
                            <h5 class="font-medium text-gray-900 mb-2">Form Status</h5>
                            <div class="space-y-2 text-sm">
                                <div class="flex items-center space-x-2">
                                    <span class="w-3 h-3 rounded-full" :class="termsChecked ? 'bg-green-500' : 'bg-red-500'"></span>
                                    <span :class="termsChecked ? 'text-green-700' : 'text-red-700'">
                                        Terms & Privacy Policy: <span x-text="termsChecked ? 'Accepted' : 'Not Accepted'"></span>
                                    </span>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <span class="w-3 h-3 rounded-full" :class="documentationConfirmed ? 'bg-green-500' : 'bg-red-500'"></span>
                                    <span :class="documentationConfirmed ? 'text-green-700' : 'text-red-700'">
                                        Documentation Confirmed: <span x-text="documentationConfirmed ? 'Confirmed' : 'Not Confirmed'"></span>
                                    </span>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <span class="w-3 h-3 rounded-full" :class="formData.organization_name && formData.email && formData.phone && formData.address ? 'bg-green-500' : 'bg-red-500'"></span>
                                    <span :class="formData.organization_name && formData.email && formData.phone && formData.address ? 'text-green-700' : 'text-red-700'">
                                        Required Fields: <span x-text="formData.organization_name && formData.email && formData.phone && formData.address ? 'Complete' : 'Incomplete'"></span>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="mt-8 flex justify-between">
                            <button type="button"
                                @click="step = 3"
                                class="px-6 py-2 text-gray-600 hover:text-[#336d66] transition-colors">
                                &larr; Previous Step
                            </button>
                            <button type="submit"
                                class="px-8 py-3 bg-[#336d66] text-white rounded-xl hover:bg-[#20b6d2] transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                                :disabled="!termsChecked || !documentationConfirmed"
                                @click="console.log('Form Data:', formData); console.log('Terms Checked:', termsChecked); console.log('Documentation Confirmed:', documentationConfirmed)">
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

<script>
function checkPasswordStrength(password) {
    const strengthIndicator = document.getElementById('password-strength');
    const strengthBar = document.getElementById('strength-bar');
    const strengthText = document.getElementById('strength-text');
    
    // Show indicator when user starts typing
    if (password.length > 0) {
        strengthIndicator.classList.remove('hidden');
    } else {
        strengthIndicator.classList.add('hidden');
        return;
    }
    
    // Check requirements
    const requirements = {
        length: password.length >= 8,
        uppercase: /[A-Z]/.test(password),
        lowercase: /[a-z]/.test(password),
        number: /[0-9]/.test(password),
        special: /[!@#$%^&*(),.?":{}|<>]/.test(password)
    };
    
    // Update requirement indicators
    updateRequirement('req-length', requirements.length);
    updateRequirement('req-uppercase', requirements.uppercase);
    updateRequirement('req-lowercase', requirements.lowercase);
    updateRequirement('req-number', requirements.number);
    updateRequirement('req-special', requirements.special);
    
    // Calculate strength
    const fulfilled = Object.values(requirements).filter(Boolean).length;
    let strength = 0;
    let strengthLabel = '';
    let barColor = '';
    
    if (fulfilled === 5) {
        strength = 100;
        strengthLabel = 'Strong';
        barColor = 'bg-green-500';
    } else if (fulfilled >= 3) {
        strength = 60;
        strengthLabel = 'Medium';
        barColor = 'bg-yellow-500';
    } else {
        strength = 30;
        strengthLabel = 'Weak';
        barColor = 'bg-red-500';
    }
    
    // Update bar
    strengthBar.style.width = strength + '%';
    strengthBar.className = 'h-full transition-all duration-300 rounded-full ' + barColor;
    strengthText.textContent = strengthLabel;
    strengthText.className = 'text-xs font-medium ' + (
        barColor === 'bg-green-500' ? 'text-green-600' :
        barColor === 'bg-yellow-500' ? 'text-yellow-600' : 'text-red-600'
    );
}

function updateRequirement(elementId, isMet) {
    const element = document.getElementById(elementId);
    const icon = element.querySelector('.requirement-icon');
    
    if (isMet) {
        element.classList.remove('text-gray-500');
        element.classList.add('text-green-600');
        icon.textContent = '✓';
    } else {
        element.classList.remove('text-green-600');
        element.classList.add('text-gray-500');
        icon.textContent = '○';
    }
}

function checkPasswordMatch() {
    // This function is called from Alpine.js context
    // The actual matching logic is handled by Alpine's reactive data binding
    // No additional JavaScript needed as Alpine handles the x-show directives
    console.log('Password match check triggered');
}
</script>
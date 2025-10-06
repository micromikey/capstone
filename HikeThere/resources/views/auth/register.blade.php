<x-guest-layout>
    <div x-data="{ 
        password: '',
        password_confirmation: '',
        termsChecked: false,
        guidelinesChecked: false
    }" class="min-h-screen flex items-center justify-center bg-gradient-to-br from-[#336d66]/5 to-white py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8 bg-white/80 backdrop-blur-lg p-8 rounded-2xl shadow-xl">
            <div class="text-center">
                <a href="/" class="flex items-center justify-center space-x-3 mb-8">
                    <img src="{{ asset('img/icon1.png') }}" alt="HikeThere Logo" class="h-12 w-auto">
                    <span class="font-bold text-2xl text-[#336d66]">HikeThere</span>
                </a>
                <h2 class="text-3xl font-extrabold text-gray-900 mb-2">Create Hiker Account</h2>
                <p class="text-gray-600">Start your hiking journey today</p>
            </div>

            <x-validation-errors class="mb-4 bg-red-50 text-red-500 p-4 rounded-lg text-sm" />

            <form method="POST" action="{{ route('register') }}" class="mt-8 space-y-6">
                @csrf
                <input type="hidden" name="user_type" value="hiker">

                <div class="space-y-4">
                    <div>
                        <x-label for="name" value="{{ __('Name') }}" class="text-gray-700 font-medium" />
                        <div class="mt-1 relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </div>
                            <x-input id="name" class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-xl focus:ring-[#20b6d2] focus:border-[#20b6d2] transition duration-150" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" placeholder="Enter your full name" />
                        </div>
                    </div>

                    <div>
                        <x-label for="email" value="{{ __('Email') }}" class="text-gray-700 font-medium" />
                        <div class="mt-1 relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
                                </svg>
                            </div>
                            <x-input id="email" class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-xl focus:ring-[#20b6d2] focus:border-[#20b6d2] transition duration-150" type="email" name="email" :value="old('email')" required autocomplete="username" placeholder="Enter your email" />
                        </div>
                    </div>

                    <div>
                        <x-label for="password" value="{{ __('Password') }}" class="text-gray-700 font-medium" />
                        <div class="mt-1 relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                            </div>
                            <x-input id="password" 
                                x-model="password"
                                class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-xl focus:ring-[#20b6d2] focus:border-[#20b6d2] transition duration-150" 
                                type="password" 
                                name="password" 
                                required 
                                autocomplete="new-password" 
                                placeholder="Create a password" 
                                oninput="checkPasswordStrength(this.value)" />
                        </div>
                        
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
                        <x-label for="password_confirmation" value="{{ __('Confirm Password') }}" class="text-gray-700 font-medium" />
                        <div class="mt-1 relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                            </div>
                            <x-input id="password_confirmation" 
                                x-model="password_confirmation"
                                class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-xl focus:ring-[#20b6d2] focus:border-[#20b6d2] transition duration-150" 
                                type="password" 
                                name="password_confirmation" 
                                required 
                                autocomplete="new-password" 
                                placeholder="Confirm your password" />
                        </div>
                        
                        <!-- Password Match Indicator -->
                        <div x-show="password_confirmation.length > 0" 
                             x-cloak 
                             class="mt-2 text-sm">
                            <div x-show="password === password_confirmation && password_confirmation.length > 0" 
                                 class="flex items-center gap-2 text-green-600">
                                <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                                <span class="font-medium">Passwords match</span>
                            </div>
                            <div x-show="password !== password_confirmation" 
                                 class="flex items-center gap-2 text-red-600">
                                <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                </svg>
                                <span class="font-medium">Passwords do not match</span>
                            </div>
                        </div>
                    </div>

                    <!-- Terms and Privacy Policy Section -->
                    <div class="space-y-4 pt-4 border-t border-gray-200">
                        <h4 class="font-medium text-gray-700">Terms & Agreements</h4>

                        <div class="bg-gray-50 p-4 rounded-xl space-y-4">
                            <!-- Terms Checkbox -->
                            <div class="flex items-start space-x-3">
                                <div class="flex items-center h-5">
                                    <input type="checkbox"
                                        id="terms"
                                        name="terms"
                                        value="1"
                                        x-model="termsChecked"
                                        class="h-4 w-4 rounded border-gray-300 text-[#336d66] focus:ring-[#336d66]">
                                </div>
                                <div class="text-sm">
                                    <label for="terms" class="font-medium text-gray-700 cursor-pointer">
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

                            <!-- Guidelines Checkbox -->
                            <div class="flex items-start space-x-3">
                                <div class="flex items-center h-5">
                                    <input type="checkbox"
                                        id="guidelines"
                                        name="guidelines"
                                        value="1"
                                        x-model="guidelinesChecked"
                                        class="h-4 w-4 rounded border-gray-300 text-[#336d66] focus:ring-[#336d66]">
                                </div>
                                <div class="text-sm">
                                    <label for="guidelines" class="font-medium text-gray-700 cursor-pointer">
                                        I agree to follow hiking safety guidelines
                                    </label>
                                    <p class="text-gray-500">
                                        I understand and will follow proper hiking etiquette and safety protocols
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Debug Info (remove after testing) -->
                    <div class="text-xs bg-blue-50 p-3 rounded-lg space-y-1">
                        <div>Terms Checked: <span x-text="termsChecked"></span></div>
                        <div>Guidelines Checked: <span x-text="guidelinesChecked"></span></div>
                        <div>Button Disabled: <span x-text="!termsChecked || !guidelinesChecked"></span></div>
                    </div>

                    <!-- Submit Button -->
                    <div>
                        <button type="submit"
                            class="group relative w-full flex justify-center py-3 px-4 border border-transparent rounded-xl text-white bg-[#336d66] hover:bg-[#20b6d2] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#20b6d2] transition-colors duration-300 font-medium disabled:opacity-50 disabled:cursor-not-allowed"
                            :disabled="!termsChecked || !guidelinesChecked">
                            {{ __('Register') }}
                        </button>
                    </div>

                    <!-- Sign In Link -->
                    <div class="text-center text-sm text-gray-600">
                        Already have an account?
                        <a href="{{ route('login') }}" class="font-medium text-[#20b6d2] hover:text-[#336d66] transition-colors">
                            Sign in
                        </a>
                    </div>
                </div>
            </form>
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
</script>
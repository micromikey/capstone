<x-guest-layout>
    <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-[#336d66]/5 to-white py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-4xl w-full space-y-8 bg-white/80 backdrop-blur-lg p-8 rounded-2xl shadow-xl">
            <div class="text-center">
                <a href="/" class="flex items-center justify-center space-x-3 mb-8">
                    <img src="{{ asset('img/icon1.png') }}" alt="HikeThere Logo" class="h-12 w-auto">
                    <span class="font-bold text-2xl text-[#336d66]">HikeThere</span>
                </a>
                <h2 class="text-3xl font-extrabold text-gray-900 mb-2">Privacy Policy</h2>
                <p class="text-gray-600">Last updated: {{ date('F d, Y') }}</p>
            </div>

            <div class="prose max-w-none mt-8">
                <h3>1. Information We Collect</h3>
                <p>We collect information that you provide directly to us, including:</p>
                <ul>
                    <li>Name and contact information</li>
                    <li>Organization details (for organization accounts)</li>
                    <li>Profile information</li>
                    <li>Content you post or share</li>
                </ul>

                <h3>2. How We Use Your Information</h3>
                <p>We use the information we collect to:</p>
                <ul>
                    <li>Provide and maintain our services</li>
                    <li>Process your transactions</li>
                    <li>Send you notifications and updates</li>
                    <li>Improve our services</li>
                </ul>

                <h3>3. Information Sharing</h3>
                <p>We may share your information with:</p>
                <ul>
                    <li>Other users (as per your settings)</li>
                    <li>Service providers</li>
                    <li>Legal authorities when required</li>
                </ul>

                <h3>4. Data Security</h3>
                <p>We implement appropriate security measures to protect your information.</p>

                <h3>5. Your Rights</h3>
                <p>You have the right to:</p>
                <ul>
                    <li>Access your personal data</li>
                    <li>Correct inaccurate data</li>
                    <li>Request deletion of your data</li>
                    <li>Opt-out of communications</li>
                </ul>
            </div>

            <div class="mt-8 text-center">
                <a href="{{ url()->previous() }}" class="text-[#20b6d2] hover:text-[#336d66] transition-colors">
                    &larr; Go back
                </a>
            </div>
        </div>
    </div>
</x-guest-layout>

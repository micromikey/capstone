<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('About HikeThere - For Organizations') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6 lg:p-8">
                    <!-- Hero Section -->
                    <div class="text-center mb-12">
                        <img src="{{ asset('img/icon1.png') }}" alt="HikeThere Logo" class="h-24 w-auto mx-auto mb-6">
                        <h1 class="text-4xl font-bold text-gray-900 mb-4">HikeThere for Organizations</h1>
                        <p class="text-xl text-gray-600 max-w-3xl mx-auto mb-6">
                            Your comprehensive platform for managing hiking trails, organizing events, and connecting with passionate hikers.
                        </p>
                    </div>

                    <!-- Mission Section -->
                    <div class="mb-12 bg-gradient-to-r from-blue-50 to-green-50 p-8 rounded-lg">
                        <h2 class="text-3xl font-bold text-gray-900 mb-4">Empowering Organizations</h2>
                        <p class="text-lg text-gray-700 leading-relaxed mb-4">
                            HikeThere provides hiking organizations, tour operators, and trail management companies with the tools 
                            they need to showcase their trails, manage events, handle bookings, and grow their community presence. 
                            We're here to help you focus on what you do best—creating unforgettable outdoor experiences.
                        </p>
                        <p class="text-lg text-gray-700 leading-relaxed">
                            Whether you're a small local guide service or a large outdoor recreation company, our platform scales with 
                            your needs. Join hundreds of trusted organizations already using HikeThere to streamline operations, 
                            reach more customers, and grow their business.
                        </p>
                    </div>

                    <!-- Features Grid for Organizations -->
                    <div class="mb-12">
                        <h2 class="text-3xl font-bold text-gray-900 mb-8 text-center">Platform Features</h2>
                        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                            <!-- Feature 1 -->
                            <div class="bg-white border border-gray-200 rounded-lg p-6 hover:shadow-lg transition duration-300">
                                <div class="text-blue-600 mb-4">
                                    <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path>
                                    </svg>
                                </div>
                                <h3 class="text-xl font-semibold mb-2">Trail Management</h3>
                                <p class="text-gray-600">
                                    Create and manage detailed trail listings with photos, maps, difficulty ratings, and comprehensive information.
                                </p>
                            </div>

                            <!-- Feature 2 -->
                            <div class="bg-white border border-gray-200 rounded-lg p-6 hover:shadow-lg transition duration-300">
                                <div class="text-green-600 mb-4">
                                    <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                                <h3 class="text-xl font-semibold mb-2">Event Organization</h3>
                                <p class="text-gray-600">
                                    Schedule and manage hiking events with custom dates, batch management, and capacity controls.
                                </p>
                            </div>

                            <!-- Feature 3 -->
                            <div class="bg-white border border-gray-200 rounded-lg p-6 hover:shadow-lg transition duration-300">
                                <div class="text-purple-600 mb-4">
                                    <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                                    </svg>
                                </div>
                                <h3 class="text-xl font-semibold mb-2">Booking Management</h3>
                                <p class="text-gray-600">
                                    Handle bookings efficiently with real-time availability tracking, status updates, and payment verification.
                                </p>
                            </div>

                            <!-- Feature 4 -->
                            <div class="bg-white border border-gray-200 rounded-lg p-6 hover:shadow-lg transition duration-300">
                                <div class="text-yellow-600 mb-4">
                                    <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                    </svg>
                                </div>
                                <h3 class="text-xl font-semibold mb-2">Payment Integration</h3>
                                <p class="text-gray-600">
                                    Accept payments through GCash or manual verification, with flexible payment setup options.
                                </p>
                            </div>

                            <!-- Feature 5 -->
                            <div class="bg-white border border-gray-200 rounded-lg p-6 hover:shadow-lg transition duration-300">
                                <div class="text-red-600 mb-4">
                                    <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                                    </svg>
                                </div>
                                <h3 class="text-xl font-semibold mb-2">Smart Notifications</h3>
                                <p class="text-gray-600">
                                    Stay updated with real-time notifications for bookings, payments, and customer interactions.
                                </p>
                            </div>

                            <!-- Feature 6 -->
                            <div class="bg-white border border-gray-200 rounded-lg p-6 hover:shadow-lg transition duration-300">
                                <div class="text-indigo-600 mb-4">
                                    <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                    </svg>
                                </div>
                                <h3 class="text-xl font-semibold mb-2">Community Building</h3>
                                <p class="text-gray-600">
                                    Build your following, engage with hikers, receive reviews, and grow your organization's reputation.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- How It Works Section -->
                    <div class="mb-12 bg-gray-50 p-8 rounded-lg">
                        <h2 class="text-3xl font-bold text-gray-900 mb-6 text-center">How It Works</h2>
                        <div class="space-y-6">
                            <div class="flex items-start">
                                <div class="flex-shrink-0 w-12 h-12 bg-blue-600 text-white rounded-full flex items-center justify-center font-bold text-xl">
                                    1
                                </div>
                                <div class="ml-6">
                                    <h3 class="text-xl font-semibold mb-2">Create Your Trails</h3>
                                    <p class="text-gray-600">
                                        Add your hiking trails with detailed information, stunning photos, and accurate GPS coordinates.
                                    </p>
                                </div>
                            </div>

                            <div class="flex items-start">
                                <div class="flex-shrink-0 w-12 h-12 bg-green-600 text-white rounded-full flex items-center justify-center font-bold text-xl">
                                    2
                                </div>
                                <div class="ml-6">
                                    <h3 class="text-xl font-semibold mb-2">Schedule Events</h3>
                                    <p class="text-gray-600">
                                        Set up hiking events with specific dates, batch sizes, and pricing to accommodate different group sizes.
                                    </p>
                                </div>
                            </div>

                            <div class="flex items-start">
                                <div class="flex-shrink-0 w-12 h-12 bg-purple-600 text-white rounded-full flex items-center justify-center font-bold text-xl">
                                    3
                                </div>
                                <div class="ml-6">
                                    <h3 class="text-xl font-semibold mb-2">Manage Bookings</h3>
                                    <p class="text-gray-600">
                                        Receive and manage bookings through our intuitive dashboard. Track capacity, verify payments, and communicate with customers.
                                    </p>
                                </div>
                            </div>

                            <div class="flex items-start">
                                <div class="flex-shrink-0 w-12 h-12 bg-yellow-600 text-white rounded-full flex items-center justify-center font-bold text-xl">
                                    4
                                </div>
                                <div class="ml-6">
                                    <h3 class="text-xl font-semibold mb-2">Grow Your Community</h3>
                                    <p class="text-gray-600">
                                        Build your follower base, receive reviews, and establish your organization as a trusted hiking provider.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Benefits Section -->
                    <div class="mb-12">
                        <h2 class="text-3xl font-bold text-gray-900 mb-6 text-center">Why Choose HikeThere?</h2>
                        <div class="grid md:grid-cols-2 gap-8">
                            <div class="bg-white border-l-4 border-blue-600 p-6 rounded-r-lg shadow-md">
                                <h3 class="text-xl font-semibold mb-3 text-blue-700">Streamlined Operations</h3>
                                <p class="text-gray-600">
                                    Manage everything from a single dashboard—trails, events, bookings, and payments all in one place.
                                </p>
                            </div>
                            <div class="bg-white border-l-4 border-green-600 p-6 rounded-r-lg shadow-md">
                                <h3 class="text-xl font-semibold mb-3 text-green-700">Increased Visibility</h3>
                                <p class="text-gray-600">
                                    Reach thousands of hiking enthusiasts actively looking for their next adventure on our platform.
                                </p>
                            </div>
                            <div class="bg-white border-l-4 border-purple-600 p-6 rounded-r-lg shadow-md">
                                <h3 class="text-xl font-semibold mb-3 text-purple-700">Flexible Payment Options</h3>
                                <p class="text-gray-600">
                                    Choose between automated GCash payments or manual verification to suit your business needs.
                                </p>
                            </div>
                            <div class="bg-white border-l-4 border-yellow-600 p-6 rounded-r-lg shadow-md">
                                <h3 class="text-xl font-semibold mb-3 text-yellow-700">Real-time Updates</h3>
                                <p class="text-gray-600">
                                    Get instant notifications about new bookings, payments, and customer inquiries.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Advanced Features & Analytics -->
                    <div class="mb-12">
                        <h2 class="text-3xl font-bold text-gray-900 mb-6 text-center">Advanced Platform Capabilities</h2>
                        <div class="grid md:grid-cols-2 gap-8">
                            <div class="bg-gradient-to-br from-blue-50 to-blue-100 p-6 rounded-lg border border-blue-200">
                                <h3 class="text-2xl font-semibold mb-4 text-blue-900">
                                    <svg class="w-8 h-8 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                    </svg>
                                    Booking Analytics
                                </h3>
                                <ul class="space-y-2 text-gray-700">
                                    <li>✓ Track booking trends and patterns</li>
                                    <li>✓ Monitor popular trails and events</li>
                                    <li>✓ Analyze customer demographics</li>
                                    <li>✓ Revenue tracking and forecasting</li>
                                    <li>✓ Capacity utilization reports</li>
                                </ul>
                            </div>
                            <div class="bg-gradient-to-br from-green-50 to-green-100 p-6 rounded-lg border border-green-200">
                                <h3 class="text-2xl font-semibold mb-4 text-green-900">
                                    <svg class="w-8 h-8 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    Payment Management
                                </h3>
                                <ul class="space-y-2 text-gray-700">
                                    <li>✓ Multiple payment method support</li>
                                    <li>✓ Automated GCash integration</li>
                                    <li>✓ Manual payment verification tools</li>
                                    <li>✓ Payment receipt generation</li>
                                    <li>✓ Refund and cancellation handling</li>
                                </ul>
                            </div>
                            <div class="bg-gradient-to-br from-purple-50 to-purple-100 p-6 rounded-lg border border-purple-200">
                                <h3 class="text-2xl font-semibold mb-4 text-purple-900">
                                    <svg class="w-8 h-8 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                    Event Scheduling
                                </h3>
                                <ul class="space-y-2 text-gray-700">
                                    <li>✓ Flexible batch management</li>
                                    <li>✓ Recurring event support</li>
                                    <li>✓ Automated slot allocation</li>
                                    <li>✓ Waitlist management</li>
                                    <li>✓ Multi-date event coordination</li>
                                </ul>
                            </div>
                            <div class="bg-gradient-to-br from-yellow-50 to-yellow-100 p-6 rounded-lg border border-yellow-200">
                                <h3 class="text-2xl font-semibold mb-4 text-yellow-900">
                                    <svg class="w-8 h-8 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                    </svg>
                                    Customer Relations
                                </h3>
                                <ul class="space-y-2 text-gray-700">
                                    <li>✓ Built-in messaging system</li>
                                    <li>✓ Review and rating management</li>
                                    <li>✓ Customer database</li>
                                    <li>✓ Automated notifications</li>
                                    <li>✓ Follower engagement tools</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Support & Resources -->
                    <div class="mb-12">
                        <h2 class="text-3xl font-bold text-gray-900 mb-6 text-center">Support & Resources</h2>
                        <div class="bg-white border-2 border-gray-200 rounded-lg p-8">
                            <div class="grid md:grid-cols-3 gap-8">
                                <div class="text-center">
                                    <div class="text-blue-600 mb-4">
                                        <svg class="w-16 h-16 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                        </svg>
                                    </div>
                                    <h3 class="text-xl font-semibold mb-3">Documentation</h3>
                                    <p class="text-gray-600 mb-4">
                                        Comprehensive guides, tutorials, and best practices for maximizing your presence on HikeThere.
                                    </p>
                                </div>
                                <div class="text-center">
                                    <div class="text-green-600 mb-4">
                                        <svg class="w-16 h-16 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                        </svg>
                                    </div>
                                    <h3 class="text-xl font-semibold mb-3">24/7 Support</h3>
                                    <p class="text-gray-600 mb-4">
                                        Our dedicated support team is always available to help you with any questions or technical issues.
                                    </p>
                                </div>
                                <div class="text-center">
                                    <div class="text-purple-600 mb-4">
                                        <svg class="w-16 h-16 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                        </svg>
                                    </div>
                                    <h3 class="text-xl font-semibold mb-3">Partner Network</h3>
                                    <p class="text-gray-600 mb-4">
                                        Connect with other organizations, share insights, and collaborate for mutual growth.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- FAQ for Organizations -->
                    <div class="mb-12">
                        <h2 class="text-3xl font-bold text-gray-900 mb-8 text-center">Frequently Asked Questions</h2>
                        <div class="max-w-4xl mx-auto space-y-4">
                            <div class="bg-white border border-gray-200 rounded-lg p-6">
                                <h3 class="text-lg font-semibold text-gray-900 mb-2">Is there a fee to join HikeThere?</h3>
                                <p class="text-gray-600">
                                    Registration and basic platform access are free. We operate on a commission-based model, taking a small 
                                    percentage only from successful bookings. No bookings means no fees—your success is our success.
                                </p>
                            </div>
                            <div class="bg-white border border-gray-200 rounded-lg p-6">
                                <h3 class="text-lg font-semibold text-gray-900 mb-2">How long does approval take?</h3>
                                <p class="text-gray-600">
                                    The verification process typically takes 2-5 business days. We review your business registration, 
                                    credentials, and ensure compliance with safety standards before approval.
                                </p>
                            </div>
                            <div class="bg-white border border-gray-200 rounded-lg p-6">
                                <h3 class="text-lg font-semibold text-gray-900 mb-2">Can I manage multiple trails and events?</h3>
                                <p class="text-gray-600">
                                    Absolutely! Create unlimited trails and events. Our platform is designed to scale with organizations 
                                    of all sizes, from single-trail operators to companies managing dozens of locations.
                                </p>
                            </div>
                            <div class="bg-white border border-gray-200 rounded-lg p-6">
                                <h3 class="text-lg font-semibold text-gray-900 mb-2">What payment methods can I accept?</h3>
                                <p class="text-gray-600">
                                    You can set up GCash for automated payments or use manual verification for bank transfers and other methods. 
                                    The choice is yours, and you can switch between methods anytime.
                                </p>
                            </div>
                            <div class="bg-white border border-gray-200 rounded-lg p-6">
                                <h3 class="text-lg font-semibold text-gray-900 mb-2">How do refunds and cancellations work?</h3>
                                <p class="text-gray-600">
                                    You set your own cancellation policies. The platform provides tools to manage refunds, but the final 
                                    decision is yours based on your policies and the specific circumstances.
                                </p>
                            </div>
                            <div class="bg-white border border-gray-200 rounded-lg p-6">
                                <h3 class="text-lg font-semibold text-gray-900 mb-2">Can I customize pricing for different customer types?</h3>
                                <p class="text-gray-600">
                                    Yes! Set flexible pricing based on package inclusions, group sizes, dates, and other factors. 
                                    Our batch system allows sophisticated pricing strategies.
                                </p>
                            </div>
                            <div class="bg-white border border-gray-200 rounded-lg p-6">
                                <h3 class="text-lg font-semibold text-gray-900 mb-2">What kind of support do you provide?</h3>
                                <p class="text-gray-600">
                                    We offer comprehensive support including technical assistance, onboarding help, best practice guidance, 
                                    and ongoing account management. Reach out anytime via our support system.
                                </p>
                            </div>
                            <div class="bg-white border border-gray-200 rounded-lg p-6">
                                <h3 class="text-lg font-semibold text-gray-900 mb-2">How do I increase my visibility on the platform?</h3>
                                <p class="text-gray-600">
                                    Maintain complete trail profiles, respond promptly to inquiries, encourage reviews, post regular updates, 
                                    and engage with the community. High-quality content and excellent service naturally boost visibility.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Dashboard Overview -->
                    <div class="mb-12 bg-gradient-to-r from-blue-600 to-green-600 text-white p-12 rounded-lg text-center">
                        <h2 class="text-3xl font-bold mb-4">Your Organization Dashboard</h2>
                        <p class="text-xl mb-8 opacity-90 max-w-3xl mx-auto">
                            Access powerful tools designed specifically for hiking organizations. Monitor your trails, 
                            track bookings, manage events, and grow your business—all from one centralized platform.
                        </p>
                        <div class="flex justify-center gap-4 flex-wrap">
                            <a href="{{ route('org.trails.index') }}" class="bg-white text-blue-600 px-8 py-3 rounded-lg font-semibold hover:bg-gray-100 transition duration-300">
                                Manage Trails
                            </a>
                            <a href="{{ route('org.events.index') }}" class="bg-white text-blue-600 px-8 py-3 rounded-lg font-semibold hover:bg-gray-100 transition duration-300">
                                View Events
                            </a>
                            <a href="{{ route('org.bookings.index') }}" class="bg-transparent border-2 border-white text-white px-8 py-3 rounded-lg font-semibold hover:bg-white hover:text-blue-600 transition duration-300">
                                Manage Bookings
                            </a>
                        </div>
                    </div>

                    <!-- Support Section -->
                    <div class="text-center">
                        <h3 class="text-2xl font-bold text-gray-900 mb-4">Need Assistance?</h3>
                        <p class="text-gray-600 mb-6">
                            We're here to help you succeed. Have questions about managing your trails, events, or bookings?
                        </p>
                        
                        <!-- Support System Card -->
                        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border-2 border-blue-200 rounded-lg p-6 mb-6 max-w-2xl mx-auto">
                            <div class="flex items-center justify-center mb-4">
                                <svg class="w-12 h-12 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                </svg>
                            </div>
                            <h4 class="text-xl font-bold text-gray-900 mb-2">Get Support</h4>
                            <p class="text-gray-700 mb-4">
                                Need help with your organization account, trail management, or booking system? Our support team is ready to assist you!
                            </p>
                            <a href="{{ route('support.create') }}" class="inline-flex items-center px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg shadow-md transition duration-150">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
                                </svg>
                                Create Support Ticket
                            </a>
                            <p class="text-sm text-gray-600 mt-3">
                                Or view your existing tickets in the <a href="{{ route('support.index') }}" class="text-blue-600 hover:text-blue-800 font-semibold underline">Support Center</a>
                            </p>
                        </div>

                        <p class="text-gray-700">
                            Configure your <a href="{{ route('org.payment.index') }}" class="text-blue-600 hover:text-blue-800 font-semibold">Payment Setup</a> 
                            or visit your <a href="{{ route('account.settings') }}" class="text-blue-600 hover:text-blue-800 font-semibold">Account Settings</a> 
                            to manage your organization preferences.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

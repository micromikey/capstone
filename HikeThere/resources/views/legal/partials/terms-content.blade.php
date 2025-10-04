<div class="flex flex-col lg:flex-row gap-6">
    <!-- Sidebar Navigation -->
    <aside class="lg:w-80 flex-shrink-0">
        <!-- Spacer to maintain layout when sidebar is fixed -->
        <div class="hidden lg:block lg:w-80"></div>
        
        <div class="bg-white rounded-lg shadow-lg border border-gray-200 lg:fixed lg:w-80 lg:top-32" style="max-height: calc(100vh - 152px);" x-data="{ activeSection: '' }" @scroll.window="
            let sections = ['introduction', 'definitions', 'eligibility', 'user-types', 'bookings', 'trail-info', 'user-content', 'safety', 'itinerary', 'community', 'intellectual', 'prohibited', 'liability', 'data-protection', 'termination', 'modifications', 'dispute', 'general', 'contact', 'acknowledgment'];
            for (let section of sections) {
                let element = document.getElementById(section);
                if (element) {
                    let rect = element.getBoundingClientRect();
                    if (rect.top >= 0 && rect.top <= 200) {
                        activeSection = section;
                        break;
                    }
                }
            }
        ">
            <div class="p-6 border-b border-gray-200">
                <div class="flex items-center space-x-2 text-green-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <h3 class="font-semibold text-gray-900">Table of Contents</h3>
                </div>
            </div>
            <nav class="p-4 overflow-y-auto" style="max-height: calc(100vh - 320px);">
                <ul class="space-y-1 text-sm">
                    <li><a href="#introduction" class="block px-3 py-2 rounded-md hover:bg-green-50 hover:text-green-700 transition" :class="activeSection === 'introduction' ? 'bg-green-50 text-green-700 font-medium' : 'text-gray-700'">1. Introduction</a></li>
                    <li><a href="#definitions" class="block px-3 py-2 rounded-md hover:bg-green-50 hover:text-green-700 transition" :class="activeSection === 'definitions' ? 'bg-green-50 text-green-700 font-medium' : 'text-gray-700'">2. Definitions</a></li>
                    <li><a href="#eligibility" class="block px-3 py-2 rounded-md hover:bg-green-50 hover:text-green-700 transition" :class="activeSection === 'eligibility' ? 'bg-green-50 text-green-700 font-medium' : 'text-gray-700'">3. Eligibility & Registration</a></li>
                    <li><a href="#user-types" class="block px-3 py-2 rounded-md hover:bg-green-50 hover:text-green-700 transition" :class="activeSection === 'user-types' ? 'bg-green-50 text-green-700 font-medium' : 'text-gray-700'">4. User Types & Roles</a></li>
                    <li><a href="#bookings" class="block px-3 py-2 rounded-md hover:bg-green-50 hover:text-green-700 transition" :class="activeSection === 'bookings' ? 'bg-green-50 text-green-700 font-medium' : 'text-gray-700'">5. Bookings & Payments</a></li>
                    <li><a href="#trail-info" class="block px-3 py-2 rounded-md hover:bg-green-50 hover:text-green-700 transition" :class="activeSection === 'trail-info' ? 'bg-green-50 text-green-700 font-medium' : 'text-gray-700'">6. Trail Information</a></li>
                    <li><a href="#user-content" class="block px-3 py-2 rounded-md hover:bg-green-50 hover:text-green-700 transition" :class="activeSection === 'user-content' ? 'bg-green-50 text-green-700 font-medium' : 'text-gray-700'">7. User-Generated Content</a></li>
                    <li><a href="#safety" class="block px-3 py-2 rounded-md hover:bg-green-50 hover:text-green-700 transition" :class="activeSection === 'safety' ? 'bg-green-50 text-green-700 font-medium' : 'text-gray-700'">8. Safety & Emergency</a></li>
                    <li><a href="#itinerary" class="block px-3 py-2 rounded-md hover:bg-green-50 hover:text-green-700 transition" :class="activeSection === 'itinerary' ? 'bg-green-50 text-green-700 font-medium' : 'text-gray-700'">9. Itinerary Builder</a></li>
                    <li><a href="#community" class="block px-3 py-2 rounded-md hover:bg-green-50 hover:text-green-700 transition" :class="activeSection === 'community' ? 'bg-green-50 text-green-700 font-medium' : 'text-gray-700'">10. Community Features</a></li>
                    <li><a href="#intellectual" class="block px-3 py-2 rounded-md hover:bg-green-50 hover:text-green-700 transition" :class="activeSection === 'intellectual' ? 'bg-green-50 text-green-700 font-medium' : 'text-gray-700'">11. Intellectual Property</a></li>
                    <li><a href="#prohibited" class="block px-3 py-2 rounded-md hover:bg-green-50 hover:text-green-700 transition" :class="activeSection === 'prohibited' ? 'bg-green-50 text-green-700 font-medium' : 'text-gray-700'">12. Prohibited Conduct</a></li>
                    <li><a href="#liability" class="block px-3 py-2 rounded-md hover:bg-green-50 hover:text-green-700 transition" :class="activeSection === 'liability' ? 'bg-green-50 text-green-700 font-medium' : 'text-gray-700'">13. Liability & Disclaimers</a></li>
                    <li><a href="#data-protection" class="block px-3 py-2 rounded-md hover:bg-green-50 hover:text-green-700 transition" :class="activeSection === 'data-protection' ? 'bg-green-50 text-green-700 font-medium' : 'text-gray-700'">14. Data Protection</a></li>
                    <li><a href="#termination" class="block px-3 py-2 rounded-md hover:bg-green-50 hover:text-green-700 transition" :class="activeSection === 'termination' ? 'bg-green-50 text-green-700 font-medium' : 'text-gray-700'">15. Account Termination</a></li>
                    <li><a href="#modifications" class="block px-3 py-2 rounded-md hover:bg-green-50 hover:text-green-700 transition" :class="activeSection === 'modifications' ? 'bg-green-50 text-green-700 font-medium' : 'text-gray-700'">16. Modifications to Terms</a></li>
                    <li><a href="#dispute" class="block px-3 py-2 rounded-md hover:bg-green-50 hover:text-green-700 transition" :class="activeSection === 'dispute' ? 'bg-green-50 text-green-700 font-medium' : 'text-gray-700'">17. Dispute Resolution</a></li>
                    <li><a href="#general" class="block px-3 py-2 rounded-md hover:bg-green-50 hover:text-green-700 transition" :class="activeSection === 'general' ? 'bg-green-50 text-green-700 font-medium' : 'text-gray-700'">18. General Provisions</a></li>
                    <li><a href="#contact" class="block px-3 py-2 rounded-md hover:bg-green-50 hover:text-green-700 transition" :class="activeSection === 'contact' ? 'bg-green-50 text-green-700 font-medium' : 'text-gray-700'">19. Contact Information</a></li>
                    <li><a href="#acknowledgment" class="block px-3 py-2 rounded-md hover:bg-green-50 hover:text-green-700 transition" :class="activeSection === 'acknowledgment' ? 'bg-green-50 text-green-700 font-medium' : 'text-gray-700'">20. Acknowledgment</a></li>
                </ul>
            </nav>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 min-w-0">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-8 lg:p-12">
            <!-- Content -->
            <div id="content" class="prose prose-lg max-w-none prose-headings:text-gray-900 prose-h2:text-2xl prose-h2:font-bold prose-h2:mt-8 prose-h2:mb-4 prose-h2:scroll-mt-24 prose-h3:text-xl prose-h3:font-semibold prose-h3:mt-6 prose-h3:mb-3 prose-p:text-gray-700 prose-p:leading-relaxed prose-li:text-gray-700 prose-strong:text-gray-900 prose-a:text-green-600 prose-a:no-underline hover:prose-a:underline">
                {!! \Illuminate\Support\Str::markdown(file_get_contents(base_path('TERMS_AND_CONDITIONS.md'))) !!}
            </div>

            <!-- Footer Contact -->
            <div class="mt-12 pt-8 border-t border-gray-200">
                <div class="bg-green-50 rounded-xl p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-3">Questions about these Terms?</h3>
                    <p class="text-gray-700 mb-4">
                        If you have any questions or concerns about our Terms and Conditions, please contact us:
                    </p>
                    <div class="flex flex-col space-y-2 text-gray-700">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                            <a href="mailto:support@hikethere.ph" class="text-green-600 hover:text-green-700 font-medium">support@hikethere.ph</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

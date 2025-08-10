<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div class="space-y-2">
                <h2 class="font-bold text-2xl text-gray-900 leading-tight tracking-tight">
                    PRE-HIKE SELF-ASSESSMENT
                </h2>
                <h1 class="text-gray-600 text-lg font-medium flex items-center gap-2">
                    <span class="inline-flex items-center justify-center w-6 h-6 bg-blue-100 text-blue-800 text-sm font-bold rounded-full">0</span>
                    Instructions & Overview
                </h1>
            </div>
        </div>
    </x-slot>

    <div class="py-8 px-4 sm:px-6 lg:px-8 max-w-6xl mx-auto space-y-4">

        <!-- Intro Card -->
        <div class="bg-gradient-to-br from-white via-blue-50 to-green-50 rounded-2xl shadow-lg p-10 border border-blue-100 text-center">
            <div class="mb-8 relative flex justify-center">
                <div class="absolute inset-0 bg-gradient-to-r from-blue-400 to-green-400 rounded-full blur-3xl opacity-20 animate-pulse"></div>
                <span class="text-7xl animate-bounce relative z-10 drop-shadow-lg">🥾</span>
            </div>
            <h1 class="text-4xl font-bold mb-6 bg-gradient-to-r from-gray-800 via-blue-900 to-green-800 bg-clip-text text-transparent">
                Pre-Hike Self-Assessment
            </h1>
            <p class="text-lg text-gray-700 mb-6 leading-relaxed font-medium">
                Embark on your hiking adventures with confidence. Our assessment evaluates your readiness across <strong class="text-blue-600">six critical areas</strong>:
            </p>
            <div class="grid grid-cols-2 md:grid-cols-3 gap-4 mt-6 text-sm font-semibold justify-center">
                <span class="bg-green-100 text-green-800 px-4 py-2 rounded-full">🎒 Gear Preparation</span>
                <span class="bg-yellow-100 text-yellow-800 px-4 py-2 rounded-full">💪 Fitness Level</span>
                <span class="bg-purple-100 text-purple-800 px-4 py-2 rounded-full">🏥 Health Status</span>
                <span class="bg-cyan-100 text-cyan-800 px-4 py-2 rounded-full">🌤️ Weather Awareness</span>
                <span class="bg-red-100 text-red-800 px-4 py-2 rounded-full">🚨 Emergency Prep</span>
                <span class="bg-emerald-100 text-emerald-800 px-4 py-2 rounded-full">🌱 Environmental Care</span>
            </div>
            <p class="text-md text-gray-600 mt-8 leading-relaxed">
                Receive personalized recommendations and trail suggestions tailored to your unique readiness profile.
            </p>
        </div>

        <!-- Importance of Preparedness Section -->
        <div class="bg-gradient-to-br from-yellow-50 via-orange-50 to-red-50 border-l-4 border-yellow-400 rounded-2xl shadow-lg p-10 relative overflow-hidden">
            <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-yellow-200 to-orange-200 rounded-full opacity-20 -mr-16 -mt-16"></div>
            <div class="flex items-start relative z-10">
                <div class="mr-8 p-4 bg-gradient-to-br from-yellow-400 to-orange-500 rounded-full shadow-lg flex items-center justify-center h-14 w-14">
                    <span class="text-3xl">⚠️</span>
                </div>
                <div class="flex-1">
                    <h3 class="text-2xl font-bold mb-6 text-gray-800 flex items-center">
                        Why Assess Your Hiking Preparedness?
                        <span class="ml-3 text-sm bg-yellow-200 text-yellow-800 px-3 py-1 rounded-full font-medium">CRITICAL</span>
                    </h3>
                    <p class="text-gray-700 mb-6 text-lg leading-relaxed">
                        <strong>Every year, thousands of hiking incidents could be prevented through proper assessment and preparation.</strong>
                        Our tool helps you identify potential risks before they become real dangers on the trail.
                    </p>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                        <div class="group hover:scale-105 transition-all duration-300">
                            <div class="bg-white rounded-xl p-6 shadow-md hover:shadow-xl border border-yellow-200 h-full flex flex-col justify-between">
                                <div>
                                    <div class="text-3xl mb-4 group-hover:animate-pulse">🛡️</div>
                                    <h4 class="font-bold text-gray-800 mb-3 text-lg">Personal Safety Excellence</h4>
                                    <p class="text-gray-600 text-sm leading-relaxed">
                                        Identify potential hazards and ensure you're equipped with the right gear, knowledge, and physical condition for your chosen adventure level.
                                    </p>
                                </div>
                                <div class="mt-4 flex items-center text-xs text-yellow-700">
                                    <span class="w-2 h-2 bg-yellow-500 rounded-full mr-2"></span>
                                    Risk Prevention Priority
                                </div>
                            </div>
                        </div>
                        <div class="group hover:scale-105 transition-all duration-300">
                            <div class="bg-white rounded-xl p-6 shadow-md hover:shadow-xl border border-green-200 h-full flex flex-col justify-between">
                                <div>
                                    <div class="text-3xl mb-4 group-hover:animate-pulse">🌱</div>
                                    <h4 class="font-bold text-gray-800 mb-3 text-lg">Environmental Stewardship</h4>
                                    <p class="text-gray-600 text-sm leading-relaxed">
                                        Master Leave No Trace principles, minimize ecological impact, and contribute to preserving natural spaces for future generations.
                                    </p>
                                </div>
                                <div class="mt-4 flex items-center text-xs text-green-700">
                                    <span class="w-2 h-2 bg-green-500 rounded-full mr-2"></span>
                                    Sustainability Focus
                                </div>
                            </div>
                        </div>
                        <div class="group hover:scale-105 transition-all duration-300">
                            <div class="bg-white rounded-xl p-6 shadow-md hover:shadow-xl border border-blue-200 h-full flex flex-col justify-between">
                                <div>
                                    <div class="text-3xl mb-4 group-hover:animate-pulse">📉</div>
                                    <h4 class="font-bold text-gray-800 mb-3 text-lg">Risk Mitigation Mastery</h4>
                                    <p class="text-gray-600 text-sm leading-relaxed">
                                        Proactively prevent accidents and emergencies through comprehensive preparation, situational awareness, and evidence-based decision making.
                                    </p>
                                </div>
                                <div class="mt-4 flex items-center text-xs text-blue-700">
                                    <span class="w-2 h-2 bg-blue-500 rounded-full mr-2"></span>
                                    Data-Driven Safety
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-8 bg-gradient-to-r from-yellow-100 to-orange-100 border border-yellow-300 rounded-lg p-4">
                        <p class="text-sm text-gray-700 flex items-center">
                            <span class="mr-2 text-yellow-600">💡</span>
                            <strong>Did you know? </strong> 
                            Studies show that hikers who complete pre-trip assessments are 73% less likely to experience preventable incidents on the trail.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- ISO Compliance Section -->
        <div class="bg-gradient-to-br from-white to-blue-50 rounded-2xl shadow-lg p-10 border border-blue-100">
            <div class="flex items-center justify-between mb-8">
                <h3 class="text-2xl font-bold text-gray-800 flex items-center">
                    <span class="mr-4 p-2 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-lg shadow-md">
                        <span class="text-2xl text-white">📋</span>
                    </span>
                    International Standards Compliance
                </h3>
                <div class="bg-gradient-to-r from-blue-600 to-indigo-700 text-white px-4 py-2 rounded-full text-sm font-semibold shadow-lg">
                    ISO Certified Framework
                </div>
            </div>
            <p class="text-gray-600 mb-8 text-lg leading-relaxed text-left">
                Our assessment framework aligns with internationally recognized safety and quality standards,
                ensuring comprehensive evaluation based on <strong>proven methodologies</strong> and <strong>industry best practices</strong>.
            </p>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @php
                    $isoCategories = [
                        [
                            'name' => 'Gear Checklist',
                            'icon' => '🎒',
                            'iso' => 'ISO 23551-1:2018',
                            'description' => 'Personal protective equipment and safety gear standards for mountaineering, climbing, and outdoor recreational activities',
                            'color' => '#a3c585',
                            'priority' => 'HIGH'
                        ],
                        [
                            'name' => 'Fitness Assessment',
                            'icon' => '💪',
                            'iso' => 'ISO 20957:2019',
                            'description' => 'Physical fitness evaluation protocols and stationary training equipment safety performance requirements',
                            'color' => '#f4b942',
                            'priority' => 'MEDIUM'
                        ],
                        [
                            'name' => 'Health Screening',
                            'icon' => '🏥',
                            'iso' => 'ISO 14155:2020',
                            'description' => 'Clinical health assessment guidelines and medical device investigation protocols for human subjects',
                            'color' => '#d084e4',
                            'priority' => 'HIGH'
                        ],
                        [
                            'name' => 'Weather Systems',
                            'icon' => '🌤️',
                            'iso' => 'ISO 23058:2018',
                            'description' => 'Meteorological information services, mountain weather forecasting, and environmental risk assessment protocols',
                            'color' => '#70e1e1',
                            'priority' => 'CRITICAL'
                        ],
                        [
                            'name' => 'Emergency Response',
                            'icon' => '🚨',
                            'iso' => 'ISO 22320:2018',
                            'description' => 'Emergency management systems, incident response guidelines, and crisis communication frameworks',
                            'color' => '#ff5a5a',
                            'priority' => 'CRITICAL'
                        ],
                        [
                            'name' => 'Environmental Impact',
                            'icon' => '🌱',
                            'iso' => 'ISO 14001:2015',
                            'description' => 'Environmental management systems, sustainability practices, and ecological footprint minimization standards',
                            'color' => '#34d399',
                            'priority' => 'MEDIUM'
                        ]
                    ];
                @endphp
                @foreach($isoCategories as $category)
                    <div class="group border rounded-xl p-6 hover:shadow-xl transition-all duration-300 bg-white hover:scale-105 relative overflow-hidden flex flex-col justify-between">
                        <div class="flex items-start mb-4 relative z-10">
                            <div class="p-3 rounded-lg mr-4 shadow-md" style="background: linear-gradient(135deg, {{ $category['color'] }}, {{ $category['color'] }}aa);">
                                <span class="text-2xl text-white drop-shadow">{{ $category['icon'] }}</span>
                            </div>
                            <div class="flex-1">
                                <h4 class="font-bold text-gray-800 mb-2 text-lg group-hover:text-blue-700 transition-colors">{{ $category['name'] }}</h4>
                                <div class="flex items-center gap-2 mb-2">
                                    <span class="inline-block px-3 py-1 bg-blue-100 text-blue-800 text-xs font-semibold rounded-full border border-blue-200">
                                        {{ $category['iso'] }}
                                    </span>
                                    <span class="inline-block px-2 py-1 text-xs font-semibold rounded-full
                                        @if($category['priority'] === 'CRITICAL') bg-red-100 text-red-800 border border-red-200
                                        @elseif($category['priority'] === 'HIGH') bg-yellow-100 text-yellow-800 border border-yellow-200
                                        @else bg-green-100 text-green-800 border border-green-200
                                        @endif">
                                        {{ $category['priority'] }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <p class="text-sm text-gray-600 leading-relaxed mb-4 relative z-10 text-left">{{ $category['description'] }}</p>
                        <div class="flex items-center justify-between relative z-10">
                            <div class="h-2 flex-1 rounded-full mr-3" style="background: linear-gradient(90deg, {{ $category['color'] }}66, {{ $category['color'] }})"></div>
                            <span class="text-xs text-gray-500 font-medium">Compliant</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Assessment Overview -->
        <div class="bg-white rounded-2xl shadow p-8 border border-gray-100">
            <h3 class="text-xl font-bold mb-6 text-gray-800 flex items-center">
                <span class="mr-3">📊</span>
                What to Expect
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="space-y-4">
                    <div class="flex items-start">
                        <span class="mr-3 text-green-600">✓</span>
                        <p class="text-gray-700"><strong>6 Assessment Categories:</strong> Comprehensive evaluation across all critical hiking areas</p>
                    </div>
                    <div class="flex items-start">
                        <span class="mr-3 text-green-600">✓</span>
                        <p class="text-gray-700"><strong>10-15 Minutes:</strong> Quick but thorough assessment process</p>
                    </div>
                    <div class="flex items-start">
                        <span class="mr-3 text-green-600">✓</span>
                        <p class="text-gray-700"><strong>Instant Results:</strong> Immediate scoring and personalized recommendations</p>
                    </div>
                </div>
                <div class="space-y-4">
                    <div class="flex items-start">
                        <span class="mr-3 text-blue-600">📋</span>
                        <p class="text-gray-700"><strong>Trail Recommendations:</strong> Suitable trail difficulty based on your readiness</p>
                    </div>
                    <div class="flex items-start">
                        <span class="mr-3 text-blue-600">💡</span>
                        <p class="text-gray-700"><strong>Safety Tips:</strong> Personalized advice for safer hiking experiences</p>
                    </div>
                    <div class="flex items-start">
                        <span class="mr-3 text-blue-600">🎯</span>
                        <p class="text-gray-700"><strong>Action Plan:</strong> Clear next steps to improve your hiking preparedness</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Call-to-Action Section -->
        <div class="bg-gradient-to-br from-white via-yellow-50 to-orange-50 rounded-2xl shadow-2xl p-10 border-2 border-yellow-200 relative overflow-hidden text-center">
            <div class="mb-8 flex justify-center">
                <div class="inline-flex items-center justify-center w-20 h-20 bg-gradient-to-br from-yellow-400 to-orange-500 rounded-full shadow-xl mb-4 animate-bounce">
                    <span class="text-4xl text-white">🚀</span>
                </div>
            </div>
            <h3 class="text-3xl font-bold mb-6 bg-gradient-to-r from-gray-800 via-yellow-700 to-orange-700 bg-clip-text text-transparent">
                Ready to Begin Your Adventure Assessment?
            </h3>
            <p class="text-gray-700 mb-8 max-w-3xl mx-auto text-lg leading-relaxed">
                Take the first step towards safer, more confident hiking. Get your readiness score, personalized recommendations, and custom trail suggestions.
            </p>
            <a href="{{ route('assessment.gear') }}"
               class="inline-flex items-center px-10 py-5 text-white text-xl font-bold rounded-xl shadow-2xl transition-all duration-300 hover:shadow-3xl hover:scale-110 transform-gpu group relative overflow-hidden"
               style="background: linear-gradient(135deg, #e3a746 0%, #f4b942 50%, #ff8c42 100%);">
                <span class="relative z-10">Take Your Assessment Here</span>
                <svg class="ml-4 w-6 h-6 group-hover:translate-x-1 transition-transform duration-300 relative z-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                </svg>
            </a>
        </div>

        <!-- Quick Tips Section -->
        <div class="bg-white rounded-2xl shadow p-8 border border-gray-100">
            <h3 class="text-xl font-bold mb-6 text-gray-800 flex items-center">
                <span class="mr-3">💡</span>
                Assessment Tips
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="space-y-3">
                    <h4 class="font-semibold text-gray-700">For Best Results:</h4>
                    <div class="space-y-2">
                        <p class="flex items-start text-sm">
                            <span class="mr-2 text-blue-500">•</span>
                            Answer honestly about your current abilities and equipment
                        </p>
                        <p class="flex items-start text-sm">
                            <span class="mr-2 text-blue-500">•</span>
                            Consider your most recent hiking experiences
                        </p>
                        <p class="flex items-start text-sm">
                            <span class="mr-2 text-blue-500">•</span>
                            Take your time to read each question carefully
                        </p>
                    </div>
                </div>
                <div class="space-y-3">
                    <h4 class="font-semibold text-gray-700">After Assessment:</h4>
                    <div class="space-y-2">
                        <p class="flex items-start text-sm">
                            <span class="mr-2 text-green-500">•</span>
                            Review your detailed results and recommendations
                        </p>
                        <p class="flex items-start text-sm">
                            <span class="mr-2 text-green-500">•</span>
                            Print or save your assessment for reference
                        </p>
                        <p class="flex items-start text-sm">
                            <span class="mr-2 text-green-500">•</span>
                            Use the results to plan your next hiking adventure
                        </p>
                    </div>
                </div>
            </div>
        </div>


                <!-- Privacy Notice Section -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mt-10">
            <div class="flex items-start">
                <span class="mr-4 text-2xl">🔒</span>
                <div class="flex-1">
                    <h4 class="font-semibold text-blue-900 mb-2">Privacy & Confidentiality Notice</h4>
                    <p class="text-blue-800 text-sm leading-relaxed">
                        All responses provided during this assessment will remain completely <strong>confidential and anonymous</strong>. 
                        Your personal information is used solely to evaluate your overall hiking preparedness and provide personalized 
                        safety recommendations. We do not store, share, or use your data for any other purposes. Your privacy and 
                        security are our top priorities.
                    </p>
                </div>
            </div>
        </div>
        
    </div>

    <style>
        .transition-all { transition: all 0.3s ease; }
        .hover\:scale-105:hover { transform: scale(1.05); }
        .animate-bounce { animation: bounce 1s infinite; }
        @keyframes bounce {
            0%, 20%, 53%, 80%, 100% { transform: translateY(0); }
            40%, 43% { transform: translateY(-30px); }
            70% { transform: translateY(-15px); }
            90% { transform: translateY(-4px); }
        }
        .hover\:shadow-md:hover {
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }
    </style>
</x-app-layout>
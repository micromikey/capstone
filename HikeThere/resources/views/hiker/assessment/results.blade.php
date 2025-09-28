<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    PRE-HIKE SELF-ASSESSMENT RESULTS
                </h2>
                <p class="text-gray-600">
                    @if(isset($scores['overall']) && $scores['overall'] > 0)
                        @if(Session::has('assessment.gear') || Session::has('assessment.fitness') || Session::has('assessment.health') || Session::has('assessment.weather') || Session::has('assessment.emergency') || Session::has('assessment.environment'))
                            Your comprehensive hiking readiness evaluation (Live Results)
                        @else
                            Your saved hiking readiness evaluation from the database
                        @endif
                    @else
                        Your comprehensive hiking readiness evaluation
                    @endif
                </p>
            </div>
        </div>
    </x-slot>

    <div class="py-8 px-6">
        <div class="max-w-6xl mx-auto space-y-6">

            <!-- Overall Score Card -->
            <div class="bg-white rounded-lg shadow-lg p-8 text-center">
                <div class="mb-4">
                    <span class="text-6xl animate-bounce">{{ $readinessLevel['icon'] }}</span>
                </div>
                <h1 class="text-6xl font-bold mb-2 transition-all duration-500"
                    style="color: {{ $readinessLevel['color'] === 'green' ? '#059669' : ($readinessLevel['color'] === 'blue' ? '#2563eb' : ($readinessLevel['color'] === 'yellow' ? '#d97706' : '#dc2626')) }}"
                    id="overall-score">
                    0%
                </h1>
                <div class="w-full bg-gray-200 rounded-full h-4 mb-4 mx-auto max-w-md">
                    <div class="h-4 rounded-full transition-all duration-1000 ease-out"
                         style="width: 0%; background-color: {{ $readinessLevel['color'] === 'green' ? '#059669' : ($readinessLevel['color'] === 'blue' ? '#2563eb' : ($readinessLevel['color'] === 'yellow' ? '#d97706' : '#dc2626')) }}"
                         id="overall-progress">
                    </div>
                </div>
                <h2 class="text-2xl font-semibold mb-4 text-gray-800">{{ $readinessLevel['level'] }}</h2>
                <p class="text-lg text-gray-600 max-w-2xl mx-auto">{{ $readinessLevel['message'] }}</p>
            </div>

            <!-- Section Scores & Recommendations Side by Side -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Assessment Breakdown -->
                <div class="bg-white rounded-lg shadow p-6 flex flex-col h-full">
                    <h3 class="text-xl font-bold mb-6 text-gray-800 flex items-center">
                        <span class="mr-3">📊</span>
                        Assessment Breakdown
                    </h3>
                    <div class="flex flex-col gap-4 flex-1 justify-between h-full">
                        @php
                            $categories = [
                                'gear' => ['name' => 'Gear', 'icon' => '🎒', 'color' => '#a3c585'],
                                'fitness' => ['name' => 'Fitness', 'icon' => '💪', 'color' => '#f4b942'],
                                'health' => ['name' => 'Health', 'icon' => '🏥', 'color' => '#d084e4'],
                                'weather' => ['name' => 'Weather', 'icon' => '🌤️', 'color' => '#70e1e1'],
                                'emergency' => ['name' => 'Emergency', 'icon' => '🚨', 'color' => '#ff5a5a'],
                                'environment' => ['name' => 'Environment', 'icon' => '🌱', 'color' => '#34d399'],
                            ];
                        @endphp
                        @foreach($categories as $key => $category)
                            <div class="border rounded-lg p-4 flex items-center min-h-[110px]">
                                <div class="text-2xl mr-4">{{ $category['icon'] }}</div>
                                <div class="flex-1">
                                    <div class="font-semibold text-gray-800 mb-1">{{ $category['name'] }}</div>
                                    <div class="flex items-center mb-1">
                                        <span class="text-xl font-bold score-number"
                                              style="color: {{ $category['color'] }}"
                                              data-target="{{ $scores[$key] ?? 0 }}">0%</span>
                                        <div class="w-full bg-gray-200 rounded-full h-2 mx-2 flex-1">
                                            <div class="h-2 rounded-full transition-all duration-1000 ease-out score-bar"
                                                 style="width: 0%; background-color: {{ $category['color'] }}"
                                                 data-width="{{ $scores[$key] ?? 0 }}%"></div>
                                        </div>
                                    </div>
                                    <div class="text-xs text-gray-600">
                                        @if(($scores[$key] ?? 0) >= 85)
                                            <span class="text-green-600 font-semibold">Excellent</span>
                                        @elseif(($scores[$key] ?? 0) >= 70)
                                            <span class="text-blue-600 font-semibold">Good</span>
                                        @elseif(($scores[$key] ?? 0) >= 55)
                                            <span class="text-yellow-600 font-semibold">Needs Improvement</span>
                                        @else
                                            <span class="text-red-600 font-semibold">Attention Needed</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Personalized Recommendations -->
                <div class="bg-white rounded-lg shadow p-6 flex flex-col h-full">
                    <h3 class="text-xl font-bold mb-6 text-gray-800 flex items-center">
                        <span class="mr-3">📋</span>
                        Personalized Recommendations
                    </h3>
                    <div class="flex-1 flex flex-col justify-between h-full">
                        @foreach($categories as $key => $category)
                            @php
                                $score = $scores[$key] ?? 0;
                            @endphp
                            <div class="border-l-4 p-4 rounded-r-lg min-h-[110px]
                                @if($score < 70)
                                    {{ $score < 55 ? 'border-red-500 bg-red-50' : 'border-orange-500 bg-orange-50' }}
                                @else
                                    border-green-500 bg-green-50
                                @endif">
                                <div class="flex items-start">
                                    <span class="mr-3 text-xl">
                                        @if($score < 70)
                                            {{ $key === 'emergency' ? '🚨' : ($score < 55 ? '🚨' : '⚠️') }}
                                        @elseif($key === 'environment')
                                            🌱
                                        @else
                                            🎉
                                        @endif
                                    </span>
                                    <div class="flex-1">
                                        <div class="flex items-center justify-between mb-1">
                                            <h4 class="font-semibold text-gray-800">{{ $category['name'] }}</h4>
                                            <span class="inline-block px-2 py-1 text-xs font-semibold rounded-full
                                                @if($score < 70)
                                                    {{ $score < 55 ? 'bg-red-200 text-red-800' : 'bg-orange-200 text-orange-800' }}
                                                @else
                                                    bg-green-200 text-green-800
                                                @endif">
                                                @if($score < 70)
                                                    {{ $score < 55 ? 'CRITICAL' : 'HIGH' }}
                                                @else
                                                    READY
                                                @endif
                                            </span>
                                        </div>
                                        <p class="text-gray-700 text-sm">
                                            @if($score < 70)
                                                @switch($key)
                                                    @case('gear')
                                                        Double-check your essential gear checklist and ensure you have all required equipment.
                                                        @break
                                                    @case('fitness')
                                                        Choose trails within your current fitness level and gradually build up your hiking experience.
                                                        @break
                                                    @case('health')
                                                        Monitor your health symptoms closely and bring necessary medications or first aid.
                                                        @break
                                                    @case('weather')
                                                        Review weather forecasts and be prepared for sudden changes. Consider rescheduling if severe weather is expected.
                                                        @break
                                                    @case('emergency')
                                                        Stay on marked trails, avoid risky situations, and review your emergency action plan.
                                                        @break
                                                    @case('environment')
                                                        Review Leave No Trace principles and improve your environmental practices for safer, more responsible hiking.
                                                        @break
                                                @endswitch
                                            @else
                                                You're well-prepared in this area!
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            <!-- Trail Recommendations -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-xl font-bold mb-4 text-gray-800 flex items-center">
                    <span class="mr-3">🎯</span>
                    Hiking Trail Recommendations
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    @php
                        $trailRecommendations = [
                            'easy' => [
                                'name' => 'Easy Trails',
                                'icon' => '🚶‍♂️',
                                'suitable' => $scores['overall'] >= 40,
                                'examples' => 'Nature walks, paved trails, short distances (1-3km)'
                            ],
                            'moderate' => [
                                'name' => 'Moderate Trails',
                                'icon' => '🥾',
                                'suitable' => $scores['overall'] >= 60,
                                'examples' => 'Hill walks, unpaved trails, moderate elevation (5-10km)'
                            ],
                            'challenging' => [
                                'name' => 'Challenging Trails',
                                'icon' => '⛰️',
                                'suitable' => $scores['overall'] >= 75,
                                'examples' => 'Mountain trails, significant elevation gain (10-20km)'
                            ],
                            'expert' => [
                                'name' => 'Expert Trails',
                                'icon' => '🏔️',
                                'suitable' => $scores['overall'] >= 85,
                                'examples' => 'Multi-day treks, extreme terrain, technical routes'
                            ]
                        ];
                    @endphp
                    @foreach($trailRecommendations as $trail)
                        <div class="border rounded-lg p-4 text-center {{ $trail['suitable'] ? 'bg-green-50 border-green-200' : 'bg-gray-50 border-gray-200' }}">
                            <div class="text-3xl mb-2">{{ $trail['icon'] }}</div>
                            <h4 class="font-semibold text-gray-800 mb-2">{{ $trail['name'] }}</h4>
                            <div class="mb-2">
                                @if($trail['suitable'])
                                    <span class="inline-block px-2 py-1 bg-green-200 text-green-800 text-xs font-semibold rounded-full">
                                        ✓ SUITABLE
                                    </span>
                                @else
                                    <span class="inline-block px-2 py-1 bg-gray-200 text-gray-600 text-xs font-semibold rounded-full">
                                        ✗ NOT READY
                                    </span>
                                @endif
                            </div>
                            <p class="text-xs text-gray-600">{{ $trail['examples'] }}</p>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Tips Section -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-xl font-bold mb-4 text-gray-800 flex items-center">
                    <span class="mr-3">💡</span>
                    Personalized Safety Tips
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-3">
                        <h4 class="font-semibold text-gray-700 mb-2">Before Your Hike</h4>
                        @if($scores['weather'] < 70)
                            <p class="flex items-start text-sm"><span class="mr-2 text-orange-500">⚠️</span>Check detailed weather forecasts and mountain conditions</p>
                        @endif
                        @if($scores['gear'] < 70)
                            <p class="flex items-start text-sm"><span class="mr-2 text-orange-500">⚠️</span>Double-check your essential gear checklist</p>
                        @endif
                        <p class="flex items-start text-sm"><span class="mr-2 text-green-600">✓</span>Inform someone about your detailed hiking plans</p>
                        <p class="flex items-start text-sm"><span class="mr-2 text-green-600">✓</span>Start early to avoid afternoon weather changes</p>
                        @if($scores['fitness'] < 70)
                            <p class="flex items-start text-sm"><span class="mr-2 text-orange-500">⚠️</span>Choose trails within your current fitness level</p>
                        @endif
                    </div>
                    <div class="space-y-3">
                        <h4 class="font-semibold text-gray-700 mb-2">During Your Hike</h4>
                        @if($scores['health'] < 70)
                            <p class="flex items-start text-sm"><span class="mr-2 text-orange-500">⚠️</span>Monitor your health symptoms closely</p>
                        @endif
                        @if($scores['emergency'] < 70)
                            <p class="flex items-start text-sm"><span class="mr-2 text-orange-500">⚠️</span>Stay on marked trails and avoid risky situations</p>
                        @endif
                        <p class="flex items-start text-sm"><span class="mr-2 text-green-600">✓</span>Drink water regularly, don't wait until thirsty</p>
                        <p class="flex items-start text-sm"><span class="mr-2 text-green-600">✓</span>Turn back if conditions become unsafe</p>
                        <p class="flex items-start text-sm"><span class="mr-2 text-green-600">✓</span>Take breaks and listen to your body</p>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex flex-wrap gap-4 justify-center">
                    <button onclick="printResults()"
                            class="bg-white hover:bg-yellow-600 text-black font-semibold py-3 px-6 rounded-lg border border-gray-300 transition duration-150 flex items-center hover:scale-105">
                        <span class="mr-2">🖨️</span>
                        Print Results
                    </button>
                    <a href="{{ route('dashboard') }}"
                       class="bg-white hover:bg-yellow-600 text-black font-semibold py-3 px-6 rounded-lg border border-gray-300 transition duration-150 flex items-center hover:scale-105">
                        <span class="mr-2">🏠</span>
                        Go to Dashboard
                    </a>
                    <a href="{{ route('assessment.gear') }}"
                       class="bg-white hover:bg-yellow-600 text-black font-semibold py-3 px-6 rounded-lg border border-gray-300 transition duration-150 flex items-center hover:scale-105">
                        <span class="mr-2">🔄</span>
                        Retake Assessment
                    </a>
                    <button onclick="shareResults()"
                            class="bg-white hover:bg-yellow-600 text-black font-semibold py-3 px-6 rounded-lg border border-gray-300 transition duration-150 flex items-center hover:scale-105">
                        <span class="mr-2">📤</span>
                        Share Results
                    </button>

                </div>
            </div>

            <!-- Save Results and Create Itinerary Buttons -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="text-center space-y-4">
                    <!-- Save Results Button -->
                    <div>
                        <h3 class="text-xl font-bold mb-4 text-gray-800">Save Your Assessment Results</h3>
                        <p class="text-gray-600 mb-6">Save these results to your profile to track your progress and generate personalized itineraries.</p>
                        <form action="{{ route('assessment.save-results') }}" method="POST" class="inline">
                            @csrf
                            <button type="submit"
                               class="inline-flex items-center px-8 py-4 text-white text-lg font-semibold rounded-lg shadow-lg transition duration-150 hover:shadow-xl hover:scale-105"
                               style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                                💾 Save Results to Profile
                                <svg class="ml-2 w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                            </button>
                        </form>
                    </div>
                    
                    <!-- Create Itinerary Button -->
                    <div class="pt-6 border-t border-gray-200">
                        <h3 class="text-xl font-bold mb-4 text-gray-800">Ready for Your Adventure?</h3>
                        <p class="text-gray-600 mb-6">Based on your assessment results, create a personalized hiking itinerary that matches your readiness level.</p>
                        <a href="{{ route('itinerary.build') }}"
                           class="inline-flex items-center px-8 py-4 text-white text-lg font-semibold rounded-lg shadow-lg transition duration-150 hover:shadow-xl hover:scale-105"
                           style="background: linear-gradient(135deg, #e3a746 0%, #f4b942 100%);">
                            Create Personalized Itinerary
                            <svg class="ml-2 w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function printResults() {
            window.print();
        }
        function shareResults() {
            if (navigator.share) {
                navigator.share({
                    title: 'My Hiking Assessment Results',
                    text: `I scored {{ $scores['overall'] }}% on my hiking readiness assessment! My level: {{ $readinessLevel['level'] }}`,
                    url: window.location.href
                });
            } else {
                const text = `I scored {{ $scores['overall'] }}% on my hiking readiness assessment! My level: {{ $readinessLevel['level'] }}`;
                navigator.clipboard.writeText(text).then(() => {
                    alert('Results copied to clipboard!');
                });
            }
        }
        

        document.addEventListener('DOMContentLoaded', function() {
            // Animate the overall score
            const scoreElement = document.getElementById('overall-score');
            const progressBar = document.getElementById('overall-progress');
            const targetScore = {{ $scores['overall'] }};
            let currentScore = 0;
            const increment = targetScore / 100;
            const scoreTimer = setInterval(() => {
                currentScore += increment;
                if (currentScore >= targetScore) {
                    currentScore = targetScore;
                    clearInterval(scoreTimer);
                }
                scoreElement.textContent = Math.round(currentScore) + '%';
            }, 15);
            setTimeout(() => {
                progressBar.style.width = targetScore + '%';
            }, 500);
            setTimeout(() => {
                document.querySelectorAll('.score-bar').forEach((bar, index) => {
                    setTimeout(() => {
                        bar.style.width = bar.dataset.width;
                    }, index * 200);
                });
                document.querySelectorAll('.score-number').forEach((number, index) => {
                    setTimeout(() => {
                        const target = parseInt(number.dataset.target);
                        let current = 0;
                        const increment = target / 50;
                        const timer = setInterval(() => {
                            current += increment;
                            if (current >= target) {
                                current = target;
                                clearInterval(timer);
                            }
                            number.textContent = Math.round(current) + '%';
                        }, 20);
                    }, index * 200);
                });
            }, 1000);
        });
    </script>
    <style>
        @media print {
            .no-print {
                display: none !important;
            }
            .print-break {
                page-break-before: always;
            }
            body {
                -webkit-print-color-adjust: exact;
                color-adjust: exact;
            }
        }
        .transition-all {
            transition: all 0.3s ease;
        }
        .hover\:scale-105:hover {
            transform: scale(1.05);
        }
        .animate-bounce {
            animation: bounce 1s infinite;
        }
        @keyframes bounce {
            0%, 20%, 53%, 80%, 100% {
                transform: translateY(0);
            }
            40%, 43% {
                transform: translateY(-30px);
            }
            70% {
                transform: translateY(-15px);
            }
            90% {
                transform: translateY(-4px);
            }
        }
    </style>
</x-app-layout>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Organization {{ ucfirst($action) }} - HikeThere Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'hike-teal': '#336d66',
                        'hike-blue': '#20b6d2',
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gradient-to-br from-hike-teal/5 to-white min-h-screen">
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-6 bg-white/80 backdrop-blur-lg p-8 rounded-2xl shadow-xl text-center">
            
            <!-- Logo -->
            <div class="flex justify-center">
                <img src="{{ asset('img/icon1.png') }}" alt="HikeThere Logo" class="h-16 w-auto">
            </div>

            @if($action === 'approved')
                <!-- Success Icon -->
                <div class="flex justify-center">
                    <div class="rounded-full bg-green-100 p-3">
                        <svg class="h-12 w-12 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>

                <!-- Success Message -->
                <div class="space-y-2">
                    <h2 class="text-2xl font-bold text-gray-900">Organization Approved! ✅</h2>
                    <p class="text-gray-600">
                        <strong>{{ $organization }}</strong> has been successfully approved and notified via email.
                    </p>
                </div>

                <!-- Success Details -->
                <div class="bg-green-50 border border-green-200 p-4 rounded-lg text-left">
                    <h3 class="font-semibold text-green-800 mb-2">Actions Completed:</h3>
                    <ul class="text-sm text-green-700 space-y-1">
                        <li>• Organization status updated to "Approved"</li>
                        <li>• Approval timestamp recorded</li>
                        <li>• Welcome email sent to organization</li>
                        <li>• Account access granted</li>
                    </ul>
                </div>

            @else
                <!-- Rejection Icon -->
                <div class="flex justify-center">
                    <div class="rounded-full bg-red-100 p-3">
                        <svg class="h-12 w-12 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16c-.77.833.192 2.5 1.732 2.5z" />
                        </svg>
                    </div>
                </div>

                <!-- Rejection Message -->
                <div class="space-y-2">
                    <h2 class="text-2xl font-bold text-gray-900">Organization Rejected ❌</h2>
                    <p class="text-gray-600">
                        <strong>{{ $organization }}</strong> has been rejected and notified via email.
                    </p>
                </div>

                <!-- Rejection Details -->
                <div class="bg-red-50 border border-red-200 p-4 rounded-lg text-left">
                    <h3 class="font-semibold text-red-800 mb-2">Actions Completed:</h3>
                    <ul class="text-sm text-red-700 space-y-1">
                        <li>• Organization status updated to "Rejected"</li>
                        <li>• Rejection notification sent</li>
                        <li>• Account access restricted</li>
                    </ul>
                </div>
            @endif

            <!-- Timestamp -->
            <div class="text-sm text-gray-500 pt-4 border-t">
                Action completed on {{ now()->format('F j, Y \a\t g:i A') }}
            </div>

            <!-- Footer -->
            <div class="pt-6">
                <p class="text-xs text-gray-400">
                    HikeThere Admin Action Confirmation
                </p>
            </div>
        </div>
    </div>
</body>
</html>
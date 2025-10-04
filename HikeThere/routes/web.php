<?php

use App\Http\Controllers\AssessmentController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\CommunityController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExploreController;
use App\Http\Controllers\GPXLibraryController;
use App\Http\Controllers\Hiker\RefactoredItineraryController;
use App\Http\Controllers\ItineraryController;
use App\Http\Controllers\LocationWeatherController;
use App\Http\Controllers\MapController;
use App\Http\Controllers\OrganizationApprovalController;
use App\Http\Controllers\OrganizationTrailController;
use App\Http\Controllers\OrganizationLocationController;
use App\Http\Controllers\TrailController;
use App\Http\Controllers\TrailCoordinateController;
use App\Http\Controllers\TrailPdfController;
use App\Http\Controllers\TrailTcpdfController;
use App\Http\Controllers\TrailReviewController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Include Jetstream routes
require __DIR__ . '/jetstream.php';

// Local testing helper (remove in production) - MUST be outside auth middleware
Route::get('/test/confirm-payment/{paymentId}', [App\Http\Controllers\PaymentController::class, 'testConfirmPayment'])->name('payment.test.confirm');

Route::get('/', function () {
    return view('welcome');
});

Route::get('/test-gpx', function () {
    return view('test-gpx');
});

// Email verification routes
Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');

// Guest email verification notice (for newly registered users)
Route::get('/email/verify/notice', function () {
    return view('auth.verify-email-notice');
})->name('verification.notice.guest');

Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();

    // After verification, redirect hikers to the onboarding preferences flow so they can set hiking preferences.
    $user = $request->user();
    if ($user && $user->user_type === 'hiker') {
        return redirect()->route('onboard.preferences')
            ->with('status', 'Email verified successfully! Please set your hiking preferences to get personalized recommendations.');
    }

    // For other users, redirect to login as before
    return redirect()->route('login')
        ->with('status', 'Email verified successfully! You can now sign in to your account.');
})->middleware(['auth:sanctum', 'signed'])->name('verification.verify');

Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();

    return back()->with('message', 'Verification link sent!');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');

// Routes that require authentication and email verification (for hikers only)
Route::middleware([
    'auth:sanctum',
    'verified',
])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Weather animation test page
    Route::get('/weather-animation-test', function () {
        return view('weather-animation-test');
    })->name('weather-animation-test');

    Route::get('/explore', [ExploreController::class, 'index'])->name('explore');

    // Advanced Trail Map
    Route::get('/advanced-trail-map', function () {
        return view('components.advanced-trail-map');
    })->name('advanced-trail-map');

    Route::get('/advanced-trail-map/{trail:slug}', function (App\Models\Trail $trail) {
        return view('components.advanced-trail-map', compact('trail'));
    })->name('advanced-trail-map.trail');

    // Hiker Tools
    Route::get('/hiker/hiking-tools', function () {
        return view('hiker.hiking-tools');
    })->name('hiking-tools');

    // Booking controller routes
    // Compatibility: legacy route name `booking.details` used in some views -> route to create page
    Route::get('/hiker/booking/booking-details', [App\Http\Controllers\Hiker\BookingController::class, 'create'])->name('booking.details');
    Route::get('/hiker/booking', [App\Http\Controllers\Hiker\BookingController::class, 'index'])->name('booking.index');
    Route::get('/hiker/booking/create', [App\Http\Controllers\Hiker\BookingController::class, 'create'])->name('booking.create');
    Route::post('/hiker/booking', [App\Http\Controllers\Hiker\BookingController::class, 'store'])->name('booking.store');
    Route::get('/hiker/booking/{booking}', [App\Http\Controllers\Hiker\BookingController::class, 'show'])->name('booking.show');
    Route::get('/hiker/booking/{booking}/payment', [App\Http\Controllers\Hiker\BookingController::class, 'showPayment'])->name('booking.payment');
    Route::post('/hiker/booking/{booking}/payment', [App\Http\Controllers\Hiker\BookingController::class, 'submitPayment'])->name('booking.payment.submit');
    Route::get('/hiker/booking/{booking}/edit', [App\Http\Controllers\Hiker\BookingController::class, 'edit'])->name('booking.edit');
    Route::patch('/hiker/booking/{booking}', [App\Http\Controllers\Hiker\BookingController::class, 'update'])->name('booking.update');
    Route::delete('/hiker/booking/{booking}', [App\Http\Controllers\Hiker\BookingController::class, 'destroy'])->name('booking.destroy');
    Route::get('/hiker/booking/{booking}/download-slip', [App\Http\Controllers\Hiker\BookingController::class, 'downloadSlip'])->name('booking.download-slip');
    Route::get('/hiker/booking/package-details', [App\Http\Controllers\Hiker\BookingController::class, 'packageDetails'])->name('package.details');

    // AJAX endpoint to fetch trails for an organization (only for followed orgs)
    Route::get('/hiker/api/organization/{organization}/trails', [App\Http\Controllers\Hiker\BookingController::class, 'organizationTrails'])->name('api.hiker.organization.trails');

    // Trail package details used by booking preview
    // Bind by id (Trail model uses slug as default route key) so numeric ids work
    Route::get('/hiker/api/trail/{trail:id}/package', [App\Http\Controllers\Hiker\BookingController::class, 'trailPackage'])->name('api.hiker.trail.package');

    // Batches API for booking form
    Route::get('/hiker/api/trail/{trail:id}/batches', [App\Http\Controllers\Hiker\BookingController::class, 'trailBatches'])->name('api.hiker.trail.batches');

    // Slot availability API
    Route::get('/api/slots/batch/{batchId}', [App\Http\Controllers\Api\SlotAvailabilityController::class, 'checkBatch'])->name('api.slots.check');
    Route::get('/api/slots/trail/{trailId}/alternatives', [App\Http\Controllers\Api\SlotAvailabilityController::class, 'getAlternatives'])->name('api.slots.alternatives');

    // Payment routes
    Route::get('/payment/create', [App\Http\Controllers\PaymentController::class, 'create'])->name('payment.create');
    Route::post('/payment/process', [App\Http\Controllers\PaymentController::class, 'processPayment'])->name('payment.process');
    Route::get('/payment/success', [App\Http\Controllers\PaymentController::class, 'success'])->name('payment.success');
    Route::post('/payment/webhook', [App\Http\Controllers\PaymentController::class, 'webhook'])->name('payment.webhook');

    Route::get('/hiker/itinerary/itinerary-instructions', function () {
        return view('hiker.itinerary.itinerary-instructions');
    })->name('itinerary.instructions');

    Route::get('/hiker/itinerary/build', [ItineraryController::class, 'build'])->name('hiker.itinerary.build');

    // Submission: handle itinerary payloads via ItineraryController@store
    Route::post('/hiker/itinerary/generate', [ItineraryController::class, 'store'])->name('hiker.itinerary.generate');

    // Assessment routes
    Route::get('/assessment/instruction', [AssessmentController::class, 'instruction'])->name('assessment.instruction');
    Route::get('/assessment/gear', [AssessmentController::class, 'gear'])->name('assessment.gear');
    Route::get('/assessment/fitness', [AssessmentController::class, 'fitness'])->name('assessment.fitness');
    Route::get('/assessment/health', [AssessmentController::class, 'health'])->name('assessment.health');
    Route::get('/assessment/weather', [AssessmentController::class, 'weather'])->name('assessment.weather');
    Route::get('/assessment/emergency', [AssessmentController::class, 'emergency'])->name('assessment.emergency');
    Route::get('/assessment/environment', [AssessmentController::class, 'environment'])->name('assessment.environment');
    Route::get('/assessment/result', [AssessmentController::class, 'result'])->name('assessment.result');
    Route::get('/assessment/saved-results', [AssessmentController::class, 'viewSavedResults'])->name('assessment.saved-results');
    Route::post('/assessment/save-results', [AssessmentController::class, 'saveResults'])->name('assessment.save-results');

    // Assessment form submissions
    Route::post('/assessment/gear', [AssessmentController::class, 'storeGear'])->name('assessment.gear.store');
    Route::post('/assessment/fitness', [AssessmentController::class, 'storeFitness'])->name('assessment.fitness.store');
    Route::post('/assessment/health', [AssessmentController::class, 'storeHealth'])->name('assessment.health.store');
    Route::post('/assessment/weather', [AssessmentController::class, 'storeWeather'])->name('assessment.weather.store');
    Route::post('/assessment/emergency', [AssessmentController::class, 'storeEmergency'])->name('assessment.emergency.store');
    Route::post('/assessment/environment', [AssessmentController::class, 'storeEnvironment'])->name('assessment.environment.store');

    // Itinerary routes
    Route::get('/itinerary', [ItineraryController::class, 'index'])->name('itinerary.index');
    Route::get('/itinerary/build', [ItineraryController::class, 'build'])->name('itinerary.build');
    Route::get('/itinerary/build/{trail}', [ItineraryController::class, 'buildWithTrail'])->name('itinerary.build.trail');
    // Submission: handle itinerary payloads via ItineraryController@store
    Route::post('/itinerary/generate', [ItineraryController::class, 'store'])->name('itinerary.generate');
    Route::post('/hiker/itinerary/generate-pdf', [ItineraryController::class, 'generatePdf'])->name('itinerary.generate.pdf');
    Route::get('/itinerary/{itinerary}/print', [ItineraryController::class, 'printView'])->name('itinerary.print');
    Route::get('/itinerary/{itinerary}/pdf', [ItineraryController::class, 'pdf'])->name('itinerary.pdf');
    Route::get('/itinerary/{itinerary}', [ItineraryController::class, 'show'])->name('itinerary.show');

    // Refactored itinerary routes for testing
    Route::get('/itinerary/refactored/preview', [App\Http\Controllers\Hiker\RefactoredItineraryController::class, 'preview'])->name('itinerary.refactored.preview');
    Route::get('/itinerary/refactored/show', [App\Http\Controllers\Hiker\RefactoredItineraryController::class, 'show'])->name('itinerary.refactored.show');
    Route::post('/itinerary/refactored/api', [App\Http\Controllers\Hiker\RefactoredItineraryController::class, 'generateApi'])->name('itinerary.refactored.api');

    // Community routes
    Route::get('/community', [CommunityController::class, 'index'])->name('community.index');
    Route::get('/community/organization/{organization}', [CommunityController::class, 'showOrganization'])->name('community.organization.show');

    // AJAX Community routes
    Route::post('/api/community/follow', [CommunityController::class, 'follow'])->name('api.community.follow');
    Route::post('/api/community/unfollow', [CommunityController::class, 'unfollow'])->name('api.community.unfollow');
    Route::get('/api/community/followed-trails', [CommunityController::class, 'getFollowedTrails'])->name('api.community.followed-trails');
    Route::get('/api/community/organization/{organization}', [CommunityController::class, 'getOrganization'])->name('api.community.organization');
    Route::get('/api/community/organization/{organization}/trails', [CommunityController::class, 'getOrganizationTrails'])->name('api.community.organization.trails');

    // AJAX Trail Review routes
    Route::post('/api/trails/reviews', [TrailReviewController::class, 'store'])->name('api.trails.reviews.store');
    Route::put('/api/trails/reviews/{review}', [TrailReviewController::class, 'update'])->name('api.trails.reviews.update');
    Route::delete('/api/trails/reviews/{review}', [TrailReviewController::class, 'destroy'])->name('api.trails.reviews.destroy');

    // Notifications routes
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [App\Http\Controllers\NotificationController::class, 'index'])->name('index');
        Route::get('/api/get', [App\Http\Controllers\NotificationController::class, 'getNotifications'])->name('get');
        Route::get('/api/latest', [App\Http\Controllers\NotificationController::class, 'getLatest'])->name('latest');
        Route::post('/{id}/read', [App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('read');
        Route::post('/{id}/unread', [App\Http\Controllers\NotificationController::class, 'markAsUnread'])->name('unread');
        Route::post('/read-all', [App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('read-all');
        Route::delete('/{id}', [App\Http\Controllers\NotificationController::class, 'destroy'])->name('destroy');
        Route::delete('/read/clear', [App\Http\Controllers\NotificationController::class, 'destroyRead'])->name('destroy-read');
    });
    
    // Debug route to manually test toast notification
    Route::get('/test-toast-notification', function () {
        $user = Auth::user();
        $notificationService = new App\Services\NotificationService();
        
        // Create a test notification
        $notification = $notificationService->create(
            $user,
            'system',
            'Test Toast Notification',
            'This is a test toast notification! If you see this, the system is working correctly. 🎉',
            [
                'test' => true,
                'timestamp' => now()->toDateTimeString()
            ]
        );
        
        return response()->json([
            'success' => true,
            'message' => 'Test notification created! Check your notifications.',
            'notification' => $notification
        ]);
    })->middleware('auth');
    
    // Debug route to manually test weather notification
    Route::get('/test-weather-notification', function () {
        $user = Auth::user();
        $weatherService = new App\Services\WeatherNotificationService(
            new App\Services\NotificationService()
        );
        
        try {
            $notification = $weatherService->sendLoginWeatherNotification($user);
            if ($notification) {
                return response()->json([
                    'success' => true,
                    'message' => 'Weather notification sent!',
                    'notification' => $notification
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to send weather notification - check logs'
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    })->name('test.weather.notification');
})->middleware(['user.type:hiker', 'ensure.hiking.preferences']);

// Onboarding routes for hikers to set preferences (shown after email verification)
Route::middleware(['auth:sanctum', 'verified', 'user.type:hiker'])->group(function () {
    Route::get('/onboard/preferences', [App\Http\Controllers\OnboardingController::class, 'showPreferences'])->name('onboard.preferences');
    Route::post('/onboard/preferences', [App\Http\Controllers\OnboardingController::class, 'savePreferences'])->name('onboard.preferences.save');
});

// Routes that require authentication and approval (for organizations only)
Route::middleware(['auth:sanctum', 'check.approval'])->group(function () {
    // Organization dashboard
    Route::get('/org/dashboard', function () {
        $totalTrails = \App\Models\Trail::where('user_id', auth()->id())->count();
        $activeEvents = \App\Models\Event::where('user_id', auth()->id())
            ->where(function($query) {
                $query->where('end_at', '>=', now())
                    ->orWhere('always_available', true);
            })
            ->count();
        return view('org.dashboard', compact('totalTrails', 'activeEvents'));
    })->name('org.dashboard');

    // Organization trails management
    Route::resource('org/trails', OrganizationTrailController::class, ['as' => 'org']);
    Route::patch('/org/trails/{trail}/toggle-status', [OrganizationTrailController::class, 'toggleStatus'])->name('org.trails.toggle-status');

    // Organization locations (inline management)
    Route::get('/org/locations', [OrganizationLocationController::class, 'index'])->name('org.locations.index');
    Route::post('/org/locations', [OrganizationLocationController::class, 'store'])->name('org.locations.store');
    Route::get('/org/locations/{location:slug}/edit', [OrganizationLocationController::class, 'edit'])->name('org.locations.edit');
    Route::patch('/org/locations/{location:slug}', [OrganizationLocationController::class, 'update'])->name('org.locations.update');
    Route::delete('/org/locations/{location:slug}', [OrganizationLocationController::class, 'destroy'])->name('org.locations.destroy');

    // Organization Trail Coordinate Generation routes
    Route::post('/org/trails/generate-coordinates', [TrailCoordinateController::class, 'generateCoordinatesFromForm'])->name('org.trails.generate-coordinates');
    Route::post('/org/trails/generate-google-coordinates', [TrailCoordinateController::class, 'generateCoordinatesFromForm'])->name('org.trails.generate-google-coordinates');
    Route::post('/org/trails/generate-custom-coordinates', [TrailCoordinateController::class, 'generateCustomCoordinatesFromForm'])->name('org.trails.generate-custom-coordinates');
    Route::post('/org/trails/preview-coordinates', [TrailCoordinateController::class, 'previewCoordinates'])->name('org.trails.preview-coordinates');

    // GPX Library routes for trail creation
    Route::get('/api/gpx-library', [\App\Http\Controllers\GPXLibraryController::class, 'index'])->name('api.gpx-library');
    Route::post('/api/gpx-library/parse', [\App\Http\Controllers\GPXLibraryController::class, 'parseGPX'])->name('api.gpx-library.parse');
    Route::post('/api/gpx-library/search', [\App\Http\Controllers\GPXLibraryController::class, 'searchTrails'])->name('api.gpx-library.search');

    // Protected routes that require approval
    // These routes will be accessible to approved organizations
})->middleware('user.type:organization');

// Organization Bookings management - accessible to approved organizations
Route::middleware(['auth:sanctum', 'check.approval', 'user.type:organization'])->group(function () {
    // Organization bookings listing and details
    Route::get('/org/bookings', [App\Http\Controllers\OrganizationBookingController::class, 'index'])->name('org.bookings.index');
    Route::get('/org/bookings/{booking}', [App\Http\Controllers\OrganizationBookingController::class, 'show'])->name('org.bookings.show');
    Route::patch('/org/bookings/{booking}/status', [App\Http\Controllers\OrganizationBookingController::class, 'updateStatus'])->name('org.bookings.update-status');
    
    // Manual payment verification
    Route::post('/org/bookings/{booking}/verify-payment', [App\Http\Controllers\OrganizationBookingController::class, 'verifyPayment'])->name('org.bookings.verify-payment');
    Route::post('/org/bookings/{booking}/reject-payment', [App\Http\Controllers\OrganizationBookingController::class, 'rejectPayment'])->name('org.bookings.reject-payment');
});

// Organization Payment Setup - accessible to approved organizations
Route::middleware(['auth:sanctum', 'check.approval', 'user.type:organization'])->group(function () {
    Route::get('/org/payment', [App\Http\Controllers\OrganizationPaymentController::class, 'index'])->name('org.payment.index');
    Route::put('/org/payment', [App\Http\Controllers\OrganizationPaymentController::class, 'update'])->name('org.payment.update');
    Route::put('/org/payment/manual', [App\Http\Controllers\OrganizationPaymentController::class, 'updateManual'])->name('org.payment.update-manual');
    Route::put('/org/payment/toggle-method', [App\Http\Controllers\OrganizationPaymentController::class, 'togglePaymentMethod'])->name('org.payment.toggle-method');
    Route::post('/org/payment/test', [App\Http\Controllers\OrganizationPaymentController::class, 'test'])->name('org.payment.test');
    Route::delete('/org/payment/clear', [App\Http\Controllers\OrganizationPaymentController::class, 'clear'])->name('org.payment.clear');
});

// About HikeThere route (for organizations)
Route::middleware(['auth:sanctum', 'check.approval', 'user.type:organization'])->group(function () {
    Route::get('/org/about', function () {
        return view('org.about');
    })->name('org.about');
});

// Organization Events management
Route::middleware(['auth:sanctum', 'check.approval', 'user.type:organization'])->group(function () {
    Route::resource('org/events', App\Http\Controllers\OrganizationEventController::class, ['as' => 'org']);
});

// Public Events
Route::get('/events', [App\Http\Controllers\EventController::class, 'index'])->name('events.index');
Route::get('/events/{slug}', [App\Http\Controllers\EventController::class, 'show'])->name('events.show');

// Hiker-specific event detail route (authenticated hikers)
Route::middleware(['auth:sanctum', 'verified', 'user.type:hiker'])->group(function () {
    Route::get('/hiker/events/{slug}', [App\Http\Controllers\EventController::class, 'show'])->name('hiker.events.show');
});

// Public routes
Route::get('/location-weather', [LocationWeatherController::class, 'getWeather']);
Route::get('/trails', [TrailController::class, 'index'])->name('trails.index');
Route::get('/trails/search', [TrailController::class, 'search'])->name('trails.search');

// Trail PDF and Elevation routes
Route::get('/trails/{trail}/download-map', [TrailPdfController::class, 'downloadMap'])->name('trails.download-map');
Route::get('/trails/{trail}/print-map', [TrailPdfController::class, 'printMap'])->name('trails.print-map');
Route::get('/trails/{trail}/download-map-tcpdf', [TrailTcpdfController::class, 'downloadMap'])->name('trails.download-map-tcpdf');
Route::get('/trails/{trail}/elevation-profile', [TrailPdfController::class, 'getElevationProfile'])->name('trails.elevation-profile');

// Public AJAX routes for trail reviews (anyone can view reviews)
Route::get('/api/trails/{trail}/reviews', [TrailReviewController::class, 'getTrailReviews'])->name('api.trails.reviews.index');

// API route to check assessment status
Route::get('/api/user/assessment-status', function(\Illuminate\Http\Request $request) {
    $user = $request->user();
    $hasAssessment = $user && $user->latestAssessmentResult()->exists();
    
    return response()->json([
        'success' => true,
        'has_assessment' => $hasAssessment,
        'message' => $hasAssessment ? 'User has completed assessment' : 'User needs to complete assessment'
    ]);
})->middleware('auth')->name('api.user.assessment-status');

// Web route to toggle favorites using session-based auth (fallback for logged-in users)
Route::post('/trails/favorite/toggle', [App\Http\Controllers\Api\TrailFavoriteController::class, 'toggle'])
    ->middleware('auth')
    ->name('trails.favorite.toggle');

// Check if the current user has favorited a specific trail (authenticated users only)
Route::get('/trails/{trail}/is-favorited', [App\Http\Controllers\Api\TrailFavoriteController::class, 'isFavorited'])
    ->middleware('auth')
    ->name('trails.is-favorited');

// Public AJAX routes for trail information
Route::get('/api/trails/{trail}', [App\Http\Controllers\Api\TrailController::class, 'show'])->name('api.trails.show');
Route::get('/api/trail/{trail}/payment-method', [App\Http\Controllers\Api\TrailController::class, 'getPaymentMethod'])->name('api.trail.payment-method');

// Enhanced Map routes (temporarily public for testing)
Route::get('/map', [MapController::class, 'index'])->name('map.index');
Route::get('/map/simple', function () {
    return view('map.simple');
})->name('map.simple');
Route::get('/map/demo', [MapController::class, 'demo'])->name('map.demo');
Route::get('/map/trails', [MapController::class, 'getTrails'])->name('map.trails');
Route::get('/map/trails/{id}', [MapController::class, 'getTrailDetails'])->name('map.trail.details');
Route::get('/map/trails/{id}/images', [MapController::class, 'getTrailImages'])->name('map.trail.images');
Route::get('/map/trails/{id}/elevation', [MapController::class, 'getTrailElevation'])->name('map.trail.elevation');
Route::get('/map/trail-paths', [MapController::class, 'getTrailPaths'])->name('map.trail.paths');
Route::get('/map/enhanced-trails', [MapController::class, 'getEnhancedTrails'])->name('map.enhanced.trails');
Route::get('/map/weather', [MapController::class, 'getWeatherData'])->name('map.weather');
Route::post('/map/search-nearby', [MapController::class, 'searchNearby'])->name('map.search.nearby');

// Profile routes (require authentication)
Route::middleware(['auth:sanctum', 'ensure.hiking.preferences'])->group(function () {
    // Trail show route (authenticated hikers only)
    Route::get('/trails/{trail}', [TrailController::class, 'show'])->name('trails.show');
    
    Route::get('/profile', [App\Http\Controllers\ProfileController::class, 'show'])->name('custom.profile.show');
    // Saved Trails view (server-side rendered)
    Route::get('/profile/saved-trails', [App\Http\Controllers\SavedTrailsController::class, 'index'])
        ->name('profile.saved-trails');

    // Proxy JSON endpoint for favorites (session auth) - returns same shape as API index()
    Route::get('/profile/api/favorites', [App\Http\Controllers\Api\TrailFavoriteController::class, 'index'])->name('profile.api.favorites');
    Route::get('/profile/edit', [App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile/picture', [App\Http\Controllers\ProfileController::class, 'deleteProfilePicture'])->name('profile.picture.delete');
    // Dedicated AJAX upload endpoint for profile picture (returns JSON)
    Route::post('/profile/picture/upload', [App\Http\Controllers\ProfileController::class, 'uploadProfilePicture'])->name('profile.picture.upload');

    // Account Settings route
    Route::get('/account/settings', [App\Http\Controllers\AccountSettingsController::class, 'index'])->name('account.settings');

    // About HikeThere route (for hikers)
    Route::get('/about', function () {
        return view('about');
    })->name('about');

    // Account Settings - Preferences
    Route::get('/account/preferences', [App\Http\Controllers\AccountSettings\PreferencesController::class, 'index'])->name('preferences.index');
    Route::post('/account/preferences', [App\Http\Controllers\AccountSettings\PreferencesController::class, 'update'])->name('preferences.update');
    Route::post('/account/preferences/reset', [App\Http\Controllers\AccountSettings\PreferencesController::class, 'reset'])->name('preferences.reset');
    Route::get('/account/preferences/export', [App\Http\Controllers\AccountSettings\PreferencesController::class, 'export'])->name('preferences.export');
});

// Guest routes (registration and login)
Route::middleware(['guest'])->group(function () {
    Route::get('/register/select', function () {
        return view('auth.register-select');
    })->name('register.select');

    Route::get('/register', [RegisteredUserController::class, 'create'])
        ->name('register');

    Route::post('/register', [RegisteredUserController::class, 'store']);

    Route::get('/register/organization', [RegisteredUserController::class, 'createOrganization'])
        ->name('register.organization');

    Route::post('/register/organization', [RegisteredUserController::class, 'storeOrganization'])->name('register.organization.store');
});

// Static pages
Route::get('/policy', function () {
    return view('policy');
})->name('custom.policy.show');

Route::get('/terms', function () {
    return view('terms');
})->name('custom.terms.show');

Route::get('/pending-approval', function () {
    return view('auth.pending-approval');
})->name('auth.pending-approval');

// Admin routes for managing approvals - REMOVED
// Organizations are now approved directly via email links

// Organization approval routes (direct from email) - using signed URLs for security
Route::get('/organizations/{user}/approve/email', [OrganizationApprovalController::class, 'approveFromEmail'])
    ->name('organizations.approve.email')
    ->middleware(['signed', 'throttle:3,1']); // Limit to 3 attempts per minute
Route::get('/organizations/{user}/reject/email', [OrganizationApprovalController::class, 'rejectFromEmail'])
    ->name('organizations.reject.email')
    ->middleware(['signed', 'throttle:3,1']); // Limit to 3 attempts per minute

// Admin routes
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/review-moderation', [App\Http\Controllers\Admin\ReviewModerationController::class, 'index'])->name('review-moderation.index');
    Route::post('/review-moderation/{review}/approve', [App\Http\Controllers\Admin\ReviewModerationController::class, 'approve'])->name('review-moderation.approve');
    Route::post('/review-moderation/{review}/reject', [App\Http\Controllers\Admin\ReviewModerationController::class, 'reject'])->name('review-moderation.reject');
    Route::post('/review-moderation/{review}/remoderate', [App\Http\Controllers\Admin\ReviewModerationController::class, 'remoderate'])->name('review-moderation.remoderate');
    Route::get('/review-moderation/{review}', [App\Http\Controllers\Admin\ReviewModerationController::class, 'show'])->name('review-moderation.show');
    Route::post('/review-moderation/bulk-approve', [App\Http\Controllers\Admin\ReviewModerationController::class, 'bulkApprove'])->name('review-moderation.bulk-approve');
    Route::get('/review-moderation/statistics', [App\Http\Controllers\Admin\ReviewModerationController::class, 'statistics'])->name('review-moderation.statistics');
});

//  SOL START
// Assessment routes have been moved to the protected hiker section
// SOL END

// Test route for debugging Google Maps integration
Route::get('/debug-itinerary', function () {
    $service = app(\App\Services\ItineraryGeneratorService::class);
    
    $itineraryData = [
        (object) [
            'duration_days' => 2,
            'duration_nights' => 1
        ]
    ];

    $trail = [
        'name' => 'Mt. Pulag via Ambangeg Trail',
        'location' => 'Benguet, Philippines',
        'latitude' => 16.5966,
        'longitude' => 120.9060,
        'transport_included' => 1,
        'transport_details' => 'Van transportation from Shaw Boulevard Manila to Ambangeg Trail',
        'departure_point' => 'Shaw Boulevard Manila'
    ];

    $buildData = [
        'user_lat' => 14.6417,
        'user_lng' => 120.4736,
        'user_location' => 'Bataan, Philippines',
        'start_date' => now()->addDays(7)->format('Y-m-d')
    ];
    
    $generatedData = $service->generateItinerary($itineraryData, $trail, $buildData);
    
    return response()->json([
        'preHikeActivities' => $generatedData['preHikeActivities'] ?? [],
        'totalActivities' => count($generatedData['preHikeActivities'] ?? []),
        'totalHours' => isset($generatedData['preHikeActivities']) && !empty($generatedData['preHikeActivities']) 
            ? round(end($generatedData['preHikeActivities'])['minutes'] / 60, 1) 
            : 0,
        'debugInfo' => [
            'trail_passed' => $trail,
            'build_passed' => $buildData,
            'activities_detail' => $generatedData['preHikeActivities'] ?? []
        ]
    ]);
});

// Test route without transport_included to see if that's the issue
Route::get('/debug-itinerary-no-transport', function () {
    $service = app(\App\Services\ItineraryGeneratorService::class);
    
    $itineraryData = [
        (object) [
            'duration_days' => 2,
            'duration_nights' => 1
        ]
    ];

    // This mimics what might come from database/web interface - NO transport_included
    $trail = [
        'name' => 'Mt. Pulag via Ambangeg Trail',
        'location' => 'Benguet, Philippines',
        'latitude' => 16.5966,
        'longitude' => 120.9060,
        'transport_details' => 'Van transportation from Shaw Boulevard Manila to Ambangeg Trail',
        'departure_point' => 'Shaw Boulevard Manila'
    ];

    $buildData = [
        'user_lat' => 14.6417,
        'user_lng' => 120.4736,
        'user_location' => 'Bataan, Philippines',
        'start_date' => now()->addDays(7)->format('Y-m-d')
    ];
    
    $generatedData = $service->generateItinerary($itineraryData, $trail, $buildData);
    
    return response()->json([
        'scenario' => 'WITHOUT transport_included',
        'preHikeActivities' => $generatedData['preHikeActivities'] ?? [],
        'totalHours' => isset($generatedData['preHikeActivities']) && !empty($generatedData['preHikeActivities']) 
            ? round(end($generatedData['preHikeActivities'])['minutes'] / 60, 1) 
            : 0,
        'activities_detail' => $generatedData['preHikeActivities'] ?? []
    ]);
});

// API Routes for Event Polling (Real-time updates)
Route::prefix('api')->group(function () {
    Route::get('/events/latest', [App\Http\Controllers\Api\EventPollingController::class, 'getLatestEvents'])->name('api.events.latest');
    Route::get('/events/count', [App\Http\Controllers\Api\EventPollingController::class, 'getEventCount'])->name('api.events.count');
});

<?php

use App\Http\Controllers\AssessmentController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\CommunityController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExploreController;
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
require __DIR__.'/jetstream.php';

Route::get('/', function () {
    return view('welcome');
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

    // After verification, redirect to login with success message
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

    Route::get('/hiker/booking/booking-details', function () {
        return view('hiker.booking.booking-details');
    })->name('booking.details');

    Route::get('/hiker/booking/package-details', function () {
        return view('hiker.booking.package-details');
    })->name('package.details');

    Route::get('/hiker/itinerary/itinerary-instructions', function () {
        return view('hiker.itinerary.itinerary-instructions');
    })->name('itinerary.instructions');

    Route::get('/hiker/itinerary/build', [ItineraryController::class, 'build'])->name('hiker.itinerary.build');

    Route::post('/hiker/itinerary/generate', [ItineraryController::class, 'generate'])->name('hiker.itinerary.generate');

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
    Route::get('/itinerary/build', [ItineraryController::class, 'build'])->name('itinerary.build');
    Route::post('/itinerary/generate', [ItineraryController::class, 'generate'])->name('itinerary.generate');
    Route::get('/itinerary/{itinerary}/pdf', [ItineraryController::class, 'pdf'])->name('itinerary.pdf');
    Route::get('/itinerary/{itinerary}', [ItineraryController::class, 'show'])->name('itinerary.show');

    // Community routes
    Route::get('/community', [CommunityController::class, 'index'])->name('community.index');

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
})->middleware('user.type:hiker');

// Routes that require authentication and approval (for organizations only)
Route::middleware(['auth:sanctum', 'check.approval'])->group(function () {
    // Organization dashboard
    Route::get('/org/dashboard', function () {
        return view('org.dashboard');
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

    // Protected routes that require approval
    // These routes will be accessible to approved organizations
})->middleware('user.type:organization');

// Public routes
Route::get('/location-weather', [LocationWeatherController::class, 'getWeather']);
Route::get('/trails', [TrailController::class, 'index'])->name('trails.index');
Route::get('/trails/{trail}', [TrailController::class, 'show'])->name('trails.show');
Route::get('/trails/search', [TrailController::class, 'search'])->name('trails.search');

// Trail PDF and Elevation routes
Route::get('/trails/{trail}/download-map', [TrailPdfController::class, 'downloadMap'])->name('trails.download-map');
Route::get('/trails/{trail}/download-map-tcpdf', [TrailTcpdfController::class, 'downloadMap'])->name('trails.download-map-tcpdf');
Route::get('/trails/{trail}/elevation-profile', [TrailPdfController::class, 'getElevationProfile'])->name('trails.elevation-profile');

// Public AJAX routes for trail reviews (anyone can view reviews)
Route::get('/api/trails/{trail}/reviews', [TrailReviewController::class, 'getTrailReviews'])->name('api.trails.reviews.index');

// Public AJAX routes for trail information
Route::get('/api/trails/{trail}', [App\Http\Controllers\Api\TrailController::class, 'show'])->name('api.trails.show');

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
Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/profile', [App\Http\Controllers\ProfileController::class, 'show'])->name('custom.profile.show');
    Route::get('/profile/edit', [App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile/picture', [App\Http\Controllers\ProfileController::class, 'deleteProfilePicture'])->name('profile.picture.delete');

    // Account Settings route
    Route::get('/account/settings', [App\Http\Controllers\AccountSettingsController::class, 'index'])->name('account.settings');

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

<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\ExploreController;
use App\Http\Controllers\LocationWeatherController;
use App\Http\Controllers\TrailController;
use App\Http\Controllers\OrganizationApprovalController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\AssessmentController;
use Illuminate\Support\Facades\Mail;
use App\Models\User;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OrganizationTrailController;



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
    
    Route::get('/hiker/itinerary/build', function () {
        return view('hiker.itinerary.build'); 
    })->name('itinerary.build');
    
    Route::post('/hiker/itinerary/generate', function () {
        return view('hiker.itinerary.generated'); 
    })->name('itinerary.generate');
    
    // Assessment routes
    Route::get('/assessment/instruction', [AssessmentController::class, 'instruction'])->name('assessment.instruction');
    Route::get('/assessment/gear', [AssessmentController::class, 'gear'])->name('assessment.gear');
    Route::get('/assessment/fitness', [AssessmentController::class, 'fitness'])->name('assessment.fitness');
    Route::get('/assessment/health', [AssessmentController::class, 'health'])->name('assessment.health');
    Route::get('/assessment/weather', [AssessmentController::class, 'weather'])->name('assessment.weather');
    Route::get('/assessment/emergency', [AssessmentController::class, 'emergency'])->name('assessment.emergency');
    Route::get('/assessment/environment', [AssessmentController::class, 'environment'])->name('assessment.environment');
    Route::get('/assessment/result', [AssessmentController::class, 'result'])->name('assessment.result');
    
    // Assessment form submissions
    Route::post('/assessment/gear', [AssessmentController::class, 'storeGear'])->name('assessment.gear.store');
    Route::post('/assessment/fitness', [AssessmentController::class, 'storeFitness'])->name('assessment.fitness.store');
    Route::post('/assessment/health', [AssessmentController::class, 'storeHealth'])->name('assessment.health.store');
    Route::post('/assessment/weather', [AssessmentController::class, 'storeWeather'])->name('assessment.weather.store');
    Route::post('/assessment/emergency', [AssessmentController::class, 'storeEmergency'])->name('assessment.emergency.store');
    Route::post('/assessment/environment', [AssessmentController::class, 'storeEnvironment'])->name('assessment.environment.store');
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
    
    // Protected routes that require approval
    // These routes will be accessible to approved organizations
})->middleware('user.type:organization');

// Public routes
Route::get('/location-weather', [LocationWeatherController::class, 'getWeather']);
Route::get('/trails', [TrailController::class, 'index'])->name('trails.index');
Route::get('/trails/{trail}', [TrailController::class, 'show'])->name('trails.show');
Route::get('/trails/search', [TrailController::class, 'search'])->name('trails.search');

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
})->name('policy.show');

Route::get('/terms', function () {
    return view('terms');
})->name('terms.show');

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










//  SOL START
// Assessment routes have been moved to the protected hiker section
// SOL END
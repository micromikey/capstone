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


Route::get('/', function () {
    return view('welcome');
});

// Email verification routes
Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();
    return redirect('/dashboard');
})->middleware(['auth', 'signed'])->name('verification.verify');

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
})->middleware('user.type:hiker');

// Routes that require authentication and approval (for organizations only)
Route::middleware(['auth:sanctum', 'check.approval'])->group(function () {
    // Organization dashboard
    Route::get('/org/dashboard', function () {
        return view('org.dashboard');
    })->name('org.dashboard');
    
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










//jas
Route::get('/hiker/booking/booking-details', function () {
    return view('hiker.booking.booking-details'); 
});

//jas
Route::get('/hiker/hiking-tools', function () {
    return view('hiker.tools.hiking-tools'); 
});




//  SOL START
Route::group(['prefix' => 'assessment', 'as' => 'assessment.'], function () {
    
    Route::get('/instruction', [AssessmentController::class, 'instruction'])->name('instruction');

    // Assessment form pages
    
    Route::get('/gear', [AssessmentController::class, 'gear'])->name('gear');
    Route::get('/fitness', [AssessmentController::class, 'fitness'])->name('fitness');
    Route::get('/health', [AssessmentController::class, 'health'])->name('health');
    Route::get('/weather', [AssessmentController::class, 'weather'])->name('weather');
    Route::get('/emergency', [AssessmentController::class, 'emergency'])->name('emergency');
    Route::get('/environment', [AssessmentController::class, 'environment'])->name('environment');
    
    // Assessment form submissions
    Route::post('/gear', [AssessmentController::class, 'storeGear'])->name('gear.store');
    Route::post('/fitness', [AssessmentController::class, 'storeFitness'])->name('fitness.store');
    Route::post('/health', [AssessmentController::class, 'storeHealth'])->name('health.store');
    Route::post('/weather', [AssessmentController::class, 'storeWeather'])->name('weather.store');
    Route::post('/emergency', [AssessmentController::class, 'storeEmergency'])->name('emergency.store');
    Route::post('/environment', [AssessmentController::class, 'storeEnvironment'])->name('environment.store');
    
    // Results page
    Route::get('/result', [AssessmentController::class, 'result'])->name('result');
});






Route::get('/hiker/booking/package-details', function () {
    return view('hiker.booking.package-details'); 
});



Route::get('/hiker/itinerary/itinerary-instructions', function () {
    return view('hiker.itinerary.itinerary-instructions'); 
});



// SOL END
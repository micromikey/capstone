<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExploreController;
use App\Http\Controllers\AssessmentController;






Route::get('/', function () {
    return view('welcome');
});

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

Route::middleware([
    'auth:sanctum',
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});

Route::fallback(function (){
    return 'This page is not found. Please try again';
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






Route::get('/booking/package-details', function () {
    return view('booking.package-details');
})->name('booking.package-details');




Route::get('/booking/booking-details', function () {
    return view('booking.booking-details'); 
});





Route::get('/itinerary/itinerary-instructions', function () {
    return view('itinerary.itinerary-instructions'); 
});




use App\Http\Controllers\BookingController;


// Booking routes
Route::group(['prefix' => 'booking', 'as' => 'bookings.'], function () {

    // Show booking form
    Route::get('/booking-details', [BookingController::class, 'create'])->name('create');

    // Handle booking submission
    Route::post('/booking-details', [BookingController::class, 'store'])->name('store');

    // Show booking confirmation page
    Route::get('/confirmation', [BookingController::class, 'confirmation'])->name('confirmation');
});


Route::post('/booking/package-details', [BookingController::class, 'packageDetails'])->name('booking.package-details');

use App\Http\Controllers\AdminController;

Route::middleware(['auth', 'is_admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'admin-booking'])->name('dashboard');

    // Bookings
    Route::get('/bookings', [AdminController::class, 'bookings'])->name('bookings');
    Route::get('/bookings/{id}', [AdminController::class, 'showBooking'])->name('bookings.show');
    Route::post('/bookings/{id}/update-status', [AdminController::class, 'updateBookingStatus'])->name('bookings.updateStatus');

    // Reports
    Route::get('/reports', [AdminController::class, 'reports'])->name('reports');

    // Users
    Route::get('/users', [AdminController::class, 'users'])->name('users');
});

Route::prefix('booking')->group(function () {
    Route::get('/admin-booking', [AdminController::class, 'bookings'])->name('admin.bookings');
});



// use App\Http\Controllers\ReportController;

// // routes/web.php
// Route::prefix('reports')->name('reports.')->group(function () {
//     Route::get('/', [ReportController::class, 'index'])->name('index');
//     Route::post('/generate', [ReportController::class, 'generate'])->name('generate');
//     Route::get('/download/{report}', [ReportController::class, 'download'])->name('download');
//     Route::post('/email', [ReportController::class, 'email'])->name('email');
//     Route::post('/offline-sync', [ReportController::class, 'offlineSync'])->name('offline-sync');
// });


use App\Http\Controllers\ReportController;

    // Report routes
    Route::prefix('reports')->name('reports.')->group(function () {
        
        // Main report page
        Route::get('/', [ReportController::class, 'index'])->name('index');
        
        // Generate report
        Route::post('/generate', [ReportController::class, 'generate'])->name('generate');
        
        // Offline sync
        Route::post('/offline-sync', [ReportController::class, 'offlineSync'])->name('offline-sync');
        
        // Email report
        Route::post('/email', [ReportController::class, 'email'])->name('email');
        
        // Download report file
        Route::get('/download/{file}', [ReportController::class, 'download'])->name('download');
        
    });


// Additional API routes if needed
Route::middleware(['auth:sanctum'])->prefix('api/reports')->group(function () {
    
    Route::get('/data/{type}', [ReportController::class, 'getReportData']);
    Route::get('/export/{type}/{format}', [ReportController::class, 'exportReport']);
    
});

// SOL END









Route::get('/', [DashboardController::class, 'index']);
//----------------USERs ROUTES----------------//
Route::middleware(['auth:sanctum', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/explore', [ExploreController::class, 'index'])->name('explore');
});

use App\Http\Controllers\LocationWeatherController;
use App\Http\Controllers\TrailController;

Route::get('/location-weather', [LocationWeatherController::class, 'getWeather']);

Route::get('/location-weather', function (Request $request) {
    if ($request->has(['lat', 'lon'])) {
        session([
            'lat' => $request->lat,
            'lon' => $request->lon,
        ]);
    }
    return response()->json(['status' => 'Location saved']);
});

Route::get('/trails', [TrailController::class, 'index'])->name('trails.index');
Route::get('/trails/{trail}', [TrailController::class, 'show'])->name('trails.show');
Route::get('/trails/search', [TrailController::class, 'search'])->name('trails.search');
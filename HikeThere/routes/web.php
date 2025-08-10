<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;

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
    
    // Assessment form pages
    Route::get('/gear', [AssessmentController::class, 'gear'])->name('gear');
    Route::get('/fitness', [AssessmentController::class, 'fitness'])->name('fitness');
    Route::get('/health', [AssessmentController::class, 'health'])->name('health');
    Route::get('/weather', [AssessmentController::class, 'weather'])->name('weather');
    Route::get('/emergency', [AssessmentController::class, 'emergency'])->name('emergency');
    
    // Assessment form submissions
    Route::post('/gear', [AssessmentController::class, 'storeGear'])->name('gear.store');
    Route::post('/fitness', [AssessmentController::class, 'storeFitness'])->name('fitness.store');
    Route::post('/health', [AssessmentController::class, 'storeHealth'])->name('health.store');
    Route::post('/weather', [AssessmentController::class, 'storeWeather'])->name('weather.store');
    Route::post('/emergency', [AssessmentController::class, 'storeEmergency'])->name('emergency.store');
    
    // Results page
    Route::get('/result', [AssessmentController::class, 'result'])->name('result');
});

// SOL END




// Dummy search route
Route::get('/search', function (Request $request) {
    return 'You searched for: ' . $request->query('query');
})->name('search');

// Example dummy controller for notification handling
Route::post('/notifications/mark-as-read', function (Request $request) {
    // Here you can add logic to mark notifications as read
    // Example: auth()->user()->unreadNotifications->markAsRead();
    return back()->with('status', 'Notifications marked as read.');
})->name('notifications.markAsRead');




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
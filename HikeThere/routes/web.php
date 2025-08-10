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


//generated Fam
Route::get('/itinerary/generated', function () {
    return view('itinerary.generated'); // Blade file: resources/views/generated-itinerary.blade.php
})->middleware('auth')->name('itinerary.generated');

Route::get('/itinerary/build', function () {
    return view('itinerary.build'); // Blade file: resources/views/generated-itinerary.blade.php
})->middleware('auth')->name('itinerary.build');






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

<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\ExploreController;
use App\Http\Controllers\LocationWeatherController;
use App\Http\Controllers\TrailController;
use App\Http\Controllers\OrganizationApprovalController;
use App\Http\Controllers\Auth\RegisteredUserController;
use Illuminate\Support\Facades\Mail;
use App\Models\User;
use App\Http\Controllers\DashboardController;


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
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
});

Route::middleware(['auth:sanctum', 'verified'])->group(function () {
    Route::get('/explore', [ExploreController::class, 'index'])->name('explore');
});

Route::get('/location-weather', [LocationWeatherController::class, 'getWeather']);

Route::get('/trails', [TrailController::class, 'index'])->name('trails.index');
Route::get('/trails/{trail}', [TrailController::class, 'show'])->name('trails.show');
Route::get('/trails/search', [TrailController::class, 'search'])->name('trails.search');

Route::middleware(['guest'])->group(function () {
    Route::get('/register/select', function () {
        return view('auth.register-select');
    })->name('register.select');

    Route::get('/register', [RegisteredUserController::class, 'create'])
        ->name('register');

    Route::post('/register', [RegisteredUserController::class, 'store']);

    Route::get('/register/organization', [RegisteredUserController::class, 'createOrganization'])
        ->name('register.organization');

    Route::post('/register/organization', [RegisteredUserController::class, 'storeOrganization']);
});

Route::get('/policy', function () {
    return view('policy');
})->name('policy.show');

Route::get('/pending-approval', function () {
    return view('auth.pending-approval');
})->name('auth.pending-approval');

// Middleware to check approval status
Route::middleware(['auth', 'check.approval'])->group(function () {
    // Protected routes that require approval
});

// Admin routes for managing approvals
Route::middleware(['auth', 'admin'])->group(function () {
    Route::post('/organizations/{user}/approve', [OrganizationApprovalController::class, 'approve'])
        ->name('organizations.approve');
    Route::post('/organizations/{user}/reject', [OrganizationApprovalController::class, 'reject'])
        ->name('organizations.reject');
});

Route::get('/admin/organization/{user}/approve', function ($userId) {
    if (!request()->hasValidSignature()) {
        abort(401, 'Invalid or expired link');
    }

    $user = User::findOrFail($userId);

    if ($user->status !== 'pending') {
        return response()->json(['message' => 'User has already been processed.'], 400);
    }

    // Approve the user
    $user->update(['status' => 'approved']);

    // Send approval email to the organization
    Mail::to($user->email)->send(new \App\Mail\OrganizationApprovalNotification($user, true));

    return response()->view('admin.organization-processed', [
        'user' => $user,
        'action' => 'approved'
    ]);
})->name('admin.organization.approve');

Route::get('/admin/organization/{user}/reject', function ($userId) {
    if (!request()->hasValidSignature()) {
        abort(401, 'Invalid or expired link');
    }

    $user = User::findOrFail($userId);

    if ($user->status !== 'pending') {
        return response()->json(['message' => 'User has already been processed.'], 400);
    }

    // Reject the user
    $user->update(['status' => 'rejected']);

    // Send rejection email to the organization
    Mail::to($user->email)->send(new \App\Mail\OrganizationApprovalNotification($user, false));

    return response()->view('admin.organization-processed', [
        'user' => $user,
        'action' => 'rejected'
    ]);
})->name('admin.organization.reject');

// Add these routes to your routes/web.php file

// Organization approval routes (signed URLs for email actions)
Route::get('/admin/organization/{user}/approve', [OrganizationApprovalController::class, 'approve'])
    ->name('admin.organization.approve')
    ->middleware('signed');

Route::get('/admin/organization/{user}/reject', [OrganizationApprovalController::class, 'reject'])
    ->name('admin.organization.reject')
    ->middleware('signed');

// Organization registration routes
Route::get('/register/organization', [RegisteredUserController::class, 'createOrganization'])
    ->name('register.organization');

Route::post('/register/organization', [RegisteredUserController::class, 'storeOrganization'])
    ->name('register.organization');

// Pending approval page
Route::get('/auth/pending-approval', function () {
    return view('auth.pending-approval');
})->name('auth.pending-approval');
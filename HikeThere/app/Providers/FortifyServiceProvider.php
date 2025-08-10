<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\ResetUserPassword;
use App\Actions\Fortify\UpdateUserPassword;
use App\Actions\Fortify\UpdateUserProfileInformation;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Laravel\Fortify\Actions\RedirectIfTwoFactorAuthenticatable;
use Laravel\Fortify\Fortify;

class FortifyServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Fortify::createUsersUsing(CreateNewUser::class);
        Fortify::updateUserProfileInformationUsing(UpdateUserProfileInformation::class);
        Fortify::updateUserPasswordsUsing(UpdateUserPassword::class);
        Fortify::resetUserPasswordsUsing(ResetUserPassword::class);
        Fortify::redirectUserForTwoFactorAuthenticationUsing(RedirectIfTwoFactorAuthenticatable::class);

        Fortify::registerView(function () {
            return view('auth.register');
        });

        // Custom redirect after login based on user type
        Fortify::redirects('login', function (Request $request) {
            if (Auth::check()) {
                $user = Auth::user();
                
                if ($user->user_type === 'organization') {
                    if ($user->approval_status === 'approved') {
                        return redirect()->route('org.dashboard');
                    } elseif ($user->approval_status === 'pending') {
                        return redirect()->route('auth.pending-approval');
                    } elseif ($user->approval_status === 'rejected') {
                        Auth::logout();
                        return redirect()->route('login')
                            ->with('error', 'Your organization registration was rejected. Please contact support for more information.');
                    }
                }
                
                // For hikers, redirect to regular dashboard
                if ($user->user_type === 'hiker') {
                    return redirect()->route('dashboard');
                }
            }
            
            // Default redirect
            return redirect()->route('dashboard');
        });

        RateLimiter::for('login', function (Request $request) {
            $throttleKey = Str::transliterate(Str::lower($request->input(Fortify::username())).'|'.$request->ip());

            return Limit::perMinute(5)->by($throttleKey);
        });

        RateLimiter::for('two-factor', function (Request $request) {
            return Limit::perMinute(5)->by($request->session()->get('login.id'));
        });
    }
}

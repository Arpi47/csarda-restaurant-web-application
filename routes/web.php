<?php

use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\Auth\SocialAuthController;
use App\Http\Controllers\Auth\UserAuthController;
use App\Http\Controllers\Auth\UserVerificationController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\UserProfileController;
use App\Http\Controllers\UserReservationController;
use App\Http\Middleware\SetLocale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/verify-registration/{token}',
    [UserVerificationController::class, 'verify']
)->name('verification.approve');
Route::get('lang/{locale}', function ($locale) {
    if (in_array($locale, ['en', 'hu', 'sr', 'sr_cyrl'])) {
        session(['locale' => $locale]);
    }
    return redirect()->back();
})->name('lang.switch');
Route::post('/theme', function (Request $request) {
    $theme = $request->theme;
    if (in_array($theme, ['light', 'dark', 'auto'])) {
        session(['theme' => $theme]);
    }
    return redirect()->back();
})->name('theme.switch');
Route::middleware(['guest', SetLocale::class])->group(function () {
    Route::get('/auth/google', [
        SocialAuthController::class,
        'redirectToGoogle',
    ])->name('google.login');
    Route::get('/auth/google/callback', [
        SocialAuthController::class,
        'handleGoogleCallback',
    ])->name('google.callback');
    Route::get('/auth/apple', [
        SocialAuthController::class,
        'redirectToApple',
    ])->name('apple.login');
    Route::get('/login', function () {
        return redirect(
            config('app.frontend_url').'/login'
        );
    })->name('login');
    Route::get('/register', function () {
        return redirect(
            config('app.frontend_url').'/register'
        );
    })->name('register');
    Route::post('/login', [
        UserAuthController::class,
        'login',
    ]);
    Route::post('/register', [
        UserAuthController::class,
        'register',
    ]);
    Route::get('/forgot-password', [
        PasswordResetController::class,
        'showLinkRequestForm'
    ])->name('password.request');
    Route::post('/forgot-password', [
        PasswordResetController::class,
        'sendResetLink'
    ])
    ->middleware('throttle:5,1')
    ->name('password.email');
    Route::get('/reset-password/{token}', [
        PasswordResetController::class,
        'showResetForm'
    ])->name('password.reset');
    Route::post('/reset-password', [
        PasswordResetController::class,
        'reset'
    ])->name('password.update');
});
Route::get('/auth/google/link', [
    SocialAuthController::class,
    'redirectToGoogleLink',
])->name('google.link');
Route::get('/auth/google/link/callback', [
    SocialAuthController::class,
    'handleGoogleLinkCallback',
])->name('google.link.callback');
Route::post('/logout', [
    UserAuthController::class,
    'logout'
])
->middleware('auth')
->name('logout');
Route::middleware([SetLocale::class])->group(function () {
    Route::get('/', function () {
        return redirect(
            config('app.frontend_url')
        );
    })->name('home');
    Route::get('/contact', function () {
        return redirect(
            config('app.frontend_url').'/contact'
        );
    })->name('contact');
    Route::get('/menu', function () {
        return redirect(
            config('app.frontend_url').'/menu'
        );
    })->name('menu.index');
});
Route::middleware(['auth', SetLocale::class])->group(function () {
    Route::get('/reservation', function () {
        return redirect(
            config('app.frontend_url').'/reservation'
        );
    })->name('reservation');
    Route::post('/foglalas', [
        ReservationController::class,
        'store'
    ])->name('reservation.store');
    Route::prefix('user')
        ->name('user.')
        ->group(function () {
            Route::get('/profile', function () {
                return redirect(
                    config('app.frontend_url').'/profile'
                );
            })
            ->name('profile.show');
            Route::get('/profile/edit', function () {
                return redirect(
                    config('app.frontend_url').'/profile/edit'
                );
            })
            ->name('profile.edit');
            Route::put('/profile', [
                UserProfileController::class,
                'update'
            ])
            ->name('profile.update');
            Route::post('/profile/google/disconnect', [
                UserProfileController::class,
                'disconnectGoogle'
            ])
            ->name('profile.google.disconnect');
            Route::post('/profile/delete-request', [
                UserProfileController::class,
                'requestDelete'
            ])
            ->name('profile.deleteRequest');
            Route::post('/profile/delete-cancel', [
                UserProfileController::class,
                'cancelDelete'
            ])
            ->name('profile.deleteCancel');
            Route::get('/reservations', function () {
                return redirect(
                    config('app.frontend_url').'/profile/reservations'
                );
            })
            ->name('reservations.index');
            Route::delete('/reservations/{reservation}', [
                UserReservationController::class,
                'destroy'
            ])
            ->name('reservations.destroy');
        });
});
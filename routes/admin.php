<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\MenuController;
use App\Http\Controllers\Admin\ReservationController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\AdminActivityController;
use App\Http\Controllers\Admin\AdminInviteController;
use App\Http\Controllers\Admin\AdminRegistrationController;
use App\Http\Controllers\Admin\GalleryController;
use App\Http\Middleware\AdminSettings;
use App\Http\Middleware\RedirectIfNotAdmin;
use App\Http\Middleware\TrackAdminActivity;
use App\Http\Middleware\EnsureSuperAdmin;

Route::post('admin/set-timezone', function (\Illuminate\Http\Request $request) {
    if ($request->timezone) {
        session(['admin_timezone' => $request->timezone]);
    }
    return response()->noContent();
})->name('admin.set-timezone');
Route::prefix('admin')
    ->name('admin.')
    ->middleware([AdminSettings::class])
    ->group(function () {
        Route::get('login', [AuthController::class, 'showLogin'])->name('login');
        Route::post('login', [AuthController::class, 'login'])->name('login.submit');
        Route::post('logout', [AuthController::class, 'logout'])->name('logout');
    });
Route::prefix('admin')
    ->name('admin.')
    ->middleware([AdminSettings::class])
    ->group(function () {
        Route::get('lang/{locale}', function ($locale) {
            if (in_array($locale, ['en', 'hu', 'sr', 'sr_cyrl'])) {
                session(['admin_locale' => $locale]);
            }
            return redirect()->back();
        })->name('lang');
    });
Route::prefix('admin')
    ->middleware([AdminSettings::class])
    ->group(function () {
        Route::get('register/{token}', [AdminRegistrationController::class, 'showRegistrationForm'])
            ->name('admin.register');
        Route::post('register/{token}', [AdminRegistrationController::class, 'register'])
            ->name('admin.register.submit');
    });
Route::prefix('admin')
    ->name('admin.')
    ->middleware([EnsureSuperAdmin::class])
    ->group(function () {
        Route::get('email-preview', function (\Illuminate\Http\Request $request) {
            $locale = $request->query('locale', 'en');
            app()->setLocale($locale);

            $invitation = (object)[
                'token' => 'preview-token',
                'expires_at' => now()->addDays(2),
                'locale' => $locale,
            ];

            $registerUrl = route('admin.register', ['token' => $invitation->token]);

            return view('emails.admin_invitation', compact('invitation', 'registerUrl'));
        })->name('admin.email.preview');
    });

Route::prefix('admin')
    ->name('admin.')
    ->middleware([
        RedirectIfNotAdmin::class,
        AdminSettings::class,
        TrackAdminActivity::class,
    ])
    ->group(function () {
        Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');
        Route::resource('menu', MenuController::class);
        Route::resource('reservations', ReservationController::class)->only(['index', 'show', 'destroy']);
        Route::post('reservations/{reservation}/status', [ReservationController::class, 'updateStatus'])
            ->name('reservations.updateStatus');
        Route::get('admins', [AdminUserController::class, 'index'])->name('admins.index');
        Route::get('admins/{admin}/edit', [AdminUserController::class, 'edit'])->name('admins.edit');
        Route::put('admins/{admin}', [AdminUserController::class, 'update'])->name('admins.update');
        Route::get('users', [AdminUserController::class, 'usersIndex'])->name('users.index');
        Route::get('users/{user}/edit', [AdminUserController::class, 'editUser'])->name('users.edit');
        Route::put('users/{user}', [AdminUserController::class, 'updateUser'])->name('users.update');
        Route::post('users/{user}/toggle-suspend', [AdminUserController::class, 'toggleUserSuspend'])
            ->name('users.toggleSuspend');
        Route::delete('users/{user}', [AdminUserController::class, 'destroyUser'])->name('users.destroy');
        Route::resource(
            'gallery',
            GalleryController::class
        )->only([
            'index',
            'store',
            'destroy'
        ]);
        Route::post(
            'gallery/reorder',
            [GalleryController::class, 'reorder']
        )->name('gallery.reorder');
        Route::middleware(EnsureSuperAdmin::class)->group(function () {
            Route::get('admins/create', [AdminUserController::class, 'create'])->name('admins.create');
            Route::post('admins', [AdminUserController::class, 'store'])->name('admins.store');
            Route::delete('admins/{admin}', [AdminUserController::class, 'destroy'])->name('admins.destroy');
            Route::post('admins/{admin}/toggle-suspend', [AdminUserController::class, 'toggleSuspend'])
                ->name('admins.toggleSuspend');
            Route::get('admins/invite', [AdminInviteController::class, 'create'])->name('admins.invite');
            Route::post('admins/invite', [AdminInviteController::class, 'store'])->name('admins.invite.store');
            Route::get('admin-activity', [AdminActivityController::class, 'index'])->name('admin.activity.index');
        });
    });

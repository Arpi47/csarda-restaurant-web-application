<?php

use App\Http\Controllers\Admin\AdminActivityController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\AdminInviteController;
use App\Http\Controllers\Admin\AdminRegistrationController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\AppDownloadController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ContactController;
use App\Http\Controllers\Admin\GalleryController;
use App\Http\Controllers\Admin\MenuController;
use App\Http\Controllers\Admin\OpeningHourController;
use App\Http\Controllers\Admin\ReservationController;
use App\Http\Controllers\Admin\SpecialOpeningHourController;
use App\Http\Middleware\AdminSettings;
use App\Http\Middleware\EnsureSuperAdmin;
use App\Http\Middleware\RedirectIfNotAdmin;
use App\Http\Middleware\TrackAdminActivity;
use Illuminate\Support\Facades\Route;

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
                session(['locale' => $locale]);
                app()->setLocale($locale);
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
            $invitation = (object) [
                'token' => 'preview-token',
                'expires_at' => now()->addDays(2),
                'locale' => $locale,
            ];
            $registerUrl = route('admin.register', [
                'token' => $invitation->token,
            ]);
            $formattedExpiresAt = \App\Mail\AdminInvitationMail::formatExpiresAt(
                \Carbon\Carbon::parse($invitation->expires_at),
                $locale
            );
            return view(
                'emails.admin_invitation',
                compact(
                    'invitation',
                    'registerUrl',
                    'formattedExpiresAt'
                )
            );
        })->name('admin.email.preview');
    });

Route::prefix('admin')
    ->name('admin.')
    ->middleware([RedirectIfNotAdmin::class, AdminSettings::class, TrackAdminActivity::class])
    ->group(function () {
        Route::get('/', [AdminController::class, 'dashboard'])
            ->name('dashboard');

        /* Menu */

        Route::resource('menu', MenuController::class);
        Route::post('menu/reorder', [MenuController::class, 'reorder'])
            ->name('menu.reorder');

        /* Categories */

        Route::post('categories/reorder', [CategoryController::class, 'reorder'])
            ->name('categories.reorder');
        Route::resource('categories', CategoryController::class)
            ->except(['show']);

        /* Contact */

        Route::get('contact', [ContactController::class, 'index'])
            ->name('contact.index');
        Route::put('contact/information', [ContactController::class, 'updateInformation'])
            ->name('contact.information.update');
        Route::post('contact/social', [ContactController::class, 'storeSocial'])
            ->name('contact.social.store');
        Route::post('contact/social/reorder', [ContactController::class, 'reorderSocial'])
            ->name('contact.social.reorder');
        Route::get('contact/social/{contactSetting}/edit', [ContactController::class, 'editSocial'])
            ->name('contact.social.edit');
        Route::put('contact/social/{contactSetting}', [ContactController::class, 'updateSocial'])
            ->name('contact.social.update');
        Route::delete('contact/social/{contactSetting}', [ContactController::class, 'destroySocial'])
            ->name('contact.social.destroy');

        /* Opening Hours */

        Route::get('opening-hours', [OpeningHourController::class, 'index'])
            ->name('opening-hours.index');
        Route::put('opening-hours/{openingHour}', [OpeningHourController::class, 'update'])
            ->name('opening-hours.update');

        /* Special Opening Hours */

        Route::post('special-opening-hours', [SpecialOpeningHourController::class, 'store'])
            ->name('special-opening-hours.store');
        Route::put('special-opening-hours/{specialOpeningHour}', [SpecialOpeningHourController::class, 'update'])
            ->name('special-opening-hours.update');
        Route::delete('special-opening-hours/{specialOpeningHour}', [SpecialOpeningHourController::class, 'destroy'])
            ->name('special-opening-hours.destroy');

        /* Reservations */

        Route::resource('reservations', ReservationController::class)
            ->only(['index', 'show', 'destroy']);
        Route::post('reservations/{reservation}/status', [ReservationController::class, 'updateStatus'])
            ->name('reservations.updateStatus');

        /* App Downloads */

        Route::get('app-downloads', [AppDownloadController::class, 'index'])
            ->name('app-downloads.index');
        Route::put('app-downloads', [AppDownloadController::class, 'update'])
            ->name('app-downloads.update');

        /* Admins */

        Route::get('admins', [AdminUserController::class, 'index'])
            ->name('admins.index');
        Route::get('admins/edit', [AdminUserController::class, 'editProfile'])
            ->name('admins.edit');
        Route::put('admins/edit', [AdminUserController::class, 'updateProfile'])
            ->name('admins.update');

        /* Users */

        Route::get('users', [AdminUserController::class, 'usersIndex'])
            ->name('users.index');
        Route::post('users/{user}/toggle-suspend', [AdminUserController::class, 'toggleUserSuspend'])
            ->name('users.toggleSuspend');
        Route::delete('users/{user}', [AdminUserController::class, 'destroyUser'])
            ->name('users.destroy');

        /* Gallery */

        Route::resource('gallery', GalleryController::class)
            ->only(['index', 'store', 'destroy']);
        Route::post('gallery/reorder', [GalleryController::class, 'reorder'])
            ->name('gallery.reorder');

        /* Super Admin */

        Route::middleware(EnsureSuperAdmin::class)->group(function () {
            Route::delete('admins/{admin}', [AdminUserController::class, 'destroy'])
                ->name('admins.destroy');
            Route::post('admins/{admin}/toggle-suspend', [AdminUserController::class, 'toggleSuspend'])
                ->name('admins.toggleSuspend');
            Route::get('admins/invite', [AdminInviteController::class, 'create'])
                ->name('admins.invite');
            Route::post('admins/invite', [AdminInviteController::class, 'store'])
                ->name('admins.invite.store');
            Route::get('admin-activity', [AdminActivityController::class, 'index'])
                ->name('admin.activity.index');
        });
    });

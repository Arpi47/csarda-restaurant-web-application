<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\GalleryController;
use App\Http\Controllers\Api\MenuController;
use App\Http\Controllers\Api\VerificationController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\UserProfileController;
use App\Models\AppDownload;
use App\Models\ContactInformation;
use App\Models\ContactSetting;
use App\Models\OpeningHour;
use App\Models\Reservation;
use App\Models\SpecialOpeningHour;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/categories', [
    CategoryController::class,
    'index',
]);
Route::get('/menu', [
    MenuController::class,
    'index',
]);
Route::get('/gallery', [
    GalleryController::class,
    'index',
]);
Route::get('/contact', function () {
    return response()->json([
        'information' => ContactInformation::first([
            'phone',
            'email',
        ]),
        'socialLinks' => ContactSetting::where('is_active', true)
            ->orderBy('sort_order')
            ->get([
                'platform',
                'url',
                'sort_order',
            ]),
    ]);
});
Route::get('/opening-hours', function () {
    return response()->json([
        'weekly' => OpeningHour::orderBy('day_of_week')
            ->get([
                'day_of_week',
                'is_active',
                'open_time',
                'close_time',
            ]),
        'special' => SpecialOpeningHour::orderBy('date')
            ->get([
                'date',
                'is_active',
                'open_time',
                'close_time',
            ]),
    ]);
});
Route::get('/reservation-event-types', function () {
    return response()->json(
        \App\Models\ReservationEventType::where('is_active', true)
            ->orderBy('sort_order')
            ->get([
                'id',
                'name_en',
                'name_hu',
                'name_sr',
                'name_sr_cyrl',
            ])
    );
});
Route::get('/app-downloads', function () {
    return response()->json(
        AppDownload::whereIn('platform', [
            'google_play',
            'app_store',
        ])
            ->get([
                'platform',
                'url',
            ])
            ->keyBy('platform')
    );
});
Route::post('/contact', [
    ContactController::class,
    'send',
]);
Route::get(
    '/verify-email/{token}',
    [
        VerificationController::class,
        'verify',
    ]
);
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function () {
        return response()->json(
            Auth::user()
        );
    });
    Route::post('/logout', [
        AuthController::class,
        'logout',
    ]);
    Route::post('/reservation', [
        ReservationController::class,
        'store',
    ]);
    Route::get('/profile', [
        UserProfileController::class,
        'show',
    ]);
    Route::put('/profile', [
        UserProfileController::class,
        'update',
    ]);
    Route::post('/profile/google/disconnect', [
        UserProfileController::class,
        'disconnectGoogle',
    ]);
    Route::post('/profile/delete-request', [
        UserProfileController::class,
        'requestDelete',
    ]);
    Route::post('/profile/delete-cancel', [
        UserProfileController::class,
        'cancelDelete',
    ]);
    Route::get('/reservations', function () {
        return Reservation::where(
            'user_id',
            Auth::id()
        )
            ->orderBy(
                'date_time',
                'asc'
            )
            ->get();
    });
    Route::delete('/reservations/{reservation}', function (
        Reservation $reservation
    ) {
        if ($reservation->user_id !== Auth::id()) {
            abort(403);
        }
        $reservation->delete();

        return response()->json([
            'success' => true,
        ]);
    });
});
Route::post('/login', [
    AuthController::class,
    'login',
]);
Route::post('/register', [
    AuthController::class,
    'register',
]);
Route::post(
    '/forgot-password',
    [
        PasswordResetController::class,
        'sendResetLink',
    ]
);
Route::post(
    '/reset-password',
    [
        PasswordResetController::class,
        'reset',
    ]
);

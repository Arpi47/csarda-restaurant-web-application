<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;

class UserProfileController extends Controller
{
    public function show()
    {
        $user = Auth::user();

        return response()->json([
            ...$user->toArray(),
            'can_change_password' => $user->canChangePassword(),
            'google_connected' => $user->socialAccounts()
                ->where('provider', 'google')
                ->exists(),
            'deletion_requested' => $user->deletion_requested,
        ]);
    }

    public function disconnectGoogle()
    {
        $user = Auth::user();
        $socialAccount = $user->socialAccounts()
            ->where('provider', 'google')
            ->first();

        if (! $socialAccount) {
            return response()->json([
                'success' => false,
                'message' => __('messages.google_not_connected'),
            ], 422);
        }
        if (! $user->canChangePassword()) {
            return response()->json([
                'success' => false,
                'message' => __('messages.google_disconnect_password_required'),
            ], 422);
        }

        $socialAccount->delete();

        return response()->json([
            'success' => true,
            'message' => __('messages.google_disconnected'),
        ]);
    }

    public function edit()
    {
        $user = Auth::user();

        return view(
            'user.edit',
            compact('user')
        );
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        $recaptcha = $request->input('g-recaptcha-response');
        if (! $recaptcha) {
            return response()->json([
                'message' => __('messages.recaptcha_required'),
            ], 422);
        }
        $response = Http::asForm()->post(
            'https://www.google.com/recaptcha/api/siteverify',
            [
                'secret' => config('app.recaptcha_secret_key'),
                'response' => $recaptcha,
                'remoteip' => $request->ip(),
            ]
        );
        $result = $response->json();
        $success =
            Arr::get(
                $result,
                'success',
                false
            );
        $score =
            Arr::get(
                $result,
                'score',
                0
            );
        if (! $success) {
            return response()->json([
                'message' => __('messages.recaptcha_failed'),
            ], 422);
        }
        if ($score < 0.5) {
            return response()->json([
                'message' => __('messages.invalid_captcha'),
            ], 422);
        }
        $request->validate([
            'first_name' => [
                'sometimes',
                'string',
                'max:50',
            ],
            'last_name' => [
                'sometimes',
                'string',
                'max:50',
            ],
            'password' => [
                'nullable',
                'confirmed',
                'min:8',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).+$/',
            ],
        ]);
        if ($request->filled('first_name')) {
            $user->first_name =
                $request->first_name;
        }
        if ($request->filled('last_name')) {
            $user->last_name =
                $request->last_name;
        }
        if ($request->filled('password')) {
            if (
                $user->password &&
                Hash::check(
                    $request->password,
                    $user->password
                )
            ) {
                return response()->json([
                    'message' => __('messages.same_password'),
                ], 422);
            }
            $user->password =
                Hash::make(
                    $request->password
                );
        }
        $user->save();
        $userData = [
            ...$user->toArray(),
            'can_change_password' => $user->canChangePassword(),
            'google_connected' => $user->socialAccounts()
                ->where('provider', 'google')
                ->exists(),
            'deletion_requested' => $user->deletion_requested,
        ];

        return response()->json([
            'message' => __('messages.profile_updated'),
            'user' => $userData,
        ]);
    }

    public function requestDelete(Request $request)
    {
        $user = Auth::user();
        $now = now();
        if (
            $user->deletion_requested_at &&
            $user->deletion_requested_at
                ->lt(
                    $now->copy()->subDay()
                )
        ) {
            $user->deletion_attempts_last_24h = 0;
        }
        if (
            $user->deletion_attempts_last_24h >= 2
        ) {
            return response()->json([
                'success' => false,
                'too_many_attempts' => true,
                'message' => __('messages.too_many_attempts'),
            ]);
        }
        $user->deletion_attempts_last_24h++;
        $user->deletion_requested = true;
        $user->deletion_requested_at = $now;
        $user->deletion_will_be_final_at = $now->copy()->addDays(30);
        $user->save();

        return response()->json([
            'success' => true,
            'too_many_attempts' => false,
            'message' => __('messages.deletion_requested'),
        ]);
    }

    public function cancelDelete(Request $request)
    {
        $user = Auth::user();
        if (! $user->deletion_requested) {
            return response()->json([
                'success' => false,
                'message' => __('messages.no_deletion_request'),
            ]);
        }
        $user->deletion_requested = false;
        $user->deletion_requested_at = null;
        $user->deletion_will_be_final_at = null;
        $user->save();

        return response()->json([
            'success' => true,
            'message' => __('messages.deletion_cancelled'),
        ]);
    }
}

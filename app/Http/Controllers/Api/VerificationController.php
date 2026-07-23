<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PendingUser;
use App\Models\User;

class VerificationController extends Controller
{
    public function verify($token)
    {
        $pendingUser = PendingUser::where(
            'verification_token',
            $token
        )->first();

        if (! $pendingUser) {

            return response()->json([
                'message' => __('messages.invalid_verification_token'),
            ], 404);
        }

        if (
            $pendingUser->expires_at &&
            now()->greaterThan(
                $pendingUser->expires_at
            )
        ) {
            $pendingUser->delete();

            return response()->json([
                'message' => __('messages.verification_expired'),
            ], 422);

        }

        if (
            User::where(
                'email',
                $pendingUser->email
            )->exists()
        ) {
            $pendingUser->delete();

            return response()->json([
                'message' => __('messages.email_already_verified'),
            ], 422);
        }

        $user = User::create([
            'first_name' => $pendingUser->first_name,
            'last_name' => $pendingUser->last_name,
            'email' => $pendingUser->email,
            'password' => $pendingUser->password,
            'email_verified_at' => now(),
        ]);

        $pendingUser->delete();

        return redirect(
            config('app.frontend_url').'/verification-success'
        );

    }
}

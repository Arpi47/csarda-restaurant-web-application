<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\VerifyRegistrationMail;
use App\Models\PendingUser;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => [
                'required',
                'email',
            ],
            'password' => [
                'required',
            ],
        ]);

        if (
            PendingUser::where(
                'email',
                $request->email
            )->exists()
        ) {
            return response()->json([
                'message' => __('messages.email_not_verified'),
            ], 422);
        }

        $user = User::where(
            'email',
            $request->email
        )->first();

        if (
            $user &&
            $user->is_suspended
        ) {
            return response()->json([
                'message' => __('messages.account_suspended'),
            ], 403);
        }

        $user = User::where(
            'email',
            $request->email
        )->first();

        if (
            ! $user ||
            is_null($user->password) ||
            ! Hash::check(
                $request->password,
                $user->password
            )
        ) {
            return response()->json([
                'message' => __('messages.invalid_credentials'),
            ], 422);
        }
        $token = $user
            ->createToken('login')
            ->plainTextToken;

        return response()->json([
            'message' => __('messages.login_success'),
            'token' => $token,
            'user' => $user,
        ]);
    }

    public function register(Request $request)
    {
        $recaptcha = $request->input('recaptcha_token');

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
        if (
            ! ($result['success'] ?? false)
            ||
            ($result['score'] ?? 0) < 0.5
        ) {
            return response()->json([
                'message' => __('messages.invalid_captcha'),
            ], 422);
        }

        $data = $request->validate([
            'first_name' => [
                'required',
                'string',
                'max:50',
            ],
            'last_name' => [
                'required',
                'string',
                'max:50',
            ],
            'email' => [
                'required',
                'email',
                'unique:users,email',

                function ($attribute, $value, $fail) {

                    $blockedDomains = config('email.blocked_domains');
                    $domain = strtolower(
                        substr(
                            strrchr($value, '@'),
                            1
                        )
                    );
                    if (
                        in_array(
                            $domain,
                            $blockedDomains,
                            true
                        )
                    ) {
                        $fail(__('messages.temporary_email_not_allowed'));
                    }
                },
            ],
            'password' => [
                'required',
                'string',
                'confirmed',
                'min:8',
                "regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).+$/",
            ],
        ], [
            'password.regex' => __('messages.password_requirements'),
            'password.min' => __('messages.password_min'),
            'password.confirmed' => __('messages.password_confirmation_required'),
        ]);

        PendingUser::where(
            'email',
            $request->email
        )->delete();

        $token = Str::random(64);

        $pendingUser = PendingUser::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'language' => match ($request->input('language')) {
                'sr_cyrl' => 'sr_cyrl',
                'sr_lat' => 'sr_lat',
                'hu' => 'hu',
                'en' => 'en',
                default => 'en',
            },
            'verification_token' => $token,
            'expires_at' => Carbon::now()->addHours(24),
        ]);

        $url =
            url('/api/verify-email/'.$token);

        Mail::to(
            $pendingUser->email
        )->send(
            new VerifyRegistrationMail(
                $pendingUser,
                $url
            )
        );

        return response()->json([
            'message' => __('messages.registration_successful'),
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()
            ?->tokens()
            ->delete();

        return response()->json([
            'message' => __('messages.logout_success'),
        ]);
    }
}

<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\VerifyRegistrationMail;
use App\Models\PendingUser;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class UserAuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $recaptcha = $request->input('g-recaptcha-response');
        if (! $recaptcha) {
            return back()
                ->withErrors(['captcha' => __('messages.recaptcha_required')])
                ->withInput();
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
        if (! ($result['success'] ?? false) || ($result['score'] ?? 0) < 0.5) {
            return back()
                ->withErrors(['captcha' => __('messages.invalid_captcha')])
                ->withInput();
        }

        $request->validate([
            'first_name' => [
                'required',
                'string',
                'max:50',
                function ($attribute, $value, $fail) {
                    if (filter_var($value, FILTER_VALIDATE_EMAIL)) {
                        $fail(__('messages.username_cannot_be_email'));
                    }
                },
            ],

            'last_name' => [
                'required',
                'string',
                'max:50',
                function ($attribute, $value, $fail) {
                    if (filter_var($value, FILTER_VALIDATE_EMAIL)) {
                        $fail(__('messages.username_cannot_be_email'));
                    }
                },
            ],

            'email' => [
                'required',
                'email',
                'unique:users,email',
                'unique:pending_users,email',

                function ($attribute, $value, $fail) {
                    $blockedDomains = config('email.blocked_domains', []);

                    $domain = strtolower(
                        substr(
                            strrchr($value, '@'),
                            1
                        )
                    );

                    if (in_array($domain, $blockedDomains, true)) {
                        $fail(
                            __('messages.temporary_email_not_allowed')
                        );
                    }
                },
            ],
            'password' => [
                'required',
                'string',
                'confirmed',
                'min:8',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).+$/',
            ],
            'avatar' => 'nullable|image|max:2048',
        ], [
            'password.regex' => __('messages.password_requirements'),
            'password.min' => __('messages.password_min'),
            'password.confirmed' => __('messages.password_confirmation_required'),
        ]);

        PendingUser::where('email', $request->email)->delete();

        $avatarPath = null;
        if ($request->hasFile('avatar')) {

            $avatarPath = $request->file('avatar')
                ->store('users/avatars', 'public');
        }

        $token = Str::random(64);

        $pendingUser = PendingUser::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'profile_image' => $avatarPath,
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
        $verificationUrl = route('verification.approve', $token);

        Mail::to($request->email)
            ->send(new VerifyRegistrationMail($pendingUser, $verificationUrl));

        return view('auth.check-email', [
            'email' => $request->email,
        ]);
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (PendingUser::where('email', $request->email)->exists()) {

            return back()->withErrors([
                'email' => __('messages.email_not_verified'),
            ]);
        }
        $user = User::where('email', $request->email)->first();

        if ($user && $user->is_suspended) {

            return back()->withErrors([
                'email' => __('messages.account_suspended'),
            ]);
        }
        if (Auth::attempt($credentials, $request->boolean('remember'))) {

            $request->session()->regenerate();

            return redirect('/');
        }

        return back()->withErrors([
            'email' => __('messages.invalid_credentials'),
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}

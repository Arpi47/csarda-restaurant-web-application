<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\AdminActivityLogger;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('admin.auth.login');
    }

    public function login(Request $request)
    {
        $recaptcha = $request->input('g-recaptcha-response');

        if (! $recaptcha) {
            return back()->withErrors(['captcha' => __('messages.recaptcha_required')])->withInput();
        }

        $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
            'secret' => config('app.recaptcha_secret_key'),
            'response' => $recaptcha,
            'remoteip' => $request->ip(),
        ]);

        $result = $response->json();
        $success = \Illuminate\Support\Arr::get($result, 'success', false);
        $score = \Illuminate\Support\Arr::get($result, 'score', 0);

        if (! $success) {
            return back()->withErrors(['captcha' => __('messages.recaptcha_failed')])->withInput();
        }

        if ($score < 0.5) {
            return back()->withErrors(['captcha' => __('messages.invalid_captcha')])->withInput();
        }

        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if (Auth::guard('admin')->attempt($credentials)) {
            $request->session()->regenerate();

            $admin = Auth::guard('admin')->user();

            if ($admin->is_suspended) {
                Auth::guard('admin')->logout();

                return back()->with('suspended', __('messages.account_suspended'));
            }

            AdminActivityLogger::login($admin);

            $admin->update([
                'last_login_at' => now(),
                'session_started_at' => now(),
            ]);

            return redirect()->route('admin.dashboard');
        }

        return back()->withErrors([
            'email' => __('messages.invalid_credentials'),
        ]);
    }

    public function logout(Request $request)
    {
        $admin = Auth::guard('admin')->user();

        if ($admin) {
            AdminActivityLogger::logout($admin);
        }

        Auth::guard('admin')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }
}

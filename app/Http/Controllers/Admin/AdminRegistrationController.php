<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\AdminInvitation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Arr;
use App\Helpers\AdminActivityLogger;

class AdminRegistrationController extends Controller
{
    public function showRegistrationForm(string $token)
    {
        $invitation = AdminInvitation::where('token', $token)
            ->where('used', false)
            ->where('expires_at', '>', now())
            ->firstOrFail();

        App::setLocale($invitation->locale);

        return view('admin.register', compact('invitation'));
    }

    public function register(Request $request, string $token)
    {
        return DB::transaction(function () use ($request, $token) {

            $invitation = AdminInvitation::lockForUpdate()
                ->where('token', $token)
                ->where('used', false)
                ->where('expires_at', '>', now())
                ->firstOrFail();

            App::setLocale($invitation->locale);

            $recaptcha = $request->input('g-recaptcha-response');
            if (!$recaptcha) {
                return back()->withErrors(['captcha' => __('messages.recaptcha_required')])->withInput();
            }

            $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
                'secret' => config('app.recaptcha_secret_key'),
                'response' => $recaptcha,
                'remoteip' => $request->ip(),
            ]);

            $result = $response->json();
            $success = Arr::get($result, 'success', false);
            $score   = Arr::get($result, 'score', 0);

            if (!$success) {
                return back()->withErrors(['captcha' => __('messages.recaptcha_failed')])->withInput();
            }

            if ($score < 0.5) { // bot-ellenőrzés threshold
                return back()->withErrors(['captcha' => __('messages.invalid_captcha')])->withInput();
            }

            $request->validate([
                'name' => [
                    'required',
                    'string',
                    'max:255',
                    function ($attribute, $value, $fail) {
                        if (filter_var($value, FILTER_VALIDATE_EMAIL)) {
                            $fail(__('messages.username_cannot_be_email'));
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
            ], [
                'password.regex' => __('messages.password_requirements'),
                'password.min' => __('messages.password_min'),
                'password.confirmed' => __('messages.password_confirmation_required'),
            ]);

            if (Admin::where('email', $invitation->email)->exists()) {
                abort(409, 'Admin already exists.');
            }

            $admin = Admin::create([
                'name'     => $request->name,
                'email'    => $invitation->email,
                'password' => Hash::make($request->password),
                'status'   => 'pending',
            ]);

            $invitation->update(['used' => true]);

            AdminActivityLogger::registeredViaInvitation($admin);

            return redirect()
                ->route('admin.login')
                ->with('success', __('messages.registration_pending'));
        });
    }
}
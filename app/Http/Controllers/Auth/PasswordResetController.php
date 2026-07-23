<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password as PasswordRule;

class PasswordResetController extends Controller
{
    public function showLinkRequestForm(Request $request)
    {
        $a = rand(1, 9);
        $b = rand(1, 9);
        $request->session()->put('captcha_question', "$a + $b = ?");
        $request->session()->put('captcha_answer', $a + $b);

        return view('auth.forgot-password', [
            'captcha_question' => $request->session()->get('captcha_question'),
        ]);
    }

    public function sendResetLink(Request $request)
    {
        $request->validate([
            'email' => [
                'required',
                'email',
            ],
            'recaptcha_token' => [
                'required',
            ],
        ]);

        if (
            ! $this->verifyRecaptcha(
                $request->input('recaptcha_token')
            )
        ) {
            return response()->json([
                'message' => 'invalid_captcha',
            ], 422);
        }

        $status = Password::sendResetLink(
            $request->only('email')
        );

        if ($status === Password::RESET_LINK_SENT) {

            return response()->json([
                'message' => 'password_reset_sent',
            ]);
        }

        return response()->json([
            'message' => 'password_reset_failed',
        ], 422);
    }

    private function verifyRecaptcha($token)
    {
        $response = file_get_contents(
            'https://www.google.com/recaptcha/api/siteverify?secret='
            .config('services.recaptcha.secret_key')
            .'&response='.$token
        );

        $response = json_decode($response);

        return
            isset($response->success)
            &&
            $response->success
            &&
            isset($response->score)
            &&
            $response->score >= 0.5;
    }

    public function showResetForm(Request $request, $token)
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->email,
        ]);
    }

    public function reset(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => [
                'required',
                'confirmed',
                PasswordRule::min(8)
                    ->mixedCase()
                    ->numbers()
                    ->symbols(),
            ],
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->password = Hash::make($password);
                $user->setRememberToken(Str::random(60));
                $user->save();
            }
        );

        if ($status === Password::PASSWORD_RESET) {

            return response()->json([
                'message' => __('messages.password_reset_success'),
            ]);
        }

        return response()->json([
            'message' => __('messages.reset_failed'),
        ], 422);
    }
}

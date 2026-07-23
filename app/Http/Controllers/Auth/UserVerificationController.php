<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\PendingUser;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class UserVerificationController extends Controller
{
    public function verify($token)
    {
        $pending = PendingUser::where('verification_token', $token)->first();
        if (! $pending) {

            return redirect('/')
                ->with('success', __('messages.account_verified'));

        }
        if ($pending->expires_at && $pending->expires_at->isPast()) {
            return redirect()->route('register')
                ->withErrors(__('messages.token_expired'));
        }
        $user = User::create([
            'first_name' => $pending->first_name,
            'last_name' => $pending->last_name,
            'email' => $pending->email,
            'password' => $pending->password,
            'language' => $pending->language,
            'email_verified_at' => now(),
        ]);
        $pending->delete();
        Auth::login($user);

        return redirect('/')
            ->with('success', __('messages.account_verified'));
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\AdminActivityLogger;
use App\Http\Controllers\Controller;
use App\Mail\AdminInvitationMail;
use App\Models\AdminInvitation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class AdminInviteController extends Controller
{
    public function create(Request $request)
    {
        $locale = $request->query('locale', app()->getLocale()); // alapértelmezett a jelenlegi admin nyelve
        app()->setLocale($locale);

        $invitationPreview = (object) [
            'token' => 'preview-token',
            'expires_at' => now()->addDays(2),
            'locale' => $locale,
        ];
        $registerUrl = route('admin.register', ['token' => $invitationPreview->token]);

        return view('admin.admins.invite', compact('invitationPreview', 'registerUrl', 'locale'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'email' => 'required|email|unique:admins,email',
            'locale' => 'nullable|string|in:en,hu,sr,sr_cyrl',
        ]);

        AdminInvitation::where('email', $request->email)
            ->where('used', false)
            ->delete();

        $locale = $request->input('locale') ?? 'en';

        $invitation = AdminInvitation::create([
            'email' => $request->email,
            'token' => Str::random(40),
            'used' => false,
            'expires_at' => now()->addDays(2),
            'locale' => $locale,
        ]);

        Mail::to($invitation->email)
            ->queue(new AdminInvitationMail($invitation));

        AdminActivityLogger::log(
            'admin_invitation_sent',
            auth('admin')->user(),
            $invitation,
            ['email' => $invitation->email, 'locale' => $locale]
        );

        return redirect()
            ->back()
            ->with('success', __('messages.admin_invitation_sent'));
    }
}

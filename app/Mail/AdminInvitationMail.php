<?php

namespace App\Mail;

use App\Models\AdminInvitation;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AdminInvitationMail extends Mailable
{
    use Queueable, SerializesModels;

    public AdminInvitation $invitation;

    public string $formattedExpiresAt;

    public function __construct(AdminInvitation $invitation)
    {
        $this->invitation = $invitation;
        $this->formattedExpiresAt = self::formatExpiresAt(
            $invitation->expires_at,
            $invitation->locale ?? 'en'
        );
    }

    public static function formatExpiresAt(
        Carbon $expiresAt,
        string $locale
    ): string {
        $carbonLocale = match ($locale) {
            'sr_cyrl' => 'sr_Cyrl',
            'sr' => 'sr',
            default => $locale,
        };
        $date = $expiresAt->copy()->locale($carbonLocale);
        return match ($locale) {
            'hu' => $date->translatedFormat('Y. F j. H:i'),
            'en' => $date->translatedFormat('F j, Y H:i'),
            'sr', 'sr_cyrl' => $date->translatedFormat('j. F Y. H:i'),
            default => $date->translatedFormat('Y-m-d H:i'),
        };
    }

    public function build()
    {
        app()->setLocale($this->invitation->locale);
        return $this
            ->subject(__('messages.admin_invitation_subject'))
            ->view('emails.admin_invitation')
            ->with([
                'invitation' => $this->invitation,
                'registerUrl' => route(
                    'admin.register',
                    $this->invitation->token
                ),
                'formattedExpiresAt' => $this->formattedExpiresAt,
            ]);
    }
}

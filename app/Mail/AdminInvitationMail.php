<?php

namespace App\Mail;

use App\Models\AdminInvitation;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AdminInvitationMail extends Mailable
{
    use Queueable, SerializesModels;

    public AdminInvitation $invitation;

    public function __construct(AdminInvitation $invitation)
    {
        $this->invitation = $invitation;
    }

    public function build()
    {
        app()->setLocale($this->invitation->locale);

        return $this
            ->subject(__('messages.admin_invitation_subject'))
            ->view('emails.admin_invitation')
            ->with([
                'invitation' => $this->invitation,
                'registerUrl' => route('admin.register', $this->invitation->token),
            ]);
    }
}

<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;

class VerifyRegistrationMail extends Mailable
{
    public $user;

    public $url;

    public function __construct($user, $url)
    {
        $this->user = $user;
        $this->url = $url;
    }

    public function build()
    {
        app()->setLocale(
            $this->user->language ?? 'en'
        );

        return $this
            ->subject(
                __('messages.verify_email_subject')
            )
            ->view(
                'emails.verify_registration'
            );
    }
}

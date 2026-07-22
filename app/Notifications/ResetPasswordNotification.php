<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResetPasswordNotification extends Notification
{

    use Queueable;

    public function __construct(
        public string $token
    )
    {

    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $url =
            config('app.frontend_url')
            .
            '/reset-password/'
            .
            $this->token
            .
            '?email='
            .
            urlencode($notifiable->email);

        return (new MailMessage)

            ->subject(
                __('messages.password_reset_subject')
            )

            ->view(
                'emails.reset_password',
                [
                    'user'=>$notifiable,
                    'url'=>$url
                ]
            );
    }
}
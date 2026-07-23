<?php

namespace App\Mail;

use App\Models\Reservation;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ReservationStatusMail extends Mailable
{
    use Queueable, SerializesModels;

    public Reservation $reservation;

    public string $formattedDate;

    public string $formattedTime;

    public function __construct(Reservation $reservation)
    {
        $this->reservation = $reservation;

        $locale = $reservation->language ?? 'en';
        $carbonLocale = match ($locale) {
            'sr_cyrl' => 'sr_Cyrl',
            'sr_lat' => 'sr',
            default => $locale,
        };

        $date = Carbon::parse($reservation->date_time)
            ->locale($carbonLocale);

        switch ($locale) {
            case 'hu':
                $this->formattedDate =
                    $date->translatedFormat('Y. F j.');
                break;
            case 'en':
                $this->formattedDate =
                    $date->translatedFormat('F j, Y');
                break;
            case 'sr':
            case 'sr_lat':
            case 'sr_cyrl':
                $this->formattedDate =
                    $date->translatedFormat('j. F Y.');
                break;
            default:
                $this->formattedDate =
                    $date->translatedFormat('j F Y');
                break;
        }
        $this->formattedTime =
            $date->format('H:i');

    }

    public function build()
    {
        $locale = $this->reservation->language ?? 'en';

        app()->setLocale($locale);

        return $this
            ->subject(
                $this->reservation->status === 'approved'
                    ? __('messages.reservation_approved_subject')
                    : __('messages.reservation_rejected_subject')
            )
            ->view('emails.reservation_status');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\OpeningHour;
use App\Models\Reservation;
use App\Models\SpecialOpeningHour;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

class ReservationController extends Controller
{
    public function store(Request $request)
    {
        $user = Auth::user();
        if (! $user) {
            return response()->json([
                'success' => false,
                'message' => __('messages.login_required'),
            ], 401);
        }
        $recaptcha = $request->input('g-recaptcha-response');
        if (! $recaptcha) {
            return response()->json([
                'success' => false,
                'message' => __('messages.recaptcha_required'),
            ]);
        }
        $response = Http::asForm()->post(
            'https://www.google.com/recaptcha/api/siteverify',
            [
                'secret' => config('app.recaptcha_secret_key'),
                'response' => $recaptcha,
                'remoteip' => $request->ip(),
            ]
        );
        $result = $response->json();
        $captchaSuccess = Arr::get(
            $result,
            'success',
            false
        );
        $score = Arr::get(
            $result,
            'score',
            0
        );
        if (! $captchaSuccess) {
            return response()->json([
                'success' => false,
                'message' => __('messages.recaptcha_failed'),
            ]);
        }
        if ($score < 0.5) {
            return response()->json([
                'success' => false,
                'message' => __('messages.invalid_captcha'),
            ]);
        }
        $minimumReservationDate = now()->addDays(2)->format('Y-m-d');
        $validator = Validator::make(
            $request->all(),
            [
                'first_name' => 'required|string|max:100',
                'last_name' => 'required|string|max:100',
                'email' => 'required|email|max:150',
                'date' => 'required|date|after_or_equal:'.
                    $minimumReservationDate,
                'time' => 'required|date_format:H:i',
                'guests' => 'required|integer|min:1|max:70',
            ],
            [
                'first_name.required' => __('messages.first_name_required'),
                'last_name.required' => __('messages.last_name_required'),
                'email.required' => __('messages.email_required'),
                'email.email' => __('messages.email_invalid'),
                'date.required' => __('messages.date_required'),
                'date.after_or_equal' => __('messages.date_too_soon', [
                    'date' => $minimumReservationDate,
                ]),
                'time.required' => __('messages.time_required'),
                'time.date_format' => __('messages.time_invalid'),
                'guests.required' => __('messages.guests_required'),
                'guests.integer' => __('messages.guests_invalid'),
                'guests.min' => __('messages.guests_min'),
                'guests.max' => __('messages.guests_max'),
            ]
        );
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => implode(
                    ' ',
                    $validator->errors()->all()
                ),
            ]);
        }
        $data = $validator->validated();
        if (! $this->isValidEmail($data['email'])) {
            return response()->json([
                'success' => false,
                'message' => __('messages.email_temporary_not_allowed'),
            ]);
        }
        $existing = Reservation::where('user_id', $user->id)
            ->whereDate(
                'date_time',
                $data['date']
            )
            ->first();
        if ($existing) {
            return response()->json([
                'success' => false,
                'message' => __('messages.reservation_already_exists'),
            ]);
        }
        $date = Carbon::parse($data['date']);
        $specialOpeningHour = SpecialOpeningHour::whereDate(
            'date',
            $date->toDateString()
        )->first();
        if ($specialOpeningHour) {
            $openingHour = $specialOpeningHour;
        } else {
            $openingHour = OpeningHour::where(
                'day_of_week',
                $date->dayOfWeekIso
            )->first();
        }
        if (
            ! $openingHour ||
            ! $openingHour->is_active
        ) {
            return response()->json([
                'success' => false,
                'message' => __('messages.restaurant_closed', [
                    'day' => $date->translatedFormat('l'),
                ]),
            ]);
        }
        if (
            ! $openingHour->open_time ||
            ! $openingHour->close_time ||
            ! $openingHour->last_reservation_time
        ) {
            return response()->json([
                'success' => false,
                'message' => __('messages.reservation_time_not_configured'),
            ]);
        }
        $openTime = Carbon::parse(
            $openingHour->open_time
        )->format('H:i');
        $closeTime = Carbon::parse(
            $openingHour->close_time
        )->format('H:i');
        $lastReservationTime = Carbon::parse(
            $openingHour->last_reservation_time
        )->format('H:i');
        if ($openTime >= $closeTime) {
            return response()->json([
                'success' => false,
                'message' => __('messages.invalid_opening_hours'),
            ]);
        }
        if (
            $lastReservationTime < $openTime ||
            $lastReservationTime >= $closeTime
        ) {
            return response()->json([
                'success' => false,
                'message' => __('messages.invalid_last_reservation_time'),
            ]);
        }
        if (
            $data['time'] < $openTime ||
            $data['time'] > $lastReservationTime
        ) {
            return response()->json([
                'success' => false,
                'message' => __('messages.time_out_of_hours', [
                    'open' => $openTime,
                    'close' => $lastReservationTime,
                ]),
            ]);
        }
        Reservation::create([
            'user_id' => $user->id,
            'fname' => $data['first_name'],
            'lname' => $data['last_name'],
            'email' => $data['email'],
            'date_time' => $data['date'].' '.$data['time'],
            'guests' => $data['guests'],
            'status' => 'pending',
            'language' => match (
                $request->header('Accept-Language')
            ) {
                'sr_cyrl' => 'sr_cyrl',
                'sr_lat' => 'sr_lat',
                'hu' => 'hu',
                'en' => 'en',
                default => 'en',
            },
        ]);

        return response()->json([
            'success' => true,
        ]);
    }

    private function isValidEmail($email)
    {
        $domain =
            substr(
                strrchr($email, '@'),
                1
            );
        $blockedDomains = config(
            'email.blocked_domains'
        );

        return ! in_array(
            $domain,
            $blockedDomains,
            true
        );
    }
}

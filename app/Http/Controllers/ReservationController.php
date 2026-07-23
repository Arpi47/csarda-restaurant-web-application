<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
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
        $validator = Validator::make(
            $request->all(),
            [
                'first_name' => 'required|string|max:100',
                'last_name' => 'required|string|max:100',
                'email' => 'required|email|max:150',
                'date' => 'required|date|after_or_equal:'.
                    now()->addDays(2)->format('Y-m-d'),
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
                    'date' => now()->addDays(2)->format('Y-m-d'),
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
        $openingHours = [
            'Tuesday' => ['11:00', '22:00'],
            'Wednesday' => ['11:00', '22:00'],
            'Thursday' => ['11:00', '22:00'],
            'Friday' => ['11:00', '23:00'],
            'Saturday' => ['11:00', '23:00'],
            'Sunday' => ['11:00', '21:00'],
            'Monday' => null,
        ];
        $dayName =
            Carbon::parse($data['date'])
                ->format('l');
        $open = $openingHours[$dayName] ?? null;
        if (! $open) {
            return response()->json([
                'success' => false,
                'message' => __('messages.restaurant_closed', [
                    'day' => $dayName,
                ]),
            ]);
        }
        if (
            $data['time'] < $open[0] ||
            $data['time'] > $open[1]
        ) {
            return response()->json([
                'success' => false,
                'message' => __('messages.time_out_of_hours', [
                    'open' => $open[0],
                    'close' => $open[1],
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
        $blockedDomains = config('email.blocked_domains');

        return ! in_array(
            $domain,
            $blockedDomains,
            true
        );
    }
}

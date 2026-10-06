<?php

namespace App\Http\Controllers;

use App\Models\HungarianHoliday;
use App\Models\OpeningHour;
use App\Models\Reservation;
use App\Models\SerbianHoliday;
use App\Models\SpecialOpeningHour;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

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

        $minimumReservationDate = now()
            ->addDays(2)
            ->format('Y-m-d');

        $validator = Validator::make(
            $request->all(),
            [
                'date' => 'required|date|after_or_equal:'.
                    $minimumReservationDate,
                'time' => 'required|date_format:H:i',
                'guests' => 'required|integer|min:1|max:70',
                'event_type_id' => [
                    'required',
                    'integer',
                    Rule::exists(
                        'reservation_event_types',
                        'id'
                    )->where(
                        'is_active',
                        true
                    ),
                ],
            ],
            [
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
                'event_type_id.required' => __('messages.event_type_required'),
                'event_type_id.exists' => __('messages.event_type_invalid'),
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

        $date = Carbon::parse($data['date']);

        $reservationDateTime = Carbon::parse(
            $data['date'].' '.$data['time']
        );

        $specialOpeningHour = SpecialOpeningHour::where(
            'type',
            'restaurant'
        )
            ->whereDate(
                'date',
                $date->toDateString()
            )
            ->first();

        if (! $specialOpeningHour) {
            $previousDate = $date->copy()->subDay();

            $previousSpecialOpeningHour = SpecialOpeningHour::where(
                'type',
                'restaurant'
            )
                ->whereDate(
                    'date',
                    $previousDate->toDateString()
                )
                ->first();

            if (
                $previousSpecialOpeningHour &&
                $previousSpecialOpeningHour->is_active &&
                $previousSpecialOpeningHour->open_time &&
                $previousSpecialOpeningHour->close_time
            ) {
                $previousOpenTime = Carbon::parse(
                    $previousSpecialOpeningHour->open_time
                )->format('H:i');

                $previousCloseTime = Carbon::parse(
                    $previousSpecialOpeningHour->close_time
                )->format('H:i');

                if ($previousCloseTime < $previousOpenTime) {
                    $specialOpeningHour = $previousSpecialOpeningHour;
                }
            }
        }

        if ($specialOpeningHour) {
            if (! $specialOpeningHour->is_active) {
                return response()->json([
                    'success' => false,
                    'message' => __('messages.restaurant_closed', [
                        'day' => $date->translatedFormat('l'),
                    ]),
                ]);
            }

            if (
                ! $specialOpeningHour->open_time ||
                ! $specialOpeningHour->close_time ||
                ! $specialOpeningHour->last_reservation_time
            ) {
                return response()->json([
                    'success' => false,
                    'message' => __('messages.reservation_time_not_configured'),
                ]);
            }

            $scheduleDate = Carbon::parse(
                $specialOpeningHour->date
            )->startOfDay();

            $openTime = Carbon::parse(
                $specialOpeningHour->open_time
            )->format('H:i');

            $closeTime = Carbon::parse(
                $specialOpeningHour->close_time
            )->format('H:i');

            $lastReservationTime = Carbon::parse(
                $specialOpeningHour->last_reservation_time
            )->format('H:i');

            $openingDateTime = Carbon::parse(
                $scheduleDate->toDateString().' '.$openTime
            );

            $closingDateTime = Carbon::parse(
                $scheduleDate->toDateString().' '.$closeTime
            );

            $lastReservationDateTime = Carbon::parse(
                $scheduleDate->toDateString().' '.$lastReservationTime
            );

            if ($closeTime < $openTime) {
                $closingDateTime->addDay();

                if ($lastReservationTime < $openTime) {
                    $lastReservationDateTime->addDay();
                }
            }

            if ($openingDateTime >= $closingDateTime) {
                return response()->json([
                    'success' => false,
                    'message' => __('messages.invalid_opening_hours'),
                ]);
            }

            if (
                $lastReservationDateTime < $openingDateTime ||
                $lastReservationDateTime >= $closingDateTime
            ) {
                return response()->json([
                    'success' => false,
                    'message' => __('messages.invalid_last_reservation_time'),
                ]);
            }

            if (
                $reservationDateTime < $openingDateTime ||
                $reservationDateTime > $lastReservationDateTime
            ) {
                return response()->json([
                    'success' => false,
                    'message' => __('messages.time_out_of_hours', [
                        'open' => $openingDateTime->format('H:i'),
                        'close' => $lastReservationDateTime->format('H:i'),
                    ]),
                ]);
            }
        } else {
            $serbianHoliday = SerbianHoliday::whereDate(
                'date',
                $date->toDateString()
            )
                ->first();

            $hungarianHoliday = HungarianHoliday::whereDate(
                'date',
                $date->toDateString()
            )
                ->first();

            if (
                ($serbianHoliday && ! $serbianHoliday->restaurant_is_active) ||
                ($hungarianHoliday && ! $hungarianHoliday->restaurant_is_active)
            ) {
                return response()->json([
                    'success' => false,
                    'message' => __('messages.restaurant_closed', [
                        'day' => $date->translatedFormat('l'),
                    ]),
                ]);
            }

            if ($serbianHoliday || $hungarianHoliday) {
                if ($serbianHoliday && $hungarianHoliday) {
                    $serbianOpenTime = Carbon::parse(
                        $serbianHoliday->restaurant_open_time
                    )->format('H:i');

                    $hungarianOpenTime = Carbon::parse(
                        $hungarianHoliday->restaurant_open_time
                    )->format('H:i');

                    $serbianCloseTime = Carbon::parse(
                        $serbianHoliday->restaurant_close_time
                    )->format('H:i');

                    $hungarianCloseTime = Carbon::parse(
                        $hungarianHoliday->restaurant_close_time
                    )->format('H:i');

                    $serbianLastReservationTime = Carbon::parse(
                        $serbianHoliday->restaurant_last_reservation_time
                    )->format('H:i');

                    $hungarianLastReservationTime = Carbon::parse(
                        $hungarianHoliday->restaurant_last_reservation_time
                    )->format('H:i');

                    $openTime = max(
                        $serbianOpenTime,
                        $hungarianOpenTime
                    );

                    $closeTime = min(
                        $serbianCloseTime,
                        $hungarianCloseTime
                    );

                    $lastReservationTime = min(
                        $serbianLastReservationTime,
                        $hungarianLastReservationTime
                    );
                } elseif ($serbianHoliday) {
                    $openTime = $serbianHoliday->restaurant_open_time;
                    $closeTime = $serbianHoliday->restaurant_close_time;
                    $lastReservationTime =
                        $serbianHoliday->restaurant_last_reservation_time;
                } else {
                    $openTime = $hungarianHoliday->restaurant_open_time;
                    $closeTime = $hungarianHoliday->restaurant_close_time;
                    $lastReservationTime =
                        $hungarianHoliday->restaurant_last_reservation_time;
                }
            } else {
                $openingHour = OpeningHour::where(
                    'type',
                    'restaurant'
                )
                    ->where(
                        'day_of_week',
                        $date->dayOfWeekIso
                    )
                    ->first();

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

                $openTime = $openingHour->open_time;
                $closeTime = $openingHour->close_time;
                $lastReservationTime =
                    $openingHour->last_reservation_time;
            }

            if (
                ! $openTime ||
                ! $closeTime ||
                ! $lastReservationTime
            ) {
                return response()->json([
                    'success' => false,
                    'message' => __('messages.reservation_time_not_configured'),
                ]);
            }

            $openTime = Carbon::parse(
                $openTime
            )->format('H:i');

            $closeTime = Carbon::parse(
                $closeTime
            )->format('H:i');

            $lastReservationTime = Carbon::parse(
                $lastReservationTime
            )->format('H:i');

            $openingDateTime = Carbon::parse(
                $date->toDateString().' '.$openTime
            );

            $closingDateTime = Carbon::parse(
                $date->toDateString().' '.$closeTime
            );

            $lastReservationDateTime = Carbon::parse(
                $date->toDateString().' '.$lastReservationTime
            );

            if ($closeTime < $openTime) {
                $closingDateTime->addDay();

                if ($lastReservationTime < $openTime) {
                    $lastReservationDateTime->addDay();
                }
            }

            if ($openingDateTime >= $closingDateTime) {
                return response()->json([
                    'success' => false,
                    'message' => __('messages.invalid_opening_hours'),
                ]);
            }

            if (
                $lastReservationDateTime < $openingDateTime ||
                $lastReservationDateTime >= $closingDateTime
            ) {
                return response()->json([
                    'success' => false,
                    'message' => __('messages.invalid_last_reservation_time'),
                ]);
            }

            if (
                $reservationDateTime < $openingDateTime ||
                $reservationDateTime > $lastReservationDateTime
            ) {
                return response()->json([
                    'success' => false,
                    'message' => __('messages.time_out_of_hours', [
                        'open' => $openingDateTime->format('H:i'),
                        'close' => $lastReservationDateTime->format('H:i'),
                    ]),
                ]);
            }
        }

        $existing = Reservation::where(
            'user_id',
            $user->id
        )
            ->whereBetween(
                'date_time',
                [
                    $openingDateTime,
                    $lastReservationDateTime,
                ]
            )
            ->first();

        if ($existing) {
            return response()->json([
                'success' => false,
                'message' => __('messages.reservation_already_exists'),
            ]);
        }

        Reservation::create([
            'user_id' => $user->id,
            'fname' => $user->first_name,
            'lname' => $user->last_name,
            'email' => $user->email,
            'date_time' => $data['date'].' '.$data['time'],
            'guests' => $data['guests'],
            'event_type_id' => $data['event_type_id'],
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
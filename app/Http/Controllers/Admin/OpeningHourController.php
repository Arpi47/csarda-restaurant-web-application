<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OpeningHour;
use Carbon\Carbon;
use Illuminate\Http\Request;

class OpeningHourController extends Controller
{
    public function index()
    {
        return view('admin.opening-hours.index');
    }

    public function defaultHours()
    {
        $restaurantOpeningHours = OpeningHour::where(
            'type',
            'restaurant'
        )
            ->orderBy('day_of_week')
            ->get();
        $kitchenOpeningHours = OpeningHour::where(
            'type',
            'kitchen'
        )
            ->orderBy('day_of_week')
            ->get();
        return view(
            'admin.opening-hours.opening-hours',
            compact(
                'restaurantOpeningHours',
                'kitchenOpeningHours'
            )
        );
    }

    public function update(
        Request $request,
        OpeningHour $openingHour
    ) {
        $validated = $request->validate([
            'is_active' => 'required|boolean',
            'open_time' => 'nullable|date_format:H:i',
            'close_time' => 'nullable|date_format:H:i',
            'last_reservation_time' => 'nullable|date_format:H:i',
        ]);
        if (! $validated['is_active']) {
            $validated['open_time'] = null;
            $validated['close_time'] = null;
            $validated['last_reservation_time'] = null;
        } else {
            if (
                ! $validated['open_time'] ||
                ! $validated['close_time'] ||
                ! $validated['last_reservation_time']
            ) {
                return back()
                    ->withErrors([
                        'last_reservation_time' =>
                            __('messages.reservation_time_required'),
                    ])
                    ->withInput();
            }
            $openTime = Carbon::createFromFormat(
                'H:i',
                $validated['open_time']
            );
            $closeTime = Carbon::createFromFormat(
                'H:i',
                $validated['close_time']
            );
            $lastReservationTime = Carbon::createFromFormat(
                'H:i',
                $validated['last_reservation_time']
            );
            if ($openTime >= $closeTime) {
                return back()
                    ->withErrors([
                        'close_time' =>
                            __('messages.invalid_opening_hours'),
                    ])
                    ->withInput();
            }
            if ($lastReservationTime < $openTime) {
                return back()
                    ->withErrors([
                        'last_reservation_time' =>
                            __('messages.invalid_last_reservation_time'),
                    ])
                    ->withInput();
            }
            $minimumLastReservationTime = $closeTime->copy()
                ->subMinutes(30);
            if (
                $lastReservationTime >
                $minimumLastReservationTime
            ) {
                return back()
                    ->withErrors([
                        'last_reservation_time' =>
                            __('messages.last_reservation_too_late'),
                    ])
                    ->withInput();
            }
        }
        $openingHour->update($validated);
        return redirect()
            ->route('admin.opening-hours.opening-hours')
            ->with(
                'success',
                __('messages.opening_hours_updated')
            );
    }
}

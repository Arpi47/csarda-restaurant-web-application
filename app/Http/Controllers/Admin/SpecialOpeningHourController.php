<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SpecialOpeningHour;
use Carbon\Carbon;
use Illuminate\Http\Request;

class SpecialOpeningHourController extends Controller
{
    public function index()
    {
        $specialOpeningHours = SpecialOpeningHour::where(
            'is_manually_deleted',
            false
        )
            ->orderBy('date')
            ->get();
        return view(
            'admin.opening-hours.special-opening-hours',
            compact('specialOpeningHours')
        );
    }
    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => [
                'required',
                'in:restaurant,kitchen',
            ],
            'date' => [
                'required',
                'date',
                'unique:special_opening_hours,date,NULL,id,type,'.$request->type,
            ],
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
            $error = $this->validateOpeningTimes($validated);
            if ($error) {
                return back()
                    ->withErrors([
                        'last_reservation_time' => $error,
                    ])
                    ->withInput();
            }
        }
        SpecialOpeningHour::create($validated);
        return redirect()
            ->route('admin.opening-hours.special-opening-hours')
            ->with(
                'success',
                __('messages.special_opening_hour_created')
            );
    }

    public function update(
        Request $request,
        SpecialOpeningHour $specialOpeningHour
    ) {
        $validated = $request->validate([
            'type' => [
                'required',
                'in:restaurant,kitchen',
            ],
            'date' => [
                'required',
                'date',
                'unique:special_opening_hours,date,'
                    .$specialOpeningHour->id
                    .',id,type,'
                    .$request->type,
            ],
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
            $error = $this->validateOpeningTimes($validated);
            if ($error) {
                return back()
                    ->withErrors([
                        'last_reservation_time' => $error,
                    ])
                    ->withInput();
            }
        }
        if ($specialOpeningHour->is_google_calendar) {
            $validated['is_manually_overridden'] = true;
        }
        $specialOpeningHour->update($validated);
        return redirect()
            ->route('admin.opening-hours.special-opening-hours')
            ->with(
                'success',
                __('messages.special_opening_hour_created')
            );
    }

    public function destroy(
        SpecialOpeningHour $specialOpeningHour
    ) {
        if ($specialOpeningHour->is_google_calendar) {
            $specialOpeningHour->update([
                'is_manually_deleted' => true,
            ]);
        } else {
            $specialOpeningHour->delete();
        }
        return redirect()
            ->route('admin.opening-hours.special-opening-hours')
            ->with(
                'success',
                __('messages.special_opening_hour_created')
            );
    }

    private function validateOpeningTimes(array $data): ?string
    {
        if (
            ! $data['open_time'] ||
            ! $data['close_time'] ||
            ! $data['last_reservation_time']
        ) {
            return __('messages.reservation_time_required');
        }
        $openTime = Carbon::createFromFormat(
            'H:i',
            $data['open_time']
        );
        $closeTime = Carbon::createFromFormat(
            'H:i',
            $data['close_time']
        );
        $lastReservationTime = Carbon::createFromFormat(
            'H:i',
            $data['last_reservation_time']
        );
        if ($openTime >= $closeTime) {
            return __('messages.invalid_opening_hours');
        }
        if ($lastReservationTime < $openTime) {
            return __('messages.invalid_last_reservation_time');
        }
        $minimumLastReservationTime = $closeTime->copy()
            ->subMinutes(30);
        if (
            $lastReservationTime >
            $minimumLastReservationTime
        ) {
            return __('messages.last_reservation_too_late');
        }
        return null;
    }
}

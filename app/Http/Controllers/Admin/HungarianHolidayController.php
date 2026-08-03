<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HungarianHoliday;
use App\Services\GoogleCalendarHungarianHolidaySyncService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HungarianHolidayController extends Controller
{
    public function index(): View
    {
        $holidays = HungarianHoliday::orderBy('date')->get();
        return view(
            'admin.opening-hours.hungarian-holidays',
            compact('holidays')
        );
    }

    public function import(
        GoogleCalendarHungarianHolidaySyncService $syncService
    ): RedirectResponse {
        $syncService->sync();
        return redirect()
            ->route('admin.opening-hours.hungarian-holidays')
            ->with(
                'success',
                __('messages.hungarian_holidays_imported')
            );
    }

    public function update(
        Request $request,
        HungarianHoliday $holiday
    ) {
        $validated = $request->validate([
            'restaurant_is_active' => 'required|boolean',
            'restaurant_open_time' => 'nullable|date_format:H:i',
            'restaurant_close_time' => 'nullable|date_format:H:i',
            'restaurant_last_reservation_time' => 'nullable|date_format:H:i',
            'kitchen_is_active' => 'required|boolean',
            'kitchen_open_time' => 'nullable|date_format:H:i',
            'kitchen_close_time' => 'nullable|date_format:H:i',
            'kitchen_last_order_time' => 'nullable|date_format:H:i',
        ]);
        if (! $validated['restaurant_is_active']) {
            $validated['restaurant_open_time'] = null;
            $validated['restaurant_close_time'] = null;
            $validated['restaurant_last_reservation_time'] = null;
        } else {
            if (
                ! $validated['restaurant_open_time'] ||
                ! $validated['restaurant_close_time'] ||
                ! $validated['restaurant_last_reservation_time']
            ) {
                return back()
                    ->withErrors([
                        'restaurant_last_reservation_time' =>
                            __('messages.reservation_time_required'),
                    ])
                    ->withInput();
            }
            $restaurantOpen = Carbon::createFromFormat(
                'H:i',
                $validated['restaurant_open_time']
            );
            $restaurantClose = Carbon::createFromFormat(
                'H:i',
                $validated['restaurant_close_time']
            );
            $restaurantLastReservation = Carbon::createFromFormat(
                'H:i',
                $validated['restaurant_last_reservation_time']
            );
            if ($restaurantOpen >= $restaurantClose) {
                return back()
                    ->withErrors([
                        'restaurant_close_time' =>
                            __('messages.invalid_opening_hours'),
                    ])
                    ->withInput();
            }
            if ($restaurantLastReservation < $restaurantOpen) {
                return back()
                    ->withErrors([
                        'restaurant_last_reservation_time' =>
                            __('messages.invalid_last_reservation_time'),
                    ])
                    ->withInput();
            }
            $minimumLastReservationTime = $restaurantClose->copy()
                ->subMinutes(30);
            if (
                $restaurantLastReservation >
                $minimumLastReservationTime
            ) {
                return back()
                    ->withErrors([
                        'restaurant_last_reservation_time' =>
                            __('messages.last_reservation_too_late'),
                    ])
                    ->withInput();
            }
        }
        if (! $validated['kitchen_is_active']) {
            $validated['kitchen_open_time'] = null;
            $validated['kitchen_close_time'] = null;
            $validated['kitchen_last_order_time'] = null;
        } else {
            if (
                ! $validated['kitchen_open_time'] ||
                ! $validated['kitchen_close_time'] ||
                ! $validated['kitchen_last_order_time']
            ) {
                return back()
                    ->withErrors([
                        'kitchen_last_order_time' =>
                            __('messages.reservation_time_required'),
                    ])
                    ->withInput();
            }
            $kitchenOpen = Carbon::createFromFormat(
                'H:i',
                $validated['kitchen_open_time']
            );
            $kitchenClose = Carbon::createFromFormat(
                'H:i',
                $validated['kitchen_close_time']
            );
            $kitchenLastOrder = Carbon::createFromFormat(
                'H:i',
                $validated['kitchen_last_order_time']
            );
            if ($kitchenOpen >= $kitchenClose) {
                return back()
                    ->withErrors([
                        'kitchen_close_time' =>
                            __('messages.invalid_opening_hours'),
                    ])
                    ->withInput();
            }
            if ($kitchenLastOrder < $kitchenOpen) {
                return back()
                    ->withErrors([
                        'kitchen_last_order_time' =>
                            __('messages.invalid_last_reservation_time'),
                    ])
                    ->withInput();
            }
            $minimumLastOrderTime = $kitchenClose->copy()
                ->subMinutes(30);
            if (
                $kitchenLastOrder >
                $minimumLastOrderTime
            ) {
                return back()
                    ->withErrors([
                        'kitchen_last_order_time' =>
                            __('messages.last_reservation_too_late'),
                    ])
                    ->withInput();
            }
        }
        $holiday->update($validated);
        return redirect()
            ->route('admin.opening-hours.hungarian-holidays')
            ->with(
                'success',
                __('messages.hungarian_holiday_updated')
            );
    }
}

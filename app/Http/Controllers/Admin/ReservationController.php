<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\ReservationStatusMail;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class ReservationController extends Controller
{
    public function index()
    {
        $reservations = Reservation::orderBy('date_time', 'desc')->get();

        return view(
            'admin.reservations.index',
            compact('reservations')
        );
    }

    public function updateStatus(
        Request $request,
        Reservation $reservation
    ) {
        $request->validate([
            'status' => 'required|in:pending,approved,rejected',
        ]);

        if ($reservation->status === $request->status) {

            return back()
                ->with(
                    'info',
                    __('messages.status_unchanged')
                );

        }

        $reservation->status = $request->status;
        $reservation->status_changed_at = now();
        $reservation->status_changed_by = Auth::id();
        $reservation->save();

        Mail::to($reservation->email)
            ->send(
                new ReservationStatusMail($reservation)
            );

        return back()
            ->with(
                'success',
                __('messages.status_updated')
            );
    }

    public function destroy(Reservation $reservation)
    {
        $reservation->delete();

        return redirect()
            ->back()
            ->with(
                'success',
                __('messages.reservation_deleted')
            );
    }
}

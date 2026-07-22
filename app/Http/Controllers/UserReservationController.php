<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Reservation;
use Illuminate\Support\Facades\Auth;

class UserReservationController extends Controller
{
    public function index()
    {
        $userId = Auth::id();
        $reservations = Reservation::where('user_id', $userId)
            ->orderBy('date_time', 'asc')
            ->get();
        return view('user.reservations', compact('reservations'));
    }

    public function destroy(Reservation $reservation)
    {
        if ($reservation->user_id !== Auth::id()) {
            abort(403);
        }
        $reservation->delete();
        return back()->with('success', __('messages.reservation_deleted'));
    }
}
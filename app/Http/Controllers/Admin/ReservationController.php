<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\ReservationStatusMail;
use App\Models\Reservation;
use App\Models\ReservationEventType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class ReservationController extends Controller
{
    public function index(Request $request)
    {
        $query = Reservation::query()
            ->with('eventType');

        if ($request->filled('search')) {
            $search = $request->input('search');

            $query->where(function ($query) use ($search) {
                $query->where('id', $search)
                    ->orWhere('fname', 'like', "%{$search}%")
                    ->orWhere('lname', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where(
                'status',
                $request->input('status')
            );
        }

        if ($request->filled('event_type_id')) {
            $query->where(
                'event_type_id',
                $request->input('event_type_id')
            );
        }

        if ($request->filled('date_from')) {
            $query->whereDate(
                'date_time',
                '>=',
                $request->input('date_from')
            );
        }

        if ($request->filled('date_to')) {
            $query->whereDate(
                'date_time',
                '<=',
                $request->input('date_to')
            );
        }

        $reservations = $query
            ->orderBy('date_time', 'desc')
            ->paginate(25)
            ->withQueryString();

        $eventTypes = \App\Models\ReservationEventType::where(
            'is_active',
            true
        )
            ->orderBy('id')
            ->get();

        return view(
            'admin.reservations.index',
            compact(
                'reservations',
                'eventTypes'
            )
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

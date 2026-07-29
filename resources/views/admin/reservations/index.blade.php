@extends('admin.layouts.app')
@section('title', __('messages.reservations'))
@section('content')
    <h1>{{ __('messages.reservations') }}</h1>
    <table border="1" cellpadding="5" cellspacing="0">
        <thead>
            <tr>
                <th>ID</th>
                <th>{{ __('messages.name') }}</th>
                <th>{{ __('messages.email') }}</th>
                <th>{{ __('messages.date_time') }}</th>
                <th>{{ __('messages.guests') }}</th>
                <th>{{ __('messages.status') }}</th>
                <th>{{ __('messages.actions') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($reservations as $reservation)
                <tr>
                    <td>{{ $reservation->id }}</td>
                    <td>{{ $reservation->fname }} {{ $reservation->lname }}</td>
                    <td>{{ $reservation->email }}</td>
                    <td>{{ \Carbon\Carbon::parse($reservation->date_time)->format('Y-m-d H:i') }}</td>
                    <td>{{ $reservation->guests }}</td>
                    <td>{{ ucfirst($reservation->status) }}</td>
                    <td>
                        <form method="POST" action="{{ route('admin.reservations.updateStatus', $reservation) }}"
                            style="display:inline;">
                            @csrf
                            <input type="hidden" name="status" value="approved">
                            <button type="submit" {{ $reservation->status === 'approved' ? 'disabled' : '' }}>
                                <span class="action-icon">✅</span> {{ __('messages.approve') }}
                            </button>
                        </form>
                        <form method="POST" action="{{ route('admin.reservations.updateStatus', $reservation) }}"
                            style="display:inline;">
                            @csrf
                            <input type="hidden" name="status" value="rejected">
                            <button type="submit" {{ $reservation->status === 'rejected' ? 'disabled' : '' }}>
                                <span class="action-icon">❌</span> {{ __('messages.reject') }}
                            </button>
                        </form>
                        <form method="POST" action="{{ route('admin.reservations.destroy', $reservation) }}"
                            style="display:inline;" onsubmit="return confirm('{{ __('messages.confirm_delete') }}')">
                            @csrf
                            @method('DELETE')
                            <button type="submit"><span class="action-icon">🗑️</span>
                                {{ __('messages.delete') }}</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection

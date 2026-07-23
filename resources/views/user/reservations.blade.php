@extends('layouts.app')
@section('title', __('messages.my_reservations'))
@section('content')
    <h1>{{ __('messages.my_reservations') }}</h1>
    @if (session('success'))
        <div class="success-message">
            {{ session('success') }}
        </div>
    @endif
    @if ($reservations->isEmpty())
        <p>{{ __('messages.no_reservations') }}</p>
    @else
        <table border="1" cellpadding="5" cellspacing="0">
            <thead>
                <tr>
                    <th>{{ __('messages.date') }}</th>
                    <th>{{ __('messages.time') }}</th>
                    <th>{{ __('messages.guests') }}</th>
                    <th>{{ __('messages.status') }}</th> <!-- új oszlop a státusznak -->
                    <th>{{ __('messages.actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($reservations as $reservation)
                    @php
                        $date = \Carbon\Carbon::parse($reservation->date_time)->format('Y-m-d');
                        $time = \Carbon\Carbon::parse($reservation->date_time)->format('H:i');
                        switch ($reservation->status) {
                            case 'approved':
                                $statusColor = '#28a745';
                                $statusText = __('messages.approved');
                                break;
                            case 'pending':
                                $statusColor = '#ffc107';
                                $statusText = __('messages.pending');
                                break;
                            case 'rejected':
                                $statusColor = '#dc3545';
                                $statusText = __('messages.rejected');
                                break;
                            default:
                                $statusColor = '#6c757d';
                                $statusText = ucfirst($reservation->status);
                        }
                    @endphp
                    <tr>
                        <td>{{ $date }}</td>
                        <td>{{ $time }}</td>
                        <td>{{ $reservation->guests }}</td>
                        <td>
                            <span style="color: {{ $statusColor }}; font-weight: bold;">
                                {{ $statusText }}
                            </span>
                        </td>
                        <td>
                            <form method="POST" action="{{ route('user.reservations.destroy', $reservation) }}"
                                onsubmit="return confirm('{{ __('messages.confirm_delete') }}')">
                                @csrf
                                @method('DELETE')
                                <button class="delete-btn">
                                    🗑 {{ __('messages.cancel') }}
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
@endsection
<style>
    .reservations-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
    }

    .reservations-table th,
    .reservations-table td {
        border: 1px solid #ddd;
        padding: 10px;
        text-align: left;
    }

    .reservations-table th {
        background: #f2f2f2;
    }

    .delete-btn {
        background: #e63946;
        color: white;
        border: none;
        padding: 6px 10px;
        border-radius: 4px;
        cursor: pointer;
    }

    .delete-btn:hover {
        background: #d62828;
    }

    .success-message {
        background: #d4edda;
        padding: 10px;
        border-radius: 5px;
        margin-bottom: 10px;
    }
</style>

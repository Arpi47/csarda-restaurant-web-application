@extends('admin.layouts.app')
@section('title', __('messages.reservations'))
@section('content')
    <h1>{{ __('messages.reservations') }}</h1>
    <form method="GET" action="{{ route('admin.reservations.index') }}" class="admin-filters">
        <label>
            {{ __('messages.search') }}:
            <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('messages.search') }}">
        </label>
        <div class="divider"></div>
        <label>
            {{ __('messages.status') }}:
            <select name="status">
                <option value="">
                    {{ __('messages.all') }}
                </option>
                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>
                    {{ __('messages.pending') }}
                </option>
                <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>
                    {{ __('messages.approved') }}
                </option>
                <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>
                    {{ __('messages.rejected') }}
                </option>
            </select>
        </label>
        <div class="divider"></div>
        <label>
            {{ __('messages.event_type') }}:
            <select name="event_type_id">
                <option value="">
                    {{ __('messages.all') }}
                </option>
                @foreach ($eventTypes as $eventType)
                    @php
                        $eventTypeName = match (app()->getLocale()) {
                            'hu' => $eventType->name_hu,
                            'sr_lat' => $eventType->name_sr,
                            'sr_cyr' => $eventType->name_sr_cyrl,
                            default => $eventType->name_en,
                        };
                    @endphp
                    <option value="{{ $eventType->id }}"
                        {{ request('event_type_id') == $eventType->id ? 'selected' : '' }}>
                        {{ $eventTypeName }}
                    </option>
                @endforeach
            </select>
        </label>
        <div class="divider"></div>
        <label>
            {{ __('messages.from') }}:
            <input type="date" name="date_from" value="{{ request('date_from') }}">
        </label>
        <div class="divider"></div>
        <label>
            {{ __('messages.to') }}:
            <input type="date" name="date_to" value="{{ request('date_to') }}">
        </label>
        <div class="filter-buttons">
            <button type="submit" class="btn btn-filter" title="{{ __('messages.filter') }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                    class="bi bi-funnel" viewBox="0 0 16 16">
                    <path
                        d="M1.5 1.5A.5.5 0 0 1 2 1h12a.5.5 0 0 1 .39.81L10 7.18v6.64a.5.5 0 0 1-.79.41l-2-1.5a.5.5 0 0 1-.21-.41V7.18L1.11 1.81A.5.5 0 0 1 1.5 1.5z" />
                </svg>
            </button>
            <a href="{{ route('admin.reservations.index') }}" class="btn btn-clear">
                {{ __('messages.clear') }}
            </a>
        </div>
    </form>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>{{ __('messages.name') }}</th>
                <th>{{ __('messages.email') }}</th>
                <th>{{ __('messages.date_time') }}</th>
                <th>{{ __('messages.guests') }}</th>
                <th>{{ __('messages.event_type') }}</th>
                <th>{{ __('messages.status') }}</th>
                <th>{{ __('messages.actions') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($reservations as $reservation)
                <tr>
                    <td>
                        {{ $reservation->id }}
                    </td>
                    <td>
                        {{ $reservation->fname }}
                        {{ $reservation->lname }}
                    </td>

                    <td>
                        {{ $reservation->email }}
                    </td>
                    <td>
                        {{ \Carbon\Carbon::parse($reservation->date_time)->format('Y-m-d H:i') }}
                    </td>
                    <td>
                        {{ $reservation->guests }}
                    </td>
                    <td>
                        @if ($reservation->eventType)
                            @php
                                $eventTypeName = match (app()->getLocale()) {
                                    'hu' => $reservation->eventType->name_hu,
                                    'sr_lat' => $reservation->eventType->name_sr,
                                    'sr_cyr' => $reservation->eventType->name_sr_cyrl,
                                    default => $reservation->eventType->name_en,
                                };
                            @endphp
                            {{ $eventTypeName }}
                        @else
                            -
                        @endif
                    </td>
                    <td>
                        {{ ucfirst($reservation->status) }}
                    </td>
                    <td class="action-buttons">
                        <form method="POST" action="{{ route('admin.reservations.updateStatus', $reservation) }}"
                            style="display:inline;">
                            @csrf
                            <input type="hidden" name="status" value="approved">
                            <button type="submit" {{ $reservation->status === 'approved' ? 'disabled' : '' }}>
                                <span class="action-icon">✅</span>
                                {{ __('messages.approve') }}
                            </button>
                        </form>
                        <form method="POST" action="{{ route('admin.reservations.updateStatus', $reservation) }}"
                            style="display:inline;">
                            @csrf
                            <input type="hidden" name="status" value="rejected">
                            <button type="submit" {{ $reservation->status === 'rejected' ? 'disabled' : '' }}>
                                <span class="action-icon">❌</span>
                                {{ __('messages.reject') }}
                            </button>
                        </form>
                        <form method="POST" action="{{ route('admin.reservations.destroy', $reservation) }}"
                            style="display:inline;" onsubmit="return confirm('{{ __('messages.confirm_delete') }}')">
                            @csrf
                            @method('DELETE')
                            <button type="submit">
                                <span class="action-icon">🗑️</span>
                                {{ __('messages.delete') }}
                            </button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    @if ($reservations->hasPages())
        <div class="pagination">
            @if ($reservations->onFirstPage())
                <span class="pagination-button disabled">
                    &laquo;
                </span>
            @else
                <a href="{{ $reservations->previousPageUrl() }}" class="pagination-button">
                    &laquo;
                </a>
            @endif
            @foreach ($reservations->getUrlRange(1, $reservations->lastPage()) as $page => $url)
                @if ($page == $reservations->currentPage())
                    <span class="pagination-button active">
                        {{ $page }}
                    </span>
                @else
                    <a href="{{ $url }}" class="pagination-button">
                        {{ $page }}
                    </a>
                @endif
            @endforeach
            @if ($reservations->hasMorePages())
                <a href="{{ $reservations->nextPageUrl() }}" class="pagination-button">
                    &raquo;
                </a>
            @else
                <span class="pagination-button disabled">
                    &raquo;
                </span>
            @endif
        </div>
    @endif
@endsection

@extends('admin.layouts.app')
@section('title', __('messages.admin_activity_log'))
@section('content')
    <h1>{{ __('messages.admin_activity_log') }}</h1>
    <form method="GET" action="{{ route('admin.admin.activity.index') }}" class="admin-filters">
        <label>
            {{ __('messages.admin') }}:
            <select name="admin_id">
                <option value="">{{ __('messages.all') }}</option>
                @foreach ($admins as $adminOption)
                    <option value="{{ $adminOption->id }}"
                        {{ isset($admin_id) && $admin_id == $adminOption->id ? 'selected' : '' }}>
                        {{ $adminOption->name }}
                    </option>
                @endforeach
            </select>
        </label>
        <div class="divider"></div>
        <label>
            {{ __('messages.action') }}:
            <select name="action">
                <option value="">{{ __('messages.all') }}</option>
                @foreach ($actions as $act)
                    <option value="{{ $act }}" {{ isset($action) && $action == $act ? 'selected' : '' }}>
                        {{ $act }}
                    </option>
                @endforeach
            </select>
        </label>
        <div class="divider"></div>
        <label>
            {{ __('messages.from') }}:
            <input type="datetime-local" name="date_from" value="{{ $date_from ?? '' }}">
        </label>
        <div class="divider"></div>
        <label>
            {{ __('messages.to') }}:
            <input type="datetime-local" name="date_to" value="{{ $date_to ?? '' }}">
        </label>
        <div class="filter-buttons">
            <button type="submit" class="btn btn-filter" title="{{ __('messages.filter') }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                    class="bi bi-funnel" viewBox="0 0 16 16">
                    <path
                        d="M1.5 1.5A.5.5 0 0 1 2 1h12a.5.5 0 0 1 .39.81L10 7.18v6.64a.5.5 0 0 1-.79.41l-2-1.5a.5.5 0 0 1-.21-.41V7.18L1.11 1.81A.5.5 0 0 1 1.5 1.5z" />
                </svg>
            </button>
            <a href="{{ route('admin.admin.activity.index') }}" class="btn btn-clear">
                {{ __('messages.clear') }}
            </a>
        </div>
    </form>
    <table border="1" cellpadding="5" cellspacing="0">
        <thead>
            <tr>
                <th>{{ __('messages.admin') }}</th>
                <th>{{ __('messages.action') }}</th>
                <th>{{ __('messages.subject') }}</th>
                <th>{{ __('messages.ip_address') }}</th>
                <th>{{ __('messages.user_agent') }}</th>
                <th>{{ __('messages.timestamp') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($logs as $log)
                <tr>
                    <td>{{ $log->admin ? $log->admin->name : '—' }}</td>
                    <td>{{ $log->action }}</td>
                    <td>{{ $log->subject_type }} #{{ $log->subject_id }}</td>
                    <td>{{ $log->ip_address }}</td>
                    <td>{{ $log->user_agent }}</td>
                    <td>{{ $log->created_at_local->format('Y-m-d H:i:s') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection

@extends('admin.layouts.app')
@section('title', __('messages.user_management'))
@section('content')
    <h1>{{ __('messages.user_management') }}</h1>
    <form method="GET" action="{{ route('admin.users.index') }}" class="admin-filters">
        <label>
            {{ __('messages.search') }}:
            <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('messages.search') }}">
        </label>
        <div class="divider"></div>
        <label>
            {{ __('messages.login_method') }}:
            <select name="login_method">
                <option value="">
                    {{ __('messages.all') }}
                </option>
                <option value="email_password" {{ request('login_method') === 'email_password' ? 'selected' : '' }}>
                    {{ __('messages.email_password') }}
                </option>
                <option value="google" {{ request('login_method') === 'google' ? 'selected' : '' }}>
                    Google
                </option>
                <option value="google_email_password"
                    {{ request('login_method') === 'google_email_password' ? 'selected' : '' }}>
                    Google + {{ __('messages.email_password') }}
                </option>
            </select>
        </label>
        <div class="divider"></div>
        <label>
            {{ __('messages.status') }}:
            <select name="status">
                <option value="">
                    {{ __('messages.all') }}
                </option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>
                    {{ __('messages.active') }}
                </option>
                <option value="suspended" {{ request('status') === 'suspended' ? 'selected' : '' }}>
                    {{ __('messages.suspended') }}
                </option>
            </select>
        </label>
        <div class="filter-buttons">
            <button type="submit" class="btn btn-filter" title="{{ __('messages.filter') }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                    class="bi bi-funnel" viewBox="0 0 16 16">
                    <path
                        d="M1.5 1.5A.5.5 0 0 1 2 1h12a.5.5 0 0 1 .39.81L10 7.18v6.64a.5.5 0 0 1-.79.41l-2-1.5a.5.5 0 0 1-.21-.41V7.18L1.11 1.81A.5.5 0 0 1 1.5 1.5z" />
                </svg>
            </button>
            <a href="{{ route('admin.users.index') }}" class="btn btn-clear">
                {{ __('messages.clear') }}
            </a>
        </div>
    </form>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>{{ __('messages.first_name') }}</th>
                <th>{{ __('messages.last_name') }}</th>
                <th>{{ __('messages.email') }}</th>
                <th>{{ __('messages.login_method') }}</th>
                <th>{{ __('messages.status') }}</th>
                <th>{{ __('messages.deletion_request') }}</th>
                <th>{{ __('messages.actions') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($users as $user)
                <tr>
                    <td>
                        {{ $user->id }}
                    </td>
                    <td>
                        {{ $user->first_name }}
                    </td>
                    <td>
                        {{ $user->last_name }}
                    </td>
                    <td>
                        {{ $user->email }}
                    </td>
                    <td>
                        @if ($user->hasGoogleAccount() && !is_null($user->password))
                            Google +
                            {{ __('messages.email_password') }}
                        @elseif ($user->hasGoogleAccount() && is_null($user->password))
                            Google
                        @else
                            {{ __('messages.email_password') }}
                        @endif
                    </td>
                    <td>
                        @if ($user->is_suspended)
                            {{ __('messages.suspended') }}
                        @else
                            {{ __('messages.active') }}
                        @endif
                    </td>
                    <td>
                        @if ($user->deletion_requested_at)
                            <span title="{{ __('messages.deletion_request') }}">
                                ⏳
                                {{ $user->deletion_requested_at->diffForHumans() }}
                                @if ($user->deletion_will_be_final_at)
                                    (
                                    {{ $user->deletion_will_be_final_at->diffForHumans() }}
                                    )
                                @endif
                            </span>
                        @else
                            -
                        @endif
                    </td>
                    <td class="action-buttons">
                        @if ($user->canBeEditedByAdmin())
                            <a href="{{ route('admin.users.edit', $user) }}">
                                <span class="action-icon">✏️</span>
                            </a>
                        @endif
                        <form method="POST" action="{{ route('admin.users.toggleSuspend', $user) }}"
                            style="display:inline;">
                            @csrf
                            <button type="submit" class="btn-suspend">
                                @if ($user->is_suspended)
                                    <span class="action-icon">🔓</span>
                                @else
                                    <span class="action-icon">⛔</span>
                                @endif
                            </button>
                        </form>
                        <form method="POST" action="{{ route('admin.users.destroy', $user) }}" class="delete-form"
                            style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit">
                                <span class="action-icon">🗑️</span>
                            </button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    @if ($users->hasPages())
        <div class="pagination">
            @if ($users->onFirstPage())
                <span class="pagination-button disabled">
                    &laquo;
                </span>
            @else
                <a href="{{ $users->previousPageUrl() }}" class="pagination-button">
                    &laquo;
                </a>
            @endif
            @foreach ($users->getUrlRange(1, $users->lastPage()) as $page => $url)
                @if ($page == $users->currentPage())
                    <span class="pagination-button active">
                        {{ $page }}
                    </span>
                @else
                    <a href="{{ $url }}" class="pagination-button">
                        {{ $page }}
                    </a>
                @endif
            @endforeach
            @if ($users->hasMorePages())
                <a href="{{ $users->nextPageUrl() }}" class="pagination-button">
                    &raquo;
                </a>
            @else
                <span class="pagination-button disabled">
                    &raquo;
                </span>
            @endif
        </div>
    @endif
    <script>
        document.addEventListener(
            'DOMContentLoaded',
            function() {
                const deleteForms =
                    document.querySelectorAll(
                        '.delete-form'
                    );
                deleteForms.forEach(
                    function(form) {
                        form.addEventListener(
                            'submit',
                            function(e) {
                                const confirmed =
                                    confirm(
                                        "{{ __('messages.confirm_delete') }}"
                                    );
                                if (!confirmed) {
                                    e.preventDefault();
                                }
                            }
                        );
                    }
                );
            }
        );
    </script>
@endsection

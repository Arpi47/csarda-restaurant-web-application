@extends('admin.layouts.app')
@section('title', __('messages.admin_management'))
@section('content')
    <h1>{{ __('messages.admin_management') }}</h1>
    @if (auth('admin')->user()->is_super_admin)
        <a href="{{ route('admin.admins.invite') }}" class="admin-add-button">
            + {{ __('messages.invite_admin') }}
        </a>
    @endif
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>{{ __('messages.name') }}</th>
                <th>{{ __('messages.email') }}</th>
                <th>{{ __('messages.actions') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($admins as $admin)
                <tr>
                    <td>{{ $admin->id }}</td>
                    <td>{{ $admin->name }}</td>
                    <td>{{ $admin->email }}</td>
                    <td class="action-buttons">
                        <div>
                            @if (auth('admin')->user()->is_super_admin && auth('admin')->id() !== $admin->id)
                                <form method="POST" action="{{ route('admin.admins.toggleSuspend', $admin) }}">
                                    @csrf
                                    <button type="submit" class="btn-suspend">
                                        @if ($admin->is_suspended)
                                            <span class="action-icon">🔓</span>
                                        @else
                                            <span class="action-icon">⛔</span>
                                        @endif
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('admin.admins.destroy', $admin) }}"
                                    class="delete-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit">
                                        <span class="action-icon">🗑️</span>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const deleteForms = document.querySelectorAll('.delete-form');
            deleteForms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    const confirmed = confirm("{{ __('messages.confirm_delete_admin') }}");
                    if (!confirmed) e.preventDefault();
                });
            });
        });
    </script>
@endsection

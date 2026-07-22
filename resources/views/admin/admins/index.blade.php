@extends('admin.layouts.app')
@section('title', __('messages.admin_management'))
@section('content')
<h1>{{ __('messages.admin_management') }}</h1>
@if(auth('admin')->user()->is_super_admin)
    <a href="{{ route('admin.admins.create') }}" class="btn">
        + {{ __('messages.create_admin') }}
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
        @foreach($admins as $admin)
            <tr>
                <td>{{ $admin->id }}</td>
                <td>{{ $admin->name }}</td>
                <td>{{ $admin->email }}</td>
                <td class="action-buttons">
                    @php
                        $current = auth('admin')->user();
                    @endphp
                    @if($current->is_super_admin || $current->id === $admin->id)
                        <a href="{{ route('admin.admins.edit', $admin) }}" class="action-icon">✏️</a>
                    @else
                        <span class="action-icon placeholder"><br></span>
                    @endif
                    @if(auth('admin')->user()->is_super_admin && auth('admin')->id() !== $admin->id)
                        <form method="POST" action="{{ route('admin.admins.toggleSuspend', $admin) }}" style="display:inline;">
                            @csrf
                            <button class="btn-suspend">
                                {{ $admin->is_suspended ? '🔓 ' . __('messages.activate') : '⛔ ' . __('messages.suspend') }}
                            </button>
                        </form>
                        <form method="POST" action="{{ route('admin.admins.destroy', $admin) }}" class="delete-form" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button>🗑️</button>
                        </form>
                    @endif
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
            const confirmed = confirm("{{ __('messages.confirm_delete') }}");
            if (!confirmed) e.preventDefault();
        });
    });
});
</script>
@endsection
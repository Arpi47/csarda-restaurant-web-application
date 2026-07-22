@extends('admin.layouts.app')
@section('title', __('messages.user_management'))
@section('content')
<h1>{{ __('messages.user_management') }}</h1>
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
@foreach($users as $user)
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
            @if($user->hasGoogleAccount())
                Google +
                {{ __('messages.email_password') }}
            @elseif(is_null($user->password))
                Google
            @else
                {{ __('messages.email_password') }}
            @endif
        </td>
        <td>
            @if($user->is_suspended)
                {{ __('messages.suspended') }}
            @else
                {{ __('messages.active') }}
            @endif
        </td>
        <td>
            @if($user->deletion_requested_at)
                <span
                    title="{{ __('messages.deletion_request') }}"
                >
                    ⏳
                    {{ $user->deletion_requested_at->diffForHumans() }}
                    @if($user->deletion_will_be_final_at)
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
            @if($user->canBeEditedByAdmin())
                <a
                    href="{{ route('admin.users.edit', $user) }}"
                >
                    ✏️
                </a>
            @endif
            <form
                method="POST"
                action="{{ route('admin.users.toggleSuspend', $user) }}"
                style="display:inline;"
            >
                @csrf
                <button
                    type="submit"
                    class="btn-suspend"
                >
                    @if($user->is_suspended)
                        🔓 {{ __('messages.activate') }}
                    @else
                        ⛔ {{ __('messages.suspend') }}
                    @endif
                </button>
            </form>
            <form
                method="POST"
                action="{{ route('admin.users.destroy', $user) }}"
                class="delete-form"
                style="display:inline;"
            >
                @csrf
                @method('DELETE')
                <button type="submit">
                    🗑️
                </button>
            </form>
        </td>
    </tr>
@endforeach
</tbody>
</table>
<script>
document.addEventListener(
    'DOMContentLoaded',
    function()
    {
        const deleteForms =
            document.querySelectorAll(
                '.delete-form'
            );
        deleteForms.forEach(
            function(form)
            {
                form.addEventListener(
                    'submit',
                    function(e)
                    {
                        const confirmed =
                            confirm(
                                "{{ __('messages.confirm_delete') }}"
                            );
                        if(!confirmed)
                        {
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
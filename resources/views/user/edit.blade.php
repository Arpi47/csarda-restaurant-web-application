@extends('layouts.app')
@section('title', __('messages.edit_profile'))
@section('content')
    <div class="content">
        <h1>{{ __('messages.edit_profile') }}</h1>
        @if (session('success'))
            <div style="color:green">{{ session('success') }}</div>
        @endif
        <form method="POST" action="{{ route('user.profile.update') }}" enctype="multipart/form-data" class="reservation-form"
            id="user-edit-form">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label for="first_name">{{ __('messages.first_name') }}</label>
                <input type="text" id="first_name" name="first_name" value="{{ old('first_name', $user->first_name) }}"
                    @error('first_name') class="is-invalid" @enderror>
                @error('first_name')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>
            <div class="form-group">
                <label for="last_name">{{ __('messages.last_name') }}</label>
                <input type="text" id="last_name" name="last_name" value="{{ old('last_name', $user->last_name) }}"
                    @error('last_name') class="is-invalid" @enderror>
                @error('last_name')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>
            {{-- <div class="form-group">
            <label for="avatar">{{ __('messages.avatar') }}</label>
            <div style="margin-top:5px;">
                <img id="avatarPreview" src="{{ $user->profile_image_url }}" 
                     alt="Profile" 
                     style="width:100px;height:100px;object-fit:cover;border:1px solid rgba(0,0,0,0.2);border-radius:5px">
            </div>
            <input type="file" id="avatar" name="avatar" accept="image/*" 
                   @error('avatar') class="is-invalid" @enderror>
            @error('avatar')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div> --}}
            <div class="form-group">
                <label for="password">{{ __('messages.password') }}</label>
                <small>({{ __('messages.leave_blank') }})</small>
                <input type="password" id="password" name="password" @error('password') class="is-invalid" @enderror>
                @error('password')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>
            <div class="form-group">
                <label for="password_confirmation">{{ __('messages.password_confirmation') }}</label>
                <input type="password" id="password_confirmation" name="password_confirmation">
            </div>
            <div class="show-password">
                <input type="checkbox" id="showPassword">
                <label for="showPassword">{{ __('messages.show_password') }}</label>
            </div>

            <button type="submit" class="reserve-btn">{{ __('messages.update') }}</button>
        </form>
        <div style="margin-top:20px;">
            @if ($user->deletion_requested && $user->deletion_will_be_final_at && now()->lessThan($user->deletion_will_be_final_at))
                <button id="cancelDeletionBtn" class="delete-btn"><span class="action-icon">❌</span>
                    {{ __('messages.cancel_deletion') }}</button>
            @else
                <button id="requestDeletionBtn" class="delete-btn"><span class="action-icon">🗑️</span>
                    {{ __('messages.delete_profile') }}</button>
            @endif
        </div>
    </div>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://www.google.com/recaptcha/api.js?render={{ env('RECAPTCHA_SITE_KEY') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            /* const avatarInput = document.getElementById('avatar');
            if(avatarInput){
                avatarInput.addEventListener('change', function(event){
                    const file = event.target.files[0];
                    if(file){
                        const reader = new FileReader();
                        reader.onload = function(e){
                            const img = document.getElementById('avatarPreview');
                            img.src = e.target.result;
                            img.alt = file.name;
                        }
                        reader.readAsDataURL(file);
                    }
                });
            } */
            const requestBtn = document.getElementById('requestDeletionBtn');
            if (requestBtn) {
                requestBtn.addEventListener('click', function() {
                    if (confirm("{{ __('messages.alert_delete_profile_confirm') }}")) {
                        fetch("{{ route('user.profile.deleteRequest') }}", {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': csrfToken,
                                    'Content-Type': 'application/json'
                                },
                                body: JSON.stringify({})
                            })
                            .then(res => res.json())
                            .then(data => {
                                if (data.success) {
                                    alert("{{ __('messages.alert_deletion_requested') }}");
                                    location.reload();
                                } else if (data.too_many_attempts) {
                                    alert("{{ __('messages.alert_too_many_attempts') }}");
                                }
                            });
                    }
                });
            }
            const cancelBtn = document.getElementById('cancelDeletionBtn');
            if (cancelBtn) {
                cancelBtn.addEventListener('click', function() {
                    if (confirm("{{ __('messages.alert_cancel_deletion_confirm') }}")) {
                        fetch("{{ route('user.profile.deleteCancel') }}", {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': csrfToken,
                                    'Content-Type': 'application/json'
                                },
                                body: JSON.stringify({})
                            })
                            .then(res => res.json())
                            .then(data => {
                                if (data.success) {
                                    alert("{{ __('messages.alert_deletion_cancelled') }}");
                                    location.reload();
                                }
                            });
                    }
                });
            }
            const form = document.getElementById('user-edit-form');
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                grecaptcha.ready(function() {
                    grecaptcha.execute('{{ env('RECAPTCHA_SITE_KEY') }}', {
                        action: 'edit_profile'
                    }).then(function(token) {
                        let input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = 'g-recaptcha-response';
                        input.value = token;
                        form.appendChild(input);
                        form.submit();
                    });
                });
            });
        });
        document.addEventListener("DOMContentLoaded", function() {
            const password = document.getElementById("password");
            const confirmPassword = document.getElementById("password_confirmation");
            const checkbox = document.getElementById("showPassword");
            checkbox.addEventListener("change", function() {
                const type = this.checked ? "text" : "password";
                password.type = type;
                confirmPassword.type = type;
            });
        });
    </script>
    <style>
        .delete-btn {
            background-color: #ff4d4f;
            color: white;
            border: none;
            padding: 8px 12px;
            border-radius: 5px;
            cursor: pointer;
        }

        .delete-btn:hover {
            background-color: #d9363e;
        }
    </style>
@endsection

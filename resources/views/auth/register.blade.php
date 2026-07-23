@extends('layouts.app')
@section('title', __('messages.register'))
@section('content')
    <div class="auth-container">
        <h1>{{ __('messages.register') }}</h1>
        <form method="POST" action="{{ route('register') }}" class="reservation-form" enctype="multipart/form-data"
            id="user-register-form">
            @csrf
            <div class="form-group">
                <label for="email">{{ __('messages.email') }}</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required
                    class="@error('email') is-invalid @enderror">
                @error('email')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
            <div class="form-group">
                <label for="first_name">{{ __('messages.first_name') }}</label>
                <input id="first_name" type="text" name="first_name" value="{{ old('first_name') }}" required
                    class="@error('first_name') is-invalid @enderror">
                @error('first_name')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
            <div class="form-group">
                <label for="last_name">{{ __('messages.last_name') }}</label>
                <input id="last_name" type="text" name="last_name" value="{{ old('last_name') }}" required
                    class="@error('last_name') is-invalid @enderror">
                @error('last_name')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
            <div class="form-group">
                <label for="password">{{ __('messages.password') }}</label>
                <input id="password" type="password" name="password" required
                    class="@error('password') is-invalid @enderror">
                @error('password')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
            <div class="form-group">
                <label for="password_confirmation">{{ __('messages.password_confirmation') }}</label>
                <input id="password_confirmation" type="password" name="password_confirmation" required>
            </div>
            <div class="show-password">
                <input type="checkbox" id="showPassword">
                <label for="showPassword">{{ __('messages.show_password') }}</label>
            </div>
            {{-- Avatar --}}
            {{-- 
    <div class="form-group">
        <label for="avatar">{{ __('messages.avatar') }}</label>
        <input id="avatar" type="file" name="avatar" accept="image/*">
        @error('avatar')
            <span class="text-danger">{{ $message }}</span>
        @enderror
    </div>
     --}}
            <button type="submit" class="reserve-btn">{{ __('messages.register') }}</button>
        </form>
        <script src="https://www.google.com/recaptcha/api.js?render={{ env('RECAPTCHA_SITE_KEY') }}"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const form = document.getElementById('user-register-form');
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    grecaptcha.ready(function() {
                        grecaptcha.execute('{{ env('RECAPTCHA_SITE_KEY') }}', {
                            action: 'register'
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
    </div>
@endsection

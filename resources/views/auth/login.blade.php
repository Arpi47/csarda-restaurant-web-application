@extends('layouts.app')
@section('title', __('messages.login'))
@section('content')
    <div class="auth-container">
        <h1>{{ __('messages.login') }}</h1>
        <form method="POST" action="{{ route('login') }}" class="reservation-form" id="user-login-form">
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
                <label for="password">{{ __('messages.password') }}</label>
                <input id="password" type="password" name="password" required
                    class="@error('password') is-invalid @enderror">
                @error('password')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
                <div class="show-password">
                    <input type="checkbox" id="showPassword">
                    <label for="showPassword">{{ __('messages.show_password') }}</label>
                </div>
            </div>
            <button type="submit" class="reserve-btn">{{ __('messages.login') }}</button>
            <p class="auth-link">
                <a href="{{ route('password.request') }}">
                    {{ __('messages.forgot_password') }}
                </a><br><br>
                {{ __('messages.no_account') }}
                <a href="{{ route('register') }}">{{ __('messages.register_here') }}</a>
            </p>
        </form>
        <script src="https://www.google.com/recaptcha/api.js?render={{ env('RECAPTCHA_SITE_KEY') }}"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const form = document.getElementById('user-login-form');
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    grecaptcha.ready(function() {
                        grecaptcha.execute('{{ env('RECAPTCHA_SITE_KEY') }}', {
                            action: 'login'
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
                const passwordInput = document.getElementById("password");
                const checkbox = document.getElementById("showPassword");
                checkbox.addEventListener("change", function() {
                    passwordInput.type = this.checked ? "text" : "password";
                });
            });
        </script>
    </div>
@endsection

@extends('admin.layouts.auth')
@section('title', __('messages.admin_login'))
@section('content')
    <div class="admin-login">
        <h1>{{ __('messages.admin_login') }}</h1>
        @if (session('suspended'))
            <script>
                alert("{{ session('suspended') }}");
            </script>
        @endif
        @if ($errors->any())
            <div class="error-messages">
                @foreach ($errors->all() as $error)
                    <p class="text-danger">{{ $error }}</p>
                @endforeach
            </div>
        @endif
        <form method="POST" action="{{ route('admin.login.submit') }}" id="admin-login-form">
            @csrf
            <label for="email">{{ __('messages.email') }}</label>
            <input type="email" name="email" id="email" value="{{ old('email') }}" required>

            <label id="pswd" for="password">{{ __('messages.password') }}</label>
            <input type="password" name="password" id="password" required>
            {{-- Show password --}}
            <div class="show-password">
                <input type="checkbox" id="showPassword">
                <label for="showPassword">{{ __('messages.show_password') }}</label>
            </div>
            <button type="submit">{{ __('messages.login') }}</button>
        </form>
    </div>
    <script src="https://www.google.com/recaptcha/api.js?render={{ env('RECAPTCHA_SITE_KEY') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('admin-login-form');
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
@endsection

@extends('layouts.app')
@section('title', __('messages.reset_password'))
@section('content')
<div class="content">
    <h1>{{ __('messages.reset_password') }}</h1>
    <form method="POST" action="{{ route('password.update') }}" class="reservation-form" id="reset-password-form">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">
        <div class="form-group">
            <label for="email">{{ __('messages.email') }}</label>
            <input type="email" id="email" name="email" 
                   value="{{ old('email', $email) }}" required
                   class="@error('email') is-invalid @enderror">
            @error('email')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
        <div class="form-group">
            <label for="password">{{ __('messages.new_password') }}</label>
            <input type="password" id="password" name="password" required
                   class="@error('password') is-invalid @enderror">
            @error('password')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
        <div class="form-group">
            <label for="password_confirmation">{{ __('messages.password_confirmation') }}</label>
            <input type="password" id="password_confirmation" name="password_confirmation" required>
        </div>
        <div class="show-password">
            <input type="checkbox" id="showPassword">
            <label for="showPassword">{{ __('messages.show_password') }}</label>
        </div>
        <button type="submit" class="reserve-btn">{{ __('messages.reset_password') }}</button>
    </form>
</div>
<script src="https://www.google.com/recaptcha/api.js?render={{ env('RECAPTCHA_SITE_KEY') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('reset-password-form');
    form.addEventListener('submit', function (e) {
        e.preventDefault();
        grecaptcha.ready(function() {
            grecaptcha.execute('{{ env('RECAPTCHA_SITE_KEY') }}', {action: 'reset_password'}).then(function(token) {
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
document.addEventListener("DOMContentLoaded", function () {
    const password = document.getElementById("password");
    const confirmPassword = document.getElementById("password_confirmation");
    const checkbox = document.getElementById("showPassword");
    checkbox.addEventListener("change", function () {
        const type = this.checked ? "text" : "password";
        password.type = type;
        confirmPassword.type = type;
    });
});
</script>
@endsection
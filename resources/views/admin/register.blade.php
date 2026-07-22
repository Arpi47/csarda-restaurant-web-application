@extends('admin.layouts.auth')
@section('title', __('messages.admin_registration'))
@section('content')
<h1>{{ __('messages.admin_registration') }}</h1>
@if(session('success'))
    <div style="color:green">{{ session('success') }}</div>
@endif
<form method="POST" action="{{ route('admin.register.submit', ['token' => $invitation->token]) }}" class="reservation-form menu-form" id="admin-register-form">
    @csrf
    <div class="form-group">
        <label for="name">{{ __('messages.name') }}</label>
        <input type="text" id="name" name="name" value="{{ old('name') }}" required
               @error('name') class="is-invalid" @enderror>
        @error('name')
            <div class="text-danger">{{ $message }}</div>
        @enderror
    </div>
    <div class="form-group">
        <label for="password">{{ __('messages.password') }}</label>
        <input type="password" id="password" name="password" required
               @error('password') class="is-invalid" @enderror>
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
    <button type="submit" class="reserve-btn" style="margin-bottom: 20px;">{{ __('messages.register') }}</button>
</form>
<script src="https://www.google.com/recaptcha/api.js?render={{ env('RECAPTCHA_SITE_KEY') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('admin-register-form');
    form.addEventListener('submit', function (e) {
        e.preventDefault();
        grecaptcha.ready(function() {
            grecaptcha.execute('{{ env('RECAPTCHA_SITE_KEY') }}', {action: 'register'}).then(function(token) {
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
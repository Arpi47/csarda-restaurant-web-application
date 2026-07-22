@extends('layouts.app')
@section('title', __('messages.forgot_password'))
@section('content')
<div class="content">
    <h1>{{ __('messages.forgot_password') }}</h1>
    @if(session('status'))
        <div style="color:green">{{ session('status') }}</div>
    @endif
    <form id="resetForm" method="POST" action="{{ route('password.email') }}" class="reservation-form">
        @csrf
        <div class="form-group">
            <label for="email">{{ __('messages.email') }}</label>
            <input type="email"
                   id="email"
                   name="email"
                   value="{{ old('email') }}"
                   required
                   class="@error('email') is-invalid @enderror">
            @error('email')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>
        <input type="hidden" name="g-recaptcha-response" id="recaptchaToken">
        <button type="submit" class="reserve-btn">
            {{ __('messages.send_reset_link') }}
        </button>
    </form>
</div>
<script src="https://www.google.com/recaptcha/api.js?render={{ config('services.recaptcha.site_key') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('resetForm');
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        grecaptcha.ready(function() {
            grecaptcha.execute('{{ config('services.recaptcha.site_key') }}', {action: 'reset'}).then(function(token) {
                document.getElementById('recaptchaToken').value = token;
                form.submit();
            });
        });
    });
});
</script>
@endsection
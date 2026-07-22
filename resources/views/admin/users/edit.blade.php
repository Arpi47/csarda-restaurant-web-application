@extends('admin.layouts.app')
@section('title', __('messages.edit_user'))
@section('content')
<h1>{{ __('messages.edit_user') }}</h1>
@if(session('success'))
    <div style="color:green">{{ session('success') }}</div>
@endif
<form method="POST" action="{{ route('admin.users.update', $user) }}" enctype="multipart/form-data" class="reservation-form menu-form" id="edit-user-form">
    @csrf
    @method('PUT')
    <div class="form-group">
        <label for="name">{{ __('messages.name') }}</label>
        <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" @error('name') class="is-invalid" @enderror>
        @error('name')
            <div class="text-danger">{{ $message }}</div>
        @enderror
    </div>
    <div class="form-group">
        <label for="email">{{ __('messages.email') }}</label>
        <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" @error('email') class="is-invalid" @enderror>
        @error('email')
            <div class="text-danger">{{ $message }}</div>
        @enderror
    </div>
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
    {{-- <div class="form-group">
        <label for="avatar">{{ __('messages.avatar') }}</label>
        @if($user->profile_image)
            <div>
                <img src="{{ asset('storage/' . $user->profile_image) }}" alt="Profile Image" style="max-width:150px; margin-top:5px;">
            </div>
        @endif
        <input type="file" id="avatar" name="avatar" @error('avatar') class="is-invalid" @enderror>
        @error('avatar')
            <div class="text-danger">{{ $message }}</div>
        @enderror        
    </div> --}}
    <div class="form-group">
        @error('captcha')
            <div class="text-danger">{{ $message }}</div>
        @enderror
    </div>
    <button type="submit" class="reserve-btn">{{ __('messages.update') }}</button>
</form>
<script src="https://www.google.com/recaptcha/api.js?render={{ env('RECAPTCHA_SITE_KEY') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('edit-user-form');
    form.addEventListener('submit', function (e) {
        e.preventDefault();
        grecaptcha.ready(function() {
            grecaptcha.execute('{{ env('RECAPTCHA_SITE_KEY') }}', {action: 'edit_user'}).then(function(token) {
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
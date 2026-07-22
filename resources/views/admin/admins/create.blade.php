@extends('admin.layouts.app')
@section('title', __('messages.create_admin'))
@section('content')
<h1>{{ __('messages.create_admin') }}</h1>
@if(session('success'))
    <div style="color:green">{{ session('success') }}</div>
@endif
<form method="POST" action="{{ route('admin.admins.store') }}" enctype="multipart/form-data" class="reservation-form menu-form">
    @csrf
    <div class="form-group">
        <label for="name">{{ __('messages.name') }}</label>
        <input type="text" id="name" name="name" 
               value="{{ old('name') }}" 
               @error('name') class="is-invalid" @enderror>
        @error('name')
            <div class="text-danger">{{ $message }}</div>
        @enderror
    </div>
    <div class="form-group">
        <label for="email">{{ __('messages.email') }}</label>
        <input type="email" id="email" name="email" 
               value="{{ old('email') }}" 
               @error('email') class="is-invalid" @enderror>
        @error('email')
            <div class="text-danger">{{ $message }}</div>
        @enderror
    </div>
    <div class="form-group">
        <label for="password">{{ __('messages.password') }}</label>
        <small>({{ __('messages.leave_blank') }})</small>
        <input type="password" id="password" name="password" 
               @error('password') class="is-invalid" @enderror>
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
    <button type="submit" class="reserve-btn">{{ __('messages.save') }}</button>
</form>
<script>
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
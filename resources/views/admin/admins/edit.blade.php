@extends('admin.layouts.app')
@section('title', __('messages.edit_admin'))
@section('content')
    <h1>{{ __('messages.edit_admin') }}</h1>
    @if (session('success'))
        <div style="color:green">{{ session('success') }}</div>
    @endif
    <form method="POST" action="{{ route('admin.admins.update', $admin) }}" enctype="multipart/form-data"
        class="reservation-form menu-form">
        @csrf
        @method('PUT')
        <div class="form-group">
            <label for="name">{{ __('messages.name') }}</label>
            <input type="text" id="name" name="name" value="{{ old('name', $admin->name) }}"
                @error('name') class="is-invalid" @enderror>
            @error('name')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
        @if (auth('admin')->user()->is_super_admin)
            <div class="form-group">
                <label for="email">{{ __('messages.email') }}</label>
                <input type="email" name="email" value="{{ old('email', $admin->email) }}">
            </div>
        @endif
        <div class="form-group">
            <label for="avatar">{{ __('messages.avatar') }}</label>
            <div style="margin-top:5px;">
                <img id="avatarPreview" src="{{ $admin->profile_image_url }}" alt="Profile"
                    style="width:100px;height:100px;object-fit:cover;border:1px solid rgba(0,0,0,0.2);border-radius:5px">
            </div>
            <input type="file" id="avatar" name="avatar" accept="image/*"
                @error('avatar') class="is-invalid" @enderror>
            @error('avatar')
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
        <button type="submit" class="reserve-btn">{{ __('messages.update') }}</button>
    </form>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const avatarInput = document.getElementById('avatar');
            const avatarPreview = document.getElementById('avatarPreview');
            if (avatarInput && avatarPreview) {
                avatarInput.addEventListener('change', function(event) {
                    const file = event.target.files[0];
                    if (file) {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            avatarPreview.src = e.target.result;
                            avatarPreview.alt = file.name;
                        }
                        reader.readAsDataURL(file);
                    }
                });
            }
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
@endsection

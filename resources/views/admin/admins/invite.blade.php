@extends('admin.layouts.app')
@section('title', __('messages.invite_admin'))
@section('content')
    <h1>{{ __('messages.invite_admin') }}</h1>
    @if (session('success'))
        <div style="color:green">{{ session('success') }}</div>
    @endif
    <form method="POST" action="{{ route('admin.admins.invite.store') }}" class="reservation-form menu-form">
        @csrf
        <div class="form-group">
            <label for="email">{{ __('messages.email') }}</label>
            <input type="email" id="email" name="email" required value="{{ old('email') }}"
                @error('email') class="is-invalid" @enderror>
            @error('email')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
        <div class="form-group">
            <label for="locale">{{ __('messages.language') }}</label>
            <select id="locale" name="locale" onchange="previewEmail()">
                <option value="sr" {{ old('locale') == 'sr' ? 'selected' : '' }}>Srpski</option>
                <option value="sr_cyrl" {{ old('locale') == 'sr_cyrl' ? 'selected' : '' }}>Српски</option>
                <option value="en" {{ old('locale') == 'en' ? 'selected' : '' }}>English</option>
                <option value="hu" {{ old('locale') == 'hu' ? 'selected' : '' }}>Magyar</option>
            </select>
        </div>
        <button type="submit" class="reserve-btn">{{ __('messages.send_invitation') }}</button>
    </form>
    <hr>
    <h3>{{ __('messages.email_preview') }}</h3>
    <iframe id="emailPreview" style="width:100%; height:300px; border:1px solid #ccc;"></iframe>
    <script>
        function previewEmail() {
            const locale = document.getElementById('locale').value;
            const url = "{{ route('admin.admin.email.preview') }}?locale=" + locale;
            document.getElementById('emailPreview').src = url;
        }
        document.addEventListener('DOMContentLoaded', previewEmail);
    </script>
@endsection

@extends('admin.layouts.app')
@section('title', 'Invite Admin')
@section('content')
    <h1>Invite a new Admin</h1>
    @if (session('success'))
        <div style="color:green">{{ session('success') }}</div>
    @endif
    <form method="POST" action="{{ route('admin.admins.invite.store') }}">
        @csrf
        <label for="email">Admin Email:</label>
        <input type="email" name="email" id="email" required>
        @error('email')
            <span style="color:red">{{ $message }}</span>
        @enderror
        <br><br>
        <button type="submit">Send Invitation</button>
    </form>
@endsection

@extends('admin.layouts.app')
@section('title', __('messages.dashboard'))
@section('content')
    <h1>{{ __('messages.dashboard') }}</h1>
    {{-- <p>__('messages.welcome')</p> --}}
    <div class="dashboard-buttons">
        <a href="{{ route('admin.menu.index') }}" class="dashboard-btn">
            {{ __('messages.menu_management') }}
        </a>
        <a href="{{ route('admin.categories.index') }}" class="dashboard-btn">
            {{ __('messages.category_management') }}
        </a>
        <a href="{{ route('admin.reservations.index') }}" class="dashboard-btn">
            {{ __('messages.reservations') }}
        </a>
        <a href="{{ route('admin.opening-hours.index') }}" class="dashboard-btn">
            {{ __('messages.opening_hours') }}
        </a>
        @if (auth('admin')->user()->is_super_admin)
            <a href="{{ route('admin.admins.invite') }}" class="dashboard-btn">
                {{ __('messages.invite_admin') }}
            </a>
            <a href="{{ route('admin.admin.activity.index') }}" class="dashboard-btn">
                {{ __('messages.admin_activity_log') }}
            </a>
        @endif
        <a href="{{ route('admin.admins.index') }}" class="dashboard-btn">
            {{ __('messages.admin_management') }}
        </a>
        <a href="{{ route('admin.users.index') }}" class="dashboard-btn">
            {{ __('messages.user_management') }}
        </a>
        <a href="{{ route('admin.gallery.index') }}" class="dashboard-btn">
            {{ __('messages.gallery_management') }}
        </a>
        <a href="{{ route('admin.contact.index') }}" class="dashboard-btn">
            {{ __('messages.contact_management') }}
        </a>
    </div>
@endsection

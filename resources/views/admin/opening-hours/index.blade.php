@extends('admin.layouts.app')
@section('title', __('messages.opening_hours'))
@section('content')
    <div class="admin-dashboard opening-hours-dashboard">
        <h1>{{ __('messages.opening_hours') }}</h1>
        <div class="dashboard-sections">
            <section class="dashboard-section">
                <h2>{{ __('messages.opening_hours_management') }}</h2>
                <div class="dashboard-buttons">
                    <a href="{{ route('admin.opening-hours.opening-hours') }}" class="dashboard-btn">
                        {{ __('messages.opening_hours') }}
                    </a>
                    <a href="{{ route('admin.opening-hours.special-opening-hours') }}" class="dashboard-btn">
                        {{ __('messages.special_opening_hours') }}
                    </a>
                    <a href="{{ route('admin.opening-hours.serbian-holidays') }}" class="dashboard-btn">
                        {{ __('messages.serbian_holidays') }}
                    </a>
                    <a href="{{ route('admin.opening-hours.hungarian-holidays') }}" class="dashboard-btn">
                        {{ __('messages.hungarian_holidays') }}
                    </a>
                </div>
            </section>
        </div>
    </div>
@endsection

@extends('layouts.app')
@section('title', __('messages.welcome'))
@section('left-sidebar')
    <h3>{{ __('messages.promotions') }}</h3>
    <p>🔥 Daily menu</p>
    <p>🍷 Wine discount</p>
@endsection
@section('content')
    <div class="home">
        <div class="intro">
            <h1>{{ __('messages.welcome') }}</h1>
            <p>{{ __('messages.home_description') }}</p>
        </div>
        <div class="banner">
            <img src="{{ asset('images/banner.jpg') }}" alt="Csarda Banner">
        </div>
        <div class="app-download">
            <h2>{{ __('messages.download_app') }}</h2>
            <p>
                <a href="https://yourappstorelink.com">
                    <img src="{{ asset('images/qr-code.png') }}" alt="QR Code">
                </a>
            </p>
            <p>{{ __('messages.scan_qr') }}</p>
        </div>
    </div>
@endsection
@section('right-sidebar')
    <h3>{{ __('messages.events') }}</h3>
    <p>🎶 Live music Friday</p>
    <p>📱 Mobile app available</p>
@endsection

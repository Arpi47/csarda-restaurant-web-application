<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', __('messages.admin_login'))</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flag-icons@7.5.0/css/flag-icons.min.css">
</head>
@php
    $hour = now()->hour;
    $theme = session('theme', 'auto');
    if ($theme === 'auto') {
        $theme = $hour >= 18 || $hour < 6 ? 'dark' : 'light';
    }
@endphp

<body class="auth {{ $theme }}">
    <nav class="auth-navbar">
        <div class="auth-language">
            <strong>{{ __('messages.language') }}:</strong>
            <select id="language-selector">
                <option value="{{ route('admin.lang', ['locale' => 'en']) }}"
                    {{ app()->getLocale() === 'en' ? 'selected' : '' }}>
                    🇬🇧 EN
                </option>
                <option value="{{ route('admin.lang', ['locale' => 'sr']) }}"
                    {{ app()->getLocale() === 'sr' ? 'selected' : '' }}>
                    🇷🇸 SR
                </option>
                <option value="{{ route('admin.lang', ['locale' => 'sr_cyrl']) }}"
                    {{ app()->getLocale() === 'sr_cyrl' ? 'selected' : '' }}>
                    🇷🇸 CP
                </option>
                <option value="{{ route('admin.lang', ['locale' => 'hu']) }}"
                    {{ app()->getLocale() === 'hu' ? 'selected' : '' }}>
                    🇭🇺 HU
                </option>
            </select>
        </div>
        <script>
            document
                .getElementById('language-selector')
                .addEventListener('change', function() {
                    window.location.href = this.value;
                });
        </script>
        <div class="auth-theme">
            <strong>{{ __('messages.theme') }}:</strong>
            <form method="POST" action="{{ url('/theme') }}">
                @csrf
                <select name="theme" onchange="this.form.submit()">
                    <option value="auto" {{ session('theme', 'auto') === 'auto' ? 'selected' : '' }}>
                        {{ __('messages.theme_auto') }}
                    </option>
                    <option value="light" {{ session('theme') === 'light' ? 'selected' : '' }}>
                        {{ __('messages.theme_light') }}
                    </option>
                    <option value="dark" {{ session('theme') === 'dark' ? 'selected' : '' }}>
                        {{ __('messages.theme_dark') }}
                    </option>
                </select>
            </form>
        </div>
    </nav>
    <div class="content auth-content">
        @yield('content')
    </div>
</body>

</html>

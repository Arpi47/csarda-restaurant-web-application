<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', __('messages.admin_login'))</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flag-icons@7.5.0/css/flag-icons.min.css">
    <script>
        window.addEventListener('pageshow', function(event) {
            if (event.persisted) {
                window.location.reload();
            }
        });
    </script>
</head>
@php
    $timezone = session('admin_timezone', 'Europe/Belgrade');
    $hour = now($timezone)->hour;
    $theme = session('theme', 'auto');
    if ($theme === 'auto') {
        $theme = $hour >= 18 || $hour < 6 ? 'dark' : 'light';
} @endphp

<body class="auth {{ $theme }}">
    <nav class="auth-navbar">
        <div class="auth-language">
            <strong>{{ __('messages.language') }}:</strong>
            @php
                $currentLocale = app()->getLocale();
                $languages = [
                    'en' => ['flag' => 'gb', 'label' => 'EN'],
                    'sr' => ['flag' => 'rs', 'label' => 'SR'],
                    'sr_cyrl' => ['flag' => 'rs', 'label' => 'CP'],
                    'hu' => ['flag' => 'hu', 'label' => 'HU'],
                ];
            @endphp
            <div class="language-selector" id="language-selector">
                <button type="button" class="language-current">
                    <span class="fi fi-{{ $languages[$currentLocale]['flag'] }}"></span>
                    <span>{{ $languages[$currentLocale]['label'] }}</span>
                    <span class="language-arrow">▾</span>
                </button>
                <div class="language-options">
                    @foreach ($languages as $locale => $language)
                        <a href="{{ route('admin.lang', ['locale' => $locale]) }}"
                            class="{{ $currentLocale === $locale ? 'active' : '' }}">
                            <span class="fi fi-{{ $language['flag'] }}"></span>
                            <span>{{ $language['label'] }}</span>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
        <div class="auth-theme">
            <strong>{{ __('messages.theme') }}:</strong>
            <div class="theme-selector" id="theme-selector">
                <button type="button" class="theme-current">
                    @if (session('theme', 'auto') === 'auto')
                        {{ __('messages.theme_auto') }}
                    @elseif (session('theme') === 'light')
                        {{ __('messages.theme_light') }}
                    @else
                        {{ __('messages.theme_dark') }}
                    @endif
                    <span class="theme-arrow">▾</span>
                </button>
                <div class="theme-options">
                    <form method="POST" action="{{ url('/theme') }}">
                        @csrf
                        <button type="submit" name="theme" value="auto">
                            {{ __('messages.theme_auto') }}
                        </button>
                        <button type="submit" name="theme" value="light">
                            {{ __('messages.theme_light') }}
                        </button>
                        <button type="submit" name="theme" value="dark">
                            {{ __('messages.theme_dark') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const languageSelector = document.getElementById('language-selector');
                const themeSelector = document.getElementById('theme-selector');
                const languageButton = languageSelector.querySelector('.language-current');
                const themeButton = themeSelector.querySelector('.theme-current');
                languageButton.addEventListener('click', function(event) {
                    event.stopPropagation();
                    languageSelector.classList.toggle('open');
                    themeSelector.classList.remove('open');
                });
                themeButton.addEventListener('click', function(event) {
                    event.stopPropagation();
                    themeSelector.classList.toggle('open');
                    languageSelector.classList.remove('open');
                });
                document.addEventListener('click', function(event) {
                    if (!languageSelector.contains(event.target)) {
                        languageSelector.classList.remove('open');
                    }
                    if (!themeSelector.contains(event.target)) {
                        themeSelector.classList.remove('open');
                    }
                });
            });
        </script>
    </nav>
    <div class="content auth-content">
        @yield('content')
    </div>
    @include('components.admin-footer')
</body>

</html>

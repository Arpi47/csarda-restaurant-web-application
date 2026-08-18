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
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const selector = document.getElementById('language-selector');
                const button = selector.querySelector('.language-current');
                button.addEventListener('click', function(event) {
                    event.stopPropagation();
                    selector.classList.toggle('open');
                });
                document.addEventListener('click', function(event) {
                    if (!selector.contains(event.target)) {
                        selector.classList.remove('open');
                    }
                });
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

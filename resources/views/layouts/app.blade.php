<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Csárda')</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <style>
        .profile-dropdown {
            position:absolute; 
            right: 50px;
        }
        @media (max-width: 530px) {
            .profile-dropdown {
                right: 0;
            }
        }
    </style>
    <script>
        function toggleMenu(e) {
            e.stopPropagation();
            const menu = document.getElementById('hamburger-menu');
            const overlay = document.getElementById('menu-overlay');
            const icon = document.getElementById('hamburger-icon');
            const isOpen = menu.classList.toggle('open');
            overlay.classList.toggle('show', isOpen);
            icon.textContent = isOpen ? '✕' : '☰';
        }
        function closeMenu() {
            document.getElementById('hamburger-menu').classList.remove('open');
            document.getElementById('menu-overlay').classList.remove('show');
            document.getElementById('hamburger-icon').textContent = '☰';
        }
    </script>
</head>
<body
@php
    $hour = now()->hour;
    $theme = session('theme', 'auto');
    if ($theme === 'auto') {
        $theme = ($hour >= 18 || $hour < 6) ? 'dark' : 'light';
    }
@endphp
class="{{ $theme }}"
>
<div id="menu-overlay" class="menu-overlay" onclick="closeMenu()"></div>
<button class="hamburger" onclick="toggleMenu(event)">
    <span id="hamburger-icon">☰</span>
</button>
<nav class="navbar">
    <div class="nav-left">
        <a href="{{ url('/') }}">{{ __('messages.home') }}</a>
        <a href="{{ url('/menu') }}">{{ __('messages.menu') }}</a>
        <a href="{{ url('/reservation') }}">{{ __('messages.reservation') }}</a>
        <a href="{{ url('/contact') }}">{{ __('messages.contact') }}</a>
    </div>
    <div class="nav-right">
        @auth('web')
            <div class="profile-dropdown">
                <button
                    id="userProfileBtn"
                    onclick="event.stopPropagation(); toggleUserDropdown()"
                    style="border:none; background:transparent; cursor:pointer;"
                >
                    <img
                        src="{{ auth()->user()->profile_image_url }}"
                        alt="Profile"
                        style="width:40px;height:40px;border-radius:50%;border:2px solid #ccc;object-fit:cover;"
                    >
                </button>
                <div id="userDropdown" class="profile-dropdown-menu">
                    <div class="profile-info">
                        <a href="{{ route('user.profile.edit') }}">
                            <img
                                src="{{ auth()->user()->profile_image_url }}"
                                alt="Profile"
                                style="width:60px;height:60px;border-radius:50%;border:2px solid #ccc;object-fit:cover;"
                            >
                        </a>
                        <div>
                            <strong>{{ auth()->user()->full_name }}</strong>
                            <span>{{ auth()->user()->email }}</span>
                        </div>
                    </div>
                    <a href="{{ route('user.reservations.index') }}" class="btn-reservations">
                        {{ __('messages.my_reservations') }}
                    </a>
                    <div class="logout-section">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit">{{ __('messages.logout') }}</button>
                        </form>
                    </div>
                </div>
            </div>
        @else
            <a href="{{ route('login') }}" class="btn">{{ __('messages.login') }}</a>
        @endauth
    </div>
</nav>
<script>
    function toggleUserDropdown() {
        const dropdown = document.getElementById('userDropdown');
        dropdown.style.display = dropdown.style.display === 'flex' ? 'none' : 'flex';
    }
    document.addEventListener('click', function (event) {
        const dropdown = document.getElementById('userDropdown');
        const button = document.getElementById('userProfileBtn');
        if (dropdown && button && !button.contains(event.target) && !dropdown.contains(event.target)) {
            dropdown.style.display = 'none';
        }
    });
</script>
<div id="hamburger-menu" class="hamburger-menu">
    <div class="menu-section">
        <strong>{{ __('messages.language') }}</strong>
        <a href="{{ url('lang/en') }}">🇬🇧 English</a>
        <a href="{{ url('lang/sr') }}">🇷🇸 Srpski</a>
        <a href="{{ url('lang/sr_cyrl') }}">🇷🇸 Српски</a>
        <a href="{{ url('lang/hu') }}">🇭🇺 Magyar</a>   
    </div>
    <hr>
    <div class="menu-section">
        <strong>{{ __('messages.theme') }}</strong>
        <form method="POST" action="{{ url('/theme') }}">
            @csrf
            <select name="theme" onchange="this.form.submit()" class="theme-select">
                <option value="auto" {{ session('theme','auto')=='auto'?'selected':'' }}>
                    {{ __('messages.theme_auto') }}
                </option>
                <option value="light" {{ session('theme')=='light'?'selected':'' }}>
                    {{ __('messages.theme_light') }}
                </option>
                <option value="dark" {{ session('theme')=='dark'?'selected':'' }}>
                    {{ __('messages.theme_dark') }}
                </option>
            </select>
        </form>
    </div>
</div>
<div class="layout">
    @if (request()->is('menu'))
        <aside class="left-container">
            @yield('left-container')
        </aside>
    @else
        <aside class="sidebar left">
            @yield('left-sidebar')
        </aside>
    @endif
    <main class="content">
        @yield('content')
    </main>
    @if (!request()->is('menu'))
        <aside class="sidebar right">
            @yield('right-sidebar')
        </aside>
    @endif
</div>
@stack('scripts')
</body>
</html>
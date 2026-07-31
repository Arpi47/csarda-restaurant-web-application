<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Panel')</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flag-icons@7.5.0/css/flag-icons.min.css">
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

        function toggleDropdown(id) {
            const el = document.getElementById(id);
            el.style.display = (el.style.display === 'flex') ? 'none' : 'flex';
        }
        document.addEventListener('click', function(event) {
            const dropdown = document.getElementById('adminDropdown');
            const button = document.getElementById('adminProfileBtn');
            if (!button.contains(event.target) && !dropdown.contains(event.target)) {
                dropdown.style.display = 'none';
            }
        });
    </script>
</head>

<body
    @php
$hour = now()->hour;
            $theme = session('theme', 'auto');
            if($theme === 'auto'){
                $theme = ($hour >= 18 || $hour < 6) ? 'dark' : 'light';
            } @endphp
    class="admin {{ $theme }}">
    @php
        $backRoute = null;
        if (
            request()->routeIs(
                'admin.reservations.index',
                'admin.opening-hours.index',
                'admin.app-downloads.index',
                'admin.menu.index',
                'admin.categories.index',
                'admin.admins.index',
                'admin.users.index',
                'admin.admin.activity.index',
                'admin.gallery.index',
                'admin.contact.index',
                'admin.admins.edit',
            )
        ) {
            $backRoute = route('admin.dashboard');
        }

        if (request()->routeIs('admin.menu.create', 'admin.menu.edit')) {
            $backRoute = route('admin.menu.index');
        }

        if (request()->routeIs('admin.categories.create', 'admin.categories.edit')) {
            $backRoute = route('admin.categories.index');
        }
        if (request()->routeIs('admin.admins.invite')) {
            $backRoute = route('admin.admins.index');
        }
        if (request()->routeIs('admin.users.edit')) {
            $backRoute = route('admin.users.index');
        }
        if (request()->routeIs('admin.contact.social.edit')) {
            $backRoute = route('admin.contact.index');
        }
    @endphp
    <div id="menu-overlay" class="menu-overlay" onclick="closeMenu()"></div>
    <button class="hamburger" onclick="toggleMenu(event)">
        <span id="hamburger-icon">☰</span>
    </button>
    <nav class="navbar">
        <div class="nav-left">
            @if ($backRoute)
                <a href="{{ $backRoute }}">
                    {{ __('messages.back') }}
                </a>
            @endif
        </div>
        <div class="nav-right">
            @auth('admin')
                @include('components.admin-dropdown')
            @else
                <a href="{{ route('admin.login') }}" class="btn">{{ __('messages.login') }}</a>
            @endauth
        </div>
    </nav>
    <div id="hamburger-menu" class="hamburger-menu">
        <div class="menu-section">
            <strong>{{ __('messages.language') }}</strong>
            <a href="{{ route('admin.lang', ['locale' => 'en']) }}">
                <span class="fi fi-gb admin-flag"></span>
                English
            </a>
            <a href="{{ route('admin.lang', ['locale' => 'sr']) }}">
                <span class="fi fi-rs admin-flag"></span>
                Srpski
            </a>
            <a href="{{ route('admin.lang', ['locale' => 'sr_cyrl']) }}">
                <span class="fi fi-rs admin-flag"></span>
                Српски
            </a>
            <a href="{{ route('admin.lang', ['locale' => 'hu']) }}">
                <span class="fi fi-hu admin-flag"></span>
                Magyar
            </a>
        </div>
        <hr>
        <div class="menu-section">
            <strong>{{ __('messages.theme') }}</strong>
            <form method="POST" action="{{ url('/theme') }}">
                @csrf
                <select name="theme" onchange="this.form.submit()">
                    <option value="auto" {{ session('theme', 'auto') == 'auto' ? 'selected' : '' }}>
                        {{ __('messages.theme_auto') }}
                    </option>
                    <option value="light" {{ session('theme') == 'light' ? 'selected' : '' }}>
                        {{ __('messages.theme_light') }}
                    </option>
                    <option value="dark" {{ session('theme') == 'dark' ? 'selected' : '' }}>
                        {{ __('messages.theme_dark') }}
                    </option>
                </select>
            </form>
        </div>
    </div>
    <div class="content">
        @yield('content')
    </div>
    <script>
        function toggleMenu(e) {
            e.stopPropagation();
            const menu = document.getElementById('hamburger-menu');
            const overlay = document.getElementById('menu-overlay');
            const icon = document.getElementById('hamburger-icon');
            const dropdown = document.getElementById('adminDropdown');
            const isOpen = menu.classList.toggle('open');

            if (isOpen && dropdown) {
                dropdown.style.display = 'none';
            }
            overlay.classList.toggle('show', isOpen);
            icon.textContent = isOpen ? '✕' : '☰';
        }

        function closeMenu() {
            const menu = document.getElementById('hamburger-menu');
            const overlay = document.getElementById('menu-overlay');
            const icon = document.getElementById('hamburger-icon');
            if (menu) {
                menu.classList.remove('open');
            }
            if (overlay) {
                overlay.classList.remove('show');
            }
            if (icon) {
                icon.textContent = '☰';
            }
        }

        function toggleDropdown(id) {
            const el = document.getElementById(id);
            if (!el) {
                return;
            }
            const isOpen = el.style.display === 'flex';
            if (!isOpen && id === 'adminDropdown') {
                closeMenu();
            }

            el.style.display = isOpen ? 'none' : 'flex';
        }

        function closeDropdown() {
            const dropdown = document.getElementById('adminDropdown');

            if (dropdown) {
                dropdown.style.display = 'none';
            }
        }
        document.addEventListener('click', function(event) {
            const dropdown = document.getElementById('adminDropdown');
            const button = document.getElementById('adminProfileBtn');
            const menu = document.getElementById('hamburger-menu');
            const hamburger = document.querySelector('.hamburger');
            if (
                dropdown &&
                button &&
                !button.contains(event.target) &&
                !dropdown.contains(event.target)
            ) {
                closeDropdown();
            }
            if (
                menu &&
                hamburger &&
                menu.classList.contains('open') &&
                !menu.contains(event.target) &&
                !hamburger.contains(event.target)
            ) {
                closeMenu();
            }
        });
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closeMenu();
                closeDropdown();
            }
        });
    </script>
</body>

</html>

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Admin Login')</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>

<body class="auth">
    <div class="content auth-content">
        @yield('content')
    </div>
</body>

</html>

<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class AdminSettings
{
    public function handle(Request $request, Closure $next)
    {
        $lang = session('admin_locale', 'en');
        App::setLocale($lang);

        $theme = session('theme', 'light');
        view()->share('theme', $theme);

        return $next($request);
    }
}

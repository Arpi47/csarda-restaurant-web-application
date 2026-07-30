<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class AdminSettings
{
    public function handle(Request $request, Closure $next)
    {
        $lang = session('locale', 'en');
        App::setLocale($lang);
        $theme = session('theme', 'auto');
        view()->share('theme', $theme);
        return $next($request);
    }
}

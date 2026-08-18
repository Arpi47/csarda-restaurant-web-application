<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RedirectIfNotAdmin
{
    public function handle(Request $request, Closure $next)
    {
        $admin = auth('admin')->user();
        if (! $admin) {
            return redirect()->route('admin.login');
        }
        if ($admin->is_suspended) {
            auth('admin')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect()
                ->route('admin.login')
                ->withErrors([
                    'email' => __('messages.account_suspended'),
                ]);
        }
        return $next($request);
    }
}
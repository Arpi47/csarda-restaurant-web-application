<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureSuperAdmin
{
    public function handle(Request $request, Closure $next)
    {
        $admin = auth('admin')->user();
        if (!$admin || !$admin->is_super_admin) {
            // A hibaüzenet a kiválasztott nyelven
            abort(403, __('messages.super_admin_only'));
        }
        return $next($request);
    }
}

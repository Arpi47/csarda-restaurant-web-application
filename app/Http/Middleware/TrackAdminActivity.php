<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\AdminActivityLog;
use Illuminate\Support\Facades\Auth;

class TrackAdminActivity
{
    public function handle(Request $request, Closure $next)
    {
        $admin = Auth::guard('admin')->user();
        if ($admin) {
            $action = null;
            $subjectType = null;
            $subjectId = null;

            if ($request->isMethod('post')) {
                $action = 'created';
            } elseif ($request->isMethod('put') || $request->isMethod('patch')) {
                $action = 'updated';
            } elseif ($request->isMethod('delete')) {
                $action = 'deleted';
            }
            $routeParameters = $request->route()->parameters();
            if (!empty($routeParameters)) {
                foreach ($routeParameters as $param) {
                    if (is_object($param)) {
                        $subjectType = class_basename($param);
                        $subjectId = $param->id ?? null;
                        break;
                    }
                }
            }
            if ($request->routeIs('admin.login.submit')) {
                $action = 'login';
            } elseif ($request->routeIs('admin.logout')) {
                $action = 'logout';
            }
            if ($action) {
                AdminActivityLog::create([
                    'admin_id' => $admin->id,
                    'action' => $action,
                    'subject_type' => $subjectType,
                    'subject_id' => $subjectId,
                    'route' => $request->path(),
                    'method' => $request->method(),
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ]);
            }
            $admin->update(['last_activity_at' => now()]);
        }
        return $next($request);
    }
}
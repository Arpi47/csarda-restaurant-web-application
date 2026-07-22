<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminActivityLog;
use App\Models\Admin;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AdminActivityController extends Controller
{
    public function index(Request $request)
    {
        $admin_id = $request->query('admin_id');
        $action = $request->query('action');
        $date_from = $request->query('date_from');
        $date_to = $request->query('date_to');

        $query = AdminActivityLog::with('admin')->orderByDesc('created_at');

        if ($admin_id) {
            $query->where('admin_id', $admin_id);
        }

        if ($action) {
            $query->where('action', $action);
        }

        $tz = session('admin_timezone', config('app.timezone'));

        if ($date_from) {
            $query->where(
                'created_at',
                '>=',
                Carbon::parse($date_from, $tz)->utc()
            );
        }

        if ($date_to) {
            $query->where(
                'created_at',
                '<=',
                Carbon::parse($date_to, $tz)->utc()
            );
        }

        $logs = $query->limit(100)->get();
        $admins = Admin::orderBy('name')->get();
        $actions = AdminActivityLog::select('action')->distinct()->pluck('action');

        return view('admin.admins.activity', compact(
            'logs', 'admins', 'actions', 'admin_id', 'action', 'date_from', 'date_to'
        ));
    }
}

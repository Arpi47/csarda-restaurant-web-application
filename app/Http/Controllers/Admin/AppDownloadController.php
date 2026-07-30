<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AppDownload;
use Illuminate\Http\Request;

class AppDownloadController extends Controller
{
    public function index()
    {
        $downloads = AppDownload::whereIn('platform', [
            'google_play',
            'app_store',
        ])
            ->orderBy('platform')
            ->get()
            ->keyBy('platform');

        return view('admin.app-downloads.index', compact('downloads'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'google_play' => [
                'required',
                'url',
                'max:2048',
            ],
            'app_store' => [
                'required',
                'max:2048',
            ],
        ]);
        AppDownload::updateOrCreate(
            ['platform' => 'google_play'],
            ['url' => $validated['google_play']]
        );
        AppDownload::updateOrCreate(
            ['platform' => 'app_store'],
            ['url' => $validated['app_store']]
        );
        return redirect()
            ->route('admin.app-downloads.index')
            ->with('success', __('messages.app_downloads_updated'));
    }
}
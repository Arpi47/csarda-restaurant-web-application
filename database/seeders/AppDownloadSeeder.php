<?php

namespace Database\Seeders;

use App\Models\AppDownload;
use Illuminate\Database\Seeder;

class AppDownloadSeeder extends Seeder
{
    public function run(): void
    {
        AppDownload::create([
            'platform' => 'google_play',
            'url' => 'https://play.google.com/store/apps',
        ]);

        AppDownload::create([
            'platform' => 'app_store',
            'url' => 'itms-apps://://apple.com',
        ]);
    }
}
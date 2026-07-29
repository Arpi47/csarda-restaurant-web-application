<?php

namespace Database\Seeders;

use App\Models\ContactSetting;
use Illuminate\Database\Seeder;

class ContactSettingSeeder extends Seeder
{
    public function run(): void
    {
        ContactSetting::create([
            'platform' => 'facebook',
            'url' => '#',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        ContactSetting::create([
            'platform' => 'instagram',
            'url' => '#',
            'sort_order' => 2,
            'is_active' => true,
        ]);

        ContactSetting::create([
            'platform' => 'tiktok',
            'url' => '#',
            'sort_order' => 3,
            'is_active' => true,
        ]);

        ContactSetting::create([
            'platform' => 'youtube',
            'url' => '#',
            'sort_order' => 4,
            'is_active' => true,
        ]);
    }
}

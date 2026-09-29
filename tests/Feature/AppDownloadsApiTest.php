<?php

namespace Tests\Feature;

use App\Models\AppDownload;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AppDownloadsApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_app_downloads_endpoint_returns_only_supported_platforms_keyed_by_platform(): void
    {
        AppDownload::create([
            'platform' => 'google_play',
            'url' => 'https://play.google.com/store/apps/details?id=test',
        ]);

        AppDownload::create([
            'platform' => 'app_store',
            'url' => 'https://apps.apple.com/app/test',
        ]);

        AppDownload::create([
            'platform' => 'windows_store',
            'url' => 'https://apps.microsoft.com/detail/test',
        ]);

        $response = $this->getJson('/api/app-downloads');

        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'google_play' => [
                    'platform',
                    'url',
                ],
                'app_store' => [
                    'platform',
                    'url',
                ],
            ])
            ->assertJsonPath(
                'google_play.platform',
                'google_play'
            )
            ->assertJsonPath(
                'google_play.url',
                'https://play.google.com/store/apps/details?id=test'
            )
            ->assertJsonPath(
                'app_store.platform',
                'app_store'
            )
            ->assertJsonPath(
                'app_store.url',
                'https://apps.apple.com/app/test'
            );

        $response->assertJsonMissing([
            'platform' => 'windows_store',
        ]);
    }
}

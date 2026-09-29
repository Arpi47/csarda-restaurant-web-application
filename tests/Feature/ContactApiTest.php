<?php

namespace Tests\Feature;

use App\Models\ContactInformation;
use App\Models\ContactSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContactApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_contact_endpoint_returns_contact_information_and_active_social_links_in_correct_order(): void
    {
        ContactInformation::create([
            'phone' => '+381 24 123 456',
            'email' => 'info@csarda.test',
        ]);

        ContactSetting::create([
            'platform' => 'facebook',
            'url' => 'https://facebook.com/csarda',
            'sort_order' => 2,
            'is_active' => true,
        ]);

        ContactSetting::create([
            'platform' => 'instagram',
            'url' => 'https://instagram.com/csarda',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        ContactSetting::create([
            'platform' => 'youtube',
            'url' => 'https://youtube.com/csarda',
            'sort_order' => 0,
            'is_active' => false,
        ]);

        $response = $this->getJson('/api/contact');

        $response
            ->assertStatus(200)
            ->assertJsonPath('information.phone', '+381 24 123 456')
            ->assertJsonPath('information.email', 'info@csarda.test')
            ->assertJsonCount(2, 'socialLinks')
            ->assertJsonPath('socialLinks.0.platform', 'instagram')
            ->assertJsonPath('socialLinks.0.url', 'https://instagram.com/csarda')
            ->assertJsonPath('socialLinks.0.sort_order', 1)
            ->assertJsonPath('socialLinks.1.platform', 'facebook')
            ->assertJsonPath('socialLinks.1.url', 'https://facebook.com/csarda')
            ->assertJsonPath('socialLinks.1.sort_order', 2);

        $response->assertJsonMissing([
            'platform' => 'youtube',
        ]);
    }
}

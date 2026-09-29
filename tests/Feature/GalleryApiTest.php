<?php

namespace Tests\Feature;

use App\Models\GalleryImage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GalleryApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_gallery_endpoint_returns_all_images_in_correct_order(): void
    {
        GalleryImage::create([
            'image' => 'gallery3.jpg',
            'order' => 3,
        ]);

        GalleryImage::create([
            'image' => 'gallery1.jpg',
            'order' => 1,
        ]);

        GalleryImage::create([
            'image' => 'gallery2.jpg',
            'order' => 2,
        ]);

        $response = $this->getJson('/api/gallery');

        $response
            ->assertStatus(200)
            ->assertJsonCount(3)
            ->assertJsonPath('0.image', 'gallery1.jpg')
            ->assertJsonPath('1.image', 'gallery2.jpg')
            ->assertJsonPath('2.image', 'gallery3.jpg');

        $response->assertJsonPath('0.order', 1);
        $response->assertJsonPath('1.order', 2);
        $response->assertJsonPath('2.order', 3);
    }
}

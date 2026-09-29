<?php

namespace Tests\Feature;

use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_categories_endpoint_returns_only_active_categories_in_correct_order(): void
    {
        Category::create([
            'name_hu' => 'Desszertek',
            'name_en' => 'Desserts',
            'name_sr_lat' => 'Dezerti',
            'name_sr_cyr' => 'Десерти',
            'sort_order' => 2,
            'is_active' => true,
        ]);

        Category::create([
            'name_hu' => 'Levesek',
            'name_en' => 'Soups',
            'name_sr_lat' => 'Supe',
            'name_sr_cyr' => 'Супе',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        Category::create([
            'name_hu' => 'Inaktív kategória',
            'name_en' => 'Inactive category',
            'name_sr_lat' => 'Neaktivna kategorija',
            'name_sr_cyr' => 'Неактивна категорија',
            'sort_order' => 0,
            'is_active' => false,
        ]);

        $response = $this->getJson('/api/categories');

        $response
            ->assertStatus(200)
            ->assertJsonCount(2)
            ->assertJsonPath('0.name_hu', 'Levesek')
            ->assertJsonPath('1.name_hu', 'Desszertek');

        $response->assertJsonMissing([
            'name_hu' => 'Inaktív kategória',
        ]);
    }
}
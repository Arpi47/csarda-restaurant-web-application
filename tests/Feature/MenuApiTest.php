<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Menu;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MenuApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_menu_endpoint_returns_only_active_items_from_active_categories_in_correct_order(): void
    {
        $mainCourses = Category::create([
            'name_hu' => 'Főételek',
            'name_en' => 'Main courses',
            'name_sr_lat' => 'Glavna jela',
            'name_sr_cyr' => 'Главна јела',
            'sort_order' => 2,
            'is_active' => true,
        ]);

        $soups = Category::create([
            'name_hu' => 'Levesek',
            'name_en' => 'Soups',
            'name_sr_lat' => 'Supe',
            'name_sr_cyr' => 'Супе',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        $inactiveCategory = Category::create([
            'name_hu' => 'Inaktív kategória',
            'name_en' => 'Inactive category',
            'name_sr_lat' => 'Neaktivna kategorija',
            'name_sr_cyr' => 'Неактивна категорија',
            'sort_order' => 0,
            'is_active' => false,
        ]);

        Menu::create([
            'category_id' => $mainCourses->id,
            'name_hu' => 'Rántott hús',
            'name_en' => 'Breaded meat',
            'name_sr_lat' => 'Bečka šnicla',
            'name_sr_cyr' => 'Бечка шницла',
            'description_hu' => 'Ropogós panírozott hús.',
            'description_en' => 'Crispy breaded meat.',
            'description_sr_lat' => 'Hrskavo pohovano meso.',
            'description_sr_cyr' => 'Хрскаво поховано месо.',
            'price' => 2500,
            'image' => 'test.jpg',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        Menu::create([
            'category_id' => $soups->id,
            'name_hu' => 'Gulyásleves',
            'name_en' => 'Goulash soup',
            'name_sr_lat' => 'Gulaš supa',
            'name_sr_cyr' => 'Гулаш супа',
            'description_hu' => 'Hagyományos gulyásleves.',
            'description_en' => 'Traditional goulash soup.',
            'description_sr_lat' => 'Tradicionalna gulaš supa.',
            'description_sr_cyr' => 'Традиционална гулаш супа.',
            'price' => 1800,
            'image' => 'test.jpg',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        Menu::create([
            'category_id' => $soups->id,
            'name_hu' => 'Húsleves',
            'name_en' => 'Broth',
            'name_sr_lat' => 'Supa sa mesom',
            'name_sr_cyr' => 'Супа са месом',
            'description_hu' => 'Hagyományos húsleves.',
            'description_en' => 'Traditional meat broth.',
            'description_sr_lat' => 'Tradicionalna supa sa mesom.',
            'description_sr_cyr' => 'Традиционална супа са месом.',
            'price' => 1600,
            'image' => 'test.jpg',
            'sort_order' => 2,
            'is_active' => true,
        ]);

        Menu::create([
            'category_id' => $soups->id,
            'name_hu' => 'Inaktív étel',
            'name_en' => 'Inactive dish',
            'name_sr_lat' => 'Neaktivno jelo',
            'name_sr_cyr' => 'Неактивно јело',
            'description_hu' => 'Inaktív étel leírása.',
            'description_en' => 'Inactive dish description.',
            'description_sr_lat' => 'Opis neaktivnog jela.',
            'description_sr_cyr' => 'Опис неактивног јела.',
            'price' => 1500,
            'image' => 'test.jpg',
            'sort_order' => 0,
            'is_active' => false,
        ]);

        Menu::create([
            'category_id' => $inactiveCategory->id,
            'name_hu' => 'Inaktív kategória étele',
            'name_en' => 'Dish from inactive category',
            'name_sr_lat' => 'Jelo iz neaktivne kategorije',
            'name_sr_cyr' => 'Јело из неактивне категорије',
            'description_hu' => 'Inaktív kategóriához tartozó étel.',
            'description_en' => 'Dish from an inactive category.',
            'description_sr_lat' => 'Jelo iz neaktivne kategorije.',
            'description_sr_cyr' => 'Јело из неактивне категорије.',
            'price' => 1400,
            'image' => 'test.jpg',
            'sort_order' => 0,
            'is_active' => true,
        ]);

        $response = $this->getJson('/api/menu');

        $response
            ->assertStatus(200)
            ->assertJsonCount(3)
            ->assertJsonPath('0.name_hu', 'Gulyásleves')
            ->assertJsonPath('1.name_hu', 'Húsleves')
            ->assertJsonPath('2.name_hu', 'Rántott hús');

        $response->assertJsonPath('0.category.name_hu', 'Levesek');
        $response->assertJsonPath('1.category.name_hu', 'Levesek');
        $response->assertJsonPath('2.category.name_hu', 'Főételek');

        $response->assertJsonMissing([
            'name_hu' => 'Inaktív étel',
        ]);

        $response->assertJsonMissing([
            'name_hu' => 'Inaktív kategória étele',
        ]);
    }
}
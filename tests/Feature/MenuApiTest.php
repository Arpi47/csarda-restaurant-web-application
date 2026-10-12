<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Menu;
use App\Models\MenuImage;
use App\Models\MenuReview;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
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

    public function test_menu_detail_endpoint_returns_active_item_with_category_and_ordered_images(): void
    {
        $category = Category::create([
            'name_hu' => 'Főételek',
            'name_en' => 'Main courses',
            'name_sr_lat' => 'Glavna jela',
            'name_sr_cyr' => 'Главна јела',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        $menu = Menu::create([
            'category_id' => $category->id,
            'name_hu' => 'Rántott hús',
            'name_en' => 'Breaded meat',
            'name_sr_lat' => 'Bečka šnicla',
            'name_sr_cyr' => 'Бечка шницла',
            'description_hu' => 'Ropogós panírozott hús.',
            'description_en' => 'Crispy breaded meat.',
            'description_sr_lat' => 'Hrskavo pohovano meso.',
            'description_sr_cyr' => 'Хрскаво поховано месо.',
            'price' => 2500,
            'image' => 'main.jpg',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        MenuImage::create([
            'menu_id' => $menu->id,
            'image' => 'second.jpg',
            'sort_order' => 2,
        ]);

        MenuImage::create([
            'menu_id' => $menu->id,
            'image' => 'first.jpg',
            'sort_order' => 1,
        ]);

        $response = $this->getJson("/api/menu/{$menu->id}");

        $response
            ->assertOk()
            ->assertJsonPath('id', $menu->id)
            ->assertJsonPath('name_hu', 'Rántott hús')
            ->assertJsonPath('image', 'main.jpg')
            ->assertJsonPath('category.name_hu', 'Főételek')
            ->assertJsonPath('images.0.image', 'first.jpg')
            ->assertJsonPath('images.1.image', 'second.jpg');
    }

    public function test_menu_reviews_endpoint_returns_only_approved_reviews_and_calculates_average(): void
    {
        $menu = $this->createMenuForReviewTests();

        $approvedUser = User::factory()->create([
            'first_name' => 'Anna',
            'last_name' => 'Teszt',
        ]);

        $pendingUser = User::factory()->create();

        MenuReview::create([
            'menu_id' => $menu->id,
            'user_id' => $approvedUser->id,
            'rating' => 5,
            'comment' => 'Nagyon finom!',
            'is_approved' => true,
        ]);

        MenuReview::create([
            'menu_id' => $menu->id,
            'user_id' => $pendingUser->id,
            'rating' => 1,
            'comment' => 'Még nem jóváhagyott vélemény.',
            'is_approved' => false,
        ]);

        $this->getJson("/api/menu/{$menu->id}/reviews")
            ->assertOk()
            ->assertJsonPath('reviews_count', 1)
            ->assertJsonPath('average_rating', 5)
            ->assertJsonPath('reviews.0.rating', 5)
            ->assertJsonPath('reviews.0.comment', 'Nagyon finom!')
            ->assertJsonPath('reviews.0.user.full_name', 'Anna Teszt')
            ->assertJsonMissing([
                'comment' => 'Még nem jóváhagyott vélemény.',
            ]);
    }

    public function test_menu_reviews_endpoint_returns_404_for_inactive_item(): void
    {
        $menu = $this->createMenuForReviewTests(false);

        $this->getJson("/api/menu/{$menu->id}/reviews")
            ->assertNotFound();
    }

    private function createMenuForReviewTests(bool $active = true): Menu
    {
        $category = Category::create([
            'name_hu' => 'Főételek',
            'name_en' => 'Main courses',
            'name_sr_lat' => 'Glavna jela',
            'name_sr_cyr' => 'Главна јела',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        return Menu::create([
            'category_id' => $category->id,
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
            'is_active' => $active,
        ]);
    }


    public function test_menu_detail_endpoint_returns_404_for_inactive_item(): void
    {
        $category = Category::create([
            'name_hu' => 'Főételek',
            'name_en' => 'Main courses',
            'name_sr_lat' => 'Glavna jela',
            'name_sr_cyr' => 'Главна јела',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        $menu = Menu::create([
            'category_id' => $category->id,
            'name_hu' => 'Inaktív étel',
            'name_en' => 'Inactive dish',
            'name_sr_lat' => 'Neaktivno jelo',
            'name_sr_cyr' => 'Неактивно јело',
            'description_hu' => 'Leírás.',
            'description_en' => 'Description.',
            'description_sr_lat' => 'Opis.',
            'description_sr_cyr' => 'Опис.',
            'price' => 1500,
            'image' => 'inactive.jpg',
            'sort_order' => 1,
            'is_active' => false,
        ]);

        $this->getJson("/api/menu/{$menu->id}")
            ->assertNotFound();
    }

    public function test_authenticated_user_can_submit_a_menu_review(): void
    {
        $menu = $this->createMenuForReviewTests();
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $this->postJson("/api/menu/{$menu->id}/reviews", [
            'rating' => 5,
            'comment' => 'Nagyon finom!',
        ])
            ->assertCreated()
            ->assertJsonPath('review.rating', 5)
            ->assertJsonPath('review.comment', 'Nagyon finom!')
            ->assertJsonPath('review.is_approved', false);

        $this->assertDatabaseHas('menu_reviews', [
            'menu_id' => $menu->id,
            'user_id' => $user->id,
            'rating' => 5,
            'is_approved' => false,
        ]);
    }

    public function test_user_cannot_submit_a_second_review_for_the_same_menu_item(): void
    {
        $menu = $this->createMenuForReviewTests();
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $this->postJson("/api/menu/{$menu->id}/reviews", [
            'rating' => 5,
            'comment' => 'Első vélemény',
        ])->assertCreated();

        $this->postJson("/api/menu/{$menu->id}/reviews", [
            'rating' => 3,
            'comment' => 'Második vélemény',
        ])->assertUnprocessable();

        $this->assertDatabaseCount('menu_reviews', 1);
    }

    public function test_authenticated_user_can_update_their_own_review(): void
    {
        $menu = $this->createMenuForReviewTests();
        $user = User::factory()->create();

        $review = MenuReview::create([
            'menu_id' => $menu->id,
            'user_id' => $user->id,
            'rating' => 3,
            'comment' => 'Korábbi vélemény',
            'is_approved' => true,
        ]);

        Sanctum::actingAs($user);

        $this->putJson("/api/menu/{$menu->id}/reviews", [
            'rating' => 4,
            'comment' => 'Frissített vélemény',
        ])
            ->assertOk()
            ->assertJsonPath('review.rating', 4)
            ->assertJsonPath('review.comment', 'Frissített vélemény')
            ->assertJsonPath('review.is_approved', false);

        $this->assertDatabaseHas('menu_reviews', [
            'id' => $review->id,
            'rating' => 4,
            'comment' => 'Frissített vélemény',
            'is_approved' => false,
        ]);
    }

    public function test_user_cannot_update_another_users_review(): void
    {
        $menu = $this->createMenuForReviewTests();
        $reviewOwner = User::factory()->create();
        $otherUser = User::factory()->create();

        MenuReview::create([
            'menu_id' => $menu->id,
            'user_id' => $reviewOwner->id,
            'rating' => 5,
            'comment' => 'Eredeti vélemény',
            'is_approved' => true,
        ]);

        Sanctum::actingAs($otherUser);

        $this->putJson("/api/menu/{$menu->id}/reviews", [
            'rating' => 1,
            'comment' => 'Más felhasználó véleménye',
        ])->assertNotFound();

        $this->assertDatabaseHas('menu_reviews', [
            'menu_id' => $menu->id,
            'user_id' => $reviewOwner->id,
            'rating' => 5,
            'comment' => 'Eredeti vélemény',
            'is_approved' => true,
        ]);
    }

    public function test_menu_review_requires_a_valid_rating(): void
    {
        $menu = $this->createMenuForReviewTests();
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $this->postJson("/api/menu/{$menu->id}/reviews", [
            'rating' => 6,
            'comment' => 'Érvénytelen értékelés',
        ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('rating');

        $this->assertDatabaseCount('menu_reviews', 0);
    }

    public function test_menu_review_submission_requires_authentication(): void
    {
        $menu = $this->createMenuForReviewTests();

        $this->postJson("/api/menu/{$menu->id}/reviews", [
            'rating' => 5,
            'comment' => 'Vendégvélemény',
        ])->assertUnauthorized();
    }
}
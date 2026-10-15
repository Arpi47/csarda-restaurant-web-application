<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Category;
use App\Models\Menu;
use App\Models\MenuImage;
use App\Models\MenuReview;
use App\Models\MenuReviewComment;
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

    public function test_menu_reviews_endpoint_returns_rating_statistics_separately_from_comments(): void
    {
        $menu = $this->createMenuForReviewTests();
        $approvedUser = User::factory()->create();
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
            ->assertJsonPath('ratings_count', 1)
            ->assertJsonPath('reviews_count', 1)
            ->assertJsonPath('average_rating', 5)
            ->assertJsonCount(1, 'reviews')
            ->assertJsonPath('reviews.0.rating', 5)
            ->assertJsonPath('reviews.0.comment', 'Nagyon finom!');
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

    public function test_authenticated_user_can_submit_a_menu_rating_without_a_comment(): void
    {
        $menu = $this->createMenuForReviewTests();
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $this->postJson("/api/menu/{$menu->id}/reviews", [
            'rating' => 5,
        ])
            ->assertCreated()
            ->assertJsonPath('review.rating', 5)
            ->assertJsonPath('review.comment', null);

        $this->assertDatabaseHas('menu_reviews', [
            'menu_id' => $menu->id,
            'user_id' => $user->id,
            'rating' => 5,
            'comment' => null,
            'is_approved' => true,
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

    public function test_authenticated_user_can_update_their_own_rating(): void
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
        ])
            ->assertOk()
            ->assertJsonPath('review.rating', 4)
            ->assertJsonPath('review.comment', 'Korábbi vélemény');

        $this->assertDatabaseHas('menu_reviews', [
            'id' => $review->id,
            'rating' => 4,
            'comment' => 'Korábbi vélemény',
            'is_approved' => true,
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

    public function test_authenticated_user_can_create_multiple_comments_on_same_menu(): void
    {
        $menu = $this->createMenuForReviewTests();
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $this->postJson("/api/menu/{$menu->id}/comments", [
            'body' => 'Első különálló komment.',
        ])->assertCreated();

        $this->postJson("/api/menu/{$menu->id}/comments", [
            'body' => 'Második különálló komment.',
        ])->assertCreated();

        $this->assertDatabaseCount('menu_review_comments', 2);

        $this->assertDatabaseHas('menu_review_comments', [
            'menu_id' => $menu->id,
            'user_id' => $user->id,
            'body' => 'Első különálló komment.',
            'parent_id' => null,
            'is_admin' => false,
        ]);

        $this->assertDatabaseHas('menu_review_comments', [
            'menu_id' => $menu->id,
            'user_id' => $user->id,
            'body' => 'Második különálló komment.',
            'parent_id' => null,
            'is_admin' => false,
        ]);
    }

    public function test_comment_validation_rejects_invalid_body_lengths_and_whitespace(): void
    {
        $menu = $this->createMenuForReviewTests();
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $invalidBodies = [
            'short' => 'Rövi',
            'whitespace' => '     ',
            'too_long' => str_repeat('a', 2001),
        ];

        foreach ($invalidBodies as $body) {
            $this->postJson("/api/menu/{$menu->id}/comments", [
                'body' => $body,
            ])->assertUnprocessable()
                ->assertJsonValidationErrors('body');
        }

        $this->assertDatabaseCount('menu_review_comments', 0);
    }

    public function test_authenticated_user_can_reply_to_a_reply_and_comments_are_returned_recursively(): void
    {
        $menu = $this->createMenuForReviewTests();
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $rootResponse = $this->postJson(
            "/api/menu/{$menu->id}/comments",
            ['body' => 'Ez az eredeti komment.']
        )->assertCreated();

        $rootId = $rootResponse->json('comment.id');

        $replyResponse = $this->postJson(
            "/api/menu/{$menu->id}/comments",
            [
                'body' => 'Ez az első válasz.',
                'parent_id' => $rootId,
            ]
        )->assertCreated();

        $replyId = $replyResponse->json('comment.id');

        $this->postJson(
            "/api/menu/{$menu->id}/comments",
            [
                'body' => 'Ez a második szintű válasz.',
                'parent_id' => $replyId,
            ]
        )->assertCreated();

        $this->assertDatabaseHas('menu_review_comments', [
            'id' => $replyId,
            'parent_id' => $rootId,
        ]);

        $this->getJson("/api/menu/{$menu->id}/comments")
            ->assertOk()
            ->assertJsonPath('comments.0.id', $rootId)
            ->assertJsonPath(
                'comments.0.replies.0.id',
                $replyId
            )
            ->assertJsonPath(
                'comments.0.replies.0.replies.0.body',
                'Ez a második szintű válasz.'
            );
    }

    public function test_user_can_edit_own_comment_after_admin_has_replied(): void
    {
        $menu = $this->createMenuForReviewTests();
        $user = User::factory()->create();
        $adminUser = User::factory()->create();

        $comment = MenuReviewComment::create([
            'menu_id' => $menu->id,
            'user_id' => $user->id,
            'parent_id' => null,
            'body' => 'Eredeti saját komment.',
            'is_admin' => false,
        ]);

        MenuReviewComment::create([
            'menu_id' => $menu->id,
            'user_id' => $adminUser->id,
            'parent_id' => $comment->id,
            'body' => 'Az adminisztrátor válasza.',
            'is_admin' => true,
        ]);

        Sanctum::actingAs($user);

        $this->putJson(
            "/api/menu/{$menu->id}/comments/{$comment->id}",
            ['body' => 'Módosított saját komment.']
        )
            ->assertOk()
            ->assertJsonPath(
                'comment.body',
                'Módosított saját komment.'
            );

        $this->assertDatabaseHas('menu_review_comments', [
            'id' => $comment->id,
            'body' => 'Módosított saját komment.',
        ]);
    }

    public function test_user_cannot_edit_or_delete_another_users_comment(): void
    {
        $menu = $this->createMenuForReviewTests();
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();

        $comment = MenuReviewComment::create([
            'menu_id' => $menu->id,
            'user_id' => $owner->id,
            'parent_id' => null,
            'body' => 'Másik felhasználó kommentje.',
            'is_admin' => false,
        ]);

        Sanctum::actingAs($otherUser);

        $this->putJson(
            "/api/menu/{$menu->id}/comments/{$comment->id}",
            ['body' => 'Jogosulatlan módosítás.']
        )->assertForbidden();

        $this->deleteJson(
            "/api/menu/{$menu->id}/comments/{$comment->id}"
        )->assertForbidden();

        $this->assertDatabaseHas('menu_review_comments', [
            'id' => $comment->id,
            'user_id' => $owner->id,
            'body' => 'Másik felhasználó kommentje.',
        ]);
    }

    public function test_guest_cannot_create_edit_or_delete_comments(): void
    {
        $menu = $this->createMenuForReviewTests();
        $owner = User::factory()->create();

        $comment = MenuReviewComment::create([
            'menu_id' => $menu->id,
            'user_id' => $owner->id,
            'parent_id' => null,
            'body' => 'Eredeti komment a teszthez.',
            'is_admin' => false,
        ]);

        $this->postJson("/api/menu/{$menu->id}/comments", [
            'body' => 'Vendég által beküldött komment.',
        ])->assertUnauthorized();

        $this->putJson(
            "/api/menu/{$menu->id}/comments/{$comment->id}",
            ['body' => 'Vendég által módosított komment.']
        )->assertUnauthorized();

        $this->deleteJson(
            "/api/menu/{$menu->id}/comments/{$comment->id}"
        )->assertUnauthorized();

        $this->assertDatabaseCount('menu_review_comments', 1);

        $this->assertDatabaseHas('menu_review_comments', [
            'id' => $comment->id,
            'user_id' => $owner->id,
            'body' => 'Eredeti komment a teszthez.',
        ]);
    }

    public function test_user_cannot_reply_to_comment_from_another_menu(): void
    {
        $firstMenu = $this->createMenuForReviewTests();
        $secondMenu = $this->createMenuForReviewTests();
        $owner = User::factory()->create();
        $replyingUser = User::factory()->create();

        $comment = MenuReviewComment::create([
            'menu_id' => $firstMenu->id,
            'user_id' => $owner->id,
            'parent_id' => null,
            'body' => 'Komment az első ételhez.',
            'is_admin' => false,
        ]);

        Sanctum::actingAs($replyingUser);

        $this->postJson(
            "/api/menu/{$secondMenu->id}/comments",
            [
                'body' => 'Válasz másik étel kommentjére.',
                'parent_id' => $comment->id,
            ]
        )->assertNotFound();

        $this->assertDatabaseCount('menu_review_comments', 1);
    }

    public function test_user_cannot_reply_to_nonexistent_comment(): void
    {
        $menu = $this->createMenuForReviewTests();
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $this->postJson("/api/menu/{$menu->id}/comments", [
            'body' => 'Válasz nem létező kommentre.',
            'parent_id' => 999999,
        ])->assertNotFound();

        $this->assertDatabaseCount('menu_review_comments', 0);
    }

    public function test_admin_can_reply_to_an_existing_reply(): void
    {
        $menu = $this->createMenuForReviewTests();

        $user = User::factory()->create();

        $rootComment = MenuReviewComment::create([
            'menu_id' => $menu->id,
            'user_id' => $user->id,
            'parent_id' => null,
            'body' => 'Eredeti felhasználói komment.',
            'is_admin' => false,
        ]);

        $existingReply = MenuReviewComment::create([
            'menu_id' => $menu->id,
            'user_id' => $user->id,
            'parent_id' => $rootComment->id,
            'body' => 'Válasz az eredeti kommentre.',
            'is_admin' => false,
        ]);

        $admin = Admin::create([
            'name' => 'Test Admin',
            'email' => 'test-admin@example.com',
            'password' => 'test-password',
            'is_super_admin' => false,
            'is_suspended' => false,
        ]);

        $response = $this
            ->actingAs($admin, 'admin')
            ->post(
                route('admin.menu-reviews.reply', $existingReply),
                [
                    'body' => 'Admin válasza egy válaszra.',
                ]
            );

        $response->assertRedirect(
            route('admin.menu-reviews.show', $menu->id)
        );

        $this->assertDatabaseHas('menu_review_comments', [
            'menu_id' => $menu->id,
            'user_id' => null,
            'parent_id' => $existingReply->id,
            'body' => 'Admin válasza egy válaszra.',
            'is_admin' => true,
        ]);
    }

    public function test_admin_can_delete_a_nested_comment_and_its_replies(): void
    {
        $menu = $this->createMenuForReviewTests();

        $user = User::factory()->create();

        $rootComment = MenuReviewComment::create([
            'menu_id' => $menu->id,
            'user_id' => $user->id,
            'parent_id' => null,
            'body' => 'Eredeti felhasználói komment.',
            'is_admin' => false,
        ]);

        $reply = MenuReviewComment::create([
            'menu_id' => $menu->id,
            'user_id' => $user->id,
            'parent_id' => $rootComment->id,
            'body' => 'Első válasz a kommentre.',
            'is_admin' => false,
        ]);

        $nestedReply = MenuReviewComment::create([
            'menu_id' => $menu->id,
            'user_id' => $user->id,
            'parent_id' => $reply->id,
            'body' => 'Második szintű válasz.',
            'is_admin' => false,
        ]);

        $admin = Admin::create([
            'name' => 'Test Admin',
            'email' => 'delete-admin@example.com',
            'password' => 'test-password',
            'is_super_admin' => false,
            'is_suspended' => false,
        ]);

        $response = $this
            ->actingAs($admin, 'admin')
            ->delete(route('admin.menu-reviews.destroy', $reply));

        $response->assertRedirect(
            route('admin.menu-reviews.show', $menu->id)
        );

        $this->assertDatabaseHas('menu_review_comments', [
            'id' => $rootComment->id,
        ]);

        $this->assertDatabaseMissing('menu_review_comments', [
            'id' => $reply->id,
        ]);

        $this->assertDatabaseMissing('menu_review_comments', [
            'id' => $nestedReply->id,
        ]);
    }

    public function test_admin_can_delete_a_root_comment_and_its_entire_reply_tree(): void
    {
        $menu = $this->createMenuForReviewTests();

        $user = User::factory()->create();

        $rootComment = MenuReviewComment::create([
            'menu_id' => $menu->id,
            'user_id' => $user->id,
            'parent_id' => null,
            'body' => 'Törlendő gyökérkomment.',
            'is_admin' => false,
        ]);

        $firstReply = MenuReviewComment::create([
            'menu_id' => $menu->id,
            'user_id' => $user->id,
            'parent_id' => $rootComment->id,
            'body' => 'Első válasz a gyökérkommentre.',
            'is_admin' => false,
        ]);

        $secondReply = MenuReviewComment::create([
            'menu_id' => $menu->id,
            'user_id' => $user->id,
            'parent_id' => $firstReply->id,
            'body' => 'Második szintű válasz.',
            'is_admin' => true,
        ]);

        $admin = Admin::create([
            'name' => 'Test Admin',
            'email' => 'delete-tree-admin@example.com',
            'password' => 'test-password',
            'is_super_admin' => false,
            'is_suspended' => false,
        ]);

        $response = $this
            ->actingAs($admin, 'admin')
            ->delete(route('admin.menu-reviews.destroy', $rootComment));

        $response->assertRedirect(
            route('admin.menu-reviews.show', $menu->id)
        );

        $this->assertDatabaseMissing('menu_review_comments', [
            'id' => $rootComment->id,
        ]);

        $this->assertDatabaseMissing('menu_review_comments', [
            'id' => $firstReply->id,
        ]);

        $this->assertDatabaseMissing('menu_review_comments', [
            'id' => $secondReply->id,
        ]);
    }

    public function test_admin_menu_review_page_loads_nested_comments_recursively(): void
    {
        $menu = $this->createMenuForReviewTests();

        $user = User::factory()->create();

        $rootComment = MenuReviewComment::create([
            'menu_id' => $menu->id,
            'user_id' => $user->id,
            'parent_id' => null,
            'body' => 'Eredeti komment a teszthez.',
            'is_admin' => false,
        ]);

        $reply = MenuReviewComment::create([
            'menu_id' => $menu->id,
            'user_id' => $user->id,
            'parent_id' => $rootComment->id,
            'body' => 'Válasz az eredeti kommentre.',
            'is_admin' => false,
        ]);

        $nestedReply = MenuReviewComment::create([
            'menu_id' => $menu->id,
            'user_id' => $user->id,
            'parent_id' => $reply->id,
            'body' => 'Válasz egy másik válaszra.',
            'is_admin' => true,
        ]);

        $admin = Admin::create([
            'name' => 'Test Admin',
            'email' => 'nested-comments-admin@example.com',
            'password' => 'test-password',
            'is_super_admin' => false,
            'is_suspended' => false,
        ]);

        $response = $this->actingAs($admin, 'admin')->get(
            route('admin.menu-reviews.show', $menu->id)
        );

        $response->assertOk();
        $response->assertSee('Eredeti komment a teszthez.');
        $response->assertSee('Válasz az eredeti kommentre.');
        $response->assertSee('Válasz egy másik válaszra.');
    }

    public function test_regular_user_cannot_reply_to_or_delete_comments_through_admin_routes(): void
    {
        $menu = $this->createMenuForReviewTests();
        $user = User::factory()->create();

        $comment = MenuReviewComment::create([
            'menu_id' => $menu->id,
            'user_id' => $user->id,
            'parent_id' => null,
            'body' => 'Eredeti komment a jogosultsági teszthez.',
            'is_admin' => false,
        ]);

        $response = $this->actingAs($user)->post(
            route('admin.menu-reviews.reply', $comment),
            ['body' => 'Jogosulatlan adminválasz.']
        );

        $this->assertDatabaseMissing('menu_review_comments', [
            'menu_id' => $menu->id,
            'body' => 'Jogosulatlan adminválasz.',
            'is_admin' => true,
        ]);

        $this->actingAs($user)
            ->delete(route('admin.menu-reviews.destroy', $comment));

        $this->assertDatabaseHas('menu_review_comments', [
            'id' => $comment->id,
            'body' => 'Eredeti komment a jogosultsági teszthez.',
        ]);
    }

    public function test_guest_cannot_reply_to_or_delete_comments_through_admin_routes(): void
    {
        $menu = $this->createMenuForReviewTests();
        $user = User::factory()->create();

        $comment = MenuReviewComment::create([
            'menu_id' => $menu->id,
            'user_id' => $user->id,
            'parent_id' => null,
            'body' => 'Eredeti komment a vendég jogosultsági tesztjéhez.',
            'is_admin' => false,
        ]);

        $response = $this->post(
            route('admin.menu-reviews.reply', $comment),
            ['body' => 'Vendég által küldött adminválasz.']
        );

        $this->assertDatabaseMissing('menu_review_comments', [
            'menu_id' => $menu->id,
            'body' => 'Vendég által küldött adminválasz.',
            'is_admin' => true,
        ]);

        $this->delete(route('admin.menu-reviews.destroy', $comment));

        $this->assertDatabaseHas('menu_review_comments', [
            'id' => $comment->id,
            'body' => 'Eredeti komment a vendég jogosultsági tesztjéhez.',
        ]);
    }

    public function test_admin_reply_validation_errors_use_the_correct_error_bag(): void
    {
        $menu = $this->createMenuForReviewTests();
        $user = User::factory()->create();

        $comment = MenuReviewComment::create([
            'menu_id' => $menu->id,
            'user_id' => $user->id,
            'parent_id' => null,
            'body' => 'Eredeti komment a validációs teszthez.',
            'is_admin' => false,
        ]);

        $admin = Admin::create([
            'name' => 'Validation Test Admin',
            'email' => 'validation-test-admin@example.com',
            'password' => 'test-password',
            'is_super_admin' => false,
            'is_suspended' => false,
        ]);

        $response = $this->actingAs($admin, 'admin')->post(
            route('admin.menu-reviews.reply', $comment),
            ['body' => 'Rövi']
        );

        $response->assertRedirect();
        $response->assertSessionHasErrorsIn(
            'reply_' . $comment->id,
            ['body']
        );

        $this->assertDatabaseMissing('menu_review_comments', [
            'menu_id' => $menu->id,
            'parent_id' => $comment->id,
            'body' => 'Rövid',
            'is_admin' => true,
        ]);
    }

    public function test_invalid_admin_reply_preserves_text_only_for_its_comment(): void
    {
        $menu = $this->createMenuForReviewTests();
        $user = User::factory()->create();

        $comment = MenuReviewComment::create([
            'menu_id' => $menu->id,
            'user_id' => $user->id,
            'parent_id' => null,
            'body' => 'Első komment a teszthez.',
            'is_admin' => false,
        ]);

        $otherComment = MenuReviewComment::create([
            'menu_id' => $menu->id,
            'user_id' => $user->id,
            'parent_id' => null,
            'body' => 'Második komment a teszthez.',
            'is_admin' => false,
        ]);

        $admin = Admin::create([
            'name' => 'Reply Session Test Admin',
            'email' => 'reply-session-test@example.com',
            'password' => 'test-password',
            'is_super_admin' => false,
            'is_suspended' => false,
        ]);

        $response = $this->actingAs($admin, 'admin')->post(
            route('admin.menu-reviews.reply', $comment),
            ['body' => 'Rövi']
        );

        $response->assertRedirect();

        $response->assertSessionHasErrorsIn(
            'reply_' . $comment->id,
            ['body']
        );

        $response->assertSessionHas(
            'reply_body_' . $comment->id,
            'Rövi'
        );

        $response->assertSessionMissing(
            'reply_body_' . $otherComment->id
        );
    }

    public function test_successful_admin_reply_does_not_flash_reply_text_to_session(): void
    {
        $menu = $this->createMenuForReviewTests();
        $user = User::factory()->create();

        $comment = MenuReviewComment::create([
            'menu_id' => $menu->id,
            'user_id' => $user->id,
            'parent_id' => null,
            'body' => 'Eredeti komment a sikeres válasz tesztjéhez.',
            'is_admin' => false,
        ]);

        $admin = Admin::create([
            'name' => 'Successful Reply Test Admin',
            'email' => 'successful-reply-test@example.com',
            'password' => 'test-password',
            'is_super_admin' => false,
            'is_suspended' => false,
        ]);

        $response = $this->actingAs($admin, 'admin')->post(
            route('admin.menu-reviews.reply', $comment),
            ['body' => 'Köszönjük a visszajelzést!']
        );

        $response->assertRedirect(
            route('admin.menu-reviews.show', $menu->id)
        );

        $response->assertSessionMissing(
            'reply_body_' . $comment->id
        );

        $this->assertDatabaseHas('menu_review_comments', [
            'menu_id' => $menu->id,
            'parent_id' => $comment->id,
            'body' => 'Köszönjük a visszajelzést!',
            'is_admin' => true,
        ]);
    }
}
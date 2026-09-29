<?php

namespace Tests\Feature;

use App\Models\SocialAccount;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ProfileApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_profile_requires_authentication(): void
    {
        $response = $this->getJson('/api/profile');

        $response->assertUnauthorized();
    }

    public function test_authenticated_user_can_view_own_profile(): void
    {
        $user = User::factory()->create([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@example.com',
            'password' => Hash::make('Password123!'),
            'deletion_requested' => false,
        ]);

        Sanctum::actingAs($user);

        $response = $this->getJson('/api/profile');

        $response
            ->assertOk()
            ->assertJson([
                'first_name' => 'John',
                'last_name' => 'Doe',
                'email' => 'john@example.com',
                'can_change_password' => true,
                'google_connected' => false,
                'deletion_requested' => false,
            ])
            ->assertJsonMissing([
                'password' => Hash::make('Password123!'),
            ]);
    }

    public function test_profile_shows_google_connected_as_true(): void
    {
        $user = User::factory()->create();

        SocialAccount::create([
            'user_id' => $user->id,
            'provider' => 'google',
            'provider_id' => 'google-123',
        ]);

        Sanctum::actingAs($user);

        $response = $this->getJson('/api/profile');

        $response
            ->assertOk()
            ->assertJson([
                'google_connected' => true,
            ]);
    }

    public function test_profile_shows_deletion_requested_status(): void
    {
        $user = User::factory()->create([
            'deletion_requested' => true,
        ]);

        Sanctum::actingAs($user);

        $response = $this->getJson('/api/profile');

        $response
            ->assertOk()
            ->assertJson([
                'deletion_requested' => true,
            ]);
    }

    public function test_profile_update_requires_authentication(): void
    {
        $response = $this->putJson('/api/profile', [
            'g-recaptcha-response' => 'valid-token',
            'first_name' => 'John',
        ]);

        $response->assertUnauthorized();
    }

    public function test_profile_update_requires_recaptcha(): void
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $response = $this->putJson('/api/profile', [
            'first_name' => 'John',
        ]);

        $response
            ->assertStatus(422)
            ->assertJson([
                'message' => __('messages.recaptcha_required'),
            ]);
    }

    public function test_profile_update_rejects_failed_recaptcha(): void
    {
        Http::fake([
            'https://www.google.com/recaptcha/api/siteverify' => Http::response([
                'success' => false,
            ], 200),
        ]);

        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $response = $this->putJson('/api/profile', [
            'g-recaptcha-response' => 'invalid-token',
            'first_name' => 'John',
        ]);

        $response
            ->assertStatus(422)
            ->assertJson([
                'message' => __('messages.recaptcha_failed'),
            ]);
    }

    public function test_profile_update_rejects_low_recaptcha_score(): void
    {
        Http::fake([
            'https://www.google.com/recaptcha/api/siteverify' => Http::response([
                'success' => true,
                'score' => 0.4,
            ], 200),
        ]);

        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $response = $this->putJson('/api/profile', [
            'g-recaptcha-response' => 'low-score-token',
            'first_name' => 'John',
        ]);

        $response
            ->assertStatus(422)
            ->assertJson([
                'message' => __('messages.invalid_captcha'),
            ]);
    }

    public function test_user_can_update_first_name(): void
    {
        Http::fake([
            'https://www.google.com/recaptcha/api/siteverify' => Http::response([
                'success' => true,
                'score' => 0.9,
            ], 200),
        ]);

        $user = User::factory()->create([
            'first_name' => 'John',
            'last_name' => 'Doe',
        ]);

        Sanctum::actingAs($user);

        $response = $this->putJson('/api/profile', [
            'g-recaptcha-response' => 'valid-token',
            'first_name' => 'Jane',
        ]);

        $response
            ->assertOk()
            ->assertJson([
                'message' => __('messages.profile_updated'),
                'user' => [
                    'first_name' => 'Jane',
                    'last_name' => 'Doe',
                ],
            ]);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'first_name' => 'Jane',
            'last_name' => 'Doe',
        ]);
    }

    public function test_user_can_update_last_name(): void
    {
        Http::fake([
            'https://www.google.com/recaptcha/api/siteverify' => Http::response([
                'success' => true,
                'score' => 0.9,
            ], 200),
        ]);

        $user = User::factory()->create([
            'first_name' => 'John',
            'last_name' => 'Doe',
        ]);

        Sanctum::actingAs($user);

        $response = $this->putJson('/api/profile', [
            'g-recaptcha-response' => 'valid-token',
            'last_name' => 'Smith',
        ]);

        $response
            ->assertOk()
            ->assertJson([
                'message' => __('messages.profile_updated'),
                'user' => [
                    'first_name' => 'John',
                    'last_name' => 'Smith',
                ],
            ]);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'first_name' => 'John',
            'last_name' => 'Smith',
        ]);
    }

    public function test_user_can_update_first_and_last_name(): void
    {
        Http::fake([
            'https://www.google.com/recaptcha/api/siteverify' => Http::response([
                'success' => true,
                'score' => 0.9,
            ], 200),
        ]);

        $user = User::factory()->create([
            'first_name' => 'John',
            'last_name' => 'Doe',
        ]);

        Sanctum::actingAs($user);

        $response = $this->putJson('/api/profile', [
            'g-recaptcha-response' => 'valid-token',
            'first_name' => 'Jane',
            'last_name' => 'Smith',
        ]);

        $response
            ->assertOk()
            ->assertJson([
                'message' => __('messages.profile_updated'),
                'user' => [
                    'first_name' => 'Jane',
                    'last_name' => 'Smith',
                ],
            ]);
    }

    public function test_profile_update_rejects_first_name_longer_than_50_characters(): void
    {
        Http::fake([
            'https://www.google.com/recaptcha/api/siteverify' => Http::response([
                'success' => true,
                'score' => 0.9,
            ], 200),
        ]);

        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $response = $this->putJson('/api/profile', [
            'g-recaptcha-response' => 'valid-token',
            'first_name' => str_repeat('A', 51),
        ]);

        $response->assertUnprocessable();
    }

    public function test_profile_update_rejects_last_name_longer_than_50_characters(): void
    {
        Http::fake([
            'https://www.google.com/recaptcha/api/siteverify' => Http::response([
                'success' => true,
                'score' => 0.9,
            ], 200),
        ]);

        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $response = $this->putJson('/api/profile', [
            'g-recaptcha-response' => 'valid-token',
            'last_name' => str_repeat('A', 51),
        ]);

        $response->assertUnprocessable();
    }

    public function test_user_can_change_password(): void
    {
        Http::fake([
            'https://www.google.com/recaptcha/api/siteverify' => Http::response([
                'success' => true,
                'score' => 0.9,
            ], 200),
        ]);

        $user = User::factory()->create([
            'password' => Hash::make('OldPassword123!'),
        ]);

        Sanctum::actingAs($user);

        $response = $this->putJson('/api/profile', [
            'g-recaptcha-response' => 'valid-token',
            'password' => 'NewPassword123!',
            'password_confirmation' => 'NewPassword123!',
        ]);

        $response
            ->assertOk()
            ->assertJson([
                'message' => __('messages.profile_updated'),
            ]);

        $user->refresh();

        $this->assertTrue(
            Hash::check(
                'NewPassword123!',
                $user->password
            )
        );
    }

    public function test_profile_update_rejects_mismatched_password_confirmation(): void
    {
        Http::fake([
            'https://www.google.com/recaptcha/api/siteverify' => Http::response([
                'success' => true,
                'score' => 0.9,
            ], 200),
        ]);

        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $response = $this->putJson('/api/profile', [
            'g-recaptcha-response' => 'valid-token',
            'password' => 'NewPassword123!',
            'password_confirmation' => 'DifferentPassword123!',
        ]);

        $response->assertUnprocessable();
    }

    public function test_profile_update_rejects_password_shorter_than_eight_characters(): void
    {
        Http::fake([
            'https://www.google.com/recaptcha/api/siteverify' => Http::response([
                'success' => true,
                'score' => 0.9,
            ], 200),
        ]);

        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $response = $this->putJson('/api/profile', [
            'g-recaptcha-response' => 'valid-token',
            'password' => 'Ab1!',
            'password_confirmation' => 'Ab1!',
        ]);

        $response->assertUnprocessable();
    }

    public function test_profile_update_rejects_password_without_lowercase_letter(): void
    {
        Http::fake([
            'https://www.google.com/recaptcha/api/siteverify' => Http::response([
                'success' => true,
                'score' => 0.9,
            ], 200),
        ]);

        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $response = $this->putJson('/api/profile', [
            'g-recaptcha-response' => 'valid-token',
            'password' => 'PASSWORD123!',
            'password_confirmation' => 'PASSWORD123!',
        ]);

        $response->assertUnprocessable();
    }

    public function test_profile_update_rejects_password_without_uppercase_letter(): void
    {
        Http::fake([
            'https://www.google.com/recaptcha/api/siteverify' => Http::response([
                'success' => true,
                'score' => 0.9,
            ], 200),
        ]);

        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $response = $this->putJson('/api/profile', [
            'g-recaptcha-response' => 'valid-token',
            'password' => 'password123!',
            'password_confirmation' => 'password123!',
        ]);

        $response->assertUnprocessable();
    }

    public function test_profile_update_rejects_password_without_number(): void
    {
        Http::fake([
            'https://www.google.com/recaptcha/api/siteverify' => Http::response([
                'success' => true,
                'score' => 0.9,
            ], 200),
        ]);

        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $response = $this->putJson('/api/profile', [
            'g-recaptcha-response' => 'valid-token',
            'password' => 'Password!',
            'password_confirmation' => 'Password!',
        ]);

        $response->assertUnprocessable();
    }

    public function test_profile_update_rejects_password_without_symbol(): void
    {
        Http::fake([
            'https://www.google.com/recaptcha/api/siteverify' => Http::response([
                'success' => true,
                'score' => 0.9,
            ], 200),
        ]);

        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $response = $this->putJson('/api/profile', [
            'g-recaptcha-response' => 'valid-token',
            'password' => 'Password123',
            'password_confirmation' => 'Password123',
        ]);

        $response->assertUnprocessable();
    }

    public function test_profile_update_rejects_same_password(): void
    {
        Http::fake([
            'https://www.google.com/recaptcha/api/siteverify' => Http::response([
                'success' => true,
                'score' => 0.9,
            ], 200),
        ]);

        $user = User::factory()->create([
            'password' => Hash::make('CurrentPassword123!'),
        ]);

        Sanctum::actingAs($user);

        $response = $this->putJson('/api/profile', [
            'g-recaptcha-response' => 'valid-token',
            'password' => 'CurrentPassword123!',
            'password_confirmation' => 'CurrentPassword123!',
        ]);

        $response
            ->assertStatus(422)
            ->assertJson([
                'message' => __('messages.same_password'),
            ]);
    }
}

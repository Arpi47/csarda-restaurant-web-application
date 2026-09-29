<?php

namespace Tests\Feature;

use App\Models\SocialAccount;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ProfileActionsApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_google_disconnect_requires_authentication(): void
    {
        $response = $this->postJson(
            '/api/profile/google/disconnect'
        );

        $response->assertUnauthorized();
    }

    public function test_google_disconnect_fails_when_google_is_not_connected(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('Password123!'),
        ]);

        Sanctum::actingAs($user);

        $response = $this->postJson(
            '/api/profile/google/disconnect'
        );

        $response
            ->assertStatus(422)
            ->assertJson([
                'success' => false,
                'message' => __('messages.google_not_connected'),
            ]);
    }

    public function test_google_disconnect_requires_password(): void
    {
        $user = User::factory()->create([
            'password' => null,
        ]);

        SocialAccount::create([
            'user_id' => $user->id,
            'provider' => 'google',
            'provider_id' => 'google-123',
        ]);

        Sanctum::actingAs($user);

        $response = $this->postJson(
            '/api/profile/google/disconnect'
        );

        $response
            ->assertStatus(422)
            ->assertJson([
                'success' => false,
                'message' => __('messages.google_disconnect_password_required'),
            ]);
    }

    public function test_user_can_disconnect_google_account(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('Password123!'),
        ]);

        $socialAccount = SocialAccount::create([
            'user_id' => $user->id,
            'provider' => 'google',
            'provider_id' => 'google-123',
        ]);

        Sanctum::actingAs($user);

        $response = $this->postJson(
            '/api/profile/google/disconnect'
        );

        $response
            ->assertOk()
            ->assertJson([
                'success' => true,
                'message' => __('messages.google_disconnected'),
            ]);

        $this->assertDatabaseMissing('social_accounts', [
            'id' => $socialAccount->id,
        ]);
    }

    public function test_delete_request_requires_authentication(): void
    {
        $response = $this->postJson(
            '/api/profile/delete-request'
        );

        $response->assertUnauthorized();
    }

    public function test_user_can_request_account_deletion(): void
    {
        $user = User::factory()->create([
            'deletion_requested' => false,
            'deletion_attempts_last_24h' => 0,
            'deletion_requested_at' => null,
            'deletion_will_be_final_at' => null,
        ]);

        Sanctum::actingAs($user);

        $before = now();

        $response = $this->postJson(
            '/api/profile/delete-request'
        );

        $response
            ->assertOk()
            ->assertJson([
                'success' => true,
                'too_many_attempts' => false,
                'message' => __('messages.deletion_requested'),
            ]);

        $user->refresh();

        $this->assertTrue($user->deletion_requested);
        $this->assertEquals(1, $user->deletion_attempts_last_24h);
        $this->assertNotNull($user->deletion_requested_at);
        $this->assertNotNull($user->deletion_will_be_final_at);

        $this->assertTrue(
            $user->deletion_requested_at->between(
                $before->copy()->subSecond(),
                now()->addSecond()
            )
        );

        $this->assertTrue(
            $user->deletion_will_be_final_at->between(
                $before->copy()->addDays(30)->subSecond(),
                now()->addDays(30)->addSecond()
            )
        );
    }

    public function test_user_can_request_account_deletion_twice(): void
    {
        $user = User::factory()->create([
            'deletion_requested' => false,
            'deletion_attempts_last_24h' => 0,
        ]);

        Sanctum::actingAs($user);

        $firstResponse = $this->postJson(
            '/api/profile/delete-request'
        );

        $firstResponse
            ->assertOk()
            ->assertJson([
                'success' => true,
                'too_many_attempts' => false,
            ]);

        $secondResponse = $this->postJson(
            '/api/profile/delete-request'
        );

        $secondResponse
            ->assertOk()
            ->assertJson([
                'success' => true,
                'too_many_attempts' => false,
            ]);

        $user->refresh();

        $this->assertEquals(2, $user->deletion_attempts_last_24h);
        $this->assertTrue($user->deletion_requested);
    }

    public function test_delete_request_is_blocked_after_two_attempts_within_24_hours(): void
    {
        $user = User::factory()->create([
            'deletion_requested' => true,
            'deletion_requested_at' => now(),
            'deletion_attempts_last_24h' => 2,
        ]);

        Sanctum::actingAs($user);

        $response = $this->postJson(
            '/api/profile/delete-request'
        );

        $response
            ->assertOk()
            ->assertJson([
                'success' => false,
                'too_many_attempts' => true,
                'message' => __('messages.too_many_attempts'),
            ]);

        $user->refresh();

        $this->assertEquals(2, $user->deletion_attempts_last_24h);
    }

    public function test_delete_request_attempts_reset_after_24_hours(): void
    {
        $user = User::factory()->create([
            'deletion_requested' => true,
            'deletion_requested_at' => now()->subDay()->subMinute(),
            'deletion_attempts_last_24h' => 2,
        ]);

        Sanctum::actingAs($user);

        $response = $this->postJson(
            '/api/profile/delete-request'
        );

        $response
            ->assertOk()
            ->assertJson([
                'success' => true,
                'too_many_attempts' => false,
                'message' => __('messages.deletion_requested'),
            ]);

        $user->refresh();

        $this->assertEquals(1, $user->deletion_attempts_last_24h);
        $this->assertTrue($user->deletion_requested);
    }

    public function test_delete_cancel_requires_authentication(): void
    {
        $response = $this->postJson(
            '/api/profile/delete-cancel'
        );

        $response->assertUnauthorized();
    }

    public function test_delete_cancel_fails_without_active_deletion_request(): void
    {
        $user = User::factory()->create([
            'deletion_requested' => false,
        ]);

        Sanctum::actingAs($user);

        $response = $this->postJson(
            '/api/profile/delete-cancel'
        );

        $response
            ->assertOk()
            ->assertJson([
                'success' => false,
                'message' => __('messages.no_deletion_request'),
            ]);
    }

    public function test_user_can_cancel_account_deletion(): void
    {
        $user = User::factory()->create([
            'deletion_requested' => true,
            'deletion_requested_at' => now(),
            'deletion_will_be_final_at' => now()->addDays(30),
            'deletion_attempts_last_24h' => 1,
        ]);

        Sanctum::actingAs($user);

        $response = $this->postJson(
            '/api/profile/delete-cancel'
        );

        $response
            ->assertOk()
            ->assertJson([
                'success' => true,
                'message' => __('messages.deletion_cancelled'),
            ]);

        $user->refresh();

        $this->assertFalse($user->deletion_requested);
        $this->assertNull($user->deletion_requested_at);
        $this->assertNull($user->deletion_will_be_final_at);

        $this->assertEquals(
            1,
            $user->deletion_attempts_last_24h
        );
    }
}

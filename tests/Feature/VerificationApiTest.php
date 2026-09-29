<?php

namespace Tests\Feature;

use App\Models\PendingUser;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class VerificationApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_invalid_verification_token_returns_not_found(): void
    {
        $response = $this->getJson(
            '/api/verify-email/invalid-token'
        );

        $response->assertStatus(404);
    }

    public function test_expired_verification_token_returns_unprocessable_entity_and_deletes_pending_user(): void
    {
        $pendingUser = PendingUser::create([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@example.com',
            'password' => Hash::make('Password123!'),
            'verification_token' => 'expired-token',
            'expires_at' => now()->subMinute(),
        ]);

        $response = $this->getJson(
            '/api/verify-email/expired-token'
        );

        $response->assertStatus(422);

        $this->assertDatabaseMissing(
            'pending_users',
            ['id' => $pendingUser->id]
        );

        $this->assertDatabaseMissing(
            'users',
            ['email' => 'john@example.com']
        );
    }

    public function test_verification_token_for_existing_email_returns_unprocessable_entity_and_deletes_pending_user(): void
    {
        User::create([
            'first_name' => 'Existing',
            'last_name' => 'User',
            'email' => 'existing@example.com',
            'password' => Hash::make('Password123!'),
        ]);

        $pendingUser = PendingUser::create([
            'first_name' => 'New',
            'last_name' => 'User',
            'email' => 'existing@example.com',
            'password' => Hash::make('Password123!'),
            'verification_token' => 'existing-email-token',
            'expires_at' => now()->addHour(),
        ]);

        $response = $this->getJson(
            '/api/verify-email/existing-email-token'
        );

        $response->assertStatus(422);

        $this->assertDatabaseMissing(
            'pending_users',
            ['id' => $pendingUser->id]
        );

        $this->assertDatabaseCount('users', 1);
    }

    public function test_valid_verification_token_creates_user_deletes_pending_user_and_redirects(): void
    {
        $pendingUser = PendingUser::create([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@example.com',
            'password' => Hash::make('Password123!'),
            'verification_token' => 'valid-verification-token',
            'expires_at' => now()->addHour(),
        ]);

        $response = $this->get(
            '/api/verify-email/valid-verification-token'
        );

        $response
            ->assertRedirect(
                config('app.frontend_url').'/verification-success'
            );

        $this->assertDatabaseMissing(
            'pending_users',
            ['id' => $pendingUser->id]
        );

        $this->assertDatabaseHas('users', [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@example.com',
        ]);

        $user = User::where(
            'email',
            'john@example.com'
        )->first();

        $this->assertNotNull($user);
        $this->assertNotNull($user->email_verified_at);
    }
}

<?php

namespace Tests\Feature;

use App\Models\PendingUser;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LoginApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_requires_email(): void
    {
        $response = $this->postJson('/api/login', [
            'password' => 'Password123!',
        ]);

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                'email',
            ]);
    }

    public function test_login_requires_valid_email_format(): void
    {
        $response = $this->postJson('/api/login', [
            'email' => 'not-an-email',
            'password' => 'Password123!',
        ]);

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                'email',
            ]);
    }

    public function test_login_requires_password(): void
    {
        $response = $this->postJson('/api/login', [
            'email' => 'john@example.com',
        ]);

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                'password',
            ]);
    }

    public function test_login_rejects_email_with_pending_verification(): void
    {
        PendingUser::create([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@example.com',
            'password' => Hash::make('Password123!'),
            'verification_token' => 'pending-token',
            'expires_at' => now()->addHour(),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'john@example.com',
            'password' => 'Password123!',
        ]);

        $response
            ->assertStatus(422)
            ->assertJsonPath('message', __('messages.email_not_verified'));
    }

    public function test_login_rejects_suspended_user(): void
    {
        User::create([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@example.com',
            'password' => Hash::make('Password123!'),
            'is_suspended' => true,
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'john@example.com',
            'password' => 'Password123!',
        ]);

        $response
            ->assertStatus(403)
            ->assertJsonPath('message', __('messages.account_suspended'));
    }

    public function test_login_rejects_nonexistent_user(): void
    {
        $response = $this->postJson('/api/login', [
            'email' => 'unknown@example.com',
            'password' => 'Password123!',
        ]);

        $response
            ->assertStatus(422)
            ->assertJsonPath('message', __('messages.invalid_credentials'));
    }

    public function test_login_rejects_incorrect_password(): void
    {
        User::create([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@example.com',
            'password' => Hash::make('CorrectPassword123!'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'john@example.com',
            'password' => 'WrongPassword123!',
        ]);

        $response
            ->assertStatus(422)
            ->assertJsonPath('message', __('messages.invalid_credentials'));
    }

    public function test_login_rejects_user_without_password(): void
    {
        User::create([
            'first_name' => 'Google',
            'last_name' => 'User',
            'email' => 'google@example.com',
            'password' => null,
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'google@example.com',
            'password' => 'Password123!',
        ]);

        $response
            ->assertStatus(422)
            ->assertJsonPath('message', __('messages.invalid_credentials'));
    }

    public function test_login_with_valid_credentials_returns_token_and_user(): void
    {
        $user = User::create([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@example.com',
            'password' => Hash::make('Password123!'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'john@example.com',
            'password' => 'Password123!',
        ]);

        $response
            ->assertStatus(200)
            ->assertJsonPath(
                'message',
                __('messages.login_success')
            )
            ->assertJsonPath('user.id', $user->id)
            ->assertJsonPath('user.first_name', 'John')
            ->assertJsonPath('user.last_name', 'Doe')
            ->assertJsonPath('user.email', 'john@example.com')
            ->assertJsonPath('user.is_suspended', false)
            ->assertJsonStructure([
                'message',
                'token',
                'user',
            ]);

        $this->assertNotEmpty(
            $response->json('token')
        );

        $this->assertDatabaseHas('personal_access_tokens', [
            'tokenable_id' => $user->id,
            'tokenable_type' => User::class,
            'name' => 'login',
        ]);
    }
}

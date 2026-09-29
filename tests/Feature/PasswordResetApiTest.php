<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Laravel\Sanctum\PersonalAccessToken;
use Tests\TestCase;

class PasswordResetApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_forgot_password_requires_email(): void
    {
        $response = $this->postJson('/api/forgot-password', [
            'recaptcha_token' => 'test-token',
        ]);

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                'email',
            ]);
    }

    public function test_forgot_password_requires_recaptcha_token(): void
    {
        $response = $this->postJson('/api/forgot-password', [
            'email' => 'john@gmail.com',
        ]);

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                'recaptcha_token',
            ]);
    }

    public function test_forgot_password_rejects_invalid_email(): void
    {
        $response = $this->postJson('/api/forgot-password', [
            'email' => 'invalid-email',
            'recaptcha_token' => 'test-token',
        ]);

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                'email',
            ]);
    }

    public function test_forgot_password_is_rate_limited(): void
    {
        $payload = [
            'email' => 'john@gmail.com',
            'recaptcha_token' => 'test-token',
        ];

        for ($i = 0; $i < 5; $i++) {
            $this->postJson('/api/forgot-password', $payload);
        }

        $response = $this->postJson(
            '/api/forgot-password',
            $payload
        );

        $response->assertStatus(429);
    }

    public function test_reset_password_requires_token(): void
    {
        $user = User::factory()->create([
            'email' => 'john@gmail.com',
        ]);

        $response = $this->postJson('/api/reset-password', [
            'email' => $user->email,
            'password' => 'NewPassword1!',
            'password_confirmation' => 'NewPassword1!',
        ]);

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                'token',
            ]);
    }

    public function test_reset_password_requires_email(): void
    {
        $response = $this->postJson('/api/reset-password', [
            'token' => 'invalid-token',
            'password' => 'NewPassword1!',
            'password_confirmation' => 'NewPassword1!',
        ]);

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                'email',
            ]);
    }

    public function test_reset_password_rejects_invalid_email(): void
    {
        $response = $this->postJson('/api/reset-password', [
            'token' => 'invalid-token',
            'email' => 'invalid-email',
            'password' => 'NewPassword1!',
            'password_confirmation' => 'NewPassword1!',
        ]);

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                'email',
            ]);
    }

    public function test_reset_password_requires_password(): void
    {
        $response = $this->postJson('/api/reset-password', [
            'token' => 'invalid-token',
            'email' => 'john@gmail.com',
        ]);

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                'password',
            ]);
    }

    public function test_reset_password_requires_password_confirmation(): void
    {
        $response = $this->postJson('/api/reset-password', [
            'token' => 'invalid-token',
            'email' => 'john@gmail.com',
            'password' => 'NewPassword1!',
        ]);

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                'password',
            ]);
    }

    public function test_reset_password_rejects_mismatched_password_confirmation(): void
    {
        $response = $this->postJson('/api/reset-password', [
            'token' => 'invalid-token',
            'email' => 'john@gmail.com',
            'password' => 'NewPassword1!',
            'password_confirmation' => 'DifferentPassword1!',
        ]);

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                'password',
            ]);
    }

    public function test_reset_password_rejects_password_shorter_than_eight_characters(): void
    {
        $response = $this->postJson('/api/reset-password', [
            'token' => 'invalid-token',
            'email' => 'john@gmail.com',
            'password' => 'Ab1!',
            'password_confirmation' => 'Ab1!',
        ]);

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                'password',
            ]);
    }

    public function test_reset_password_requires_mixed_case_password(): void
    {
        $response = $this->postJson('/api/reset-password', [
            'token' => 'invalid-token',
            'email' => 'john@gmail.com',
            'password' => 'password1!',
            'password_confirmation' => 'password1!',
        ]);

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                'password',
            ]);
    }

    public function test_reset_password_requires_number(): void
    {
        $response = $this->postJson('/api/reset-password', [
            'token' => 'invalid-token',
            'email' => 'john@gmail.com',
            'password' => 'Password!',
            'password_confirmation' => 'Password!',
        ]);

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                'password',
            ]);
    }

    public function test_reset_password_requires_symbol(): void
    {
        $response = $this->postJson('/api/reset-password', [
            'token' => 'invalid-token',
            'email' => 'john@gmail.com',
            'password' => 'Password1',
            'password_confirmation' => 'Password1',
        ]);

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                'password',
            ]);
    }

    public function test_reset_password_rejects_invalid_token(): void
    {
        $user = User::factory()->create([
            'email' => 'john@gmail.com',
        ]);

        $response = $this->postJson('/api/reset-password', [
            'token' => 'invalid-token',
            'email' => $user->email,
            'password' => 'NewPassword1!',
            'password_confirmation' => 'NewPassword1!',
        ]);

        $response
            ->assertStatus(422)
            ->assertJson([
                'message' => __('messages.reset_failed'),
            ]);
    }

    public function test_user_can_reset_password_with_valid_token(): void
    {
        Notification::fake();

        $user = User::factory()->create([
            'email' => 'john@gmail.com',
            'password' => Hash::make('OldPassword1!'),
        ]);

        $token = Password::broker()->createToken($user);

        $response = $this->postJson('/api/reset-password', [
            'token' => $token,
            'email' => $user->email,
            'password' => 'NewPassword1!',
            'password_confirmation' => 'NewPassword1!',
        ]);

        $response
            ->assertOk()
            ->assertJson([
                'message' => __('messages.password_reset_success'),
            ]);

        $user->refresh();

        $this->assertTrue(
            Hash::check(
                'NewPassword1!',
                $user->password
            )
        );

        $this->assertFalse(
            Hash::check(
                'OldPassword1!',
                $user->password
            )
        );
    }

    public function test_reset_password_invalidates_existing_sanctum_tokens(): void
    {
        $user = User::factory()->create([
            'email' => 'john@gmail.com',
            'password' => Hash::make('OldPassword1!'),
        ]);

        $token = $user->createToken('test-token');

        $this->assertDatabaseHas('personal_access_tokens', [
            'id' => $token->accessToken->id,
            'tokenable_id' => $user->id,
            'tokenable_type' => User::class,
        ]);

        $resetToken = Password::broker()->createToken($user);

        $response = $this->postJson('/api/reset-password', [
            'token' => $resetToken,
            'email' => $user->email,
            'password' => 'NewPassword1!',
            'password_confirmation' => 'NewPassword1!',
        ]);

        $response->assertOk();

        $this->assertDatabaseMissing('personal_access_tokens', [
            'id' => $token->accessToken->id,
        ]);

        $this->assertSame(
            0,
            PersonalAccessToken::where(
                'tokenable_id',
                $user->id
            )
                ->where(
                    'tokenable_type',
                    User::class
                )
                ->count()
        );
    }

    public function test_reset_password_is_rate_limited(): void
    {
        $payload = [
            'token' => 'invalid-token',
            'email' => 'john@gmail.com',
            'password' => 'NewPassword1!',
            'password_confirmation' => 'NewPassword1!',
        ];

        for ($i = 0; $i < 10; $i++) {
            $this->postJson(
                '/api/reset-password',
                $payload
            );
        }

        $response = $this->postJson(
            '/api/reset-password',
            $payload
        );

        $response->assertStatus(429);
    }
}

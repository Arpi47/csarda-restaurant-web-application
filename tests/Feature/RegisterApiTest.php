<?php

namespace Tests\Feature;

use App\Mail\VerifyRegistrationMail;
use App\Models\PendingUser;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class RegisterApiTest extends TestCase
{
    use RefreshDatabase;

    private function fakeRecaptcha(float $score = 0.9): void
    {
        Http::fake([
            'https://www.google.com/recaptcha/api/siteverify' => Http::response([
                'success' => true,
                'score' => $score,
            ], 200),
        ]);
    }

    private function validRegistrationData(array $overrides = []): array
    {
        return array_merge([
            'recaptcha_token' => 'valid-recaptcha-token',
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@gmail.com',
            'password' => 'Password1!',
            'password_confirmation' => 'Password1!',
            'language' => 'en',
        ], $overrides);
    }

    public function test_registration_requires_recaptcha(): void
    {
        $response = $this->postJson('/api/register', [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@gmail.com',
            'password' => 'Password1!',
            'password_confirmation' => 'Password1!',
        ]);

        $response
            ->assertStatus(422)
            ->assertJson([
                'message' => __('messages.recaptcha_required'),
            ]);
    }

    public function test_registration_rejects_failed_recaptcha(): void
    {
        Http::fake([
            'https://www.google.com/recaptcha/api/siteverify' => Http::response([
                'success' => false,
                'score' => 0.9,
            ], 200),
        ]);

        $response = $this->postJson(
            '/api/register',
            $this->validRegistrationData()
        );

        $response
            ->assertStatus(422)
            ->assertJson([
                'message' => __('messages.invalid_captcha'),
            ]);
    }

    public function test_registration_rejects_recaptcha_score_below_0_5(): void
    {
        $this->fakeRecaptcha(0.49);

        $response = $this->postJson(
            '/api/register',
            $this->validRegistrationData()
        );

        $response
            ->assertStatus(422)
            ->assertJson([
                'message' => __('messages.invalid_captcha'),
            ]);
    }

    public function test_registration_accepts_recaptcha_score_of_exactly_0_5(): void
    {
        $this->fakeRecaptcha(0.5);
        Mail::fake();

        $response = $this->postJson(
            '/api/register',
            $this->validRegistrationData()
        );

        $response->assertOk();

        $this->assertDatabaseHas('pending_users', [
            'email' => 'john@gmail.com',
        ]);

        Mail::assertSent(VerifyRegistrationMail::class);
    }

    public function test_registration_requires_first_name(): void
    {
        $this->fakeRecaptcha();

        $response = $this->postJson(
            '/api/register',
            $this->validRegistrationData([
                'first_name' => '',
            ])
        );

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('first_name');
    }

    public function test_registration_requires_last_name(): void
    {
        $this->fakeRecaptcha();

        $response = $this->postJson(
            '/api/register',
            $this->validRegistrationData([
                'last_name' => '',
            ])
        );

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('last_name');
    }

    public function test_registration_requires_email(): void
    {
        $this->fakeRecaptcha();

        $response = $this->postJson(
            '/api/register',
            $this->validRegistrationData([
                'email' => '',
            ])
        );

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('email');
    }

    public function test_registration_requires_password(): void
    {
        $this->fakeRecaptcha();

        $response = $this->postJson(
            '/api/register',
            $this->validRegistrationData([
                'password' => '',
                'password_confirmation' => '',
            ])
        );

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('password');
    }

    public function test_registration_requires_password_confirmation(): void
    {
        $this->fakeRecaptcha();

        $response = $this->postJson(
            '/api/register',
            $this->validRegistrationData([
                'password_confirmation' => '',
            ])
        );

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('password');
    }

    public function test_registration_rejects_first_name_shorter_than_2_characters(): void
    {
        $this->fakeRecaptcha();

        $response = $this->postJson(
            '/api/register',
            $this->validRegistrationData([
                'first_name' => 'J',
            ])
        );

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('first_name');
    }

    public function test_registration_rejects_first_name_longer_than_50_characters(): void
    {
        $this->fakeRecaptcha();

        $response = $this->postJson(
            '/api/register',
            $this->validRegistrationData([
                'first_name' => str_repeat('J', 51),
            ])
        );

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('first_name');
    }

    public function test_registration_accepts_first_name_with_exactly_2_characters(): void
    {
        $this->fakeRecaptcha();
        Mail::fake();

        $response = $this->postJson(
            '/api/register',
            $this->validRegistrationData([
                'first_name' => 'Jo',
            ])
        );

        $response->assertOk();
    }

    public function test_registration_accepts_first_name_with_exactly_50_characters(): void
    {
        $this->fakeRecaptcha();
        Mail::fake();

        $response = $this->postJson(
            '/api/register',
            $this->validRegistrationData([
                'first_name' => str_repeat('J', 50),
            ])
        );

        $response->assertOk();
    }

    public function test_registration_rejects_last_name_shorter_than_2_characters(): void
    {
        $this->fakeRecaptcha();

        $response = $this->postJson(
            '/api/register',
            $this->validRegistrationData([
                'last_name' => 'D',
            ])
        );

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('last_name');
    }

    public function test_registration_rejects_last_name_longer_than_50_characters(): void
    {
        $this->fakeRecaptcha();

        $response = $this->postJson(
            '/api/register',
            $this->validRegistrationData([
                'last_name' => str_repeat('D', 51),
            ])
        );

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('last_name');
    }

    public function test_registration_accepts_last_name_with_exactly_2_characters(): void
    {
        $this->fakeRecaptcha();
        Mail::fake();

        $response = $this->postJson(
            '/api/register',
            $this->validRegistrationData([
                'last_name' => 'Do',
            ])
        );

        $response->assertOk();
    }

    public function test_registration_accepts_last_name_with_exactly_50_characters(): void
    {
        $this->fakeRecaptcha();
        Mail::fake();

        $response = $this->postJson(
            '/api/register',
            $this->validRegistrationData([
                'last_name' => str_repeat('D', 50),
            ])
        );

        $response->assertOk();
    }

    public function test_registration_rejects_invalid_email(): void
    {
        $this->fakeRecaptcha();

        $response = $this->postJson(
            '/api/register',
            $this->validRegistrationData([
                'email' => 'invalid-email',
            ])
        );

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('email');
    }

    public function test_registration_rejects_existing_email(): void
    {
        $this->fakeRecaptcha();

        User::create([
            'first_name' => 'Existing',
            'last_name' => 'User',
            'email' => 'john@gmail.com',
            'password' => Hash::make('Password1!'),
            'email_verified_at' => now(),
        ]);

        $response = $this->postJson(
            '/api/register',
            $this->validRegistrationData()
        );

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('email');
    }

    public function test_registration_rejects_blocked_email_domain(): void
    {
        $this->fakeRecaptcha();

        $response = $this->postJson(
            '/api/register',
            $this->validRegistrationData([
                'email' => 'john@tempmail.com',
            ])
        );

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('email');
    }

    public function test_registration_rejects_password_shorter_than_8_characters(): void
    {
        $this->fakeRecaptcha();

        $response = $this->postJson(
            '/api/register',
            $this->validRegistrationData([
                'password' => 'Pass1!',
                'password_confirmation' => 'Pass1!',
            ])
        );

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('password');
    }

    public function test_registration_rejects_password_without_lowercase_letter(): void
    {
        $this->fakeRecaptcha();

        $response = $this->postJson(
            '/api/register',
            $this->validRegistrationData([
                'password' => 'PASSWORD1!',
                'password_confirmation' => 'PASSWORD1!',
            ])
        );

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('password');
    }

    public function test_registration_rejects_password_without_uppercase_letter(): void
    {
        $this->fakeRecaptcha();

        $response = $this->postJson(
            '/api/register',
            $this->validRegistrationData([
                'password' => 'password1!',
                'password_confirmation' => 'password1!',
            ])
        );

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('password');
    }

    public function test_registration_rejects_password_without_number(): void
    {
        $this->fakeRecaptcha();

        $response = $this->postJson(
            '/api/register',
            $this->validRegistrationData([
                'password' => 'Password!',
                'password_confirmation' => 'Password!',
            ])
        );

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('password');
    }

    public function test_registration_rejects_password_without_special_character(): void
    {
        $this->fakeRecaptcha();

        $response = $this->postJson(
            '/api/register',
            $this->validRegistrationData([
                'password' => 'Password1',
                'password_confirmation' => 'Password1',
            ])
        );

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('password');
    }

    public function test_registration_rejects_mismatched_password_confirmation(): void
    {
        $this->fakeRecaptcha();

        $response = $this->postJson(
            '/api/register',
            $this->validRegistrationData([
                'password_confirmation' => 'Different1!',
            ])
        );

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('password');
    }

    public function test_successful_registration_creates_pending_user(): void
    {
        $this->fakeRecaptcha();
        Mail::fake();

        $response = $this->postJson(
            '/api/register',
            $this->validRegistrationData()
        );

        $response
            ->assertOk()
            ->assertJson([
                'message' => __('messages.registration_successful'),
            ]);

        $pendingUser = PendingUser::where(
            'email',
            'john@gmail.com'
        )->first();

        $this->assertNotNull($pendingUser);
        $this->assertSame('John', $pendingUser->first_name);
        $this->assertSame('Doe', $pendingUser->last_name);
        $this->assertTrue(
            Hash::check('Password1!', $pendingUser->password)
        );
        $this->assertNotSame(
            'Password1!',
            $pendingUser->password
        );
        $this->assertSame(
            64,
            strlen($pendingUser->verification_token)
        );
        $this->assertNotNull($pendingUser->expires_at);
        $this->assertSame('en', $pendingUser->language);

        Mail::assertSent(
            VerifyRegistrationMail::class,
            function ($mail) {
                return $mail->hasTo('john@gmail.com');
            }
        );
    }

    public function test_registration_saves_selected_language(): void
    {
        $this->fakeRecaptcha();
        Mail::fake();

        foreach (['en', 'hu', 'sr_lat', 'sr_cyrl'] as $language) {
            $email = 'register-'.$language.'@gmail.com';

            $response = $this->postJson(
                '/api/register',
                $this->validRegistrationData([
                    'email' => $email,
                    'language' => $language,
                ])
            );

            $response->assertOk();

            $this->assertDatabaseHas('pending_users', [
                'email' => $email,
                'language' => $language,
            ]);
        }
    }

    public function test_registration_falls_back_to_english_for_unknown_language(): void
    {
        $this->fakeRecaptcha();
        Mail::fake();

        $response = $this->postJson(
            '/api/register',
            $this->validRegistrationData([
                'language' => 'de',
            ])
        );

        $response->assertOk();

        $this->assertDatabaseHas('pending_users', [
            'email' => 'john@gmail.com',
            'language' => 'en',
        ]);
    }

    public function test_new_registration_deletes_existing_pending_user_with_same_email(): void
    {
        $this->fakeRecaptcha();
        Mail::fake();

        PendingUser::create([
            'first_name' => 'Old',
            'last_name' => 'User',
            'email' => 'john@gmail.com',
            'password' => Hash::make('OldPassword1!'),
            'language' => 'en',
            'verification_token' => str_repeat('a', 64),
            'expires_at' => now()->addHours(24),
        ]);

        $response = $this->postJson(
            '/api/register',
            $this->validRegistrationData([
                'first_name' => 'John',
                'last_name' => 'Doe',
            ])
        );

        $response->assertOk();

        $pendingUsers = PendingUser::where(
            'email',
            'john@gmail.com'
        )->get();

        $this->assertCount(1, $pendingUsers);
        $this->assertSame(
            'John',
            $pendingUsers->first()->first_name
        );
        $this->assertSame(
            'Doe',
            $pendingUsers->first()->last_name
        );
        $this->assertNotSame(
            str_repeat('a', 64),
            $pendingUsers->first()->verification_token
        );
    }
}

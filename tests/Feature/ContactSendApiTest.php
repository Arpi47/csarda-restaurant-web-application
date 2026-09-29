<?php

namespace Tests\Feature;

use App\Mail\ContactMessageMail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class ContactSendApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_contact_endpoint_requires_recaptcha_token(): void
    {
        $response = $this->postJson('/api/contact', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'message' => 'Test message.',
        ]);

        $response
            ->assertStatus(422)
            ->assertJson([
                'message' => 'reCAPTCHA is required.',
            ]);
    }

    public function test_contact_endpoint_rejects_failed_recaptcha(): void
    {
        Http::fake([
            'https://www.google.com/recaptcha/api/siteverify' => Http::response([
                'success' => false,
                'score' => 0.9,
            ], 200),
        ]);

        $response = $this->postJson('/api/contact', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'message' => 'Test message.',
            'g-recaptcha-response' => 'invalid-token',
        ]);

        $response
            ->assertStatus(422)
            ->assertJson([
                'message' => 'Invalid reCAPTCHA.',
            ]);
    }

    public function test_contact_endpoint_rejects_recaptcha_score_below_threshold(): void
    {
        Http::fake([
            'https://www.google.com/recaptcha/api/siteverify' => Http::response([
                'success' => true,
                'score' => 0.49,
            ], 200),
        ]);

        $response = $this->postJson('/api/contact', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'message' => 'Test message.',
            'g-recaptcha-response' => 'low-score-token',
        ]);

        $response
            ->assertStatus(422)
            ->assertJson([
                'message' => 'Invalid reCAPTCHA.',
            ]);
    }

    public function test_contact_endpoint_accepts_recaptcha_score_of_exactly_zero_point_five(): void
    {
        Http::fake([
            'https://www.google.com/recaptcha/api/siteverify' => Http::response([
                'success' => true,
                'score' => 0.5,
            ], 200),
        ]);

        Mail::fake();

        $response = $this->postJson('/api/contact', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'message' => 'Test message.',
            'g-recaptcha-response' => 'threshold-token',
        ]);

        $response
            ->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Message sent successfully.',
            ]);

        Mail::assertSent(
            ContactMessageMail::class,
            function ($mail) {
                return $mail->hasTo('info@csarda.com');
            }
        );
    }

    public function test_contact_endpoint_validates_required_fields(): void
    {
        Http::fake([
            'https://www.google.com/recaptcha/api/siteverify' => Http::response([
                'success' => true,
                'score' => 0.9,
            ], 200),
        ]);

        $response = $this->postJson('/api/contact', [
            'g-recaptcha-response' => 'valid-token',
        ]);

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                'name',
                'email',
                'message',
            ]);
    }

    public function test_contact_endpoint_rejects_invalid_email(): void
    {
        Http::fake([
            'https://www.google.com/recaptcha/api/siteverify' => Http::response([
                'success' => true,
                'score' => 0.9,
            ], 200),
        ]);

        $response = $this->postJson('/api/contact', [
            'name' => 'John Doe',
            'email' => 'not-an-email',
            'message' => 'Test message.',
            'g-recaptcha-response' => 'valid-token',
        ]);

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                'email',
            ]);
    }

    public function test_contact_endpoint_rejects_name_longer_than_100_characters(): void
    {
        Http::fake([
            'https://www.google.com/recaptcha/api/siteverify' => Http::response([
                'success' => true,
                'score' => 0.9,
            ], 200),
        ]);

        $response = $this->postJson('/api/contact', [
            'name' => str_repeat('A', 101),
            'email' => 'john@example.com',
            'message' => 'Test message.',
            'g-recaptcha-response' => 'valid-token',
        ]);

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                'name',
            ]);
    }

    public function test_contact_endpoint_rejects_message_longer_than_3000_characters(): void
    {
        Http::fake([
            'https://www.google.com/recaptcha/api/siteverify' => Http::response([
                'success' => true,
                'score' => 0.9,
            ], 200),
        ]);

        $response = $this->postJson('/api/contact', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'message' => str_repeat('A', 3001),
            'g-recaptcha-response' => 'valid-token',
        ]);

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                'message',
            ]);
    }
}

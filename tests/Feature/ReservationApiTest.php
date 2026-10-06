<?php

namespace Tests\Feature;

use App\Models\HungarianHoliday;
use App\Models\OpeningHour;
use App\Models\ReservationEventType;
use App\Models\SerbianHoliday;
use App\Models\SpecialOpeningHour;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ReservationApiTest extends TestCase
{
    use RefreshDatabase;

    private function setupRecaptcha(): void
    {
        Http::fake([
            'https://www.google.com/recaptcha/api/siteverify' => Http::response([
                'success' => true,
                'score' => 0.9,
            ]),
        ]);
    }

    private function createUser(): User
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        return $user;
    }

    private function createEventType(): ReservationEventType
    {
        return ReservationEventType::create([
            'name_en' => 'Birthday',
            'name_hu' => 'Születésnap',
            'name_sr' => 'Rođendan',
            'name_sr_cyrl' => 'Рођендан',
            'is_active' => true,
            'sort_order' => 1,
        ]);
    }

    private function createWeeklyOpeningHour(
        $date,
        string $openTime = '11:00',
        string $closeTime = '22:00',
        string $lastReservationTime = '21:00'
    ): void {
        OpeningHour::create([
            'type' => 'restaurant',
            'day_of_week' => $date->dayOfWeekIso,
            'is_active' => true,
            'open_time' => $openTime,
            'close_time' => $closeTime,
            'last_reservation_time' => $lastReservationTime,
        ]);
    }

    private function reservationPayload(
        $date,
        string $time,
        int $eventTypeId,
        int $guests = 2
    ): array {
        return [
            'date' => $date->toDateString(),
            'time' => $time,
            'guests' => $guests,
            'event_type_id' => $eventTypeId,
            'g-recaptcha-response' => 'test-recaptcha-token',
        ];
    }

    public function test_reservation_is_rejected_when_hungarian_holiday_is_closed(): void
    {
        $this->setupRecaptcha();

        $user = $this->createUser();
        $eventType = $this->createEventType();

        $date = now()->addDays(2)->startOfDay();

        $this->createWeeklyOpeningHour($date);

        HungarianHoliday::create([
            'google_event_id' => 'hungarian-holiday-test',
            'name' => 'Test Hungarian Holiday',
            'date' => $date->toDateString(),

            'restaurant_is_active' => false,
            'restaurant_open_time' => null,
            'restaurant_close_time' => null,
            'restaurant_last_reservation_time' => null,

            'kitchen_is_active' => false,
            'kitchen_open_time' => null,
            'kitchen_close_time' => null,
            'kitchen_last_order_time' => null,
        ]);

        $response = $this->postJson(
            '/api/reservation',
            $this->reservationPayload(
                $date,
                '18:00',
                $eventType->id
            )
        );

        $response
            ->assertOk()
            ->assertJson([
                'success' => false,
                'message' => __('messages.restaurant_closed', [
                    'day' => $date->translatedFormat('l'),
                ]),
            ]);

        $this->assertDatabaseMissing('reservations', [
            'user_id' => $user->id,
            'date_time' => $date->toDateString().' 18:00:00',
        ]);
    }

    public function test_reservation_is_rejected_when_serbian_holiday_is_closed(): void
    {
        $this->setupRecaptcha();

        $user = $this->createUser();
        $eventType = $this->createEventType();

        $date = now()->addDays(2)->startOfDay();

        $this->createWeeklyOpeningHour($date);

        SerbianHoliday::create([
            'google_event_id' => 'serbian-holiday-test',
            'name' => 'Test Serbian Holiday',
            'date' => $date->toDateString(),

            'restaurant_is_active' => false,
            'restaurant_open_time' => null,
            'restaurant_close_time' => null,
            'restaurant_last_reservation_time' => null,

            'kitchen_is_active' => false,
            'kitchen_open_time' => null,
            'kitchen_close_time' => null,
            'kitchen_last_reservation_time' => null,
        ]);

        $response = $this->postJson(
            '/api/reservation',
            $this->reservationPayload(
                $date,
                '18:00',
                $eventType->id
            )
        );

        $response
            ->assertOk()
            ->assertJson([
                'success' => false,
                'message' => __('messages.restaurant_closed', [
                    'day' => $date->translatedFormat('l'),
                ]),
            ]);

        $this->assertDatabaseMissing('reservations', [
            'user_id' => $user->id,
            'date_time' => $date->toDateString().' 18:00:00',
        ]);
    }

    public function test_reservation_is_rejected_when_serbian_holiday_is_closed_even_if_hungarian_holiday_is_open(): void
    {
        $this->setupRecaptcha();

        $user = $this->createUser();
        $eventType = $this->createEventType();

        $date = now()->addDays(2)->startOfDay();

        SerbianHoliday::create([
            'google_event_id' => 'serbian-holiday-closed',
            'name' => 'Serbian Closed',
            'date' => $date->toDateString(),

            'restaurant_is_active' => false,
            'restaurant_open_time' => null,
            'restaurant_close_time' => null,
            'restaurant_last_reservation_time' => null,

            'kitchen_is_active' => true,
            'kitchen_open_time' => '11:00',
            'kitchen_close_time' => '22:00',
            'kitchen_last_order_time' => '21:00',
        ]);

        HungarianHoliday::create([
            'google_event_id' => 'hungarian-holiday-open',
            'name' => 'Hungarian Open',
            'date' => $date->toDateString(),

            'restaurant_is_active' => true,
            'restaurant_open_time' => '11:00',
            'restaurant_close_time' => '22:00',
            'restaurant_last_reservation_time' => '21:00',

            'kitchen_is_active' => true,
            'kitchen_open_time' => '11:00',
            'kitchen_close_time' => '22:00',
            'kitchen_last_order_time' => '21:00',
        ]);

        $response = $this->postJson(
            '/api/reservation',
            $this->reservationPayload(
                $date,
                '18:00',
                $eventType->id
            )
        );

        $response
            ->assertOk()
            ->assertJson([
                'success' => false,
                'message' => __('messages.restaurant_closed', [
                    'day' => $date->translatedFormat('l'),
                ]),
            ]);

        $this->assertDatabaseMissing('reservations', [
            'user_id' => $user->id,
        ]);
    }

    public function test_reservation_is_rejected_when_hungarian_holiday_is_closed_even_if_serbian_holiday_is_open(): void
    {
        $this->setupRecaptcha();

        $user = $this->createUser();
        $eventType = $this->createEventType();

        $date = now()->addDays(2)->startOfDay();

        SerbianHoliday::create([
            'google_event_id' => 'serbian-holiday-open',
            'name' => 'Serbian Open',
            'date' => $date->toDateString(),

            'restaurant_is_active' => true,
            'restaurant_open_time' => '11:00',
            'restaurant_close_time' => '22:00',
            'restaurant_last_reservation_time' => '21:00',

            'kitchen_is_active' => true,
            'kitchen_open_time' => '11:00',
            'kitchen_close_time' => '22:00',
            'kitchen_last_order_time' => '21:00',
        ]);

        HungarianHoliday::create([
            'google_event_id' => 'hungarian-holiday-closed',
            'name' => 'Hungarian Closed',
            'date' => $date->toDateString(),

            'restaurant_is_active' => false,
            'restaurant_open_time' => null,
            'restaurant_close_time' => null,
            'restaurant_last_reservation_time' => null,

            'kitchen_is_active' => false,
            'kitchen_open_time' => null,
            'kitchen_close_time' => null,
            'kitchen_last_order_time' => null,
        ]);

        $response = $this->postJson(
            '/api/reservation',
            $this->reservationPayload(
                $date,
                '18:00',
                $eventType->id
            )
        );

        $response
            ->assertOk()
            ->assertJson([
                'success' => false,
                'message' => __('messages.restaurant_closed', [
                    'day' => $date->translatedFormat('l'),
                ]),
            ]);

        $this->assertDatabaseMissing('reservations', [
            'user_id' => $user->id,
        ]);
    }

    public function test_reservation_is_rejected_when_both_holidays_are_closed(): void
    {
        $this->setupRecaptcha();

        $user = $this->createUser();
        $eventType = $this->createEventType();

        $date = now()->addDays(2)->startOfDay();

        SerbianHoliday::create([
            'google_event_id' => 'serbian-holiday-closed',
            'name' => 'Serbian Closed',
            'date' => $date->toDateString(),

            'restaurant_is_active' => false,
            'restaurant_open_time' => null,
            'restaurant_close_time' => null,
            'restaurant_last_reservation_time' => null,

            'kitchen_is_active' => false,
            'kitchen_open_time' => null,
            'kitchen_close_time' => null,
            'kitchen_last_order_time' => null,
        ]);

        HungarianHoliday::create([
            'google_event_id' => 'hungarian-holiday-closed',
            'name' => 'Hungarian Closed',
            'date' => $date->toDateString(),

            'restaurant_is_active' => false,
            'restaurant_open_time' => null,
            'restaurant_close_time' => null,
            'restaurant_last_reservation_time' => null,

            'kitchen_is_active' => false,
            'kitchen_open_time' => null,
            'kitchen_close_time' => null,
            'kitchen_last_order_time' => null,
        ]);

        $response = $this->postJson(
            '/api/reservation',
            $this->reservationPayload(
                $date,
                '18:00',
                $eventType->id
            )
        );

        $response
            ->assertOk()
            ->assertJson([
                'success' => false,
                'message' => __('messages.restaurant_closed', [
                    'day' => $date->translatedFormat('l'),
                ]),
            ]);

        $this->assertDatabaseMissing('reservations', [
            'user_id' => $user->id,
        ]);
    }

    public function test_reservation_uses_narrower_interval_when_both_holidays_are_open(): void
    {
        $this->setupRecaptcha();

        $user = $this->createUser();
        $eventType = $this->createEventType();

        $date = now()->addDays(2)->startOfDay();

        SerbianHoliday::create([
            'google_event_id' => 'serbian-holiday-open',
            'name' => 'Serbian Open',
            'date' => $date->toDateString(),

            'restaurant_is_active' => true,
            'restaurant_open_time' => '11:00',
            'restaurant_close_time' => '22:00',
            'restaurant_last_reservation_time' => '21:00',

            'kitchen_is_active' => true,
            'kitchen_open_time' => '11:00',
            'kitchen_close_time' => '22:00',
            'kitchen_last_order_time' => '21:00',
        ]);

        HungarianHoliday::create([
            'google_event_id' => 'hungarian-holiday-open',
            'name' => 'Hungarian Open',
            'date' => $date->toDateString(),

            'restaurant_is_active' => true,
            'restaurant_open_time' => '12:00',
            'restaurant_close_time' => '21:00',
            'restaurant_last_reservation_time' => '20:00',

            'kitchen_is_active' => true,
            'kitchen_open_time' => '12:00',
            'kitchen_close_time' => '21:00',
            'kitchen_last_order_time' => '20:00',
        ]);

        $response = $this->postJson(
            '/api/reservation',
            $this->reservationPayload(
                $date,
                '20:00',
                $eventType->id
            )
        );

        $response
            ->assertOk()
            ->assertJson([
                'success' => true,
            ]);

        $this->assertDatabaseHas('reservations', [
            'user_id' => $user->id,
            'date_time' => $date->toDateString().' 20:00:00',
            'guests' => 2,
            'event_type_id' => $eventType->id,
        ]);
    }

    public function test_reservation_uses_only_serbian_holiday_when_no_hungarian_holiday_exists(): void
    {
        $this->setupRecaptcha();

        $user = $this->createUser();
        $eventType = $this->createEventType();

        $date = now()->addDays(2)->startOfDay();

        SerbianHoliday::create([
            'google_event_id' => 'serbian-holiday-open',
            'name' => 'Serbian Open',
            'date' => $date->toDateString(),

            'restaurant_is_active' => true,
            'restaurant_open_time' => '13:00',
            'restaurant_close_time' => '20:00',
            'restaurant_last_reservation_time' => '19:00',

            'kitchen_is_active' => true,
            'kitchen_open_time' => '13:00',
            'kitchen_close_time' => '20:00',
            'kitchen_last_order_time' => '19:00',
        ]);

        $response = $this->postJson(
            '/api/reservation',
            $this->reservationPayload(
                $date,
                '19:00',
                $eventType->id
            )
        );

        $response
            ->assertOk()
            ->assertJson([
                'success' => true,
            ]);

        $this->assertDatabaseHas('reservations', [
            'user_id' => $user->id,
            'date_time' => $date->toDateString().' 19:00:00',
        ]);
    }

    public function test_reservation_uses_only_hungarian_holiday_when_no_serbian_holiday_exists(): void
    {
        $this->setupRecaptcha();

        $user = $this->createUser();
        $eventType = $this->createEventType();

        $date = now()->addDays(2)->startOfDay();

        HungarianHoliday::create([
            'google_event_id' => 'hungarian-holiday-open',
            'name' => 'Hungarian Open',
            'date' => $date->toDateString(),

            'restaurant_is_active' => true,
            'restaurant_open_time' => '14:00',
            'restaurant_close_time' => '20:00',
            'restaurant_last_reservation_time' => '19:00',

            'kitchen_is_active' => true,
            'kitchen_open_time' => '14:00',
            'kitchen_close_time' => '20:00',
            'kitchen_last_order_time' => '19:00',
        ]);

        $response = $this->postJson(
            '/api/reservation',
            $this->reservationPayload(
                $date,
                '19:00',
                $eventType->id
            )
        );

        $response
            ->assertOk()
            ->assertJson([
                'success' => true,
            ]);

        $this->assertDatabaseHas('reservations', [
            'user_id' => $user->id,
            'date_time' => $date->toDateString().' 19:00:00',
        ]);
    }

    public function test_reservation_uses_weekly_opening_hours_when_no_holiday_exists(): void
    {
        $this->setupRecaptcha();

        $user = $this->createUser();
        $eventType = $this->createEventType();

        $date = now()->addDays(2)->startOfDay();

        $this->createWeeklyOpeningHour(
            $date,
            '11:00',
            '22:00',
            '21:00'
        );

        $response = $this->postJson(
            '/api/reservation',
            $this->reservationPayload(
                $date,
                '18:00',
                $eventType->id
            )
        );

        $response
            ->assertOk()
            ->assertJson([
                'success' => true,
            ]);

        $this->assertDatabaseHas('reservations', [
            'user_id' => $user->id,
            'date_time' => $date->toDateString().' 18:00:00',
        ]);
    }

    public function test_reservation_is_accepted_at_midnight_for_overnight_special_opening_hour(): void
    {
        $this->setupRecaptcha();

        $user = $this->createUser();
        $eventType = $this->createEventType();

        $openingDate = now()
            ->addDays(2)
            ->startOfDay();

        $reservationDate = $openingDate->copy()
            ->addDay();

        SpecialOpeningHour::create([
            'type' => 'restaurant',
            'date' => $openingDate->toDateString(),
            'is_active' => true,
            'open_time' => '21:00',
            'close_time' => '06:00',
            'last_reservation_time' => '00:00',
        ]);

        $response = $this->postJson(
            '/api/reservation',
            $this->reservationPayload(
                $reservationDate,
                '00:00',
                $eventType->id
            )
        );

        $response
            ->assertOk()
            ->assertJson([
                'success' => true,
            ]);

        $this->assertDatabaseHas('reservations', [
            'user_id' => $user->id,
            'date_time' => $reservationDate->toDateString().' 00:00:00',
            'guests' => 2,
            'event_type_id' => $eventType->id,
        ]);
    }

    public function test_reservation_is_rejected_before_overnight_special_opening_hour(): void
    {
        $this->setupRecaptcha();

        $user = $this->createUser();
        $eventType = $this->createEventType();

        $date = now()
            ->addDays(2)
            ->startOfDay();

        SpecialOpeningHour::create([
            'type' => 'restaurant',
            'date' => $date->toDateString(),
            'is_active' => true,
            'open_time' => '21:00',
            'close_time' => '06:00',
            'last_reservation_time' => '00:00',
        ]);

        $response = $this->postJson(
            '/api/reservation',
            $this->reservationPayload(
                $date,
                '20:59',
                $eventType->id
            )
        );

        $response
            ->assertOk()
            ->assertJson([
                'success' => false,
                'message' => __('messages.time_out_of_hours', [
                    'open' => '21:00',
                    'close' => '00:00',
                ]),
            ]);

        $this->assertDatabaseMissing('reservations', [
            'user_id' => $user->id,
        ]);
    }

    public function test_reservation_is_accepted_before_midnight_for_overnight_special_opening_hour(): void
    {
        $this->setupRecaptcha();

        $user = $this->createUser();
        $eventType = $this->createEventType();

        $date = now()
            ->addDays(2)
            ->startOfDay();

        SpecialOpeningHour::create([
            'type' => 'restaurant',
            'date' => $date->toDateString(),
            'is_active' => true,
            'open_time' => '21:00',
            'close_time' => '06:00',
            'last_reservation_time' => '00:00',
        ]);

        $response = $this->postJson(
            '/api/reservation',
            $this->reservationPayload(
                $date,
                '23:59',
                $eventType->id
            )
        );

        $response
            ->assertOk()
            ->assertJson([
                'success' => true,
            ]);

        $this->assertDatabaseHas('reservations', [
            'user_id' => $user->id,
            'date_time' => $date->toDateString().' 23:59:00',
        ]);
    }

    public function test_reservation_is_rejected_after_last_reservation_time_for_overnight_special_opening_hour(): void
    {
        $this->setupRecaptcha();

        $user = $this->createUser();
        $eventType = $this->createEventType();

        $openingDate = now()
            ->addDays(2)
            ->startOfDay();

        $reservationDate = $openingDate->copy()
            ->addDay();

        SpecialOpeningHour::create([
            'type' => 'restaurant',
            'date' => $openingDate->toDateString(),
            'is_active' => true,
            'open_time' => '21:00',
            'close_time' => '06:00',
            'last_reservation_time' => '00:00',
        ]);

        $response = $this->postJson(
            '/api/reservation',
            $this->reservationPayload(
                $reservationDate,
                '00:01',
                $eventType->id
            )
        );

        $response
            ->assertOk()
            ->assertJson([
                'success' => false,
                'message' => __('messages.time_out_of_hours', [
                    'open' => '21:00',
                    'close' => '00:00',
                ]),
            ]);

        $this->assertDatabaseMissing('reservations', [
            'user_id' => $user->id,
        ]);
    }

    public function test_reservation_is_rejected_at_closing_time_for_overnight_special_opening_hour(): void
    {
        $this->setupRecaptcha();

        $user = $this->createUser();
        $eventType = $this->createEventType();

        $openingDate = now()
            ->addDays(2)
            ->startOfDay();

        $reservationDate = $openingDate->copy()
            ->addDay();

        SpecialOpeningHour::create([
            'type' => 'restaurant',
            'date' => $openingDate->toDateString(),
            'is_active' => true,
            'open_time' => '21:00',
            'close_time' => '06:00',
            'last_reservation_time' => '00:00',
        ]);

        $response = $this->postJson(
            '/api/reservation',
            $this->reservationPayload(
                $reservationDate,
                '06:00',
                $eventType->id
            )
        );

        $response
            ->assertOk()
            ->assertJson([
                'success' => false,
                'message' => __('messages.time_out_of_hours', [
                    'open' => '21:00',
                    'close' => '00:00',
                ]),
            ]);

        $this->assertDatabaseMissing('reservations', [
            'user_id' => $user->id,
        ]);
    }
}

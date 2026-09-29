<?php

namespace Tests\Feature;

use App\Models\HungarianHoliday;
use App\Models\OpeningHour;
use App\Models\SerbianHoliday;
use App\Models\SpecialOpeningHour;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OpeningHoursApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_opening_hours_endpoint_returns_all_opening_hours_data_in_correct_order(): void
    {
        OpeningHour::create([
            'type' => 'restaurant',
            'day_of_week' => 2,
            'is_active' => true,
            'open_time' => '11:00',
            'close_time' => '22:00',
            'last_reservation_time' => '21:00',
        ]);

        OpeningHour::create([
            'type' => 'restaurant',
            'day_of_week' => 1,
            'is_active' => false,
            'open_time' => null,
            'close_time' => null,
            'last_reservation_time' => null,
        ]);

        OpeningHour::create([
            'type' => 'kitchen',
            'day_of_week' => 2,
            'is_active' => true,
            'open_time' => '11:30',
            'close_time' => '21:30',
            'last_reservation_time' => '21:00',
        ]);

        SpecialOpeningHour::create([
            'type' => 'restaurant',
            'date' => '2026-12-24',
            'is_active' => true,
            'open_time' => '11:00',
            'close_time' => '18:00',
            'last_reservation_time' => '17:00',
        ]);

        SpecialOpeningHour::create([
            'type' => 'kitchen',
            'date' => '2026-12-24',
            'is_active' => true,
            'open_time' => '11:30',
            'close_time' => '17:30',
            'last_reservation_time' => '17:00',
        ]);

        SerbianHoliday::create([
            'google_event_id' => 'test-serbian-holiday',
            'name' => 'Test Serbian Holiday',
            'date' => '2026-12-25',
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
            'google_event_id' => 'test-hungarian-holiday',
            'name' => 'Test Hungarian Holiday',
            'date' => '2026-12-26',
            'restaurant_is_active' => true,
            'restaurant_open_time' => '12:00',
            'restaurant_close_time' => '20:00',
            'restaurant_last_reservation_time' => '19:00',
            'kitchen_is_active' => true,
            'kitchen_open_time' => '12:00',
            'kitchen_close_time' => '19:30',
            'kitchen_last_order_time' => '19:00',
        ]);

        $response = $this->getJson('/api/opening-hours');

        $response
            ->assertStatus(200)
            ->assertJsonCount(2, 'restaurant_weekly')
            ->assertJsonCount(1, 'kitchen_weekly')
            ->assertJsonCount(1, 'restaurant_special')
            ->assertJsonCount(1, 'kitchen_special')
            ->assertJsonCount(1, 'serbian_holidays')
            ->assertJsonCount(1, 'hungarian_holidays');

        $response
            ->assertJsonPath('restaurant_weekly.0.day_of_week', 1)
            ->assertJsonPath('restaurant_weekly.0.is_active', false)
            ->assertJsonPath('restaurant_weekly.1.day_of_week', 2)
            ->assertJsonPath('restaurant_weekly.1.is_active', true)
            ->assertJsonPath('restaurant_weekly.1.open_time', '11:00')
            ->assertJsonPath('restaurant_weekly.1.close_time', '22:00')
            ->assertJsonPath('restaurant_weekly.1.last_reservation_time', '21:00');

        $response
            ->assertJsonPath('kitchen_weekly.0.day_of_week', 2)
            ->assertJsonPath('kitchen_weekly.0.is_active', true)
            ->assertJsonPath('kitchen_weekly.0.open_time', '11:30')
            ->assertJsonPath('kitchen_weekly.0.close_time', '21:30')
            ->assertJsonPath('kitchen_weekly.0.last_reservation_time', '21:00');

        $response
            ->assertJsonPath(
                'restaurant_special.0.date',
                '2026-12-24T00:00:00.000000Z'
            )
            ->assertJsonPath('restaurant_special.0.is_active', true)
            ->assertJsonPath('restaurant_special.0.open_time', '11:00')
            ->assertJsonPath('restaurant_special.0.close_time', '18:00')
            ->assertJsonPath(
                'restaurant_special.0.last_reservation_time',
                '17:00'
            );

        $response
            ->assertJsonPath(
                'kitchen_special.0.date',
                '2026-12-24T00:00:00.000000Z'
            )
            ->assertJsonPath('kitchen_special.0.is_active', true)
            ->assertJsonPath('kitchen_special.0.open_time', '11:30')
            ->assertJsonPath('kitchen_special.0.close_time', '17:30')
            ->assertJsonPath(
                'kitchen_special.0.last_reservation_time',
                '17:00'
            );

        $response
            ->assertJsonPath(
                'serbian_holidays.0.date',
                '2026-12-25T00:00:00.000000Z'
            )
            ->assertJsonPath(
                'serbian_holidays.0.restaurant_is_active',
                false
            )
            ->assertJsonPath(
                'serbian_holidays.0.kitchen_is_active',
                false
            );

        $response
            ->assertJsonPath(
                'hungarian_holidays.0.date',
                '2026-12-26T00:00:00.000000Z'
            )
            ->assertJsonPath(
                'hungarian_holidays.0.restaurant_is_active',
                true
            )
            ->assertJsonPath(
                'hungarian_holidays.0.restaurant_open_time',
                '12:00'
            )
            ->assertJsonPath(
                'hungarian_holidays.0.restaurant_close_time',
                '20:00'
            )
            ->assertJsonPath(
                'hungarian_holidays.0.restaurant_last_reservation_time',
                '19:00'
            )
            ->assertJsonPath(
                'hungarian_holidays.0.kitchen_is_active',
                true
            )
            ->assertJsonPath(
                'hungarian_holidays.0.kitchen_open_time',
                '12:00'
            )
            ->assertJsonPath(
                'hungarian_holidays.0.kitchen_close_time',
                '19:30'
            )
            ->assertJsonPath(
                'hungarian_holidays.0.kitchen_last_order_time',
                '19:00'
            );
    }
}

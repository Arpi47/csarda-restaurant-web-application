<?php

namespace Database\Seeders;

use App\Models\OpeningHour;
use Illuminate\Database\Seeder;

class OpeningHourSeeder extends Seeder
{
    public function run(): void
    {
        $restaurantOpeningHours = [
            [
                'type' => 'restaurant',
                'day_of_week' => 1,
                'is_active' => false,
                'open_time' => null,
                'close_time' => null,
                'last_reservation_time' => null,
            ],
            [
                'type' => 'restaurant',
                'day_of_week' => 2,
                'is_active' => true,
                'open_time' => '11:00',
                'close_time' => '22:00',
                'last_reservation_time' => '21:00',
            ],
            [
                'type' => 'restaurant',
                'day_of_week' => 3,
                'is_active' => true,
                'open_time' => '11:00',
                'close_time' => '22:00',
                'last_reservation_time' => '21:00',
            ],
            [
                'type' => 'restaurant',
                'day_of_week' => 4,
                'is_active' => true,
                'open_time' => '11:00',
                'close_time' => '22:00',
                'last_reservation_time' => '21:00',
            ],
            [
                'type' => 'restaurant',
                'day_of_week' => 5,
                'is_active' => true,
                'open_time' => '11:00',
                'close_time' => '23:00',
                'last_reservation_time' => '22:00',
            ],
            [
                'type' => 'restaurant',
                'day_of_week' => 6,
                'is_active' => true,
                'open_time' => '11:00',
                'close_time' => '23:00',
                'last_reservation_time' => '22:00',
            ],
            [
                'type' => 'restaurant',
                'day_of_week' => 7,
                'is_active' => true,
                'open_time' => '11:00',
                'close_time' => '21:00',
                'last_reservation_time' => '20:00',
            ],
        ];

        $kitchenOpeningHours = [
            [
                'type' => 'kitchen',
                'day_of_week' => 1,
                'is_active' => false,
                'open_time' => null,
                'close_time' => null,
                'last_reservation_time' => null,
            ],
            [
                'type' => 'kitchen',
                'day_of_week' => 2,
                'is_active' => true,
                'open_time' => '10:00',
                'close_time' => '21:00',
                'last_reservation_time' => '20:00',
            ],
            [
                'type' => 'kitchen',
                'day_of_week' => 3,
                'is_active' => true,
                'open_time' => '10:00',
                'close_time' => '21:00',
                'last_reservation_time' => '20:00',
            ],
            [
                'type' => 'kitchen',
                'day_of_week' => 4,
                'is_active' => true,
                'open_time' => '10:00',
                'close_time' => '21:00',
                'last_reservation_time' => '20:00',
            ],
            [
                'type' => 'kitchen',
                'day_of_week' => 5,
                'is_active' => true,
                'open_time' => '10:00',
                'close_time' => '22:00',
                'last_reservation_time' => '21:00',
            ],
            [
                'type' => 'kitchen',
                'day_of_week' => 6,
                'is_active' => true,
                'open_time' => '10:00',
                'close_time' => '22:00',
                'last_reservation_time' => '21:00',
            ],
            [
                'type' => 'kitchen',
                'day_of_week' => 7,
                'is_active' => true,
                'open_time' => '10:00',
                'close_time' => '20:00',
                'last_reservation_time' => '19:00',
            ],
        ];

        foreach (
            array_merge(
                $restaurantOpeningHours,
                $kitchenOpeningHours
            ) as $openingHour
        ) {
            OpeningHour::create($openingHour);
        }
    }
}

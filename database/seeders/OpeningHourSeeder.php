<?php

namespace Database\Seeders;

use App\Models\OpeningHour;
use Illuminate\Database\Seeder;

class OpeningHourSeeder extends Seeder
{
    public function run(): void
    {
        $openingHours = [
            [
                'day_of_week' => 1,
                'is_active' => false,
                'open_time' => null,
                'close_time' => null,
                'last_reservation_time' => null,
            ],
            [
                'day_of_week' => 2,
                'is_active' => true,
                'open_time' => '11:00',
                'close_time' => '22:00',
                'last_reservation_time' => '21:00',
            ],
            [
                'day_of_week' => 3,
                'is_active' => true,
                'open_time' => '11:00',
                'close_time' => '22:00',
                'last_reservation_time' => '21:00',
            ],
            [
                'day_of_week' => 4,
                'is_active' => true,
                'open_time' => '11:00',
                'close_time' => '22:00',
                'last_reservation_time' => '21:00',
            ],
            [
                'day_of_week' => 5,
                'is_active' => true,
                'open_time' => '11:00',
                'close_time' => '23:00',
                'last_reservation_time' => '22:00',
            ],
            [
                'day_of_week' => 6,
                'is_active' => true,
                'open_time' => '11:00',
                'close_time' => '23:00',
                'last_reservation_time' => '22:00',
            ],
            [
                'day_of_week' => 7,
                'is_active' => true,
                'open_time' => '11:00',
                'close_time' => '21:00',
                'last_reservation_time' => '20:00',
            ],
        ];

        foreach ($openingHours as $openingHour) {
            OpeningHour::create($openingHour);
        }
    }
}

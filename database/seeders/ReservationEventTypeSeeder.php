<?php

namespace Database\Seeders;

use App\Models\ReservationEventType;
use Illuminate\Database\Seeder;

class ReservationEventTypeSeeder extends Seeder
{
    public function run(): void
    {
        ReservationEventType::insert([
            [
                'name_en' => 'No special occasion',
                'name_hu' => 'Nincs különleges alkalom',
                'name_sr' => 'Bez posebne prilike',
                'name_sr_cyrl' => 'Без посебне прилике',
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'name_en' => 'Birthday',
                'name_hu' => 'Születésnap',
                'name_sr' => 'Rođendan',
                'name_sr_cyrl' => 'Рођендан',
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'name_en' => 'Anniversary',
                'name_hu' => 'Évforduló',
                'name_sr' => 'Godišnjica',
                'name_sr_cyrl' => 'Годишњица',
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'name_en' => 'Business dinner',
                'name_hu' => 'Üzleti vacsora',
                'name_sr' => 'Poslovna večera',
                'name_sr_cyrl' => 'Пословна вечера',
                'is_active' => true,
                'sort_order' => 4,
            ],
            [
                'name_en' => 'Family gathering',
                'name_hu' => 'Családi összejövetel',
                'name_sr' => 'Porodično okupljanje',
                'name_sr_cyrl' => 'Породично окупљање',
                'is_active' => true,
                'sort_order' => 5,
            ],
            [
                'name_en' => 'Other',
                'name_hu' => 'Egyéb',
                'name_sr' => 'Drugo',
                'name_sr_cyrl' => 'Друго',
                'is_active' => true,
                'sort_order' => 6,
            ],
        ]);
    }
}
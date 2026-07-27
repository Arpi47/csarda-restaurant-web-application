<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        DB::table('categories')->insert([
            [
                'name_hu' => 'Előételek',
                'name_en' => 'Appetizers',
                'name_sr_lat' => 'Predjela',
                'name_sr_cyr' => 'Предјела',
                'sort_order' => 1,
            ],
            [
                'name_hu' => 'Levesek',
                'name_en' => 'Soups',
                'name_sr_lat' => 'Supe',
                'name_sr_cyr' => 'Супе',
                'sort_order' => 2,
            ],
            [
                'name_hu' => 'Főételek',
                'name_en' => 'Main dishes',
                'name_sr_lat' => 'Glavna jela',
                'name_sr_cyr' => 'Главна јела',
                'sort_order' => 3,
            ],
            [
                'name_hu' => 'Halételek',
                'name_en' => 'Fish dishes',
                'name_sr_lat' => 'Riblja jela',
                'name_sr_cyr' => 'Рибља јела',
                'sort_order' => 4,
            ],
            [
                'name_hu' => 'Saláták',
                'name_en' => 'Salads',
                'name_sr_lat' => 'Salate',
                'name_sr_cyr' => 'Салате',
                'sort_order' => 5,
            ],
            [
                'name_hu' => 'Köretek',
                'name_en' => 'Side dishes',
                'name_sr_lat' => 'Prilozi',
                'name_sr_cyr' => 'Прилози',
                'sort_order' => 6,
            ],
            [
                'name_hu' => 'Desszertek',
                'name_en' => 'Desserts',
                'name_sr_lat' => 'Dezerti',
                'name_sr_cyr' => 'Дезерти',
                'sort_order' => 7,
            ],
            [
                'name_hu' => 'Palacsinták',
                'name_en' => 'Pancakes',
                'name_sr_lat' => 'Palačinke',
                'name_sr_cyr' => 'Палачинке',
                'sort_order' => 8,
            ],
        ]);
    }
}
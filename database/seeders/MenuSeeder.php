<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MenuSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('menu')->insert([
            [
                'category_id' => 2,
                'name_hu' => 'Gulyásleves',
                'name_en' => 'Goulash Soup',
                'name_sr_lat' => 'Gulaš supa',
                'name_sr_cyr' => 'Гулаш супа',
                'description_hu' => 'Hagyományos magyar gulyás marhahússal.',
                'description_en' => 'Traditional Hungarian goulash with beef.',
                'description_sr_lat' => 'Tradicionalni gulaš sa govedinom.',
                'description_sr_cyr' => 'Традиционални гулаш са говедином.',
                'price' => 850.00,
                'image' => 'gulyas.jpg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'category_id' => 3,
                'name_hu' => 'Rántott hús',
                'name_en' => 'Breaded Schnitzel',
                'name_sr_lat' => 'Pohovano meso',
                'name_sr_cyr' => 'Поховано месо',
                'description_hu' => 'Ropogós rántott sertéshús.',
                'description_en' => 'Crispy breaded pork schnitzel.',
                'description_sr_lat' => 'Hrskavo pohovano svinjsko meso.',
                'description_sr_cyr' => 'Хрскаво поховано свињско месо.',
                'price' => 1200.00,
                'image' => 'rantott_hus.jpg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'category_id' => 3,
                'name_hu' => 'Spagetti bolognai',
                'name_en' => 'Spaghetti Bolognese',
                'name_sr_lat' => 'Špageti bolonjeze',
                'name_sr_cyr' => 'Шпагети болоњезе',
                'description_hu' => 'Paradicsomos húsos szósz spagettivel.',
                'description_en' => 'Pasta with rich tomato meat sauce.',
                'description_sr_lat' => 'Testenina sa paradajz sosom i mesom.',
                'description_sr_cyr' => 'Тестенина са парадајз сосом и месом.',
                'price' => 950.00,
                'image' => 'spaghetti.jpg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'category_id' => 5,

                'name_hu' => 'Cézár saláta',
                'name_en' => 'Caesar Salad',
                'name_sr_lat' => 'Cezar salata',
                'name_sr_cyr' => 'Цезар салата',
                'description_hu' => 'Csirkés cézár saláta parmezánnal.',
                'description_en' => 'Chicken Caesar salad with parmesan.',
                'description_sr_lat' => 'Cezar salata sa piletinom i parmezanom.',
                'description_sr_cyr' => 'Цезар салата са пилетином и пармезаном.',
                'price' => 800.00,
                'image' => 'caesar.jpg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'category_id' => 7,
                'name_hu' => 'Somlói galuska',
                'name_en' => 'Somloi Dumplings',
                'name_sr_lat' => 'Somlo kolač',
                'name_sr_cyr' => 'Шомло колач',
                'description_hu' => 'Csokoládés, diós magyar desszert.',
                'description_en' => 'Hungarian dessert with chocolate and walnuts.',
                'description_sr_lat' => 'Mađarski desert sa čokoladom i orasima.',
                'description_sr_cyr' => 'Мађарски десерт са чоколадом и орасима.',
                'price' => 600.00,
                'image' => 'somloi.jpg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}

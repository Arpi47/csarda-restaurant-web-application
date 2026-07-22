<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            SuperAdminSeeder::class,
            AdminSeeder::class,
            UserSeeder::class,
            CategorySeeder::class,
            MenuSeeder::class,
            GalleryImageSeeder::class,
        ]);
    }

}
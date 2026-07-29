<?php

namespace Database\Seeders;

use App\Models\ContactInformation;
use Illuminate\Database\Seeder;

class ContactInformationSeeder extends Seeder
{
    public function run(): void
    {
        ContactInformation::create([
            'phone' => '+381 XX XXX XXXX',
            'email' => 'info@csarda.com',
        ]);
    }
}

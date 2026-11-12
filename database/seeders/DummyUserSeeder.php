<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DummyUserSeeder extends Seeder
{
    public function run(): void
    {
        // Panggil OfficialUserSeeder resmi
        $this->call(OfficialUserSeeder::class);
    }
}

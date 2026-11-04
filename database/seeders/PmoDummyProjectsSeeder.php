<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class PmoDummyProjectsSeeder extends Seeder
{
    public function run(): void
    {
        // Seed proyek resmi Angkasa Pura Support & PPATK Peremajaan Router
        $seeder = new AngkasaPuraProjectSeeder();
        $seeder->run();

        $ppatkSeeder = new PpatkProjectSeeder();
        $ppatkSeeder->run();
    }
}

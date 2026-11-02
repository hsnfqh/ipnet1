<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class PmoDummyProjectsSeeder extends Seeder
{
    public function run(): void
    {
        // Hanya seed proyek resmi Angkasa Pura Support sesuai instruksi user
        $seeder = new AngkasaPuraProjectSeeder();
        $seeder->run();
    }
}

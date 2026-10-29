<?php
// database/seeders/DatabaseSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            DummyUserSeeder::class,
            PmoDummyProjectsSeeder::class,
            TechnicalWorkflowScenarioSeeder::class,
            AngkasaPuraProjectSeeder::class,
        ]);
    }
}

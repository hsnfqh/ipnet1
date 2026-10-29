<?php

use Illuminate\Database\Migrations\Migration;
use Database\Seeders\AngkasaPuraProjectSeeder;

return new class extends Migration
{
    public function up(): void
    {
        try {
            $seeder = new AngkasaPuraProjectSeeder();
            $seeder->run();
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('SeedAngkasaPuraProject migration warning: ' . $e->getMessage());
        }
    }

    public function down(): void
    {
        // Keep data intact on rollback
    }
};


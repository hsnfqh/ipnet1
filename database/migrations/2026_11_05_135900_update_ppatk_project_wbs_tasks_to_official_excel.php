<?php

use Illuminate\Database\Migrations\Migration;
use Database\Seeders\PpatkProjectSeeder;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        try {
            (new PpatkProjectSeeder())->run();
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Update PPATK WBS Migration error: ' . $e->getMessage());
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No down needed
    }
};


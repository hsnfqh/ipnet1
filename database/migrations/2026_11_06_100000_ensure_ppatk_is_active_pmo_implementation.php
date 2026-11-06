<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        try {
            DB::table('projects')
                ->where(function ($q) {
                    $q->where('name', 'like', '%PPAT%')
                      ->orWhere('name', 'like', '%Peremajaan Router%')
                      ->orWhere('client', 'like', '%PPAT%');
                })
                ->update([
                    'handover_status'            => 'Approved',
                    'handover_approved_at'       => '2026-07-30 10:00:00',
                    'commercial_handover_status' => 'Approved',
                    'handover_target'            => 'pmo',
                    'project_type'               => 'Implementation',
                    'stage'                      => 'Deliver',
                    'status'                     => 'In Progress',
                ]);

            if (class_exists(\Database\Seeders\PpatkProjectSeeder::class)) {
                $seeder = new \Database\Seeders\PpatkProjectSeeder();
                $seeder->run();
            }
        } catch (\Throwable $e) {
            // Silently continue if tables not yet populated
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No down operation needed
    }
};

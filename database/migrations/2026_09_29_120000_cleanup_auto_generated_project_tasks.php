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
            // Hapus relasi di pivot task_user
            if (Schema::hasTable('task_user') && Schema::hasTable('tasks')) {
                $taskIds = DB::table('tasks')
                    ->where('title', 'like', 'Implementasi Teknis:%')
                    ->pluck('id');

                if ($taskIds->isNotEmpty()) {
                    DB::table('task_user')->whereIn('task_id', $taskIds)->delete();
                }
            }

            // Hapus schedule spam yang terhubung
            if (Schema::hasTable('schedules')) {
                DB::table('schedules')
                    ->where('title', 'like', 'Implementasi Teknis:%')
                    ->delete();
            }

            // Hapus task Implementasi Teknis yang ter-generate otomatis dari proyek
            if (Schema::hasTable('tasks')) {
                DB::table('tasks')
                    ->where('title', 'like', 'Implementasi Teknis:%')
                    ->delete();
            }
        } catch (\Exception $e) {
            // Abaikan jika tabel belum tersedia
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Tidak perlu me-restore data spam/otomatis
    }
};

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
        // 1. Tambah deadline_time dan ubah deadline menjadi DATETIME di tabel tasks
        Schema::table('tasks', function (Blueprint $table) {
            if (!Schema::hasColumn('tasks', 'deadline_time')) {
                $table->time('deadline_time')->nullable()->after('deadline');
            }
        });

        // Pastikan kolom deadline bertipe DATETIME
        try {
            DB::statement('ALTER TABLE tasks MODIFY deadline DATETIME NULL');
        } catch (\Throwable $e) {
            // Fallback jika tidak didukung
        }

        // 2. Modifikasi kolom tabel projects agar aman saat project dibuat cepat via opsi 'Other'
        try {
            DB::statement('ALTER TABLE projects MODIFY client VARCHAR(255) NULL, MODIFY location VARCHAR(255) NULL, MODIFY start_date DATE NULL, MODIFY deadline DATE NULL');
        } catch (\Throwable $e) {
            // Fallback
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            if (Schema::hasColumn('tasks', 'deadline_time')) {
                $table->dropColumn('deadline_time');
            }
        });

        try {
            DB::statement('ALTER TABLE tasks MODIFY deadline DATE NULL');
        } catch (\Throwable $e) {
        }
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('engineer_activity_logs')) {
            Schema::create('engineer_activity_logs', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');          // Engineer yang mencatat
                $table->unsignedBigInteger('project_id')->nullable(); // Proyek terkait (opsional)
                $table->string('activity_type')->default('Kegiatan Harian');
                // Jenis: Kegiatan Harian, Kunjungan Klien, Troubleshooting, Preventive Maintenance, Konfigurasi, Lainnya
                $table->text('description');                    // Deskripsi kegiatan
                $table->string('location')->nullable();         // Lokasi kegiatan
                $table->date('activity_date');                  // Tanggal kegiatan
                $table->time('start_time')->nullable();         // Jam mulai
                $table->time('end_time')->nullable();           // Jam selesai
                $table->string('status')->default('Selesai');   // Selesai, Sedang Berjalan, Ditunda
                $table->text('notes')->nullable();              // Catatan tambahan
                $table->timestamps();

                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                $table->foreign('project_id')->references('id')->on('projects')->onDelete('set null');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('engineer_activity_logs');
    }
};

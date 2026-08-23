<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('attendances')) {
            Schema::create('attendances', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->enum('type', ['clock_in', 'clock_out']);
                $table->decimal('latitude', 10, 7)->nullable();
                $table->decimal('longitude', 10, 7)->nullable();
                $table->integer('distance_meters')->nullable(); // jarak dari kantor
                $table->boolean('is_within_range')->default(false);
                $table->string('photo_path')->nullable();       // path foto selfie (opsional)
                $table->string('address')->nullable();          // nama alamat dari reverse geocoding
                $table->text('note')->nullable();               // catatan opsional
                $table->date('attendance_date');                // tanggal presensi
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};

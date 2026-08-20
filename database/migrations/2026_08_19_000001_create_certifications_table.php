<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('certifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('file_path');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->timestamp('uploaded_at')->nullable();
            $table->timestamps();
        });

        // Migrasi data lama dari tabel users jika ada
        if (Schema::hasColumn('users', 'certification_file')) {
            $oldUsers = DB::table('users')->whereNotNull('certification_file')->where('certification_file', '!=', '')->get();
            foreach ($oldUsers as $oldUser) {
                DB::table('certifications')->insert([
                    'user_id'     => $oldUser->id,
                    'name'        => $oldUser->certification ?: 'Sertifikasi Keahlian',
                    'file_path'   => $oldUser->certification_file,
                    'status'      => in_array($oldUser->certification_status, ['pending', 'approved', 'rejected']) ? $oldUser->certification_status : 'pending',
                    'uploaded_at' => $oldUser->certification_uploaded_at ?: now(),
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ]);
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('certifications');
    }
};

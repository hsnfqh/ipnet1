<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('activity_document_signatures', function (Blueprint $table) {
            $table->id();
            $table->string('document_number')->unique();
            $table->string('scope_key')->index(); // e.g. "proj_12" or "no_proj_5"
            $table->unsignedBigInteger('project_id')->nullable()->index();
            $table->string('project_name')->nullable();
            $table->json('log_ids')->nullable();

            // 1. PIC Lapangan (Dibuat Oleh)
            $table->foreignId('pic_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('pic_name')->nullable();
            $table->string('pic_title')->default('PIC Field Engineer');
            $table->longText('pic_signature')->nullable();
            $table->timestamp('pic_signed_at')->nullable();

            // 2. Lead Engineer (Diperiksa Oleh)
            $table->foreignId('lead_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('lead_name')->nullable();
            $table->string('lead_title')->default('Lead Network Engineer');
            $table->longText('lead_signature')->nullable();
            $table->timestamp('lead_signed_at')->nullable();

            // 3. Head of Division / Group Leader (Mengetahui & Menyetujui)
            $table->foreignId('head_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('head_name')->nullable();
            $table->string('head_title')->default('Head of Division');
            $table->longText('head_signature')->nullable();
            $table->timestamp('head_signed_at')->nullable();

            // Status & Integritas Kriptografis
            $table->string('status')->default('draft'); // draft, signed_pic, signed_lead, fully_approved
            $table->string('verification_hash', 64)->nullable();
            $table->text('notes')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_document_signatures');
    }
};

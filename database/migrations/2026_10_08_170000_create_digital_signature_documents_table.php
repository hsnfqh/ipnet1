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
        if (!Schema::hasTable('digital_signature_documents')) {
            Schema::create('digital_signature_documents', function (Blueprint $table) {
                $table->id();
                $table->string('document_number')->unique();
                $table->string('title');
                $table->string('category')->default('BAST');
                $table->foreignId('project_id')->nullable()->constrained('projects')->nullOnDelete();
                $table->string('project_name')->nullable();
                $table->string('file_path');
                $table->string('file_name');
                $table->bigInteger('file_size')->nullable();
                $table->text('description')->nullable();
                $table->string('workflow_type')->default('sequential');
                $table->string('status')->default('draft');
                $table->foreignId('created_by')->constrained('users');
                $table->longText('internal_signers')->nullable();
                $table->string('client_name')->nullable();
                $table->string('client_position')->nullable();
                $table->string('client_company')->nullable();
                $table->string('client_phone')->nullable();
                $table->string('client_email')->nullable();
                $table->longText('client_signature')->nullable();
                $table->dateTime('client_signed_at')->nullable();
                $table->string('client_ip')->nullable();
                $table->string('client_signing_token', 64)->nullable()->unique();
                $table->dateTime('client_token_expires_at')->nullable();
                $table->string('verification_hash', 100)->nullable()->unique();
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('digital_signature_documents');
    }
};

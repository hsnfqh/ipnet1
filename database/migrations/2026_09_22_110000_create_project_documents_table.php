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
        if (!Schema::hasTable('project_documents')) {
            Schema::create('project_documents', function (Blueprint $table) {
                $table->id();
                $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();
                $table->unsignedTinyInteger('stage_number')->default(1)->comment('1: Commercial, 2: Solution, 3: PM, 4: Implementation, 5: Acceptance, 6: Operate');
                $table->string('stage_name')->default('Commercial');
                $table->string('document_key'); // e.g. customer_requirement, solution_architecture, bast_document
                $table->string('document_title');
                $table->boolean('is_mandatory')->default(true);
                $table->string('file_path')->nullable();
                $table->string('file_name')->nullable();
                $table->unsignedBigInteger('file_size')->nullable(); // in bytes
                $table->string('file_extension')->nullable();
                $table->string('status')->default('Pending')->comment('Pending, Uploaded, Verified, Rejected');
                $table->text('notes')->nullable();
                $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamp('uploaded_at')->nullable();
                $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamp('verified_at')->nullable();
                $table->timestamps();

                $table->index(['project_id', 'stage_number']);
                $table->index(['project_id', 'document_key']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_documents');
    }
};

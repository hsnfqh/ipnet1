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
        Schema::table('projects', function (Blueprint $table) {
            if (!Schema::hasColumn('projects', 'stage')) {
                $table->string('stage')->default('Deliver')->after('status')->comment('Acquire, Design, Deliver, Operate');
            }
            if (!Schema::hasColumn('projects', 'process_status')) {
                $table->string('process_status')->default('In Progress')->after('stage')->comment('Belum Mulai, In Progress, Menunggu Handover, Selesai, Dibatalkan');
            }
            if (!Schema::hasColumn('projects', 'pm_id')) {
                $table->foreignId('pm_id')->nullable()->after('created_by')->constrained('users')->nullOnDelete();
            }
            if (!Schema::hasColumn('projects', 'documents_checklist')) {
                $table->json('documents_checklist')->nullable()->after('description');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            if (Schema::hasColumn('projects', 'documents_checklist')) {
                $table->dropColumn('documents_checklist');
            }
            if (Schema::hasColumn('projects', 'pm_id')) {
                $table->dropForeign(['pm_id']);
                $table->dropColumn('pm_id');
            }
            if (Schema::hasColumn('projects', 'process_status')) {
                $table->dropColumn('process_status');
            }
            if (Schema::hasColumn('projects', 'stage')) {
                $table->dropColumn('stage');
            }
        });
    }
};

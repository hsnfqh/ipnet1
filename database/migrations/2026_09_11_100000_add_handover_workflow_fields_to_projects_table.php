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
            if (!Schema::hasColumn('projects', 'handover_status')) {
                $table->string('handover_status')->default('Draft')->after('acquire_status')->comment('Draft, Submitted, Conditional, Approved');
            }
            if (!Schema::hasColumn('projects', 'handover_data')) {
                $table->json('handover_data')->nullable()->after('handover_status')->comment('Checklist 4 Kategori Handover');
            }
            if (!Schema::hasColumn('projects', 'special_notes')) {
                $table->text('special_notes')->nullable()->after('handover_data')->comment('Catatan Khusus / Pengecualian dari Tim Commercial');
            }
            if (!Schema::hasColumn('projects', 'handover_conditional_notes')) {
                $table->text('handover_conditional_notes')->nullable()->after('special_notes')->comment('Catatan kekurangan dari PM untuk Handover Conditional');
            }
            if (!Schema::hasColumn('projects', 'handover_conditional_deadline')) {
                $table->dateTime('handover_conditional_deadline')->nullable()->after('handover_conditional_notes')->comment('Batas 2x24 jam perbaikan');
            }
            if (!Schema::hasColumn('projects', 'handover_submitted_at')) {
                $table->dateTime('handover_submitted_at')->nullable()->after('handover_conditional_deadline');
            }
            if (!Schema::hasColumn('projects', 'handover_approved_at')) {
                $table->dateTime('handover_approved_at')->nullable()->after('handover_submitted_at');
            }
            if (!Schema::hasColumn('projects', 'handover_approved_by')) {
                $table->foreignId('handover_approved_by')->nullable()->after('handover_approved_at')->constrained('users')->nullOnDelete();
            }
            if (!Schema::hasColumn('projects', 'customer_pic_technical')) {
                $table->string('customer_pic_technical')->nullable()->after('handover_approved_by');
            }
            if (!Schema::hasColumn('projects', 'customer_pic_business')) {
                $table->string('customer_pic_business')->nullable()->after('customer_pic_technical');
            }
            if (!Schema::hasColumn('projects', 'customer_pic_finance')) {
                $table->string('customer_pic_finance')->nullable()->after('customer_pic_business');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $columns = [
                'customer_pic_finance',
                'customer_pic_business',
                'customer_pic_technical',
                'handover_approved_by',
                'handover_approved_at',
                'handover_submitted_at',
                'handover_conditional_deadline',
                'handover_conditional_notes',
                'special_notes',
                'handover_data',
                'handover_status',
            ];

            foreach ($columns as $col) {
                if (Schema::hasColumn('projects', $col)) {
                    if ($col === 'handover_approved_by') {
                        $table->dropForeign(['handover_approved_by']);
                    }
                    $table->dropColumn($col);
                }
            }
        });
    }
};

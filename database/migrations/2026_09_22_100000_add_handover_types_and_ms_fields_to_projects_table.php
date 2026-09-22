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
            if (!Schema::hasColumn('projects', 'handover_target')) {
                $table->string('handover_target')->default('pmo')->after('commercial_handover_status')->comment('pmo, managed_service');
            }
            if (!Schema::hasColumn('projects', 'service_start_date')) {
                $table->date('service_start_date')->nullable()->after('handover_target');
            }
            if (!Schema::hasColumn('projects', 'service_end_date')) {
                $table->date('service_end_date')->nullable()->after('service_start_date');
            }
            if (!Schema::hasColumn('projects', 'maintenance_frequency')) {
                $table->string('maintenance_frequency')->nullable()->after('service_end_date')->comment('Monthly, Quarterly, Bi-Annual, Annual, On-Demand');
            }
            if (!Schema::hasColumn('projects', 'sla_coverage_hours')) {
                $table->string('sla_coverage_hours')->nullable()->after('maintenance_frequency')->comment('24x7, 8x5, Custom');
            }
            if (!Schema::hasColumn('projects', 'ms_handover_status')) {
                $table->string('ms_handover_status')->default('Draft')->after('sla_coverage_hours')->comment('Draft, Submitted, Accepted');
            }
            if (!Schema::hasColumn('projects', 'ms_accepted_at')) {
                $table->dateTime('ms_accepted_at')->nullable()->after('ms_handover_status');
            }
            if (!Schema::hasColumn('projects', 'ms_accepted_by')) {
                $table->foreignId('ms_accepted_by')->nullable()->after('ms_accepted_at')->constrained('users')->nullOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $cols = [
                'ms_accepted_by',
                'ms_accepted_at',
                'ms_handover_status',
                'sla_coverage_hours',
                'maintenance_frequency',
                'service_end_date',
                'service_start_date',
                'handover_target',
            ];

            foreach ($cols as $col) {
                if (Schema::hasColumn('projects', $col)) {
                    if ($col === 'ms_accepted_by') {
                        $table->dropForeign(['ms_accepted_by']);
                    }
                    $table->dropColumn($col);
                }
            }
        });
    }
};

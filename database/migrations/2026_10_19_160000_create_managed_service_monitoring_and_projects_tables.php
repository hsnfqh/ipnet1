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
        // 1. Tambahkan parameter SLA, CM, PM, dan Berkas Dokumen ke tabel projects
        if (Schema::hasTable('projects')) {
            Schema::table('projects', function (Blueprint $table) {
                if (!Schema::hasColumn('projects', 'cm_quota')) {
                    $table->integer('cm_quota')->default(18)->after('sla_tier');
                }
                if (!Schema::hasColumn('projects', 'pm_frequency_type')) {
                    $table->string('pm_frequency_type')->default('per_3_bulan')->after('cm_quota');
                }
                if (!Schema::hasColumn('projects', 'pm_total_cycles')) {
                    $table->integer('pm_total_cycles')->default(4)->after('pm_frequency_type');
                }
                if (!Schema::hasColumn('projects', 'mttr_hours')) {
                    $table->integer('mttr_hours')->default(4)->after('pm_total_cycles');
                }
                if (!Schema::hasColumn('projects', 'call_response_time')) {
                    $table->string('call_response_time')->default('15 menit')->after('mttr_hours');
                }
                if (!Schema::hasColumn('projects', 'incident_resolution_hours')) {
                    $table->integer('incident_resolution_hours')->default(4)->after('call_response_time');
                }
                if (!Schema::hasColumn('projects', 'sla_target_percentage')) {
                    $table->decimal('sla_target_percentage', 5, 2)->default(99.50)->after('incident_resolution_hours');
                }
                if (!Schema::hasColumn('projects', 'presales_name')) {
                    $table->string('presales_name')->nullable()->after('sales_name');
                }
                if (!Schema::hasColumn('projects', 'contract_file')) {
                    $table->string('contract_file')->nullable()->after('po_spk_file');
                }
                if (!Schema::hasColumn('projects', 'boq_file')) {
                    $table->string('boq_file')->nullable()->after('contract_file');
                }
                if (!Schema::hasColumn('projects', 'bast_file')) {
                    $table->string('bast_file')->nullable()->after('boq_file');
                }
            });
        }

        // 2. Buat tabel siklus Preventive Maintenance (PM Tracker)
        if (!Schema::hasTable('managed_service_pm_cycles')) {
            Schema::create('managed_service_pm_cycles', function (Blueprint $table) {
                $table->id();
                $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();
                $table->integer('cycle_number');
                $table->string('cycle_label');
                $table->date('scheduled_date')->nullable();
                $table->date('actual_date')->nullable();
                $table->string('activity_status')->default('Scheduled'); // Scheduled, In Progress, Completed
                $table->text('activity_notes')->nullable();
                $table->foreignId('assigned_engineer_id')->nullable()->constrained('users')->nullOnDelete();
                $table->string('report_status')->default('Pending'); // Pending, Draft, Submitted, Approved
                $table->string('report_file')->nullable();
                $table->string('report_filename')->nullable();
                $table->string('billing_ba_status')->default('Pending'); // Pending, Draft, Submitted, Signed, Invoiced
                $table->string('billing_ba_file')->nullable();
                $table->string('billing_ba_filename')->nullable();
                $table->timestamps();

                $table->index(['project_id', 'cycle_number']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('managed_service_pm_cycles');

        if (Schema::hasTable('projects')) {
            Schema::table('projects', function (Blueprint $table) {
                $columns = [
                    'cm_quota',
                    'pm_frequency_type',
                    'pm_total_cycles',
                    'mttr_hours',
                    'call_response_time',
                    'incident_resolution_hours',
                    'sla_target_percentage',
                    'presales_name',
                    'contract_file',
                    'boq_file',
                    'bast_file',
                ];
                foreach ($columns as $col) {
                    if (Schema::hasColumn('projects', $col)) {
                        $table->dropColumn($col);
                    }
                }
            });
        }
    }
};

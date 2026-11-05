<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('managed_service_pm_cycles')) {
            Schema::table('managed_service_pm_cycles', function (Blueprint $table) {
                if (!Schema::hasColumn('managed_service_pm_cycles', 'assigned_engineers')) {
                    $table->json('assigned_engineers')->nullable()->after('assigned_engineer_id');
                }
                if (!Schema::hasColumn('managed_service_pm_cycles', 'report_files')) {
                    $table->json('report_files')->nullable()->after('report_filename');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('managed_service_pm_cycles')) {
            Schema::table('managed_service_pm_cycles', function (Blueprint $table) {
                if (Schema::hasColumn('managed_service_pm_cycles', 'assigned_engineers')) {
                    $table->dropColumn('assigned_engineers');
                }
                if (Schema::hasColumn('managed_service_pm_cycles', 'report_files')) {
                    $table->dropColumn('report_files');
                }
            });
        }
    }
};


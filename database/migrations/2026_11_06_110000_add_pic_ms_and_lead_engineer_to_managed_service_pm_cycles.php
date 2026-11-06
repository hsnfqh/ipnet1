<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('managed_service_pm_cycles')) {
            Schema::table('managed_service_pm_cycles', function (Blueprint $table) {
                if (!Schema::hasColumn('managed_service_pm_cycles', 'pic_ms_id')) {
                    $table->unsignedBigInteger('pic_ms_id')->nullable()->after('activity_notes');
                }
                if (!Schema::hasColumn('managed_service_pm_cycles', 'lead_engineer_id')) {
                    $table->unsignedBigInteger('lead_engineer_id')->nullable()->after('pic_ms_id');
                }
            });

            // Default PIC MS dan Lead Engineer di-strip (null) dulu sesuai instruksi
            try {
                DB::table('managed_service_pm_cycles')->update([
                    'pic_ms_id' => null,
                    'lead_engineer_id' => null,
                ]);
            } catch (\Throwable $e) {}
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('managed_service_pm_cycles')) {
            Schema::table('managed_service_pm_cycles', function (Blueprint $table) {
                if (Schema::hasColumn('managed_service_pm_cycles', 'pic_ms_id')) {
                    $table->dropColumn('pic_ms_id');
                }
                if (Schema::hasColumn('managed_service_pm_cycles', 'lead_engineer_id')) {
                    $table->dropColumn('lead_engineer_id');
                }
            });
        }
    }
};

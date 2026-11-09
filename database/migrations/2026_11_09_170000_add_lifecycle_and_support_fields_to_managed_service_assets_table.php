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
        if (Schema::hasTable('managed_service_assets')) {
            Schema::table('managed_service_assets', function (Blueprint $table) {
                if (!Schema::hasColumn('managed_service_assets', 'os_version')) {
                    $table->string('os_version', 100)->nullable()->after('model');
                }
                if (!Schema::hasColumn('managed_service_assets', 'eol_date')) {
                    $table->date('eol_date')->nullable()->after('warranty_expiry');
                }
                if (!Schema::hasColumn('managed_service_assets', 'eos_date')) {
                    $table->date('eos_date')->nullable()->after('eol_date');
                }
                if (!Schema::hasColumn('managed_service_assets', 'support_status')) {
                    $table->string('support_status', 50)->default('Active')->nullable()->after('eos_date'); // Active, Expired, Critical, Pending Renewal
                }
                if (!Schema::hasColumn('managed_service_assets', 'support_expiry_date')) {
                    $table->date('support_expiry_date')->nullable()->after('support_status');
                }
                if (!Schema::hasColumn('managed_service_assets', 'service_support_type')) {
                    $table->string('service_support_type', 100)->nullable()->after('support_expiry_date'); // PAR-ND, PAR-SD, PAR-RTF, 24x7x4, 8x5xNBD, etc.
                }
                if (!Schema::hasColumn('managed_service_assets', 'backup_status')) {
                    $table->string('backup_status', 50)->default('Not Backed Up')->nullable()->after('service_support_type'); // Backed Up, Not Backed Up, Scheduled
                }
                if (!Schema::hasColumn('managed_service_assets', 'last_backup_date')) {
                    $table->date('last_backup_date')->nullable()->after('backup_status');
                }
                if (!Schema::hasColumn('managed_service_assets', 'backup_notes')) {
                    $table->text('backup_notes')->nullable()->after('last_backup_date');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('managed_service_assets')) {
            Schema::table('managed_service_assets', function (Blueprint $table) {
                $columns = [
                    'os_version',
                    'eol_date',
                    'eos_date',
                    'support_status',
                    'support_expiry_date',
                    'service_support_type',
                    'backup_status',
                    'last_backup_date',
                    'backup_notes',
                ];
                foreach ($columns as $column) {
                    if (Schema::hasColumn('managed_service_assets', $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }
    }
};

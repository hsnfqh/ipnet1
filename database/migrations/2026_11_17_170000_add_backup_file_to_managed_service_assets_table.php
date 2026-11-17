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
                if (!Schema::hasColumn('managed_service_assets', 'backup_file_path')) {
                    $table->string('backup_file_path', 255)->nullable()->after('backup_notes');
                }
                if (!Schema::hasColumn('managed_service_assets', 'backup_file_name')) {
                    $table->string('backup_file_name', 255)->nullable()->after('backup_file_path');
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
                if (Schema::hasColumn('managed_service_assets', 'backup_file_name')) {
                    $table->dropColumn('backup_file_name');
                }
                if (Schema::hasColumn('managed_service_assets', 'backup_file_path')) {
                    $table->dropColumn('backup_file_path');
                }
            });
        }
    }
};

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
        if (Schema::hasTable('managed_service_tickets')) {
            Schema::table('managed_service_tickets', function (Blueprint $table) {
                if (!Schema::hasColumn('managed_service_tickets', 'start_time')) {
                    $table->string('start_time', 20)->nullable()->after('incident_date');
                }
                if (!Schema::hasColumn('managed_service_tickets', 'end_time')) {
                    $table->string('end_time', 20)->nullable()->after('start_time');
                }
                if (!Schema::hasColumn('managed_service_tickets', 'duration_hours')) {
                    $table->decimal('duration_hours', 6, 2)->nullable()->after('end_time');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('managed_service_tickets')) {
            Schema::table('managed_service_tickets', function (Blueprint $table) {
                if (Schema::hasColumn('managed_service_tickets', 'duration_hours')) {
                    $table->dropColumn('duration_hours');
                }
                if (Schema::hasColumn('managed_service_tickets', 'end_time')) {
                    $table->dropColumn('end_time');
                }
                if (Schema::hasColumn('managed_service_tickets', 'start_time')) {
                    $table->dropColumn('start_time');
                }
            });
        }
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('engineer_activity_logs') && !Schema::hasColumn('engineer_activity_logs', 'category')) {
            Schema::table('engineer_activity_logs', function (Blueprint $table) {
                $table->string('category', 50)->default('project')->after('project_id')->index();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('engineer_activity_logs') && Schema::hasColumn('engineer_activity_logs', 'category')) {
            Schema::table('engineer_activity_logs', function (Blueprint $table) {
                $table->dropColumn('category');
            });
        }
    }
};

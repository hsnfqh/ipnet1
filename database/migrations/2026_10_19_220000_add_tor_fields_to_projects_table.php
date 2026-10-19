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
        if (Schema::hasTable('projects')) {
            Schema::table('projects', function (Blueprint $table) {
                if (!Schema::hasColumn('projects', 'tor_file')) {
                    $table->string('tor_file')->nullable()->after('bast_file');
                }
                if (!Schema::hasColumn('projects', 'tor_number')) {
                    $table->string('tor_number')->nullable()->after('tor_file');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('projects')) {
            Schema::table('projects', function (Blueprint $table) {
                if (Schema::hasColumn('projects', 'tor_file')) {
                    $table->dropColumn('tor_file');
                }
                if (Schema::hasColumn('projects', 'tor_number')) {
                    $table->dropColumn('tor_number');
                }
            });
        }
    }
};

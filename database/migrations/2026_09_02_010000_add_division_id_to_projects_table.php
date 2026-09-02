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
        if (Schema::hasTable('projects') && !Schema::hasColumn('projects', 'division_id')) {
            Schema::table('projects', function (Blueprint $table) {
                $table->foreignId('division_id')->nullable()->after('client')->constrained('divisions')->nullOnDelete();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('projects') && Schema::hasColumn('projects', 'division_id')) {
            Schema::table('projects', function (Blueprint $table) {
                $table->dropForeign(['division_id']);
                $table->dropColumn('division_id');
            });
        }
    }
};

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
        if (Schema::hasTable('activity_document_signatures')) {
            Schema::table('activity_document_signatures', function (Blueprint $table) {
                if (!Schema::hasColumn('activity_document_signatures', 'report_data')) {
                    $table->json('report_data')->nullable()->after('notes');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('activity_document_signatures')) {
            Schema::table('activity_document_signatures', function (Blueprint $table) {
                if (Schema::hasColumn('activity_document_signatures', 'report_data')) {
                    $table->dropColumn('report_data');
                }
            });
        }
    }
};

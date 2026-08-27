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
        Schema::table('projects', function (Blueprint $table) {
            $table->string('sales_name')->nullable()->after('client');
            $table->string('project_type')->default('One-Time Project')->after('location');
            $table->string('visit_schedule')->nullable()->after('project_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn(['sales_name', 'project_type', 'visit_schedule']);
        });
    }
};

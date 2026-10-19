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
                if (!Schema::hasColumn('managed_service_assets', 'product_description')) {
                    $table->text('product_description')->nullable()->after('device_name');
                }
                if (!Schema::hasColumn('managed_service_assets', 'quantity')) {
                    $table->integer('quantity')->default(1)->after('model');
                }
                if (!Schema::hasColumn('managed_service_assets', 'owner_type')) {
                    $table->string('owner_type')->default('client')->after('client_name');
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
                if (Schema::hasColumn('managed_service_assets', 'owner_type')) {
                    $table->dropColumn('owner_type');
                }
                if (Schema::hasColumn('managed_service_assets', 'quantity')) {
                    $table->dropColumn('quantity');
                }
                if (Schema::hasColumn('managed_service_assets', 'product_description')) {
                    $table->dropColumn('product_description');
                }
            });
        }
    }
};

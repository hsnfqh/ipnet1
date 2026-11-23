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
        if (Schema::hasTable('warehouse_items')) {
            Schema::table('warehouse_items', function (Blueprint $table) {
                if (!Schema::hasColumn('warehouse_items', 'device_status')) {
                    $table->string('device_status', 50)->default('Aktif')->after('status');
                }
                if (!Schema::hasColumn('warehouse_items', 'image')) {
                    $table->string('image')->nullable()->after('device_status');
                }
            });
        }

        if (Schema::hasTable('warehouse_suppliers')) {
            Schema::table('warehouse_suppliers', function (Blueprint $table) {
                if (!Schema::hasColumn('warehouse_suppliers', 'contacts')) {
                    $table->text('contacts')->nullable()->after('email');
                }
                if (!Schema::hasColumn('warehouse_suppliers', 'brands')) {
                    $table->text('brands')->nullable()->after('products_supplied');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('warehouse_items')) {
            Schema::table('warehouse_items', function (Blueprint $table) {
                if (Schema::hasColumn('warehouse_items', 'device_status')) {
                    $table->dropColumn('device_status');
                }
                if (Schema::hasColumn('warehouse_items', 'image')) {
                    $table->dropColumn('image');
                }
            });
        }

        if (Schema::hasTable('warehouse_suppliers')) {
            Schema::table('warehouse_suppliers', function (Blueprint $table) {
                if (Schema::hasColumn('warehouse_suppliers', 'contacts')) {
                    $table->dropColumn('contacts');
                }
                if (Schema::hasColumn('warehouse_suppliers', 'brands')) {
                    $table->dropColumn('brands');
                }
            });
        }
    }
};

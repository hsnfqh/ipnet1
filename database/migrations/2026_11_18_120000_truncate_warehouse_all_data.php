<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Membersihkan seluruh data transaksi dan inventaris gudang/warehouse.
     * Modul lain (proyek, managed service, users, clients, dll) TIDAK disentuh sama sekali.
     */
    public function up(): void
    {
        try {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');

            $tables = [
                'warehouse_so_items',
                'warehouse_stock_opnames',
                'warehouse_do_items',
                'warehouse_delivery_orders',
                'warehouse_po_items',
                'warehouse_purchase_orders',
                'warehouse_products',
                'warehouse_items',
                'warehouse_suppliers',
                'warehouse_categories',
                'warehouse_units',
            ];

            foreach ($tables as $table) {
                if (Schema::hasTable($table)) {
                    DB::table($table)->truncate();
                }
            }

            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        } catch (\Throwable $e) {
            // Abaikan jika database driver tidak mendukung FOREIGN_KEY_CHECKS atau sedang migrasi awal
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No reverse needed for cleanup
    }
};

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
        // 1. Kategori Produk
        if (!Schema::hasTable('warehouse_categories')) {
            Schema::create('warehouse_categories', function (Blueprint $table) {
                $table->id();
                $table->string('code')->unique(); // CAT-RT, CAT-SW, CAT-FW
                $table->string('name');
                $table->text('description')->nullable();
                $table->string('icon')->nullable();
                $table->string('status')->default('Aktif'); // Aktif, Nonaktif
                $table->timestamps();
            });
        }

        // 2. Satuan
        if (!Schema::hasTable('warehouse_units')) {
            Schema::create('warehouse_units', function (Blueprint $table) {
                $table->id();
                $table->string('code')->unique(); // UNT, PCS, BOX, ROL, MTR, SET, PAK
                $table->string('name'); // Unit, Pcs, Box, Roll, Meter, Set, Pack
                $table->string('short_name'); // Unit, Pcs, Box, Rol, M, Set, Pck
                $table->text('description')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        // 3. Supplier
        if (!Schema::hasTable('warehouse_suppliers')) {
            Schema::create('warehouse_suppliers', function (Blueprint $table) {
                $table->id();
                $table->string('code')->unique(); // SUP-2026-001
                $table->string('name'); // PT Synnex Metrodata Indonesia, dll
                $table->string('contact_person')->nullable();
                $table->string('phone')->nullable();
                $table->string('email')->nullable();
                $table->text('address')->nullable();
                $table->string('products_supplied')->nullable(); // Cisco, Fortinet, Aruba
                $table->string('status')->default('Aktif'); // Aktif, Nonaktif
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }

        // 4. Warehouse (Master Lokasi Gudang)
        if (!Schema::hasTable('warehouse_locations')) {
            Schema::create('warehouse_locations', function (Blueprint $table) {
                $table->id();
                $table->string('code')->unique(); // WH-JKT-01, WH-SBY-02
                $table->string('name'); // Gudang Utama IPNET Jakarta
                $table->string('pic_name')->nullable();
                $table->string('pic_phone')->nullable();
                $table->text('address')->nullable();
                $table->string('capacity_desc')->nullable(); // 500 Unit / 10 Rak
                $table->string('status')->default('Operasional'); // Operasional, Penuh, Maintenance
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }

        // 5. Produk
        if (!Schema::hasTable('warehouse_products')) {
            Schema::create('warehouse_products', function (Blueprint $table) {
                $table->id();
                $table->string('sku')->unique(); // PRD-CS-3850
                $table->string('barcode')->nullable()->index();
                $table->string('name');
                $table->string('brand')->nullable();
                $table->foreignId('category_id')->nullable()->constrained('warehouse_categories')->nullOnDelete();
                $table->string('category_name')->nullable();
                $table->foreignId('unit_id')->nullable()->constrained('warehouse_units')->nullOnDelete();
                $table->string('unit_name')->default('Unit');
                $table->foreignId('supplier_id')->nullable()->constrained('warehouse_suppliers')->nullOnDelete();
                $table->string('supplier_name')->nullable();
                $table->foreignId('warehouse_id')->nullable()->constrained('warehouse_locations')->nullOnDelete();
                $table->string('warehouse_name')->nullable();
                $table->string('rack_position')->nullable(); // e.g. Rak A-02, Bin 3
                $table->integer('min_stock')->default(5);
                $table->integer('current_stock')->default(0);
                $table->decimal('unit_price', 15, 2)->default(0);
                $table->text('specifications')->nullable();
                $table->string('status')->default('Tersedia'); // Tersedia, Stok Menipis, Habis, Discontinued
                $table->string('image')->nullable();
                $table->timestamps();
            });
        }

        // 6. Purchase Order (PO)
        if (!Schema::hasTable('warehouse_purchase_orders')) {
            Schema::create('warehouse_purchase_orders', function (Blueprint $table) {
                $table->id();
                $table->string('po_number')->unique(); // PO-IPN-2026-001
                $table->date('po_date');
                $table->foreignId('supplier_id')->nullable()->constrained('warehouse_suppliers')->nullOnDelete();
                $table->string('supplier_name')->nullable();
                $table->foreignId('warehouse_id')->nullable()->constrained('warehouse_locations')->nullOnDelete();
                $table->string('warehouse_name')->nullable();
                $table->date('expected_date')->nullable();
                $table->decimal('total_amount', 15, 2)->default(0);
                $table->string('status')->default('Draft'); // Draft, Diproses, Disetujui, Diterima Sebagian, Selesai, Dibatalkan
                $table->text('notes')->nullable();
                $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();
            });
        }

        // 7. Purchase Order Items
        if (!Schema::hasTable('warehouse_po_items')) {
            Schema::create('warehouse_po_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('purchase_order_id')->constrained('warehouse_purchase_orders')->cascadeOnDelete();
                $table->foreignId('product_id')->nullable()->constrained('warehouse_products')->nullOnDelete();
                $table->string('product_name');
                $table->integer('quantity')->default(1);
                $table->string('unit')->default('Unit');
                $table->decimal('unit_price', 15, 2)->default(0);
                $table->decimal('subtotal', 15, 2)->default(0);
                $table->integer('received_qty')->default(0);
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }

        // 8. Stock Opname
        if (!Schema::hasTable('warehouse_stock_opnames')) {
            Schema::create('warehouse_stock_opnames', function (Blueprint $table) {
                $table->id();
                $table->string('so_number')->unique(); // SO-2026-10-001
                $table->date('so_date');
                $table->foreignId('warehouse_id')->nullable()->constrained('warehouse_locations')->nullOnDelete();
                $table->string('warehouse_name')->nullable();
                $table->string('auditor_name')->nullable();
                $table->string('status')->default('Draft'); // Draft, Menunggu Approval, Selesai
                $table->text('notes')->nullable();
                $table->integer('total_items_checked')->default(0);
                $table->integer('total_discrepancy')->default(0);
                $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();
            });
        }

        // 9. Stock Opname Items
        if (!Schema::hasTable('warehouse_so_items')) {
            Schema::create('warehouse_so_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('stock_opname_id')->constrained('warehouse_stock_opnames')->cascadeOnDelete();
                $table->foreignId('product_id')->nullable()->constrained('warehouse_products')->nullOnDelete();
                $table->string('item_code')->nullable();
                $table->string('product_name');
                $table->integer('system_stock')->default(0);
                $table->integer('physical_stock')->default(0);
                $table->integer('difference')->default(0);
                $table->string('condition_status')->default('Baik & Lengkap'); // Baik & Lengkap, Perlu Servis, Hilang, Kemasan Rusak
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }

        // 10. Delivery Order (DO)
        if (!Schema::hasTable('warehouse_delivery_orders')) {
            Schema::create('warehouse_delivery_orders', function (Blueprint $table) {
                $table->id();
                $table->string('do_number')->unique(); // DO-IPN-2026-0089
                $table->date('do_date');
                $table->string('reference_no')->nullable(); // PO atau Project
                $table->foreignId('warehouse_id')->nullable()->constrained('warehouse_locations')->nullOnDelete();
                $table->string('warehouse_name')->nullable();
                $table->string('recipient_name');
                $table->string('recipient_company')->nullable();
                $table->string('recipient_phone')->nullable();
                $table->text('delivery_address');
                $table->string('courier_name')->nullable();
                $table->string('vehicle_no')->nullable();
                $table->string('tracking_no')->nullable();
                $table->string('status')->default('Draft'); // Draft, Siap Dikirim, Dalam Pengiriman, Terkirim & Diterima, Batal
                $table->text('notes')->nullable();
                $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();
            });
        }

        // 11. Delivery Order Items
        if (!Schema::hasTable('warehouse_do_items')) {
            Schema::create('warehouse_do_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('delivery_order_id')->constrained('warehouse_delivery_orders')->cascadeOnDelete();
                $table->foreignId('product_id')->nullable()->constrained('warehouse_products')->nullOnDelete();
                $table->string('product_name');
                $table->string('serial_number')->nullable();
                $table->integer('quantity')->default(1);
                $table->string('unit')->default('Unit');
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('warehouse_do_items');
        Schema::dropIfExists('warehouse_delivery_orders');
        Schema::dropIfExists('warehouse_so_items');
        Schema::dropIfExists('warehouse_stock_opnames');
        Schema::dropIfExists('warehouse_po_items');
        Schema::dropIfExists('warehouse_purchase_orders');
        Schema::dropIfExists('warehouse_products');
        Schema::dropIfExists('warehouse_locations');
        Schema::dropIfExists('warehouse_suppliers');
        Schema::dropIfExists('warehouse_units');
        Schema::dropIfExists('warehouse_categories');
    }
};

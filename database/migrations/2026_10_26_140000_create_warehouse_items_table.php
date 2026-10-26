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
        Schema::create('warehouse_items', function (Blueprint $table) {
            $table->id();
            $table->string('item_code')->unique(); // e.g. WH-2026-0001
            $table->string('name'); // Nama Barang / Model
            $table->string('brand')->nullable(); // Merk: Cisco, Fortinet, Mikrotik, dll
            
            // Klasifikasi Barang & Perangkat
            $table->string('item_type'); // Router, Switch, Firewall, AP, Bandwidth Management, Kabel, Lainnya
            $table->string('component_type')->default('Perangkat Utuh'); // Perangkat Utuh, Filter Udara, SFP, Fan, Modul / Card, Chasis / Rack, PSU, Kabel
            $table->string('cable_type')->nullable(); // Fiber Optic (FO), UTP / Ethernet, C14 - C13, C13 - CEE 7, CEE, C15 - 2 Pin Japanese, C19 - CEE 7, Patch Cord, dll
            
            // Serial & Dokumen
            $table->string('serial_number')->nullable()->index(); // S/N
            $table->string('product_number')->nullable()->index(); // P/N
            $table->string('po_number')->nullable(); // No. PO
            $table->string('contract_number')->nullable(); // No. Kontrak
            
            // Kategori Kepemilikan: Stock, Backup / Spare, Pinjaman
            $table->string('category')->default('Stock'); 
            
            // Jika Kategori = Pinjaman
            $table->string('loan_source_type')->nullable(); // disti, ipnet
            // Data Pinjaman Disti
            $table->string('disti_name')->nullable();
            $table->text('disti_address')->nullable();
            $table->string('disti_pic')->nullable();
            $table->string('disti_phone')->nullable();
            $table->string('disti_duration')->nullable(); // e.g. 30 Hari, s/d 15 Nov 2026
            // Data Pinjaman Internal IPNET
            $table->string('ipnet_pic_name')->nullable();
            $table->string('ipnet_work_unit')->nullable(); // IPNET 1, IPNET 2, IPNET 3, IPNET 4, IPNET 5
            $table->string('ipnet_phone')->nullable();
            $table->string('ipnet_duration')->nullable();

            // Lokasi Fisik Barang
            $table->string('location_type')->default('Warehouse IPNET'); // Warehouse IPNET, Customer
            $table->string('warehouse_rack')->nullable(); // e.g. Rak A-01, Bin 2
            // Jika Lokasi = Customer
            $table->text('customer_address')->nullable();
            $table->string('customer_pic')->nullable();
            $table->string('customer_phone')->nullable();
            $table->string('customer_sales_name')->nullable(); // AM / Sales

            // Status Operasional: Tersedia, POC / Pinjaman, Pinjaman, Pelanggan, Maintenance
            $table->string('status')->default('Tersedia');
            // Detail jika Status POC / Pinjaman ke Klien
            $table->string('poc_customer_name')->nullable();
            $table->string('poc_pic_name')->nullable();
            $table->string('poc_pic_phone')->nullable();
            $table->string('poc_sales_name')->nullable();
            $table->string('poc_duration')->nullable();

            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('warehouse_items');
    }
};

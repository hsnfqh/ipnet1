<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\WarehouseCategory;
use App\Models\WarehouseUnit;
use App\Models\WarehouseSupplier;
use App\Models\WarehouseLocation;
use App\Models\WarehouseProduct;
use App\Models\WarehouseItem;
use App\Models\WarehousePurchaseOrder;
use App\Models\WarehousePoItem;
use App\Models\WarehouseStockOpname;
use App\Models\WarehouseSoItem;
use App\Models\WarehouseDeliveryOrder;
use App\Models\WarehouseDoItem;
use Spatie\Permission\Models\Role;

class WarehouseCleanSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Pastikan Role & User Warehouse ada
        Role::firstOrCreate(['name' => 'Warehouse', 'guard_name' => 'web']);

        $warehouseUser = User::firstOrCreate(
            ['email' => 'warehouse@ipnetsolusindo.com'],
            [
                'name'     => 'Ahmad',
                'password' => Hash::make('password'),
                'position' => 'Warehouse & Inventory Officer',
                'status'   => 'active',
            ]
        );
        $warehouseUser->update([
            'name'     => 'Ahmad',
            'position' => 'Warehouse & Inventory Officer',
        ]);
        $warehouseUser->syncRoles(['Warehouse']);

        // 2. Bersihkan seluruh data lama warehouse
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        WarehouseDoItem::truncate();
        WarehouseDeliveryOrder::truncate();
        WarehouseSoItem::truncate();
        WarehouseStockOpname::truncate();
        WarehousePoItem::truncate();
        WarehousePurchaseOrder::truncate();
        WarehouseItem::truncate();
        WarehouseProduct::truncate();
        WarehouseLocation::truncate();
        WarehouseSupplier::truncate();
        WarehouseUnit::truncate();
        WarehouseCategory::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // 3. Tepat 1 Titik Gudang Fisik Resmi PT IP Network Solusindo
        $gudangUtama = WarehouseLocation::create([
            'name'          => 'Gudang Utama IPNET',
            'code'          => 'WH-JKT-01',
            'address'       => 'Kantor Pusat PT IP Network Solusindo, Jl. Boulevard Raya Blok QA No. 12, Kelapa Gading, Jakarta Utara',
            'pic_name'      => 'Ahmad',
            'pic_phone'     => '0812-8888-9999',
            'capacity_desc' => 'Kapasitas 10 Rak Fisik (A, B, C)',
            'notes'         => 'Fasilitas penyimpanan logistik rak A, B, C dan lab pre-konfigurasi perangkat jaringan.',
            'status'        => 'Operasional',
        ]);

        // 4. Kategori Produk
        $catSwitch = WarehouseCategory::create([
            'name'        => 'Switch',
            'code'        => 'CAT-SW',
            'description' => 'Managed switch layer 2 & layer 3, distribution switch, dan access switch PoE.',
            'status'      => 'Aktif',
        ]);
        $catFirewall = WarehouseCategory::create([
            'name'        => 'Firewall',
            'code'        => 'CAT-FW',
            'description' => 'Next-generation firewall appliance, UTM gateway, dan perimeter security.',
            'status'      => 'Aktif',
        ]);
        $catRouter = WarehouseCategory::create([
            'name'        => 'Router',
            'code'        => 'CAT-RT',
            'description' => 'Edge router, cloud core router ISP grade, dan VPN concentrator.',
            'status'      => 'Aktif',
        ]);
        $catAP = WarehouseCategory::create([
            'name'        => 'Access Point',
            'code'        => 'CAT-AP',
            'description' => 'Enterprise Wi-Fi 6 indoor/outdoor access point dan cloud controller.',
            'status'      => 'Aktif',
        ]);

        // 5. Satuan Baku
        $unitUnit = WarehouseUnit::create(['name' => 'Unit', 'code' => 'UNT', 'short_name' => 'Unit', 'description' => 'Satuan per unit fisik perangkat']);
        WarehouseUnit::create(['name' => 'Pcs', 'code' => 'PCS', 'short_name' => 'Pcs', 'description' => 'Pieces']);
        WarehouseUnit::create(['name' => 'Box', 'code' => 'BOX', 'short_name' => 'Box', 'description' => 'Kemasan box']);
        WarehouseUnit::create(['name' => 'Roll', 'code' => 'ROL', 'short_name' => 'Rol', 'description' => 'Kabel gulungan roll']);
        WarehouseUnit::create(['name' => 'Meter', 'code' => 'MTR', 'short_name' => 'M', 'description' => 'Panjang meter']);
        WarehouseUnit::create(['name' => 'Set', 'code' => 'SET', 'short_name' => 'Set', 'description' => 'Set / Kit']);

        // 6. Mitra Supplier & Distributor Resmi
        $supSynnex = WarehouseSupplier::create([
            'name'          => 'PT Synnex Metrodata Indonesia',
            'code'          => 'SUP-SMI-01',
            'contact_person'=> 'Hendra Wijaya',
            'phone'         => '021-29345800',
            'email'         => 'procurement@synnexmetrodata.com',
            'address'       => 'APL Tower Lt. 38, Jl. Letjen S. Parman Kav. 28, Tanjung Duren Selatan, Jakarta Barat',
            'status'        => 'Aktif',
        ]);
        $supECS = WarehouseSupplier::create([
            'name'          => 'PT ECS Indo Jaya',
            'code'          => 'SUP-ECS-02',
            'contact_person'=> 'Dewi Lestari',
            'phone'         => '021-62312890',
            'email'         => 'sales.enterprise@ecsindo.com',
            'address'       => 'Komp. Ruko Mangga Dua Mall No. 8-9, Sawah Besar, Jakarta Pusat',
            'status'        => 'Aktif',
        ]);
        $supMaster = WarehouseSupplier::create([
            'name'          => 'PT Mastersystem Infotama',
            'code'          => 'SUP-MSI-03',
            'contact_person'=> 'Budi Prasetyo',
            'phone'         => '021-57973500',
            'email'         => 'partner@mastersystem.co.id',
            'address'       => 'Sudirman Plaza, Plaza Marein Lt. 19, Jl. Jend. Sudirman Kav. 76-78, Jakarta Selatan',
            'status'        => 'Aktif',
        ]);

        // 7. TEPAT 5 MASTER PRODUK REALISTIS
        // Produk 1: Cisco Catalyst 3850
        $p1 = WarehouseProduct::create([
            'name'           => 'Cisco Catalyst 3850-24T-S',
            'sku'            => 'WS-C3850-24T-S',
            'category_id'    => $catSwitch->id,
            'category_name'  => $catSwitch->name,
            'unit_id'        => $unitUnit->id,
            'unit_name'      => 'Unit',
            'supplier_id'    => $supSynnex->id,
            'supplier_name'  => $supSynnex->name,
            'warehouse_id'   => $gudangUtama->id,
            'warehouse_name' => $gudangUtama->name,
            'brand'          => 'Cisco',
            'rack_position'  => 'Rak A-01',
            'unit_price'     => 45000000,
            'current_stock'  => 1, // 1 di rak gudang, 1 teralokasi ke klien BRI
            'min_stock'      => 1,
            'specifications' => '24-Port GE switch enterprise Layer 3, IP Base feature set, dual modular power.',
            'status'         => 'Tersedia',
        ]);

        // Produk 2: Fortinet FortiGate 100F
        $p2 = WarehouseProduct::create([
            'name'           => 'Fortinet FortiGate 100F',
            'sku'            => 'FG-100F-BDL-950-DD',
            'category_id'    => $catFirewall->id,
            'category_name'  => $catFirewall->name,
            'unit_id'        => $unitUnit->id,
            'unit_name'      => 'Unit',
            'supplier_id'    => $supSynnex->id,
            'supplier_name'  => $supSynnex->name,
            'warehouse_id'   => $gudangUtama->id,
            'warehouse_name' => $gudangUtama->name,
            'brand'          => 'Fortinet',
            'rack_position'  => 'Rak B-01',
            'unit_price'     => 68000000,
            'current_stock'  => 0, // 0 di rak gudang (sedang dipinjamkan uji coba POC ke Telkom)
            'min_stock'      => 1,
            'specifications' => 'Next-Generation Firewall 1 Gbps threat protection throughput, 22 x GE RJ45, 4 x SFP.',
            'status'         => 'Tersedia',
        ]);

        // Produk 3: MikroTik CCR2004-16G-2S+
        $p3 = WarehouseProduct::create([
            'name'           => 'MikroTik CCR2004-16G-2S+',
            'sku'            => 'CCR2004-16G-2S+',
            'category_id'    => $catRouter->id,
            'category_name'  => $catRouter->name,
            'unit_id'        => $unitUnit->id,
            'unit_name'      => 'Unit',
            'supplier_id'    => $supECS->id,
            'supplier_name'  => $supECS->name,
            'warehouse_id'   => $gudangUtama->id,
            'warehouse_name' => $gudangUtama->name,
            'brand'          => 'MikroTik',
            'rack_position'  => 'Rak B-02',
            'unit_price'     => 12500000,
            'current_stock'  => 2, // 2 unit di rak B-02
            'min_stock'      => 1,
            'specifications' => '16 x Gigabit Ethernet ports, 2 x 10G SFP+ cages, Annapurna Labs 4-core 1.7GHz CPU.',
            'status'         => 'Tersedia',
        ]);

        // Produk 4: Ruijie Reyee RG-AP820-L(V2)
        $p4 = WarehouseProduct::create([
            'name'           => 'Ruijie Reyee RG-AP820-L(V2)',
            'sku'            => 'RG-AP820-L-V2',
            'category_id'    => $catAP->id,
            'category_name'  => $catAP->name,
            'unit_id'        => $unitUnit->id,
            'unit_name'      => 'Unit',
            'supplier_id'    => $supECS->id,
            'supplier_name'  => $supECS->name,
            'warehouse_id'   => $gudangUtama->id,
            'warehouse_name' => $gudangUtama->name,
            'brand'          => 'Ruijie',
            'rack_position'  => 'Rak C-01',
            'unit_price'     => 3800000,
            'current_stock'  => 2, // 2 unit di rak C-01
            'min_stock'      => 1,
            'specifications' => 'Wi-Fi 6 (802.11ax) indoor ceiling access point, dual-band 2.4GHz & 5GHz dual-stream.',
            'status'         => 'Tersedia',
        ]);

        // Produk 5: Ubiquiti UniFi USW-Pro-48-PoE
        $p5 = WarehouseProduct::create([
            'name'           => 'Ubiquiti UniFi USW-Pro-48-PoE',
            'sku'            => 'USW-PRO-48-POE',
            'category_id'    => $catSwitch->id,
            'category_name'  => $catSwitch->name,
            'unit_id'        => $unitUnit->id,
            'unit_name'      => 'Unit',
            'supplier_id'    => $supMaster->id,
            'supplier_name'  => $supMaster->name,
            'warehouse_id'   => $gudangUtama->id,
            'warehouse_name' => $gudangUtama->name,
            'brand'          => 'Ubiquiti',
            'rack_position'  => 'Rak A-02',
            'unit_price'     => 18500000,
            'current_stock'  => 1, // 1 unit di rak A-02
            'min_stock'      => 1,
            'specifications' => '48-Port PoE switch Layer 3, 40 x PoE+, 8 x PoE++, 4 x 10G SFP+ uplink, 600W power budget.',
            'status'         => 'Tersedia',
        ]);

        // 8. DATA FISIK INVENTARIS PERANGKAT (WarehouseItem) - TERHUBUNG 100%
        // Unit 1 Produk 1 (Tersedia di Gudang)
        WarehouseItem::create([
            'item_code'        => 'WH-2026-0001',
            'name'             => $p1->name,
            'brand'            => $p1->brand,
            'item_type'        => 'Switch',
            'component_type'   => 'Perangkat Utuh',
            'serial_number'    => 'FCW2143L0G7',
            'product_number'   => $p1->sku,
            'po_number'        => 'PO-IPN-2026-001',
            'contract_number'  => 'CTR-STOCK-IPN-2026',
            'category'         => 'Stock',
            'location_type'    => 'Gudang IPNET',
            'warehouse_rack'   => 'Rak A-01',
            'status'           => 'Tersedia',
            'notes'            => 'Unit baru segel distributor, siap deploy untuk proyek implementasi.',
        ]);

        // Unit 2 Produk 1 (Terkirim ke Klien BRI via Delivery Order DO-IPN-2026-001)
        WarehouseItem::create([
            'item_code'        => 'WH-2026-0002',
            'name'             => $p1->name,
            'brand'            => $p1->brand,
            'item_type'        => 'Switch',
            'component_type'   => 'Perangkat Utuh',
            'serial_number'    => 'FCW2143L0G8',
            'product_number'   => $p1->sku,
            'po_number'        => 'PO-IPN-2026-001',
            'contract_number'  => 'CTR-BRI-LAN-2026',
            'category'         => 'Stock',
            'location_type'    => 'Customer',
            'warehouse_rack'   => null,
            'customer_address' => 'Gedung Menara BRI Lt. 18, Jl. Jend. Sudirman Kav. 44-46, Jakarta Pusat',
            'customer_pic'     => 'Budi Hartono (IT Infrastructure)',
            'customer_phone'   => '0812-9876-5432',
            'customer_sales_name' => 'Raiza',
            'status'           => 'Pelanggan',
            'poc_customer_name'=> 'PT Bank Rakyat Indonesia (Persero) Tbk',
            'poc_pic_name'     => 'Budi Hartono',
            'poc_pic_phone'    => '0812-9876-5432',
            'poc_sales_name'   => 'Raiza',
            'notes'            => 'Terkirim melalui Surat Jalan DO-IPN-2026-001 untuk peremajaan Core Switch BRI.',
        ]);

        // Unit 3 Produk 2 (POC di Telkom Indonesia via DO-IPN-2026-002)
        WarehouseItem::create([
            'item_code'        => 'WH-2026-0003',
            'name'             => $p2->name,
            'brand'            => $p2->brand,
            'item_type'        => 'Firewall',
            'component_type'   => 'Perangkat Utuh',
            'serial_number'    => 'FGT100F19827391',
            'product_number'   => $p2->sku,
            'po_number'        => 'PO-IPN-2026-005',
            'contract_number'  => 'CTR-POC-TELKOM-2026',
            'category'         => 'Pinjaman',
            'loan_source_type' => 'disti',
            'location_type'    => 'Customer',
            'warehouse_rack'   => null,
            'customer_address' => 'Telkom Landmark Tower Lt. 22, Jl. Jend. Gatot Subroto Kav. 52, Jakarta Selatan',
            'customer_pic'     => 'Denny Irawan (Security Architect)',
            'customer_phone'   => '0811-9876-5432',
            'customer_sales_name' => 'Raiza',
            'status'           => 'POC / Pinjaman',
            'poc_customer_name'=> 'PT Telkom Indonesia (Persero) Tbk',
            'poc_pic_name'     => 'Denny Irawan',
            'poc_pic_phone'    => '0811-9876-5432',
            'poc_sales_name'   => 'Raiza',
            'notes'            => 'Uji coba POC High Availability Firewall Data Center durasi 14 hari.',
        ]);

        // Unit 4 Produk 3 (Tersedia di Gudang)
        WarehouseItem::create([
            'item_code'        => 'WH-2026-0004',
            'name'             => $p3->name,
            'brand'            => $p3->brand,
            'item_type'        => 'Router',
            'component_type'   => 'Perangkat Utuh',
            'serial_number'    => 'HE7391028472',
            'product_number'   => $p3->sku,
            'po_number'        => 'PO-IPN-2026-003',
            'contract_number'  => 'CTR-STOCK-IPN-2026',
            'category'         => 'Stock',
            'location_type'    => 'Gudang IPNET',
            'warehouse_rack'   => 'Rak B-02',
            'status'           => 'Tersedia',
            'notes'            => 'Router core BGP testbed siap operasional.',
        ]);

        // Unit 5 Produk 3 (Backup / Spare di Gudang)
        WarehouseItem::create([
            'item_code'        => 'WH-2026-0005',
            'name'             => $p3->name,
            'brand'            => $p3->brand,
            'item_type'        => 'Router',
            'component_type'   => 'Perangkat Utuh',
            'serial_number'    => 'HE7391028473',
            'product_number'   => $p3->sku,
            'po_number'        => 'PO-IPN-2026-003',
            'contract_number'  => 'CTR-STOCK-IPN-2026',
            'category'         => 'Backup / Spare',
            'location_type'    => 'Gudang IPNET',
            'warehouse_rack'   => 'Rak B-02',
            'status'           => 'Tersedia',
            'notes'            => 'Unit cadangan darurat (cold spare) untuk SLA Managed Service.',
        ]);

        // Unit 6 Produk 4 (Tersedia di Gudang)
        WarehouseItem::create([
            'item_code'        => 'WH-2026-0006',
            'name'             => $p4->name,
            'brand'            => $p4->brand,
            'item_type'        => 'Access Point',
            'component_type'   => 'Perangkat Utuh',
            'serial_number'    => 'RJAP99182310',
            'product_number'   => $p4->sku,
            'po_number'        => 'PO-IPN-2026-004',
            'contract_number'  => 'CTR-STOCK-IPN-2026',
            'category'         => 'Stock',
            'location_type'    => 'Gudang IPNET',
            'warehouse_rack'   => 'Rak C-01',
            'status'           => 'Tersedia',
            'notes'            => 'Perangkat utuh lengkap mounting bracket dan PoE injector adapter.',
        ]);

        // Unit 7 Produk 4 (Tersedia di Gudang)
        WarehouseItem::create([
            'item_code'        => 'WH-2026-0007',
            'name'             => $p4->name,
            'brand'            => $p4->brand,
            'item_type'        => 'Access Point',
            'component_type'   => 'Perangkat Utuh',
            'serial_number'    => 'RJAP99182311',
            'product_number'   => $p4->sku,
            'po_number'        => 'PO-IPN-2026-004',
            'contract_number'  => 'CTR-STOCK-IPN-2026',
            'category'         => 'Stock',
            'location_type'    => 'Gudang IPNET',
            'warehouse_rack'   => 'Rak C-01',
            'status'           => 'Tersedia',
            'notes'            => 'Stok fisik rak C-01 telah diverifikasi audit Stock Opname.',
        ]);

        // Unit 8 Produk 5 (Tersedia di Gudang)
        WarehouseItem::create([
            'item_code'        => 'WH-2026-0008',
            'name'             => $p5->name,
            'brand'            => $p5->brand,
            'item_type'        => 'Switch',
            'component_type'   => 'Perangkat Utuh',
            'serial_number'    => 'UB48P998271A',
            'product_number'   => $p5->sku,
            'po_number'        => 'PO-IPN-2026-002',
            'contract_number'  => 'CTR-STOCK-IPN-2026',
            'category'         => 'Stock',
            'location_type'    => 'Gudang IPNET',
            'warehouse_rack'   => 'Rak A-02',
            'status'           => 'Tersedia',
            'notes'            => 'Switch 48 port PoE++ 600W dalam kemasan original box rak A-02.',
        ]);

        // 9. PURCHASE ORDER (1 PO Berjalan Aktif Sesuai Screenshot)
        $po1 = WarehousePurchaseOrder::create([
            'po_number'      => 'PO-IPN-2026-008',
            'po_date'        => now()->subDays(3)->format('Y-m-d'),
            'supplier_id'    => $supSynnex->id,
            'supplier_name'  => $supSynnex->name,
            'warehouse_id'   => $gudangUtama->id,
            'warehouse_name' => $gudangUtama->name,
            'expected_date'  => now()->addDays(3)->format('Y-m-d'),
            'total_amount'   => 90000000, // 2 unit Cisco Catalyst @ Rp 45.000.000
            'status'         => 'Diproses', // 1 PO Aktif berjalan!
            'notes'          => 'Pemesanan restock switch core Cisco resmi distributor Synnex. Garansi TAC 1 tahun.',
            'created_by'     => $warehouseUser->id,
        ]);
        WarehousePoItem::create([
            'purchase_order_id' => $po1->id,
            'product_id'        => $p1->id,
            'product_name'      => $p1->name,
            'quantity'          => 2,
            'unit'              => 'Unit',
            'unit_price'        => 45000000,
            'subtotal'          => 90000000,
            'received_qty'      => 0,
            'notes'             => 'Proses restock reguler rak A-01.',
        ]);

        // 10. DELIVERY ORDER (1 Surat Jalan Berjalan Sesuai Screenshot)
        $do1 = WarehouseDeliveryOrder::create([
            'do_number'         => 'DO-IPN-2026-001',
            'do_date'           => now()->format('Y-m-d'),
            'reference_no'      => 'CTR-BRI-LAN-2026',
            'warehouse_id'      => $gudangUtama->id,
            'warehouse_name'    => $gudangUtama->name,
            'recipient_name'    => 'Budi Hartono (IT Infrastructure Lead)',
            'recipient_company' => 'PT Bank Rakyat Indonesia (Persero) Tbk',
            'recipient_phone'   => '0812-9876-5432',
            'delivery_address'  => 'Gedung Menara BRI Lt. 18, Jl. Jend. Sudirman Kav. 44-46, Jakarta Pusat',
            'courier_name'      => 'Supriyadi (Driver Logistik IPNET)',
            'vehicle_no'        => 'B 9182 JKL',
            'tracking_no'       => 'TRK-IPN-BRI-001',
            'status'            => 'Dalam Pengiriman', // 1 Surat Jalan Pengiriman Berjalan!
            'notes'             => 'Pengiriman unit switch core L3 untuk peremajaan infrastruktur LAN Gedung Pusat BRI.',
            'created_by'        => $warehouseUser->id,
        ]);
        WarehouseDoItem::create([
            'delivery_order_id' => $do1->id,
            'product_id'        => $p1->id,
            'product_name'      => $p1->name,
            'serial_number'     => 'FCW2143L0G8',
            'quantity'          => 1,
            'unit'              => 'Unit',
            'notes'             => 'Kondisi lengkap: unit fisik switch, kabel power AC, dan rackmount ear kit.',
        ]);

        // 11. STOCK OPNAME (1 Dokumen Audit Selesai - Klop 100%)
        $so1 = WarehouseStockOpname::create([
            'so_number'            => 'SO-2026-10-001',
            'so_date'              => now()->subDays(2)->format('Y-m-d'),
            'warehouse_id'         => $gudangUtama->id,
            'warehouse_name'       => $gudangUtama->name,
            'auditor_name'         => 'Ahmad (Warehouse Specialist)',
            'status'               => 'Selesai',
            'notes'                => 'Audit fisik berkala seluruh rak A, B, dan C di Gudang Utama IPNET. Saldo klop 100%.',
            'total_items_checked'  => 5,
            'total_discrepancy'    => 0,
            'created_by'           => $warehouseUser->id,
        ]);
        // Audit 5 produk
        WarehouseSoItem::create([
            'stock_opname_id' => $so1->id,
            'product_id'      => $p1->id,
            'item_code'       => $p1->sku,
            'product_name'    => $p1->name,
            'system_stock'    => 1,
            'physical_stock'  => 1,
            'difference'      => 0,
            'condition_status'=> 'Baik & Lengkap',
            'notes'           => 'Fisik di rak A-01 klop.',
        ]);
        WarehouseSoItem::create([
            'stock_opname_id' => $so1->id,
            'product_id'      => $p2->id,
            'item_code'       => $p2->sku,
            'product_name'    => $p2->name,
            'system_stock'    => 0,
            'physical_stock'  => 0,
            'difference'      => 0,
            'condition_status'=> 'Baik & Lengkap',
            'notes'           => 'Fisik 0 unit di rak (sedang teralokasi POC ke Telkom via DO-IPN-2026-002).',
        ]);
        WarehouseSoItem::create([
            'stock_opname_id' => $so1->id,
            'product_id'      => $p3->id,
            'item_code'       => $p3->sku,
            'product_name'    => $p3->name,
            'system_stock'    => 2,
            'physical_stock'  => 2,
            'difference'      => 0,
            'condition_status'=> 'Baik & Lengkap',
            'notes'           => 'Fisik di rak B-02 klop (1 stock, 1 backup).',
        ]);
        WarehouseSoItem::create([
            'stock_opname_id' => $so1->id,
            'product_id'      => $p4->id,
            'item_code'       => $p4->sku,
            'product_name'    => $p4->name,
            'system_stock'    => 2,
            'physical_stock'  => 2,
            'difference'      => 0,
            'condition_status'=> 'Baik & Lengkap',
            'notes'           => 'Fisik di rak C-01 klop (2 unit AP Wi-Fi 6).',
        ]);
        WarehouseSoItem::create([
            'stock_opname_id' => $so1->id,
            'product_id'      => $p5->id,
            'item_code'       => $p5->sku,
            'product_name'    => $p5->name,
            'system_stock'    => 1,
            'physical_stock'  => 1,
            'difference'      => 0,
            'condition_status'=> 'Baik & Lengkap',
            'notes'           => 'Fisik di rak A-02 klop (1 unit PoE 48-port).',
        ]);
    }
}

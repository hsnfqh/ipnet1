<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\WarehouseItem;
use Spatie\Permission\Models\Role;

class WarehouseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Pastikan Role Warehouse ada
        $role = Role::firstOrCreate(['name' => 'Warehouse', 'guard_name' => 'web']);

        // 2. Akun Dummy Warehouse
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

        // 3. Data Dummy Inventaris Realistis
        $items = [
            [
                'item_code'        => 'WH-2026-0001',
                'name'             => 'Cisco Catalyst 3850 48-Port PoE+ Switch',
                'brand'            => 'Cisco',
                'item_type'        => 'Switch',
                'component_type'   => 'Perangkat Utuh',
                'cable_type'       => null,
                'serial_number'    => 'FCW2143L0G7',
                'product_number'   => 'WS-C3850-48F-L',
                'po_number'        => 'PO-IPN-2026-089',
                'contract_number'  => 'CTR-BRI-LAN-2026',
                'category'         => 'Stock',
                'location_type'    => 'Customer',
                'warehouse_rack'   => null,
                'customer_address' => 'Gedung Kantor Pusat BRI, Jl. Jend. Sudirman Kav. 44-46, Jakarta Pusat',
                'customer_pic'     => 'Budi Hartono (IT Infrastructure)',
                'customer_phone'   => '081287654321',
                'customer_sales_name' => 'Raiza',
                'status'           => 'Pelanggan',
                'poc_customer_name'=> 'PT Bank Rakyat Indonesia (Persero) Tbk',
                'poc_pic_name'     => 'Budi Hartono',
                'poc_pic_phone'    => '081287654321',
                'poc_sales_name'   => 'Raiza',
                'notes'            => 'Kondisi terpasang, instalasi switch distribusi core LAN Gedung Pusat BRI.',
            ],
            [
                'item_code'        => 'WH-2026-0002',
                'name'             => 'Fortinet FortiGate 100F Next-Gen Firewall',
                'brand'            => 'Fortinet',
                'item_type'        => 'Firewall',
                'component_type'   => 'Perangkat Utuh',
                'cable_type'       => null,
                'serial_number'    => 'FG100FTK21004921',
                'product_number'   => 'FG-100F-BDL-950-DD',
                'po_number'        => 'PO-IPN-2026-042',
                'contract_number'  => 'CTR-MANDIRI-FW-2026',
                'category'         => 'Stock',
                'location_type'    => 'Customer',
                'warehouse_rack'   => null,
                'customer_address' => 'Plaza Mandiri Lt. 18, Jl. Jend. Gatot Subroto Kav. 36-38, Jakarta Selatan',
                'customer_pic'     => 'Arief Wicaksono (Security Lead)',
                'customer_phone'   => '081198761234',
                'customer_sales_name' => 'Nabylla Berlianita',
                'status'           => 'Pelanggan',
                'poc_customer_name'=> 'PT Bank Mandiri (Persero) Tbk',
                'poc_pic_name'     => 'Arief Wicaksono',
                'poc_pic_phone'    => '081198761234',
                'poc_sales_name'   => 'Nabylla Berlianita',
                'notes'            => 'Perimeter firewall HA cluster implementasi Data Center Bank Mandiri.',
            ],
            [
                'item_code'        => 'WH-2026-0003',
                'name'             => 'MikroTik CCR2004-16G-2S+ Core Router',
                'brand'            => 'Mikrotik',
                'item_type'        => 'Router',
                'component_type'   => 'Perangkat Utuh',
                'cable_type'       => null,
                'serial_number'    => 'E81D084F592C',
                'product_number'   => 'CCR2004-16G-2S+',
                'po_number'        => 'PO-IPN-2026-015',
                'contract_number'  => 'CTR-POC-PLN-2026',
                'category'         => 'Stock',
                'location_type'    => 'Customer',
                'customer_address' => 'Gedung PT PLN (Persero) Pusat, Jl. Trunojoyo Blok M, Jakarta Selatan',
                'customer_pic'     => 'Bambang Triyono (Senior Engineer)',
                'customer_phone'   => '081388776655',
                'customer_sales_name' => 'Raiza',
                'status'           => 'POC / Pinjaman',
                'poc_customer_name'=> 'PT PLN (Persero)',
                'poc_pic_name'     => 'Bambang Triyono',
                'poc_pic_phone'    => '081388776655',
                'poc_sales_name'   => 'Raiza',
                'poc_duration'     => '14 Hari (20 Okt - 03 Nov 2026)',
                'notes'            => 'Peminjaman uji coba POC routing load balancing.',
            ],
            [
                'item_code'        => 'WH-2026-0004',
                'name'             => 'Ruijie Reyee RG-AP820-L(V3) Wi-Fi 6 Access Point',
                'brand'            => 'Ruijie',
                'item_type'        => 'AP (Access Point)',
                'component_type'   => 'Perangkat Utuh',
                'cable_type'       => null,
                'serial_number'    => 'G1NB66C004211',
                'product_number'   => 'RG-AP820-L(V3)',
                'po_number'        => 'PO-IPN-2026-112',
                'contract_number'  => null,
                'category'         => 'Stock',
                'location_type'    => 'Gudang IPNET',
                'warehouse_rack'   => 'Rak AP C-03',
                'status'           => 'Tersedia',
                'notes'            => 'Stok siap pasang untuk kantor cabang.',
            ],
            [
                'item_code'        => 'WH-2026-0005',
                'name'             => 'Sangfor IAM M5400-AC Bandwidth Management Gateway',
                'brand'            => 'Sangfor',
                'item_type'        => 'Bandwidth Management',
                'component_type'   => 'Perangkat Utuh',
                'cable_type'       => null,
                'serial_number'    => 'SF-IAM-5400-9921',
                'product_number'   => 'M5400-AC',
                'po_number'        => 'DO-MTR-2026-019',
                'contract_number'  => 'DO-MTR-2026-019',
                'category'         => 'Pinjaman',
                'loan_source_type' => 'disti',
                'disti_name'       => 'PT Synnex Metrodata Indonesia',
                'disti_address'    => 'APL Tower Lt. 42, Jl. Letjen S. Parman, Jakarta Barat',
                'disti_pic'        => 'Hendra Wijaya',
                'disti_phone'      => '081199223344',
                'disti_duration'   => '30 Hari (s/d 15 Nov 2026)',
                'location_type'    => 'Gudang IPNET',
                'warehouse_rack'   => 'Rak Pinjaman Disti D-01',
                'customer_sales_name' => 'Ahmad (Sales Enterprise)',
                'poc_sales_name'   => 'Ahmad (Sales Enterprise)',
                'status'           => 'Pinjaman',
                'notes'            => 'Unit pinjaman demo disti untuk presentasi solusi traffic shaping.',
            ],
            [
                'item_code'        => 'WH-2026-0006',
                'name'             => 'Cisco 10GBASE-SR SFP+ Optical Transceiver Module',
                'brand'            => 'Cisco',
                'item_type'        => 'Switch',
                'component_type'   => 'SFP',
                'cable_type'       => null,
                'serial_number'    => 'OP-SR-2026-091',
                'product_number'   => 'SFP-10G-SR',
                'po_number'        => 'PO-IPN-2026-089',
                'contract_number'  => null,
                'category'         => 'Stock',
                'location_type'    => 'Gudang IPNET',
                'warehouse_rack'   => 'Laci SFP Modul S-01',
                'status'           => 'Tersedia',
                'notes'            => 'Multimode 850nm 300m, stok 10 unit di laci S-01.',
            ],
            [
                'item_code'        => 'WH-2026-0007',
                'name'             => 'Power Cord C14 - C13 High Density Server Cable 2M',
                'brand'            => 'APC / Schneider',
                'item_type'        => 'Kabel',
                'component_type'   => 'Kabel',
                'cable_type'       => 'C14 - C13',
                'serial_number'    => 'PC-C14C13-082',
                'product_number'   => 'AP9870',
                'po_number'        => 'PO-IPN-2026-011',
                'contract_number'  => null,
                'category'         => 'Stock',
                'location_type'    => 'Gudang IPNET',
                'warehouse_rack'   => 'Bin Kabel K-02',
                'status'           => 'Tersedia',
                'notes'            => 'Kabel power PDU ke server/switch.',
            ],
            [
                'item_code'        => 'WH-2026-0008',
                'name'             => 'Power Cord C13 - CEE 7 Schuko Standard 2.5M',
                'brand'            => 'Standard OEM',
                'item_type'        => 'Kabel',
                'component_type'   => 'Kabel',
                'cable_type'       => 'C13 - CEE 7',
                'serial_number'    => 'PC-C13CEE7-104',
                'product_number'   => 'PC-EU-C13',
                'po_number'        => 'PO-IPN-2026-011',
                'contract_number'  => null,
                'category'         => 'Stock',
                'location_type'    => 'Gudang IPNET',
                'warehouse_rack'   => 'Bin Kabel K-01',
                'status'           => 'Tersedia',
                'notes'            => 'Kabel colokan standar PLN Eropa/Indonesia.',
            ],
            [
                'item_code'        => 'WH-2026-0009',
                'name'             => 'Cisco Fan Tray Module Catalyst 3850',
                'brand'            => 'Cisco',
                'item_type'        => 'Switch',
                'component_type'   => 'Fan',
                'cable_type'       => null,
                'serial_number'    => 'FAN-C3850-991',
                'product_number'   => 'FAN-T1',
                'po_number'        => 'PO-IPN-2026-042',
                'contract_number'  => null,
                'category'         => 'Backup / Spare',
                'location_type'    => 'Gudang IPNET',
                'warehouse_rack'   => 'Rak Spare B-03',
                'status'           => 'Tersedia',
                'notes'            => 'Kipas pendingin cadangan switch backbone.',
            ],
            [
                'item_code'        => 'WH-2026-0010',
                'name'             => 'Air Filter Dust Sponge Chassis Rack 19 Inch',
                'brand'            => 'OEM Rack',
                'item_type'        => 'Switch',
                'component_type'   => 'Filter Udara',
                'cable_type'       => null,
                'serial_number'    => 'FLT-UDARA-041',
                'product_number'   => 'FLT-19INCH-HD',
                'po_number'        => 'PO-IPN-2026-012',
                'contract_number'  => null,
                'category'         => 'Stock',
                'location_type'    => 'Gudang IPNET',
                'warehouse_rack'   => 'Rak Filter F-01',
                'status'           => 'Tersedia',
                'notes'            => 'Saringan debu ventilasi rak server.',
            ],
            [
                'item_code'        => 'WH-2026-0011',
                'name'             => 'Juniper SRX300 Services Gateway Firewall',
                'brand'            => 'Juniper',
                'item_type'        => 'Firewall',
                'component_type'   => 'Perangkat Utuh',
                'cable_type'       => null,
                'serial_number'    => 'JN1289AC4012',
                'product_number'   => 'SRX300-SYS-JB',
                'po_number'        => 'PO-IPN-2026-077',
                'contract_number'  => 'PO-IPN-2026-077',
                'category'         => 'Pinjaman',
                'loan_source_type' => 'ipnet',
                'ipnet_pic_name'   => 'Nugraha Pratama',
                'ipnet_work_unit'  => 'IPNET 1',
                'ipnet_phone'      => '081211223344',
                'ipnet_duration'   => '7 Hari (s/d 30 Okt 2026)',
                'location_type'    => 'Customer',
                'customer_address' => 'Gedung Menara Mandiri, Jl. Jend Sudirman, Jakarta Selatan',
                'customer_pic'     => 'Dedy Suryana',
                'customer_phone'   => '081299887766',
                'customer_sales_name' => 'Nabylla Berlianita',
                'status'           => 'POC / Pinjaman',
                'poc_customer_name'=> 'Bank Mandiri',
                'poc_pic_name'     => 'Dedy Suryana',
                'poc_pic_phone'    => '081299887766',
                'poc_sales_name'   => 'Nabylla Berlianita',
                'poc_duration'     => '7 Hari',
                'notes'            => 'Dipinjam oleh Tim IPNET 1 untuk staging & testing lapangan.',
            ],
            [
                'item_code'        => 'WH-2026-0012',
                'name'             => 'Cisco Catalyst 2960-X 24 GigE PoE 370W',
                'brand'            => 'Cisco',
                'item_type'        => 'Switch',
                'component_type'   => 'Perangkat Utuh',
                'cable_type'       => null,
                'serial_number'    => 'FCW2960X99182',
                'product_number'   => 'WS-C2960X-24PD-L',
                'po_number'        => 'PO-IPN-2026-095',
                'contract_number'  => 'CTR-BCA-CAB-2026',
                'category'         => 'Stock',
                'location_type'    => 'Customer',
                'warehouse_rack'   => null,
                'customer_address' => 'Menara BCA Lt. 32, Grand Indonesia, Jl. M.H. Thamrin No. 1, Jakarta Pusat',
                'customer_pic'     => 'Fajar Nugroho (IT Branch Specialist)',
                'customer_phone'   => '08176543210',
                'customer_sales_name' => 'Raiza',
                'status'           => 'Pelanggan',
                'poc_customer_name'=> 'PT Bank Central Asia Tbk',
                'poc_pic_name'     => 'Fajar Nugroho',
                'poc_pic_phone'    => '08176543210',
                'poc_sales_name'   => 'Raiza',
                'notes'            => 'Pemasangan switch access migrasi jaringan kantor cabang BCA Sudirman.',
            ],
            [
                'item_code'        => 'WH-2026-0013',
                'name'             => 'Ruijie RG-NBS3100-24GT4SFP Managed Switch',
                'brand'            => 'Ruijie',
                'item_type'        => 'Switch',
                'component_type'   => 'Perangkat Utuh',
                'cable_type'       => null,
                'serial_number'    => 'RJ-NBS-3100-8831',
                'product_number'   => 'RG-NBS3100-24GT4SFP',
                'po_number'        => 'PO-IPN-2026-104',
                'contract_number'  => 'DO-SILOAM-POC-2026',
                'category'         => 'Stock',
                'location_type'    => 'Customer',
                'warehouse_rack'   => null,
                'customer_address' => 'Gedung MRCCC Siloam Hospitals, Jl. Garnisun No. 2-3, Semanggi, Jakarta Selatan',
                'customer_pic'     => 'Dr. Hendra Saputra (IT Hospital)',
                'customer_phone'   => '081377889900',
                'customer_sales_name' => 'Ahmad (Sales Enterprise)',
                'status'           => 'POC / Pinjaman',
                'poc_customer_name'=> 'RS Siloam Hospitals Semanggi',
                'poc_pic_name'     => 'Dr. Hendra Saputra',
                'poc_pic_phone'    => '081377889900',
                'poc_sales_name'   => 'Ahmad (Sales Enterprise)',
                'poc_duration'     => '14 Hari (22 Okt - 05 Nov 2026)',
                'notes'            => 'Uji coba POC switch jaringan CCTV dan medical imaging RS Siloam.',
            ],
        ];

        foreach ($items as $data) {
            $data['created_by'] = $warehouseUser->id;
            WarehouseItem::updateOrCreate(
                ['item_code' => $data['item_code']],
                $data
            );
        }

        // 4. Seeder Kategori Produk
        $categories = [
            ['code' => 'CAT-RT',  'name' => 'Router & Gateway',               'description' => 'Core router, enterprise edge router, branch SD-WAN gateway', 'icon' => 'server', 'status' => 'Aktif'],
            ['code' => 'CAT-SW',  'name' => 'Switch Managed & PoE',           'description' => 'L2/L3 Managed switches, PoE+ access switch, core fiber switch', 'icon' => 'cpu', 'status' => 'Aktif'],
            ['code' => 'CAT-FW',  'name' => 'Firewall & Security',            'description' => 'Next-Generation Firewall (NGFW), UTM appliance, SSL VPN', 'icon' => 'shield', 'status' => 'Aktif'],
            ['code' => 'CAT-AP',  'name' => 'Access Point & Wireless',        'description' => 'Indoor/Outdoor Wi-Fi 6 access points, wireless controllers', 'icon' => 'wifi', 'status' => 'Aktif'],
            ['code' => 'CAT-SRV', 'name' => 'Server & Storage',               'description' => 'Rackmount servers, SAN/NAS storage, hyperconverged nodes', 'icon' => 'database', 'status' => 'Aktif'],
            ['code' => 'CAT-CAB', 'name' => 'Kabel, SFP & Fiber Optic',       'description' => 'Transceiver SFP/SFP+, Patch cord single/multimode, UTP Cat6/6A', 'icon' => 'git-commit', 'status' => 'Aktif'],
            ['code' => 'CAT-ACC', 'name' => 'Power Supply & Rack Accessories', 'description' => 'UPS rackmount, PDU, modular PSU redundant, patch panel', 'icon' => 'zap', 'status' => 'Aktif'],
        ];
        foreach ($categories as $cat) {
            \App\Models\WarehouseCategory::updateOrCreate(['code' => $cat['code']], $cat);
        }

        // 5. Seeder Satuan
        $units = [
            ['code' => 'UNT', 'name' => 'Unit',  'short_name' => 'Unit', 'description' => 'Satuan standar per unit perangkat utuh', 'is_active' => true],
            ['code' => 'PCS', 'name' => 'Pieces','short_name' => 'Pcs',  'description' => 'Satuan per keping atau barang satuan kecil', 'is_active' => true],
            ['code' => 'BOX', 'name' => 'Box',   'short_name' => 'Box',  'description' => 'Satuan kotak kemasan', 'is_active' => true],
            ['code' => 'ROL', 'name' => 'Roll',  'short_name' => 'Rol',  'description' => 'Satuan gulungan kabel (e.g. 305m)', 'is_active' => true],
            ['code' => 'MTR', 'name' => 'Meter', 'short_name' => 'M',    'description' => 'Satuan panjang kabel per meter', 'is_active' => true],
            ['code' => 'SET', 'name' => 'Set',   'short_name' => 'Set',  'description' => 'Paket perangkat lengkap aksesoris', 'is_active' => true],
            ['code' => 'PAK', 'name' => 'Pack',  'short_name' => 'Pck',  'description' => 'Kemasan pack isi multipiece', 'is_active' => true],
        ];
        foreach ($units as $u) {
            \App\Models\WarehouseUnit::updateOrCreate(['code' => $u['code']], $u);
        }

        // 6. Seeder Supplier
        $suppliers = [
            [
                'code'              => 'SUP-2026-001',
                'name'              => 'PT Synnex Metrodata Indonesia',
                'contact_person'    => 'Bambang Sugianto',
                'phone'             => '021-29345800',
                'email'             => 'sales.cisco@synnexmetrodata.co.id',
                'address'           => 'APL Tower Lt. 42, Jl. Letjen S. Parman Kav. 28, Tanjung Duren, Jakarta Barat',
                'products_supplied' => 'Cisco, Fortinet, Aruba, H3C',
                'status'            => 'Aktif',
                'notes'             => 'Distributor resmi principal tier-1 Cisco & Fortinet Indonesia.',
            ],
            [
                'code'              => 'SUP-2026-002',
                'name'              => 'PT Computrade Technology International (CTI)',
                'contact_person'    => 'Yohanes Wijaya',
                'phone'             => '021-39707888',
                'email'             => 'yohanes@computrade.co.id',
                'address'           => 'Graha CTI, Jl. RS Fatmawati No. 75, Cilandak, Jakarta Selatan',
                'products_supplied' => 'Fortinet, F5 Networks, Huawei Enterprise',
                'status'            => 'Aktif',
                'notes'             => 'Distributor resmi solusi cybersecurity & network infrastructure.',
            ],
            [
                'code'              => 'SUP-2026-003',
                'name'              => 'PT Ingram Micro Indonesia',
                'contact_person'    => 'Denny Chandra',
                'phone'             => '021-50858000',
                'email'             => 'enterprise@ingrammicro.com',
                'address'           => 'Menara Mandiri II Lt. 15, Jl. Jend. Sudirman Kav. 54-55, Jakarta Selatan',
                'products_supplied' => 'Dell Technologies, HPE, Palo Alto Networks',
                'status'            => 'Aktif',
                'notes'             => 'Supplier server, storage, dan firewall enterprise.',
            ],
            [
                'code'              => 'SUP-2026-004',
                'name'              => 'PT Mastersystem Infotama',
                'contact_person'    => 'Jessica Tendean',
                'phone'             => '021-57973555',
                'email'             => 'jessica.t@mastersystem.co.id',
                'address'           => 'Sudirman Plaza, Plaza Marein Lt. 19, Jl. Jend. Sudirman Kav. 76-78, Jakarta Selatan',
                'products_supplied' => 'Optical Fiber Transceivers, Belden Cabling, CommScope',
                'status'            => 'Aktif',
                'notes'             => 'Penyedia infrastruktur kabel optik data center & patch cord.',
            ],
            [
                'code'              => 'SUP-2026-005',
                'name'              => 'PT Citraweb Solusi Teknologi (Citraweb)',
                'contact_person'    => 'Aditya Nugroho',
                'phone'             => '0274-554444',
                'email'             => 'sales@mikrotik.co.id',
                'address'           => 'Jl. Pandean Sari Blok IV No. 10, Candok, Condongcatur, Sleman, Yogyakarta',
                'products_supplied' => 'MikroTik RouterBOARD, Wireless Outdoor, Ruijie Reyee',
                'status'            => 'Aktif',
                'notes'             => 'Master distributor resmi MikroTik & Ruijie Reyee di Indonesia.',
            ],
        ];
        foreach ($suppliers as $sup) {
            \App\Models\WarehouseSupplier::updateOrCreate(['code' => $sup['code']], $sup);
        }

        // 7. Seeder Warehouse (Master Lokasi Gudang)
        $locations = [
            [
                'code'          => 'WH-JKT-01',
                'name'          => 'Gudang Utama IPNET Jakarta',
                'pic_name'      => 'Ahmad',
                'pic_phone'     => '081298765432',
                'address'       => 'Jl. Kebon Jeruk Raya No. 12, Kebon Jeruk, Jakarta Barat 11530',
                'capacity_desc' => '600 Unit / 12 Rak Rackmount Server',
                'status'        => 'Operasional',
                'notes'         => 'Gudang operasional pusat inventaris perangkat jaringan & server IPNET.',
            ],
            [
                'code'          => 'WH-STAGING-01',
                'name'          => 'Lab Staging & Pre-Config IPNET',
                'pic_name'      => 'Dani Pratama',
                'pic_phone'     => '081377889900',
                'address'       => 'Ruko Grand Aries Niaga Blok E1 No. 2A, Meruya Utara, Jakarta Barat',
                'capacity_desc' => '250 Unit / 6 Test Bench Racks',
                'status'        => 'Operasional',
                'notes'         => 'Ruang staging pengujian, QC, upgrade firmware, dan konfigurasi sebelum pengiriman.',
            ],
            [
                'code'          => 'WH-TNG-02',
                'name'          => 'Gudang Cadangan & Spare Transit',
                'pic_name'      => 'Hendra Kusuma',
                'pic_phone'     => '081122334455',
                'address'       => 'Pergudangan Kosambi Indah Blok D-08, Jl. Raya Perancis, Tangerang',
                'capacity_desc' => '1.200 Unit / 25 Pallet Rack',
                'status'        => 'Operasional',
                'notes'         => 'Buffer stock untuk backup perangkat SLA 4-jam & kabel bulk roll.',
            ],
            [
                'code'          => 'WH-SBY-03',
                'name'          => 'Gudang Proyek Regional Surabaya',
                'pic_name'      => 'Fajar Setiawan',
                'pic_phone'     => '081234567890',
                'address'       => 'Ruko HR Muhammad Square Blok C-15, Sukomanunggal, Surabaya, Jawa Timur',
                'capacity_desc' => '300 Unit / 5 Rak Staging',
                'status'        => 'Operasional',
                'notes'         => 'Hub logistik implementasi proyek Jawa Timur, Bali, dan Nusa Tenggara.',
            ],
        ];
        foreach ($locations as $loc) {
            \App\Models\WarehouseLocation::updateOrCreate(['code' => $loc['code']], $loc);
        }

        // 8. Seeder Master Produk
        $catSW = \App\Models\WarehouseCategory::where('code', 'CAT-SW')->first();
        $catFW = \App\Models\WarehouseCategory::where('code', 'CAT-FW')->first();
        $catRT = \App\Models\WarehouseCategory::where('code', 'CAT-RT')->first();
        $catAP = \App\Models\WarehouseCategory::where('code', 'CAT-AP')->first();
        $catCAB = \App\Models\WarehouseCategory::where('code', 'CAT-CAB')->first();
        $catACC = \App\Models\WarehouseCategory::where('code', 'CAT-ACC')->first();

        $untUnit = \App\Models\WarehouseUnit::where('code', 'UNT')->first();
        $untPcs = \App\Models\WarehouseUnit::where('code', 'PCS')->first();
        $untRol = \App\Models\WarehouseUnit::where('code', 'ROL')->first();

        $sup1 = \App\Models\WarehouseSupplier::where('code', 'SUP-2026-001')->first();
        $sup2 = \App\Models\WarehouseSupplier::where('code', 'SUP-2026-002')->first();
        $sup4 = \App\Models\WarehouseSupplier::where('code', 'SUP-2026-004')->first();
        $sup5 = \App\Models\WarehouseSupplier::where('code', 'SUP-2026-005')->first();

        $loc1 = \App\Models\WarehouseLocation::where('code', 'WH-JKT-01')->first();
        $loc2 = \App\Models\WarehouseLocation::where('code', 'WH-STAGING-01')->first();
        $loc3 = \App\Models\WarehouseLocation::where('code', 'WH-TNG-02')->first();

        $products = [
            [
                'sku'             => 'PRD-CS-3850',
                'barcode'         => '882658532014',
                'name'            => 'Cisco Catalyst 3850 48-Port PoE+ Switch',
                'brand'           => 'Cisco',
                'category_id'     => $catSW ? $catSW->id : null,
                'category_name'   => 'Switch Managed & PoE',
                'unit_id'         => $untUnit ? $untUnit->id : null,
                'unit_name'       => 'Unit',
                'supplier_id'     => $sup1 ? $sup1->id : null,
                'supplier_name'   => 'PT Synnex Metrodata Indonesia',
                'warehouse_id'    => $loc1 ? $loc1->id : null,
                'warehouse_name'  => 'Gudang Utama IPNET Jakarta',
                'rack_position'   => 'Rak A-01 / Slot 3',
                'min_stock'       => 3,
                'current_stock'   => 12,
                'unit_price'      => 45000000,
                'specifications'  => '48x Gigabit Ethernet 10/100/1000 PoE+ ports, 715W AC Power Supply, StackWise-480, IP Base License.',
                'status'          => 'Tersedia',
            ],
            [
                'sku'             => 'PRD-FG-100F',
                'barcode'         => '847209188432',
                'name'            => 'Fortinet FortiGate 100F Next-Gen Firewall',
                'brand'           => 'Fortinet',
                'category_id'     => $catFW ? $catFW->id : null,
                'category_name'   => 'Firewall & Security',
                'unit_id'         => $untUnit ? $untUnit->id : null,
                'unit_name'       => 'Unit',
                'supplier_id'     => $sup2 ? $sup2->id : null,
                'supplier_name'   => 'PT Computrade Technology International (CTI)',
                'warehouse_id'    => $loc1 ? $loc1->id : null,
                'warehouse_name'  => 'Gudang Utama IPNET Jakarta',
                'rack_position'   => 'Rak A-02 / Slot 1',
                'min_stock'       => 2,
                'current_stock'   => 8,
                'unit_price'      => 62000000,
                'specifications'  => '22x GE RJ45, 4x 10GE SFP+ slots, NGFW throughput 1 Gbps, Threat Protection 700 Mbps, Dual AC PSU.',
                'status'          => 'Tersedia',
            ],
            [
                'sku'             => 'PRD-CCR-2004',
                'barcode'         => '4752224005824',
                'name'            => 'MikroTik CCR2004-16G-2S+ Core Router',
                'brand'           => 'Mikrotik',
                'category_id'     => $catRT ? $catRT->id : null,
                'category_name'   => 'Router & Gateway',
                'unit_id'         => $untUnit ? $untUnit->id : null,
                'unit_name'       => 'Unit',
                'supplier_id'     => $sup5 ? $sup5->id : null,
                'supplier_name'   => 'PT Citraweb Solusi Teknologi (Citraweb)',
                'warehouse_id'    => $loc2 ? $loc2->id : null,
                'warehouse_name'  => 'Lab Staging & Pre-Config IPNET',
                'rack_position'   => 'Bench Staging 2',
                'min_stock'       => 2,
                'current_stock'   => 5,
                'unit_price'      => 8500000,
                'specifications'  => '16x Gigabit Ethernet, 2x 10G SFP+ cages, Annapurna Labs Alpine AL32400 Quad-Core 1.7GHz, 4GB RAM, Dual PSU.',
                'status'          => 'Tersedia',
            ],
            [
                'sku'             => 'PRD-CS-9200L',
                'barcode'         => '882658991205',
                'name'            => 'Cisco Catalyst 9200L 24-Port Gigabit Switch',
                'brand'           => 'Cisco',
                'category_id'     => $catSW ? $catSW->id : null,
                'category_name'   => 'Switch Managed & PoE',
                'unit_id'         => $untUnit ? $untUnit->id : null,
                'unit_name'       => 'Unit',
                'supplier_id'     => $sup1 ? $sup1->id : null,
                'supplier_name'   => 'PT Synnex Metrodata Indonesia',
                'warehouse_id'    => $loc1 ? $loc1->id : null,
                'warehouse_name'  => 'Gudang Utama IPNET Jakarta',
                'rack_position'   => 'Rak B-01 / Slot 2',
                'min_stock'       => 4,
                'current_stock'   => 15,
                'unit_price'      => 28500000,
                'specifications'  => '24 ports data, 4x 1G fixed uplinks, Network Essentials license, Cisco DNA ready, Stacking support.',
                'status'          => 'Tersedia',
            ],
            [
                'sku'             => 'PRD-FAP-231F',
                'barcode'         => '847209210983',
                'name'            => 'FortiAP 231F Indoor Wi-Fi 6 Access Point',
                'brand'           => 'Fortinet',
                'category_id'     => $catAP ? $catAP->id : null,
                'category_name'   => 'Access Point & Wireless',
                'unit_id'         => $untUnit ? $untUnit->id : null,
                'unit_name'       => 'Unit',
                'supplier_id'     => $sup2 ? $sup2->id : null,
                'supplier_name'   => 'PT Computrade Technology International (CTI)',
                'warehouse_id'    => $loc1 ? $loc1->id : null,
                'warehouse_name'  => 'Gudang Utama IPNET Jakarta',
                'rack_position'   => 'Rak C-03 / Bin 4',
                'min_stock'       => 5,
                'current_stock'   => 20,
                'unit_price'      => 7800000,
                'specifications'  => 'Tri-radio 802.11ax (Wi-Fi 6) 2x2:2 MU-MIMO, Bluetooth Low Energy (BLE) radio, 2x 1GbE RJ45 ports (1x PoE+).',
                'status'          => 'Tersedia',
            ],
            [
                'sku'             => 'PRD-SFP-10G-SR',
                'barcode'         => '074312890123',
                'name'            => 'SFP+ 10GBASE-SR 850nm 300m MMF Transceiver',
                'brand'           => 'Cisco Compatible',
                'category_id'     => $catCAB ? $catCAB->id : null,
                'category_name'   => 'Kabel, SFP & Fiber Optic',
                'unit_id'         => $untPcs ? $untPcs->id : null,
                'unit_name'       => 'Pcs',
                'supplier_id'     => $sup4 ? $sup4->id : null,
                'supplier_name'   => 'PT Mastersystem Infotama',
                'warehouse_id'    => $loc1 ? $loc1->id : null,
                'warehouse_name'  => 'Gudang Utama IPNET Jakarta',
                'rack_position'   => 'Laci Aset Optik 1',
                'min_stock'       => 10,
                'current_stock'   => 38,
                'unit_price'      => 650000,
                'specifications'  => '10Gbps Multi-Mode Fiber (MMF) LC Duplex Connector, 850nm wavelength up to 300 meters on OM3/OM4.',
                'status'          => 'Tersedia',
            ],
            [
                'sku'             => 'PRD-BEL-CAT6',
                'barcode'         => '612345678901',
                'name'            => 'Belden UTP Cable Cat6 305M Roll Original',
                'brand'           => 'Belden',
                'category_id'     => $catCAB ? $catCAB->id : null,
                'category_name'   => 'Kabel, SFP & Fiber Optic',
                'unit_id'         => $untRol ? $untRol->id : null,
                'unit_name'       => 'Rol',
                'supplier_id'     => $sup4 ? $sup4->id : null,
                'supplier_name'   => 'PT Mastersystem Infotama',
                'warehouse_id'    => $loc3 ? $loc3->id : null,
                'warehouse_name'  => 'Gudang Cadangan & Spare Transit',
                'rack_position'   => 'Pallet K-01',
                'min_stock'       => 5,
                'current_stock'   => 18,
                'unit_price'      => 2450000,
                'specifications'  => 'Belden 7814A Category 6 Unshielded Twisted Pair 23 AWG Solid Bare Copper, 305 Meter Box, Grey Jacket.',
                'status'          => 'Tersedia',
            ],
            [
                'sku'             => 'PRD-UPS-3000VA',
                'barcode'         => '731304312891',
                'name'            => 'APC Smart-UPS On-Line SRT 3000VA 230V',
                'brand'           => 'APC Schneider',
                'category_id'     => $catACC ? $catACC->id : null,
                'category_name'   => 'Power Supply & Rack Accessories',
                'unit_id'         => $untUnit ? $untUnit->id : null,
                'unit_name'       => 'Unit',
                'supplier_id'     => $sup1 ? $sup1->id : null,
                'supplier_name'   => 'PT Synnex Metrodata Indonesia',
                'warehouse_id'    => $loc1 ? $loc1->id : null,
                'warehouse_name'  => 'Gudang Utama IPNET Jakarta',
                'rack_position'   => 'Zona Lantai Heavy-Duty',
                'min_stock'       => 1,
                'current_stock'   => 4,
                'unit_price'      => 34000000,
                'specifications'  => 'Double-conversion On-Line Topology, 3000VA / 2700W, Rack/Tower convertible 2U, SmartSlot Network Card.',
                'status'          => 'Tersedia',
            ],
        ];

        foreach ($products as $p) {
            \App\Models\WarehouseProduct::updateOrCreate(['sku' => $p['sku']], $p);
        }

        // 9. Seeder Purchase Orders (PO)
        $po1 = \App\Models\WarehousePurchaseOrder::updateOrCreate(
            ['po_number' => 'PO-IPN-2026-001'],
            [
                'po_date'        => now()->subDays(15)->format('Y-m-d'),
                'supplier_id'    => $sup1 ? $sup1->id : null,
                'supplier_name'  => 'PT Synnex Metrodata Indonesia',
                'warehouse_id'   => $loc1 ? $loc1->id : null,
                'warehouse_name' => 'Gudang Utama IPNET Jakarta',
                'expected_date'  => now()->subDays(3)->format('Y-m-d'),
                'total_amount'   => 145000000,
                'status'         => 'Selesai',
                'notes'          => 'Pengadaan unit switch Cisco Catalyst & UPS untuk proyek Bank Mandiri & BRI.',
                'created_by'     => $warehouseUser->id,
            ]
        );
        $prodCS = \App\Models\WarehouseProduct::where('sku', 'PRD-CS-3850')->first();
        if ($prodCS) {
            \App\Models\WarehousePoItem::updateOrCreate(
                ['purchase_order_id' => $po1->id, 'product_name' => $prodCS->name],
                [
                    'product_id'   => $prodCS->id,
                    'quantity'     => 3,
                    'unit'         => 'Unit',
                    'unit_price'   => 45000000,
                    'subtotal'     => 135000000,
                    'received_qty' => 3,
                    'notes'        => 'Diterima dalam kondisi prima dan tersegel resmi.',
                ]
            );
        }

        $po2 = \App\Models\WarehousePurchaseOrder::updateOrCreate(
            ['po_number' => 'PO-IPN-2026-002'],
            [
                'po_date'        => now()->subDays(6)->format('Y-m-d'),
                'supplier_id'    => $sup2 ? $sup2->id : null,
                'supplier_name'  => 'PT Computrade Technology International (CTI)',
                'warehouse_id'   => $loc1 ? $loc1->id : null,
                'warehouse_name' => 'Gudang Utama IPNET Jakarta',
                'expected_date'  => now()->addDays(4)->format('Y-m-d'),
                'total_amount'   => 88500000,
                'status'         => 'Disetujui',
                'notes'          => 'Pengadaan FortiGate 100F & FortiAP 231F restock Q4.',
                'created_by'     => $warehouseUser->id,
            ]
        );
        $prodFG = \App\Models\WarehouseProduct::where('sku', 'PRD-FG-100F')->first();
        if ($prodFG) {
            \App\Models\WarehousePoItem::updateOrCreate(
                ['purchase_order_id' => $po2->id, 'product_name' => $prodFG->name],
                [
                    'product_id'   => $prodFG->id,
                    'quantity'     => 1,
                    'unit'         => 'Unit',
                    'unit_price'   => 62000000,
                    'subtotal'     => 62000000,
                    'received_qty' => 0,
                    'notes'        => 'Sedang proses shipping dari distributor ke gudang.',
                ]
            );
        }

        // 10. Seeder Stock Opname
        $so1 = \App\Models\WarehouseStockOpname::updateOrCreate(
            ['so_number' => 'SO-2026-10-001'],
            [
                'so_date'              => now()->subDays(5)->format('Y-m-d'),
                'warehouse_id'         => $loc1 ? $loc1->id : null,
                'warehouse_name'       => 'Gudang Utama IPNET Jakarta',
                'auditor_name'         => 'Ahmad (Warehouse Lead)',
                'status'               => 'Selesai',
                'notes'                => 'Audit fisik kuartal IV inventori switch, firewall, dan modul optik.',
                'total_items_checked'  => 18,
                'total_discrepancy'    => 0,
                'created_by'           => $warehouseUser->id,
            ]
        );
        if ($prodCS) {
            \App\Models\WarehouseSoItem::updateOrCreate(
                ['stock_opname_id' => $so1->id, 'item_code' => $prodCS->sku],
                [
                    'product_id'       => $prodCS->id,
                    'product_name'     => $prodCS->name,
                    'system_stock'     => 12,
                    'physical_stock'   => 12,
                    'difference'       => 0,
                    'condition_status' => 'Baik & Lengkap',
                    'notes'            => 'Stok fisik rak A-01 klop 100% dengan database.',
                ]
            );
        }

        // 11. Seeder Delivery Order (DO)
        $do1 = \App\Models\WarehouseDeliveryOrder::updateOrCreate(
            ['do_number' => 'DO-IPN-2026-0089'],
            [
                'do_date'           => now()->subDays(8)->format('Y-m-d'),
                'reference_no'      => 'CTR-BRI-LAN-2026',
                'warehouse_id'      => $loc1 ? $loc1->id : null,
                'warehouse_name'    => 'Gudang Utama IPNET Jakarta',
                'recipient_name'    => 'Budi Hartono (IT Infrastructure)',
                'recipient_company' => 'PT Bank Rakyat Indonesia (Persero) Tbk',
                'recipient_phone'   => '081287654321',
                'delivery_address'  => 'Gedung Kantor Pusat BRI, Jl. Jend. Sudirman Kav. 44-46, Jakarta Pusat',
                'courier_name'      => 'Supriyadi (Internal Driver)',
                'vehicle_no'        => 'B 9182 JKL',
                'tracking_no'       => 'TRK-BRI-0089',
                'status'            => 'Terkirim & Diterima',
                'notes'             => 'Pengiriman switch core & patch cord untuk peremajaan LAN BRI Pusat.',
                'created_by'        => $warehouseUser->id,
            ]
        );
        if ($prodCS) {
            \App\Models\WarehouseDoItem::updateOrCreate(
                ['delivery_order_id' => $do1->id, 'product_name' => $prodCS->name],
                [
                    'product_id'    => $prodCS->id,
                    'serial_number' => 'FCW2143L0G7',
                    'quantity'      => 1,
                    'unit'          => 'Unit',
                    'notes'         => 'Sudah ditandatangani BAST penerimaan oleh PIC klien.',
                ]
            );
        }

        $do2 = \App\Models\WarehouseDeliveryOrder::updateOrCreate(
            ['do_number' => 'DO-IPN-2026-0091'],
            [
                'do_date'           => now()->subDays(1)->format('Y-m-d'),
                'reference_no'      => 'PRJ-PLN-ROUTER-2026',
                'warehouse_id'      => $loc2 ? $loc2->id : null,
                'warehouse_name'    => 'Lab Staging & Pre-Config IPNET',
                'recipient_name'    => 'Bambang Eko (Head of Network)',
                'recipient_company' => 'PT PLN (Persero) Kantor Pusat',
                'recipient_phone'   => '081299112233',
                'delivery_address'  => 'Gedung PT PLN (Persero) Kantor Pusat, Jl. Trunojoyo Blok M I/135, Kebayoran Baru, Jakarta Selatan',
                'courier_name'      => 'Asep Sunandar (Lalamove Van)',
                'vehicle_no'        => 'B 4521 SBN',
                'tracking_no'       => 'LLM-20261026-991',
                'status'            => 'Dalam Pengiriman',
                'notes'             => 'Pengiriman router CCR2004 setelah lolos uji pre-config di lab.',
                'created_by'        => $warehouseUser->id,
            ]
        );
        $prodCCR = \App\Models\WarehouseProduct::where('sku', 'PRD-CCR-2004')->first();
        if ($prodCCR) {
            \App\Models\WarehouseDoItem::updateOrCreate(
                ['delivery_order_id' => $do2->id, 'product_name' => $prodCCR->name],
                [
                    'product_id'    => $prodCCR->id,
                    'serial_number' => 'E81D084F592C',
                    'quantity'      => 1,
                    'unit'          => 'Unit',
                    'notes'         => 'Dalam perjalanan menuju lokasi klien PLN Pusat.',
                ]
            );
        }
    }
}


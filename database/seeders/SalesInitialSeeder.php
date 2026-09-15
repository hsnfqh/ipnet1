<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use App\Models\User;
use App\Models\Division;
use App\Models\Client;
use App\Models\Vendor;
use App\Models\InventoryItem;
use App\Models\InventoryTransaction;
use App\Models\Project;

class SalesInitialSeeder extends Seeder
{
    public function run(): void
    {
        // -------------------------------------------------------------
        // 1. IDENTIFIKASI 9 USER SALES RESMI & DIVISI
        // -------------------------------------------------------------
        $salesDonny   = User::where('email', 'donny.burnan@ipnetsolusindo.com')->first();
        $salesErie    = User::where('email', 'erie@ipnetsolusindo.com')->first();
        $salesHendry  = User::where('email', 'hendry.wibowo@ipnetsolusindo.com')->first();
        $salesNabylla = User::where('email', 'nabylla.berlianita@ipnetsolusindo.com')->first();
        $salesNelvia  = User::where('email', 'nelvia.nataliandi@ipnetsolusindo.com')->first();
        $salesRaiza   = User::where('email', 'raiza@ipnetsolusindo.com')->first();
        $salesRibka   = User::where('email', 'ribka.junita@ipnetsolusindo.com')->first() ?? User::where('email', 'ribka@ipnetsolusindo.com')->first();
        $salesSabar   = User::where('email', 'sabar.sianturi@ipnetsolusindo.com')->first();
        $salesWidodo  = User::where('email', 'widodo@ipnetsolusindo.com')->first();
        $pmRizki      = User::where('email', 'rizki@ipnetsolusindo.com')->first();

        // Fallback user ID jika belum ada
        $defaultSalesId = $salesRibka?->id ?? $salesRaiza?->id ?? $salesWidodo?->id ?? User::first()?->id;

        $divNet = Division::where('code', 'NET')->first() ?? Division::firstOrCreate(['name' => 'Divisi Network'], ['code' => 'NET', 'description' => 'Jaringan & Infrastruktur']);
        $divSec = Division::where('code', 'SEC')->first() ?? Division::firstOrCreate(['name' => 'Divisi Security'], ['code' => 'SEC', 'description' => 'Keamanan Jaringan & Cyber Security']);
        $divMnt = Division::where('code', 'MNT')->first() ?? Division::firstOrCreate(['name' => 'Divisi Maintenance & Helpdesk'], ['code' => 'MNT', 'description' => 'Helpdesk & Pemeliharaan']);

        // -------------------------------------------------------------
        // 2. SEED KLIEN B2B / ENTERPRISE (16 KLIEN)
        // -------------------------------------------------------------
        $clients = [
            [
                'name'       => 'PT Bank Central Asia Tbk',
                'department' => 'Data Center & Network Operations',
                'pic_name'   => 'Bpk. Kevin Tanujaya',
                'phone'      => '021-23588000',
                'email'      => 'kevin_t@bca.co.id',
                'address'    => 'Menara BCA Lt. 22, Grand Indonesia, Jl. M.H. Thamrin No. 1, Jakarta Pusat',
                'notes'      => 'Klien prioritas tier-1 perbankan, regulasi SLA ketat 99.99%',
            ],
            [
                'name'       => 'PT Bank Mandiri (Persero) Tbk',
                'department' => 'IT Infrastructure & Security',
                'pic_name'   => 'Bpk. Arya Wiguna',
                'phone'      => '021-5265000',
                'email'      => 'it.infra@bankmandiri.co.id',
                'address'    => 'Plaza Mandiri Lt. 14, Jl. Jend. Gatot Subroto Kav. 36-38, Jakarta Selatan',
                'notes'      => 'Project firewall enterprise, WAF, dan switch core DC',
            ],
            [
                'name'       => 'PT Bank Syariah Indonesia Tbk (BSI)',
                'department' => 'IT Enterprise Architecture',
                'pic_name'   => 'Ibu Fatimah Zahra',
                'phone'      => '021-3450099',
                'email'      => 'fatimah.z@bankbsi.co.id',
                'address'    => 'The Tower Lt. 10, Jl. Gatot Subroto No. 27, Jakarta Selatan',
                'notes'      => 'Ekspansi jaringan SD-WAN kantor cabang regional',
            ],
            [
                'name'       => 'PT Bank BTPN Tbk',
                'department' => 'Network & Communication Dept',
                'pic_name'   => 'Bpk. Dimas Prasetya',
                'phone'      => '021-30026000',
                'email'      => 'dimas.p@btpn.com',
                'address'    => 'Menara BTPN Lt. 19, CBD Mega Kuningan, Jakarta Selatan',
                'notes'      => 'Upgrade WiFi Corporate dan implementasi 802.1X Radius',
            ],
            [
                'name'       => 'RS Siloam Hospitals Group',
                'department' => 'Medical IT Systems & Infrastructure',
                'pic_name'   => 'dr. Farhan Malik, M.Kom',
                'phone'      => '021-25606100',
                'email'      => 'it.corp@siloamhospitals.com',
                'address'    => 'Siloam Hospitals Kebon Jeruk, Jl. Raya Perjuangan No. 8, Jakarta Barat',
                'notes'      => 'Kontrak pemeliharaan SLA tahunan 24/7 dan segmentasi VLAN Medis',
            ],
            [
                'name'       => 'RS Mayapada Healthcare Group',
                'department' => 'Hospital Technology Information',
                'pic_name'   => 'Bpk. Hendra Gunawan',
                'phone'      => '021-29217777',
                'email'      => 'it.support@mayapadahospital.com',
                'address'    => 'Mayapada Hospital Jakarta Selatan, Jl. Lebak Bulus I Kav. 29, Jakarta',
                'notes'      => 'Pengadaan Core Switch dan Access Point WiFi 6 untuk gedung baru',
            ],
            [
                'name'       => 'PT Telekomunikasi Selular (Telkomsel)',
                'department' => 'Enterprise Network Security Core',
                'pic_name'   => 'Bpk. Ridwan Hakim',
                'phone'      => '021-5240123',
                'email'      => 'ridwan.hakim@telkomsel.co.id',
                'address'    => 'Telkomsel Smart Office Lt. 16, Jl. Jend. Gatot Subroto Kav. 52, Jakarta',
                'notes'      => 'Implementasi Zero Trust ZTNA dan FortiAnalyzer',
            ],
            [
                'name'       => 'PT Indosat Ooredoo Hutchison Tbk',
                'department' => 'Data Center Infrastructure Operations',
                'pic_name'   => 'Bpk. Satria Wibowo',
                'phone'      => '021-30003001',
                'email'      => 'satria.w@ioh.co.id',
                'address'    => 'Kantor Pusat Indosat, Jl. Medan Merdeka Barat No. 21, Jakarta Pusat',
                'notes'      => 'Pemasangan Cisco Nexus Data Center Switch di Jatiluhur DC',
            ],
            [
                'name'       => 'PT Shopee International Indonesia',
                'department' => 'Tech Infrastructure & Cyber Defense',
                'pic_name'   => 'Bpk. Raymond Tan',
                'phone'      => '021-80604255',
                'email'      => 'raymond.tan@shopee.com',
                'address'    => 'Cyber 2 Tower Lt. 28, Jl. HR Rasuna Said Blok X-5 Kav. 13, Jakarta Selatan',
                'notes'      => 'Vulnerability Assessment dan Red Teaming Security Analysis',
            ],
            [
                'name'       => 'PT Tokopedia (GoTo Group)',
                'department' => 'Cloud & Network Engineering',
                'pic_name'   => 'Ibu Cindy Amanda',
                'phone'      => '021-80647300',
                'email'      => 'cindy.amanda@tokopedia.com',
                'address'    => 'Tokopedia Tower Lt. 42, Ciputra World 2, Kuningan, Jakarta Selatan',
                'notes'      => 'Implementasi WAF dan integrasi mitigasi anti-DDoS Layer 7',
            ],
            [
                'name'       => 'PT Global Tiket Network (Tiket.com)',
                'department' => 'DevOps & Cloud Connectivity',
                'pic_name'   => 'Bpk. Edwin Surya',
                'phone'      => '021-39710888',
                'email'      => 'edwin.s@tiket.com',
                'address'    => 'Gedung Grha Niaga Thamrin Lt. 5, Jl. KH Mas Mansyur, Jakarta Pusat',
                'notes'      => 'Pemasangan Dedicated Cross-Connect AWS DirectConnect 10Gbps',
            ],
            [
                'name'       => 'Summarecon Mall Group',
                'department' => 'Commercial Property & IT Network',
                'pic_name'   => 'Bpk. Bambang Sutrisno',
                'phone'      => '021-4531101',
                'email'      => 'bambang.s@summarecon.com',
                'address'    => 'Plaza Summarecon Lt. 7, Jl. Perintis Kemerdekaan No. 42, Jakarta Timur',
                'notes'      => 'WiFi 6 Publik Mall Kelapa Gading & Managed Service mingguan',
            ],
            [
                'name'       => 'PT Lazada Express Indonesia',
                'department' => 'Supply Chain & Warehouse Technology',
                'pic_name'   => 'Bpk. Agus Firmansyah',
                'phone'      => '021-80630300',
                'email'      => 'agus.f@lazada.com',
                'address'    => 'Kawasan Industri MM2100 Blok LL No. 4, Cikarang Barat, Bekasi',
                'notes'      => 'Infrastruktur CCTV AI NVR 64TB dan switch PoE 48 Port',
            ],
            [
                'name'       => 'PT Sumber Alfaria Trijaya Tbk (Alfamart)',
                'department' => 'Head Office IT Infrastructure',
                'pic_name'   => 'Bpk. Wahyu Nugroho',
                'phone'      => '021-55755966',
                'email'      => 'it.network@alfamartku.com',
                'address'    => 'Alfa Tower Lt. 18, Alam Sutera, Tangerang, Banten',
                'notes'      => 'SD-WAN interkoneksi 300 hub distribusi logistik regional',
            ],
            [
                'name'       => 'PT Pertamina Hulu Energi',
                'department' => 'ICT Upstream Operations',
                'pic_name'   => 'Bpk. Ir. Dwi Wicaksono',
                'phone'      => '021-52908000',
                'email'      => 'ict.upstream@pertamina.com',
                'address'    => 'PHE Tower Lt. 11, Jl. TB Simatupang Kav. 99, Jakarta Selatan',
                'notes'      => 'Re-cabling Cat6A shielded dan penataan server rack DC',
            ],
            [
                'name'       => 'BASARNAS (Badan SAR Nasional)',
                'department' => 'Pusdatin & Cyber Security',
                'pic_name'   => 'Letkol (Tek) Danang S.',
                'phone'      => '021-65867510',
                'email'      => 'cybersec@basarnas.go.id',
                'address'    => 'Jl. Angkasa Blok B.15 Kav. 2-3, Kemayoran, Jakarta Pusat',
                'notes'      => 'Firewall Perimeter dan monitoring NOC command center',
            ],
        ];

        foreach ($clients as $c) {
            Client::updateOrCreate(
                ['name' => $c['name']],
                array_merge($c, ['created_by' => $defaultSalesId])
            );
        }

        // -------------------------------------------------------------
        // 3. SEED PRINCIPAL / VENDOR (10 VENDOR)
        // -------------------------------------------------------------
        $vendors = [
            [
                'name'             => 'Cisco Systems Indonesia',
                'department'       => 'Enterprise Networking & Security',
                'channel_manager'  => 'Ferry Gunawan (0811-8899-0011)',
                'phone'            => '021-57973000',
                'email'            => 'cisco-id-partner@cisco.com',
                'address'          => 'World Trade Center 2 Lt. 18, Jl. Jend. Sudirman Kav. 29, Jakarta',
                'product_category' => 'Catalyst Switches, Nexus, ISR/ASR Routers, Meraki, Webex',
                'notes'            => 'Tier-1 Gold Partner status',
            ],
            [
                'name'             => 'Fortinet Indonesia',
                'department'       => 'Channel Partner Distribution',
                'channel_manager'  => 'Denny Sugiarto (0818-9900-1122)',
                'phone'            => '021-83783788',
                'email'            => 'sales-id@fortinet.com',
                'address'          => 'Menara BCA Lt. 38, Grand Indonesia, Jl. M.H. Thamrin No. 1, Jakarta',
                'product_category' => 'FortiGate NGFW, FortiSwitch, FortiAP, FortiAnalyzer, FortiSIEM',
                'notes'            => 'Authorized Enterprise Integrator',
            ],
            [
                'name'             => 'Juniper Networks Indonesia',
                'department'       => 'Enterprise Sales Team',
                'channel_manager'  => 'Maya Indriani (0813-4455-6677)',
                'phone'            => '021-29955800',
                'email'            => 'id-partners@juniper.net',
                'address'          => 'Plaza Sentral Lt. 12, Jl. Jend. Sudirman Kav. 47, Jakarta',
                'product_category' => 'Router MX Series, Switch EX/QFX Series, SRX Firewall, Mist AI',
                'notes'            => 'Service Level Partner Juniper Care SLA',
            ],
            [
                'name'             => 'HPE Aruba Networks',
                'department'       => 'Commercial Networking',
                'channel_manager'  => 'Budi Santoso (0812-7788-9911)',
                'phone'            => '021-29345800',
                'email'            => 'aruba.id@hpe.com',
                'address'          => 'Prudential Tower Lt. 15, Jl. Jend. Sudirman Kav. 79, Jakarta',
                'product_category' => 'Aruba AP WiFi 6, Aruba CX Switches, ClearPass NAC, Aruba Central',
                'notes'            => 'Top Commercial Distributor',
            ],
            [
                'name'             => 'Dell Technologies Indonesia',
                'department'       => 'Server & Storage Partner Team',
                'channel_manager'  => 'Aditya Pratama (0811-2233-4455)',
                'phone'            => '021-50858800',
                'email'            => 'partner_id@dell.com',
                'address'          => 'Menara BCA Lt. 45, Jl. M.H. Thamrin No. 1, Jakarta Pusat',
                'product_category' => 'Server PowerEdge R750/R650, Storage Unity, PowerSwitch',
                'notes'            => 'Direct OEM Warranty Partner',
            ],
            [
                'name'             => 'Palo Alto Networks',
                'department'       => 'Next-Gen Cyber Security',
                'channel_manager'  => 'Taufik Hidayat (0812-9900-4433)',
                'phone'            => '021-29222999',
                'email'            => 'indonesia-sales@paloaltonetworks.com',
                'address'          => 'Sequis Tower Lt. 21, SCBD Lot 11B, Jl. Jend. Sudirman, Jakarta',
                'product_category' => 'PA Series Firewall, Prisma Access, Cortex XDR, SASE',
                'notes'            => 'Strategic Banking Project Partner',
            ],
            [
                'name'             => 'Sangfor Technologies Indonesia',
                'department'       => 'HCI & Cyber Security',
                'channel_manager'  => 'Andi Wijaya (0817-5544-3322)',
                'phone'            => '021-50986100',
                'email'            => 'sales.id@sangfor.com',
                'address'          => 'Treasury Tower Lt. 31, District 8 SCBD, Jakarta Selatan',
                'product_category' => 'Sangfor HCI, Sangfor NGAF Firewall, Cyber Command, IAG',
                'notes'            => 'Enterprise Gold Partner',
            ],
            [
                'name'             => 'Synnex Metrodata Indonesia',
                'department'       => 'Commercial Distribution',
                'channel_manager'  => 'Rudy Hartono (0812-3344-5566)',
                'phone'            => '021-29292929',
                'email'            => 'networking@metrodata.co.id',
                'address'          => 'APL Tower Lt. 39, Podomoro City, Jl. Letjen S. Parman, Jakarta Barat',
                'product_category' => 'Distributor Resmi Cisco, Aruba, APC Schneider, Belden',
                'notes'            => 'Lead Master Distributor Stockist',
            ],
            [
                'name'             => 'Exclusive Networks Indonesia',
                'department'       => 'Value Added Distributor',
                'channel_manager'  => 'Indra Kusuma (0815-1122-8899)',
                'phone'            => '021-83783788',
                'email'            => 'sales-id@exclusive-networks.com',
                'address'          => 'Kota Kasablanka Tower A Lt. 19, Jl. Casablanca Raya, Jakarta',
                'product_category' => 'Fortinet, Palo Alto Networks, F5 Networks, Infoblox, Rubrik',
                'notes'            => 'Cyber Security Specialist Distributor',
            ],
            [
                'name'             => 'Blue Power Technology',
                'department'       => 'Enterprise Infrastructure Solution',
                'channel_manager'  => 'Hendra Setiawan (0812-9988-7711)',
                'phone'            => '021-29955801',
                'email'            => 'marketing@bluepowertechnology.com',
                'address'          => 'Centennial Tower Lt. 27, Jl. Gatot Subroto Kav. 24-25, Jakarta',
                'product_category' => 'Extreme Networks, Lenovo ThinkSystem, IBM Storage',
                'notes'            => 'HCI & Data Center Distributor',
            ],
        ];

        foreach ($vendors as $v) {
            Vendor::updateOrCreate(
                ['name' => $v['name']],
                array_merge($v, ['created_by' => $defaultSalesId])
            );
        }

        // -------------------------------------------------------------
        // 4. SEED INVENTORY ITEMS & TRANSAKSI STOK (16 ITEMS)
        // -------------------------------------------------------------
        $items = [
            [
                'product_code' => 'SW-CS-9300-24P',
                'product_name' => 'Cisco Catalyst 9300 24-Port PoE+ Switch',
                'category'     => 'Switch',
                'stock'        => 14,
                'unit'         => 'Unit',
                'unit_price'   => 45000000,
                'status'       => 'Tersedia',
                'description'  => 'Enterprise Layer 3 Switch dengan 24 port Gigabit PoE+ (370W) dan modular uplink 10G/40G.'
            ],
            [
                'product_code' => 'SW-CS-9200-48P',
                'product_name' => 'Cisco Catalyst 9200 48-Port Gigabit PoE+',
                'category'     => 'Switch',
                'stock'        => 8,
                'unit'         => 'Unit',
                'unit_price'   => 38500000,
                'status'       => 'Tersedia',
                'description'  => 'Managed Access Switch 48 port Gigabit Ethernet PoE+ 740W dengan 4x 1G SFP fixed uplink.'
            ],
            [
                'product_code' => 'SW-AR-2930F-48G',
                'product_name' => 'Aruba 2930F 48G PoE+ 4SFP+ Switch',
                'category'     => 'Switch',
                'stock'        => 10,
                'unit'         => 'Unit',
                'unit_price'   => 29000000,
                'status'       => 'Tersedia',
                'description'  => 'Layer 3 Campus Access Switch dengan 48x RJ-45 PoE+ 370W dan 4x 10GbE SFP+ uplinks.'
            ],
            [
                'product_code' => 'FW-FG-100F',
                'product_name' => 'Fortinet FortiGate 100F Next-Gen Firewall',
                'category'     => 'Firewall',
                'stock'        => 6,
                'unit'         => 'Unit',
                'unit_price'   => 68000000,
                'status'       => 'Tersedia',
                'description'  => 'Next Generation Firewall 1 Gbps Threat Protection throughput dengan Dual AC Power Supply.'
            ],
            [
                'product_code' => 'FW-FG-60F',
                'product_name' => 'Fortinet FortiGate 60F Desktop Enterprise Firewall',
                'category'     => 'Firewall',
                'stock'        => 18,
                'unit'         => 'Unit',
                'unit_price'   => 16500000,
                'status'       => 'Tersedia',
                'description'  => 'Compact Desktop Next-Gen Firewall 700 Mbps Threat Protection, ideal untuk kantor cabang (Branch).'
            ],
            [
                'product_code' => 'FW-PA-440',
                'product_name' => 'Palo Alto PA-440 Next-Gen Firewall',
                'category'     => 'Firewall',
                'stock'        => 5,
                'unit'         => 'Unit',
                'unit_price'   => 42000000,
                'status'       => 'Tersedia',
                'description'  => 'Machine Learning-Powered NGFW untuk distributed enterprise branch & midsize retail.'
            ],
            [
                'product_code' => 'RT-JN-SRX345',
                'product_name' => 'Juniper SRX345 Services Gateway Router',
                'category'     => 'Router',
                'stock'        => 4,
                'unit'         => 'Unit',
                'unit_price'   => 52000000,
                'status'       => 'Tersedia',
                'description'  => 'High-security Branch Gateway Router dengan 8x 1GbE RJ45 dan 8x 1GbE SFP transceiver ports.'
            ],
            [
                'product_code' => 'RT-CS-ISR4331',
                'product_name' => 'Cisco ISR 4331 Integrated Services Router',
                'category'     => 'Router',
                'stock'        => 7,
                'unit'         => 'Unit',
                'unit_price'   => 48000000,
                'status'       => 'Tersedia',
                'description'  => 'Enterprise WAN Aggregation Router throughput 100Mbps up to 300Mbps dengan integrated voice/data.'
            ],
            [
                'product_code' => 'AP-AR-515',
                'product_name' => 'Aruba AP-515 Unified Campus Access Point WiFi 6',
                'category'     => 'Access Point',
                'stock'        => 32,
                'unit'         => 'Unit',
                'unit_price'   => 11500000,
                'status'       => 'Tersedia',
                'description'  => 'Dual-radio WiFi 6 (802.11ax) 4x4:4 MU-MIMO high density enterprise access point.'
            ],
            [
                'product_code' => 'AP-CS-9120AXI',
                'product_name' => 'Cisco Catalyst 9120AXI Indoor WiFi 6 AP',
                'category'     => 'Access Point',
                'stock'        => 24,
                'unit'         => 'Unit',
                'unit_price'   => 13200000,
                'status'       => 'Tersedia',
                'description'  => 'Enterprise-class WiFi 6 Access Point dengan integrated Bluetooth 5 & Zigbee radio.'
            ],
            [
                'product_code' => 'SRV-DL-R750',
                'product_name' => 'Dell PowerEdge R750 Rack Server 2U',
                'category'     => 'Server',
                'stock'        => 3,
                'unit'         => 'Unit',
                'unit_price'   => 125000000,
                'status'       => 'Tersedia',
                'description'  => 'Dual Intel Xeon Silver 4314 (32 Core), 128GB DDR4 ECC, 2x 960GB SAS SSD, Dual PSU 800W Platinum.'
            ],
            [
                'product_code' => 'SFP-10G-LR',
                'product_name' => 'Cisco 10GBASE-LR SFP+ Transceiver Module',
                'category'     => 'SFP / Module',
                'stock'        => 45,
                'unit'         => 'Pcs',
                'unit_price'   => 3200000,
                'status'       => 'Tersedia',
                'description'  => '10 Gigabit Ethernet single-mode optical transceiver, panjang gelombang 1310nm, jangkauan 10km.'
            ],
            [
                'product_code' => 'SFP-1G-SX',
                'product_name' => 'Finisar / HP 1000BASE-SX SFP Optical Transceiver',
                'category'     => 'SFP / Module',
                'stock'        => 60,
                'unit'         => 'Pcs',
                'unit_price'   => 950000,
                'status'       => 'Tersedia',
                'description'  => 'Gigabit Multimode Transceiver 850nm LC Connector, jangkauan transmisi hingga 550 meter.'
            ],
            [
                'product_code' => 'ACC-FO-PATCH-LC-LC',
                'product_name' => 'Fiber Optic Patch Cord LC-LC Duplex SM 3M',
                'category'     => 'Accessories',
                'stock'        => 120,
                'unit'         => 'Pcs',
                'unit_price'   => 125000,
                'status'       => 'Tersedia',
                'description'  => 'Duplex Single Mode 9/125um Yellow LSZH jacket 3 meters low insertion loss ceramic ferrule.'
            ],
            [
                'product_code' => 'ACC-CAT6A-305M',
                'product_name' => 'Belden CAT6A UTP Cable 305M Blue Box',
                'category'     => 'Accessories',
                'stock'        => 25,
                'unit'         => 'Roll',
                'unit_price'   => 2850000,
                'status'       => 'Tersedia',
                'description'  => 'Kabel UTP Cat6A 10Gbps 500MHz 23AWG Solid Bare Copper 305 meter LSZH Certified.'
            ],
            [
                'product_code' => 'LIC-FG-UTP-1Y',
                'product_name' => 'FortiGate 100F 1-Year UTP License',
                'category'     => 'License',
                'stock'        => 15,
                'unit'         => 'License',
                'unit_price'   => 24500000,
                'status'       => 'Tersedia',
                'description'  => 'Unified Threat Protection (UTP) License: IPS, Advanced Malware, Application Control, Web Filtering.'
            ],
        ];

        $createdItemModels = [];
        foreach ($items as $item) {
            $createdItemModels[] = InventoryItem::updateOrCreate(
                ['product_code' => $item['product_code']],
                $item
            );
        }

        // Catat transaksi dummy stock-in & stock-out
        if ($createdItemModels[0] ?? null) {
            InventoryTransaction::firstOrCreate(
                ['reference_no' => 'PO-IN-2026-081'],
                [
                    'inventory_item_id' => $createdItemModels[0]->id,
                    'type'              => 'in',
                    'qty'               => 10,
                    'transaction_date'  => now()->subDays(20)->toDateString(),
                    'notes'             => 'Penerimaan batch PO Cisco dari distributor Synnex Metrodata',
                    'created_by'        => $defaultSalesId,
                ]
            );
        }
        if ($createdItemModels[3] ?? null) {
            InventoryTransaction::firstOrCreate(
                ['reference_no' => 'DO-OUT-2026-044'],
                [
                    'inventory_item_id' => $createdItemModels[3]->id,
                    'type'              => 'out',
                    'qty'               => 2,
                    'transaction_date'  => now()->subDays(5)->toDateString(),
                    'notes'             => 'Alokasi implementasi Proyek Firewall Bank Mandiri DC',
                    'created_by'        => $defaultSalesId,
                ]
            );
        }

        // -------------------------------------------------------------
        // 5. SEED PROYEK & SALES PIPELINE LENGKAP (2026 REALISTIC)
        // -------------------------------------------------------------
        $salesReps = [
            'Donny'   => ['user_id' => $salesDonny?->id ?? $defaultSalesId, 'name' => 'Donny Burnan'],
            'Erie'    => ['user_id' => $salesErie?->id ?? $defaultSalesId, 'name' => 'Erie'],
            'Hendry'  => ['user_id' => $salesHendry?->id ?? $defaultSalesId, 'name' => 'Hendry Wibowo'],
            'Nabylla' => ['user_id' => $salesNabylla?->id ?? $defaultSalesId, 'name' => 'Nabylla Berlianita'],
            'Nelvia'  => ['user_id' => $salesNelvia?->id ?? $defaultSalesId, 'name' => 'Nelvia Nataliandi'],
            'Raiza'   => ['user_id' => $salesRaiza?->id ?? $defaultSalesId, 'name' => 'Raiza'],
            'Ribka'   => ['user_id' => $salesRibka?->id ?? $defaultSalesId, 'name' => 'Ribka Junita'],
            'Sabar'   => ['user_id' => $salesSabar?->id ?? $defaultSalesId, 'name' => 'Sabar Sianturi'],
            'Widodo'  => ['user_id' => $salesWidodo?->id ?? $defaultSalesId, 'name' => 'Widodo'],
        ];

        $salesProjects = [
            // --- TAHAP: OPPORTUNITY / PLANNING (PIPELINE TENDER) ---
            [
                'name'           => 'Tender Pengadaan SD-WAN 50 Kantor Cabang Regional Bank BSI',
                'client'         => 'PT Bank Syariah Indonesia Tbk (BSI)',
                'sales'          => $salesReps['Donny'],
                'division_id'    => $divNet->id,
                'location'       => 'Gedung The Tower & 50 Cabang Regional',
                'project_type'   => 'One-Time Project',
                'description'    => 'Pengadaan dan konfigurasi 50 unit FortiGate 60F SD-WAN terpusat dengan monitoring FortiManager Cloud.',
                'start_date'     => Carbon::create(2026, 9, 10),
                'deadline'       => Carbon::create(2026, 12, 20),
                'status'         => 'Opportunity',
                'stage'          => 'Acquire',
                'process_status' => 'In Progress',
                'acquire_status' => 'Penawaran Komersial',
                'contract_value' => 850000000,
                'po_number'      => null,
                'created_at'     => Carbon::create(2026, 8, 15),
            ],
            [
                'name'           => 'Pengadaan Core Switch Nexus 9K High-Density Data Center Indosat',
                'client'         => 'PT Indosat Ooredoo Hutchison Tbk',
                'sales'          => $salesReps['Erie'],
                'division_id'    => $divNet->id,
                'location'       => 'Data Center Jatiluhur, Jawa Barat',
                'project_type'   => 'One-Time Project',
                'description'    => 'Modernisasi spine-leaf switch Nexus 9300 100G untuk mengakomodasi ekspansi traffic 5G.',
                'start_date'     => Carbon::create(2026, 10, 1),
                'deadline'       => Carbon::create(2026, 12, 15),
                'status'         => 'Opportunity',
                'stage'          => 'Acquire',
                'process_status' => 'In Progress',
                'acquire_status' => 'Kualifikasi Kebutuhan',
                'contract_value' => 1250000000,
                'po_number'      => null,
                'created_at'     => Carbon::create(2026, 7, 20),
            ],
            [
                'name'           => 'Implementasi Next-Gen WAF & Anti-Bot Protection Tokopedia',
                'client'         => 'PT Tokopedia (GoTo Group)',
                'sales'          => $salesReps['Hendry'],
                'division_id'    => $divSec->id,
                'location'       => 'Tokopedia Tower, Jakarta Selatan',
                'project_type'   => 'One-Time Project',
                'description'    => 'Proteksi API gateway e-commerce dari serangan automated bot, web scraping, dan DDoS Layer 7.',
                'start_date'     => Carbon::create(2026, 9, 25),
                'deadline'       => Carbon::create(2026, 11, 30),
                'status'         => 'Planning',
                'stage'          => 'Design',
                'process_status' => 'In Progress',
                'acquire_status' => 'Penawaran Komersial',
                'contract_value' => 475000000,
                'po_number'      => null,
                'created_at'     => Carbon::create(2026, 6, 10),
            ],
            [
                'name'           => 'Peremajaan Campus Network WiFi 6 Gedung Baru RS Mayapada',
                'client'         => 'RS Mayapada Healthcare Group',
                'sales'          => $salesReps['Nabylla'],
                'division_id'    => $divNet->id,
                'location'       => 'Mayapada Hospital Jakarta Selatan',
                'project_type'   => 'One-Time Project',
                'description'    => 'Instalasi 40 titik Access Point WiFi 6 Aruba AP-515, switch PoE Aruba CX, dan captive portal pasien.',
                'start_date'     => Carbon::create(2026, 10, 5),
                'deadline'       => Carbon::create(2026, 11, 20),
                'status'         => 'Opportunity',
                'stage'          => 'Acquire',
                'process_status' => 'In Progress',
                'acquire_status' => 'Kualifikasi Kebutuhan',
                'contract_value' => 360000000,
                'po_number'      => null,
                'created_at'     => Carbon::create(2026, 8, 5),
            ],
            [
                'name'           => 'Pengadaan Lisensi Cyber Command & EDR Monitoring BASARNAS',
                'client'         => 'BASARNAS (Badan SAR Nasional)',
                'sales'          => $salesReps['Nelvia'],
                'division_id'    => $divSec->id,
                'location'       => 'Pusdatin Basarnas Kemayoran, Jakarta Pusat',
                'project_type'   => 'One-Time Project',
                'description'    => 'Penguatan SOC monitoring incident response dan threat hunting endpoint 200 node.',
                'start_date'     => Carbon::create(2026, 11, 1),
                'deadline'       => Carbon::create(2026, 12, 31),
                'status'         => 'Opportunity',
                'stage'          => 'Acquire',
                'process_status' => 'In Progress',
                'acquire_status' => 'Prospek Awal',
                'contract_value' => 290000000,
                'po_number'      => null,
                'created_at'     => Carbon::create(2026, 8, 28),
            ],

            // --- TAHAP: DRAFT (PROPOSAL DALAM PENYUSUNAN) ---
            [
                'name'           => 'Draft Proposal Solusi Zero Trust Network Access (ZTNA) Telkomsel',
                'client'         => 'PT Telekomunikasi Selular (Telkomsel)',
                'sales'          => $salesReps['Raiza'],
                'division_id'    => $divSec->id,
                'location'       => 'Telkomsel Smart Office, Jakarta',
                'project_type'   => 'One-Time Project',
                'description'    => 'Proposal transisi arsitektur VPN tradisional ke ZTNA berbasis identity cloud provider.',
                'start_date'     => Carbon::create(2026, 10, 15),
                'deadline'       => Carbon::create(2026, 12, 10),
                'status'         => 'Draft',
                'stage'          => 'Acquire',
                'process_status' => 'Belum Mulai',
                'acquire_status' => 'Prospek Awal',
                'contract_value' => 520000000,
                'po_number'      => null,
                'created_at'     => Carbon::create(2026, 8, 22),
            ],
            [
                'name'           => 'Draft Penawaran Dark Fiber Underground Ring Metro-E Maybank',
                'client'         => 'PT Bank Maybank Indonesia Tbk',
                'sales'          => $salesReps['Ribka'],
                'division_id'    => $divNet->id,
                'location'       => 'Maybank Tower, Senayan, Jakarta',
                'project_type'   => 'One-Time Project',
                'description'    => 'Draft estimasi biaya penggelaran kabel dark fiber ring 48 core antar data center.',
                'start_date'     => Carbon::create(2026, 11, 10),
                'deadline'       => Carbon::create(2027, 1, 15),
                'status'         => 'Draft',
                'stage'          => 'Acquire',
                'process_status' => 'Belum Mulai',
                'acquire_status' => 'Prospek Awal',
                'contract_value' => 680000000,
                'po_number'      => null,
                'created_at'     => Carbon::create(2026, 8, 30),
            ],

            // --- TAHAP: IN PROGRESS / ON PROGRESS (DEAL WON & DELIVERING) ---
            [
                'name'           => 'Hardening Firewall HA & Integrasi SIEM Bank Mandiri',
                'client'         => 'PT Bank Mandiri (Persero) Tbk',
                'sales'          => $salesReps['Sabar'],
                'division_id'    => $divSec->id,
                'location'       => 'Data Center Plaza Mandiri, Jakarta',
                'project_type'   => 'One-Time Project',
                'description'    => 'Audit keamanan, setup FortiGate 100F High Availability Active-Passive, dan forwarding alert SIEM.',
                'start_date'     => Carbon::create(2026, 7, 1),
                'deadline'       => Carbon::create(2026, 9, 30),
                'status'         => 'On Progress',
                'stage'          => 'Deliver',
                'process_status' => 'In Progress',
                'acquire_status' => 'Deal / PO Terbit',
                'contract_value' => 450000000,
                'po_number'      => 'PO/MDR/2026/07/0419',
                'created_at'     => Carbon::create(2026, 6, 25),
            ],
            [
                'name'           => 'Instalasi Backbone Fiber Optik OM4 Gedung BCA Thamrin',
                'client'         => 'PT Bank Central Asia Tbk',
                'sales'          => $salesReps['Widodo'],
                'division_id'    => $divNet->id,
                'location'       => 'Menara BCA Lt. 12-24, Jakarta Pusat',
                'project_type'   => 'One-Time Project',
                'description'    => 'Penarikan fiber optik riser 12 lantai, splicing ODF, dan testing OTDR sertifikasi Fluke.',
                'start_date'     => Carbon::create(2026, 6, 15),
                'deadline'       => Carbon::create(2026, 9, 15),
                'status'         => 'On Progress',
                'stage'          => 'Deliver',
                'process_status' => 'In Progress',
                'acquire_status' => 'Deal / PO Terbit',
                'contract_value' => 580000000,
                'po_number'      => 'PO/BCA/2026/06/0892',
                'created_at'     => Carbon::create(2026, 5, 20),
            ],
            [
                'name'           => 'Kontrak Preventive Maintenance SLA Bulanan RS Siloam',
                'client'         => 'RS Siloam Hospitals Group',
                'sales'          => $salesReps['Donny'],
                'division_id'    => $divMnt->id,
                'location'       => 'RS Siloam Kebon Jeruk, Jakarta Barat',
                'project_type'   => 'Maintenance Berkala',
                'description'    => 'Kontrak pemeliharaan jaringan 1 tahun SLA 99.9%, visit bulanan, dan on-call emergency 24/7.',
                'start_date'     => Carbon::create(2026, 1, 1),
                'deadline'       => Carbon::create(2026, 12, 31),
                'status'         => 'On Progress',
                'stage'          => 'Deliver',
                'process_status' => 'In Progress',
                'acquire_status' => 'Deal / PO Terbit',
                'contract_value' => 320000000,
                'po_number'      => 'PO/SLM/2026/01/0105',
                'created_at'     => Carbon::create(2026, 1, 5),
            ],
            [
                'name'           => 'Infrastruktur CCTV IP AI 48 Titik & NVR 64TB Gudang Logistik Lazada',
                'client'         => 'PT Lazada Express Indonesia',
                'sales'          => $salesReps['Erie'],
                'division_id'    => $divNet->id,
                'location'       => 'Kawasan Industri MM2100, Cikarang',
                'project_type'   => 'One-Time Project',
                'description'    => 'Pemasangan IP Camera 4K Hikvision, switch PoE 48 Port, dan storage server NVR redundan.',
                'start_date'     => Carbon::create(2026, 7, 10),
                'deadline'       => Carbon::create(2026, 9, 20),
                'status'         => 'On Progress',
                'stage'          => 'Deliver',
                'process_status' => 'In Progress',
                'acquire_status' => 'Deal / PO Terbit',
                'contract_value' => 650000000,
                'po_number'      => 'PO/LZD/2026/07/0771',
                'created_at'     => Carbon::create(2026, 6, 28),
            ],
            [
                'name'           => 'Dedicated Cloud Interconnect AWS DirectConnect 10G Tiket.com',
                'client'         => 'PT Global Tiket Network (Tiket.com)',
                'sales'          => $salesReps['Hendry'],
                'division_id'    => $divNet->id,
                'location'       => 'Equinix Data Center JK1, Jakarta',
                'project_type'   => 'One-Time Project',
                'description'    => 'Penyambungan cross-connect fiber optik 10Gbps dedicated link ke AWS Region Jakarta.',
                'start_date'     => Carbon::create(2026, 8, 1),
                'deadline'       => Carbon::create(2026, 9, 30),
                'status'         => 'On Progress',
                'stage'          => 'Deliver',
                'process_status' => 'In Progress',
                'acquire_status' => 'Deal / PO Terbit',
                'contract_value' => 240000000,
                'po_number'      => 'PO/TKT/2026/08/0312',
                'created_at'     => Carbon::create(2026, 7, 15),
            ],
            [
                'name'           => 'SD-WAN Deployment 300 Hub Distribusi Logistik Alfamart',
                'client'         => 'PT Sumber Alfaria Trijaya Tbk (Alfamart)',
                'sales'          => $salesReps['Nabylla'],
                'division_id'    => $divNet->id,
                'location'       => 'Head Office Alfamart & 300 DC Hub',
                'project_type'   => 'One-Time Project',
                'description'    => 'Implementasi SD-WAN interkoneksi gudang regional ke Google Cloud & Microsoft Azure.',
                'start_date'     => Carbon::create(2026, 5, 1),
                'deadline'       => Carbon::create(2026, 10, 31),
                'status'         => 'On Progress',
                'stage'          => 'Deliver',
                'process_status' => 'In Progress',
                'acquire_status' => 'Deal / PO Terbit',
                'contract_value' => 1450000000,
                'po_number'      => 'PO/ALF/2026/04/1102',
                'created_at'     => Carbon::create(2026, 4, 18),
            ],

            // --- TAHAP: PENDING / WAITING APPROVAL ---
            [
                'name'           => 'Upgrade Bandwidth & Redundant BGP Peering BNI Pusat',
                'client'         => 'PT Bank Negara Indonesia (Persero) Tbk',
                'sales'          => $salesReps['Nelvia'],
                'division_id'    => $divNet->id,
                'location'       => 'Grha BNI Lt. 15-28, Sudirman, Jakarta',
                'project_type'   => 'One-Time Project',
                'description'    => 'Penambahan modul switch core 40G dan konfigurasi BGP multihome upstream Telkom & Indosat.',
                'start_date'     => Carbon::create(2026, 8, 15),
                'deadline'       => Carbon::create(2026, 10, 15),
                'status'         => 'Pending',
                'stage'          => 'Acquire',
                'process_status' => 'In Progress',
                'acquire_status' => 'Penawaran Komersial',
                'contract_value' => 390000000,
                'po_number'      => null,
                'created_at'     => Carbon::create(2026, 7, 28),
            ],
            [
                'name'           => 'Renewal Lisensi Tahunan FortiCare & FortiGuard Shopee DC',
                'client'         => 'PT Shopee International Indonesia',
                'sales'          => $salesReps['Sabar'],
                'division_id'    => $divSec->id,
                'location'       => 'Cyber 2 Tower, Jakarta Selatan',
                'project_type'   => 'One-Time Project',
                'description'    => 'Perpanjangan lisensi 24/7 FortiCare dan UTP Protection untuk 8 cluster firewall cluster DC.',
                'start_date'     => Carbon::create(2026, 9, 1),
                'deadline'       => Carbon::create(2026, 10, 1),
                'status'         => 'Pending',
                'stage'          => 'Acquire',
                'process_status' => 'In Progress',
                'acquire_status' => 'Penawaran Komersial',
                'contract_value' => 195000000,
                'po_number'      => null,
                'created_at'     => Carbon::create(2026, 8, 10),
            ],

            // --- TAHAP: COMPLETED / CLOSED WON ---
            [
                'name'           => 'Rollout 65 Titik WiFi 6 & Captive Portal Mall Kelapa Gading',
                'client'         => 'Summarecon Mall Group',
                'sales'          => $salesReps['Widodo'],
                'division_id'    => $divNet->id,
                'location'       => 'Mall Kelapa Gading 1-5, Jakarta Utara',
                'project_type'   => 'One-Time Project',
                'description'    => 'Pemasangan 65 unit AP Aruba AP-515, gateway controller, dan captive portal pengunjung berkecepatan tinggi.',
                'start_date'     => Carbon::create(2026, 3, 1),
                'deadline'       => Carbon::create(2026, 5, 25),
                'status'         => 'Completed',
                'stage'          => 'Operate',
                'process_status' => 'Selesai',
                'acquire_status' => 'Deal / PO Terbit',
                'contract_value' => 780000000,
                'po_number'      => 'PO/SMC/2026/02/0540',
                'created_at'     => Carbon::create(2026, 2, 10),
            ],
            [
                'name'           => 'Audit Keamanan PCI-DSS & Hardening TLS 1.3 DANA Indonesia',
                'client'         => 'PT Espay Debit Indonesia Koe',
                'sales'          => $salesReps['Ribka'],
                'division_id'    => $divSec->id,
                'location'       => 'Capital Place, Gatot Subroto, Jakarta',
                'project_type'   => 'One-Time Project',
                'description'    => 'Audit kepatuhan transaksi dompet digital, penetrasi segmen data cardholder, dan sertifikasi PCI-DSS.',
                'start_date'     => Carbon::create(2026, 2, 15),
                'deadline'       => Carbon::create(2026, 4, 30),
                'status'         => 'Completed',
                'stage'          => 'Operate',
                'process_status' => 'Selesai',
                'acquire_status' => 'Deal / PO Terbit',
                'contract_value' => 310000000,
                'po_number'      => 'PO/DNA/2026/02/0211',
                'created_at'     => Carbon::create(2026, 2, 1),
            ],
            [
                'name'           => 'Revitalisasi Kabel Data Cat6A Shielded PHE Tower Pertamina',
                'client'         => 'PT Pertamina Hulu Energi',
                'sales'          => $salesReps['Donny'],
                'division_id'    => $divNet->id,
                'location'       => 'PHE Tower Lt. 11, Jakarta Selatan',
                'project_type'   => 'One-Time Project',
                'description'    => 'Re-cabling 250 node kabel Cat6A shielded Belden, re-patching 6 rack server, dan cable management.',
                'start_date'     => Carbon::create(2026, 1, 10),
                'deadline'       => Carbon::create(2026, 3, 20),
                'status'         => 'Completed',
                'stage'          => 'Operate',
                'process_status' => 'Selesai',
                'acquire_status' => 'Deal / PO Terbit',
                'contract_value' => 420000000,
                'po_number'      => 'PO/PHE/2026/01/0088',
                'created_at'     => Carbon::create(2026, 1, 8),
            ],
            [
                'name'           => 'Implementasi WPA3 Enterprise & Radius 802.1X Menara BTPN',
                'client'         => 'PT Bank BTPN Tbk',
                'sales'          => $salesReps['Hendry'],
                'division_id'    => $divNet->id,
                'location'       => 'CBD Mega Kuningan, Jakarta Selatan',
                'project_type'   => 'One-Time Project',
                'description'    => 'Integrasi WiFi corporate ke Microsoft Active Directory dan sertifikat autentikasi 802.1X.',
                'start_date'     => Carbon::create(2026, 4, 1),
                'deadline'       => Carbon::create(2026, 6, 10),
                'status'         => 'Completed',
                'stage'          => 'Operate',
                'process_status' => 'Selesai',
                'acquire_status' => 'Deal / PO Terbit',
                'contract_value' => 265000000,
                'po_number'      => 'PO/BTPN/2026/03/0914',
                'created_at'     => Carbon::create(2026, 3, 15),
            ],
        ];

        $sampleFile = 'proposals/EcW3J8dl2cSjn5zLG2BzqThC5gjZmueZUIPFW5X5.pdf';
        $mandaysArr = [8, 10, 12, 14, 15, 18, 20];

        foreach ($salesProjects as $idx => $proj) {
            $salesInfo = $proj['sales'];
            unset($proj['sales']);

            $isWon = in_array($proj['status'] ?? '', ['On Progress', 'Completed', 'Closed Won']);
            $mDays = $isWon ? ($mandaysArr[$idx % count($mandaysArr)]) : null;

            $projData = array_merge([
                'mandays'         => $mDays,
                'proposal_file'   => $isWon ? $sampleFile : null,
                'presales_status' => $isWon ? 'Submitted' : 'Pending',
                'proposal_notes'  => $isWon ? "Ruang lingkup teknis (SOW) telah selesai disusun oleh tim Presales Engineer: Alokasi {$mDays} Mandays Engineer." : null,
            ], $proj, [
                'sales_name' => $salesInfo['name'],
                'created_by' => $salesInfo['user_id'],
                'pm_id'      => $pmRizki?->id ?? $salesInfo['user_id'],
            ]);

            $p = Project::updateOrCreate(
                ['name' => $projData['name']],
                $projData
            );

            // Set explicit created_at date so that the monthly revenue chart reflects monthly distribution throughout the year
            if (isset($proj['created_at'])) {
                $p->created_at = $proj['created_at'];
                $p->saveQuietly();
            }
        }
    }
}

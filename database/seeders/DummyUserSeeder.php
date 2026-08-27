<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Division;
use App\Models\Team;
use App\Models\Project;
use App\Models\Task;
use App\Models\Schedule;
use App\Models\Attendance;
use App\Models\Timesheet;
use Spatie\Permission\Models\Role;

class DummyUserSeeder extends Seeder
{
    public function run(): void
    {
        // 0. Bersihkan akun dummy lama jika ada
        User::whereIn('name', ['Rangga Saputra', 'Bayu Kusuma', 'Rani Oktaviani', 'Bambang Wicaksono', 'Fajar Ramadhan', 'Dimas Aditya'])
            ->orWhere('email', 'like', '%@ipnetwork.co.id')
            ->delete();

        // 1. Pastikan roles tersedia
        $roles = [
            'Direktur',
            'HD / Direktur',
            'Group Leader',
            'Lead Divisi',
            'Team Leader',
            'Lead Maintenance',
            'Lead Engineer',
            'Engineer',
            'Maintenance',
            'Engineer L1',
            'Engineer L2',
        ];
        foreach ($roles as $r) {
            Role::firstOrCreate(['name' => $r]);
        }

        // 2. Divisi (Network, Security, Maintenance)
        $divNet = Division::firstOrCreate(
            ['name' => 'Divisi Network'],
            ['code' => 'NET', 'description' => 'Divisi Infrastruktur Jaringan, Routing, Switching & Fiber Optik']
        );
        $divSec = Division::firstOrCreate(
            ['name' => 'Divisi Security'],
            ['code' => 'SEC', 'description' => 'Divisi Keamanan Jaringan & Cyber Security Protection']
        );
        $divMnt = Division::firstOrCreate(
            ['name' => 'Divisi Maintenance'],
            ['code' => 'MNT', 'description' => 'Divisi Pemeliharaan Rutin, Preventive & Corrective Maintenance SLA']
        );

        // 3. Tim
        $teamNet = Team::firstOrCreate(
            ['name' => 'Tim Network', 'division_id' => $divNet->id],
            ['description' => 'Tim Operasional, Deployment & Instalasi Jaringan Lapangan']
        );
        $teamSec = Team::firstOrCreate(
            ['name' => 'Tim Security', 'division_id' => $divSec->id],
            ['description' => 'Tim Monitoring, Hardening & Security Implementation']
        );
        $teamMnt = Team::firstOrCreate(
            ['name' => 'Tim Maintenance', 'division_id' => $divMnt->id],
            ['description' => 'Tim Preventive Maintenance, SLA Support & Field Troubleshooting']
        );

        // ============================================================
        // 4. SEED USERS RESMI IP-NET (MANAGERIAL & 3 DIVISI)
        // ============================================================
        $direktur = User::updateOrCreate(
            ['email' => 'hariyadi@ipnetsolusindo.com'],
            [
                'name'        => 'Hariyadi',
                'password'    => Hash::make('password123'),
                'phone'       => '08111000001',
                'position'    => 'Direktur Utama',
                'status'      => 'Active',
                'division_id' => null,
                'team_id'     => null,
                'level'       => null,
            ]
        );
        $direktur->syncRoles(['Direktur']);

        $gl = User::updateOrCreate(
            ['email' => 'susanto@ipnetsolusindo.com'],
            [
                'name'        => 'Susanto Djaya',
                'password'    => Hash::make('password123'),
                'phone'       => '08111000002',
                'position'    => 'Group Leader',
                'status'      => 'Active',
                'division_id' => null,
                'team_id'     => null,
                'level'       => null,
            ]
        );
        $gl->syncRoles(['Group Leader']);

        // --- DIVISI NETWORK ---
        $tlNet = User::updateOrCreate(
            ['email' => 'nugraha@ipnetsolusindo.com'],
            [
                'name'        => 'Nugraha Pratama',
                'password'    => Hash::make('password123'),
                'phone'       => '08111000003',
                'position'    => 'Network Leader',
                'status'      => 'Active',
                'division_id' => $divNet->id,
                'team_id'     => $teamNet->id,
                'level'       => null,
            ]
        );
        $tlNet->syncRoles(['Team Leader']);
        $teamNet->update(['leader_id' => $tlNet->id]);

        $engNet1 = User::updateOrCreate(
            ['email' => 'rorik@ipnetsolusindo.com'],
            [
                'name'        => 'Rorik',
                'password'    => Hash::make('password123'),
                'phone'       => '08111000005',
                'position'    => 'L1 Network Engineer',
                'status'      => 'Active',
                'division_id' => $divNet->id,
                'team_id'     => $teamNet->id,
                'level'       => 'L1',
            ]
        );
        $engNet1->syncRoles(['Engineer']);

        $engNet2 = User::updateOrCreate(
            ['email' => 'shiamsyah@ipnetsolusindo.com'],
            [
                'name'        => 'Shiamsyah Azis',
                'password'    => Hash::make('password123'),
                'phone'       => '08111000006',
                'position'    => 'L1 Network Engineer',
                'status'      => 'Active',
                'division_id' => $divNet->id,
                'team_id'     => $teamNet->id,
                'level'       => 'L1',
            ]
        );
        $engNet2->syncRoles(['Engineer']);

        $engNet3 = User::updateOrCreate(
            ['email' => 'dedy@ipnetsolusindo.com'],
            [
                'name'        => 'Dedy Suryana',
                'password'    => Hash::make('password123'),
                'phone'       => '08111000007',
                'position'    => 'L2 Network Engineer',
                'status'      => 'Active',
                'division_id' => $divNet->id,
                'team_id'     => $teamNet->id,
                'level'       => 'L2',
            ]
        );
        $engNet3->syncRoles(['Engineer']);

        $engNet4 = User::updateOrCreate(
            ['email' => 'syaiful@ipnetsolusindo.com'],
            [
                'name'        => 'Syaiful Amin',
                'password'    => Hash::make('password123'),
                'phone'       => '08111000008',
                'position'    => 'L2 Network Engineer',
                'status'      => 'Active',
                'division_id' => $divNet->id,
                'team_id'     => $teamNet->id,
                'level'       => 'L2',
            ]
        );
        $engNet4->syncRoles(['Engineer']);

        // --- DIVISI SECURITY ---
        $tlSec = User::updateOrCreate(
            ['email' => 'ignatius@ipnetsolusindo.com'],
            [
                'name'        => 'Ignatius Rizky',
                'password'    => Hash::make('password123'),
                'phone'       => '08111000004',
                'position'    => 'Security Leader',
                'status'      => 'Active',
                'division_id' => $divSec->id,
                'team_id'     => $teamSec->id,
                'level'       => null,
            ]
        );
        $tlSec->syncRoles(['Team Leader']);
        $teamSec->update(['leader_id' => $tlSec->id]);

        $engSec1 = User::updateOrCreate(
            ['email' => 'eka@ipnetsolusindo.com'],
            [
                'name'        => 'Eka Kurnia',
                'password'    => Hash::make('password123'),
                'phone'       => '08111000009',
                'position'    => 'L1 Security Engineer',
                'status'      => 'Active',
                'division_id' => $divSec->id,
                'team_id'     => $teamSec->id,
                'level'       => 'L1',
            ]
        );
        $engSec1->syncRoles(['Engineer']);

        // --- DIVISI MAINTENANCE (DORIS, MARIO, ERIS) ---
        $tlMnt = User::updateOrCreate(
            ['email' => 'doris@ipnetsolusindo.com'],
            [
                'name'        => 'Doris',
                'password'    => Hash::make('password123'),
                'phone'       => '08111000010',
                'position'    => 'Lead Maintenance',
                'status'      => 'Active',
                'division_id' => $divMnt->id,
                'team_id'     => $teamMnt->id,
                'level'       => null,
            ]
        );
        $tlMnt->syncRoles(['Lead Maintenance']);
        $teamMnt->update(['leader_id' => $tlMnt->id]);

        $engMnt1 = User::updateOrCreate(
            ['email' => 'mario@ipnetsolusindo.com'],
            [
                'name'        => 'Mario',
                'password'    => Hash::make('password123'),
                'phone'       => '08111000011',
                'position'    => 'Maintenance Staff',
                'status'      => 'Active',
                'division_id' => $divMnt->id,
                'team_id'     => $teamMnt->id,
                'level'       => null,
            ]
        );
        $engMnt1->syncRoles(['Maintenance']);

        $engMnt2 = User::updateOrCreate(
            ['email' => 'eris@ipnetsolusindo.com'],
            [
                'name'        => 'Eris',
                'password'    => Hash::make('password123'),
                'phone'       => '08111000012',
                'position'    => 'Maintenance Staff',
                'status'      => 'Active',
                'division_id' => $divMnt->id,
                'team_id'     => $teamMnt->id,
                'level'       => null,
            ]
        );
        $engMnt2->syncRoles(['Maintenance']);

        // ============================================================
        // 5. SEED 20 PROYEK LENGKAP & SEMUA MEMILIKI SALES RESMI
        // ============================================================
        $projectsData = [
            [
                'name'           => 'Upgrade Jaringan WAN & SD-WAN Adira Finance',
                'client'         => 'PT Adira Dinamika Multi Finance Tbk',
                'sales_name'     => 'Riko Wijaya',
                'project_type'   => 'One-Time Project',
                'visit_schedule' => 'None',
                'location'       => 'Kantor Pusat Adira & 15 Cabang',
                'start_date'     => now()->subDays(12),
                'deadline'       => now()->addDays(18),
                'status'         => 'On Progress',
                'description'    => 'Implementasi SD-WAN, upgrade bandwidth router utama, dan konfigurasi failover link.',
                'created_by'     => $tlNet->id,
            ],
            [
                'name'           => 'Hardening Firewall & SIEM Monitoring Bank Mandiri',
                'client'         => 'PT Bank Mandiri (Persero) Tbk',
                'sales_name'     => 'Anita Lestari',
                'project_type'   => 'One-Time Project',
                'visit_schedule' => 'None',
                'location'       => 'Data Center Plaza Mandiri, Jakarta',
                'start_date'     => now()->subDays(10),
                'deadline'       => now()->addDays(20),
                'status'         => 'On Progress',
                'description'    => 'Audit keamanan siber, konfigurasi Fortigate Firewall HA, dan integrasi alert SIEM.',
                'created_by'     => $tlSec->id,
            ],
            [
                'name'           => 'Instalasi Fiber Optik Backbone - Gedung BCA Thamrin',
                'client'         => 'PT Bank Central Asia Tbk',
                'sales_name'     => 'Hendra Gunawan',
                'project_type'   => 'One-Time Project',
                'visit_schedule' => 'None',
                'location'       => 'Menara BCA Lt. 12-24, Jakarta Pusat',
                'start_date'     => now()->subDays(20),
                'deadline'       => now()->addDays(10),
                'status'         => 'On Progress',
                'description'    => 'Pemasangan jalur fiber optik backbone multimode OM4 untuk 12 lantai perkantoran.',
                'created_by'     => $tlNet->id,
            ],
            [
                'name'           => 'Preventive Maintenance SLA Bulanan Jaringan - RS Siloam Kebon Jeruk',
                'client'         => 'RS Siloam Hospitals Group',
                'sales_name'     => 'Riko Wijaya',
                'project_type'   => 'Maintenance Berkala',
                'visit_schedule' => 'Bulanan (Monthly)',
                'location'       => 'RS Siloam Kebon Jeruk, Jakarta Barat',
                'start_date'     => now()->subDays(15),
                'deadline'       => now()->addDays(75),
                'status'         => 'On Progress',
                'description'    => 'Kontrak pemeliharaan berkala SLA 99.9%, inspeksi bulanan switch core, dan restrukturisasi segmentasi VLAN medis.',
                'created_by'     => $tlMnt->id,
            ],
            [
                'name'           => 'Upgrade Bandwidth & WiFi 6 - Mall Kelapa Gading',
                'client'         => 'Summarecon Mall Group',
                'sales_name'     => 'Hendra Gunawan',
                'project_type'   => 'One-Time Project',
                'visit_schedule' => 'None',
                'location'       => 'Mall Kelapa Gading 1-5, Jakarta Utara',
                'start_date'     => now()->subDays(40),
                'deadline'       => now()->subDays(5),
                'status'         => 'Completed',
                'description'    => 'Pemasangan 65 titik Access Point WiFi 6 Aruba dan gateway captive portal pengunjung.',
                'created_by'     => $tlNet->id,
            ],
            [
                'name'           => 'Rollout WiFi Corporate & Radius Server - Menara BTPN',
                'client'         => 'PT Bank BTPN Tbk',
                'sales_name'     => 'Anita Lestari',
                'project_type'   => 'One-Time Project',
                'visit_schedule' => 'None',
                'location'       => 'CBD Mega Kuningan, Jakarta Selatan',
                'start_date'     => now()->subDays(8),
                'deadline'       => now()->addDays(22),
                'status'         => 'On Progress',
                'description'    => 'Implementasi WPA3 Enterprise, integrasi Active Directory, dan autentikasi 802.1X.',
                'created_by'     => $tlNet->id,
            ],
            [
                'name'           => 'Security Vulnerability Assessment - Shopee Data Center',
                'client'         => 'PT Shopee International Indonesia',
                'sales_name'     => 'Riko Wijaya',
                'project_type'   => 'One-Time Project',
                'visit_schedule' => 'None',
                'location'       => 'Tersentrik Cyber 2 Tower, Jakarta',
                'start_date'     => now()->subDays(6),
                'deadline'       => now()->addDays(24),
                'status'         => 'On Progress',
                'description'    => 'Penetration testing internal segment, port scanning, dan review policy WAF.',
                'created_by'     => $tlSec->id,
            ],
            [
                'name'           => 'CCTV IP & Network Infrastructure - Gudang Logistik Lazada',
                'client'         => 'PT Lazada Express Indonesia',
                'sales_name'     => 'Hendra Gunawan',
                'project_type'   => 'One-Time Project',
                'visit_schedule' => 'None',
                'location'       => 'Kawasan Industri MM2100, Cikarang',
                'start_date'     => now()->subDays(35),
                'deadline'       => now()->subDays(2),
                'status'         => 'Completed',
                'description'    => 'Instalasi 48 IP Camera Hikvision 4K, PoE switch manageable, dan storage NVR 64TB.',
                'created_by'     => $tlNet->id,
            ],
            [
                'name'           => 'Kontrak Pemeliharaan Rutin Triwulanan Firewall & Switch - BCA Thamrin',
                'client'         => 'PT Bank Central Asia Tbk',
                'sales_name'     => 'Anita Lestari',
                'project_type'   => 'Maintenance Berkala',
                'visit_schedule' => 'Triwulanan (Quarterly)',
                'location'       => 'Menara BCA Thamrin Lt. 12, Jakarta Pusat',
                'start_date'     => now()->subDays(10),
                'deadline'       => now()->addDays(80),
                'status'         => 'On Progress',
                'description'    => 'Jadwal visit berkala triwulanan pembersihan rack, audit firmware switch Cisco, dan health check firewall.',
                'created_by'     => $tlMnt->id,
            ],
            [
                'name'           => 'Managed Services & Maintenance Mingguan - Mall Kelapa Gading',
                'client'         => 'Summarecon Mall Group',
                'sales_name'     => 'Hendra Gunawan',
                'project_type'   => 'Managed Service',
                'visit_schedule' => 'Mingguan (Weekly)',
                'location'       => 'Mall Kelapa Gading 1-5, Jakarta Utara',
                'start_date'     => now()->subDays(7),
                'deadline'       => now()->addDays(53),
                'status'         => 'On Progress',
                'description'    => 'Layanan on-site support mingguan untuk pengecekan throughput WiFi publik dan gateway captive portal.',
                'created_by'     => $tlMnt->id,
            ],
            [
                'name'           => 'Maintenance & SLA Support WiFi Corporate - Menara BTPN',
                'client'         => 'PT Bank BTPN Tbk',
                'sales_name'     => 'Anita Lestari',
                'project_type'   => 'Maintenance Berkala',
                'visit_schedule' => 'Bulanan (Monthly)',
                'location'       => 'CBD Mega Kuningan, Jakarta Selatan',
                'start_date'     => now()->subDays(5),
                'deadline'       => now()->addDays(85),
                'status'         => 'On Progress',
                'description'    => 'Visit bulanan pengecekan log autentikasi 802.1X FreeRadius dan optimasi sinyal AP Aruba.',
                'created_by'     => $tlMnt->id,
            ],
            [
                'name'           => 'Inspeksi & Pemeliharaan Berkala CCTV NVR - Gudang Lazada',
                'client'         => 'PT Lazada Express Indonesia',
                'sales_name'     => 'Hendra Gunawan',
                'project_type'   => 'Maintenance Berkala',
                'visit_schedule' => 'Bulanan (Monthly)',
                'location'       => 'Kawasan Industri MM2100, Cikarang',
                'start_date'     => now()->subDays(3),
                'deadline'       => now()->addDays(87),
                'status'         => 'On Progress',
                'description'    => 'Pengecekan rutin storage NVR, angle kamera CCTV, dan cleaning debu lensa di area warehouse.',
                'created_by'     => $tlMnt->id,
            ],
            [
                'name'           => 'Redundant BGP Peering & Routing - Data Center Telkomsigma',
                'client'         => 'PT Sigma Cipta Caraka (Telkomsigma)',
                'sales_name'     => 'Riko Wijaya',
                'project_type'   => 'One-Time Project',
                'visit_schedule' => 'None',
                'location'       => 'Serpong Data Center, Tangerang Selatan',
                'start_date'     => now()->addDays(3),
                'deadline'       => now()->addDays(35),
                'status'         => 'Planning',
                'description'    => 'Konfigurasi ASN BGP multihoming dengan upstream Telkom & Indosat.',
                'created_by'     => $tlNet->id,
            ],
            [
                'name'           => 'Disaster Recovery Network Setup - Astra International',
                'client'         => 'PT Astra International Tbk',
                'sales_name'     => 'Anita Lestari',
                'project_type'   => 'One-Time Project',
                'visit_schedule' => 'None',
                'location'       => 'Menara Astra & Cibitung DR Site',
                'start_date'     => now()->addDays(7),
                'deadline'       => now()->addDays(40),
                'status'         => 'Planning',
                'description'    => 'Penyusunan link cadangan dark fiber dan simulasi failover database core.',
                'created_by'     => $tlSec->id,
            ],
            [
                'name'           => 'Deployment Core Switch Nexus 9K - Data Center Indosat',
                'client'         => 'PT Indosat Ooredoo Hutchison Tbk',
                'sales_name'     => 'Hendra Gunawan',
                'project_type'   => 'One-Time Project',
                'visit_schedule' => 'None',
                'location'       => 'Data Center Jatiluhur, Jawa Barat',
                'start_date'     => now()->subDays(14),
                'deadline'       => now()->addDays(15),
                'status'         => 'On Progress',
                'description'    => 'Instalasi chassis switch spine-leaf Cisco Nexus 9000 untuk high-density traffic.',
                'created_by'     => $tlNet->id,
            ],
            [
                'name'           => 'WAF (Web Application Firewall) Implementation - Tokopedia',
                'client'         => 'PT Tokopedia (GoTo Group)',
                'sales_name'     => 'Maya Safitri',
                'project_type'   => 'One-Time Project',
                'visit_schedule' => 'None',
                'location'       => 'Tokopedia Tower, Jakarta Selatan',
                'start_date'     => now()->subDays(9),
                'deadline'       => now()->addDays(16),
                'status'         => 'On Progress',
                'description'    => 'Implementasi Cloudflare Enterprise WAF dan mitigasi serangan DDoS Layer 7.',
                'created_by'     => $tlSec->id,
            ],
            [
                'name'           => 'Migrasi Cloud Interconnect AWS DirectConnect - Tiket.com',
                'client'         => 'PT Global Tiket Network',
                'sales_name'     => 'Riko Wijaya',
                'project_type'   => 'One-Time Project',
                'visit_schedule' => 'None',
                'location'       => 'Equinix Data Center JK1, Jakarta',
                'start_date'     => now()->subDays(18),
                'deadline'       => now()->addDays(8),
                'status'         => 'On Progress',
                'description'    => 'Penyambungan cross-connect link 10Gbps dedicated ke AWS Region Jakarta.',
                'created_by'     => $tlNet->id,
            ],
            [
                'name'           => 'Implementasi Zero Trust Network Access (ZTNA) - Telkomsel',
                'client'         => 'PT Telekomunikasi Selular',
                'sales_name'     => 'Maya Safitri',
                'project_type'   => 'One-Time Project',
                'visit_schedule' => 'None',
                'location'       => 'Telkomsel Smart Office, Jakarta',
                'start_date'     => now()->subDays(5),
                'deadline'       => now()->addDays(28),
                'status'         => 'On Progress',
                'description'    => 'Penggantian legacy VPN dengan arsitektur ZTNA berbasis identity provider.',
                'created_by'     => $tlSec->id,
            ],
            [
                'name'           => 'Audit Kepatuhan PCI-DSS & Endpoint Security - DANA Indonesia',
                'client'         => 'PT Espay Debit Indonesia Koe',
                'sales_name'     => 'Anita Lestari',
                'project_type'   => 'One-Time Project',
                'visit_schedule' => 'None',
                'location'       => 'Capital Place, Gatot Subroto, Jakarta',
                'start_date'     => now()->subDays(30),
                'deadline'       => now()->subDays(1),
                'status'         => 'Completed',
                'description'    => 'Audit jaringan transaksi pembayaran e-wallet dan hardening enkripsi TLS 1.3.',
                'created_by'     => $tlSec->id,
            ],
            [
                'name'           => 'Konektivitas Cabang Multi-Cloud - Alfamart Head Office',
                'client'         => 'PT Sumber Alfaria Trijaya Tbk',
                'sales_name'     => 'Hendra Gunawan',
                'project_type'   => 'One-Time Project',
                'visit_schedule' => 'None',
                'location'       => 'Head Office Alfamart, Alam Sutera, Tangerang',
                'start_date'     => now()->subDays(11),
                'deadline'       => now()->addDays(19),
                'status'         => 'On Progress',
                'description'    => 'SD-WAN interkoneksi 300 hub distribusi ke Google Cloud & Azure.',
                'created_by'     => $tlNet->id,
            ],
            [
                'name'           => 'Monitoring Jaringan APM & Network Tapping - OVO',
                'client'         => 'PT Visionet Internasional',
                'sales_name'     => 'Riko Wijaya',
                'project_type'   => 'One-Time Project',
                'visit_schedule' => 'None',
                'location'       => 'Lippo Kuningan, Jakarta Selatan',
                'start_date'     => now()->subDays(7),
                'deadline'       => now()->addDays(23),
                'status'         => 'On Progress',
                'description'    => 'Pemasangan network packet broker Gigamon dan integrasi Dynatrace APM.',
                'created_by'     => $tlNet->id,
            ],
            [
                'name'           => 'Pemasangan Dark Fiber Ring Metro-E - Maybank Tower',
                'client'         => 'PT Bank Maybank Indonesia Tbk',
                'sales_name'     => 'Anita Lestari',
                'project_type'   => 'One-Time Project',
                'visit_schedule' => 'None',
                'location'       => 'Maybank Tower, Senayan, Jakarta',
                'start_date'     => now()->addDays(5),
                'deadline'       => now()->addDays(38),
                'status'         => 'Planning',
                'description'    => 'Pembangunan ring proteksi fiber optik underground 48 core.',
                'created_by'     => $tlNet->id,
            ],
            [
                'name'           => 'Penetration Testing & Red Teaming - Blibli Data Center',
                'client'         => 'PT Global Digital Niaga Tbk',
                'sales_name'     => 'Maya Safitri',
                'project_type'   => 'One-Time Project',
                'visit_schedule' => 'None',
                'location'       => 'KS Tubun, Slipi, Jakarta Barat',
                'start_date'     => now()->subDays(8),
                'deadline'       => now()->addDays(14),
                'status'         => 'On Progress',
                'description'    => 'Simulasi serangan Advanced Persistent Threat (APT) dan pengujian SIEM detection.',
                'created_by'     => $tlSec->id,
            ],
            [
                'name'           => 'Revitalisasi Kabel Data Cat6A & Rack - Pertamina Hulu Energi',
                'client'         => 'PT Pertamina Hulu Energi',
                'sales_name'     => 'Hendra Gunawan',
                'project_type'   => 'One-Time Project',
                'visit_schedule' => 'None',
                'location'       => 'PHE Tower, Pasar Minggu, Jakarta Selatan',
                'start_date'     => now()->subDays(50),
                'deadline'       => now()->subDays(10),
                'status'         => 'Completed',
                'description'    => 'Re-cabling 250 node kabel Cat6A shielded dan penataan ulang 6 rack server.',
                'created_by'     => $tlNet->id,
            ],
            [
                'name'           => 'Infrastruktur Jaringan Core & Data Center - BNI Pusat',
                'client'         => 'PT Bank Negara Indonesia (Persero) Tbk',
                'sales_name'     => 'Riko Wijaya',
                'project_type'   => 'One-Time Project',
                'visit_schedule' => 'None',
                'location'       => 'Grha BNI Lt. 15-28, Sudirman, Jakarta',
                'start_date'     => now()->subDays(15),
                'deadline'       => now()->addDays(30),
                'status'         => 'On Progress',
                'description'    => 'Penggelaran switch core redundansi dan routing BGP multihome data center BNI.',
                'created_by'     => $tlNet->id,
            ],
        ];

        $projects = [];
        foreach ($projectsData as $pData) {
            $p = Project::updateOrCreate(
                ['name' => $pData['name']],
                $pData
            );
            $projects[] = $p;
        }

        // Safety guarantee: pastikan jika ada sisa data lama yang sales_name-nya NULL atau kosong, langsung diisi default
        Project::whereNull('sales_name')
            ->orWhere('sales_name', '')
            ->update([
                'sales_name'     => 'Riko Wijaya',
                'project_type'   => 'One-Time Project',
                'visit_schedule' => 'None',
            ]);

        // ============================================================
        // 6. SEED TASKS (NETWORK, SECURITY & MAINTENANCE)
        // ============================================================
        $tasksData = [
            // --- DEDY SURYANA (L2 Network) ---
            [
                'title'       => 'Konfigurasi BGP Routing & Inter-VLAN Gateway Adira',
                'project_id'  => $projects[0]->id,
                'engineer_id' => $engNet3->id,
                'priority'    => 'High',
                'deadline'    => now()->addDays(1),
                'progress'    => 85,
                'status'      => 'In Progress',
                'description' => 'Setting peering BGP ke provider ISP utama dan backup dengan auto failover.',
                'created_by'  => $tlNet->id,
            ],
            [
                'title'       => 'Optimasi VPN IPsec Tunnel Antar Cabang Adira Finance',
                'project_id'  => $projects[0]->id,
                'engineer_id' => $engNet3->id,
                'priority'    => 'High',
                'deadline'    => now()->addDays(3),
                'progress'    => 50,
                'status'      => 'In Progress',
                'description' => 'Penyusunan route policy dan enkripsi AES-256 pada VPN tunnel 15 cabang.',
                'created_by'  => $tlNet->id,
            ],
            [
                'title'       => 'Setup Radius Server 802.1X Menara BTPN',
                'project_id'  => $projects[5]->id,
                'engineer_id' => $engNet3->id,
                'priority'    => 'High',
                'deadline'    => now()->addDays(8),
                'progress'    => 30,
                'status'      => 'In Progress',
                'description' => 'Integrasi FreeRadius ke Active Directory Windows Server BTPN.',
                'created_by'  => $tlNet->id,
            ],

            // --- RORIK (L1 Network) ---
            [
                'title'       => 'Tarik Kabel Fiber Optik Lantai 12-16 Gedung BCA',
                'project_id'  => $projects[2]->id,
                'engineer_id' => $engNet1->id,
                'priority'    => 'High',
                'deadline'    => now()->addDays(1),
                'progress'    => 80,
                'status'      => 'In Progress',
                'description' => 'Penarikan kabel fiber optik riser backbone vertikal lantai 12 sampai 16.',
                'created_by'  => $tlNet->id,
            ],
            [
                'title'       => 'Splicing & Terminasi Fiber Optik ODF Ruang Server BCA',
                'project_id'  => $projects[2]->id,
                'engineer_id' => $engNet1->id,
                'priority'    => 'High',
                'deadline'    => now()->addDays(3),
                'progress'    => 45,
                'status'      => 'In Progress',
                'description' => 'Penyambungan core fiber menggunakan fusion splicer dan penataan tray.',
                'created_by'  => $tlNet->id,
            ],

            // --- EKA KURNIA (L1 Security) ---
            [
                'title'       => 'Audit Rule Firewall & Port Filtering Bank Mandiri',
                'project_id'  => $projects[1]->id,
                'engineer_id' => $engSec1->id,
                'priority'    => 'High',
                'deadline'    => now()->addDays(2),
                'progress'    => 75,
                'status'      => 'In Progress',
                'description' => 'Review rule akses inbound/outbound dan penutupan port non-standar.',
                'created_by'  => $tlSec->id,
            ],
            [
                'title'       => 'Internal Vulnerability Scanning Shopee DC',
                'project_id'  => $projects[6]->id,
                'engineer_id' => $engSec1->id,
                'priority'    => 'High',
                'deadline'    => now()->addDays(3),
                'progress'    => 50,
                'status'      => 'In Progress',
                'description' => 'Scanning menggunakan Nessus Vulnerability Scanner pada subnet server.',
                'created_by'  => $tlSec->id,
            ],

            // --- SHIAMSYAH AZIS (L1 Network) ---
            [
                'title'       => 'Instalasi & Mounting Router Mikrotik Baru Adira',
                'project_id'  => $projects[0]->id,
                'engineer_id' => $engNet2->id,
                'priority'    => 'Medium',
                'deadline'    => now()->addDays(2),
                'progress'    => 55,
                'status'      => 'In Progress',
                'description' => 'Pemasangan router rackmount CCR2004 dan pengetesan redundansi power supply.',
                'created_by'  => $tlNet->id,
            ],
            [
                'title'       => 'Pemasangan Access Point WiFi Menara BTPN Lantai 8-10',
                'project_id'  => $projects[5]->id,
                'engineer_id' => $engNet2->id,
                'priority'    => 'High',
                'deadline'    => now()->addDays(3),
                'progress'    => 40,
                'status'      => 'In Progress',
                'description' => 'Mounting ceiling AP Aruba dan penarikan kabel drop LAN PoE.',
                'created_by'  => $tlNet->id,
            ],

            // --- SYAIFUL AMIN (L2 Network) ---
            [
                'title'       => 'Stress Test Failover SD-WAN Dual Provider Adira',
                'project_id'  => $projects[0]->id,
                'engineer_id' => $engNet4->id,
                'priority'    => 'High',
                'deadline'    => now()->addDays(2),
                'progress'    => 70,
                'status'      => 'In Progress',
                'description' => 'Simulasi packet loss, latency spike, dan jitter testing pada controller SD-WAN.',
                'created_by'  => $tlNet->id,
            ],
            [
                'title'       => 'Testing Throughput & OTDR Link Fiber Optik BCA',
                'project_id'  => $projects[2]->id,
                'engineer_id' => $engNet4->id,
                'priority'    => 'High',
                'deadline'    => now()->addDays(4),
                'progress'    => 35,
                'status'      => 'In Progress',
                'description' => 'Pengukuran redaman dB loss menggunakan alat ukur OTDR EXFO.',
                'created_by'  => $tlNet->id,
            ],

            // ============================================================
            // --- TIM MAINTENANCE TASKS ---
            // ============================================================
            // DORIS (Lead Maintenance - Supervisi SLA & Kontrak)
            [
                'title'       => 'Supervisi Jadwal Kunjungan SLA Bulanan RS Siloam',
                'project_id'  => $projects[3]->id,
                'engineer_id' => $tlMnt->id,
                'priority'    => 'High',
                'deadline'    => now()->addDays(2),
                'progress'    => 60,
                'status'      => 'In Progress',
                'description' => 'Supervisi checklist preventive maintenance switch core dan review log insiden medis.',
                'created_by'  => $tlMnt->id,
            ],
            [
                'title'       => 'Evaluasi Laporan Berkala & Berita Acara Maintenance BCA',
                'project_id'  => $projects[8]->id,
                'engineer_id' => $tlMnt->id,
                'priority'    => 'Medium',
                'deadline'    => now()->addDays(5),
                'progress'    => 20,
                'status'      => 'In Progress',
                'description' => 'Penyusunan summary performa jaringan triwulanan dan penyerahan BAST pemeliharaan.',
                'created_by'  => $tlMnt->id,
            ],

            // MARIO (L1 Maintenance Engineer)
            [
                'title'       => 'Inspeksi Fisik & Cleaning Debu Rack Server RS Siloam',
                'project_id'  => $projects[3]->id,
                'engineer_id' => $engMnt1->id,
                'priority'    => 'Medium',
                'deadline'    => now()->addDays(1),
                'progress'    => 80,
                'status'      => 'In Progress',
                'description' => 'Pembersihan filter fan rack, pengecekan suhu ruang server, dan pelabelan kabel yang kendor.',
                'created_by'  => $tlMnt->id,
            ],
            [
                'title'       => 'Testing Sinyal & Heatmap Survey Berkala Mall Kelapa Gading',
                'project_id'  => $projects[9]->id,
                'engineer_id' => $engMnt1->id,
                'priority'    => 'High',
                'deadline'    => now()->addDays(3),
                'progress'    => 40,
                'status'      => 'In Progress',
                'description' => 'Survey sinyal WiFi di 5 atrium mall, verifikasi kecepatan bandwidth publik min. 20Mbps.',
                'created_by'  => $tlMnt->id,
            ],
            [
                'title'       => 'Pemeriksaan Angle & Pembersihan Lensa 48 CCTV Lazada',
                'project_id'  => $projects[11]->id,
                'engineer_id' => $engMnt1->id,
                'priority'    => 'Low',
                'deadline'    => now()->addDays(4),
                'progress'    => 25,
                'status'      => 'In Progress',
                'description' => 'Pembersihan debu pada dome kamera gudang dan verifikasi kualitas rekaman malam hari.',
                'created_by'  => $tlMnt->id,
            ],
            [
                'title'       => 'Penggantian Patch Cord LAN Cat6 Rusak RS Siloam',
                'project_id'  => $projects[3]->id,
                'engineer_id' => $engMnt1->id,
                'priority'    => 'Medium',
                'deadline'    => now()->subDays(2),
                'progress'    => 100,
                'status'      => 'Completed',
                'description' => 'Pergantian 6 patch cord yang mengalami error CRC pada switch distribusi lantai 3.',
                'created_by'  => $tlMnt->id,
            ],

            // ERIS (L2 Maintenance Engineer)
            [
                'title'       => 'Audit Log Error & Health Check Switch Core RS Siloam',
                'project_id'  => $projects[3]->id,
                'engineer_id' => $engMnt2->id,
                'priority'    => 'High',
                'deadline'    => now()->addDays(2),
                'progress'    => 70,
                'status'      => 'In Progress',
                'description' => 'Pengecekan log buffer switch core, utilisasi memori/CPU, dan uji STP loop prevention.',
                'created_by'  => $tlMnt->id,
            ],
            [
                'title'       => 'Backup Konfigurasi & Firmware Check Firewall BCA Thamrin',
                'project_id'  => $projects[8]->id,
                'engineer_id' => $engMnt2->id,
                'priority'    => 'High',
                'deadline'    => now()->addDays(3),
                'progress'    => 50,
                'status'      => 'In Progress',
                'description' => 'Ekspor running configuration Fortigate & Cisco switch ke cloud backup server.',
                'created_by'  => $tlMnt->id,
            ],
            [
                'title'       => 'Optimasi Channel & Roaming AP WiFi Menara BTPN',
                'project_id'  => $projects[10]->id,
                'engineer_id' => $engMnt2->id,
                'priority'    => 'High',
                'deadline'    => now()->addDays(4),
                'progress'    => 35,
                'status'      => 'In Progress',
                'description' => 'Penyelarasan daya transmit radio frequency untuk meminimalisir co-channel interference.',
                'created_by'  => $tlMnt->id,
            ],
            [
                'title'       => 'Health Check Storage RAID NVR CCTV Lazada Warehouse',
                'project_id'  => $projects[11]->id,
                'engineer_id' => $engMnt2->id,
                'priority'    => 'Medium',
                'deadline'    => now()->subDays(1),
                'progress'    => 100,
                'status'      => 'Completed',
                'description' => 'SMART test harddisk 64TB NVR, status RAID 5 optimal dan retensi rekaman 30 hari aman.',
                'created_by'  => $tlMnt->id,
            ],
        ];

        foreach ($tasksData as $tData) {
            Task::updateOrCreate(['title' => $tData['title'], 'project_id' => $tData['project_id']], $tData);
        }

        // ============================================================
        // 7. SEED TIMESHEETS (TERMASUK PERSONIL MAINTENANCE)
        // ============================================================
        $timesheetsData = [
            // Mario (L1 Maintenance)
            [
                'user_id'          => $engMnt1->id,
                'project_id'       => $projects[3]->id,
                'date'             => now()->subDays(2)->toDateString(),
                'start_time'       => '08:30:00',
                'end_time'         => '16:30:00',
                'duration_minutes' => 480,
                'category'         => 'Maintenance',
                'activity'         => 'Kunjungan rutin SLA bulanan: Pembersihan filter debu rack server & penggantian 6 patch cord rusak di RS Siloam.',
                'notes'            => 'Pekerjaan selesai sesuai checklist SOP maintenance.',
            ],
            [
                'user_id'          => $engMnt1->id,
                'project_id'       => $projects[9]->id,
                'date'             => now()->subDays(1)->toDateString(),
                'start_time'       => '09:00:00',
                'end_time'         => '16:00:00',
                'duration_minutes' => 420,
                'category'         => 'On-Site',
                'activity'         => 'Heatmap survey sinyal WiFi di koridor Mall Kelapa Gading 1-3 dan pengecekan power PoE switch.',
                'notes'            => 'Throughput rata-rata pengunjung mencapai 28Mbps.',
            ],
            [
                'user_id'          => $engMnt1->id,
                'project_id'       => $projects[3]->id,
                'date'             => now()->toDateString(),
                'start_time'       => '08:30:00',
                'end_time'         => '12:30:00',
                'duration_minutes' => 240,
                'category'         => 'Maintenance',
                'activity'         => 'Inspeksi suhu ruang server farmasi dan pencatatan log UPS redundansi RS Siloam.',
                'notes'            => 'Suhu ruang stabil di 19 derajat Celcius.',
            ],

            // Eris (L2 Maintenance)
            [
                'user_id'          => $engMnt2->id,
                'project_id'       => $projects[8]->id,
                'date'             => now()->subDays(2)->toDateString(),
                'start_time'       => '09:00:00',
                'end_time'         => '17:00:00',
                'duration_minutes' => 480,
                'category'         => 'Maintenance',
                'activity'         => 'Backup rutin konfigurasi switch Cisco Catalyst dan review policy firewall Fortigate Menara BCA Thamrin.',
                'notes'            => 'File backup tersimpan aman di server backup terenkripsi.',
            ],
            [
                'user_id'          => $engMnt2->id,
                'project_id'       => $projects[11]->id,
                'date'             => now()->subDays(1)->toDateString(),
                'start_time'       => '08:30:00',
                'end_time'         => '16:30:00',
                'duration_minutes' => 480,
                'category'         => 'Maintenance',
                'activity'         => 'Health check storage RAID 5 NVR Hikvision Gudang Logistik Lazada Cikarang.',
                'notes'            => 'Semua 8 HDD status normal tanpa bad sector.',
            ],
            [
                'user_id'          => $engMnt2->id,
                'project_id'       => $projects[3]->id,
                'date'             => now()->toDateString(),
                'start_time'       => '09:00:00',
                'end_time'         => '13:00:00',
                'duration_minutes' => 240,
                'category'         => 'Maintenance',
                'activity'         => 'Audit utilisasi bandwidth port trunk switch core dan verifikasi ACL database rekam medis RS Siloam.',
                'notes'            => 'Utilisasi trunk link di bawah 45%, kapasitas aman.',
            ],

            // Personel Network Lama
            [
                'user_id'          => $engNet1->id,
                'project_id'       => $projects[2]->id,
                'date'             => now()->subDays(1)->toDateString(),
                'start_time'       => '08:30:00',
                'end_time'         => '16:30:00',
                'duration_minutes' => 480,
                'category'         => 'On-Site',
                'activity'         => 'Penarikan kabel fiber optik riser backbone vertikal lantai 12-14 Gedung BCA Thamrin bersama tim.',
                'notes'            => 'Pekerjaan berjalan lancar tanpa hambatan.',
            ],
            [
                'user_id'          => $engNet3->id,
                'project_id'       => $projects[0]->id,
                'date'             => now()->subDays(1)->toDateString(),
                'start_time'       => '09:00:00',
                'end_time'         => '17:00:00',
                'duration_minutes' => 480,
                'category'         => 'On-Site',
                'activity'         => 'Simulasi failover link SD-WAN saat koneksi utama terputus, verifikasi zero packet loss.',
                'notes'            => 'Failover time kurang dari 1 detik.',
            ],
            [
                'user_id'          => $engSec1->id,
                'project_id'       => $projects[1]->id,
                'date'             => now()->toDateString(),
                'start_time'       => '08:30:00',
                'end_time'         => '12:30:00',
                'duration_minutes' => 240,
                'category'         => 'On-Site',
                'activity'         => 'Hardening keamanan port console switch management dan pengujian autentikasi TACACS+.',
                'notes'            => 'Akses console sekarang terlindungi enkripsi.',
            ],
        ];

        foreach ($timesheetsData as $tsData) {
            Timesheet::updateOrCreate(
                [
                    'user_id'    => $tsData['user_id'],
                    'project_id' => $tsData['project_id'],
                    'date'       => $tsData['date'],
                    'start_time' => $tsData['start_time'],
                ],
                $tsData
            );
        }

        // ============================================================
        // 8. SEED SCHEDULES (JADWAL PENUGASAN LAPANGAN SINKRON)
        // ============================================================
        $schedulesData = [
            [
                'title'       => 'Supervisi Implementasi SD-WAN Kantor Pusat Adira',
                'project_id'  => $projects[0]->id,
                'engineer_id' => $tlNet->id,
                'date'        => now()->toDateString(),
                'start_time'  => '09:00',
                'end_time'    => '15:30',
                'location'    => 'Kantor Pusat Adira Finance, Jakarta',
                'description' => 'Supervisi arsitektur routing dan koordinasi dengan tim IT client.',
                'created_by'  => $tlNet->id,
            ],
            [
                'title'       => 'Audit Firewall & Briefing Tim Keamanan Bank Mandiri',
                'project_id'  => $projects[1]->id,
                'engineer_id' => $tlSec->id,
                'date'        => now()->toDateString(),
                'start_time'  => '08:30',
                'end_time'    => '16:00',
                'location'    => 'Data Center Plaza Mandiri, Jakarta',
                'description' => 'Inspeksi cluster firewall HA dan evaluasi policy keamanan.',
                'created_by'  => $tlSec->id,
            ],
            [
                'title'       => 'Supervisi Visit Pemeliharaan Bulanan RS Siloam',
                'project_id'  => $projects[3]->id,
                'engineer_id' => $tlMnt->id,
                'date'        => now()->toDateString(),
                'start_time'  => '08:30',
                'end_time'    => '15:00',
                'location'    => 'RS Siloam Kebon Jeruk, Jakarta Barat',
                'description' => 'Review checklist preventive maintenance dan koordinasi dengan Manager IT RS.',
                'created_by'  => $tlMnt->id,
            ],
            [
                'title'       => 'Inspeksi Fisik & Cleaning Rack RS Siloam',
                'project_id'  => $projects[3]->id,
                'engineer_id' => $engMnt1->id,
                'date'        => now()->toDateString(),
                'start_time'  => '08:30',
                'end_time'    => '14:30',
                'location'    => 'RS Siloam Kebon Jeruk, Jakarta Barat',
                'description' => 'Pembersihan debu server farmasi & penataan patch cord switch distribusi.',
                'created_by'  => $tlMnt->id,
            ],
            [
                'title'       => 'Audit Log & Health Check Switch Core RS Siloam',
                'project_id'  => $projects[3]->id,
                'engineer_id' => $engMnt2->id,
                'date'        => now()->toDateString(),
                'start_time'  => '09:00',
                'end_time'    => '16:00',
                'location'    => 'RS Siloam Kebon Jeruk, Jakarta Barat',
                'description' => 'Pengecekan utilisasi CPU/Memory switch core dan backup konfigurasi.',
                'created_by'  => $tlMnt->id,
            ],
            [
                'title'       => 'Penarikan Fiber Optik Riser Gedung BCA Lantai 16',
                'project_id'  => $projects[2]->id,
                'engineer_id' => $engNet1->id,
                'date'        => now()->toDateString(),
                'start_time'  => '08:30',
                'end_time'    => '14:30',
                'location'    => 'Menara BCA Thamrin, Jakarta Pusat',
                'description' => 'Penarikan kabel dan terminasi ODF server room.',
                'created_by'  => $tlNet->id,
            ],
            [
                'title'       => 'Vulnerability Scanning Shopee Data Center',
                'project_id'  => $projects[6]->id,
                'engineer_id' => $engSec1->id,
                'date'        => now()->toDateString(),
                'start_time'  => '09:00',
                'end_time'    => '15:30',
                'location'    => 'Tersentrik Cyber 2 Tower, Jakarta',
                'description' => 'Security assessment internal server segmen data center.',
                'created_by'  => $tlSec->id,
            ],
        ];

        foreach ($schedulesData as $sd) {
            Schedule::updateOrCreate(
                ['title' => $sd['title'], 'date' => $sd['date'], 'engineer_id' => $sd['engineer_id']],
                $sd
            );
        }

        // ============================================================
        // 9. SEED ATTENDANCES (PRESENSI HARIAN SELURUH TIM)
        // ============================================================
        $allEngineers = [
            $tlNet, $tlSec, $tlMnt,
            $engNet1, $engNet2, $engNet3, $engNet4,
            $engSec1,
            $engMnt1, $engMnt2
        ];
        $today = now()->toDateString();

        foreach ($allEngineers as $idx => $eng) {
            Attendance::updateOrCreate(
                ['user_id' => $eng->id, 'attendance_date' => $today, 'type' => 'clock_in'],
                [
                    'latitude'        => -6.1664,
                    'longitude'       => 106.8148,
                    'distance_meters' => 8 + $idx * 3,
                    'is_within_range' => true,
                    'address'         => 'Kantor Pusat PT IP Network Solusindo, Gambir, Jakarta Pusat',
                    'note'            => 'Hadir tepat waktu bertugas di lokasi project / maintenance.',
                ]
            );
        }

        $this->command->info('✅ Seluruh 25 Proyek Berhasil Di-Update Lengkap dengan Sales Resmi!');
    }
}

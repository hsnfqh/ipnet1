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
        // 0. Bersihkan akun dummy lama
        User::whereIn('name', ['Rangga Saputra', 'Fajar Nugroho', 'Dimas Prakoso', 'Sinta Wulandari', 'Bayu Kusuma', 'Rani Oktaviani'])
            ->orWhere('email', 'like', '%@ipnetwork.co.id')
            ->delete();

        // 1. Pastikan roles tersedia
        $roles = [
            'Direktur',
            'HD / Direktur',
            'Group Leader',
            'Team Leader',
            'Engineer',
            'Lead Engineer',
            'Engineer L1',
            'Engineer L2',
        ];
        foreach ($roles as $r) {
            Role::firstOrCreate(['name' => $r]);
        }

        // 2. Divisi
        $divNet = Division::firstOrCreate(
            ['name' => 'Divisi Network'],
            ['code' => 'NET', 'description' => 'Divisi Infrastruktur Jaringan, Routing & Switching']
        );
        $divSec = Division::firstOrCreate(
            ['name' => 'Divisi Security'],
            ['code' => 'SEC', 'description' => 'Divisi Keamanan Jaringan & Cyber Security Protection']
        );

        // 3. Tim
        $teamNet = Team::firstOrCreate(
            ['name' => 'Tim Network', 'division_id' => $divNet->id],
            ['description' => 'Tim Operasional, Deployment & Maintenance Jaringan']
        );
        $teamSec = Team::firstOrCreate(
            ['name' => 'Tim Security', 'division_id' => $divSec->id],
            ['description' => 'Tim Monitoring, Hardening & Security Implementation']
        );

        // ============================================================
        // 4. SEED USERS RESMI
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

        // ============================================================
        // 5. SEED PROJECTS (10 PROYEK REALISTIS & LENGKAP)
        // ============================================================
        $projectsData = [
            [
                'name'        => 'Upgrade Jaringan WAN & SD-WAN Adira Finance',
                'client'      => 'PT Adira Dinamika Multi Finance Tbk',
                'location'    => 'Kantor Pusat Adira & 15 Cabang',
                'start_date'  => now()->subDays(12),
                'deadline'    => now()->addDays(18),
                'status'      => 'On Progress',
                'description' => 'Implementasi SD-WAN, upgrade bandwidth router utama, dan konfigurasi failover link.',
                'created_by'  => $tlNet->id,
            ],
            [
                'name'        => 'Hardening Firewall & SIEM Monitoring Bank Mandiri',
                'client'      => 'PT Bank Mandiri (Persero) Tbk',
                'location'    => 'Data Center Plaza Mandiri, Jakarta',
                'start_date'  => now()->subDays(10),
                'deadline'    => now()->addDays(20),
                'status'      => 'On Progress',
                'description' => 'Audit keamanan siber, konfigurasi Fortigate Firewall HA, dan integrasi alert SIEM.',
                'created_by'  => $tlSec->id,
            ],
            [
                'name'        => 'Instalasi Fiber Optik Backbone - Gedung BCA Thamrin',
                'client'      => 'PT Bank Central Asia Tbk',
                'location'    => 'Menara BCA Lt. 12-24, Jakarta Pusat',
                'start_date'  => now()->subDays(20),
                'deadline'    => now()->addDays(10),
                'status'      => 'On Progress',
                'description' => 'Pemasangan jalur fiber optik backbone multimode OM4 untuk 12 lantai perkantoran.',
                'created_by'  => $tlNet->id,
            ],
            [
                'name'        => 'Maintenance Jaringan Core & VLAN - RS Siloam Kebon Jeruk',
                'client'      => 'RS Siloam Hospitals Group',
                'location'    => 'RS Siloam Kebon Jeruk, Jakarta Barat',
                'start_date'  => now()->subDays(15),
                'deadline'    => now()->addDays(5),
                'status'      => 'On Progress',
                'description' => 'Pemeliharaan rutin switch core Cisco Catalyst dan restrukturisasi segmentasi VLAN medis.',
                'created_by'  => $tlNet->id,
            ],
            [
                'name'        => 'Upgrade Bandwidth & WiFi 6 - Mall Kelapa Gading',
                'client'      => 'Summarecon Mall Group',
                'location'    => 'Mall Kelapa Gading 1-5, Jakarta Utara',
                'start_date'  => now()->subDays(40),
                'deadline'    => now()->subDays(5),
                'status'      => 'Completed',
                'description' => 'Pemasangan 65 titik Access Point WiFi 6 Aruba dan gateway captive portal pengunjung.',
                'created_by'  => $tlNet->id,
            ],
            [
                'name'        => 'Rollout WiFi Corporate & Radius Server - Menara BTPN',
                'client'      => 'PT Bank BTPN Tbk',
                'location'    => 'CBD Mega Kuningan, Jakarta Selatan',
                'start_date'  => now()->subDays(8),
                'deadline'    => now()->addDays(22),
                'status'      => 'On Progress',
                'description' => 'Implementasi WPA3 Enterprise, integrasi Active Directory, dan autentikasi 802.1X.',
                'created_by'  => $tlNet->id,
            ],
            [
                'name'        => 'Security Vulnerability Assessment - Shopee Data Center',
                'client'      => 'PT Shopee International Indonesia',
                'location'    => 'Tersentrik Cyber 2 Tower, Jakarta',
                'start_date'  => now()->subDays(6),
                'deadline'    => now()->addDays(24),
                'status'      => 'On Progress',
                'description' => 'Penetration testing internal segment, port scanning, dan review policy WAF.',
                'created_by'  => $tlSec->id,
            ],
            [
                'name'        => 'CCTV IP & Network Infrastructure - Gudang Logistik Lazada',
                'client'      => 'PT Lazada Express Indonesia',
                'location'    => 'Kawasan Industri MM2100, Cikarang',
                'start_date'  => now()->subDays(35),
                'deadline'    => now()->subDays(2),
                'status'      => 'Completed',
                'description' => 'Instalasi 48 IP Camera Hikvision 4K, PoE switch manageable, dan storage NVR 64TB.',
                'created_by'  => $tlNet->id,
            ],
            [
                'name'        => 'Redundant BGP Peering & Routing - Data Center Telkomsigma',
                'client'      => 'PT Sigma Cipta Caraka (Telkomsigma)',
                'location'    => 'Serpong Data Center, Tangerang Selatan',
                'start_date'  => now()->addDays(3),
                'deadline'    => now()->addDays(35),
                'status'      => 'Planning',
                'description' => 'Konfigurasi ASN BGP multihoming dengan upstream Telkom & Indosat.',
                'created_by'  => $tlNet->id,
            ],
            [
                'name'        => 'Disaster Recovery Network Setup - Astra International',
                'client'      => 'PT Astra International Tbk',
                'location'    => 'Menara Astra & Cibitung DR Site',
                'start_date'  => now()->addDays(7),
                'deadline'    => now()->addDays(40),
                'status'      => 'Planning',
                'description' => 'Penyusunan link cadangan dark fiber dan simulasi failover database core.',
                'created_by'  => $tlSec->id,
            ],
        ];

        $projects = [];
        foreach ($projectsData as $pData) {
            $p = Project::updateOrCreate(['name' => $pData['name']], $pData);
            $projects[] = $p;
        }

        // ============================================================
        // 6. SEED TASKS (30+ TUGAS BERAGAM STATUS & PRIORITAS)
        // ============================================================
        $tasksData = [
            // Nugraha Pratama (TL Network)
            [
                'title'       => 'Supervisi Arsitektur SD-WAN & Core Routing Adira',
                'project_id'  => $projects[0]->id,
                'engineer_id' => $tlNet->id,
                'priority'    => 'High',
                'deadline'    => now()->addDays(2),
                'progress'    => 75,
                'status'      => 'In Progress',
                'description' => 'Supervisi langsung konfigurasi topologi jaringan dan arsitektur routing.',
                'created_by'  => $tlNet->id,
            ],
            [
                'title'       => 'User Acceptance Test (UAT) & Sign-Off Proyek Adira',
                'project_id'  => $projects[0]->id,
                'engineer_id' => $tlNet->id,
                'priority'    => 'High',
                'deadline'    => now()->addDays(16),
                'progress'    => 10,
                'status'      => 'In Progress',
                'description' => 'Pengujian fungsionalitas failover link dan serah terima dokumen teknis ke client.',
                'created_by'  => $tlNet->id,
            ],
            [
                'title'       => 'Review Desain Topologi Fiber Optik BCA',
                'project_id'  => $projects[2]->id,
                'engineer_id' => $tlNet->id,
                'priority'    => 'Medium',
                'deadline'    => now()->subDays(1),
                'progress'    => 100,
                'status'      => 'Completed',
                'description' => 'Verifikasi jalur tray kabel fiber optik dan persetujuan skema sambungan ODF.',
                'created_by'  => $tlNet->id,
            ],

            // Ignatius Rizky (TL Security)
            [
                'title'       => 'Audit Keamanan & Policy Review Firewall Bank Mandiri',
                'project_id'  => $projects[1]->id,
                'engineer_id' => $tlSec->id,
                'priority'    => 'High',
                'deadline'    => now()->addDays(3),
                'progress'    => 65,
                'status'      => 'In Progress',
                'description' => 'Analisis rule akses firewall, compliance security, dan arsitektur HA.',
                'created_by'  => $tlSec->id,
            ],
            [
                'title'       => 'Security Assessment & Executive Report Shopee DC',
                'project_id'  => $projects[6]->id,
                'engineer_id' => $tlSec->id,
                'priority'    => 'High',
                'deadline'    => now()->addDays(21),
                'progress'    => 20,
                'status'      => 'In Progress',
                'description' => 'Penyusunan laporan audit vulnerability dan rekomendasi mitigasi risiko cyber.',
                'created_by'  => $tlSec->id,
            ],

            // Rorik (L1 Network)
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
                'title'       => 'Splicing & Terminasi Fiber Optik ODF Ruang Server',
                'project_id'  => $projects[2]->id,
                'engineer_id' => $engNet1->id,
                'priority'    => 'High',
                'deadline'    => now()->addDays(4),
                'progress'    => 40,
                'status'      => 'In Progress',
                'description' => 'Penyambungan core fiber menggunakan fusion splicer dan penataan tray.',
                'created_by'  => $tlNet->id,
            ],
            [
                'title'       => 'Pengecekan Kabel UTP & Patch Panel Site Adira',
                'project_id'  => $projects[0]->id,
                'engineer_id' => $engNet1->id,
                'priority'    => 'Medium',
                'deadline'    => now()->addDays(10),
                'progress'    => 0,
                'status'      => 'Assigned',
                'description' => 'Testing kelayakan kabel LAN Cat6 dan pelabelan port switch.',
                'created_by'  => $tlNet->id,
            ],
            [
                'title'       => 'Dokumentasi As-Built Drawing Jaringan RS Siloam',
                'project_id'  => $projects[3]->id,
                'engineer_id' => $engNet1->id,
                'priority'    => 'Low',
                'deadline'    => now()->subDays(2),
                'progress'    => 100,
                'status'      => 'Completed',
                'description' => 'Pembuatan dokumen as-built drawing jalur kabel dan titik outlet LAN.',
                'created_by'  => $tlNet->id,
            ],

            // Shiamsyah Azis (L1 Network)
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
                'deadline'    => now()->addDays(5),
                'progress'    => 30,
                'status'      => 'In Progress',
                'description' => 'Mounting ceiling AP Aruba dan penarikan kabel drop LAN PoE.',
                'created_by'  => $tlNet->id,
            ],
            [
                'title'       => 'Testing Speedtest & Coverage Sinyal WiFi Mall Kelapa Gading',
                'project_id'  => $projects[4]->id,
                'engineer_id' => $engNet2->id,
                'priority'    => 'Low',
                'deadline'    => now()->subDays(4),
                'progress'    => 100,
                'status'      => 'Completed',
                'description' => 'Heatmap survey sinyal dan throughput test di atrium dan koridor mall.',
                'created_by'  => $tlNet->id,
            ],
            [
                'title'       => 'Labeling Port Switch & Patch Cord RS Siloam',
                'project_id'  => $projects[3]->id,
                'engineer_id' => $engNet2->id,
                'priority'    => 'Medium',
                'deadline'    => now()->addDays(12),
                'progress'    => 0,
                'status'      => 'Assigned',
                'description' => 'Pemasangan label numbering tahan lama pada rack 01-04.',
                'created_by'  => $tlNet->id,
            ],

            // Dedy Suryana (L2 Network)
            [
                'title'       => 'Konfigurasi BGP Routing & Inter-VLAN Gateway Adira',
                'project_id'  => $projects[0]->id,
                'engineer_id' => $engNet3->id,
                'priority'    => 'High',
                'deadline'    => now()->addDays(1),
                'progress'    => 90,
                'status'      => 'In Progress',
                'description' => 'Setting peering BGP ke provider ISP utama dan backup dengan auto failover.',
                'created_by'  => $tlNet->id,
            ],
            [
                'title'       => 'Optimasi VPN IPsec Tunnel Antar Cabang Adira Finance',
                'project_id'  => $projects[0]->id,
                'engineer_id' => $engNet3->id,
                'priority'    => 'High',
                'deadline'    => now()->addDays(6),
                'progress'    => 40,
                'status'      => 'In Progress',
                'description' => 'Penyusunan route policy dan enkripsi AES-256 pada VPN tunnel 15 cabang.',
                'created_by'  => $tlNet->id,
            ],
            [
                'title'       => 'Restrukturisasi VLAN & QoS Voice/Data RS Siloam',
                'project_id'  => $projects[3]->id,
                'engineer_id' => $engNet3->id,
                'priority'    => 'Medium',
                'deadline'    => now()->addDays(3),
                'progress'    => 70,
                'status'      => 'In Progress',
                'description' => 'Isolasi subnet perangkat ICU dan prioritasi traffic data rekam medis.',
                'created_by'  => $tlNet->id,
            ],
            [
                'title'       => 'Setup Radius Server 802.1X Menara BTPN',
                'project_id'  => $projects[5]->id,
                'engineer_id' => $engNet3->id,
                'priority'    => 'High',
                'deadline'    => now()->addDays(15),
                'progress'    => 0,
                'status'      => 'Assigned',
                'description' => 'Integrasi FreeRadius ke Active Directory Windows Server BTPN.',
                'created_by'  => $tlNet->id,
            ],
            [
                'title'       => 'Konfigurasi Switch Distribusi Lazada Warehouse',
                'project_id'  => $projects[7]->id,
                'engineer_id' => $engNet3->id,
                'priority'    => 'Low',
                'deadline'    => now()->subDays(6),
                'progress'    => 100,
                'status'      => 'Completed',
                'description' => 'Setting LACP Link Aggregation dan IGMP snooping multicast video CCTV.',
                'created_by'  => $tlNet->id,
            ],

            // Syaiful Amin (L2 Network)
            [
                'title'       => 'Stress Test Failover SD-WAN Dual Provider Adira',
                'project_id'  => $projects[0]->id,
                'engineer_id' => $engNet4->id,
                'priority'    => 'High',
                'deadline'    => now()->addDays(2),
                'progress'    => 60,
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
                'progress'    => 50,
                'status'      => 'In Progress',
                'description' => 'Pengukuran redaman dB loss menggunakan alat ukur OTDR EXFO.',
                'created_by'  => $tlNet->id,
            ],
            [
                'title'       => 'Audit Bandwidth Monitoring & Grafana Dashboard Adira',
                'project_id'  => $projects[0]->id,
                'engineer_id' => $engNet4->id,
                'priority'    => 'Medium',
                'deadline'    => now()->addDays(14),
                'progress'    => 15,
                'status'      => 'In Progress',
                'description' => 'Setup SNMP exporter untuk visualisasi real-time traffic jaringan cabang.',
                'created_by'  => $tlNet->id,
            ],
            [
                'title'       => 'Firmware Upgrade Switch Core Cisco RS Siloam',
                'project_id'  => $projects[3]->id,
                'engineer_id' => $engNet4->id,
                'priority'    => 'High',
                'deadline'    => now()->subDays(3),
                'progress'    => 100,
                'status'      => 'Completed',
                'description' => 'Upgrade IOS-XE versi stabil dan pengujian switch stack redundansi.',
                'created_by'  => $tlNet->id,
            ],

            // Eka Kurnia (L1 Security)
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
                'title'       => 'Integrasi Log Firewall ke SIEM Splunk Bank Mandiri',
                'project_id'  => $projects[1]->id,
                'engineer_id' => $engSec1->id,
                'priority'    => 'High',
                'deadline'    => now()->addDays(7),
                'progress'    => 35,
                'status'      => 'In Progress',
                'description' => 'Konfigurasi Syslog forwarder dan parsing format log event keamanan.',
                'created_by'  => $tlSec->id,
            ],
            [
                'title'       => 'Internal Vulnerability Scanning Shopee DC',
                'project_id'  => $projects[6]->id,
                'engineer_id' => $engSec1->id,
                'priority'    => 'High',
                'deadline'    => now()->addDays(14),
                'progress'    => 20,
                'status'      => 'In Progress',
                'description' => 'Scanning menggunakan Nessus Vulnerability Scanner pada subnet server.',
                'created_by'  => $tlSec->id,
            ],
            [
                'title'       => 'Update Signature IPS & Antivirus Fortigate Bank Mandiri',
                'project_id'  => $projects[1]->id,
                'engineer_id' => $engSec1->id,
                'priority'    => 'Low',
                'deadline'    => now()->subDays(1),
                'progress'    => 100,
                'status'      => 'Completed',
                'description' => 'Pembaruan database threat intelligence dan uji proteksi malware payload.',
                'created_by'  => $tlSec->id,
            ],
        ];

        foreach ($tasksData as $tData) {
            Task::updateOrCreate(['title' => $tData['title'], 'project_id' => $tData['project_id']], $tData);
        }

        // ============================================================
        // 7. SEED TIMESHEETS (LOG KERJA LENGKAP UNTUK SEMUA ENGINEER)
        // ============================================================
        $timesheetsData = [
            // Rorik (L1 Network)
            [
                'user_id'          => $engNet1->id,
                'project_id'       => $projects[2]->id,
                'date'             => now()->subDays(4)->toDateString(),
                'start_time'       => '08:30:00',
                'end_time'         => '16:30:00',
                'duration_minutes' => 480,
                'category'         => 'On-Site',
                'activity'         => 'Penarikan kabel fiber optik riser backbone vertikal lantai 12-14 Gedung BCA Thamrin bersama tim.',
                'notes'            => 'Pekerjaan berjalan lancar tanpa hambatan.',
            ],
            [
                'user_id'          => $engNet1->id,
                'project_id'       => $projects[2]->id,
                'date'             => now()->subDays(3)->toDateString(),
                'start_time'       => '08:30:00',
                'end_time'         => '17:00:00',
                'duration_minutes' => 510,
                'category'         => 'On-Site',
                'activity'         => 'Lanjutan penarikan kabel fiber optik lantai 15-16 dan terminasi tray kabel shaft.',
                'notes'            => 'Kabel telah terpasang rapi di dalam conduit.',
            ],
            [
                'user_id'          => $engNet1->id,
                'project_id'       => $projects[2]->id,
                'date'             => now()->subDays(2)->toDateString(),
                'start_time'       => '09:00:00',
                'end_time'         => '15:30:00',
                'duration_minutes' => 390,
                'category'         => 'On-Site',
                'activity'         => 'Splicing core fiber optik ODF di ruang server lantai 12. Total 24 core selesai disambung.',
                'notes'            => 'Redaman rata-rata 0.02 dB (sangat baik).',
            ],
            [
                'user_id'          => $engNet1->id,
                'project_id'       => $projects[0]->id,
                'date'             => now()->subDays(1)->toDateString(),
                'start_time'       => '08:30:00',
                'end_time'         => '14:30:00',
                'duration_minutes' => 360,
                'category'         => 'On-Site',
                'activity'         => 'Pengecekan jalur kabel UTP dan penataan patch cord switch rack server Adira Finance.',
                'notes'            => 'Patch cord yang rusak telah diganti baru.',
            ],
            [
                'user_id'          => $engNet1->id,
                'project_id'       => $projects[2]->id,
                'date'             => now()->toDateString(),
                'start_time'       => '08:30:00',
                'end_time'         => '12:30:00',
                'duration_minutes' => 240,
                'category'         => 'On-Site',
                'activity'         => 'Testing kontinuitas cahaya laser visual fault locator pada seluruh core ODF Gedung BCA.',
                'notes'            => 'Seluruh jalur core 1-24 tembus sempurna.',
            ],

            // Shiamsyah Azis (L1 Network)
            [
                'user_id'          => $engNet2->id,
                'project_id'       => $projects[0]->id,
                'date'             => now()->subDays(3)->toDateString(),
                'start_time'       => '09:00:00',
                'end_time'         => '16:00:00',
                'duration_minutes' => 420,
                'category'         => 'On-Site',
                'activity'         => 'Instalasi dan mounting router Mikrotik CCR2004 di rack 02 Kantor Pusat Adira Finance.',
                'notes'            => 'Power supply redundant 1 dan 2 telah dihubungkan ke UPS terpisah.',
            ],
            [
                'user_id'          => $engNet2->id,
                'project_id'       => $projects[5]->id,
                'date'             => now()->subDays(2)->toDateString(),
                'start_time'       => '08:30:00',
                'end_time'         => '17:30:00',
                'duration_minutes' => 540,
                'category'         => 'On-Site',
                'activity'         => 'Pemasangan bracket dan instalasi 12 unit Access Point Aruba WiFi 6 di Menara BTPN lantai 8.',
                'notes'            => 'Semua AP menyala dan terhubung PoE switch.',
            ],
            [
                'user_id'          => $engNet2->id,
                'project_id'       => $projects[5]->id,
                'date'             => now()->subDays(1)->toDateString(),
                'start_time'       => '09:00:00',
                'end_time'         => '18:00:00',
                'duration_minutes' => 540,
                'category'         => 'Overtime',
                'activity'         => 'Lembur penarikan kabel drop LAN PoE untuk AP lantai 9 dan 10 Menara BTPN.',
                'notes'            => 'Pekerjaan diselesaikan malam hari agar tidak mengganggu aktivitas kantor.',
            ],
            [
                'user_id'          => $engNet2->id,
                'project_id'       => $projects[0]->id,
                'date'             => now()->toDateString(),
                'start_time'       => '08:30:00',
                'end_time'         => '13:00:00',
                'duration_minutes' => 270,
                'category'         => 'On-Site',
                'activity'         => 'Labeling kabel jaringan dan update denah port switch distribution Adira Finance.',
                'notes'            => 'Dokumentasi telah diserahkan ke Lead Engineer.',
            ],

            // Dedy Suryana (L2 Network)
            [
                'user_id'          => $engNet3->id,
                'project_id'       => $projects[0]->id,
                'date'             => now()->subDays(4)->toDateString(),
                'start_time'       => '09:00:00',
                'end_time'         => '17:00:00',
                'duration_minutes' => 480,
                'category'         => 'On-Site',
                'activity'         => 'Konfigurasi BGP Routing peering ke ISP 1 (Telkom) & ISP 2 (Indosat) dengan auto switchover.',
                'notes'            => 'BGP state Established, traffic balance normal.',
            ],
            [
                'user_id'          => $engNet3->id,
                'project_id'       => $projects[0]->id,
                'date'             => now()->subDays(3)->toDateString(),
                'start_time'       => '09:00:00',
                'end_time'         => '16:30:00',
                'duration_minutes' => 450,
                'category'         => 'Remote',
                'activity'         => 'Setup VPN IPsec Tunnel AES-256 antar kantor pusat Adira ke 8 kantor cabang utama.',
                'notes'            => 'Tunnel aktif dan latency rata-rata 18ms.',
            ],
            [
                'user_id'          => $engNet3->id,
                'project_id'       => $projects[3]->id,
                'date'             => now()->subDays(2)->toDateString(),
                'start_time'       => '08:30:00',
                'end_time'         => '16:00:00',
                'duration_minutes' => 450,
                'category'         => 'Maintenance',
                'activity'         => 'Restrukturisasi subnet VLAN medis dan isolasi segmen server database di RS Siloam Kebon Jeruk.',
                'notes'            => 'Inter-VLAN routing berjalan lancar dengan ACL proteksi.',
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
                'user_id'          => $engNet3->id,
                'project_id'       => $projects[5]->id,
                'date'             => now()->toDateString(),
                'start_time'       => '08:30:00',
                'end_time'         => '12:30:00',
                'duration_minutes' => 240,
                'category'         => 'On-Site',
                'activity'         => 'Integrasi controller WiFi Aruba ke Active Directory Domain Controller Bank BTPN.',
                'notes'            => 'Uji coba login user staff sukses dengan autentikasi WPA3-Enterprise.',
            ],

            // Syaiful Amin (L2 Network)
            [
                'user_id'          => $engNet4->id,
                'project_id'       => $projects[2]->id,
                'date'             => now()->subDays(4)->toDateString(),
                'start_time'       => '09:00:00',
                'end_time'         => '17:00:00',
                'duration_minutes' => 480,
                'category'         => 'On-Site',
                'activity'         => 'Pengujian redaman serat optik menggunakan OTDR pada 24 core backbone Gedung BCA Thamrin.',
                'notes'            => 'Seluruh sambungan memenuhi standar ITU-T G.652.',
            ],
            [
                'user_id'          => $engNet4->id,
                'project_id'       => $projects[0]->id,
                'date'             => now()->subDays(3)->toDateString(),
                'start_time'       => '08:30:00',
                'end_time'         => '16:30:00',
                'duration_minutes' => 480,
                'category'         => 'On-Site',
                'activity'         => 'Stress test traffic bandwidth controller SD-WAN menggunakan Iperf3 pada beban 1Gbps.',
                'notes'            => 'CPU load stabil pada 42%, tidak ditemukan packet drop.',
            ],
            [
                'user_id'          => $engNet4->id,
                'project_id'       => $projects[3]->id,
                'date'             => now()->subDays(2)->toDateString(),
                'start_time'       => '21:00:00',
                'end_time'         => '03:00:00',
                'duration_minutes' => 360,
                'category'         => 'Overtime',
                'activity'         => 'Maintenance malam: upgrade firmware Cisco IOS-XE switch core RS Siloam Kebon Jeruk.',
                'notes'            => 'Downtime maintenance 15 menit, sistem pulih sempurna.',
            ],
            [
                'user_id'          => $engNet4->id,
                'project_id'       => $projects[0]->id,
                'date'             => now()->toDateString(),
                'start_time'       => '09:00:00',
                'end_time'         => '13:00:00',
                'duration_minutes' => 240,
                'category'         => 'Remote',
                'activity'         => 'Setup SNMP Prometheus exporter dan dashboard monitoring Grafana untuk link WAN cabang.',
                'notes'            => 'Dashboard visualisasi utilisasi link telah online.',
            ],

            // Eka Kurnia (L1 Security)
            [
                'user_id'          => $engSec1->id,
                'project_id'       => $projects[1]->id,
                'date'             => now()->subDays(4)->toDateString(),
                'start_time'       => '08:30:00',
                'end_time'         => '16:30:00',
                'duration_minutes' => 480,
                'category'         => 'On-Site',
                'activity'         => 'Review konfigurasi firewall Fortigate cluster Bank Mandiri dan audit access control list.',
                'notes'            => 'Ditemukan 4 rule usang yang telah direkomendasikan untuk dihapus.',
            ],
            [
                'user_id'          => $engSec1->id,
                'project_id'       => $projects[1]->id,
                'date'             => now()->subDays(3)->toDateString(),
                'start_time'       => '08:30:00',
                'end_time'         => '17:00:00',
                'duration_minutes' => 510,
                'category'         => 'On-Site',
                'activity'         => 'Konfigurasi Syslog forwarder dari Fortigate ke SIEM Splunk dan pembuatan alert anomali.',
                'notes'            => 'Log event keamanan tersinkronisasi real-time.',
            ],
            [
                'user_id'          => $engSec1->id,
                'project_id'       => $projects[6]->id,
                'date'             => now()->subDays(2)->toDateString(),
                'start_time'       => '09:00:00',
                'end_time'         => '16:00:00',
                'duration_minutes' => 420,
                'category'         => 'On-Site',
                'activity'         => 'Vulnerability scanning internal network server menggunakan Nessus di Shopee Data Center.',
                'notes'            => 'Laporan scanning awal telah di-generate untuk tim Lead Security.',
            ],
            [
                'user_id'          => $engSec1->id,
                'project_id'       => $projects[1]->id,
                'date'             => now()->subDays(1)->toDateString(),
                'start_time'       => '08:30:00',
                'end_time'         => '15:30:00',
                'duration_minutes' => 420,
                'category'         => 'On-Site',
                'activity'         => 'Update threat intelligence database dan engine anti-botnet di perimeter firewall.',
                'notes'            => 'Signature database up-to-date per tanggal hari ini.',
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
        // 8. SEED SCHEDULES (JADWAL PENUGASAN LAPANGAN)
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
                'title'       => 'Instalasi Access Point WiFi 6 Menara BTPN',
                'project_id'  => $projects[5]->id,
                'engineer_id' => $engNet2->id,
                'date'        => now()->toDateString(),
                'start_time'  => '09:00',
                'end_time'    => '16:00',
                'location'    => 'Menara BTPN Mega Kuningan, Jakarta Selatan',
                'description' => 'Mounting ceiling AP dan integrasi PoE switch.',
                'created_by'  => $tlNet->id,
            ],
            [
                'title'       => 'Konfigurasi Failover BGP Peering ISP Adira',
                'project_id'  => $projects[0]->id,
                'engineer_id' => $engNet3->id,
                'date'        => now()->toDateString(),
                'start_time'  => '09:30',
                'end_time'    => '16:30',
                'location'    => 'Site Adira Finance Pusat, Jakarta',
                'description' => 'Setting BGP route map dan simulasi link switchover.',
                'created_by'  => $tlNet->id,
            ],
            [
                'title'       => 'Pengukuran Redaman OTDR Fiber Optik BCA',
                'project_id'  => $projects[2]->id,
                'engineer_id' => $engNet4->id,
                'date'        => now()->toDateString(),
                'start_time'  => '09:00',
                'end_time'    => '15:00',
                'location'    => 'Menara BCA Thamrin, Jakarta Pusat',
                'description' => 'OTDR testing 24 core fiber dan pembuatan trace report.',
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
        // 9. SEED ATTENDANCES (PRESENSI HARIAN TIM RESMI)
        // ============================================================
        $allEngineers = [$tlNet, $tlSec, $engNet1, $engNet2, $engNet3, $engNet4, $engSec1];
        $today = now()->toDateString();

        foreach ($allEngineers as $idx => $eng) {
            Attendance::updateOrCreate(
                ['user_id' => $eng->id, 'attendance_date' => $today, 'type' => 'clock_in'],
                [
                    'latitude'        => -6.1664,
                    'longitude'       => 106.8148,
                    'distance_meters' => 10 + $idx * 4,
                    'is_within_range' => true,
                    'address'         => 'Kantor Pusat PT IP Network Solusindo, Gambir, Jakarta Pusat',
                    'note'            => 'Hadir tepat waktu bertugas di lokasi project.',
                ]
            );
        }

        $this->command->info('✅ Seluruh Proyek, Tugas, Timesheet, Jadwal & Presensi Tim Resmi Berhasil Dibuat!');
    }
}

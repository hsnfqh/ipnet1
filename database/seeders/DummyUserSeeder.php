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
use Spatie\Permission\Models\Role;

class DummyUserSeeder extends Seeder
{
    public function run(): void
    {
        // 0. Bersihkan akun dummy lama (Rangga, Fajar, Dimas, Sinta, Bayu)
        User::whereIn('name', ['Rangga Saputra', 'Fajar Nugroho', 'Dimas Prakoso', 'Sinta Wulandari', 'Bayu Kusuma'])
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
        // 4. SEED USERS SESUAI DATA RESMI DARI USER
        // ============================================================

        // 1. Direktur - Hariyadi
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

        // 2. Head Division / GL - Susanto Djaya
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

        // 3. Network Leader - Nugraha Pratama
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

        // 4. Security Leader - Ignatius Rizky
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

        // 5. L1 Network (1) - Rorik
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

        // 6. L1 Network (2) - Shiamsyah Azis
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

        // 7. L2 Network (1) - Dedy Suryana
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

        // 8. L2 Network (2) - Syaiful Amin
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

        // 9. L1 Security (1) - Eka Kurnia
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
        // 5. SEED PROJECTS
        // ============================================================
        $projNet = Project::firstOrCreate(
            ['name' => 'Upgrade Jaringan WAN & SD-WAN Adira Finance'],
            [
                'client'      => 'PT Adira Dinamika Multi Finance Tbk',
                'location'    => 'Kantor Pusat Adira & 15 Cabang',
                'start_date'  => now()->subDays(10),
                'deadline'    => now()->addDays(20),
                'status'      => 'On Progress',
                'description' => 'Implementasi SD-WAN, upgrade bandwidth router utama, dan konfigurasi failover link.',
                'created_by'  => $direktur->id,
            ]
        );

        $projSec = Project::firstOrCreate(
            ['name' => 'Hardening Firewall & SIEM Monitoring Bank Mandiri'],
            [
                'client'      => 'PT Bank Mandiri (Persero) Tbk',
                'location'    => 'Data Center Plaza Mandiri, Jakarta',
                'start_date'  => now()->subDays(7),
                'deadline'    => now()->addDays(25),
                'status'      => 'On Progress',
                'description' => 'Audit keamanan siber, konfigurasi Fortigate Firewall HA, dan integrasi alert SIEM.',
                'created_by'  => $direktur->id,
            ]
        );

        // ============================================================
        // 6. SEED TASKS UNTUK SETIAP ENGINEER & TEAM LEADER
        // ============================================================
        $tasksData = [
            // Nugraha Pratama (Network Leader / TL Network) - Tugas Arsitektur & Supervisi Lapangan
            [
                'title'       => 'Supervisi Arsitektur SD-WAN & Core Routing Adira',
                'project_id'  => $projNet->id,
                'engineer_id' => $tlNet->id,
                'priority'    => 'High',
                'deadline'    => now()->addDays(2), // Minggu Ini
                'progress'    => 70,
                'status'      => 'In Progress',
                'description' => 'Supervisi langsung konfigurasi topologi jaringan dan arsitektur routing.',
                'created_by'  => $direktur->id,
            ],
            [
                'title'       => 'User Acceptance Test (UAT) & Sign-Off Proyek Adira',
                'project_id'  => $projNet->id,
                'engineer_id' => $tlNet->id,
                'priority'    => 'High',
                'deadline'    => now()->addDays(18), // Bulan Ini
                'progress'    => 10,
                'status'      => 'In Progress',
                'description' => 'Pengujian fungsionalitas failover link dan serah terima dokumen teknis ke client.',
                'created_by'  => $direktur->id,
            ],

            // Ignatius Rizky (Security Leader / TL Security) - Tugas Audit & Hardening Lapangan
            [
                'title'       => 'Audit Keamanan & Policy Review Firewall Bank Mandiri',
                'project_id'  => $projSec->id,
                'engineer_id' => $tlSec->id,
                'priority'    => 'High',
                'deadline'    => now()->addDays(3), // Minggu Ini
                'progress'    => 60,
                'status'      => 'In Progress',
                'description' => 'Analisis rule akses firewall, compliance security, dan arsitektur HA.',
                'created_by'  => $direktur->id,
            ],
            [
                'title'       => 'Security Vulnerability Assessment & Executive Report',
                'project_id'  => $projSec->id,
                'engineer_id' => $tlSec->id,
                'priority'    => 'High',
                'deadline'    => now()->addDays(23), // Bulan Ini
                'progress'    => 0,
                'status'      => 'Assigned',
                'description' => 'Penyusunan laporan audit vulnerability dan rekomendasi mitigasi risiko cyber.',
                'created_by'  => $direktur->id,
            ],

            // Rorik (L1 Network) - 1 Minggu Ini, 2 Bulan Ini
            [
                'title'       => 'Pengecekan Kabel UTP & Switch Port Site Adira',
                'project_id'  => $projNet->id,
                'engineer_id' => $engNet1->id,
                'priority'    => 'High',
                'deadline'    => now()->addDays(1), // Minggu Ini
                'progress'    => 60,
                'status'      => 'In Progress',
                'description' => 'Testing koneksi LAN lantai 1-3 dan penataan patch cord rack server.',
                'created_by'  => $tlNet->id,
            ],
            [
                'title'       => 'Dokumentasi Denah Titik Access Point Kantor Cabang',
                'project_id'  => $projNet->id,
                'engineer_id' => $engNet1->id,
                'priority'    => 'Low',
                'deadline'    => now()->addDays(14), // Bulan Ini (Minggu ke-3)
                'progress'    => 0,
                'status'      => 'Assigned',
                'description' => 'Pemetaan coverage sinyal Wi-Fi di seluruh area kantor cabang.',
                'created_by'  => $tlNet->id,
            ],
            [
                'title'       => 'Sensus & Labeling Port Patch Panel Ruang Server',
                'project_id'  => $projNet->id,
                'engineer_id' => $engNet1->id,
                'priority'    => 'Medium',
                'deadline'    => now()->addDays(20), // Bulan Ini (Minggu ke-4)
                'progress'    => 20,
                'status'      => 'In Progress',
                'description' => 'Pemberian label numbering pada port switch dan kabel backbone.',
                'created_by'  => $tlNet->id,
            ],

            // Shiamsyah Azis (L1 Network) - 1 Minggu Ini, 1 Bulan Ini
            [
                'title'       => 'Instalasi & Mounting Router Mikrotik Baru',
                'project_id'  => $projNet->id,
                'engineer_id' => $engNet2->id,
                'priority'    => 'Medium',
                'deadline'    => now()->addDays(2), // Minggu Ini
                'progress'    => 40,
                'status'      => 'In Progress',
                'description' => 'Pemasangan router rackmount dan pengetesan power redundant.',
                'created_by'  => $tlNet->id,
            ],
            [
                'title'       => 'Penggantian Power Supply Cadangan Switch Core',
                'project_id'  => $projNet->id,
                'engineer_id' => $engNet2->id,
                'priority'    => 'Low',
                'deadline'    => now()->addDays(16), // Bulan Ini
                'progress'    => 0,
                'status'      => 'Assigned',
                'description' => 'Pemasangan modul redundant PSU pada switch distribusi utama.',
                'created_by'  => $tlNet->id,
            ],

            // Dedy Suryana (L2 Network) - 1 Minggu Ini, 2 Bulan Ini
            [
                'title'       => 'Konfigurasi BGP Routing & Inter-VLAN Gateway',
                'project_id'  => $projNet->id,
                'engineer_id' => $engNet3->id,
                'priority'    => 'High',
                'deadline'    => now()->addDays(1), // Minggu Ini
                'progress'    => 85,
                'status'      => 'In Progress',
                'description' => 'Setting peering BGP ke provider ISP 1 & ISP 2 dengan auto failover.',
                'created_by'  => $tlNet->id,
            ],
            [
                'title'       => 'Optimasi VPN IPsec Tunnel Antar Cabang',
                'project_id'  => $projNet->id,
                'engineer_id' => $engNet3->id,
                'priority'    => 'High',
                'deadline'    => now()->addDays(18), // Bulan Ini
                'progress'    => 15,
                'status'      => 'In Progress',
                'description' => 'Penyusunan route policy dan enkripsi AES-256 pada VPN tunnel.',
                'created_by'  => $tlNet->id,
            ],

            // Syaiful Amin (L2 Network) - 0 Minggu Ini, 2 Bulan Ini
            [
                'title'       => 'Testing Throughput & QoS Traffic Bandwidth',
                'project_id'  => $projNet->id,
                'engineer_id' => $engNet4->id,
                'priority'    => 'Medium',
                'deadline'    => now()->subDays(1),
                'progress'    => 100,
                'status'      => 'Completed',
                'description' => 'Pengujian bandwidth limiter dan prioritasi traffic VoIP & Core Database.',
                'created_by'  => $tlNet->id,
            ],
            [
                'title'       => 'Stress Test Failover SD-WAN Dual Provider',
                'project_id'  => $projNet->id,
                'engineer_id' => $engNet4->id,
                'priority'    => 'High',
                'deadline'    => now()->addDays(12), // Bulan Ini
                'progress'    => 10,
                'status'      => 'In Progress',
                'description' => 'Simulasi packet loss dan jitter testing pada controller SD-WAN.',
                'created_by'  => $tlNet->id,
            ],
            [
                'title'       => 'Audit Bandwidth Monitoring & Grafana Dashboard',
                'project_id'  => $projNet->id,
                'engineer_id' => $engNet4->id,
                'priority'    => 'Low',
                'deadline'    => now()->addDays(22), // Bulan Ini
                'progress'    => 0,
                'status'      => 'Assigned',
                'description' => 'Setup SNMP exporter untuk visualisasi latency dan utilisasi link.',
                'created_by'  => $tlNet->id,
            ],

            // Eka Kurnia (L1 Security) - 1 Minggu Ini, 2 Bulan Ini
            [
                'title'       => 'Audit Rule Firewall & Port Filtering Bank Mandiri',
                'project_id'  => $projSec->id,
                'engineer_id' => $engSec1->id,
                'priority'    => 'High',
                'deadline'    => now()->addDays(2), // Minggu Ini
                'progress'    => 50,
                'status'      => 'In Progress',
                'description' => 'Review rule akses inbound/outbound dan penutupan port non-standar.',
                'created_by'  => $tlSec->id,
            ],
            [
                'title'       => 'Penetration Testing Internal Segment Server',
                'project_id'  => $projSec->id,
                'engineer_id' => $engSec1->id,
                'priority'    => 'High',
                'deadline'    => now()->addDays(15), // Bulan Ini
                'progress'    => 25,
                'status'      => 'In Progress',
                'description' => 'Vulnerability scanning dan hardening OS server Linux/Windows.',
                'created_by'  => $tlSec->id,
            ],
            [
                'title'       => 'Update Database Antivirus & Threat Signature',
                'project_id'  => $projSec->id,
                'engineer_id' => $engSec1->id,
                'priority'    => 'Low',
                'deadline'    => now()->subDays(2),
                'progress'    => 100,
                'status'      => 'Completed',
                'description' => 'Pembaruan signature IPS dan anti-malware di Fortigate cluster.',
                'created_by'  => $tlSec->id,
            ],
        ];

        foreach ($tasksData as $td) {
            Task::updateOrCreate(
                ['title' => $td['title'], 'project_id' => $td['project_id']],
                $td
            );
        }

        // ============================================================
        // 7. SEED SCHEDULES
        // ============================================================
        $schedulesData = [
            [
                'title'       => 'Supervisi Lapangan & Testing Link Adira Pusat',
                'project_id'  => $projNet->id,
                'engineer_id' => $tlNet->id,
                'date'        => now()->toDateString(),
                'start_time'  => '09:00',
                'end_time'    => '15:30',
                'location'    => 'Kantor Pusat Adira Finance',
                'description' => 'Supervisi langsung implementasi SD-WAN dan arsitektur routing bersama tim client.',
                'created_by'  => $direktur->id,
            ],
            [
                'title'       => 'Inspeksi Firewall HA & Hardening Data Center Mandiri',
                'project_id'  => $projSec->id,
                'engineer_id' => $tlSec->id,
                'date'        => now()->toDateString(),
                'start_time'  => '08:30',
                'end_time'    => '16:00',
                'location'    => 'Data Center Plaza Mandiri',
                'description' => 'Briefing dan inspeksi implementasi firewall HA dan SIEM policy.',
                'created_by'  => $direktur->id,
            ],
            [
                'title'       => 'Maintenance Router & Switch Adira',
                'project_id'  => $projNet->id,
                'engineer_id' => $engNet1->id,
                'date'        => now()->toDateString(),
                'start_time'  => '08:30',
                'end_time'    => '14:00',
                'location'    => 'Site Adira Finance Pusat',
                'description' => 'Pengecekan perangkat jaringan dan monitoring traffic harian.',
                'created_by'  => $tlNet->id,
            ],
            [
                'title'       => 'Testing Peering BGP ISP',
                'project_id'  => $projNet->id,
                'engineer_id' => $engNet3->id,
                'date'        => now()->toDateString(),
                'start_time'  => '10:00',
                'end_time'    => '16:00',
                'location'    => 'NOC IP Net Solusindo',
                'description' => 'Simulasi failover link backbone.',
                'created_by'  => $tlNet->id,
            ],
            [
                'title'       => 'Security Log Review & Threat Check',
                'project_id'  => $projSec->id,
                'engineer_id' => $engSec1->id,
                'date'        => now()->toDateString(),
                'start_time'  => '09:00',
                'end_time'    => '15:00',
                'location'    => 'Data Center Bank Mandiri',
                'description' => 'Pemeriksaan log insiden firewall dan SIEM alert harian.',
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
        // 8. SEED ATTENDANCE
        // ============================================================
        $today = now()->toDateString();
        $allEngineers = [$tlNet, $tlSec, $engNet1, $engNet2, $engNet3, $engNet4, $engSec1];

        foreach ($allEngineers as $idx => $eng) {
            Attendance::updateOrCreate(
                ['user_id' => $eng->id, 'attendance_date' => $today, 'type' => 'clock_in'],
                [
                    'latitude'        => -6.1664,
                    'longitude'       => 106.8148,
                    'distance_meters' => 12 + $idx * 3,
                    'is_within_range' => true,
                    'address'         => 'Kantor Pusat PT IP Network Solusindo',
                    'note'            => 'Hadir tepat waktu bertugas di site/lapangan.',
                ]
            );
        }

        $this->command->info('✅ Seluruh Personel Resmi, Proyek, Tugas, Jadwal & Presensi Berhasil Dibuat!');
    }
}

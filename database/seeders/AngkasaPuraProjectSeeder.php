<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use App\Models\Division;
use App\Models\Team;
use App\Models\Client;
use App\Models\ProjectExpense;
use App\Models\EngineerActivityLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class AngkasaPuraProjectSeeder extends Seeder
{
    public function run(): void
    {
        // =========================================================================
        // 1. PASTIKAN USER-USER TERKAIT TERSEDIA (Sales Raiza, PM Rizki, Tim Lapangan)
        // =========================================================================
        $divNet = Division::where('code', 'NET')->first() 
            ?? Division::where('name', 'like', '%Network%')->first() 
            ?? Division::firstOrCreate(['name' => 'Divisi Network'], ['code' => 'NET', 'description' => 'Jaringan & Infrastruktur']);

        $teamNet = Team::where('division_id', $divNet->id)->first()
            ?? Team::firstOrCreate(['name' => 'Tim Network'], ['division_id' => $divNet->id, 'description' => 'Tim Network Engineering']);

        // Sales PIC: Raiza (Wajib sesuai permintaan user)
        $salesRaiza = User::where('email', 'raiza@ipnetsolusindo.com')
            ->orWhere('name', 'like', '%Raiza%')
            ->first();
        if (!$salesRaiza) {
            $salesRaiza = User::create([
                'name'     => 'Raiza',
                'email'    => 'raiza@ipnetsolusindo.com',
                'password' => Hash::make('password123'),
                'phone'    => '08111000025',
                'position' => 'Sales (Account Manager)',
                'status'   => 'Active',
            ]);
        }
        if (method_exists($salesRaiza, 'syncRoles')) {
            $salesRaiza->syncRoles(['Sales']);
        }

        // Project Manager: Rizki
        $pmRizki = User::where('email', 'rizki@ipnetsolusindo.com')
            ->orWhere('name', 'like', '%Rizki%')
            ->first();
        if (!$pmRizki) {
            $pmRizki = User::create([
                'name'     => 'Rizki',
                'email'    => 'rizki@ipnetsolusindo.com',
                'password' => Hash::make('password123'),
                'phone'    => '08111000010',
                'position' => 'Project Manager',
                'status'   => 'Active',
            ]);
        }
        if (method_exists($pmRizki, 'syncRoles')) {
            $pmRizki->syncRoles(['Project Manager', 'PMO']);
        }

        // Lead Engineer: Nugraha Pratama
        $leadNugraha = User::where('email', 'nugraha@ipnetsolusindo.com')
            ->orWhere('name', 'like', '%Nugraha%')
            ->first();
        if (!$leadNugraha) {
            $leadNugraha = User::create([
                'name'        => 'Nugraha Pratama',
                'email'       => 'nugraha@ipnetsolusindo.com',
                'password'    => Hash::make('password123'),
                'phone'       => '08111000003',
                'position'    => 'Lead Network Engineer',
                'status'      => 'Active',
                'division_id' => $divNet->id,
                'team_id'     => $teamNet->id,
            ]);
        }
        if (method_exists($leadNugraha, 'syncRoles')) {
            $leadNugraha->syncRoles(['Lead Engineer', 'Engineer']);
        }

        // Engineer 1: Syaiful Amin (PIC Lapangan)
        $engSyaiful = User::where('email', 'syaiful@ipnetsolusindo.com')
            ->orWhere('name', 'like', '%Syaiful%')
            ->first();
        if (!$engSyaiful) {
            $engSyaiful = User::create([
                'name'        => 'Syaiful Amin',
                'email'       => 'syaiful@ipnetsolusindo.com',
                'password'    => Hash::make('password123'),
                'phone'       => '08111000008',
                'position'    => 'L2 Network Engineer',
                'status'      => 'Active',
                'division_id' => $divNet->id,
                'team_id'     => $teamNet->id,
            ]);
        }
        if (method_exists($engSyaiful, 'syncRoles')) {
            $engSyaiful->syncRoles(['Network Engineer', 'Engineer']);
        }

        // Engineer 2: Raihan Ghiffary (Personel Lapangan)
        $engRaihan = User::where('email', 'raihan@ipnetsolusindo.com')
            ->orWhere('name', 'like', '%Raihan%')
            ->first();
        if (!$engRaihan) {
            $engRaihan = User::create([
                'name'        => 'Raihan Ghiffary',
                'email'       => 'raihan@ipnetsolusindo.com',
                'password'    => Hash::make('password123'),
                'phone'       => '08111000029',
                'position'    => 'Field Network Engineer',
                'status'      => 'Active',
                'division_id' => $divNet->id,
                'team_id'     => $teamNet->id,
            ]);
        }
        if (method_exists($engRaihan, 'syncRoles')) {
            $engRaihan->syncRoles(['Network Engineer', 'Engineer']);
        }

        // Stakeholders Approval Lainnya
        $direktur = User::where('email', 'hariyadi@ipnetsolusindo.com')->orWhere('name', 'like', '%Hariyadi%')->first();
        $divHead  = User::where('email', 'susanto@ipnetsolusindo.com')->orWhere('name', 'like', '%Susanto%')->first();
        $presales = User::where('email', 'akbar@ipnetsolusindo.com')->orWhere('name', 'like', '%Akbar%')->first();
        $sa       = User::where('email', 'aris@ipnetsolusindo.com')->orWhere('name', 'like', '%Aris%')->first();
        $bdm      = User::where('email', 'kurnijanto.edy@ipnetsolusindo.com')->orWhere('name', 'like', '%Kurnijanto%')->first();

        // =========================================================================
        // 2. KLIEN ANGKASA PURA
        // =========================================================================
        $clientAps = Client::updateOrCreate(
            ['name' => 'PT Angkasa Pura Support'],
            [
                'department' => 'Airport Technology & Information Communication (ICT)',
                'pic_name'   => 'Bpk. Wayan Koster / Tim ICT Bandara Ngurah Rai',
                'phone'      => '0361-9351011',
                'email'      => 'ict.aps@angkasapura1.co.id',
                'address'    => 'Gedung Administrasi Bandar Udara Internasional I Gusti Ngurah Rai, Tuban, Kuta, Badung, Bali 80361',
                'notes'      => 'Implementasi Perangkat Network Bandara IGNR Denpasar: 108 switch distribusi & akses, Fortinet firewall, TACACS AAA.',
                'created_by' => $salesRaiza->id,
            ]
        );

        // =========================================================================
        // 3. CHECKLIST LENGKAP SERAH TERIMA SALES KE PM (11 CHECKLIST STANDARD IPNET)
        // =========================================================================
        $checklistAps = [
            'sales_confirmation_signed' => ['checked' => true, 'note' => 'SC No: SC/IPNET/2026/088 disetujui Head Division (Susanto Djaya) & Director (Hariyadi)'],
            'contract_signed'           => ['checked' => true, 'note' => 'SPK No: SPK-042/APS-DPS/IX/2026 senilai Rp 1.485.000.000 ditandatangani'],
            'bom_proposal'              => ['checked' => true, 'note' => 'BOM Perangkat Network: 108 Unit Switch Modular (Distribusi & Akses), Fortinet Firewall, Server TACACS disusun SA Aris Sadewo & Presales Akbar'],
            'negotiation_addendum'      => ['checked' => true, 'note' => 'BA Negosiasi Scope Pekerjaan 56 Hari di Bandara IGNR Denpasar disetujui BDM Kurnijanto Edy & Sales Raiza'],
            'sow_document'              => ['checked' => true, 'note' => 'SOW Implementasi Perangkat Network Bandara: Staging, Instalasi, Migrasi, Standarisasi IP, Reconfig Firewall Fortinet, TACACS, Testing & BAST'],
            'urs_requirement'           => ['checked' => true, 'note' => 'Spesifikasi Teknis Bandara: Redundancy Core-Distribution-Access, Port Optik, Integrasi TACACS, Standarisasi IP APINDO/ELBAN'],
            'mockup_wireframe'          => ['checked' => true, 'note' => 'Topologi Jaringan Bandara IGNR Denpasar: Terminal Domestik, Terminal Internasional, dan Non Terminal disetujui'],
            'maintenance_contract'      => ['checked' => true, 'note' => 'Garansi Pengerjaan & Implementasi Deliverable sesuai SPK (Murni One-Time Project Delivery)'],
            'dp_payment_proof'          => ['checked' => true, 'note' => 'Bukti Transfer Pembayaran Termin 1 DP 30% Terverifikasi Finance'],
            'billing_milestone'         => ['checked' => true, 'note' => 'Termin 1 (DP 30%), Termin 2 (Delivery & Staging 30%), Termin 3 (Migrasi & Cutover 20%), Termin 4 (BAST Final 20%)'],
            'customer_contacts'         => ['checked' => true, 'note' => 'PIC APS: Bpk. Wayan Koster (Head of ICT Bandara IGNR Denpasar - 081238991122)'],
            'meeting_notes'             => ['checked' => true, 'note' => 'Kick-off meeting, koordinasi izin PASS Bandara, dan joint inspection disepakati bersama Sales Raiza & PM Rizki'],
        ];

        // 23 Riil Item Pengeluaran dari Budget & Costing PDF 3 (Total Rp 57.878.006)
        $expensesData = [
            [
                'id'           => 1,
                'category'     => 'Site Survey',
                'amount'       => 3733500,
                'description'  => 'Joint Inspection 19-22 Agustus 2026',
                'expense_date' => '2026-08-22',
                'creator_name' => 'Nugraha Pratama',
            ],
            [
                'id'           => 2,
                'category'     => 'Accommodation',
                'amount'       => 1665000,
                'description'  => 'Hotel Joint Inspection Nugraha dan Syaiful',
                'expense_date' => '2026-08-18',
                'creator_name' => 'Nugraha Pratama',
            ],
            [
                'id'           => 3,
                'category'     => 'Transport',
                'amount'       => 3750000,
                'description'  => 'Tiket JI: Nugraha dan Syaiful : HLP - DPS',
                'expense_date' => '2026-08-18',
                'creator_name' => 'Nugraha Pratama',
            ],
            [
                'id'           => 4,
                'category'     => 'Transport',
                'amount'       => 3970000,
                'description'  => 'Tiket JI: Nugraha dan Syaiful : DPS - CGK',
                'expense_date' => '2026-08-21',
                'creator_name' => 'Nugraha Pratama',
            ],
            [
                'id'           => 5,
                'category'     => 'Accommodation',
                'amount'       => 1000000,
                'description'  => 'Deposit Kamar (2 kamar)',
                'expense_date' => '2026-09-10',
                'creator_name' => 'Rizki',
            ],
            [
                'id'           => 6,
                'category'     => 'Accommodation',
                'amount'       => 24107106,
                'description'  => 'Sewa 2 Kamar Posko Tim Engineer Denpasar',
                'expense_date' => '2026-09-16',
                'creator_name' => 'Rizki',
            ],
            [
                'id'           => 7,
                'category'     => 'Others',
                'amount'       => 938000,
                'description'  => 'Entertaint Tim APS (Koordinasi Teknis Lapangan)',
                'expense_date' => '2026-09-16',
                'creator_name' => 'Rizki',
            ],
            [
                'id'           => 8,
                'category'     => 'Transport',
                'amount'       => 4890000,
                'description'  => 'Tiket Rizki, Syaiful, Raihan : JKT - DPS',
                'expense_date' => '2026-09-21',
                'creator_name' => 'Rizki',
            ],
            [
                'id'           => 9,
                'category'     => 'Transport',
                'amount'       => 1774000,
                'description'  => 'Tiket Rizki : DPS - JKT',
                'expense_date' => '2026-09-21',
                'creator_name' => 'Rizki',
            ],
            [
                'id'           => 10,
                'category'     => 'Utilities',
                'amount'       => 403000,
                'description'  => 'Token Listrik 2 Kamar Posko',
                'expense_date' => '2026-09-22',
                'creator_name' => 'Syaiful Amin',
            ],
            [
                'id'           => 11,
                'category'     => 'Transport',
                'amount'       => 520700,
                'description'  => 'Parkir Operasional Lapangan (22 - 28 Sept)',
                'expense_date' => '2026-09-28',
                'creator_name' => 'Syaiful Amin',
            ],
            [
                'id'           => 12,
                'category'     => 'Transport',
                'amount'       => 329700,
                'description'  => 'Bensin Kendaraan Operasional Lapangan',
                'expense_date' => '2026-09-24',
                'creator_name' => 'Raihan Ghiffary',
            ],
            [
                'id'           => 13,
                'category'     => 'Permits',
                'amount'       => 210000,
                'description'  => 'PASS Bandara Bulanan 3 Pax',
                'expense_date' => '2026-09-28',
                'creator_name' => 'Syaiful Amin',
            ],
            [
                'id'           => 14,
                'category'     => 'Transport',
                'amount'       => 475000,
                'description'  => 'Parkir Bandara (29 Sept - 1 Oct)',
                'expense_date' => '2026-10-01',
                'creator_name' => 'Syaiful Amin',
            ],
            [
                'id'           => 15,
                'category'     => 'Transport',
                'amount'       => 350000,
                'description'  => 'Bensin Operasional Lapangan',
                'expense_date' => '2026-10-05',
                'creator_name' => 'Raihan Ghiffary',
            ],
            [
                'id'           => 16,
                'category'     => 'Utilities',
                'amount'       => 400000,
                'description'  => 'Token Listrik 2 Kamar Posko',
                'expense_date' => '2026-10-09',
                'creator_name' => 'Syaiful Amin',
            ],
            [
                'id'           => 17,
                'category'     => 'Meals',
                'amount'       => 500000,
                'description'  => 'Uang Makan PM Rizki (22 - 26 Sept)',
                'expense_date' => '2026-09-26',
                'creator_name' => 'Rizki',
            ],
            [
                'id'           => 18,
                'category'     => 'Meals',
                'amount'       => 2600000,
                'description'  => 'Uang Makan Syaiful (Rp 1.300.000) & Raihan (Rp 1.300.000) (22 Sept - 4 Oct)',
                'expense_date' => '2026-10-04',
                'creator_name' => 'Syaiful Amin',
            ],
            [
                'id'           => 19,
                'category'     => 'Meals',
                'amount'       => 3400000,
                'description'  => 'Uang Makan Syaiful (Rp 1.700.000) & Raihan (Rp 1.700.000) (5 - 21 Oct)',
                'expense_date' => '2026-10-21',
                'creator_name' => 'Syaiful Amin',
            ],
            [
                'id'           => 20,
                'category'     => 'Transport',
                'amount'       => 1512000,
                'description'  => 'Tiket Nugraha : JKT - DPS',
                'expense_date' => '2026-10-07',
                'creator_name' => 'Nugraha Pratama',
            ],
            [
                'id'           => 21,
                'category'     => 'Others',
                'amount'       => 50000,
                'description'  => 'Kirim Dokumen Teknis Bandara',
                'expense_date' => '2026-10-09',
                'creator_name' => 'Syaiful Amin',
            ],
            [
                'id'           => 22,
                'category'     => 'Per Diem',
                'amount'       => 500000,
                'description'  => 'Uang Saku PM Rizki (22 - 26 Sept)',
                'expense_date' => '2026-10-09',
                'creator_name' => 'Rizki',
            ],
            [
                'id'           => 23,
                'category'     => 'Meals',
                'amount'       => 800000,
                'description'  => 'Uang Makan Nugraha (13 - 21 Oct)',
                'expense_date' => '2026-10-12',
                'creator_name' => 'Nugraha Pratama',
            ],
        ];

        // S-Curve Weekly Tracking (Sesuai 56 Hari / 8 Minggu)
        // Saat ini berada di Minggu ke-3 dengan Actual Kumulatif: 19.82%
        $sCurveWeekly = [
            1 => ['plan' => 5.5,  'actual' => 5.0,   'notes' => 'Staging awal 26 unit switch dan survey port optik'],
            2 => ['plan' => 14.0, 'actual' => 10.2,  'notes' => 'Staging lanjutan 57 unit switch, relokasi ruang buggy'],
            3 => ['plan' => 28.5, 'actual' => 19.82, 'notes' => 'Total 106 switch selesai staging, instalasi access & distribution 5%'],
        ];

        // =========================================================================
        // 4. BUAT / UPDATE PROYEK ANGKASA PURA
        // =========================================================================
        $projectStartDate = Carbon::create(2026, 9, 21);
        $projectDeadline  = Carbon::create(2026, 11, 16); // 56 hari durasi

        $project = Project::updateOrCreate(
            ['name' => 'Implementasi Perangkat Network Bandara I Gusti Ngurah Rai (IGNR) Denpasar'],
            [
                'client'                        => 'PT Angkasa Pura Support',
                'sales_name'                    => $salesRaiza->name,
                'created_by'                    => $salesRaiza->id,
                'pm_id'                         => $pmRizki->id,
                'division_id'                   => $divNet->id,
                'stage'                         => 'Deliver',
                'status'                        => 'In Progress',
                'process_status'                => 'In Progress',
                'sales_stage'                   => 'Closed Won',
                'project_type'                  => 'One-Time Project', // Murni proyek implementasi saja
                'sla_tier'                      => 'Gold',
                'location'                      => 'Bandara Internasional I Gusti Ngurah Rai (IGNR) Denpasar, Bali',
                'start_date'                    => $projectStartDate->format('Y-m-d'),
                'deadline'                      => $projectDeadline->format('Y-m-d'),
                'progress'                      => 19.82, // Persis PDF 1 page 4
                'contract_value'                => 1485000000,
                'po_spk_number'                 => 'SPK-042/APS-DPS/IX/2026',
                'po_spk_date'                   => '2026-09-18',
                'quotation_number'              => 'QUO-IPNET-2026-088',
                'quotation_amount'              => 1485000000,
                'billing_terms'                 => 'Termin 1 (DP 30% Terbayar), Termin 2 (Delivery & Staging 30%), Termin 3 (Migrasi & Cutover 20%), Termin 4 (BAST Final 20%)',
                'commercial_terms'              => 'Termasuk pengadaan 108 switch modular, rack mounting, kabel optik patch cord, konfigurasi firewall Fortinet, TACACS AAA, serta pendampingan uji komisioning dan BAST.',
                'sla_commitment'                => 'Dukungan On-site selama 56 hari kalender implementasi di Bandara IGNR Denpasar, koordinasi teknis bersama APS dan APINDO.',
                'handover_target'               => 'pmo',
                'handover_status'               => 'Approved',
                'handover_submitted_at'         => Carbon::create(2026, 9, 19, 10, 0, 0),
                'handover_approved_at'          => Carbon::create(2026, 9, 20, 14, 30, 0),
                'handover_approved_by'          => $divHead ? $divHead->id : null,
                'commercial_handover_status'    => 'Approved',
                'commercial_handover_at'        => Carbon::create(2026, 9, 20, 15, 0, 0),
                'commercial_handover_by'        => $direktur ? $direktur->id : null,
                'presales_name'                 => $presales ? $presales->name : 'Akbar',
                'presales_status'               => 'Approved',
                'initial_requirement'           => 'Kebutuhan modernisasi dan standarisasi infrastruktur jaringan Bandara Internasional I Gusti Ngurah Rai Denpasar mencakup staging 108 unit switch, instalasi distribution switch & access switch di Terminal Domestik, Internasional, dan Non Terminal, standarisasi IP, rekonfigurasi Firewall Fortinet, dan TACACS.',
                'customer_pic_technical'        => 'Bpk. Wayan Koster (Head of ICT Bandara IGNR Denpasar - 081238991122)',
                'customer_pic_business'         => 'Ibu Ni Luh Putu (Procurement & Commercial APS - 081239884422)',
                'customer_pic_finance'          => 'Bpk. Ketut Ardhana (Finance & Budgeting APS)',
                'bdm_id'                        => $bdm ? $bdm->id : null,
                'bdm_handover_status'           => 'Approved',
                'bdm_handover_at'               => Carbon::create(2026, 9, 19, 11, 0, 0),
                'special_notes'                 => 'Pekerjaan dilakukan di area restricted bandara (Terminal Domestik, Internasional, Ruang ICT, Buggy Room). Seluruh personel wajib mengantongi PASS Bandara aktif.',
                'stakeholders_data'             => [
                    'sales'        => ['name' => $salesRaiza->name, 'role' => 'Account Manager', 'status' => 'Submitted SC'],
                    'approver_hd'  => ['name' => $divHead ? $divHead->name : 'Susanto Djaya', 'role' => 'Division Head', 'status' => 'Verified & Approved'],
                    'approver_dir' => ['name' => $direktur ? $direktur->name : 'Hariyadi', 'role' => 'Director', 'status' => 'Validated & Signed'],
                    'presales'     => ['name' => $presales ? $presales->name : 'Akbar', 'role' => 'Pre-Sales Specialist', 'status' => 'Proposal Approved'],
                    'sa'           => ['name' => $sa ? $sa->name : 'Aris Sadewo', 'role' => 'Solution Architect', 'status' => 'BoQ & Topology Certified'],
                    'bdm'          => ['name' => $bdm ? $bdm->name : 'Kurnijanto Edy', 'role' => 'Business Development Manager', 'status' => 'Commercial Contract Signed'],
                    'pm'           => ['name' => $pmRizki->name, 'role' => 'Project Manager', 'status' => 'Delivery Lead'],
                    'lead_eng'     => ['name' => $leadNugraha->name, 'role' => 'Lead Engineer', 'status' => 'Engineering Delegator'],
                ],
                'handover_data'                 => array_merge($checklistAps, [
                    'assigned_engineers' => [
                        [
                            'id'          => $engSyaiful->id,
                            'name'        => $engSyaiful->name,
                            'email'       => $engSyaiful->email,
                            'position'    => 'L2 Network Engineer',
                            'role'        => 'PIC Lapangan / Staging & Standarisasi IP',
                            'division'    => 'Divisi Network',
                            'assigned_at' => '2026-09-21 09:00',
                            'assigned_by' => $leadNugraha->name,
                        ],
                        [
                            'id'          => $engRaihan->id,
                            'name'        => $engRaihan->name,
                            'email'       => $engRaihan->email,
                            'position'    => 'Field Network Engineer',
                            'role'        => 'Personel Lapangan / Staging Switch Access & TACACS',
                            'division'    => 'Divisi Network',
                            'assigned_at' => '2026-09-21 09:00',
                            'assigned_by' => $leadNugraha->name,
                        ],
                        [
                            'id'          => $leadNugraha->id,
                            'name'        => $leadNugraha->name,
                            'email'       => $leadNugraha->email,
                            'position'    => 'Lead Network Engineer',
                            'role'        => 'Lead Engineer / Joint Inspection & Quality Assurance',
                            'division'    => 'Divisi Network',
                            'assigned_at' => '2026-09-21 09:00',
                            'assigned_by' => $leadNugraha->name,
                        ],
                    ],
                    'team_delegation_notes'   => 'Tim engineering resmi ditugaskan oleh Lead Engineer Nugraha Pratama. Syaiful Amin dan Raihan Ghiffary bertindak sebagai teknisi utama di Bandara IGNR Denpasar, memegang tanggung jawab staging 108 switch, pemasangan fisik kabinet IDF/MDF, dan standarisasi IP. Lead Engineer Nugraha Pratama mendampingi pemeriksaan teknis (joint inspection) bersama APINDO & APS serta mengawal pengujian komisioning.',
                    'team_delegated_at'       => '2026-09-21 09:00',
                    'team_delegated_by_id'    => $leadNugraha->id,
                    'team_delegated_by_name'  => $leadNugraha->name . ' (Team Leader Network Engineering)',
                    'expenses'                => $expensesData,
                    's_curve_weekly'          => $sCurveWeekly,
                ]),
                'documents_checklist'           => $checklistAps,
            ]
        );

        // =========================================================================
        // 5. ISI TABEL PROJECT_EXPENSES (JIKA TABEL TERSEDIA)
        // =========================================================================
        if (Schema::hasTable('project_expenses')) {
            ProjectExpense::where('project_id', $project->id)->delete();
            foreach ($expensesData as $exp) {
                ProjectExpense::create([
                    'project_id'   => $project->id,
                    'category'     => $exp['category'],
                    'amount'       => $exp['amount'],
                    'description'  => $exp['description'],
                    'expense_date' => $exp['expense_date'],
                    'created_by'   => $pmRizki->id,
                ]);
            }
        }

        // =========================================================================
        // 6. TASKS / WBS PROYEK SESUAI DOKUMEN PDF 1 & PDF 2
        // =========================================================================
        Task::where('project_id', $project->id)->forceDelete();

        $tasks = [
            // No 1. Stagging (15 Hari: Day 1 - 15)
            [
                'title'       => 'Stagging Perangkat Network (106/108 Switch Selesai)',
                'engineer_id' => $engSyaiful->id,
                'priority'    => 'High',
                'status'      => 'In Progress',
                'progress'    => 98,
                'start_date'  => $projectStartDate->copy()->addDays(0)->format('Y-m-d'),
                'deadline'    => $projectStartDate->copy()->addDays(14)->format('Y-m-d 18:00:00'),
                'description' => 'Staging perangkat switch modular di Ruang Buggy APS. 106 dari 108 unit switch telah selesai distagging (12 Akses, 14 Distribusi, dan unit Leaf/Distribution). [Bobot: 15.46% | Realisasi: 98.15% | Kontribusi: 15.18%]',
                'created_by'  => $pmRizki->id,
            ],

            // No 2. Instalasi Distribution Switch - Area Domestik (1 Hari: Day 16)
            [
                'title'       => 'Instalasi Distribution Switch - Area Domestik',
                'engineer_id' => $engSyaiful->id,
                'priority'    => 'High',
                'status'      => 'In Progress',
                'progress'    => 5,
                'start_date'  => $projectStartDate->copy()->addDays(15)->format('Y-m-d'),
                'deadline'    => $projectStartDate->copy()->addDays(15)->format('Y-m-d 18:00:00'),
                'description' => 'Pemasangan unit Distribution Switch pada rak IDF/MDF Terminal Domestik Bandara IGNR Denpasar. [Bobot: 1.03% | Realisasi: 5.00% | Kontribusi: 0.05%]',
                'created_by'  => $pmRizki->id,
            ],

            // No 3. Instalasi Distribution Switch - Area Internasional (2 Hari: Day 17 - 18)
            [
                'title'       => 'Instalasi Distribution Switch - Area Internasional',
                'engineer_id' => $engSyaiful->id,
                'priority'    => 'High',
                'status'      => 'In Progress',
                'progress'    => 5,
                'start_date'  => $projectStartDate->copy()->addDays(16)->format('Y-m-d'),
                'deadline'    => $projectStartDate->copy()->addDays(17)->format('Y-m-d 18:00:00'),
                'description' => 'Pemasangan Distribution Switch di ruang rack Terminal Internasional. [Bobot: 2.06% | Realisasi: 5.00% | Kontribusi: 0.10%]',
                'created_by'  => $pmRizki->id,
            ],

            // No 4. Instalasi Distribution Switch - Non Terminal (2 Hari: Day 19 - 20)
            [
                'title'       => 'Instalasi Distribution Switch - Non Terminal',
                'engineer_id' => $engSyaiful->id,
                'priority'    => 'Medium',
                'status'      => 'In Progress',
                'progress'    => 5,
                'start_date'  => $projectStartDate->copy()->addDays(18)->format('Y-m-d'),
                'deadline'    => $projectStartDate->copy()->addDays(19)->format('Y-m-d 18:00:00'),
                'description' => 'Pemasangan unit Distribution Switch di gedung perkantoran & area kargo Non Terminal. [Bobot: 2.06% | Realisasi: 5.00% | Kontribusi: 0.10%]',
                'created_by'  => $pmRizki->id,
            ],

            // No 5. Instalasi Switch Access - Area Domestik (5 Hari: Day 16 - 20)
            [
                'title'       => 'Instalasi Switch Access - Area Domestik',
                'engineer_id' => $engRaihan->id,
                'priority'    => 'High',
                'status'      => 'In Progress',
                'progress'    => 5,
                'start_date'  => $projectStartDate->copy()->addDays(15)->format('Y-m-d'),
                'deadline'    => $projectStartDate->copy()->addDays(19)->format('Y-m-d 18:00:00'),
                'description' => 'Pemasangan Switch Access ke kabinet IDF di gate & ruang tunggu Terminal Domestik. [Bobot: 5.15% | Realisasi: 5.00% | Kontribusi: 0.26%]',
                'created_by'  => $pmRizki->id,
            ],

            // No 6. Instalasi Switch Access - Area Internasional (5 Hari: Day 16 - 20)
            [
                'title'       => 'Instalasi Switch Access - Area Internasional',
                'engineer_id' => $engRaihan->id,
                'priority'    => 'High',
                'status'      => 'In Progress',
                'progress'    => 5,
                'start_date'  => $projectStartDate->copy()->addDays(15)->format('Y-m-d'),
                'deadline'    => $projectStartDate->copy()->addDays(19)->format('Y-m-d 18:00:00'),
                'description' => 'Pemasangan Switch Access di Terminal Internasional. [Bobot: 5.15% | Realisasi: 5.00% | Kontribusi: 0.26%]',
                'created_by'  => $pmRizki->id,
            ],

            // No 7. Konfigurasi Lanjutan (2 Hari: Day 21 - 22)
            [
                'title'       => 'Konfigurasi Lanjutan Instalasi Perangkat Network',
                'engineer_id' => $engSyaiful->id,
                'priority'    => 'Medium',
                'status'      => 'Assigned',
                'progress'    => 0,
                'start_date'  => $projectStartDate->copy()->addDays(20)->format('Y-m-d'),
                'deadline'    => $projectStartDate->copy()->addDays(21)->format('Y-m-d 18:00:00'),
                'description' => 'Setup VLAN trunking, Spanning Tree Rapid-PVST, Port-Channel LACP, dan hardening port security. [Bobot: 2.06%]',
                'created_by'  => $pmRizki->id,
            ],

            // No 8. Integrasi Perangkat Network (1 Hari: Day 23)
            [
                'title'       => 'Integrasi Perangkat Network Bandara (Milestone Fase Selesai)',
                'engineer_id' => $engRaihan->id,
                'priority'    => 'Medium',
                'status'      => 'Assigned',
                'progress'    => 0,
                'start_date'  => $projectStartDate->copy()->addDays(22)->format('Y-m-d'),
                'deadline'    => $projectStartDate->copy()->addDays(22)->format('Y-m-d 18:00:00'),
                'description' => 'Uji integrasi link uplink optik Core menuju Distribution Switch. [Bobot: 1.03%]',
                'created_by'  => $pmRizki->id,
            ],

            // No 9. Migrasi Distribution Switch - Area Domestik (1 Hari: Day 24)
            [
                'title'       => 'Migrasi Distribution Switch - Area Domestik',
                'engineer_id' => $engSyaiful->id,
                'priority'    => 'High',
                'status'      => 'Assigned',
                'progress'    => 0,
                'start_date'  => $projectStartDate->copy()->addDays(23)->format('Y-m-d'),
                'deadline'    => $projectStartDate->copy()->addDays(23)->format('Y-m-d 18:00:00'),
                'description' => 'Cutover kabel backbone dan pemindahan gateway segment Domestik. [Bobot: 1.03%]',
                'created_by'  => $pmRizki->id,
            ],

            // No 10. Migrasi Distribution Switch - Area Internasional (2 Hari: Day 25 - 26)
            [
                'title'       => 'Migrasi Distribution Switch - Area Internasional',
                'engineer_id' => $engSyaiful->id,
                'priority'    => 'High',
                'status'      => 'Assigned',
                'progress'    => 0,
                'start_date'  => $projectStartDate->copy()->addDays(24)->format('Y-m-d'),
                'deadline'    => $projectStartDate->copy()->addDays(25)->format('Y-m-d 18:00:00'),
                'description' => 'Cutover traffic data Terminal Internasional pada maintenance window malam. [Bobot: 2.06%]',
                'created_by'  => $pmRizki->id,
            ],

            // No 11. Migrasi Distribution Switch - Non Terminal (4 Hari: Day 27 - 30)
            [
                'title'       => 'Migrasi Distribution Switch - Non Terminal',
                'engineer_id' => $engSyaiful->id,
                'priority'    => 'Medium',
                'status'      => 'Assigned',
                'progress'    => 0,
                'start_date'  => $projectStartDate->copy()->addDays(26)->format('Y-m-d'),
                'deadline'    => $projectStartDate->copy()->addDays(29)->format('Y-m-d 18:00:00'),
                'description' => 'Migrasi link distribusi kargo dan gedung kantor APS. [Bobot: 4.12%]',
                'created_by'  => $pmRizki->id,
            ],

            // No 12. Migrasi Switch Access - Area Domestik (5 Hari: Day 31 - 35)
            [
                'title'       => 'Migrasi Switch Access - Area Domestik',
                'engineer_id' => $engRaihan->id,
                'priority'    => 'High',
                'status'      => 'Assigned',
                'progress'    => 0,
                'start_date'  => $projectStartDate->copy()->addDays(30)->format('Y-m-d'),
                'deadline'    => $projectStartDate->copy()->addDays(34)->format('Y-m-d 18:00:00'),
                'description' => 'Pemindahan patch cord end-user dan AP WiFi Terminal Domestik ke Switch Access baru. [Bobot: 5.15%]',
                'created_by'  => $pmRizki->id,
            ],

            // No 13. Migrasi Switch Access - Area Internasional (10 Hari: Day 36 - 45)
            [
                'title'       => 'Migrasi Switch Access - Area Internasional (Fase Migrasi Selesai)',
                'engineer_id' => $engRaihan->id,
                'priority'    => 'High',
                'status'      => 'Assigned',
                'progress'    => 0,
                'start_date'  => $projectStartDate->copy()->addDays(35)->format('Y-m-d'),
                'deadline'    => $projectStartDate->copy()->addDays(44)->format('Y-m-d 18:00:00'),
                'description' => 'Migrasi bertahap seluruh switch akses area Terminal Internasional. [Bobot: 10.31%]',
                'created_by'  => $pmRizki->id,
            ],

            // No 14. Standarisasi IP - Konfigurasi IP (15 Hari: Day 24 - 38)
            [
                'title'       => 'Standarisasi IP & Konfigurasi IP (PID / MOP)',
                'engineer_id' => $engSyaiful->id,
                'priority'    => 'High',
                'status'      => 'In Progress',
                'progress'    => 25,
                'start_date'  => $projectStartDate->copy()->addDays(23)->format('Y-m-d'),
                'deadline'    => $projectStartDate->copy()->addDays(37)->format('Y-m-d 18:00:00'),
                'description' => 'Pengambilan data merk perangkat dan lokasi untuk pembuatan PID/MOP standarisasi IP. Menunggu verifikasi alokasi IP baru dari ELBAN/APINDO. [Bobot: 15.46% | Realisasi: 25.00% | Kontribusi: 3.87%]',
                'created_by'  => $pmRizki->id,
            ],

            // No 15. Verifikasi Standarisasi IP (2 Hari: Day 39 - 40)
            [
                'title'       => 'Verifikasi Standarisasi IP (Fase Standarisasi Selesai)',
                'engineer_id' => $engSyaiful->id,
                'priority'    => 'Medium',
                'status'      => 'Assigned',
                'progress'    => 0,
                'start_date'  => $projectStartDate->copy()->addDays(38)->format('Y-m-d'),
                'deadline'    => $projectStartDate->copy()->addDays(39)->format('Y-m-d 18:00:00'),
                'description' => 'Pengecekan konsistensi subnet IP management, monitoring NMS, dan routing table. [Bobot: 2.06%]',
                'created_by'  => $pmRizki->id,
            ],

            // No 16. Reconfig Firewall Fortinet - Instalasi & Konfigurasi (2 Hari: Day 24 - 25)
            [
                'title'       => 'Reconfig Firewall Fortinet - Instalasi dan Konfigurasi',
                'engineer_id' => $engSyaiful->id,
                'priority'    => 'High',
                'status'      => 'Assigned',
                'progress'    => 0,
                'start_date'  => $projectStartDate->copy()->addDays(23)->format('Y-m-d'),
                'deadline'    => $projectStartDate->copy()->addDays(24)->format('Y-m-d 18:00:00'),
                'description' => 'Rekonfigurasi security zone, policy firewall perimeter, dan virtual interface pada FortiGate. [Bobot: 2.06%]',
                'created_by'  => $pmRizki->id,
            ],

            // No 17. Reconfig Firewall Fortinet - Integrasi (1 Hari: Day 26)
            [
                'title'       => 'Reconfig Firewall Fortinet - Integrasi (Fase Firewall Selesai)',
                'engineer_id' => $engSyaiful->id,
                'priority'    => 'Medium',
                'status'      => 'Assigned',
                'progress'    => 0,
                'start_date'  => $projectStartDate->copy()->addDays(25)->format('Y-m-d'),
                'deadline'    => $projectStartDate->copy()->addDays(25)->format('Y-m-d 18:00:00'),
                'description' => 'Uji filtering rule firewall dan integrasi dengan Core Switch bandara. [Bobot: 1.03%]',
                'created_by'  => $pmRizki->id,
            ],

            // No 18. Implementasi TACACS - Instalasi & Konfigurasi (5 Hari: Day 24 - 28)
            [
                'title'       => 'Implementasi TACACS - Instalasi dan Konfigurasi Server',
                'engineer_id' => $engRaihan->id,
                'priority'    => 'Medium',
                'status'      => 'Assigned',
                'progress'    => 0,
                'start_date'  => $projectStartDate->copy()->addDays(23)->format('Y-m-d'),
                'deadline'    => $projectStartDate->copy()->addDays(27)->format('Y-m-d 18:00:00'),
                'description' => 'Instalasi server TACACS+ dan pembuatan database user administrator. [Bobot: 5.15%]',
                'created_by'  => $pmRizki->id,
            ],

            // No 19. Implementasi TACACS - Konfigurasi Lanjutan (2 Hari: Day 29 - 30)
            [
                'title'       => 'Implementasi TACACS - Konfigurasi Lanjutan AAA',
                'engineer_id' => $engRaihan->id,
                'priority'    => 'Medium',
                'status'      => 'Assigned',
                'progress'    => 0,
                'start_date'  => $projectStartDate->copy()->addDays(28)->format('Y-m-d'),
                'deadline'    => $projectStartDate->copy()->addDays(29)->format('Y-m-d 18:00:00'),
                'description' => 'Penetapan Privilege Level dan integrasi otentikasi login perangkat. [Bobot: 2.06%]',
                'created_by'  => $pmRizki->id,
            ],

            // No 20. Implementasi TACACS - Integrasi Seluruh Element (2 Hari: Day 31 - 32)
            [
                'title'       => 'Implementasi TACACS - Integrasi (Fase TACACS Selesai)',
                'engineer_id' => $engRaihan->id,
                'priority'    => 'Medium',
                'status'      => 'Assigned',
                'progress'    => 0,
                'start_date'  => $projectStartDate->copy()->addDays(30)->format('Y-m-d'),
                'deadline'    => $projectStartDate->copy()->addDays(31)->format('Y-m-d 18:00:00'),
                'description' => 'Penerapan parameter AAA ke seluruh switch distribusi dan akses. [Bobot: 2.06%]',
                'created_by'  => $pmRizki->id,
            ],

            // No 21. Testing Dan Komisioning Lapangan (2 Hari: Day 46 - 47)
            [
                'title'       => 'Testing Dan Komisioning Jaringan Bandara',
                'engineer_id' => $leadNugraha->id,
                'priority'    => 'High',
                'status'      => 'Assigned',
                'progress'    => 0,
                'start_date'  => $projectStartDate->copy()->addDays(45)->format('Y-m-d'),
                'deadline'    => $projectStartDate->copy()->addDays(46)->format('Y-m-d 18:00:00'),
                'description' => 'Uji beban throughput, latensi, dan failover redundancy uplink switch bersama tim ICT APS. [Bobot: 2.06%]',
                'created_by'  => $pmRizki->id,
            ],

            // No 22. Monitoring Stabilitas (2 Hari: Day 48 - 49)
            [
                'title'       => 'Monitoring Stabilitas Jaringan Pasca Cutover (Implementasi Denpasar Selesai)',
                'engineer_id' => $leadNugraha->id,
                'priority'    => 'Medium',
                'status'      => 'Assigned',
                'progress'    => 0,
                'start_date'  => $projectStartDate->copy()->addDays(47)->format('Y-m-d'),
                'deadline'    => $projectStartDate->copy()->addDays(48)->format('Y-m-d 18:00:00'),
                'description' => 'Standby monitoring traffic 48 jam non-stop untuk menjamin kelancaran sistem operasional bandara. [Bobot: 2.06%]',
                'created_by'  => $pmRizki->id,
            ],

            // No 24. Dokumentasi As-Built & Laporan (7 Hari: Day 48 - 54)
            [
                'title'       => 'Penyusunan Dokumentasi As-Built Drawing & Laporan Teknis',
                'engineer_id' => $leadNugraha->id,
                'priority'    => 'High',
                'status'      => 'Assigned',
                'progress'    => 0,
                'start_date'  => $projectStartDate->copy()->addDays(47)->format('Y-m-d'),
                'deadline'    => $projectStartDate->copy()->addDays(53)->format('Y-m-d 18:00:00'),
                'description' => 'Penyusunan dokumen as-built drawing topologi, IP address plan, konfigurasi backup, dan serah terima dokumen. [Bobot: 7.22%]',
                'created_by'  => $pmRizki->id,
            ],

            // No 25. BAST (2 Hari: Day 55 - 56)
            [
                'title'       => 'Uji Terima Teknis & Penandatanganan BAST Final (Project Closed)',
                'engineer_id' => $pmRizki->id,
                'priority'    => 'High',
                'status'      => 'Assigned',
                'progress'    => 0,
                'start_date'  => $projectStartDate->copy()->addDays(54)->format('Y-m-d'),
                'deadline'    => $projectStartDate->copy()->addDays(55)->format('Y-m-d 18:00:00'),
                'description' => 'Penandatanganan Berita Acara Serah Terima (BAST) pekerjaan implementasi jaringan bandara antara PT IPNET dan PT Angkasa Pura Support. [Bobot: 2.06%]',
                'created_by'  => $pmRizki->id,
            ],
        ];

        foreach ($tasks as $tData) {
            $t = Task::create(array_merge($tData, ['project_id' => $project->id]));
            if (Schema::hasTable('task_user')) {
                $t->engineers()->sync([$tData['engineer_id']]);
            }
        }

        // =========================================================================
        // 7. ISI TABEL ENGINEER_ACTIVITY_LOGS SESUAI DOKUMEN PDF 2
        // =========================================================================
        if (Schema::hasTable('engineer_activity_logs')) {
            EngineerActivityLog::where('project_id', $project->id)->delete();

            $activityLogs = [
                [
                    'activity_date' => '2026-09-28',
                    'start_time'    => '10:00:00',
                    'end_time'      => '17:00:00',
                    'location'      => 'Terminal Domestik/Internasional',
                    'activity_type' => 'Survey',
                    'description'   => 'Survey ketersediaan Port Optik dari Core Menuju ke SW Distribusi',
                    'user_id'       => $engSyaiful->id,
                    'notes'         => 'Road Block: PASS masih dalam proses.',
                ],
                [
                    'activity_date' => '2026-09-29',
                    'start_time'    => '10:00:00',
                    'end_time'      => '17:00:00',
                    'location'      => 'Terminal Domestik/Internasional',
                    'activity_type' => 'Survey',
                    'description'   => 'Survey ketersediaan port Optik dari setiap SW Distribusi ke setiap SW Access',
                    'user_id'       => $engSyaiful->id,
                    'notes'         => 'Road Block: PASS masih dalam proses.',
                ],
                [
                    'activity_date' => '2026-09-30',
                    'start_time'    => '10:00:00',
                    'end_time'      => '17:00:00',
                    'location'      => 'Terminal International',
                    'activity_type' => 'Stagging & Standarisasi IP',
                    'description'   => 'Pendampingan Pemeriksaan Perangkat oleh APINDO, Start Stagging perangkat Network, dan diskusi Risk Assessment untuk end device.',
                    'user_id'       => $engSyaiful->id,
                    'notes'         => 'Road Block: Pemindahan perangkat dari R. Buggy APS ke R.ICT APS. Menunggu approval dari pihak ELBAN/APINDO untuk devices mana yang sudah bisa dilaksanakan.',
                ],
                [
                    'activity_date' => '2026-10-01',
                    'start_time'    => '10:00:00',
                    'end_time'      => '17:00:00',
                    'location'      => 'Terminal International',
                    'activity_type' => 'Stagging',
                    'description'   => 'Pemindahan Lokasi Stagging. Dari Ruang ICT menuju ke R. Standby APS di Buggy Room karena lokasi awal dirasa kurang luas. Terkait Standarisasi IP, list IP baru sudah dimintakan ke tim APS untuk di-follow up ke APINDO.',
                    'user_id'       => $engSyaiful->id,
                    'notes'         => 'Menunggu approval ELBAN/APINDO untuk device mana yang bisa diganti.',
                ],
                [
                    'activity_date' => '2026-10-02',
                    'start_time'    => '10:00:00',
                    'end_time'      => '17:00:00',
                    'location'      => 'Terminal International',
                    'activity_type' => 'Stagging',
                    'description'   => 'Jumlah Perangkat yang terstagging : 26/108 (Akses 12, Distribusi 14). Setiap perangkat datang dalam keadaan modular sehingga butuh waktu untuk perakitan.',
                    'user_id'       => $engRaihan->id,
                    'notes'         => 'Perakitan modul sfp dan power redundan.',
                ],
                [
                    'activity_date' => '2026-10-03',
                    'start_time'    => '10:00:00',
                    'end_time'      => '17:00:00',
                    'location'      => 'Terminal International',
                    'activity_type' => 'Stagging',
                    'description'   => 'Stagging perangkat switch 7 unit.',
                    'user_id'       => $engRaihan->id,
                    'notes'         => 'Berjalan lancar.',
                ],
                [
                    'activity_date' => '2026-10-05',
                    'start_time'    => '10:00:00',
                    'end_time'      => '17:00:00',
                    'location'      => 'Terminal International',
                    'activity_type' => 'Stagging',
                    'description'   => 'Stagging perangkat switch 14 unit.',
                    'user_id'       => $engSyaiful->id,
                    'notes'         => 'Berjalan lancar.',
                ],
                [
                    'activity_date' => '2026-10-06',
                    'start_time'    => '10:00:00',
                    'end_time'      => '17:00:00',
                    'location'      => 'Terminal International',
                    'activity_type' => 'Stagging',
                    'description'   => 'Stagging perangkat switch 14 unit.',
                    'user_id'       => $engSyaiful->id,
                    'notes'         => 'Berjalan lancar.',
                ],
                [
                    'activity_date' => '2026-10-07',
                    'start_time'    => '10:00:00',
                    'end_time'      => '17:00:00',
                    'location'      => 'Terminal International',
                    'activity_type' => 'Stagging',
                    'description'   => 'Stagging perangkat switch 13 unit.',
                    'user_id'       => $engRaihan->id,
                    'notes'         => 'Berjalan lancar.',
                ],
                [
                    'activity_date' => '2026-10-08',
                    'start_time'    => '10:00:00',
                    'end_time'      => '17:00:00',
                    'location'      => 'Terminal International',
                    'activity_type' => 'Stagging',
                    'description'   => 'Stagging perangkat switch 6 unit. Total perangkat yang sudah terstagging mencapai 83 unit.',
                    'user_id'       => $engRaihan->id,
                    'notes'         => 'Total akumulasi staging 83 unit.',
                ],
                [
                    'activity_date' => '2026-10-09',
                    'start_time'    => '10:00:00',
                    'end_time'      => '17:00:00',
                    'location'      => 'Terminal International',
                    'activity_type' => 'Stagging',
                    'description'   => 'Stagging SW Akses.',
                    'user_id'       => $engSyaiful->id,
                    'notes'         => 'Berjalan lancar.',
                ],
                [
                    'activity_date' => '2026-10-10',
                    'start_time'    => '10:00:00',
                    'end_time'      => '17:00:00',
                    'location'      => 'Terminal International',
                    'activity_type' => 'Stagging & VLAN Audit',
                    'description'   => 'Kroscek perangkat yang terstagging, dan audit / revisi VLAN sesuai dengan info APS terakhir.',
                    'user_id'       => $engSyaiful->id,
                    'notes'         => 'Sinkronisasi info VLAN APS.',
                ],
                [
                    'activity_date' => '2026-10-11',
                    'start_time'    => '10:00:00',
                    'end_time'      => '17:00:00',
                    'location'      => 'Terminal International',
                    'activity_type' => 'Stagging',
                    'description'   => 'Stagging SW Akses.',
                    'user_id'       => $engRaihan->id,
                    'notes'         => 'Berjalan sesuai jadwal.',
                ],
                [
                    'activity_date' => '2026-10-12',
                    'start_time'    => '10:00:00',
                    'end_time'      => '17:00:00',
                    'location'      => 'Terminal International',
                    'activity_type' => 'Stagging',
                    'description'   => 'Stagging SW Akses.',
                    'user_id'       => $engRaihan->id,
                    'notes'         => 'Berjalan lancar.',
                ],
                [
                    'activity_date' => '2026-10-13',
                    'start_time'    => '10:00:00',
                    'end_time'      => '17:00:00',
                    'location'      => 'Terminal International',
                    'activity_type' => 'Stagging',
                    'description'   => 'Stagging SW Distribusi, Leaf dan Akses bersama Lead Engineer Nugraha Pratama.',
                    'user_id'       => $leadNugraha->id,
                    'notes'         => 'Didampingi Lead Engineer.',
                ],
                [
                    'activity_date' => '2026-10-14',
                    'start_time'    => '10:00:00',
                    'end_time'      => '17:00:00',
                    'location'      => 'Terminal International',
                    'activity_type' => 'Stagging',
                    'description'   => 'Stagging SW Distribusi dan Leaf.',
                    'user_id'       => $engSyaiful->id,
                    'notes'         => 'Berjalan lancar.',
                ],
                [
                    'activity_date' => '2026-10-15',
                    'start_time'    => '10:00:00',
                    'end_time'      => '17:00:00',
                    'location'      => 'Terminal International',
                    'activity_type' => 'Stagging',
                    'description'   => 'Stagging SW Distribusi dan Leaf.',
                    'user_id'       => $engSyaiful->id,
                    'notes'         => 'Tahap akhir staging switch distribusi.',
                ],
                [
                    'activity_date' => '2026-10-16',
                    'start_time'    => '10:00:00',
                    'end_time'      => '17:00:00',
                    'location'      => 'Terminal International',
                    'activity_type' => 'Stagging & MOP',
                    'description'   => 'Stagging SW Distribusi dan Leaf. Total Switch selesai di stagging : 106 Unit. Pembuatan dokumen PID / MOP untuk Standarisasi IP.',
                    'user_id'       => $engSyaiful->id,
                    'notes'         => '106 switch selesai, MOP standarisasi IP sedang disusun.',
                ],
            ];

            foreach ($activityLogs as $log) {
                EngineerActivityLog::create(array_merge($log, [
                    'project_id' => $project->id,
                    'category'   => 'project',
                    'status'     => 'Completed',
                ]));
            }
        }
    }
}

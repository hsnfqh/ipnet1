<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use App\Models\Division;
use App\Models\Team;
use App\Models\Client;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

class PpatkProjectSeeder extends Seeder
{
    public function run(): void
    {
        // 1. DIVISI & TIM NETWORK
        $divNet = Division::where('code', 'NET')->first() 
            ?? Division::where('name', 'like', '%Network%')->first() 
            ?? Division::firstOrCreate(['name' => 'Divisi Network'], ['code' => 'NET', 'description' => 'Jaringan & Infrastruktur']);

        $teamNet = Team::where('division_id', $divNet->id)->first()
            ?? Team::firstOrCreate(['name' => 'Tim Network'], ['division_id' => $divNet->id, 'description' => 'Tim Network Engineering']);

        // Sales PIC: Raiza
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

        // Lead Network Engineer: Nugraha Pratama
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

        // 2. KLIEN PPATK (Customer Resmi)
        $clientPpatk = Client::updateOrCreate(
            ['name' => 'PPAT - Pusat Pelaporan dan Analisa Transaksi Keuangan'],
            [
                'department' => 'Biro Umum & Pengadaan - PPATK',
                'pic_name'   => 'Richi Michael',
                'phone'      => '021-3866032',
                'email'      => 'richi.michael@ppatk.go.id',
                'address'    => 'Jl. Ir. H. Juanda No.35, Gambir, Jakarta Pusat 10120',
                'notes'      => 'Pengadaan Paket Perangkat Jaringan dan Support 5 Tahun untuk Peremajaan Router TA 2026.',
                'created_by' => $salesRaiza->id,
            ]
        );

        // 3. S-CURVE WEEKLY BREAKDOWN (Sesuai 5 Minggu WBS Kalender)
        // Week 0: Plan 0.0%, Actual 0.0%, Deviasi 0.0%
        // Week 1: Plan Weekly 23.3%, Plan Cum 23.3%, Actual Weekly 20.0%, Actual Cum 20.0%, Deviasi -3.3%
        // Week 2-5: Plan Weekly 23.3%, 23.3%, 23.3%, 6.7% | Plan Cum 46.7%, 70.0%, 93.3%, 100.0%
        $sCurveWeekly = [
            0 => [
                'plan_weekly'    => 0.0,
                'plan'           => 0.0,
                'actual_weekly'  => 0.0,
                'actual'         => 0.0,
                'variance'       => 0.0,
                'notes'          => 'Baseline Awal Proyek & Kick-off SPK',
            ],
            1 => [
                'plan_weekly'    => 23.3,
                'plan'           => 23.3,
                'actual_weekly'  => 20.0,
                'actual'         => 20.0,
                'variance'       => -3.3,
                'notes'          => 'Survey and Assessment selesai 100%, Design Review Meeting 50%, HLD/LLD Documentation 50%',
            ],
            2 => [
                'plan_weekly'    => 23.3,
                'plan'           => 46.7,
                'actual_weekly'  => null,
                'actual'         => null,
                'variance'       => null,
                'notes'          => 'Target: Finalisasi HLD/LLD, Stagging perangkat, dan Instalasi awal',
            ],
            3 => [
                'plan_weekly'    => 23.3,
                'plan'           => 70.0,
                'actual_weekly'  => null,
                'actual'         => null,
                'variance'       => null,
                'notes'          => 'Target: Selesai Instalasi & Konfigurasi, Integrasi Core, dan Cutover Migrasi tahap 1',
            ],
            4 => [
                'plan_weekly'    => 23.3,
                'plan'           => 93.3,
                'actual_weekly'  => null,
                'actual'         => null,
                'variance'       => null,
                'notes'          => 'Target: Migrasi selesai, Testing & Commisioning, Monitoring 24 jam, Dokumentasi As-Built',
            ],
            5 => [
                'plan_weekly'    => 6.7,
                'plan'           => 100.0,
                'actual_weekly'  => null,
                'actual'         => null,
                'variance'       => null,
                'notes'          => 'Target: BAST Final Proyek bersama PPATK',
            ],
        ];

        // 4. METADATA PROYEK PPATK RESMI
        $projectName = 'Pengadaan Paket Perangkat Jaringan dan Support 5 Tahun untuk Peremajaan Router Tahun Anggaran 2026';
        $projectStartDate = Carbon::create(2026, 7, 30);
        $projectDeadline  = Carbon::create(2026, 12, 31);

        $handoverData = [
            'estimate_project_duration' => 30,
            'execution_weeks'           => 5,
            'customer_pic'              => 'Richi Michael',
            'contract_spk_number'       => 'EP-01KYREAFEJH89MWPVDQ6W20NXV',
            's_curve_weekly'            => $sCurveWeekly,
            'actual_progress_inputs'    => [
                'Survey and Assessment'     => [1 => 100],
                'Design Review Meeting'     => [1 => 50],
                'HLD and LLD Documentation' => [1 => 50],
            ],
            'assigned_engineers'        => [
                [
                    'id'          => $engSyaiful->id,
                    'name'        => $engSyaiful->name,
                    'email'       => $engSyaiful->email,
                    'position'    => 'L2 Network Engineer',
                    'role'        => 'PIC Lapangan / Implementasi & Peremajaan Router',
                    'division'    => 'Divisi Network',
                    'assigned_at' => '2026-07-30 08:30',
                    'assigned_by' => $leadNugraha->name,
                ],
                [
                    'id'          => $engRaihan->id,
                    'name'        => $engRaihan->name,
                    'email'       => $engRaihan->email,
                    'position'    => 'Field Network Engineer',
                    'role'        => 'Teknisi Lapangan / Staging, Integrasi & Cabling',
                    'division'    => 'Divisi Network',
                    'assigned_at' => '2026-07-30 08:30',
                    'assigned_by' => $leadNugraha->name,
                ],
            ],
            'team_delegated_at'      => '2026-07-30 08:30:00',
            'team_delegated_by_name' => $leadNugraha->name,
            'team_delegation_notes'  => 'Personel Network telah dialokasikan untuk pelaksanaan WBS 30 hari di Gedung PPATK Jakarta.',
            'assigned_teams'         => ['Tim Network'],
        ];

        $project = Project::updateOrCreate(
            ['name' => $projectName],
            [
                'client'                        => 'PPAT - Pusat Pelaporan dan Analisa Transaksi Keuangan',
                'sales_name'                    => $salesRaiza->name,
                'created_by'                    => $salesRaiza->id,
                'pm_id'                         => $pmRizki->id,
                'division_id'                   => $divNet->id,
                'stage'                         => 'Deliver',
                'status'                        => 'In Progress',
                'process_status'                => 'In Progress',
                'sales_stage'                   => 'Closed Won',
                'project_type'                  => 'Implementation & Support',
                'sla_tier'                      => 'Gold',
                'location'                      => 'Gedung PPATK, Jl. Ir. H. Juanda No.35, Gambir, Jakarta Pusat',
                'start_date'                    => $projectStartDate->format('Y-m-d'),
                'deadline'                      => $projectDeadline->format('Y-m-d'),
                'progress'                      => 20.0,
                'contract_value'                => 850000000,
                'po_spk_number'                 => 'EP-01KYREAFEJH89MWPVDQ6W20NXV',
                'po_spk_date'                   => '2026-07-30',
                'quotation_number'              => 'QUO-IPNET-2026-PPATK-01',
                'quotation_amount'              => 850000000,
                'sla_commitment'                => 'Support 5 Tahun untuk Peremajaan Router, On-site 24x7 MTTR 4 Jam',
                'sla_tier'                      => 'Gold',
                'sla_coverage_hours'            => '24x7',
                'maintenance_frequency'         => 'Bulanan (Monthly)',
                'handover_target'               => 'both',
                'handover_status'               => 'Approved',
                'commercial_handover_status'    => 'Approved',
                'customer_pic_technical'        => 'Richi Michael (PPATK)',
                'customer_pic_business'         => 'Richi Michael (PPATK)',
                'handover_data'                 => $handoverData,
                'special_notes'                 => 'Pelaksanaan WBS 30 Hari (5 Minggu Pelaksanaan). Kontrak Support dan Managed Service 5 Tahun TA 2026-2031.',
            ]
        );

        // 5. 11 TASK WBS PERSIS SESUAI GAMBAR 1, 2, 3
        $tasksData = [
            [
                'title'        => 'Survey and Assessment',
                'duration'     => 2,
                'starting_day' => 0,
                'finish_day'   => 2,
                'week'         => 1,
                'bobot'        => 6.7,
                'daily_weight' => 3.3,
                'progress'     => 100,
                'status'       => 'Completed',
                'engineer_id'  => $engSyaiful->id,
                'description'  => 'Survey and Assessment topologi & infrastruktur jaringan eksisting router PPATK. [Bobot: 6.7% | StartingDay: 0 | FinishDay: 2 | Week: 1 | DailyWeight: 3.3%]',
            ],
            [
                'title'        => 'Design Review Meeting',
                'duration'     => 4,
                'starting_day' => 2,
                'finish_day'   => 6,
                'week'         => 1,
                'bobot'        => 13.3,
                'daily_weight' => 3.3,
                'progress'     => 50,
                'status'       => 'In Progress',
                'engineer_id'  => $engSyaiful->id,
                'description'  => 'Design Review Meeting arsitektur peremajaan router bersama PIC PPATK (Bpk. Richi Michael). [Bobot: 13.3% | StartingDay: 2 | FinishDay: 6 | Week: 1 | DailyWeight: 3.3%]',
            ],
            [
                'title'        => 'HLD and LLD Documentation',
                'duration'     => 4,
                'starting_day' => 6,
                'finish_day'   => 10,
                'week'         => 2,
                'bobot'        => 13.3,
                'daily_weight' => 3.3,
                'progress'     => 50,
                'status'       => 'In Progress',
                'engineer_id'  => $engSyaiful->id,
                'description'  => 'Penyusunan dokumen teknis High Level Design (HLD) dan Low Level Design (LLD). [Bobot: 13.3% | StartingDay: 6 | FinishDay: 10 | Week: 2 | DailyWeight: 3.3%]',
            ],
            [
                'title'        => 'Stagging',
                'duration'     => 1,
                'starting_day' => 10,
                'finish_day'   => 11,
                'week'         => 2,
                'bobot'        => 3.3,
                'daily_weight' => 3.3,
                'progress'     => 0,
                'status'       => 'Assigned',
                'engineer_id'  => $engRaihan->id,
                'description'  => 'Staging dan pengujian modul hardware serta firmware pada unit router baru. [Bobot: 3.3% | StartingDay: 10 | FinishDay: 11 | Week: 2 | DailyWeight: 3.3%]',
            ],
            [
                'title'        => 'Installation and Configuration',
                'duration'     => 5,
                'starting_day' => 11,
                'finish_day'   => 16,
                'week'         => 3,
                'bobot'        => 16.7,
                'daily_weight' => 3.3,
                'progress'     => 0,
                'status'       => 'Assigned',
                'engineer_id'  => $engRaihan->id,
                'description'  => 'Pemasangan fisik rackmount, perkabelan, dan konfigurasi routing peremajaan router. [Bobot: 16.7% | StartingDay: 11 | FinishDay: 16 | Week: 3 | DailyWeight: 3.3%]',
            ],
            [
                'title'        => 'Integration',
                'duration'     => 3,
                'starting_day' => 16,
                'finish_day'   => 19,
                'week'         => 3,
                'bobot'        => 10.0,
                'daily_weight' => 3.3,
                'progress'     => 0,
                'status'       => 'Assigned',
                'engineer_id'  => $engSyaiful->id,
                'description'  => 'Integrasi router ke backbone switch, firewall gateway, dan segmen server PPATK. [Bobot: 10.0% | StartingDay: 16 | FinishDay: 19 | Week: 3 | DailyWeight: 3.3%]',
            ],
            [
                'title'        => 'Migration',
                'duration'     => 3,
                'starting_day' => 19,
                'finish_day'   => 22,
                'week'         => 4,
                'bobot'        => 10.0,
                'daily_weight' => 3.3,
                'progress'     => 0,
                'status'       => 'Assigned',
                'engineer_id'  => $engSyaiful->id,
                'description'  => 'Migrasi routing tabel dan pengalihan traffic data live (cutover window). [Bobot: 10.0% | StartingDay: 19 | FinishDay: 22 | Week: 4 | DailyWeight: 3.3%]',
            ],
            [
                'title'        => 'Testing and Commisioning',
                'duration'     => 2,
                'starting_day' => 22,
                'finish_day'   => 24,
                'week'         => 4,
                'bobot'        => 6.7,
                'daily_weight' => 3.3,
                'progress'     => 0,
                'status'       => 'Assigned',
                'engineer_id'  => $engSyaiful->id,
                'description'  => 'Uji komisioning performa, redundansi High Availability (HA), dan failover link. [Bobot: 6.7% | StartingDay: 22 | FinishDay: 24 | Week: 4 | DailyWeight: 3.3%]',
            ],
            [
                'title'        => 'Monitoring',
                'duration'     => 1,
                'starting_day' => 24,
                'finish_day'   => 25,
                'week'         => 4,
                'bobot'        => 3.3,
                'daily_weight' => 3.3,
                'progress'     => 0,
                'status'       => 'Assigned',
                'engineer_id'  => $engRaihan->id,
                'description'  => 'Monitoring stabilitas traffic 24 jam paska migrasi router. [Bobot: 3.3% | StartingDay: 24 | FinishDay: 25 | Week: 4 | DailyWeight: 3.3%]',
            ],
            [
                'title'        => 'Documentation',
                'duration'     => 3,
                'starting_day' => 25,
                'finish_day'   => 28,
                'week'         => 4,
                'bobot'        => 10.0,
                'daily_weight' => 3.3,
                'progress'     => 0,
                'status'       => 'Assigned',
                'engineer_id'  => $engSyaiful->id,
                'description'  => 'Penyusunan As-Built Documentation, konfigurasi backup, dan handover manual. [Bobot: 10.0% | StartingDay: 25 | FinishDay: 28 | Week: 4 | DailyWeight: 3.3%]',
            ],
            [
                'title'        => 'BAST',
                'duration'     => 2,
                'starting_day' => 28,
                'finish_day'   => 30,
                'week'         => 5,
                'bobot'        => 6.7,
                'daily_weight' => 3.3,
                'progress'     => 0,
                'status'       => 'Assigned',
                'engineer_id'  => $engSyaiful->id,
                'description'  => 'Penandatanganan Berita Acara Serah Terima (BAST) pekerjaan bersama PPATK. [Bobot: 6.7% | StartingDay: 28 | FinishDay: 30 | Week: 5 | DailyWeight: 3.3%]',
            ],
        ];

        foreach ($tasksData as $tData) {
            $tStartDate = $projectStartDate->copy()->addDays($tData['starting_day']);
            $tDeadline  = $projectStartDate->copy()->addDays($tData['finish_day'])->subSecond();

            Task::updateOrCreate(
                [
                    'project_id' => $project->id,
                    'title'      => $tData['title'],
                ],
                [
                    'engineer_id' => $tData['engineer_id'],
                    'priority'    => 'High',
                    'status'      => $tData['status'],
                    'progress'    => $tData['progress'],
                    'start_date'  => $tStartDate->format('Y-m-d'),
                    'deadline'    => $tDeadline->format('Y-m-d H:i:s'),
                    'description' => $tData['description'],
                    'created_by'  => $pmRizki->id,
                ]
            );
        }
    }
}


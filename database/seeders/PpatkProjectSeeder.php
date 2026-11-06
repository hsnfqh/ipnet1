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

        // 3. S-CURVE WEEKLY BREAKDOWN (Sesuai 3 Minggu WBS Kalender / 20 Hari Pelaksanaan)
        // Week 0: Plan 0.0%, Plan Cum 0.0%
        // Week 1: Plan Weekly 30.0%, Plan Cum 30.0% (MOS 5% + Stagging 25%)
        // Week 2: Plan Weekly 40.0%, Plan Cum 70.0% (Installation 10% + Integrasi 10% + Migrasi 10% + User Acceptance 10%)
        // Week 3: Plan Weekly 30.0%, Plan Cum 100.0% (Monitoring 10% + Documentation 10% + BAST 10%)
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
                'plan_weekly'    => 30.0,
                'plan'           => 30.0,
                'actual_weekly'  => null,
                'actual'         => null,
                'variance'       => null,
                'notes'          => 'MOS - Material On Site (5%) & Stagging (25%)',
            ],
            2 => [
                'plan_weekly'    => 40.0,
                'plan'           => 70.0,
                'actual_weekly'  => null,
                'actual'         => null,
                'variance'       => null,
                'notes'          => 'Installation and Configuration (10%), Integrasi (10%), Migrasi (10%), User Acceptance (10%)',
            ],
            3 => [
                'plan_weekly'    => 30.0,
                'plan'           => 100.0,
                'actual_weekly'  => null,
                'actual'         => null,
                'variance'       => null,
                'notes'          => 'Monitoring (10%), Project Documentation (10%), BAST (10%)',
            ],
        ];

        // 4. METADATA PROYEK PPATK RESMI
        $projectName = 'Pengadaan Paket Perangkat Jaringan dan Support 5 Tahun untuk Peremajaan Router Tahun Anggaran 2026';
        $projectStartDate = Carbon::create(2026, 7, 30);
        $projectDeadline  = Carbon::create(2026, 12, 31);

        $handoverData = [
            'estimate_project_duration' => 20,
            'execution_weeks'           => 3,
            'customer_pic'              => 'Enterprise Network Infrastructure Upgrade',
            'contract_spk_number'       => 'EP-01KYREAFEJH89MWPVDQ6W20NXV',
            's_curve_weekly'            => $sCurveWeekly,
            'actual_progress_inputs'    => [],
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
            'team_delegation_notes'  => 'Personel Network telah dialokasikan untuk pelaksanaan WBS 20 hari di DC TAPOS PPATK.',
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
                'project_type'                  => 'Implementation',
                'location'                      => 'DC TAPOS',
                'start_date'                    => $projectStartDate->format('Y-m-d'),
                'deadline'                      => $projectDeadline->format('Y-m-d'),
                'progress'                      => 0.0,
                'contract_value'                => 850000000,
                'po_spk_number'                 => 'EP-01KYREAFEJH89MWPVDQ6W20NXV',
                'po_spk_date'                   => '2026-07-30',
                'quotation_number'              => 'QUO-IPNET-2026-PPATK-01',
                'quotation_amount'              => 850000000,
                'handover_target'               => 'pmo',
                'handover_status'               => 'Approved',
                'handover_approved_at'          => '2026-07-30 10:00:00',
                'commercial_handover_status'    => 'Approved',
                'customer_pic_technical'        => 'Enterprise Network Infrastructure Upgrade',
                'customer_pic_business'         => 'Richi Michael (PPATK)',
                'handover_data'                 => $handoverData,
                'special_notes'                 => 'Pelaksanaan WBS 20 Hari (3 Minggu Pelaksanaan). Proyek Implementasi & Peremajaan Router PPATK DC TAPOS.',
            ]
        );

        // 5. 9 TASK WBS RESMI PERSIS SESUAI GOOGLE SHEETS / EXCEL (20 HARI / 100% TOTAL BOBOT)
        $tasksData = [
            [
                'title'        => 'MOS - Material On Site',
                'duration'     => 1,
                'starting_day' => 0,
                'finish_day'   => 1,
                'week'         => 1,
                'bobot'        => 5.0,
                'daily_weight' => 5.0,
                'progress'     => 0,
                'status'       => 'Pending',
                'engineer_id'  => $engSyaiful->id,
                'description'  => 'Material On Site perangkat router dan perlengkapan instalasi di DC TAPOS. [Bobot: 5.0% | StartingDay: 0 | FinishDay: 1 | Week: 1 | DailyWeight: 5.0%]',
            ],
            [
                'title'        => 'Stagging',
                'duration'     => 5,
                'starting_day' => 1,
                'finish_day'   => 6,
                'week'         => 1,
                'bobot'        => 25.0,
                'daily_weight' => 5.0,
                'progress'     => 0,
                'status'       => 'Pending',
                'engineer_id'  => $engRaihan->id,
                'description'  => 'Staging modul router, pengujian hardware, instalasi firmware dan bench test. [Bobot: 25.0% | StartingDay: 1 | FinishDay: 6 | Week: 1 | DailyWeight: 5.0%]',
            ],
            [
                'title'        => 'Installation and Configuration',
                'duration'     => 2,
                'starting_day' => 6,
                'finish_day'   => 8,
                'week'         => 2,
                'bobot'        => 10.0,
                'daily_weight' => 5.0,
                'progress'     => 0,
                'status'       => 'Pending',
                'engineer_id'  => $engRaihan->id,
                'description'  => 'Pemasangan fisik rackmount, perkabelan, dan konfigurasi routing peremajaan router. [Bobot: 10.0% | StartingDay: 6 | FinishDay: 8 | Week: 2 | DailyWeight: 5.0%]',
            ],
            [
                'title'        => 'Integrasi',
                'duration'     => 2,
                'starting_day' => 8,
                'finish_day'   => 10,
                'week'         => 2,
                'bobot'        => 10.0,
                'daily_weight' => 5.0,
                'progress'     => 0,
                'status'       => 'Pending',
                'engineer_id'  => $engSyaiful->id,
                'description'  => 'Integrasi router ke backbone switch, firewall gateway, dan segmen server PPATK. [Bobot: 10.0% | StartingDay: 8 | FinishDay: 10 | Week: 2 | DailyWeight: 5.0%]',
            ],
            [
                'title'        => 'Migrasi',
                'duration'     => 2,
                'starting_day' => 10,
                'finish_day'   => 12,
                'week'         => 2,
                'bobot'        => 10.0,
                'daily_weight' => 5.0,
                'progress'     => 0,
                'status'       => 'Pending',
                'engineer_id'  => $engSyaiful->id,
                'description'  => 'Migrasi routing tabel dan pengalihan traffic data live (cutover window). [Bobot: 10.0% | StartingDay: 10 | FinishDay: 12 | Week: 2 | DailyWeight: 5.0%]',
            ],
            [
                'title'        => 'User Acceptance',
                'duration'     => 2,
                'starting_day' => 12,
                'finish_day'   => 14,
                'week'         => 2,
                'bobot'        => 10.0,
                'daily_weight' => 5.0,
                'progress'     => 0,
                'status'       => 'Pending',
                'engineer_id'  => $engSyaiful->id,
                'description'  => 'Pengujian User Acceptance Test (UAT) operasional router bersama teknis PPATK. [Bobot: 10.0% | StartingDay: 12 | FinishDay: 14 | Week: 2 | DailyWeight: 5.0%]',
            ],
            [
                'title'        => 'Monitoring',
                'duration'     => 2,
                'starting_day' => 14,
                'finish_day'   => 16,
                'week'         => 3,
                'bobot'        => 10.0,
                'daily_weight' => 5.0,
                'progress'     => 0,
                'status'       => 'Pending',
                'engineer_id'  => $engRaihan->id,
                'description'  => 'Monitoring stabilitas throughput dan performa router 24 jam pasca migrasi. [Bobot: 10.0% | StartingDay: 14 | FinishDay: 16 | Week: 3 | DailyWeight: 5.0%]',
            ],
            [
                'title'        => 'Project Documentation',
                'duration'     => 2,
                'starting_day' => 16,
                'finish_day'   => 18,
                'week'         => 3,
                'bobot'        => 10.0,
                'daily_weight' => 5.0,
                'progress'     => 0,
                'status'       => 'Pending',
                'engineer_id'  => $engSyaiful->id,
                'description'  => 'Penyusunan As-Built Documentation, konfigurasi backup, dan handover manual. [Bobot: 10.0% | StartingDay: 16 | FinishDay: 18 | Week: 3 | DailyWeight: 5.0%]',
            ],
            [
                'title'        => 'BAST',
                'duration'     => 2,
                'starting_day' => 18,
                'finish_day'   => 20,
                'week'         => 3,
                'bobot'        => 10.0,
                'daily_weight' => 5.0,
                'progress'     => 0,
                'status'       => 'Pending',
                'engineer_id'  => $engSyaiful->id,
                'description'  => 'Penandatanganan Berita Acara Serah Terima (BAST) pekerjaan bersama PPATK. [Bobot: 10.0% | StartingDay: 18 | FinishDay: 20 | Week: 3 | DailyWeight: 5.0%]',
            ],
        ];

        // Bersihkan task lama proyek ini agar sinkron sempurna dengan 9 task resmi
        Task::where('project_id', $project->id)->delete();

        foreach ($tasksData as $tData) {
            $tStartDate = $projectStartDate->copy()->addDays($tData['starting_day']);
            $tDeadline  = $projectStartDate->copy()->addDays($tData['finish_day'])->subSecond();

            Task::create([
                'project_id'  => $project->id,
                'title'       => $tData['title'],
                'engineer_id' => $tData['engineer_id'],
                'priority'    => 'High',
                'status'      => $tData['status'],
                'progress'    => $tData['progress'],
                'start_date'  => $tStartDate->format('Y-m-d'),
                'deadline'    => $tDeadline->format('Y-m-d H:i:s'),
                'description' => $tData['description'],
                'created_by'  => $pmRizki->id,
            ]);
        }
    }
}


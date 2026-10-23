<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use App\Models\Division;
use App\Models\Team;
use App\Models\Schedule;
use App\Models\ManagedServiceAsset;
use App\Models\ManagedServiceTicket;
use App\Models\ManagedServicePmCycle;
use App\Models\ProjectDocument;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class TechnicalWorkflowScenarioSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Validasi & Ambil User Resmi IP-NET (Sesuai Struktur Organisasi)
        $direktur = User::where('email', 'hariyadi@ipnetsolusindo.com')->orWhere('name', 'like', '%Hariyadi%')->first();
        $divHead  = User::where('email', 'susanto@ipnetsolusindo.com')->orWhere('name', 'like', '%Susanto%')->first();

        $pmRizki   = User::where('email', 'rizki@ipnetsolusindo.com')->orWhere('name', 'like', '%Rizki%')->first();
        $pmKuncoro = User::where('email', 'kuncoro@ipnetsolusindo.com')->orWhere('name', 'like', '%Kuncoro%')->first();

        $leadNugraha = User::where('email', 'nugraha@ipnetsolusindo.com')->orWhere('name', 'like', '%Nugraha%')->first();
        $engRorik    = User::where('email', 'rorik@ipnetsolusindo.com')->orWhere('name', 'like', '%Rorik%')->first();
        $engDedy     = User::where('email', 'dedy@ipnetsolusindo.com')->orWhere('name', 'like', '%Dedy%')->first();
        $engShiamsyah= User::where('email', 'shiamsyah@ipnetsolusindo.com')->orWhere('name', 'like', '%Shiamsyah%')->first();
        $engSyaiful  = User::where('email', 'syaiful@ipnetsolusindo.com')->orWhere('name', 'like', '%Syaiful%')->first();

        $leadDoris = User::where('email', 'doris@ipnetsolusindo.com')->orWhere('name', 'like', '%Doris%')->first();
        $engMario  = User::where('email', 'mario@ipnetsolusindo.com')->orWhere('name', 'like', '%Mario%')->first();
        $engEris   = User::where('email', 'eris@ipnetsolusindo.com')->orWhere('name', 'like', '%Eris%')->first();

        $salesRaiza  = User::where('email', 'raiza@ipnetsolusindo.com')->orWhere('name', 'like', '%Raiza%')->first();
        $salesDonny  = User::where('email', 'donny.burnan@ipnetsolusindo.com')->orWhere('name', 'like', '%Donny%')->first();
        $salesHendry = User::where('email', 'hendry.wibowo@ipnetsolusindo.com')->orWhere('name', 'like', '%Hendry%')->first();

        $presalesAkbar = User::where('email', 'akbar@ipnetsolusindo.com')->orWhere('name', 'like', '%Akbar%')->first();
        $saAris        = User::where('email', 'aris@ipnetsolusindo.com')->orWhere('email', 'aris.sadewo@ipnetsolusindo.com')->orWhere('name', 'like', '%Aris%')->first();
        $bdmKurnijanto = User::where('email', 'kurnijanto.edy@ipnetsolusindo.com')->orWhere('email', 'kurnijanto@ipnetsolusindo.com')->orWhere('name', 'like', '%Kurnijanto%')->first();

        $divNet = Division::where('name', 'like', '%Network%')->first() ?? Division::first();
        $divMnt = Division::where('name', 'like', '%Maintenance%')->first() ?? Division::first();

        // Standard Commercial & Technical Checklist
        $checklistP1 = [
            'sales_confirmation_signed' => ['checked' => true, 'note' => 'SC No: SC/IPNET/2026/041 disetujui Head Division (Susanto Djaya) & Director (Hariyadi)'],
            'contract_signed'           => ['checked' => true, 'note' => 'No. PO: PO-BM/IT-OPS/2026/1029 senilai Rp 1.850.000.000'],
            'bom_proposal'              => ['checked' => true, 'note' => 'BoQ Hardware Cisco & Fortinet disusun SA Aris Sadewo & Presales Akbar'],
            'negotiation_addendum'      => ['checked' => true, 'note' => 'Addendum SLA 24/7 Cutover Standby disetujui BDM Kurnijanto Edy'],
            'sow_document'              => ['checked' => true, 'note' => 'SOW Modernisasi Core Network & Perimeter Firewall Terlampir'],
            'urs_requirement'           => ['checked' => true, 'note' => 'Requirement Klien: HA Active-Passive 24/7 Redundancy'],
            'mockup_wireframe'          => ['checked' => true, 'note' => 'Topologi Leaf-Spine & Perimeter Security DC Plaza Mandiri'],
            'maintenance_contract'      => ['checked' => true, 'note' => 'Garansi Hardware 3 Tahun Cisco NBD & Lisensi FortiGuard 1 Tahun'],
            'dp_payment_proof'          => ['checked' => true, 'note' => 'DP 30% Terverifikasi Finance Mandiri'],
            'billing_milestone'         => ['checked' => true, 'note' => 'Termin 1 (DP 30%), Termin 2 (Delivery 40%), Termin 3 (BAST 30%)'],
            'customer_contacts'         => ['checked' => true, 'note' => 'PIC: Bpk. Hendro Wicaksono (VP IT Mandiri - 081288990011)'],
            'meeting_notes'             => ['checked' => true, 'note' => 'Kick-off Meeting PM Rizki & Tim Teknis Mandiri disepakati'],
        ];

        // =========================================================================
        // SKENARIO 1: PROYEK SALES KE PROJECT MANAGER (Pure Delivery / Implementation)
        // =========================================================================
        $p1 = Project::updateOrCreate(
            ['name' => 'Modernisasi Core Network & Perimeter Firewall PT Bank Mandiri (Persero) Tbk'],
            [
                'client'                        => 'PT Bank Mandiri (Persero) Tbk',
                'sales_name'                    => $salesRaiza ? $salesRaiza->name : 'Raiza',
                'created_by'                    => $salesRaiza ? $salesRaiza->id : null,
                'pm_id'                         => $pmRizki ? $pmRizki->id : null,
                'division_id'                   => $divNet ? $divNet->id : 1,
                'stage'                         => 'Deliver',
                'status'                        => 'In Progress',
                'process_status'                => 'In Progress',
                'sales_stage'                   => 'Closed Won',
                'project_type'                  => 'One-Time Project',
                'sla_tier'                      => 'Platinum',
                'location'                      => 'Plaza Mandiri Lt. 8 & Disaster Recovery Center (DRC) Jakarta',
                'start_date'                    => Carbon::now()->subDays(18)->format('Y-m-d'),
                'deadline'                      => Carbon::now()->addDays(24)->format('Y-m-d'),
                'progress'                      => 60,
                'contract_value'                => 1850000000,
                'po_spk_number'                 => 'PO-BM/IT-OPS/2026/1029',
                'po_spk_date'                   => Carbon::now()->subDays(25)->format('Y-m-d'),
                'quotation_number'              => 'QUO-IPNET-2026-041',
                'quotation_amount'              => 1850000000,
                'billing_terms'                 => 'Termin 1 (DP 30% Terbayar), Termin 2 (Delivery Hardware & Config 40% On-Going), Termin 3 (BAST & UAT Final 30%)',
                'commercial_terms'              => 'Termasuk instalasi rack mounting, lisensi FortiGuard Security 1 tahun, garansi hardware Cisco 3 tahun NBD, dan pendampingan migrasi cutover.',
                'sla_commitment'                 => 'Support On-site 4 jam, 24x7 Emergency Contact, Pendampingan Cutover 48 jam non-stop',
                'handover_target'               => 'pmo',
                'handover_status'               => 'Approved',
                'handover_submitted_at'         => Carbon::now()->subDays(20),
                'handover_approved_at'          => Carbon::now()->subDays(19),
                'handover_approved_by'          => $divHead ? $divHead->id : null,
                'commercial_handover_status'    => 'Approved',
                'commercial_handover_at'        => Carbon::now()->subDays(19),
                'commercial_handover_by'        => $direktur ? $direktur->id : null,
                'presales_name'                 => $presalesAkbar ? $presalesAkbar->name : 'Akbar',
                'presales_status'               => 'Approved',
                'initial_requirement'          => 'Klien membutuhkan peremajaan Core Switch eksisting dan arsitektur Firewall Perimeter baru dengan skema High Availability Active-Passive FortiGate 600E guna mendukung transaksi perbankan 24/7.',
                'customer_pic_technical'        => 'Bpk. Hendro Wicaksono (VP IT Infrastructure Mandiri - hendro.w@bankmandiri.co.id)',
                'customer_pic_business'         => 'Ibu Rina Kartika (SVP Procurement Mandiri - rina.kartika@bankmandiri.co.id)',
                'customer_pic_finance'          => 'Bpk. Bambang Sutopo (Finance Operations Mandiri)',
                'bdm_id'                        => $bdmKurnijanto ? $bdmKurnijanto->id : null,
                'bdm_handover_status'           => 'Approved',
                'bdm_handover_at'               => Carbon::now()->subDays(20),
                'stakeholders_data'             => [
                    'sales'        => ['name' => $salesRaiza ? $salesRaiza->name : 'Raiza', 'role' => 'Account Manager', 'status' => 'Submitted SC'],
                    'approver_hd'  => ['name' => $divHead ? $divHead->name : 'Susanto Djaya', 'role' => 'Division Head', 'status' => 'Verified & Approved'],
                    'approver_dir' => ['name' => $direktur ? $direktur->name : 'Hariyadi', 'role' => 'Director', 'status' => 'Validated & Signed'],
                    'presales'     => ['name' => $presalesAkbar ? $presalesAkbar->name : 'Akbar', 'role' => 'Pre-Sales Specialist', 'status' => 'Proposal Approved'],
                    'sa'           => ['name' => $saAris ? $saAris->name : 'Aris Sadewo', 'role' => 'Solution Architect', 'status' => 'BoQ & Topology Certified'],
                    'bdm'          => ['name' => $bdmKurnijanto ? $bdmKurnijanto->name : 'Kurnijanto Edy', 'role' => 'Business Development Manager', 'status' => 'Commercial Contract Signed'],
                    'pm'           => ['name' => $pmRizki ? $pmRizki->name : 'Rizki', 'role' => 'Project Manager', 'status' => 'Delivery Lead'],
                    'lead_eng'     => ['name' => $leadNugraha ? $leadNugraha->name : 'Nugraha Pratama', 'role' => 'Lead Engineer', 'status' => 'Engineering Delegator'],
                ],
                'handover_data'                 => array_merge($checklistP1, [
                    'assigned_engineers' => [
                        [
                            'id'          => $engRorik ? $engRorik->id : null,
                            'name'        => $engRorik ? $engRorik->name : 'Rorik',
                            'email'       => 'rorik@ipnetsolusindo.com',
                            'position'    => 'L1 Network Engineer',
                            'role'        => 'PIC Core Routing & Switching Migration',
                            'division'    => 'Divisi Network',
                            'assigned_at' => Carbon::now()->subDays(18)->format('d M Y H:i'),
                            'assigned_by' => 'Nugraha Pratama',
                        ],
                        [
                            'id'          => $engDedy ? $engDedy->id : null,
                            'name'        => $engDedy ? $engDedy->name : 'Dedy Suryana',
                            'email'       => 'dedy@ipnetsolusindo.com',
                            'position'    => 'L2 Network Engineer',
                            'role'        => 'PIC Perimeter Firewall Cluster FortiGate',
                            'division'    => 'Divisi Network',
                            'assigned_at' => Carbon::now()->subDays(18)->format('d M Y H:i'),
                            'assigned_by' => 'Nugraha Pratama',
                        ],
                        [
                            'id'          => $engSyaiful ? $engSyaiful->id : null,
                            'name'        => $engSyaiful ? $engSyaiful->name : 'Syaiful Amin',
                            'email'       => 'syaiful@ipnetsolusindo.com',
                            'position'    => 'Field Support (EOS)',
                            'role'        => 'PIC Rack Staging, Cabling & Hardware Testing',
                            'division'    => 'Divisi Maintenance & Helpdesk',
                            'assigned_at' => Carbon::now()->subDays(18)->format('d M Y H:i'),
                            'assigned_by' => 'Nugraha Pratama',
                        ],
                    ],
                    'team_delegation_notes'   => 'Tim engineering telah divalidasi dan ditugaskan sesuai matriks keahlian oleh Lead Engineer Nugraha Pratama. Syaiful menangani aspek fisik rack datacenter, Rorik menangani backbone routing BGP & VLAN, dan Dedy fokus pada hardening & clustering FortiGate 600E. Window cutover dijadwalkan hari Sabtu pukul 22.00 WIB.',
                    'team_delegated_at'       => Carbon::now()->subDays(18)->format('d M Y H:i'),
                    'team_delegated_by_id'    => $leadNugraha ? $leadNugraha->id : null,
                    'team_delegated_by_name'  => 'Nugraha Pratama (Team Leader Network Engineering)',
                    'expenses' => [
                        [
                            'id'           => 1,
                            'category'     => 'Hardware',
                            'amount'       => 720000000,
                            'description'  => 'Pengadaan 2 unit Cisco Catalyst 9500 & SFP+ 10G transceiver module PO-HW/2026/041',
                            'expense_date' => Carbon::now()->subDays(14)->format('d M Y'),
                            'creator_name' => 'Rizki',
                        ],
                        [
                            'id'           => 2,
                            'category'     => 'Hardware',
                            'amount'       => 480000000,
                            'description'  => 'Pengadaan 2 unit FortiGate 600E High-Availability cluster + license 1 yr',
                            'expense_date' => Carbon::now()->subDays(12)->format('d M Y'),
                            'creator_name' => 'Rizki',
                        ],
                        [
                            'id'           => 3,
                            'category'     => 'Transport & Site Tools',
                            'amount'       => 15000000,
                            'description'  => 'Akomodasi staging tim engineer & perlengkapan patch cord fiber LC-LC',
                            'expense_date' => Carbon::now()->subDays(8)->format('d M Y'),
                            'creator_name' => 'Rizki',
                        ],
                    ]
                ]),
                'documents_checklist'           => $checklistP1,
                'po_file'                       => 'project_documents/sample_sc_contract.pdf',
                'proposal_file'                 => 'project_documents/sample_technical_boq.pdf',
                'contract_file'                 => 'project_documents/sample_sc_contract.pdf',
                'boq_file'                      => 'project_documents/sample_technical_boq.pdf',
                'tor_file'                      => 'project_documents/sample_installation_report.pdf',
            ]
        );

        // Hapus task lama dari proyek ini agar idempotent & terstruktur rapi
        Task::where('project_id', $p1->id)->forceDelete();

        $tasksP1 = [
            [
                'title'       => 'Site Survey, Rack Staging, & Power Redundancy Assessment Data Center',
                'engineer_id' => $engSyaiful ? $engSyaiful->id : ($leadNugraha ? $leadNugraha->id : 1),
                'priority'    => 'High',
                'status'      => 'Completed',
                'progress'    => 100,
                'start_date'  => Carbon::now()->subDays(17)->format('Y-m-d'),
                'deadline'    => Carbon::now()->subDays(14)->format('Y-m-d 18:00:00'),
                'description' => 'Pengecekan fisik ruang DC Plaza Mandiri Rack DC-04, verifikasi dual power input PDU A & B, jalur duct kabel FO & UTP, serta uji kesiapan grounding rack.',
                'created_by'  => $pmRizki ? $pmRizki->id : null,
            ],
            [
                'title'       => 'Unboxing, Rack Mounting, & Structured Cabling Cisco Catalyst 9500',
                'engineer_id' => $engRorik ? $engRorik->id : ($leadNugraha ? $leadNugraha->id : 1),
                'priority'    => 'High',
                'status'      => 'Completed',
                'progress'    => 100,
                'start_date'  => Carbon::now()->subDays(13)->format('Y-m-d'),
                'deadline'    => Carbon::now()->subDays(9)->format('Y-m-d 18:00:00'),
                'description' => 'Pemasangan 2 unit Cisco Catalyst 9500 pada Rack U18-U20, koneksi kabel 40G QSFP+ StackWise Virtual, serta labeling kabel rapi sesuai standar perbankan.',
                'created_by'  => $pmRizki ? $pmRizki->id : null,
            ],
            [
                'title'       => 'High Availability Active-Passive Configuration Firewall FortiGate 600E',
                'engineer_id' => $engDedy ? $engDedy->id : ($leadNugraha ? $leadNugraha->id : 1),
                'priority'    => 'High',
                'status'      => 'In Progress',
                'progress'    => 75,
                'start_date'  => Carbon::now()->subDays(8)->format('Y-m-d'),
                'deadline'    => Carbon::now()->addDays(2)->format('Y-m-d 18:00:00'),
                'description' => 'Hardening FortiOS 7.4, aktivasi lisensi FortiGuard Enterprise, konfigurasi FGCP HA heartbeat interface, sinkronisasi state table, dan pembuatan security policy perimeter.',
                'created_by'  => $pmRizki ? $pmRizki->id : null,
            ],
            [
                'title'       => 'VLAN Trunking, BGP Peering DC, & Core Routing Migration',
                'engineer_id' => $engRorik ? $engRorik->id : ($leadNugraha ? $leadNugraha->id : 1),
                'priority'    => 'Medium',
                'status'      => 'In Progress',
                'progress'    => 50,
                'start_date'  => Carbon::now()->subDays(5)->format('Y-m-d'),
                'deadline'    => Carbon::now()->addDays(5)->format('Y-m-d 18:00:00'),
                'description' => 'Setup 48 VLAN core banking, inter-VLAN routing, BGP peering ke router perimeter DRC, pengujian redundancy link failover HSRP/VRRP.',
                'created_by'  => $pmRizki ? $pmRizki->id : null,
            ],
            [
                'title'       => 'User Acceptance Test (UAT), Failover Simulation, & Technical BAST Sign-off',
                'engineer_id' => $leadNugraha ? $leadNugraha->id : ($pmRizki ? $pmRizki->id : 1),
                'priority'    => 'High',
                'status'      => 'Assigned',
                'progress'    => 0,
                'start_date'  => Carbon::now()->addDays(6)->format('Y-m-d'),
                'deadline'    => Carbon::now()->addDays(15)->format('Y-m-d 18:00:00'),
                'description' => 'Pelaksanaan simulasi blackout failover link bersama tim IT Mandiri, validasi parameter checklist UAT, dan penandatanganan Berita Acara Serah Terima (BAST) pekerjaan teknis.',
                'created_by'  => $pmRizki ? $pmRizki->id : null,
            ],
        ];

        foreach ($tasksP1 as $tData) {
            $t = Task::create(array_merge($tData, ['project_id' => $p1->id]));
            if (Schema::hasTable('task_user')) {
                $t->engineers()->sync([$tData['engineer_id']]);
            }
        }


        // =========================================================================
        // SKENARIO 2: PROYEK SALES KE MANAGED SERVICE (Direct Managed Service)
        // =========================================================================
        $checklistP2 = [
            'sales_confirmation_signed' => ['checked' => true, 'note' => 'SC No: SC/IPNET/2026/055 disetujui Head Division & Director'],
            'contract_signed'           => ['checked' => true, 'note' => 'Kontrak Managed Service No: KTR/TLK-NET/2026/021 senilai Rp 480.000.000 / tahun'],
            'bom_proposal'              => ['checked' => true, 'note' => 'Cakupan 42 CI Node Switch/Router Enterprise Telkom disetujui SA Aris Sadewo'],
            'negotiation_addendum'      => ['checked' => true, 'note' => 'SLA Commitment: 99.95% Availability & MTTR < 4 Jam disetujui BDM Kurnijanto'],
            'sow_document'              => ['checked' => true, 'note' => 'SOW Pemeliharaan Preventif Bulanan & Standby NOC 24/7 Terlampir'],
            'urs_requirement'           => ['checked' => true, 'note' => 'Requirement SLA Platinum: Respon Helpdesk 15 Menit'],
            'mockup_wireframe'          => ['checked' => true, 'note' => 'Peta Topologi Monitoring Jaringan STO Gambir & Merdeka'],
            'maintenance_contract'      => ['checked' => true, 'note' => 'Kontrak SLA Aktif 12 Bulan (Maret 2026 s/d Maret 2027)'],
            'dp_payment_proof'          => ['checked' => true, 'note' => 'Penagihan Kuartalan Termin 1 Terbit & Terbayar'],
            'billing_milestone'         => ['checked' => true, 'note' => 'Pembayaran per Kuartal (Rp 120.000.000/kuartal) + Evaluasi Laporan Bulanan'],
            'customer_contacts'         => ['checked' => true, 'note' => 'PIC: Bpk. Agus Prasetyo (Head of Network Operations Telkom - 081122334455)'],
            'meeting_notes'             => ['checked' => true, 'note' => 'Operational Kick-off Meeting Serah Terima Layanan Doris & Tim Telkom'],
        ];

        $p2 = Project::updateOrCreate(
            ['name' => 'Kontrak Managed Service & Pemeliharaan Jaringan 24/7 PT Telkom Indonesia Tbk'],
            [
                'client'                        => 'PT Telkom Indonesia Tbk',
                'sales_name'                    => $salesDonny ? $salesDonny->name : 'Donny Burnan',
                'created_by'                    => $salesDonny ? $salesDonny->id : null,
                'pm_id'                         => null, // Direct to Managed Service
                'division_id'                   => $divMnt ? $divMnt->id : 3,
                'stage'                         => 'Operate',
                'status'                        => 'In Progress',
                'process_status'                => 'In Progress',
                'sales_stage'                   => 'Closed Won',
                'project_type'                  => 'Managed Service & SLA',
                'sla_tier'                      => 'Platinum',
                'maintenance_frequency'         => 'Bulanan',
                'sla_coverage_hours'            => '24/7/365',
                'call_response_time'            => '15 Menit',
                'incident_resolution_hours'     => 4,
                'sla_target_percentage'         => 99.95,
                'cm_quota'                      => 18,
                'pm_frequency_type'             => 'per_bulan',
                'pm_total_cycles'               => 12,
                'location'                      => 'Telkom STO Gambir & STO Merdeka Jakarta',
                'start_date'                    => Carbon::now()->subMonths(2)->format('Y-m-d'),
                'deadline'                      => Carbon::now()->addMonths(10)->format('Y-m-d'),
                'service_start_date'            => Carbon::now()->subMonths(2)->format('Y-m-d'),
                'service_end_date'              => Carbon::now()->addMonths(10)->format('Y-m-d'),
                'progress'                      => 30,
                'contract_value'                => 480000000,
                'po_spk_number'                 => 'KTR/TLK-NET/2026/021',
                'po_spk_date'                   => Carbon::now()->subMonths(2)->subDays(5)->format('Y-m-d'),
                'quotation_number'              => 'QUO-IPNET-2026-055',
                'quotation_amount'              => 480000000,
                'billing_terms'                 => 'Penagihan Triwulanan (Rp 120.000.000 per kuartal) disertai Laporan Kinerja SLA Bulanan',
                'commercial_terms'              => 'Cakupan pemeliharaan mencakup 42 node perangkat aktif jaringan backbone Telkom, preventive maintenance bulanan, standby engineer 24x7, dan jaminan suku cadang NBD.',
                'sla_commitment'                 => 'SLA Ketersediaan 99.95%, Response Time Helpdesk 15 Menit, On-Site Support Engineer 2 Jam, MTTR Resolusi Insiden 4 Jam',
                'handover_target'               => 'managed_service',
                'handover_status'               => 'Approved',
                'commercial_handover_status'    => 'Approved',
                'commercial_handover_at'        => Carbon::now()->subMonths(2)->subDays(3),
                'commercial_handover_by'        => $direktur ? $direktur->id : null,
                'ms_handover_status'            => 'Accepted',
                'ms_accepted_at'                => Carbon::now()->subMonths(2)->subDays(1),
                'ms_accepted_by'                => $leadDoris ? $leadDoris->id : null,
                'customer_pic_technical'        => 'Bpk. Agus Prasetyo (Head of Network Operations Telkom - agus.p@telkom.co.id)',
                'customer_pic_business'         => 'Ibu Dewi Lestari (Senior Manager Enterprise Telkom)',
                'bdm_id'                        => $bdmKurnijanto ? $bdmKurnijanto->id : null,
                'bdm_handover_status'           => 'Approved',
                'stakeholders_data'             => [
                    'sales'        => ['name' => $salesDonny ? $salesDonny->name : 'Donny Burnan', 'role' => 'Account Manager', 'status' => 'Submitted SC'],
                    'approver_hd'  => ['name' => $divHead ? $divHead->name : 'Susanto Djaya', 'role' => 'Division Head', 'status' => 'Approved SLA Terms'],
                    'approver_dir' => ['name' => $direktur ? $direktur->name : 'Hariyadi', 'role' => 'Director', 'status' => 'Validated Contract'],
                    'presales'     => ['name' => $presalesAkbar ? $presalesAkbar->name : 'Akbar', 'role' => 'Pre-Sales', 'status' => 'SLA Scope Validation'],
                    'bdm'          => ['name' => $bdmKurnijanto ? $bdmKurnijanto->name : 'Kurnijanto Edy', 'role' => 'BDM', 'status' => 'Contract Finalized'],
                    'ms_lead'      => ['name' => $leadDoris ? $leadDoris->name : 'Doris', 'role' => 'Managed Service Coordinator', 'status' => 'Accepted & Activated'],
                ],
                'handover_data'                 => array_merge($checklistP2, [
                    'assigned_engineers' => [
                        [
                            'id'          => $engMario ? $engMario->id : null,
                            'name'        => $engMario ? $engMario->name : 'Mario',
                            'email'       => 'mario@ipnetsolusindo.com',
                            'position'    => 'Field Support Engineer (EOS)',
                            'role'        => 'Lead NOC & Telemetry Monitoring Specialist',
                            'division'    => 'Divisi Maintenance & Helpdesk',
                            'assigned_at' => Carbon::now()->subMonths(2)->format('d M Y H:i'),
                            'assigned_by' => 'Doris',
                        ],
                        [
                            'id'          => $engEris ? $engEris->id : null,
                            'name'        => $engEris ? $engEris->name : 'Eris',
                            'email'       => 'eris@ipnetsolusindo.com',
                            'position'    => 'Field Support Engineer (EOS)',
                            'role'        => 'Preventive Maintenance & Onsite Support Engineer',
                            'division'    => 'Divisi Maintenance & Helpdesk',
                            'assigned_at' => Carbon::now()->subMonths(2)->format('d M Y H:i'),
                            'assigned_by' => 'Doris',
                        ],
                    ],
                    'team_delegation_notes'   => 'Penugasan tim operasional Managed Service langsung dikelola oleh Doris. Mario memimpin pemantauan NMS Zabbix dan respon eskalasi tiket SLA 24/7, sedangkan Eris bertugas melaksanakan inspeksi fisik dan preventive maintenance rutin bulanan di STO Gambir.',
                    'team_delegated_at'       => Carbon::now()->subMonths(2)->format('d M Y H:i'),
                    'team_delegated_by_id'    => $leadDoris ? $leadDoris->id : null,
                    'team_delegated_by_name'  => 'Doris (Managed Service Coordinator)',
                ]),
                'documents_checklist'           => $checklistP2,
                'contract_file'                 => 'project_documents/sample_sc_contract.pdf',
                'po_file'                       => 'project_documents/sample_sc_contract.pdf',
                'boq_file'                      => 'project_documents/sample_sla_managed_service.pdf',
            ]
        );

        Task::where('project_id', $p2->id)->forceDelete();

        $tasksP2 = [
            [
                'title'       => 'Audit Baseline Inventory & Onboarding CI Asset Monitoring SNMP/Zabbix',
                'engineer_id' => $engMario ? $engMario->id : ($leadDoris ? $leadDoris->id : 1),
                'priority'    => 'High',
                'status'      => 'Completed',
                'progress'    => 100,
                'start_date'  => Carbon::now()->subMonths(2)->format('Y-m-d'),
                'deadline'    => Carbon::now()->subMonths(2)->addDays(7)->format('Y-m-d 18:00:00'),
                'description' => 'Audit fisik & serial number 42 unit switch/router Telkom, konfigurasi SNMPv3 community, dan integrasi alert notifikasi Telegram/Email ke NOC IP-NET.',
                'created_by'  => $leadDoris ? $leadDoris->id : null,
            ],
            [
                'title'       => 'Preventive Maintenance Rutin Siklus-1: Backup Config & Health-Check Node STO Gambir',
                'engineer_id' => $engEris ? $engEris->id : ($leadDoris ? $leadDoris->id : 1),
                'priority'    => 'Medium',
                'status'      => 'Completed',
                'progress'    => 100,
                'start_date'  => Carbon::now()->subMonths(1)->subDays(10)->format('Y-m-d'),
                'deadline'    => Carbon::now()->subMonths(1)->subDays(3)->format('Y-m-d 18:00:00'),
                'description' => 'Inspeksi visual modul fan blower, pembersihan debu filter rak STO Gambir, pengecekan temperatur optical transceiver, dan pencadangan konfigurasi ke storage repository.',
                'created_by'  => $leadDoris ? $leadDoris->id : null,
            ],
            [
                'title'       => '24/7 Network Operation Center (NOC) Shift Standby & Incident Monitoring',
                'engineer_id' => $engMario ? $engMario->id : ($leadDoris ? $leadDoris->id : 1),
                'priority'    => 'High',
                'status'      => 'In Progress',
                'progress'    => 60,
                'start_date'  => Carbon::now()->subDays(15)->format('Y-m-d'),
                'deadline'    => Carbon::now()->addDays(15)->format('Y-m-d 18:00:00'),
                'description' => 'Pemantauan ketersediaan link antar-STO secara berkesinambungan, penanganan alert threshold utilisasi bandwidth >80%, dan response time tiket < 15 menit.',
                'created_by'  => $leadDoris ? $leadDoris->id : null,
            ],
            [
                'title'       => 'Penerbitan Laporan Kinerja SLA Bulanan & Service Review Meeting Bersama Klien',
                'engineer_id' => $leadDoris ? $leadDoris->id : ($engMario ? $engMario->id : 1),
                'priority'    => 'Medium',
                'status'      => 'Assigned',
                'progress'    => 0,
                'start_date'  => Carbon::now()->addDays(10)->format('Y-m-d'),
                'deadline'    => Carbon::now()->addDays(20)->format('Y-m-d 18:00:00'),
                'description' => 'Kompilasi rekap ketersediaan jaringan bulanan (target 99.95%), log penanganan tiket gangguan (MTTR), serta presentasi berkala ke manajemen IT Telkom.',
                'created_by'  => $leadDoris ? $leadDoris->id : null,
            ],
        ];

        foreach ($tasksP2 as $tData) {
            $t = Task::create(array_merge($tData, ['project_id' => $p2->id]));
            if (Schema::hasTable('task_user')) {
                $t->engineers()->sync([$tData['engineer_id']]);
            }
        }

        // Hubungkan Asset Managed Service untuk P2
        if (Schema::hasTable('managed_service_assets')) {
            ManagedServiceAsset::updateOrCreate(
                ['project_id' => $p2->id, 'serial_number' => 'HEE08933214'],
                [
                    'client_name'   => 'PT Telkom Indonesia Tbk',
                    'device_name'   => 'Edge BGP Border Router CCR2004',
                    'category'      => 'Router',
                    'brand'         => 'Mikrotik',
                    'model'         => 'CCR2004-1G-12S+2XS',
                    'ip_address'    => '10.100.0.1',
                    'location_site' => 'Telkom STO Gambir',
                    'rack_position' => 'Rack TELKOM-01 (U10)',
                    'status'        => 'Online',
                    'warranty_expiry'=> Carbon::now()->addMonths(10),
                    'notes'         => 'Dual Power Supply redundant. Peering IXP & Upstream terhubung ke NOC monitoring.',
                    'created_by'    => $leadDoris ? $leadDoris->id : null,
                ]
            );
        }

        // Seeding 12 Siklus PM Bulanan & 3 Tiket CM untuk P2 (Telkom - Kontrak 18 Tiket Kuota)
        if (Schema::hasTable('managed_service_pm_cycles')) {
            ManagedServicePmCycle::where('project_id', $p2->id)->delete();
            for ($i = 1; $i <= 12; $i++) {
                $schedDate = Carbon::now()->subMonths(2)->addMonths($i);
                $status = ($i <= 3) ? 'Completed' : (($i === 4) ? 'In Progress' : 'Scheduled');
                $actualDate = ($i <= 3) ? $schedDate->copy()->subDays(2)->toDateString() : (($i === 4) ? Carbon::now()->toDateString() : null);

                ManagedServicePmCycle::create([
                    'project_id'           => $p2->id,
                    'cycle_number'         => $i,
                    'cycle_label'          => 'PM ' . $i . ' (Bulan ke-' . $i . ')',
                    'scheduled_date'       => $schedDate->toDateString(),
                    'actual_date'          => $actualDate,
                    'activity_status'      => $status,
                    'assigned_engineer_id' => $engEris ? $engEris->id : null,
                    'activity_notes'       => ($i <= 3) ? 'Pembersihan rack, inspeksi fan blower, backup config router STO Gambir tuntas.' : (($i === 4) ? 'Sedang berlangsung inspeksi visual & kabel backbone.' : null),
                    'report_status'        => ($i <= 3) ? 'Approved' : 'Pending',
                    'billing_ba_status'    => ($i <= 3) ? 'Signed' : 'Pending',
                ]);
            }
        }

        if (Schema::hasTable('managed_service_tickets')) {
            ManagedServiceTicket::where('project_id', $p2->id)->delete();
            $cmTicketsP2 = [
                [
                    'ticket_number' => 'CM-TLK-2026-001',
                    'title'         => 'Flapping BGP Peering Interface TenGigE 0/0/1 STO Merdeka',
                    'priority'      => 'Critical',
                    'status'        => 'Resolved',
                    'incident_date' => Carbon::now()->subDays(25),
                    'resolved_at'   => Carbon::now()->subDays(25)->addHours(2),
                    'sla_met'       => true,
                    'description'   => 'Terjadi flapping BGP session ke upstream Telkomsel. Tim melakukan pembersihan konektor optik patch cord.',
                    'assigned_to'   => $engMario ? $engMario->id : null,
                ],
                [
                    'ticket_number' => 'CM-TLK-2026-002',
                    'title'         => 'High Optical RX Power Loss pada Transceiver SFP+ Rack TELKOM-01',
                    'priority'      => 'High',
                    'status'        => 'Resolved',
                    'incident_date' => Carbon::now()->subDays(12),
                    'resolved_at'   => Carbon::now()->subDays(12)->addHours(3),
                    'sla_met'       => true,
                    'description'   => 'Alarm RX power loss -18dBm. Dilakukan penggantian modul SFP+ 10G LR sparepart resmi IP-NET.',
                    'assigned_to'   => $engEris ? $engEris->id : null,
                ],
                [
                    'ticket_number' => 'CM-TLK-2026-003',
                    'title'         => 'Peningkatan Utilisasi CPU & Latency Spike Core Switch STO Gambir',
                    'priority'      => 'Medium',
                    'status'        => 'In Progress',
                    'incident_date' => Carbon::now()->subHours(5),
                    'resolved_at'   => null,
                    'sla_met'       => true,
                    'description'   => 'Investigasi lonjakan paket broadcast dan analisis NetFlow traffic capture oleh NOC.',
                    'assigned_to'   => $engMario ? $engMario->id : null,
                ],
            ];

            foreach ($cmTicketsP2 as $tData) {
                ManagedServiceTicket::create(array_merge($tData, [
                    'project_id'   => $p2->id,
                    'client_name'  => $p2->client,
                    'type'         => 'Corrective Maintenance',
                    'category'     => 'Troubleshooting',
                    'source'       => 'Portal',
                    'reported_by'  => 'Bpk. Agus Prasetyo (Telkom)',
                    'sla_deadline' => Carbon::parse($tData['incident_date'])->addHours(4),
                    'created_by'   => $leadDoris ? $leadDoris->id : 1,
                ]));
            }
        }


        // =========================================================================
        // SKENARIO 3: PROYEK SALES HYBRID (Project Manager + Managed Service Sekaligus)
        // =========================================================================
        $checklistP3 = [
            'sales_confirmation_signed' => ['checked' => true, 'note' => 'SC No: SC/IPNET/2026/088 disetujui Head Division & Director (Turnkey + MS Scope)'],
            'contract_signed'           => ['checked' => true, 'note' => 'Kontrak Terpadu No: SPK-PLN-PUSAT/2026/412 senilai Rp 2.450.000.000'],
            'bom_proposal'              => ['checked' => true, 'note' => 'BoQ Dua Fase: Hardware Spine-Leaf DC Rp 1.95M & SLA Operation Rp 500Jt'],
            'negotiation_addendum'      => ['checked' => true, 'note' => 'Klausul Transisi: BAST 1 Turnkey memicu otomatis aktivasi SLA Managed Service'],
            'sow_document'              => ['checked' => true, 'note' => 'SOW Lengkap: Turnkey Deployment (PM) & 24/7 SLA Operation (MS)'],
            'urs_requirement'           => ['checked' => true, 'note' => 'Requirement Data Center Tier-3 Compliance & Response Time SLA 30 Menit'],
            'mockup_wireframe'          => ['checked' => true, 'note' => 'Arsitektur EVPN-VXLAN Fabric & Zoning Micro-Perimeter DMZ PLN'],
            'maintenance_contract'      => ['checked' => true, 'note' => 'Kontrak Pemeliharaan SLA Gold 1 Tahun Pasca-Implementasi'],
            'dp_payment_proof'          => ['checked' => true, 'note' => 'DP 30% Turnkey Terbayar (Rp 585.000.000)'],
            'billing_milestone'         => ['checked' => true, 'note' => 'Milestone Turnkey: DP 30%, Delivery 40%, BAST 1 30%; Milestone MS: Triwulanan'],
            'customer_contacts'         => ['checked' => true, 'note' => 'PIC: Bpk. Ir. Danang Priyadi (Senior Specialist IT PLN - 081377889900)'],
            'meeting_notes'             => ['checked' => true, 'note' => 'Rapat Koordinasi Handover Internal: PM Rizki, Lead Eng Nugraha, & Lead MS Doris'],
        ];

        $p3 = Project::updateOrCreate(
            ['name' => 'Pengadaan, Implementasi Data Center & Full Managed Service SLA PT PLN (Persero)'],
            [
                'client'                        => 'PT PLN (Persero)',
                'sales_name'                    => $salesHendry ? $salesHendry->name : 'Hendry Wibowo',
                'created_by'                    => $salesHendry ? $salesHendry->id : null,
                'pm_id'                         => $pmRizki ? $pmRizki->id : null,
                'division_id'                   => $divNet ? $divNet->id : 1,
                'stage'                         => 'Deliver',
                'status'                        => 'In Progress',
                'process_status'                => 'In Progress',
                'sales_stage'                   => 'Closed Won',
                'project_type'                  => 'Turnkey & Managed Service',
                'sla_tier'                      => 'Gold',
                'maintenance_frequency'         => 'Triwulanan',
                'sla_coverage_hours'            => '24/7/365',
                'call_response_time'            => '30 Menit',
                'incident_resolution_hours'     => 4,
                'sla_target_percentage'         => 99.80,
                'cm_quota'                      => 8,
                'pm_frequency_type'             => 'Quarterly',
                'pm_total_cycles'               => 4,
                'location'                      => 'PLN Kantor Pusat Trunojoyo & Data Center Gandul',
                'start_date'                    => Carbon::now()->subDays(45)->format('Y-m-d'),
                'deadline'                      => Carbon::now()->addMonths(11)->format('Y-m-d'),
                'service_start_date'            => Carbon::now()->addDays(15)->format('Y-m-d'),
                'service_end_date'              => Carbon::now()->addMonths(11)->format('Y-m-d'),
                'progress'                      => 85, // Implementasi hampir tuntas menuju handover operasional
                'contract_value'                => 2450000000,
                'po_spk_number'                 => 'SPK-PLN-PUSAT/2026/412',
                'po_spk_date'                   => Carbon::now()->subDays(50)->format('Y-m-d'),
                'quotation_number'              => 'QUO-IPNET-2026-088',
                'quotation_amount'              => 2450000000,
                'billing_terms'                 => 'Turnkey Delivery Rp 1.950.000.000 (DP 30%, Delivery 40%, BAST 1 30%) + Kontrak SLA Managed Service Rp 500.000.000 (Dibayarkan Triwulanan @ Rp 125.000.000 pasca BAST 1)',
                'commercial_terms'              => 'Proyek gabungan: Implementasi fisik Turnkey Data Center oleh Project Manager & Tim Engineer, dilanjutkan kontrak penuh Managed Service SLA 1 tahun dengan support onsite dan pemeliharaan berkala.',
                'sla_commitment'                 => 'SLA Implementasi: Selesai 60 hari kerja. SLA Managed Service: Uptime 99.8%, Respons helpdesk 30 menit, MTTR 4 jam, Preventive maintenance tiap 3 bulan.',
                'handover_target'               => 'pmo',
                'handover_status'               => 'Approved',
                'commercial_handover_status'    => 'Approved',
                'commercial_handover_at'        => Carbon::now()->subDays(46),
                'commercial_handover_by'        => $direktur ? $direktur->id : null,
                'customer_pic_technical'        => 'Bpk. Ir. Danang Priyadi (Senior Specialist IT PLN - danang.p@pln.co.id)',
                'customer_pic_business'         => 'Ibu Siti Rahmawati (General Manager Procurement PLN)',
                'bdm_id'                        => $bdmKurnijanto ? $bdmKurnijanto->id : null,
                'bdm_handover_status'           => 'Approved',
                'stakeholders_data'             => [
                    'sales'        => ['name' => $salesHendry ? $salesHendry->name : 'Hendry Wibowo', 'role' => 'Account Manager', 'status' => 'Submitted SC'],
                    'approver_hd'  => ['name' => $divHead ? $divHead->name : 'Susanto Djaya', 'role' => 'Division Head', 'status' => 'Approved Hybrid Scope'],
                    'approver_dir' => ['name' => $direktur ? $direktur->name : 'Hariyadi', 'role' => 'Director', 'status' => 'Approved & Signed'],
                    'presales'     => ['name' => $presalesAkbar ? $presalesAkbar->name : 'Akbar', 'role' => 'Pre-Sales', 'status' => 'Turnkey & SLA Proposal'],
                    'sa'           => ['name' => $saAris ? $saAris->name : 'Aris Sadewo', 'role' => 'Solution Architect', 'status' => 'Hybrid Topology Design'],
                    'bdm'          => ['name' => $bdmKurnijanto ? $bdmKurnijanto->name : 'Kurnijanto Edy', 'role' => 'BDM', 'status' => 'Master Agreement Signed'],
                    'pm'           => ['name' => $pmRizki ? $pmRizki->name : 'Rizki', 'role' => 'Project Manager', 'status' => 'Delivery Phase Lead'],
                    'lead_eng'     => ['name' => $leadNugraha ? $leadNugraha->name : 'Nugraha Pratama', 'role' => 'Lead Engineer', 'status' => 'Engineering Lead'],
                    'ms_lead'      => ['name' => $leadDoris ? $leadDoris->name : 'Doris', 'role' => 'Managed Service Coordinator', 'status' => 'SLA Operation Lead'],
                ],
                'handover_data'                 => array_merge($checklistP3, [
                    'assigned_engineers' => [
                        // Personel Tim PM & Engineering (Implementasi Turnkey)
                        [
                            'id'          => $engShiamsyah ? $engShiamsyah->id : null,
                            'name'        => $engShiamsyah ? $engShiamsyah->name : 'Shiamsyah Azis',
                            'email'       => 'shiamsyah@ipnetsolusindo.com',
                            'position'    => 'L1 Network Engineer',
                            'role'        => 'PIC DC Structured Cabling & Rack PDU Power (PM Phase)',
                            'division'    => 'Divisi Network',
                            'assigned_at' => Carbon::now()->subDays(40)->format('d M Y H:i'),
                            'assigned_by' => 'Nugraha Pratama',
                        ],
                        [
                            'id'          => $engRorik ? $engRorik->id : null,
                            'name'        => $engRorik ? $engRorik->name : 'Rorik',
                            'email'       => 'rorik@ipnetsolusindo.com',
                            'position'    => 'L1 Network Engineer',
                            'role'        => 'PIC Fabric Data Center Spine-Leaf & Routing (PM Phase)',
                            'division'    => 'Divisi Network',
                            'assigned_at' => Carbon::now()->subDays(40)->format('d M Y H:i'),
                            'assigned_by' => 'Nugraha Pratama',
                        ],
                        // Personel Tim Managed Service (Operasi & Pemeliharaan SLA)
                        [
                            'id'          => $engMario ? $engMario->id : null,
                            'name'        => $engMario ? $engMario->name : 'Mario',
                            'email'       => 'mario@ipnetsolusindo.com',
                            'position'    => 'Field Support Engineer (EOS)',
                            'role'        => 'PIC Onboarding NMS Telemetry & Alerting (MS Phase)',
                            'division'    => 'Divisi Maintenance & Helpdesk',
                            'assigned_at' => Carbon::now()->subDays(20)->format('d M Y H:i'),
                            'assigned_by' => 'Doris',
                        ],
                        [
                            'id'          => $engEris ? $engEris->id : null,
                            'name'        => $engEris ? $engEris->name : 'Eris',
                            'email'       => 'eris@ipnetsolusindo.com',
                            'position'    => 'Field Support Engineer (EOS)',
                            'role'        => 'PIC Preventive Maintenance Triwulanan DC (MS Phase)',
                            'division'    => 'Divisi Maintenance & Helpdesk',
                            'assigned_at' => Carbon::now()->subDays(20)->format('d M Y H:i'),
                            'assigned_by' => 'Doris',
                        ],
                    ],
                    'team_delegation_notes'   => 'Koordinasi terpadu: Project Manager Rizki & Lead Engineer Nugraha Pratama mengawal penyelesaian akhir BAST implementasi fisik oleh Rorik & Shiamsyah. Sementara itu, Managed Service Lead Doris menugaskan Mario & Eris untuk memastikan kesiapan sensor telemetri, checklist operasional, dan jadwal preventive maintenance pasca-cutover.',
                    'team_delegated_at'       => Carbon::now()->subDays(40)->format('d M Y H:i'),
                    'team_delegated_by_id'    => $leadNugraha ? $leadNugraha->id : null,
                    'team_delegated_by_name'  => 'Nugraha Pratama & Doris (Lead Coordination)',
                ]),
                'documents_checklist'           => $checklistP3,
                'contract_file'                 => 'project_documents/sample_sc_contract.pdf',
                'po_file'                       => 'project_documents/sample_sc_contract.pdf',
                'boq_file'                      => 'project_documents/sample_technical_boq.pdf',
                'tor_file'                      => 'project_documents/sample_installation_report.pdf',
            ]
        );

        Task::where('project_id', $p3->id)->forceDelete();

        // Task terbagi jelas: [PM - Deployment] untuk Tim PM & [MS - Operation] untuk Tim MS (TANPA DUPLIKASI!)
        $tasksP3 = [
            // === FASE 1: PROJECT MANAGER & ENGINEERING IMPLEMENTATION ===
            [
                'title'       => '[PM - Deployment] Instalasi Hardware Data Center, Rack PDU, & Structured Cabling MPO',
                'engineer_id' => $engShiamsyah ? $engShiamsyah->id : ($leadNugraha ? $leadNugraha->id : 1),
                'priority'    => 'High',
                'status'      => 'Completed',
                'progress'    => 100,
                'start_date'  => Carbon::now()->subDays(42)->format('Y-m-d'),
                'deadline'    => Carbon::now()->subDays(28)->format('Y-m-d 18:00:00'),
                'description' => 'Instalasi 8 rak server data center, penggelaran trunk optic MPO 40G, instalasi panel daya PDU A-B redundan di DC PLN Trunojoyo.',
                'created_by'  => $pmRizki ? $pmRizki->id : null,
            ],
            [
                'title'       => '[PM - Deployment] Konfigurasi Spine-Leaf Network Data Center & Micro-Perimeter DMZ',
                'engineer_id' => $engRorik ? $engRorik->id : ($leadNugraha ? $leadNugraha->id : 1),
                'priority'    => 'High',
                'status'      => 'Completed',
                'progress'    => 100,
                'start_date'  => Carbon::now()->subDays(27)->format('Y-m-d'),
                'deadline'    => Carbon::now()->subDays(12)->format('Y-m-d 18:00:00'),
                'description' => 'Konfigurasi Fabric VXLAN-EVPN pada spine-leaf switch, zoning VLAN server farm, dan konfigurasi firewall micro-segmentation DMZ.',
                'created_by'  => $pmRizki ? $pmRizki->id : null,
            ],
            [
                'title'       => '[PM - Deployment] Pelaksanaan UAT Bersama Tim PLN & Serah Terima Hasil Pekerjaan (DELIVER ke OPERATE)',
                'engineer_id' => $leadNugraha ? $leadNugraha->id : ($pmRizki ? $pmRizki->id : 1),
                'priority'    => 'High',
                'status'      => 'In Progress',
                'progress'    => 80,
                'start_date'  => Carbon::now()->subDays(10)->format('Y-m-d'),
                'deadline'    => Carbon::now()->addDays(5)->format('Y-m-d 18:00:00'),
                'description' => 'Pengujian skenario link failover bersama IT PLN, verifikasi dokumen As-Built Document (ABD), dan finalisasi BAST 1 penyelesaian implementasi.',
                'created_by'  => $pmRizki ? $pmRizki->id : null,
            ],

            // === FASE 2: MANAGED SERVICE (OPERATION & MAINTENANCE SLA) ===
            [
                'title'       => '[MS - Operation] Aktivasi Telemetri NMS Zabbix & Verifikasi Checklist Serah Terima Operasional MS',
                'engineer_id' => $engMario ? $engMario->id : ($leadDoris ? $leadDoris->id : 1),
                'priority'    => 'High',
                'status'      => 'In Progress',
                'progress'    => 70,
                'start_date'  => Carbon::now()->subDays(8)->format('Y-m-d'),
                'deadline'    => Carbon::now()->addDays(6)->format('Y-m-d 18:00:00'),
                'description' => 'Validasi baseline IP addressing data center dari dokumen ABD PM, aktivasi polling SNMPv3 sensor NMS, dan pendaftaran kontak eskalasi 24/7 PLN.',
                'created_by'  => $leadDoris ? $leadDoris->id : null,
            ],
            [
                'title'       => '[MS - Operation] Jadwal Preventive Maintenance Triwulanan & Pemeliharaan Berkala DC PLN',
                'engineer_id' => $engEris ? $engEris->id : ($leadDoris ? $leadDoris->id : 1),
                'priority'    => 'Medium',
                'status'      => 'Assigned',
                'progress'    => 0,
                'start_date'  => Carbon::now()->addDays(20)->format('Y-m-d'),
                'deadline'    => Carbon::now()->addDays(35)->format('Y-m-d 18:00:00'),
                'description' => 'Pemeriksaan kondisi fisik rack, pembersihan filter pendingin, pengujian log alarm power ATS, dan verifikasi backup rutin berkala configuration.',
                'created_by'  => $leadDoris ? $leadDoris->id : null,
            ],
            [
                'title'       => '[MS - Operation] Helpdesk L1/L2 Standby & Penanganan Tiket Gangguan SLA Gold 4 Jam',
                'engineer_id' => $leadDoris ? $leadDoris->id : ($engMario ? $engMario->id : 1),
                'priority'    => 'High',
                'status'      => 'Assigned',
                'progress'    => 0,
                'start_date'  => Carbon::now()->addDays(5)->format('Y-m-d'),
                'deadline'    => Carbon::now()->addDays(90)->format('Y-m-d 18:00:00'),
                'description' => 'Kesiapan helpdesk 24x7 menangani insiden jaringan DC PLN, pemenuhan MTTR < 4 jam, dan penerbitan tiket CM sesuai SLA kontrak.',
                'created_by'  => $leadDoris ? $leadDoris->id : null,
            ],
        ];

        foreach ($tasksP3 as $tData) {
            $t = Task::create(array_merge($tData, ['project_id' => $p3->id]));
            if (Schema::hasTable('task_user')) {
                $t->engineers()->sync([$tData['engineer_id']]);
            }
        }

        // Seeding 4 Siklus PM Kuartalan & 1 Tiket CM untuk P3 (PLN - Kontrak 8 Tiket Kuota)
        if (Schema::hasTable('managed_service_pm_cycles')) {
            ManagedServicePmCycle::where('project_id', $p3->id)->delete();
            for ($i = 1; $i <= 4; $i++) {
                $schedDate = Carbon::now()->subMonths(1)->addMonths($i * 3);
                $status = ($i === 1) ? 'Completed' : 'Scheduled';
                $actualDate = ($i === 1) ? $schedDate->copy()->subDays(3)->toDateString() : null;

                ManagedServicePmCycle::create([
                    'project_id'           => $p3->id,
                    'cycle_number'         => $i,
                    'cycle_label'          => 'PM ' . $i . ' (Bulan ke-' . ($i * 3) . ')',
                    'scheduled_date'       => $schedDate->toDateString(),
                    'actual_date'          => $actualDate,
                    'activity_status'      => $status,
                    'assigned_engineer_id' => $engEris ? $engEris->id : null,
                    'activity_notes'       => ($i === 1) ? 'Inspeksi berkala kuartal 1 Data Center PLN Trunojoyo.' : null,
                    'report_status'        => ($i === 1) ? 'Approved' : 'Pending',
                    'billing_ba_status'    => ($i === 1) ? 'Signed' : 'Pending',
                ]);
            }
        }

        if (Schema::hasTable('managed_service_tickets')) {
            ManagedServiceTicket::where('project_id', $p3->id)->delete();
            ManagedServiceTicket::create([
                'project_id'    => $p3->id,
                'client_name'   => $p3->client,
                'ticket_number' => 'CM-PLN-2026-001',
                'title'         => 'Peringatan Degradasi RPM Fan Blower Modul 2 Spine Switch Data Center',
                'priority'      => 'Medium',
                'status'        => 'Resolved',
                'incident_date' => Carbon::now()->subDays(7),
                'resolved_at'   => Carbon::now()->subDays(7)->addHours(2),
                'sla_met'       => true,
                'description'   => 'Penggantian hot-swappable fan tray spare part PLN DC.',
                'type'          => 'Corrective Maintenance',
                'category'      => 'Hardware',
                'source'        => 'Helpdesk',
                'reported_by'   => 'Bpk. Ir. Danang Priyadi (PLN)',
                'assigned_to'   => $engEris ? $engEris->id : null,
                'sla_deadline'  => Carbon::now()->subDays(7)->addHours(4),
                'created_by'    => $leadDoris ? $leadDoris->id : 1,
            ]);
        }

        // =========================================================================
        // SINKRONISASI DOKUMEN SISTEM (6-Stage Project Documents Table)
        // =========================================================================
        if (Schema::hasTable('project_documents')) {
            $projectsList = [$p1, $p2, $p3];
            foreach ($projectsList as $proj) {
                // Stage 1: Commercial
                ProjectDocument::updateOrCreate(
                    ['project_id' => $proj->id, 'stage_number' => 1, 'document_key' => 'customer_requirement'],
                    [
                        'stage_name'      => 'Commercial',
                        'document_title'  => 'Customer Requirement & TOR',
                        'is_mandatory'    => true,
                        'file_path'       => 'project_documents/sample_sc_contract.pdf',
                        'file_name'       => 'TOR_Requirement_' . str_replace(' ', '_', $proj->client) . '.pdf',
                        'file_size'       => 245000,
                        'file_extension'  => 'pdf',
                        'status'          => 'Verified',
                        'notes'           => 'Dokumen kebutuhan awal telah divalidasi oleh Sales dan disetujui Head Division',
                        'uploaded_by'     => $proj->created_by ?: ($salesRaiza ? $salesRaiza->id : 1),
                        'uploaded_at'     => Carbon::now()->subDays(25),
                        'verified_by'     => $divHead ? $divHead->id : 1,
                        'verified_at'     => Carbon::now()->subDays(24),
                    ]
                );

                ProjectDocument::updateOrCreate(
                    ['project_id' => $proj->id, 'stage_number' => 1, 'document_key' => 'commercial_proposal'],
                    [
                        'stage_name'      => 'Commercial',
                        'document_title'  => 'Commercial Proposal & Penawaran Harga',
                        'is_mandatory'    => true,
                        'file_path'       => 'project_documents/sample_sc_contract.pdf',
                        'file_name'       => 'Quotation_' . ($proj->quotation_number ?: 'QUO-IPNET') . '.pdf',
                        'file_size'       => 312000,
                        'file_extension'  => 'pdf',
                        'status'          => 'Verified',
                        'notes'           => 'Quotation final telah disetujui oleh Direktur (Hariyadi)',
                        'uploaded_by'     => $proj->created_by ?: 1,
                        'uploaded_at'     => Carbon::now()->subDays(24),
                        'verified_by'     => $direktur ? $direktur->id : 1,
                        'verified_at'     => Carbon::now()->subDays(23),
                    ]
                );

                ProjectDocument::updateOrCreate(
                    ['project_id' => $proj->id, 'stage_number' => 1, 'document_key' => 'contract_po_so'],
                    [
                        'stage_name'      => 'Commercial',
                        'document_title'  => 'Signed Contract / Purchase Order',
                        'is_mandatory'    => true,
                        'file_path'       => 'project_documents/sample_sc_contract.pdf',
                        'file_name'       => 'Signed_' . ($proj->po_spk_number ?: 'PO_SPK') . '.pdf',
                        'file_size'       => 580000,
                        'file_extension'  => 'pdf',
                        'status'          => 'Verified',
                        'notes'           => 'Kontrak resmi bertandatangan legal dan bermaterai lengkap',
                        'uploaded_by'     => $bdmKurnijanto ? $bdmKurnijanto->id : 1,
                        'uploaded_at'     => Carbon::now()->subDays(22),
                        'verified_by'     => $direktur ? $direktur->id : 1,
                        'verified_at'     => Carbon::now()->subDays(21),
                    ]
                );

                // Stage 2: Solution
                ProjectDocument::updateOrCreate(
                    ['project_id' => $proj->id, 'stage_number' => 2, 'document_key' => 'solution_architecture'],
                    [
                        'stage_name'      => 'Solution',
                        'document_title'  => 'Diagram Topologi & Solusi Terintegrasi',
                        'is_mandatory'    => true,
                        'file_path'       => 'project_documents/sample_technical_boq.pdf',
                        'file_name'       => 'Topology_Architecture_' . $proj->id . '.pdf',
                        'file_size'       => 420000,
                        'file_extension'  => 'pdf',
                        'status'          => 'Verified',
                        'notes'           => 'Desain arsitektur teknis disusun dan divalidasi oleh SA Aris Sadewo',
                        'uploaded_by'     => $saAris ? $saAris->id : 1,
                        'uploaded_at'     => Carbon::now()->subDays(21),
                        'verified_by'     => $divHead ? $divHead->id : 1,
                        'verified_at'     => Carbon::now()->subDays(20),
                    ]
                );

                ProjectDocument::updateOrCreate(
                    ['project_id' => $proj->id, 'stage_number' => 2, 'document_key' => 'boq_bom'],
                    [
                        'stage_name'      => 'Solution',
                        'document_title'  => 'Bill of Quantity (BoQ) Final',
                        'is_mandatory'    => true,
                        'file_path'       => 'project_documents/sample_technical_boq.pdf',
                        'file_name'       => 'BoQ_BoM_Final_' . $proj->id . '.pdf',
                        'file_size'       => 380000,
                        'file_extension'  => 'pdf',
                        'status'          => 'Verified',
                        'notes'           => 'Daftar perangkat hardware & software telah diverifikasi prinsipal',
                        'uploaded_by'     => $presalesAkbar ? $presalesAkbar->id : 1,
                        'uploaded_at'     => Carbon::now()->subDays(20),
                        'verified_by'     => $saAris ? $saAris->id : 1,
                        'verified_at'     => Carbon::now()->subDays(19),
                    ]
                );

                // Stage 3 & 4: PM & Technical
                ProjectDocument::updateOrCreate(
                    ['project_id' => $proj->id, 'stage_number' => 3, 'document_key' => 'work_order'],
                    [
                        'stage_name'      => 'Project Management',
                        'document_title'  => 'Work Order Penugasan Engineer Lapangan',
                        'is_mandatory'    => true,
                        'file_path'       => 'project_documents/sample_installation_report.pdf',
                        'file_name'       => 'WorkOrder_SPK_Technical_' . $proj->id . '.pdf',
                        'file_size'       => 195000,
                        'file_extension'  => 'pdf',
                        'status'          => 'Verified',
                        'notes'           => 'Surat tugas personil engineer disetujui Lead Engineer Nugraha Pratama & PM',
                        'uploaded_by'     => $pmRizki ? $pmRizki->id : 1,
                        'uploaded_at'     => Carbon::now()->subDays(18),
                        'verified_by'     => $leadNugraha ? $leadNugraha->id : 1,
                        'verified_at'     => Carbon::now()->subDays(17),
                    ]
                );
            }
        }
    }
}

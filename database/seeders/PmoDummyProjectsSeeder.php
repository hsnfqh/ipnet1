<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use App\Models\Division;
use Carbon\Carbon;

class PmoDummyProjectsSeeder extends Seeder
{
    public function run(): void
    {
        $pmRizki = User::where('name', 'like', '%Rizki%')->first();
        $pmKuncoro = User::where('name', 'like', '%Kuncoro%')->first();
        $salesRaiza = User::where('name', 'like', '%Raiza%')->first();
        $salesNabylla = User::where('name', 'like', '%Nabylla%')->first();

        $divNetwork = Division::where('name', 'like', '%Network%')->first() ?? Division::first();
        $divSecurity = Division::where('name', 'like', '%Security%')->first() ?? Division::first();

        $engineerRorik = User::where('name', 'like', '%Rorik%')->first() ?? $pmRizki;
        $engineerEka = User::where('name', 'like', '%Eka%')->first() ?? $pmRizki;

        $standardChecklist = [
            'contract_signed' => ['checked' => true, 'note' => 'No. Kontrak: SPK-IPNET/2026/091 - Ttd Direktur'],
            'bom_proposal' => ['checked' => true, 'note' => 'BOM Commercial Proposal Final v2.1'],
            'negotiation_addendum' => ['checked' => true, 'note' => 'BA Negosiasi Harga & Scope'],
            'sow_document' => ['checked' => true, 'note' => 'Scope of Work Instalasi & Testing Terlampir'],
            'urs_requirement' => ['checked' => true, 'note' => 'Spesifikasi Teknis Sesuai RFP Customer'],
            'mockup_wireframe' => ['checked' => true, 'note' => 'Topologi Jaringan Desain SA Disetujui'],
            'maintenance_contract' => ['checked' => true, 'note' => 'Termasuk Garansi Hardware 1 Tahun'],
            'dp_payment_proof' => ['checked' => true, 'note' => 'Bukti Transfer DP 30% Terverifikasi Finance'],
            'billing_milestone' => ['checked' => true, 'note' => 'Termin 1 (DP 30%), Termin 2 (Delivery 40%), Termin 3 (BAST 30%)'],
            'customer_contacts' => ['checked' => true, 'note' => 'Bpk. Herman (IT Head - 081299887766)'],
            'meeting_notes' => ['checked' => true, 'note' => 'Catatan Kick-off Meeting & Ekspektasi SLA'],
        ];

        // 1. Proyek Deliver Aktif (On-Track)
        $p1 = Project::updateOrCreate(
            ['name' => 'Pengadaan & Implementasi SD-WAN 25 Cabang Bank Mandiri'],
            [
                'client' => 'PT Bank Mandiri (Persero) Tbk',
                'sales_name' => 'Raiza',
                'created_by' => $salesRaiza ? $salesRaiza->id : 11,
                'pm_id' => $pmRizki ? $pmRizki->id : 5,
                'division_id' => $divNetwork ? $divNetwork->id : 1,
                'stage' => 'Deliver',
                'status' => 'In Progress',
                'process_status' => 'In Progress',
                'sales_stage' => 'Closed Won',
                'progress' => 45,
                'contract_value' => 1850000000,
                'project_type' => 'One-Time Project',
                'sla_tier' => 'Platinum',
                'location' => 'Jakarta & 25 Kantor Cabang Nasional',
                'start_date' => Carbon::now()->subDays(20),
                'deadline' => Carbon::now()->addDays(40),
                'handover_status' => 'Approved',
                'handover_submitted_at' => Carbon::now()->subDays(22),
                'handover_approved_at' => Carbon::now()->subDays(20),
                'handover_data' => $standardChecklist,
                'special_notes' => 'Proyek prioritas tinggi perbankan nasional. Implementasi cutover harus dilakukan di luar jam kerja (weekend window).',
                'customer_pic_technical' => 'Bpk. Ahmad Fauzi (Head of Network Infra - 08112233445)',
                'customer_pic_business' => 'Ibu Ratna Dewi (VP IT Procurement)',
                'customer_pic_finance' => 'Bpk. Surya (Finance & Payment Ops)',
            ]
        );

        Task::updateOrCreate(
            ['project_id' => $p1->id, 'title' => 'Site Survey & Rack Mounting 25 Cabang'],
            ['engineer_id' => $engineerRorik ? $engineerRorik->id : 30, 'progress' => 100, 'status' => 'Completed', 'created_by' => $pmRizki ? $pmRizki->id : 5]
        );
        Task::updateOrCreate(
            ['project_id' => $p1->id, 'title' => 'Konfigurasi SD-WAN Edge & Orchestrator'],
            ['engineer_id' => $engineerRorik ? $engineerRorik->id : 30, 'progress' => 60, 'status' => 'In Progress', 'created_by' => $pmRizki ? $pmRizki->id : 5]
        );
        Task::updateOrCreate(
            ['project_id' => $p1->id, 'title' => 'UAT & Failover Test Dual WAN Link'],
            ['engineer_id' => $engineerRorik ? $engineerRorik->id : 30, 'progress' => 0, 'status' => 'Pending', 'created_by' => $pmRizki ? $pmRizki->id : 5]
        );
        Task::updateOrCreate(
            ['project_id' => $p1->id, 'title' => 'Dokumentasi As-Built & BAST Lapangan'],
            ['engineer_id' => $engineerRorik ? $engineerRorik->id : 30, 'progress' => 0, 'status' => 'Pending', 'created_by' => $pmRizki ? $pmRizki->id : 5]
        );

        // 2. Proyek Deliver Selesai (Siap Transisi ke Managed Service)
        $p2 = Project::updateOrCreate(
            ['name' => 'Modernisasi Firewall Data Center & Next-Gen IPS Telkomsel'],
            [
                'client' => 'PT Telekomunikasi Selular (Telkomsel)',
                'sales_name' => 'Nabylla Berlianita',
                'created_by' => $salesNabylla ? $salesNabylla->id : 9,
                'pm_id' => $pmRizki ? $pmRizki->id : 5,
                'division_id' => $divSecurity ? $divSecurity->id : 2,
                'stage' => 'Deliver',
                'status' => 'In Progress',
                'process_status' => 'In Progress',
                'sales_stage' => 'Closed Won',
                'progress' => 100,
                'contract_value' => 2400000000,
                'project_type' => 'One-Time Project',
                'sla_tier' => 'Platinum',
                'location' => 'Data Center BSD Serpong, Tangerang Selatan',
                'start_date' => Carbon::now()->subDays(60),
                'deadline' => Carbon::now()->subDays(2),
                'handover_status' => 'Approved',
                'handover_submitted_at' => Carbon::now()->subDays(62),
                'handover_approved_at' => Carbon::now()->subDays(60),
                'handover_data' => $standardChecklist,
                'special_notes' => 'Pekerjaan fisik dan UAT security audit selesai 100%. Siap diserahterimakan (handover) ke divisi Managed Service untuk kontrak pemeliharaan 24/7.',
                'customer_pic_technical' => 'Bpk. Denny Wicaksono (Security Operations Lead - 08128899001)',
                'customer_pic_business' => 'Ibu Maya Lestari (Procurement Enterprise)',
                'customer_pic_finance' => 'Bpk. Arif (Vendor Accounting)',
            ]
        );

        Task::updateOrCreate(
            ['project_id' => $p2->id, 'title' => 'Racking & Cabling Appliance DC'],
            ['engineer_id' => $engineerEka ? $engineerEka->id : 34, 'progress' => 100, 'status' => 'Completed', 'created_by' => $pmRizki ? $pmRizki->id : 5]
        );
        Task::updateOrCreate(
            ['project_id' => $p2->id, 'title' => 'Migrasi Policy NAT & High Availability'],
            ['engineer_id' => $engineerEka ? $engineerEka->id : 34, 'progress' => 100, 'status' => 'Completed', 'created_by' => $pmRizki ? $pmRizki->id : 5]
        );
        Task::updateOrCreate(
            ['project_id' => $p2->id, 'title' => 'Penetration Test & Stress Simulation'],
            ['engineer_id' => $engineerEka ? $engineerEka->id : 34, 'progress' => 100, 'status' => 'Completed', 'created_by' => $pmRizki ? $pmRizki->id : 5]
        );
        Task::updateOrCreate(
            ['project_id' => $p2->id, 'title' => 'Sign-off BAST 100%'],
            ['engineer_id' => $engineerEka ? $engineerEka->id : 34, 'progress' => 100, 'status' => 'Completed', 'created_by' => $pmRizki ? $pmRizki->id : 5]
        );

        // 3. Proyek Incoming Handover dari Sales (Menunggu Review PMO / Submitted)
        Project::updateOrCreate(
            ['name' => 'Implementasi Core Switch & Backbone Fiber Optic 10Gbps Pertamina'],
            [
                'client' => 'PT Pertamina Training & Consulting',
                'sales_name' => 'Raiza',
                'created_by' => $salesRaiza ? $salesRaiza->id : 11,
                'pm_id' => $pmRizki ? $pmRizki->id : 5,
                'division_id' => $divNetwork ? $divNetwork->id : 1,
                'stage' => 'Deliver',
                'status' => 'In Progress',
                'process_status' => 'In Progress',
                'sales_stage' => 'Closed Won',
                'progress' => 0,
                'contract_value' => 850000000,
                'project_type' => 'One-Time Project',
                'sla_tier' => 'Gold',
                'location' => 'Gedung Oil Centre Lt. 4, Jakarta Pusat',
                'start_date' => Carbon::now(),
                'deadline' => Carbon::now()->addDays(45),
                'handover_status' => 'Submitted',
                'handover_submitted_at' => Carbon::now()->subHours(3),
                'handover_data' => $standardChecklist,
                'special_notes' => 'Kontrak SPK No. SPK-PTC/NET/2026/089 telah ditandatangani. DP 30% sudah masuk dan divalidasi Finance. Berkas lengkap, siap diverifikasi PMO untuk penugasan tim lapangan.',
                'customer_pic_technical' => 'Bpk. Wisnu Pratama (IT Infrastructure - 08137788990)',
                'customer_pic_business' => 'Bpk. Ridwan (General Procurement)',
                'customer_pic_finance' => 'Ibu Nita (Finance Section Head)',
            ]
        );

        // 4. Proyek Incoming Handover dari Sales (Revisi Bersyarat / Conditional)
        $checklistConditional = $standardChecklist;
        $checklistConditional['sow_document']['checked'] = false;
        $checklistConditional['sow_document']['note'] = 'REVISI: Lampiran SOW rincian port mapping belum ditandatangani customer';

        Project::updateOrCreate(
            ['name' => 'Upgrade SIEM Enterprise & Security Operation Center LinkAja'],
            [
                'client' => 'PT Fintek Karya Nusantara (LinkAja)',
                'sales_name' => 'Nabylla Berlianita',
                'created_by' => $salesNabylla ? $salesNabylla->id : 9,
                'pm_id' => $pmKuncoro ? $pmKuncoro->id : 4,
                'division_id' => $divSecurity ? $divSecurity->id : 2,
                'stage' => 'Deliver',
                'status' => 'In Progress',
                'process_status' => 'In Progress',
                'sales_stage' => 'Closed Won',
                'progress' => 0,
                'contract_value' => 1200000000,
                'project_type' => 'One-Time Project',
                'sla_tier' => 'Platinum',
                'location' => 'Menara Digital Center, Jakarta Selatan',
                'start_date' => Carbon::now(),
                'deadline' => Carbon::now()->addDays(60),
                'handover_status' => 'Conditional',
                'handover_submitted_at' => Carbon::now()->subDays(2),
                'handover_conditional_notes' => 'Lampiran Statement of Work (SoW) rincian port mapping API log collector belum ditandatangani oleh PIC Teknis customer. Mohon tim Sales minta sign-off resmi sebelum approval kick-off lapangan.',
                'handover_conditional_deadline' => Carbon::now()->addDays(3),
                'handover_data' => $checklistConditional,
                'special_notes' => 'Catatan revisi bersyarat telah disampaikan ke Sales Nabylla Berlianita.',
                'customer_pic_technical' => 'Bpk. Fajar Ramadhan (CISO LinkAja - 081234112233)',
                'customer_pic_business' => 'Ibu Cynthia (Procurement Tech)',
                'customer_pic_finance' => 'Bpk. Budi (Finance Analyst)',
            ]
        );

        // 5. Proyek Deliver Aktif (Mendekati Tenggat / At-Risk)
        $p5 = Project::updateOrCreate(
            ['name' => 'Pembangunan LAN & High-Density Wi-Fi 6 Kampus Binus'],
            [
                'client' => 'Universitas Bina Nusantara',
                'sales_name' => 'Raiza',
                'created_by' => $salesRaiza ? $salesRaiza->id : 11,
                'pm_id' => $pmRizki ? $pmRizki->id : 5,
                'division_id' => $divNetwork ? $divNetwork->id : 1,
                'stage' => 'Deliver',
                'status' => 'In Progress',
                'process_status' => 'In Progress',
                'sales_stage' => 'Closed Won',
                'progress' => 25,
                'contract_value' => 650000000,
                'project_type' => 'One-Time Project',
                'sla_tier' => 'Gold',
                'location' => 'Kampus Binus Alam Sutera, Tangerang',
                'start_date' => Carbon::now()->subDays(25),
                'deadline' => Carbon::now()->addDays(4), // Mendekati tenggat (At-Risk)
                'handover_status' => 'Approved',
                'handover_submitted_at' => Carbon::now()->subDays(27),
                'handover_approved_at' => Carbon::now()->subDays(25),
                'handover_data' => $standardChecklist,
                'special_notes' => 'Tenggat waktu tinggal 4 hari. Perlu percepatan pemasangan 40 titik Access Point di lantai 2 & 3 agar tidak terkena penalty SLA.',
                'customer_pic_technical' => 'Bpk. Irwan Setiawan (Head of IT Infrastructure Binus - 08180909090)',
                'customer_pic_business' => 'Ibu Jessica (Procurement)',
                'customer_pic_finance' => 'Ibu Linda (Finance Univ)',
            ]
        );

        Task::updateOrCreate(
            ['project_id' => $p5->id, 'title' => 'Penarikan Kabel UTP Cat6 Lantai 1-3'],
            ['engineer_id' => $engineerRorik ? $engineerRorik->id : 30, 'progress' => 100, 'status' => 'Completed', 'created_by' => $pmRizki ? $pmRizki->id : 5]
        );
        Task::updateOrCreate(
            ['project_id' => $p5->id, 'title' => 'Mounting Access Point Wi-Fi 6 (40 Titik)'],
            ['engineer_id' => $engineerRorik ? $engineerRorik->id : 30, 'progress' => 30, 'status' => 'In Progress', 'created_by' => $pmRizki ? $pmRizki->id : 5]
        );
        Task::updateOrCreate(
            ['project_id' => $p5->id, 'title' => 'Integrasi SSID & RADIUS Portal'],
            ['engineer_id' => $engineerRorik ? $engineerRorik->id : 30, 'progress' => 0, 'status' => 'Pending', 'created_by' => $pmRizki ? $pmRizki->id : 5]
        );
    }
}

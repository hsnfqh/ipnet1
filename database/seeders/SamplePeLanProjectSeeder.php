<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Division;
use App\Models\Team;
use App\Models\Project;
use App\Models\Task;
use App\Models\ProjectDocument;
use App\Models\ManagedServiceAsset;
use App\Models\ManagedServiceTicket;
use App\Models\ManagedServicePmCycle;
use App\Services\ProjectDocumentFlowService;
use App\Http\Controllers\ManagedServiceController;

class SamplePeLanProjectSeeder extends Seeder
{
    /**
     * Jalankan Seeder
     */
    public function run(): void
    {
        self::runDirectly();
    }

    /**
     * Static runner yang dapat dipanggil dari mana saja (Seeder, Controller, Route)
     */
    public static function runDirectly(): Project
    {
        // 1. Pastikan file dokumen dummy valid tersedia di disk fisik
        self::ensureSampleFilesExist();

        // 2. Ambil referensi akun-akun resmi
        $raiza   = User::where('name', 'like', '%Raiza%')->first() ?? User::where('email', 'like', '%raiza%')->first();
        $akbar   = User::where('name', 'like', '%Akbar%')->first() ?? User::where('email', 'like', '%akbar%')->first();
        $aris    = User::where('name', 'like', '%Aris%')->first() ?? User::where('email', 'like', '%aris%')->first();
        $novan   = User::where('name', 'like', '%Novan%')->first() ?? User::where('email', 'like', '%novan%')->first();
        $susanto = User::where('name', 'like', '%Susanto%')->first() ?? User::where('email', 'like', '%susanto%')->first();
        $hariyadi= User::where('name', 'like', '%Hariyadi%')->first() ?? User::where('email', 'like', '%hariyadi%')->first();
        $rizki   = User::where('name', 'like', '%Rizki%')->first() ?? User::where('email', 'like', '%rizki%')->first();
        $nugraha = User::where('name', 'like', '%Nugraha%')->first() ?? User::where('email', 'like', '%nugraha%')->first();
        $rorik   = User::where('name', 'like', '%Rorik%')->first() ?? User::where('email', 'like', '%rorik%')->first();
        $dedy    = User::where('name', 'like', '%Dedy%')->first() ?? User::where('email', 'like', '%dedy%')->first();
        $doris   = User::where('name', 'like', '%Doris%')->first() ?? User::where('email', 'like', '%doris%')->first();
        $mario   = User::where('name', 'like', '%Mario%')->first() ?? User::where('email', 'like', '%mario%')->first();
        $eris    = User::where('name', 'like', '%Eris%')->first() ?? User::where('email', 'like', '%eris%')->first();

        $defaultCreatorId = $raiza?->id ?: (User::first()?->id ?: 1);

        // 3. Divisi & Tim Network
        $divNet = Division::where('name', 'like', '%Network%')->first();
        $teamNet = Team::where('name', 'like', '%Network%')->first();

        // 4. Struktur data Handover JSON lengkap
        $handoverData = [
            'draft_approvals' => [
                'head' => [
                    'assigned'         => true,
                    'approved'         => true,
                    'by'               => ($susanto ? $susanto->name : 'Susanto Djaya') . ' (Head Divisi)',
                    'assigned_to'      => $susanto ? $susanto->name : 'Pak Susanto Djaya',
                    'assigned_user_id' => $susanto?->id,
                    'date'             => '15 Oct 2026 10:00',
                    'notes'            => 'Kelayakan teknis, alokasi resource engineer network & maintenance, serta kepatuhan arsitektur disetujui penuh.',
                ],
                'director' => [
                    'assigned'         => true,
                    'approved'         => true,
                    'by'               => ($hariyadi ? $hariyadi->name : 'Hariyadi') . ' (Direktur)',
                    'assigned_to'      => $hariyadi ? $hariyadi->name : 'Pak Hariyadi',
                    'assigned_user_id' => $hariyadi?->id,
                    'date'             => '15 Oct 2026 14:30',
                    'notes'            => 'Otorisasi anggaran dan pengesahan kontrak pemeliharaan router edge PE LAN BRI Sudirman disahkan.',
                ],
            ],
            'technical_assignments' => [
                'bdm' => [
                    'assigned'         => true,
                    'assigned_to'      => $novan ? $novan->name : 'Novan Pudjirachmanto',
                    'assigned_user_id' => $novan?->id,
                    'assigned_by'      => $raiza ? $raiza->name : 'Raiza',
                    'assigned_at'      => '16 Oct 2026 09:00',
                    'status'           => 'Assigned',
                ],
                'presales' => [
                    'assigned'         => true,
                    'assigned_to'      => $akbar ? $akbar->name : 'Akbar',
                    'assigned_user_id' => $akbar?->id,
                    'assigned_by'      => $raiza ? $raiza->name : 'Raiza',
                    'assigned_at'      => '16 Oct 2026 09:15',
                    'sales_notes'      => 'Siapkan dokumen proposal teknis & lingkup kerja SOW perpanjangan router edge PE LAN BRI Sudirman.',
                    'status'           => 'Completed',
                    'completed_at'     => '17 Oct 2026 11:00',
                    'document_path'    => 'sample_docs/Proposal_Teknis_SOW_PE_LAN_BRI.pdf',
                    'document_name'    => 'Proposal_Teknis_SOW_PE_LAN_BRI.pdf',
                    'document_title'   => 'Proposal Teknis & Ruang Lingkup SOW PE LAN Sudirman',
                    'notes'            => 'Proposal SOW dan BoQ telah selesai disusun sesuai spek teknis Bank BRI.',
                ],
                'architect' => [
                    'assigned'         => true,
                    'assigned_to'      => $aris ? $aris->name : 'Aris Sadewo (Solution Architect)',
                    'assigned_user_id' => $aris?->id,
                    'assigned_by'      => $raiza ? $raiza->name : 'Raiza',
                    'assigned_at'      => '16 Oct 2026 09:15',
                    'sales_notes'      => 'Sediakan diagram topologi router PE LAN Sudirman dan redundansi link BGP failover.',
                    'status'           => 'Completed',
                    'completed_at'     => '17 Oct 2026 13:45',
                    'document_path'    => 'sample_docs/Topologi_Arsitektur_PE_LAN_Sudirman.pdf',
                    'document_name'    => 'Topologi_Arsitektur_PE_LAN_Sudirman.pdf',
                    'document_title'   => 'Desain Arsitektur Topologi & Sizing PE LAN Sudirman',
                    'notes'            => 'Diagram topologi dual uplink BGP peering telah divalidasi dan diuji kesesuaiannya.',
                ],
                'bd_verification' => [
                    'status'       => 'Approved',
                    'submitted_at' => '17 Oct 2026 14:00',
                    'submitted_by' => $akbar ? $akbar->name : 'Akbar (Pre-Sales)',
                    'verified_by'  => $novan ? $novan->name : 'Novan Pudjirachmanto (BDM)',
                    'verified_at'  => '17 Oct 2026 16:30',
                    'notes'        => 'Dokumen solusi teknis dan topologi telah sesuai standar kualifikasi Bank BRI. Disetujui untuk pengajuan komersial resmi.',
                ],
            ],
            'pmo_handover' => [
                'confirmed'    => true,
                'confirmed_at' => '18 Oct 2026 09:00',
                'confirmed_by' => $rizki ? $rizki->name : 'Rizki (Project Manager)',
                'notes'        => 'Proyek resmi disahkan oleh PMO. Pelaksanaan delivery fisik dan operasional lapangan aktif berjalan.',
            ],
            'lead_engineer_delegation' => [
                'delegated'    => true,
                'delegated_at' => '18 Oct 2026 10:30',
                'lead_name'    => $nugraha ? $nugraha->name : 'Nugraha Pratama',
                'team_name'    => 'Tim Network',
                'engineers'    => ['Rorik', 'Dedy Suryana'],
                'notes'        => 'Personel teknisi lapangan telah ditugaskan untuk audit fisik, backup running-config, dan persiapan maintenance window.',
            ],
            'ms_handover' => [
                'handed_over'    => true,
                'handed_over_at' => '18 Oct 2026 14:00',
                'handed_over_by' => ($rizki ? $rizki->name : 'Rizki') . ' (PMO)',
                'ms_lead_id'     => $doris?->id,
                'ms_lead_name'   => $doris ? $doris->name : 'Doris',
                'ms_engineers'   => ['Mario Handoko', 'Eris'],
                'ms_engineer_ids'=> array_filter([$mario?->id, $eris?->id]),
                'engineer_notes' => 'SLA 24x7 standby support, sistem monitoring SNMP, dan jadwal 4 siklus PM triwulanan telah diaktifkan di dashboard Managed Service.',
            ],
        ];

        // 5. Buat atau perbarui Project
        $projectName = 'Perpanjangan Pemeliharaan Router Edge LAN (PE LAN) Sudirman';

        $project = Project::updateOrCreate(
            ['name' => $projectName],
            [
                'client'                     => 'PT Bank Rakyat Indonesia (Persero) Tbk',
                'sales_name'                 => $raiza ? $raiza->name : 'Raiza',
                'presales_name'              => $akbar ? $akbar->name : 'Akbar',
                'location'                   => 'Gedung Kantor Pusat BRI Sudirman Lt. 8, Jakarta Pusat',
                'project_type'               => 'Implementation & Managed Service',
                'sla_tier'                   => 'Platinum',
                'status'                     => 'In Progress', // Sesuai permintaan: baru sampai in-progress, BUKAN completed
                'stage'                      => 'Deliver',
                'process_status'             => 'In Progress',
                'acquire_status'             => 'Completed',
                'sales_stage'                => 'Closed Won',
                'win_probability'            => 100,
                'contract_value'             => 850000000,
                'progress'                   => 35,
                'start_date'                 => '2026-10-01',
                'deadline'                   => '2027-09-30',
                'service_start_date'         => '2026-10-01',
                'service_end_date'           => '2027-09-30',
                'po_spk_number'              => 'SPK/BRI/TI-OPS/2026/088',
                'po_spk_date'                => '2026-10-15',
                'po_spk_file'                => 'sample_docs/SPK_BRI_PE_LAN_Sudirman_Signed.pdf',
                'quotation_number'           => 'QUO/IPNET/BRI/2026/102',
                'quotation_amount'           => 850000000,
                'quotation_file'             => 'sample_docs/Penawaran_Harga_PE_LAN_Sudirman_BRI.pdf',
                'tor_number'                 => 'TOR/BRI/NET-SDM/2026/041',
                'tor_file'                   => 'sample_docs/TOR_Pemeliharaan_Router_Edge_LAN_Sudirman.pdf',
                'contract_file'              => 'sample_docs/Kontrak_Perpanjangan_PE_LAN_Sudirman.pdf',
                'boq_file'                   => 'sample_docs/BoQ_BoM_Router_Edge_LAN_Sudirman.xlsx',
                'proposal_file'              => 'sample_docs/Proposal_Teknis_SOW_PE_LAN_BRI.pdf',
                'commercial_handover_status' => 'Approved',
                'commercial_handover_at'     => '2026-10-16 10:00:00',
                'commercial_handover_by'     => $raiza?->id ?: $defaultCreatorId,
                'handover_target'            => 'both', // Proyek Implementasi & Managed Service
                'handover_status'            => 'Approved',
                'handover_approved_at'       => '2026-10-16 11:30:00',
                'handover_approved_by'       => $susanto?->id ?: $defaultCreatorId,
                'ms_handover_status'         => 'Submitted',
                'sla_commitment'             => '99.50% Uptime, 24x7 Standby On-Site Support, MTTR < 2 Jam',
                'sla_coverage_hours'         => '24x7',
                'maintenance_frequency'      => 'Quarterly',
                'pm_frequency_type'          => 'per_3_bulan',
                'pm_total_cycles'            => 4,
                'cm_quota'                   => 12,
                'mttr_hours'                 => 2,
                'call_response_time'         => '15 menit',
                'incident_resolution_hours'  => 2,
                'sla_target_percentage'      => 99.50,
                'customer_pic_technical'     => 'Bpk. Agus Rahardjo (AVP Network Infrastructure BRI - 081287654321)',
                'customer_pic_business'      => 'Ibu Retno Lestari (Procurement & Vendor Management BRI - 08119876543)',
                'customer_pic_finance'       => 'Bpk. Bambang Suherman (Finance & Accounting BRI)',
                'division_id'                => $divNet?->id,
                'pm_id'                      => $rizki?->id,
                'bdm_id'                     => $novan?->id,
                'created_by'                 => $defaultCreatorId,
                'description'                => 'Perpanjangan kontrak pemeliharaan preventif, korektif, upgrade firmware, dan managed service operasional 24x7 router edge LAN (PE LAN) Gedung Kantor Pusat BRI Sudirman.',
                'handover_data'              => $handoverData,
                'documents_checklist'        => [
                    'tor'      => true,
                    'sph'      => true,
                    'spk'      => true,
                    'proposal' => true,
                    'topologi' => true,
                    'boq'      => true,
                    'bast'     => false,
                ],
            ]
        );

        // 6. Buat Tasks Lapangan yang sudah didelegasikan ke teknisi
        self::ensureFieldTasksExist($project, $rizki, $rorik, $dedy, $doris);

        // 7. Buat Asset Perangkat di Managed Service
        self::ensureMsAssetsExist($project, $doris);

        // 8. Inisialisasi Siklus PM
        ManagedServiceController::initPmCycles($project);

        // 9. Buat Tiket Operasional Awal di Managed Service
        self::ensureMsTicketsExist($project, $doris, $rorik);

        // 10. Inisialisasi dan lengkapi Project Documents 6-Tahap
        self::ensureProjectDocumentsPopulated($project, $raiza, $akbar, $aris, $novan, $susanto);

        return $project;
    }

    /**
     * Pastikan Tasks teknis didelegasikan ke teknisi lapangan
     */
    private static function ensureFieldTasksExist(Project $project, $pm, $rorik, $dedy, $doris): void
    {
        $creatorId = $pm?->id ?: (auth()->id() ?: 1);

        $tasksData = [
            [
                'title'       => 'Audit Fisik, Backup Running-Config & Health Check Router Edge PE LAN Sudirman',
                'description' => 'Pemeriksaan kondisi hardware, optic transceiver SFP+, utilisasi CPU/Memory, dan backup konfigurasi terkini Router Edge PE LAN Gedung BRI Sudirman.',
                'status'      => 'In Progress',
                'progress'    => 50,
                'priority'    => 'High',
                'start_date'  => '2026-10-18',
                'deadline'    => '2026-10-25 17:00:00',
                'engineer_id' => $rorik?->id,
            ],
            [
                'title'       => 'Upgrade Firmware NOS Cisco IOS-XE & Pengujian Redundansi Failover BGP HSRP',
                'description' => 'Eksekusi maintenance window malam: upgrade patch keamanan Cisco IOS-XE dan uji failover link redundant PE-LAN-SDR-01 ke PE-LAN-SDR-02.',
                'status'      => 'Assigned',
                'progress'    => 0,
                'priority'    => 'Urgent',
                'start_date'  => '2026-10-26',
                'deadline'    => '2026-10-31 23:00:00',
                'engineer_id' => $dedy?->id,
            ],
            [
                'title'       => 'Operasional Pemeliharaan Berkala & Monitoring SLA Managed Service',
                'description' => 'Monitoring berkala metrik SLA 99.50%, penanganan eskalasi tiket CM, dan pelaksanaan siklus Preventive Maintenance triwulanan.',
                'status'      => 'Assigned',
                'progress'    => 0,
                'priority'    => 'Medium',
                'start_date'  => '2026-10-18',
                'deadline'    => '2027-09-30 23:59:59',
                'engineer_id' => $doris?->id,
            ],
        ];

        foreach ($tasksData as $tData) {
            $task = Task::updateOrCreate(
                [
                    'project_id' => $project->id,
                    'title'      => $tData['title'],
                ],
                array_merge($tData, ['created_by' => $creatorId])
            );

            if ($tData['engineer_id'] && Schema::hasTable('task_user')) {
                $task->engineers()->syncWithoutDetaching([$tData['engineer_id']]);
            }
        }
    }

    /**
     * Pastikan Aset Router PE LAN Terdaftar di Managed Service
     */
    private static function ensureMsAssetsExist(Project $project, $doris): void
    {
        if (!Schema::hasTable('managed_service_assets')) {
            return;
        }

        $creatorId = $doris?->id ?: (auth()->id() ?: 1);

        ManagedServiceAsset::updateOrCreate(
            [
                'project_id'    => $project->id,
                'serial_number' => 'FOC2648BRI01',
            ],
            [
                'client_name'         => 'PT Bank Rakyat Indonesia (Persero) Tbk',
                'device_name'         => 'Router Edge LAN Sudirman (PE-LAN-SDR-01)',
                'product_description' => 'Router Edge Utama Penghubung Core LAN Gedung Kantor Pusat BRI Sudirman ke WAN / Backhaul',
                'category'            => 'Router',
                'brand'               => 'Cisco',
                'model'               => 'Catalyst 8300 (C8300-1N1S-6T)',
                'quantity'            => 1,
                'ip_address'          => '10.210.1.1',
                'location_site'       => 'Gedung Kantor Pusat BRI Sudirman Lt. 8',
                'rack_position'       => 'Rack NETWORK-PE-01 (U22-U24)',
                'status'              => 'Online',
                'warranty_expiry'     => '2027-10-01',
                'notes'               => 'Router Edge Utama terhubung ke Core Backbone & WAN Provider. BGP AS 64512.',
                'created_by'          => $creatorId,
            ]
        );

        ManagedServiceAsset::updateOrCreate(
            [
                'project_id'    => $project->id,
                'serial_number' => 'FOC2648BRI02',
            ],
            [
                'client_name'         => 'PT Bank Rakyat Indonesia (Persero) Tbk',
                'device_name'         => 'Router Edge LAN Sudirman Redundant (PE-LAN-SDR-02)',
                'product_description' => 'Router Edge Sekunder Hot Standby Redundant (HSRP Group 10)',
                'category'            => 'Router',
                'brand'               => 'Cisco',
                'model'               => 'Catalyst 8300 (C8300-1N1S-6T)',
                'quantity'            => 1,
                'ip_address'          => '10.210.1.2',
                'location_site'       => 'Gedung Kantor Pusat BRI Sudirman Lt. 8',
                'rack_position'       => 'Rack NETWORK-PE-01 (U25-U27)',
                'status'              => 'Online',
                'warranty_expiry'     => '2027-10-01',
                'notes'               => 'Router Edge Secondary HA Failover Gedung BRI Sudirman.',
                'created_by'          => $creatorId,
            ]
        );
    }

    /**
     * Pastikan Tiket Awal Managed Service Terbuat
     */
    private static function ensureMsTicketsExist(Project $project, $doris, $rorik): void
    {
        if (!Schema::hasTable('managed_service_tickets')) {
            return;
        }

        $creatorId = $doris?->id ?: (auth()->id() ?: 1);
        $asset = ManagedServiceAsset::where('project_id', $project->id)->first();

        ManagedServiceTicket::updateOrCreate(
            [
                'ticket_number' => 'CM-BRI-2026-001',
            ],
            [
                'project_id'       => $project->id,
                'client_name'      => 'PT Bank Rakyat Indonesia (Persero) Tbk',
                'title'            => 'Pemeriksaan Suhu & Pembersihan Optik Transceiver SFP+ Router PE LAN Sudirman',
                'description'      => 'Pembersihan berkala dan pengecekan optical power attenuation pada interface TenGigabitEthernet0/1/0 & TenGigabitEthernet0/1/1.',
                'incident_date'    => now()->subDays(1),
                'type'             => 'Preventive Maintenance',
                'category'         => 'Router',
                'priority'         => 'Gold',
                'status'           => 'In Progress',
                'source'           => 'Portal',
                'reported_by'      => 'Agus Rahardjo (BRI)',
                'contact_phone'    => '081287654321',
                'contact_email'    => 'agus.rahardjo@corp.bri.co.id',
                'assigned_to'      => $rorik?->id,
                'asset_id'         => $asset?->id,
                'sla_deadline'     => now()->addDays(2),
                'created_by'       => $creatorId,
            ]
        );
    }

    /**
     * Pastikan Dokumen 6-Tahap Lengkap Terdaftar
     */
    private static function ensureProjectDocumentsPopulated(Project $project, $raiza, $akbar, $aris, $novan, $susanto): void
    {
        if (!Schema::hasTable('project_documents')) {
            return;
        }

        ProjectDocumentFlowService::ensureProjectDocumentsInitialized($project);

        $docsToFill = [
            // Stage 1: Commercial
            'customer_requirement' => [
                'name'           => 'TOR_Pemeliharaan_Router_Edge_LAN_Sudirman.pdf',
                'file_name'      => 'TOR_Pemeliharaan_Router_Edge_LAN_Sudirman.pdf',
                'file_path'      => 'sample_docs/TOR_Pemeliharaan_Router_Edge_LAN_Sudirman.pdf',
                'file_size'      => 482000,
                'file_extension' => 'pdf',
                'status'         => 'Verified',
                'notes'          => 'Kerangka Acuan Kerja (KAK) & TOR resmi dari Divisi TI Kantor Pusat BRI Sudirman.',
                'uploaded_by'    => $raiza?->id,
                'verified_by'    => $susanto?->id,
            ],
            'commercial_proposal' => [
                'name'           => 'Penawaran_Harga_PE_LAN_Sudirman_BRI.pdf',
                'file_name'      => 'Penawaran_Harga_PE_LAN_Sudirman_BRI.pdf',
                'file_path'      => 'sample_docs/Penawaran_Harga_PE_LAN_Sudirman_BRI.pdf',
                'file_size'      => 342000,
                'file_extension' => 'pdf',
                'status'         => 'Verified',
                'notes'          => 'Surat Penawaran Harga (SPH) resmi dan rincian paket SLA Platinum tahun 2026-2027.',
                'uploaded_by'    => $raiza?->id,
                'verified_by'    => $susanto?->id,
            ],
            'contract_po_so' => [
                'name'           => 'SPK_BRI_PE_LAN_Sudirman_Signed.pdf',
                'file_name'      => 'SPK_BRI_PE_LAN_Sudirman_Signed.pdf',
                'file_path'      => 'sample_docs/SPK_BRI_PE_LAN_Sudirman_Signed.pdf',
                'file_size'      => 876000,
                'file_extension' => 'pdf',
                'status'         => 'Verified',
                'notes'          => 'Surat Perintah Kerja (SPK) bernomor SPK/BRI/TI-OPS/2026/088 telah ditandatangani kedua pihak.',
                'uploaded_by'    => $raiza?->id,
                'verified_by'    => $susanto?->id,
            ],
            'commercial_handover_package' => [
                'name'           => 'Commercial_Handover_Package_BRI_Sudirman.pdf',
                'file_name'      => 'Commercial_Handover_Package_BRI_Sudirman.pdf',
                'file_path'      => 'sample_docs/Commercial_Handover_Package_BRI_Sudirman.pdf',
                'file_size'      => 295000,
                'file_extension' => 'pdf',
                'status'         => 'Verified',
                'notes'          => 'Paket serah terima komersial ke Delivery PMO & Operasional Managed Service.',
                'uploaded_by'    => $raiza?->id,
                'verified_by'    => $susanto?->id,
            ],

            // Stage 2: Solution
            'solution_concept' => [
                'name'           => 'Konsep_Solusi_Pemeliharaan_PE_LAN_BRI.pdf',
                'file_name'      => 'Konsep_Solusi_Pemeliharaan_PE_LAN_BRI.pdf',
                'file_path'      => 'sample_docs/Konsep_Solusi_Pemeliharaan_PE_LAN_BRI.pdf',
                'file_size'      => 512000,
                'file_extension' => 'pdf',
                'status'         => 'Verified',
                'notes'          => 'Kerangka konseptual pemeliharaan preventif, korektif, dan SLA monitoring 24x7.',
                'uploaded_by'    => $akbar?->id,
                'verified_by'    => $novan?->id,
            ],
            'solution_architecture' => [
                'name'           => 'Topologi_Arsitektur_PE_LAN_Sudirman.pdf',
                'file_name'      => 'Topologi_Arsitektur_PE_LAN_Sudirman.pdf',
                'file_path'      => 'sample_docs/Topologi_Arsitektur_PE_LAN_Sudirman.pdf',
                'file_size'      => 780000,
                'file_extension' => 'pdf',
                'status'         => 'Verified',
                'notes'          => 'Diagram arsitektur jaringan dual-router Cisco Catalyst 8300 dengan HSRP & BGP peering.',
                'uploaded_by'    => $aris?->id,
                'verified_by'    => $novan?->id,
            ],
            'hld_lld' => [
                'name'           => 'HLD_LLD_Desain_Jaringan_PE_LAN_Sudirman.pdf',
                'file_name'      => 'HLD_LLD_Desain_Jaringan_PE_LAN_Sudirman.pdf',
                'file_path'      => 'sample_docs/HLD_LLD_Desain_Jaringan_PE_LAN_Sudirman.pdf',
                'file_size'      => 1240000,
                'file_extension' => 'pdf',
                'status'         => 'Verified',
                'notes'          => 'High-Level Design & Low-Level Design konfigurasi IP addressing, VRF, dan security ACL.',
                'uploaded_by'    => $aris?->id,
                'verified_by'    => $novan?->id,
            ],
            'technical_proposal' => [
                'name'           => 'Proposal_Teknis_SOW_PE_LAN_BRI.pdf',
                'file_name'      => 'Proposal_Teknis_SOW_PE_LAN_BRI.pdf',
                'file_path'      => 'sample_docs/Proposal_Teknis_SOW_PE_LAN_BRI.pdf',
                'file_size'      => 930000,
                'file_extension' => 'pdf',
                'status'         => 'Verified',
                'notes'          => 'Proposal teknis SOW lengkap yang telah disetujui PIC BD Novan.',
                'uploaded_by'    => $akbar?->id,
                'verified_by'    => $novan?->id,
            ],
            'boq_bom' => [
                'name'           => 'BoQ_BoM_Router_Edge_LAN_Sudirman.xlsx',
                'file_name'      => 'BoQ_BoM_Router_Edge_LAN_Sudirman.xlsx',
                'file_path'      => 'sample_docs/BoQ_BoM_Router_Edge_LAN_Sudirman.xlsx',
                'file_size'      => 145000,
                'file_extension' => 'xlsx',
                'status'         => 'Verified',
                'notes'          => 'Daftar suku cadang transceiver SFP+, kabel patch cord LC-LC, dan lisensi IOS-XE DNA Advantage.',
                'uploaded_by'    => $akbar?->id,
                'verified_by'    => $novan?->id,
            ],

            // Stage 3: Governance
            'project_charter' => [
                'name'           => 'Project_Charter_PE_LAN_Sudirman_BRI.pdf',
                'file_name'      => 'Project_Charter_PE_LAN_Sudirman_BRI.pdf',
                'file_path'      => 'sample_docs/Project_Charter_PE_LAN_Sudirman_BRI.pdf',
                'file_size'      => 320000,
                'file_extension' => 'pdf',
                'status'         => 'Uploaded',
                'notes'          => 'Piagam proyek otorisasi pelaksanaan implementasi dan operasional pemeliharaan tahun 2026-2027.',
                'uploaded_by'    => $susanto?->id,
                'verified_by'    => null,
            ],
            'kick_off_presentation' => [
                'name'           => 'Kick_Off_Meeting_Deck_PE_LAN_BRI.pdf',
                'file_name'      => 'Kick_Off_Meeting_Deck_PE_LAN_BRI.pdf',
                'file_path'      => 'sample_docs/Kick_Off_Meeting_Deck_PE_LAN_BRI.pdf',
                'file_size'      => 1850000,
                'file_extension' => 'pdf',
                'status'         => 'Uploaded',
                'notes'          => 'Materi kick-off meeting koordinasi jadwal maintenance window bersama PIC TI BRI.',
                'uploaded_by'    => $raiza?->id,
                'verified_by'    => null,
            ],

            // Stage 6: Operate
            'sla_agreement' => [
                'name'           => 'Service_Level_Agreement_BRI_99.5.pdf',
                'file_name'      => 'Service_Level_Agreement_BRI_99.5.pdf',
                'file_path'      => 'sample_docs/Service_Level_Agreement_BRI_99.5.pdf',
                'file_size'      => 410000,
                'file_extension' => 'pdf',
                'status'         => 'Uploaded',
                'notes'          => 'Komitmen jaminan SLA ketersediaan 99.50% dan respon insiden darurat 24x7.',
                'uploaded_by'    => $susanto?->id,
                'verified_by'    => null,
            ],
            'support_contact_list' => [
                'name'           => 'Matriks_Eskalasi_Support_24x7_IPNET.pdf',
                'file_name'      => 'Matriks_Eskalasi_Support_24x7_IPNET.pdf',
                'file_path'      => 'sample_docs/Matriks_Eskalasi_Support_24x7_IPNET.pdf',
                'file_size'      => 210000,
                'file_extension' => 'pdf',
                'status'         => 'Uploaded',
                'notes'          => 'Daftar nomor kontak darurat NOC IP-NET, Service Desk Managed Service, dan PIC Prinsipal Cisco TAC.',
                'uploaded_by'    => $raiza?->id,
                'verified_by'    => null,
            ],
        ];

        foreach ($docsToFill as $key => $docInfo) {
            ProjectDocument::where('project_id', $project->id)
                ->where('document_key', $key)
                ->update([
                    'name'           => $docInfo['name'],
                    'file_name'      => $docInfo['file_name'],
                    'file_path'      => $docInfo['file_path'],
                    'file_size'      => $docInfo['file_size'],
                    'file_extension' => $docInfo['file_extension'],
                    'status'         => $docInfo['status'],
                    'notes'          => $docInfo['notes'],
                    'uploaded_by'    => $docInfo['uploaded_by'] ?: 1,
                    'uploaded_at'    => now()->subDays(2),
                    'verified_by'    => $docInfo['verified_by'],
                    'verified_at'    => $docInfo['verified_by'] ? now()->subDay() : null,
                ]);
        }
    }

    /**
     * Buat file PDF dummy valid di folder storage agar saat didownload / dibuka tidak 404
     */
    public static function ensureSampleFilesExist(): void
    {
        $baseDir = (defined('LARAVEL_START') && function_exists('app') && app()->bound('path.base')) ? base_path() : dirname(__DIR__, 2);
        $dirs = [
            $baseDir . '/storage/app/public/sample_docs',
            $baseDir . '/public/storage/sample_docs',
        ];

        foreach ($dirs as $dir) {
            if (!file_exists($dir)) {
                @mkdir($dir, 0755, true);
            }
        }

        $sampleFiles = [
            'TOR_Pemeliharaan_Router_Edge_LAN_Sudirman.pdf'        => 'KERANGKA ACUAN KERJA (TOR) - PEMELIHARAAN ROUTER EDGE LAN BRI SUDIRMAN',
            'Penawaran_Harga_PE_LAN_Sudirman_BRI.pdf'              => 'SURAT PENAWARAN HARGA (SPH) - PE LAN SUDIRMAN TAHUN 2026-2027',
            'SPK_BRI_PE_LAN_Sudirman_Signed.pdf'                  => 'SURAT PERINTAH KERJA (SPK) BERNO: SPK/BRI/TI-OPS/2026/088 (SIGNED)',
            'Commercial_Handover_Package_BRI_Sudirman.pdf'         => 'PAKET COMMERCIAL HANDOVER - PT BANK RAKYAT INDONESIA TBK',
            'Proposal_Teknis_SOW_PE_LAN_BRI.pdf'                  => 'PROPOSAL TEKNIS & SCOPE OF WORK (SOW) ROUTER EDGE LAN SUDIRMAN',
            'Topologi_Arsitektur_PE_LAN_Sudirman.pdf'              => 'DIAGRAM ARSITEKTUR TOPOLOGI ROUTER PE LAN GEDUNG BRI SUDIRMAN',
            'HLD_LLD_Desain_Jaringan_PE_LAN_Sudirman.pdf'          => 'HIGH LEVEL & LOW LEVEL DESIGN PE LAN ROUTER CISCO C8300',
            'Project_Charter_PE_LAN_Sudirman_BRI.pdf'              => 'PROJECT CHARTER PELAKSANAAN DUAL SCOPE IMPLEMENTASI & OPERASIONAL',
            'Kick_Off_Meeting_Deck_PE_LAN_BRI.pdf'                 => 'KICK OFF MEETING DECK - MAINTENANCE ROUTER EDGE LAN BRI',
            'Service_Level_Agreement_BRI_99.5.pdf'                 => 'SERVICE LEVEL AGREEMENT (SLA) 99.50% & OPERATIONAL MATRIX 24X7',
            'Matriks_Eskalasi_Support_24x7_IPNET.pdf'              => 'MATRIKS ESKALASI DUKUNGAN TEKNIS & NOC TIKET 24X7 IP-NET',
            'Kontrak_Perpanjangan_PE_LAN_Sudirman.pdf'             => 'KONTRAK PERJANJIAN PERPANJANGAN PEMELIHARAAN ROUTER PE LAN BRI',
        ];

        foreach ($sampleFiles as $fileName => $title) {
            $pdfContent = self::createMinimalPdf($title, 'Klien: PT Bank Rakyat Indonesia (Persero) Tbk - PT IP-Net Solusindo');
            foreach ($dirs as $dir) {
                $targetFile = $dir . '/' . $fileName;
                if (!file_exists($targetFile)) {
                    @file_put_contents($targetFile, $pdfContent);
                }
            }
        }

        // Dummy Excel
        $xlsxName = 'BoQ_BoM_Router_Edge_LAN_Sudirman.xlsx';
        $xlsxStub = "Bill of Quantity (BoQ) & Bill of Materials (BoM)\nProyek: Perpanjangan Pemeliharaan Router Edge LAN (PE LAN) Sudirman\nKlien: PT Bank Rakyat Indonesia (Persero) Tbk\n\nNo | Deskripsi | S/N | Qty | Satuan\n1. SFP-10G-SR Optical Transceiver Module | FOC2648SFP01 | 4 | Unit\n2. Patch Cord LC to LC Duplex OM4 5m | - | 8 | Pcs\n3. Cisco DNA Advantage Tier License 1-Yr | LIC-DNA-A | 2 | License";
        foreach ($dirs as $dir) {
            $target = $dir . '/' . $xlsxName;
            if (!file_exists($target)) {
                @file_put_contents($target, $xlsxStub);
            }
        }
    }

    /**
     * Hasilkan PDF minimal valid tanpa library eksternal
     */
    private static function createMinimalPdf(string $title, string $subtitle = ''): string
    {
        $safeTitle = preg_replace('/[^a-zA-Z0-9 \-_:.\/()]/', ' ', $title);
        $safeSub   = preg_replace('/[^a-zA-Z0-9 \-_:.\/()]/', ' ', $subtitle);

        $stream = "BT /F1 14 Tf 40 760 Td (" . addcslashes($safeTitle, "()\\") . ") Tj ET\n"
                . "BT /F2 10 Tf 40 735 Td (" . addcslashes($safeSub, "()\\") . ") Tj ET\n"
                . "BT /F2 9 Tf 40 700 Td (Dokumen ini merupakan arsip resmi proyek PT IP-Net Solusindo dengan PT Bank Rakyat Indonesia Tbk.) Tj ET\n"
                . "BT /F2 9 Tf 40 680 Td (Status: Terverifikasi dan Aktif di Sistem Operasional & Managed Service.) Tj ET\n";

        $streamLen = strlen($stream);

        $pdf = "%PDF-1.4\n"
             . "1 0 obj <</Type /Catalog /Pages 2 0 R>> endobj\n"
             . "2 0 obj <</Type /Pages /Kids [3 0 R] /Count 1>> endobj\n"
             . "3 0 obj <</Type /Page /Parent 2 0 R /MediaBox [0 0 595 842] /Contents 4 0 R /Resources <</Font <</F1 5 0 R /F2 6 0 R>>>>>> endobj\n"
             . "4 0 obj <</Length {$streamLen}>> stream\n{$stream}\nendstream\nendobj\n"
             . "5 0 obj <</Type /Font /Subtype /Type1 /BaseFont /Helvetica-Bold>> endobj\n"
             . "6 0 obj <</Type /Font /Subtype /Type1 /BaseFont /Helvetica>> endobj\n"
             . "xref\n0 7\n"
             . "0000000000 65535 f \n"
             . "0000000009 00000 n \n"
             . "0000000058 00000 n \n"
             . "0000000115 00000 n \n"
             . "0000000244 00000 n \n"
             . "0000000330 00000 n \n"
             . "0000000412 00000 n \n"
             . "trailer <</Size 7 /Root 1 0 R>>\n"
             . "startxref\n490\n%%EOF\n";

        return $pdf;
    }
}

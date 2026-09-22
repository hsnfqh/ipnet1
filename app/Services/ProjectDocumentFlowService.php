<?php

namespace App\Services;

use App\Models\Project;
use App\Models\ProjectDocument;

class ProjectDocumentFlowService
{
    /**
     * Definisi Master 6 Tahapan Handover & Document Flow (Commercial to Operation)
     */
    public static function getStagesDefinition(): array
    {
        return [
            1 => [
                'stage_number'     => 1,
                'stage_name'       => 'Commercial',
                'sub_title'        => 'Opportunity to Contract',
                'owner'            => 'Commercial Group (Sales, BD, CRO)',
                'handover_package' => 'Commercial Handover Package',
                'gate_action'      => 'Review & Approve',
                'description'      => 'Inisiasi peluang dari klien, kualifikasi teknis/anggaran, proposal komersial, negosiasi, hingga penerbitan PO/SPK resmi.',
                'documents' => [
                    [
                        'key'         => 'customer_requirement',
                        'title'       => 'Customer Requirement',
                        'description' => 'Dokumen kebutuhan awal, TOR/KAK dari klien, atau ringkasan kebutuhan bisnis.',
                        'mandatory'   => true,
                    ],
                    [
                        'key'         => 'rfi_rfp_rfq',
                        'title'       => 'RFI / RFP / RFQ',
                        'description' => 'Dokumen permintaan informasi/proposal tender dari klien.',
                        'mandatory'   => false,
                    ],
                    [
                        'key'         => 'commercial_proposal',
                        'title'       => 'Commercial Proposal',
                        'description' => 'Penawaran harga resmi (Quotation) dan terms komersial final yang diajukan ke klien.',
                        'mandatory'   => true,
                    ],
                    [
                        'key'         => 'negotiation_record',
                        'title'       => 'Negotiation Record & MoM',
                        'description' => 'Berita acara negosiasi harga/skop, risalah rapat (MoM), atau addendum kesepakatan.',
                        'mandatory'   => false,
                    ],
                    [
                        'key'         => 'contract_po_so',
                        'title'       => 'Contract / PO / SO (Signed)',
                        'description' => 'Dokumen Kontrak Perjanjian Kerjasama, Purchase Order (PO), atau Surat Pesanan Kerja (SPK) bertandatangan.',
                        'mandatory'   => true,
                    ],
                    [
                        'key'         => 'commercial_handover_package',
                        'title'       => 'Commercial Handover Package',
                        'description' => 'Formulir paket serah terima komersial lengkap dari Sales ke Tim Delivery & Solution.',
                        'mandatory'   => true,
                    ],
                ]
            ],
            2 => [
                'stage_number'     => 2,
                'stage_name'       => 'Solution',
                'sub_title'        => 'Design to Proposal',
                'owner'            => 'Solution Group (Presales, Solution Architect, Tech Development)',
                'handover_package' => 'Solution / Proposal Handover Package',
                'gate_action'      => 'Review & Approve',
                'description'      => 'Perancangan arsitektur teknis, High Level & Low Level Design, Bill of Quantity (BoQ), validasi solusi dan uji kelayakan teknis.',
                'documents' => [
                    [
                        'key'         => 'solution_concept',
                        'title'       => 'Solution Concept',
                        'description' => 'Konsep perancangan sistem dan pendekatan arsitektur solusi.',
                        'mandatory'   => true,
                    ],
                    [
                        'key'         => 'solution_architecture',
                        'title'       => 'Solution Architecture',
                        'description' => 'Diagram arsitektur jaringan, sistem server, cloud, atau keamanan terintegrasi.',
                        'mandatory'   => true,
                    ],
                    [
                        'key'         => 'hld_lld',
                        'title'       => 'HLD / LLD (Design Document)',
                        'description' => 'High Level Design (HLD) dan Low Level Design (LLD) termasuk IP addressing & skema port.',
                        'mandatory'   => true,
                    ],
                    [
                        'key'         => 'technical_proposal',
                        'title'       => 'Technical Proposal',
                        'description' => 'Dokumen proposal teknis, compliance matrix, dan metodologi kerja.',
                        'mandatory'   => true,
                    ],
                    [
                        'key'         => 'boq_bom',
                        'title'       => 'BoQ / BoM (Bill of Quantity/Material)',
                        'description' => 'Rincian daftar perangkat hardware, software license, dan jasa implementasi.',
                        'mandatory'   => true,
                    ],
                    [
                        'key'         => 'solution_validation_report',
                        'title'       => 'Solution Validation Report',
                        'description' => 'Laporan uji PoC (Proof of Concept), demo produk, atau validasi prinsipal/distributor.',
                        'mandatory'   => false,
                    ],
                    [
                        'key'         => 'solution_handover_package',
                        'title'       => 'Solution Handover Package',
                        'description' => 'Paket desain final yang diserahterimakan dari Solution Architect ke PMO.',
                        'mandatory'   => true,
                    ],
                ]
            ],
            3 => [
                'stage_number'     => 3,
                'stage_name'       => 'Project Management',
                'sub_title'        => 'Initiate to Execute',
                'owner'            => 'PMO (Project Manager)',
                'handover_package' => 'Project Handover Package',
                'gate_action'      => 'Review & Approve',
                'description'      => 'Inisiasi proyek formal, penyusunan Project Charter, WBS schedule, alokasi personil engineer, dan penetapan baseline proyek.',
                'documents' => [
                    [
                        'key'         => 'project_handover_package',
                        'title'       => 'Project Handover Package',
                        'description' => 'Pengesahan penerimaan proyek dari Sales/Presales ke PMO.',
                        'mandatory'   => true,
                    ],
                    [
                        'key'         => 'project_charter',
                        'title'       => 'Project Charter',
                        'description' => 'Dokumen mandat inisiasi proyek resmi yang memuat otorisasi dan tujuan proyek.',
                        'mandatory'   => true,
                    ],
                    [
                        'key'         => 'project_plan',
                        'title'       => 'Project Plan (WBS & Schedule)',
                        'description' => 'Rencana kerja rinci, Work Breakdown Structure (WBS), dan jadwal milestone pelaksanaan.',
                        'mandatory'   => true,
                    ],
                    [
                        'key'         => 'resource_plan',
                        'title'       => 'Resource Plan',
                        'description' => 'Matriks alokasi personil Engineer, PIC teknis, dan surat tugas lapangan.',
                        'mandatory'   => true,
                    ],
                    [
                        'key'         => 'work_order',
                        'title'       => 'Work Order (SPK Pelaksanaan)',
                        'description' => 'Surat Perintah Kerja pelaksanaan untuk tim engineering atau subkontraktor/vendor.',
                        'mandatory'   => true,
                    ],
                    [
                        'key'         => 'project_baseline',
                        'title'       => 'Project Baseline',
                        'description' => 'Dokumen baseline ruang lingkup, jadwal, dan anggaran proyek.',
                        'mandatory'   => false,
                    ],
                    [
                        'key'         => 'change_request',
                        'title'       => 'Change Request (jika ada)',
                        'description' => 'Formulir persetujuan perubahan lingkup kerja, jadwal, atau spesifikasi perangkat.',
                        'mandatory'   => false,
                    ],
                ]
            ],
            4 => [
                'stage_number'     => 4,
                'stage_name'       => 'Technical Implementation',
                'sub_title'        => 'Build to Test',
                'owner'            => 'Technical Engineering (Network, Security, Field Engineer, Vendor)',
                'handover_package' => 'Technical Completion Package',
                'gate_action'      => 'Verify & Approve',
                'description'      => 'Pelaksanaan instalasi fisik/kabel/rack, konfigurasi perangkat, migrasi data, pengujian integrasi, dan penyusunan As-Built Document.',
                'documents' => [
                    [
                        'key'         => 'implementation_work_order',
                        'title'       => 'Work Order Pelaksanaan Lapangan',
                        'description' => 'Work Order dan checklist izin kerja (Permit to Work) di lokasi klien.',
                        'mandatory'   => true,
                    ],
                    [
                        'key'         => 'installation_report',
                        'title'       => 'Installation Report (Laporan Instalasi)',
                        'description' => 'Laporan pemasangan fisik perangkat, dokumentasi foto rak/kabel, dan labeling.',
                        'mandatory'   => true,
                    ],
                    [
                        'key'         => 'configuration_record',
                        'title'       => 'Configuration Record & Backup Script',
                        'description' => 'Salinan konfigurasi final (running-config/backup) dan dokumentasi parameter sistem.',
                        'mandatory'   => true,
                    ],
                    [
                        'key'         => 'test_plan_result',
                        'title'       => 'Test Plan & ATP Test Result',
                        'description' => 'Hasil uji Acceptance Test Procedure (ATP), throughput, ping latency, dan redundancy failover.',
                        'mandatory'   => true,
                    ],
                    [
                        'key'         => 'technical_completion_report',
                        'title'       => 'Technical Completion Report',
                        'description' => 'Laporan kesiapan teknis sebelum diserahterimakan ke proses UAT resmi klien.',
                        'mandatory'   => true,
                    ],
                    [
                        'key'         => 'as_built_document',
                        'title'       => 'As-Built Document (ABD)',
                        'description' => 'Gambar topologi final, wiring schematic, dan rack layout terpasang riil di lapangan.',
                        'mandatory'   => true,
                    ],
                    [
                        'key'         => 'handover_to_service_pkg',
                        'title'       => 'Handover to Service Package',
                        'description' => 'Paket data hasil implementasi siap serah terima ke tim Acceptance/Operate.',
                        'mandatory'   => true,
                    ],
                ]
            ],
            5 => [
                'stage_number'     => 5,
                'stage_name'       => 'Acceptance',
                'sub_title'        => 'Verify to Handover',
                'owner'            => 'PMO & Klien (Project Manager)',
                'handover_package' => 'Acceptance & BAST Package',
                'gate_action'      => 'Sign Off',
                'description'      => 'Pelaksanaan User Acceptance Testing (UAT) bersama klien, perbaikan punch-list, penandatanganan BAST 1 & Final, dan serah terima garansi.',
                'documents' => [
                    [
                        'key'         => 'test_result_final',
                        'title'       => 'Test Result Final',
                        'description' => 'Hasil uji pengujian akhir yang disaksikan dan disetujui bersama PIC Klien.',
                        'mandatory'   => true,
                    ],
                    [
                        'key'         => 'uat_sign_off',
                        'title'       => 'UAT Sign-Off Form',
                        'description' => 'Formulir berita acara User Acceptance Testing (UAT) bertandatangan resmi.',
                        'mandatory'   => true,
                    ],
                    [
                        'key'         => 'acceptance_record',
                        'title'       => 'Acceptance Record / Punch-List',
                        'description' => 'Catatan serah terima dan bukti penyelesaian punch-list minor (jika ada).',
                        'mandatory'   => false,
                    ],
                    [
                        'key'         => 'bast_document',
                        'title'       => 'BAST (Berita Acara Serah Terima 1 & Final)',
                        'description' => 'Dokumen resmi BAST bertandatangan basah/elektronik dari manajemen klien.',
                        'mandatory'   => true,
                    ],
                    [
                        'key'         => 'final_documentation',
                        'title'       => 'Final Documentation Package',
                        'description' => 'Dokumentasi lengkap proyek, user manual, dan sertifikat garansi perangkat.',
                        'mandatory'   => true,
                    ],
                    [
                        'key'         => 'service_handover_pkg',
                        'title'       => 'Service Handover Package (Gate 4)',
                        'description' => 'Paket transisi dari tahap Deliver ke tahap Operate (Managed Service / Support).',
                        'mandatory'   => true,
                    ],
                    [
                        'key'         => 'handover_checklist',
                        'title'       => 'Handover Checklist Sign-Off',
                        'description' => 'Checklist kelengkapan serah terima operasional yang ditandatangani PMO.',
                        'mandatory'   => true,
                    ],
                ]
            ],
            6 => [
                'stage_number'     => 6,
                'stage_name'       => 'Operate',
                'sub_title'        => 'Service and Support',
                'owner'            => 'Service Delivery (Managed Service, NOC, Help Desk)',
                'handover_package' => 'Service Handover Package',
                'gate_action'      => 'Accept & Operate',
                'description'      => 'Operasional harian, monitoring NOC 24/7, pengelolaan tiket insiden, Preventive Maintenance rutin, SLA review, dan Continuous Improvement.',
                'documents' => [
                    [
                        'key'         => 'service_handover_pkg_ms',
                        'title'       => 'Service Handover Package (Aktivasi)',
                        'description' => 'Dokumen onboarding dan pengesahan aktivasi kontrak layanan oleh tim Managed Service.',
                        'mandatory'   => true,
                    ],
                    [
                        'key'         => 'service_level_agreement',
                        'title'       => 'Service Level Agreement (SLA Terms)',
                        'description' => 'Ketentuan SLA, MTTR baseline, cakupan jam kerja, dan KPI layanan yang disepakati.',
                        'mandatory'   => true,
                    ],
                    [
                        'key'         => 'asset_information',
                        'title'       => 'Asset Information (CI / CMDB Data)',
                        'description' => 'Daftar Configuration Item (CI), serial number perangkat, lokasi rak, dan tanggal kadaluarsa garansi/lisensi.',
                        'mandatory'   => true,
                    ],
                    [
                        'key'         => 'configuration_data',
                        'title'       => 'Operational Configuration & Credential Data',
                        'description' => 'Data kredensial akses pemeliharaan, IP schema operasional, dan parameter monitoring.',
                        'mandatory'   => true,
                    ],
                    [
                        'key'         => 'support_contact_list',
                        'title'       => 'Support Escalation & Contact List',
                        'description' => 'Matriks eskalasi penanganan insiden, nomor darurat NOC, dan kontak PIC prinsipal.',
                        'mandatory'   => true,
                    ],
                    [
                        'key'         => 'operational_report',
                        'title'       => 'Operational Report (Periodic Maintenance)',
                        'description' => 'Laporan bulanan/triwulanan hasil Preventive Maintenance dan rekapitulasi tiket insiden.',
                        'mandatory'   => false,
                    ],
                    [
                        'key'         => 'continuous_improvement_plan',
                        'title'       => 'Continuous Improvement Plan',
                        'description' => 'Rekomendasi peningkatan performa, capacity planning, dan peluang renewal/upsell kontrak.',
                        'mandatory'   => false,
                    ],
                ]
            ],
        ];
    }

    /**
     * Inisialisasi slot dokumen lengkap 6 tahap untuk sebuah proyek
     */
    public static function ensureProjectDocumentsInitialized(Project $project): void
    {
        $stages = self::getStagesDefinition();
        $existingDocs = $project->projectDocuments()->pluck('document_key')->toArray();

        $toInsert = [];
        $now = now();

        foreach ($stages as $stageNumber => $stage) {
            foreach ($stage['documents'] as $doc) {
                if (!in_array($doc['key'], $existingDocs)) {
                    $toInsert[] = [
                        'project_id'     => $project->id,
                        'stage_number'   => $stageNumber,
                        'stage_name'     => $stage['stage_name'],
                        'document_key'   => $doc['key'],
                        'document_title' => $doc['title'],
                        'is_mandatory'   => $doc['mandatory'],
                        'status'         => 'Pending',
                        'notes'          => $doc['description'],
                        'created_at'     => $now,
                        'updated_at'     => $now,
                    ];
                }
            }
        }

        if (!empty($toInsert)) {
            ProjectDocument::insert($toInsert);
        }
    }

    /**
     * Hitung ringkasan progres kelengkapan dokumen per tahap untuk proyek
     */
    public static function getProjectDocumentProgress(Project $project): array
    {
        self::ensureProjectDocumentsInitialized($project);

        $documents = $project->projectDocuments()->get();
        $stages = self::getStagesDefinition();

        $progress = [];

        foreach ($stages as $stageNumber => $stage) {
            $stageDocs = $documents->where('stage_number', $stageNumber);
            $totalCount = $stageDocs->count();
            $uploadedCount = $stageDocs->whereIn('status', ['Uploaded', 'Verified'])->count();
            $verifiedCount = $stageDocs->where('status', 'Verified')->count();
            $percentage = $totalCount > 0 ? round(($uploadedCount / $totalCount) * 100) : 0;

            $progress[$stageNumber] = [
                'stage_number'     => $stageNumber,
                'stage_name'       => $stage['stage_name'],
                'sub_title'        => $stage['sub_title'],
                'owner'            => $stage['owner'],
                'handover_package' => $stage['handover_package'],
                'gate_action'      => $stage['gate_action'],
                'description'      => $stage['description'],
                'total_docs'       => $totalCount,
                'uploaded_docs'    => $uploadedCount,
                'verified_docs'    => $verifiedCount,
                'percentage'       => $percentage,
                'is_complete'      => $uploadedCount >= $stageDocs->where('is_mandatory', true)->count() && $uploadedCount > 0,
                'documents'        => $stageDocs->values(),
            ];
        }

        return $progress;
    }
}

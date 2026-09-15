<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CroEngagement;
use App\Models\CroCsatSurvey;
use App\Models\CroConcern;
use App\Models\CroAccountHealth;
use App\Models\CroOpportunity;
use App\Models\Client;
use App\Models\Project;
use App\Models\User;
use Carbon\Carbon;

class CroModuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Dapatkan Users yang relevan
        $admin = User::first();
        $salesUser = User::whereHas('roles', function($q) {
            $q->whereIn('name', ['Sales', 'Account Manager', 'BDM']);
        })->first() ?? $admin;

        $techUser = User::whereHas('roles', function($q) {
            $q->whereIn('name', ['Lead Maintenance', 'Maintenance', 'Lead Engineer', 'Engineer']);
        })->first() ?? $admin;

        // 2. Data Akun & Retensi (Account Health)
        $healthData = [
            [
                'client_name'            => 'PT Bank Mandiri (Persero) Tbk',
                'health_status'          => 'Healthy',
                'health_score'           => 96,
                'contract_end_date'      => Carbon::now()->addMonths(10)->toDateString(),
                'estimated_annual_value' => 2400000000.00, // 2.4 M
                'renewal_probability'    => 95,
                'risk_factors'           => 'Tidak ada risiko signifikan. Hubungan sangat kuat dengan C-Level.',
                'retention_strategy'     => 'Quarterly executive dinner & usulkan ekspansi SD-WAN Phase 2.',
                'last_review_date'       => Carbon::now()->subDays(12)->toDateString(),
                'next_touchpoint_date'   => Carbon::now()->addDays(20)->toDateString(),
            ],
            [
                'client_name'            => 'PT Telekomunikasi Selular (Telkomsel)',
                'health_status'          => 'Warning',
                'health_score'           => 72,
                'contract_end_date'      => Carbon::now()->addDays(45)->toDateString(),
                'estimated_annual_value' => 1850000000.00, // 1.85 M
                'renewal_probability'    => 65,
                'risk_factors'           => 'Terdapat 2 insiden latency peak di DC Surabaya pada bulan lalu yang sempat dikomplain VP Network.',
                'retention_strategy'     => 'Fasilitasi pertemuan teknis CRO + Lead Maintenance, tawarkan complimentary SLA upgrade report.',
                'last_review_date'       => Carbon::now()->subDays(5)->toDateString(),
                'next_touchpoint_date'   => Carbon::now()->addDays(3)->toDateString(),
            ],
            [
                'client_name'            => 'PT Kalbe Farma Tbk',
                'health_status'          => 'At-Risk',
                'health_score'           => 45,
                'contract_end_date'      => Carbon::now()->addDays(28)->toDateString(),
                'estimated_annual_value' => 950000000.00, // 950 Juta
                'renewal_probability'    => 35,
                'risk_factors'           => 'Komplain berulang terkait respon penggantian sparepart router cabang Cikarang, kompetitor mulai pitching proposal.',
                'retention_strategy'     => 'Segera jadwalkan Courtesy Meeting Onsite bersama Kadiv Delivery & berikan jaminan dedicated standby unit.',
                'last_review_date'       => Carbon::now()->subDays(2)->toDateString(),
                'next_touchpoint_date'   => Carbon::now()->addDays(1)->toDateString(),
            ],
            [
                'client_name'            => 'PT Astra International Tbk',
                'health_status'          => 'Healthy',
                'health_score'           => 92,
                'contract_end_date'      => Carbon::now()->addMonths(8)->toDateString(),
                'estimated_annual_value' => 3100000000.00,
                'renewal_probability'    => 90,
                'risk_factors'           => 'Relasi operasional berjalan sangat mulus.',
                'retention_strategy'     => 'Siapkan proposal Managed Security SOC 24/7.',
                'last_review_date'       => Carbon::now()->subDays(20)->toDateString(),
                'next_touchpoint_date'   => Carbon::now()->addDays(15)->toDateString(),
            ],
            [
                'client_name'            => 'RS Siloam Hospitals Group',
                'health_status'          => 'Healthy',
                'health_score'           => 88,
                'contract_end_date'      => Carbon::now()->addMonths(5)->toDateString(),
                'estimated_annual_value' => 1200000000.00,
                'renewal_probability'    => 85,
                'risk_factors'           => 'Kebutuhan integrasi SIMRS antar cabang perlu pengawasan ekstra.',
                'retention_strategy'     => 'Monthly Service Review rutin & pendampingan teknis.',
                'last_review_date'       => Carbon::now()->subDays(15)->toDateString(),
                'next_touchpoint_date'   => Carbon::now()->addDays(10)->toDateString(),
            ],
        ];

        foreach ($healthData as $h) {
            $client = Client::where('name', 'like', "%{$h['client_name']}%")->first();
            CroAccountHealth::updateOrCreate(
                ['client_name' => $h['client_name']],
                array_merge($h, [
                    'client_id'          => $client?->id,
                    'account_manager_id' => $salesUser?->id,
                    'updated_by'         => $admin?->id,
                ])
            );
        }

        // 3. Data CSAT Surveys
        $csatData = [
            [
                'client_name'            => 'PT Bank Mandiri (Persero) Tbk',
                'service_category'       => 'Managed Service',
                'respondent_name'        => 'Ir. Hendra Gunawan',
                'respondent_role'        => 'VP IT Infrastructure',
                'csat_score'             => 5,
                'nps_score'              => 10,
                'sla_satisfaction_score' => 5,
                'support_speed_score'    => 5,
                'feedback_notes'         => 'Tim Managed Service IP-NET sangat proaktif dalam pencegahan insiden sebelum berdampak ke nasabah.',
                'areas_of_improvement'   => 'Mohon pertahankan performa pelaporan mingguan yang sudah sangat rapi.',
                'sentiment'              => 'Positive',
                'survey_date'            => Carbon::now()->subDays(10)->toDateString(),
            ],
            [
                'client_name'            => 'PT Telekomunikasi Selular (Telkomsel)',
                'service_category'       => 'Technical Support',
                'respondent_name'        => 'Bagus Pratama, ST',
                'respondent_role'        => 'Network Operations Lead',
                'csat_score'             => 3,
                'nps_score'              => 6,
                'sla_satisfaction_score' => 3,
                'support_speed_score'    => 3,
                'feedback_notes'         => 'Respon tiket cukup cepat, namun investigasi Root Cause Analysis (RCA) untuk insiden Surabaya butuh 3 hari, agak lambat.',
                'areas_of_improvement'   => 'Percepat penyampaian formal RCA maksimal 24 jam setelah restore.',
                'sentiment'              => 'Neutral',
                'survey_date'            => Carbon::now()->subDays(15)->toDateString(),
            ],
            [
                'client_name'            => 'PT Kalbe Farma Tbk',
                'service_category'       => 'Managed Service',
                'respondent_name'        => 'Dr. Anton Wijaya',
                'respondent_role'        => 'Head of Corporate IT',
                'csat_score'             => 2,
                'nps_score'              => 4,
                'sla_satisfaction_score' => 2,
                'support_speed_score'    => 2,
                'feedback_notes'         => 'Sangat kecewa dengan keterlambatan sparepart pengganti pekan lalu. Cabang Cikarang sempat offline 4 jam.',
                'areas_of_improvement'   => 'Harus ada buffer stock lokal di regional Cikarang/Bekasi.',
                'sentiment'              => 'Negative',
                'survey_date'            => Carbon::now()->subDays(4)->toDateString(),
            ],
            [
                'client_name'            => 'PT Astra International Tbk',
                'service_category'       => 'Project Delivery',
                'respondent_name'        => 'Rian Firmansyah',
                'respondent_role'        => 'Senior Project Manager',
                'csat_score'             => 5,
                'nps_score'              => 9,
                'sla_satisfaction_score' => 5,
                'support_speed_score'    => 5,
                'feedback_notes'         => 'Delivery project migrasi datacenter berjalan lebih cepat 2 minggu dari jadwal semula.',
                'areas_of_improvement'   => 'Dapat ditambahkan knowledge transfer session untuk tim kami.',
                'sentiment'              => 'Positive',
                'survey_date'            => Carbon::now()->subDays(25)->toDateString(),
            ],
            [
                'client_name'            => 'RS Siloam Hospitals Group',
                'service_category'       => 'Managed Service',
                'respondent_name'        => 'Maria Kusuma',
                'respondent_role'        => 'Manager SIMRS & Jaringan',
                'csat_score'             => 4,
                'nps_score'              => 8,
                'sla_satisfaction_score' => 4,
                'support_speed_score'    => 5,
                'feedback_notes'         => 'Komunikasi via WhatsApp grup responsif dan tim teknis selalu siap sedia saat jadwal cutover malam.',
                'areas_of_improvement'   => 'Dashboard report bulanan agar bisa diekspor format excel detail.',
                'sentiment'              => 'Positive',
                'survey_date'            => Carbon::now()->subDays(18)->toDateString(),
            ],
        ];

        foreach ($csatData as $c) {
            $client = Client::where('name', 'like', "%{$c['client_name']}%")->first();
            CroCsatSurvey::create(array_merge($c, [
                'client_id'  => $client?->id,
                'created_by' => $admin?->id,
            ]));
        }

        // 4. Data Concerns & Orchestration Tickets
        $concernsData = [
            [
                'ticket_number'                => 'CR-2026-0001',
                'client_name'                  => 'PT Kalbe Farma Tbk',
                'title'                        => 'Keterlambatan Penggantian Sparepart Router Cabang Cikarang',
                'description'                  => 'Klien melaporkan downtime selama 4 jam dikarenakan teknisi harus mengambil sparepart dari DC Jakarta.',
                'source'                       => 'Client Direct',
                'severity'                     => 'P1 - Critical',
                'assigned_dept'                => 'Managed Service',
                'status'                       => 'In Progress',
                'sla_due_date'                 => Carbon::now()->addDays(1)->toDateString(),
                'root_cause'                   => 'Buffer stock di depo terdekat sedang kosong karena pergantian vendor logistik.',
                'action_taken'                 => 'Menempatkan 1 unit cold standby router Cisco di site Cikarang dan membuat SLA buffer stock lokal.',
                'customer_satisfaction_rating' => null,
            ],
            [
                'ticket_number'                => 'CR-2026-0002',
                'client_name'                  => 'PT Telekomunikasi Selular (Telkomsel)',
                'title'                        => 'Eskalasi Permintaan RCA Latency Spike Surabaya',
                'description'                  => 'VP Network Telkomsel meminta penjelasan resmi atas fluktuasi latency link metro pada tanggal 2 September.',
                'source'                       => 'Incident Escalation',
                'severity'                     => 'P2 - High',
                'assigned_dept'                => 'Technical / Network',
                'status'                       => 'Pending Confirmation',
                'sla_due_date'                 => Carbon::now()->subDays(1)->toDateString(),
                'root_cause'                   => 'Pekerjaan galian utilitas pihak ketiga memicu degradasi sinyal fiber optik di ruas Waru-Surabaya.',
                'action_taken'                 => 'RCA teknis lengkap dengan log optical time-domain reflectometer (OTDR) telah dikirimkan via email resmi dan dipresentasikan.',
                'resolved_at'                  => Carbon::now()->subHours(8),
                'customer_satisfaction_rating' => null,
            ],
            [
                'ticket_number'                => 'CR-2026-0003',
                'client_name'                  => 'PT Bank Mandiri (Persero) Tbk',
                'title'                        => 'Permintaan Tambahan Bandwidth Temporer untuk Audit BI',
                'description'                  => 'Klien memerlukan peningkatan kapasitas bandwidth link DRC selama 2 pekan masa audit Bank Indonesia.',
                'source'                       => 'QBR Meeting',
                'severity'                     => 'P3 - Medium',
                'assigned_dept'                => 'Sales / BDM',
                'status'                       => 'Closed',
                'sla_due_date'                 => Carbon::now()->subDays(5)->toDateString(),
                'root_cause'                   => 'Lonjakan volume transfer database DRC selama simulasi audit.',
                'action_taken'                 => 'Aktivasi bandwidth on-demand 1 Gbps secara instan tanpa downtime.',
                'resolved_at'                  => Carbon::now()->subDays(4),
                'customer_confirmed_at'        => Carbon::now()->subDays(3),
                'customer_confirmation_notes'  => 'Klien sangat puas dengan kecepatan tim IP-NET menangani lonjakan audit tanpa kendala.',
                'customer_satisfaction_rating' => 5,
            ],
            [
                'ticket_number'                => 'CR-2026-0004',
                'client_name'                  => 'RS Siloam Hospitals Group',
                'title'                        => 'Integrasi Notifikasi Tiket Gangguan ke Bot Telegram IT RS',
                'description'                  => 'Kepala IT RS meminta webhook integrasi dari sistem ticketing IP-NET ke channel internal mereka.',
                'source'                       => 'Survey Feedback',
                'severity'                     => 'P3 - Medium',
                'assigned_dept'                => 'PMO',
                'status'                       => 'Dispatched',
                'sla_due_date'                 => Carbon::now()->addDays(4)->toDateString(),
                'root_cause'                   => 'Kebutuhan sentralisasi alert pada sistem rumah sakit.',
                'action_taken'                 => 'Tim PMO & R&D sedang mengonfigurasi token API webhook.',
                'customer_satisfaction_rating' => null,
            ],
        ];

        foreach ($concernsData as $con) {
            $client = Client::where('name', 'like', "%{$con['client_name']}%")->first();
            CroConcern::updateOrCreate(
                ['ticket_number' => $con['ticket_number']],
                array_merge($con, [
                    'client_id'   => $client?->id,
                    'assigned_to' => $techUser?->id,
                    'created_by'  => $admin?->id,
                ])
            );
        }

        // 5. Data Engagements & Meeting Logs
        $engagementsData = [
            [
                'client_name'        => 'PT Bank Mandiri (Persero) Tbk',
                'pic_name'           => 'Ir. Hendra Gunawan',
                'pic_contact'        => 'hendra.gunawan@bankmandiri.co.id / 0811-9876-543',
                'engagement_type'    => 'QBR Review',
                'title'              => 'Executive Q3 Business Review & SLA Evaluation',
                'discussion_summary' => 'Membahas pencapaian SLA Q3 yang mencapai 99.98%, tidak ada denda SLA, dan kepuasan tim IT Bank Mandiri.',
                'action_items'       => 'Siapkan proposal perpanjangan kontrak (renewal) dengan skema 3 tahun berturut-turut.',
                'sentiment'          => 'Positive',
                'engagement_date'    => Carbon::now()->subDays(12)->toDateString(),
                'location'           => 'Plaza Mandiri Lt. 18, Gatot Subroto Jakarta',
            ],
            [
                'client_name'        => 'PT Telekomunikasi Selular (Telkomsel)',
                'pic_name'           => 'Bagus Pratama, ST',
                'pic_contact'        => 'bagus_pratama@telkomsel.co.id / 0812-3456-789',
                'engagement_type'    => 'Meeting',
                'title'              => 'Technical Clarification & RCA Handover Meeting',
                'discussion_summary' => 'Pertemuan mediasi bersama tim Network Telkomsel terkait stabilisasi fiber optik jalur Jawa Timur.',
                'action_items'       => 'Lakukan inspeksi gabungan join-survey titik rawan galian pada tanggal 20 September.',
                'sentiment'          => 'Concerned',
                'engagement_date'    => Carbon::now()->subDays(5)->toDateString(),
                'location'           => 'Telkomsel Smart Office Lt. 12, Jakarta',
            ],
            [
                'client_name'        => 'PT Astra International Tbk',
                'pic_name'           => 'Rian Firmansyah',
                'pic_contact'        => 'rian.firmansyah@astra.co.id',
                'engagement_type'    => 'Lunch / Coffee',
                'title'              => 'Courtesy Catchup & Strategic Plan 2027',
                'discussion_summary' => 'Mendiskusikan rencana ekspansi pabrik baru Astra di Karawang dan kebutuhan integrasi SD-WAN 15 site cabang.',
                'action_items'       => 'Kirimkan proposal preliminary SD-WAN dan jadwalkan sesi demo produk minggu depan.',
                'sentiment'          => 'Positive',
                'engagement_date'    => Carbon::now()->subDays(8)->toDateString(),
                'location'           => 'Menara Astra, Sudirman',
            ],
        ];

        foreach ($engagementsData as $eng) {
            $client = Client::where('name', 'like', "%{$eng['client_name']}%")->first();
            CroEngagement::create(array_merge($eng, [
                'client_id'  => $client?->id,
                'created_by' => $admin?->id,
            ]));
        }

        // 6. Data Opportunities (Bridge to Sales)
        $oppsData = [
            [
                'client_name'       => 'PT Astra International Tbk',
                'opportunity_type'  => 'Cross-Sell New Site',
                'title'             => 'Implementasi SD-WAN Multi-Branch 15 Site Karawang & Cibitung',
                'estimated_value'   => 850000000.00, // 850 Juta
                'requirement_notes' => 'Klien membutuhkan konektivitas redundant 4G/Fiber optik dengan central orchestration dashboard.',
                'status'            => 'Handed Over',
                'handed_over_at'    => Carbon::now()->subDays(3),
            ],
            [
                'client_name'       => 'PT Bank Mandiri (Persero) Tbk',
                'opportunity_type'  => 'Renewal',
                'title'             => 'Multi-Year Contract Extension Managed Network 2027-2029',
                'estimated_value'   => 4800000000.00, // 4.8 M
                'requirement_notes' => 'Perpanjangan kontrak 3 tahun dengan penambahan klausul support 24/7 on-premise engineer.',
                'status'            => 'In Sales Pipeline',
                'handed_over_at'    => Carbon::now()->subDays(10),
            ],
            [
                'client_name'       => 'RS Siloam Hospitals Group',
                'opportunity_type'  => 'Hardware / License Addon',
                'title'             => 'Upgrade Firewall Next-Gen FortiGate & License IPS 5 Rumah Sakit',
                'estimated_value'   => 420000000.00, // 420 Juta
                'requirement_notes' => 'Peremajaan perangkat gateway firewall yang sudah mendekati End-of-Support (EOS).',
                'status'            => 'Identified',
                'handed_over_at'    => null,
            ],
        ];

        foreach ($oppsData as $op) {
            $client = Client::where('name', 'like', "%{$op['client_name']}%")->first();
            CroOpportunity::create(array_merge($op, [
                'client_id'      => $client?->id,
                'handed_over_to' => $salesUser?->id,
                'created_by'     => $admin?->id,
            ]));
        }
    }
}

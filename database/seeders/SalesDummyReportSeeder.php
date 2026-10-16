<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Project;
use App\Models\SalesActivity;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;

class SalesDummyReportSeeder extends Seeder
{
    public function run(?User $targetUser = null): void
    {
        // 1. Dapatkan Sales User (Prioritas targetUser, lalu Raiza / Nabylla / Sales / User pertama)
        $salesUser = $targetUser
            ?? User::where('name', 'like', '%Raiza%')->first()
            ?? User::where('name', 'like', '%Nabylla%')->first()
            ?? User::whereHas('roles', function($q) {
                $q->whereIn('name', ['Sales', 'Account Manager', 'CRO']);
            })->first()
            ?? User::first();

        $salesName = $salesUser ? $salesUser->name : 'Raiza';
        $salesId = $salesUser ? $salesUser->id : 1;

        // 2. PROJECT DUMMY 1: Untuk Form Laporan Aktivitas Sales (Kategori 1)
        $project1 = Project::updateOrCreate(
            ['name' => 'Pengadaan Core Switch & Infrastruktur Jaringan PT Hasan Solusi Global'],
            [
                'client'                 => 'PT Hasan Solusi Global',
                'contract_value'         => 350000000,
                'sales_stage'            => 'Quotation',
                'win_probability'        => 75,
                'sales_name'             => $salesName,
                'created_by'             => $salesId,
                'status'                 => 'On Progress',
                'stage'                  => 'Commercial',
                'progress'               => 45,
                'acquire_status'         => 'Prospecting',
                'bdm_handover_status'    => 'Self-Sourced Sales',
                'start_date'             => Carbon::now()->subDays(5),
                'deadline'               => Carbon::now()->addDays(30),
                'expected_closing_date'  => Carbon::now()->addDays(20),
                'opportunity_source'     => 'Direct Sales Prospecting',
                'quotation_number'       => 'QUO-IPNET/2026/10/088',
                'quotation_amount'       => 350000000,
                'billing_terms'          => 'DP 30% setelah PO, 50% delivery hardware, 20% setelah UAT & BAST selesai.',
                'commercial_terms'       => 'Franco Jakarta, Pembayaran TOP 30 Hari setelah invoice resmi.',
                'sla_commitment'         => 'Garansi Resmi Prinsipal 1 Tahun, Support SLA 99.8% Response Time 4 Jam.',
            ]
        );

        // Payload 5 Rincian Kronologis Lengkap
        $items = [
            [
                'no'          => 1,
                'subject'     => 'Kick-off koordinasi teknis kebutuhan bandwidth & arsitektur core switch',
                'date'        => Carbon::now()->subDays(2)->toDateString(),
                'time'        => '09:00',
                'client_pic'  => 'Hasan Faqih',
                'ipnet_pic'   => 'Syaiful',
                'notes'       => 'Pemaparan arsitektur high availability dan redundancy dual-uplink 10Gbps. Klien menyepakati skema topologi jaringan.',
                'next_action' => 'Survey ruang data center'
            ],
            [
                'no'          => 2,
                'subject'     => 'Walkthrough survey denah data center & kesiapan rack server',
                'date'        => Carbon::now()->subDays(2)->toDateString(),
                'time'        => '11:00',
                'client_pic'  => 'Hasan Faqih',
                'ipnet_pic'   => 'Syaiful',
                'notes'       => 'Pengecekan ketersediaan slot 4U rack, alokasi daya PDU dual source, dan suhu ruangan server data center.',
                'next_action' => 'Kompilasi BoQ spesifikasi hardware'
            ],
            [
                'no'          => 3,
                'subject'     => 'Pembahasan BoQ hardware, modul SFP+ & kabel optical fiber',
                'date'        => Carbon::now()->subDays(1)->toDateString(),
                'time'        => '13:30',
                'client_pic'  => 'Hasan Faqih',
                'ipnet_pic'   => 'Syaiful',
                'notes'       => 'Verifikasi rincian kebutuhan 24 port SFP28 dan 48 port PoE+. Klien meminta rekomendasi switch enterprise tier-1.',
                'next_action' => 'Review draft SLA bersama tim support'
            ],
            [
                'no'          => 4,
                'subject'     => 'Peninjauan Service Level Agreement (SLA) & skema support teknis',
                'date'        => Carbon::now()->subDays(1)->toDateString(),
                'time'        => '15:00',
                'client_pic'  => 'Hasan Faqih',
                'ipnet_pic'   => 'Syaiful',
                'notes'       => 'Disepakati SLA uptime 99.8% dengan waktu respons onsite maksimal 4 jam untuk insiden critical severity 24x7.',
                'next_action' => 'Finalisasi dokumen penawaran harga'
            ],
            [
                'no'          => 5,
                'subject'     => 'Finalisasi timeline pengiriman perangkat & persiapan penawaran resmi',
                'date'        => Carbon::now()->toDateString(),
                'time'        => '16:30',
                'client_pic'  => 'Hasan Faqih',
                'ipnet_pic'   => 'Syaiful',
                'notes'       => 'Tim sales IPNET berkomitmen menerbitkan Surat Penawaran Harga (SPH) resmi dan garansi prinsipal maksimal 3 hari kerja.',
                'next_action' => 'Kirim SPH resmi dan BoQ ke Hasan Faqih'
            ]
        ];

        $payloadActivity = [
            'type'               => 'kronologi_batch',
            'title'              => 'Rapat Koordinasi & Pembahasan Teknis Infrastruktur Jaringan Enterprise',
            'meeting_media'      => 'Onsite di Lokasi Klien',
            'duration_days'      => 3,
            'meeting_time_start' => '09:00',
            'meeting_time_end'   => '17:00',
            'attendees'          => [
                [
                    'pihak'      => 'Internal IPNET',
                    'nama'       => 'Syaiful',
                    'jabatan'    => 'Presales & Solution Specialist',
                    'kontak'     => '0812-9876-5432',
                    'keterangan' => 'PIC Teknis IPNET'
                ],
                [
                    'pihak'      => 'Klien',
                    'nama'       => 'Hasan Faqih',
                    'jabatan'    => 'Head of IT Infrastructure',
                    'kontak'     => '0811-2345-6789',
                    'keterangan' => 'PIC Representatif Klien'
                ]
            ],
            'scope_category'     => 'Hardware Supply & Enterprise Network Integration',
            'scope_description'  => 'Pengadaan Core Switch 24-Port 10G SFP+, Access Switch PoE+, dan konfigurasi high-availability VSS/stacking.',
            'estimated_budget'   => 350000000,
            'tender_timeline'    => 'Q4 2026',
            'competitors'        => 'Kompetitor Tier-2 lokal',
            'challenges'         => 'Lead time pengiriman modul SFP+ 10G dan kesiapan kabel backbone fiber optic.',
            'win_probability'    => 75,
            'next_action_pic'    => 'Syaiful',
            'total_items'        => count($items),
            'items'              => $items
        ];

        // Buat SalesActivity Card 1
        SalesActivity::updateOrCreate(
            [
                'project_id'    => $project1->id,
                'activity_type' => 'Meeting',
                'subject'       => 'Rapat Koordinasi & Pembahasan Teknis Infrastruktur Jaringan Enterprise'
            ],
            [
                'sales_id'         => $salesId,
                'activity_date'    => Carbon::now()->subDays(2),
                'notes'            => json_encode($payloadActivity, JSON_UNESCAPED_UNICODE),
                'next_action'      => 'Kirim Surat Penawaran Harga (SPH) resmi dan BoQ ke Hasan Faqih',
                'next_action_date' => Carbon::now()->addDays(3),
                'status'           => 'Completed'
            ]
        );

        // 3. PROJECT DUMMY 2: Untuk Evaluasi & Lesson Learned (Lost Deal) (Kategori 2)
        $analysisData = [
            'primary_factor'          => 'Faktor Harga / Komersial & Lead Time Delivery Hardware',
            'competitor_winner'       => 'PT Mitra Solusi Datatama (Distributor Direct Tier-1)',
            'competitor_price'        => 450000000,
            'our_price'               => 520000000,
            'turning_point'           => 'Pada evaluasi komersial tahap akhir, pihak kompetitor memberikan potongan bundling lisensi 3 tahun sebesar 15% serta jaminan ketersediaan stok ready 2 minggu.',
            'gap_our_weakness'        => 'Struktur harga penawaran belum mendapatkan special project discount dari prinsipal, dan skema pembayaran mewajibkan DP 40%.',
            'gap_competitor_strength' => 'Kompetitor merupakan partner titanium prinsipal dengan margin harga fleksibel dan opsi pembayaran TOP 60 hari.',
            'lessons_learned'         => 'Untuk peluang deal bernilai di atas 500 juta, tim sales wajib mengunci registrasi deal ke prinsipal lebih awal guna memperoleh harga proteksi khusus.',
            'future_strategy'         => 'Menjaga hubungan komunikasi berkala (courtesy call setiap 2 minggu) dengan PIC klien dan memantau kepuasan implementasi kompetitor untuk persiapan renewal lisensi tahun depan.',
            'evaluated_by'            => $salesName,
            'evaluated_at'            => Carbon::now()->subDays(1)->format('Y-m-d H:i:s')
        ];

        $project2 = Project::updateOrCreate(
            ['name' => 'Pengadaan Managed Security Firewall & SD-WAN Enterprise'],
            [
                'client'                 => 'PT Finansial Solusi Mandiri',
                'contract_value'         => 520000000,
                'sales_stage'            => 'Closed Lost',
                'win_probability'        => 0,
                'sales_name'             => $salesName,
                'created_by'             => $salesId,
                'status'                 => 'Cancelled',
                'stage'                  => 'Commercial',
                'progress'               => 0,
                'acquire_status'         => 'Prospecting',
                'bdm_handover_status'    => 'Self-Sourced Sales',
                'start_date'             => Carbon::now()->subDays(20),
                'deadline'               => Carbon::now()->subDays(1),
                'expected_closing_date'  => Carbon::now()->subDays(1),
                'opportunity_source'     => 'Direct Sales Prospecting',
                'lost_reason'            => $analysisData['primary_factor'],
                'lost_competitor'        => $analysisData['competitor_winner'],
                'quotation_number'       => 'QUO-IPNET/2026/09/042',
                'quotation_amount'       => 520000000,
            ]
        );

        if (Schema::hasColumn('projects', 'lost_analysis')) {
            $project2->lost_analysis = $analysisData;
            $project2->save();
        } else {
            $project2->lost_reason = json_encode($analysisData, JSON_UNESCAPED_UNICODE);
            $project2->save();
        }

        // Buat SalesActivity Card 2 (Lesson Learned)
        SalesActivity::updateOrCreate(
            [
                'project_id'    => $project2->id,
                'activity_type' => 'Lesson Learned',
                'subject'       => 'Evaluasi & Lesson Learned (Lost Deal) - ' . $project2->name
            ],
            [
                'sales_id'         => $salesId,
                'activity_date'    => Carbon::now()->subDays(1),
                'notes'            => json_encode([
                    'type'          => 'lesson_learned',
                    'analysis_data' => $analysisData
                ], JSON_UNESCAPED_UNICODE),
                'next_action'      => 'Courtesy call berkala dan follow-up evaluasi implementasi vendor kompetitor',
                'next_action_date' => Carbon::now()->addDays(14),
                'status'           => 'Completed'
            ]
        );
    }
}

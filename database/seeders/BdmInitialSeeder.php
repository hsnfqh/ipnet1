<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MarketIntelligence;
use App\Models\Partnership;
use App\Models\Project;
use App\Models\User;
use App\Models\Client;
use Carbon\Carbon;

class BdmInitialSeeder extends Seeder
{
    public function run(): void
    {
        $kipsriyanto = User::where('email', 'kripsiyanto@ipnetsolusindo.com')->first();
        $kurnijanto  = User::where('email', 'kurnijanto.edy@ipnetsolusindo.com')->first();
        $novan       = User::where('email', 'novan.pudjirachmanto@ipnetsolusindo.com')->first();
        $armen       = User::where('email', 'armen.yuldi@ipnetsolusindo.com')->first();
        $dony        = User::where('email', 'antonius.dony@ipnetsolusindo.com')->first();

        $defaultBdmId = $kipsriyanto ? $kipsriyanto->id : User::first()->id;

        // 1. Seed Market Intelligence
        $marketIntels = [
            [
                'title'           => 'Kemenkeu Modernisasi Infrastruktur Jaringan & SD-WAN 2026',
                'category'        => 'Market Trend',
                'industry_sector' => 'Government / Kementerian',
                'summary'         => 'Rencana upgrade switch access dan penerapan SD-WAN di 45 kantor wilayah Kementerian Keuangan. Estimasi rilis TOR Q3 2026.',
                'potential_value' => 4500000000,
                'source_url'      => 'https://lpse.kemenkeu.go.id',
                'impact_level'    => 'High',
                'status'          => 'Active',
                'author_id'       => $kurnijanto ? $kurnijanto->id : $defaultBdmId,
            ],
            [
                'title'           => 'Bank Mandiri Micro-segmentation & Zero Trust Network',
                'category'        => 'Competitor Movement',
                'industry_sector' => 'Banking & Financial',
                'summary'         => 'Kompetitor sedang mengajukan solusi Cisco ISE, peluang kita masuk dengan bundling Sangfor NGFW + Aruba ClearPass.',
                'potential_value' => 2800000000,
                'source_url'      => 'Info Meeting CIO Mandiri',
                'impact_level'    => 'High',
                'status'          => 'Active',
                'author_id'       => $kipsriyanto ? $kipsriyanto->id : $defaultBdmId,
            ],
            [
                'title'           => 'PLN Nusantara Power Core Network Redundancy',
                'category'        => 'Industry Analysis',
                'industry_sector' => 'BUMN & Enterprise',
                'summary'         => 'Kebutuhan high availability data center backbone 40G/100G untuk sistem SCADA terintegrasi di 12 pembangkit.',
                'potential_value' => 3200000000,
                'source_url'      => 'RFP PLN Power 2026',
                'impact_level'    => 'High',
                'status'          => 'Active',
                'author_id'       => $novan ? $novan->id : $defaultBdmId,
            ],
            [
                'title'           => 'Kemenkes SatuSehat RSUD Interconnection Hub',
                'category'        => 'Regulatory / Policy',
                'industry_sector' => 'Healthcare & Hospital',
                'summary'         => 'Regulasi integrasi rekam medis SatuSehat mewajibkan VPN IPsec aman dan redundan di seluruh RSUD tipe A & B.',
                'potential_value' => 1950000000,
                'source_url'      => 'Permenkes Integrasi SatuSehat',
                'impact_level'    => 'Medium',
                'status'          => 'Active',
                'author_id'       => $armen ? $armen->id : $defaultBdmId,
            ],
            [
                'title'           => 'Bank BTPN Branch Network Automation & Wi-Fi 6 Refresh',
                'category'        => 'Marketing Campaign',
                'industry_sector' => 'Banking & Financial',
                'summary'         => 'Program peremajaan 80 cabang pembantu BTPN menuju smart branch dengan arsitektur cloud managed Wi-Fi.',
                'potential_value' => 1750000000,
                'source_url'      => 'Webinar Enterprise Banking 2026',
                'impact_level'    => 'Medium',
                'status'          => 'Active',
                'author_id'       => $dony ? $dony->id : $defaultBdmId,
            ],
        ];

        foreach ($marketIntels as $intel) {
            MarketIntelligence::updateOrCreate(
                ['title' => $intel['title']],
                $intel
            );
        }

        // 2. Seed Partnerships & Principal Network
        $partnerships = [
            [
                'partner_name'        => 'Fortinet Indonesia',
                'partner_type'        => 'Principal',
                'tier_level'          => 'Platinum Partner',
                'pic_name'            => 'Bambang Sudibyo',
                'pic_contact'         => '081288991122',
                'pic_email'           => 'bsudibyo@fortinet.com',
                'collaboration_scope' => 'Hak deal registration tier platinum, support demo unit FortiGate & FortiSwitch, rebate tahunan.',
                'status'              => 'Active',
                'created_by'          => $kurnijanto ? $kurnijanto->id : $defaultBdmId,
            ],
            [
                'partner_name'        => 'Aruba Networks (HPE)',
                'partner_type'        => 'Principal',
                'tier_level'          => 'Gold Partner',
                'pic_name'            => 'Rudi Hermawan',
                'pic_contact'         => '081399887766',
                'pic_email'           => 'rudi.h@hpe.com',
                'collaboration_scope' => 'Solusi Aruba Central Cloud & ClearPass NAC, proteksi harga proyek enterprise.',
                'status'              => 'Active',
                'created_by'          => $novan ? $novan->id : $defaultBdmId,
            ],
            [
                'partner_name'        => 'Cisco Systems Indonesia',
                'partner_type'        => 'Principal',
                'tier_level'          => 'Authorized Partner',
                'pic_name'            => 'Diana Lestari',
                'pic_contact'         => '081122334455',
                'pic_email'           => 'dlestari@cisco.com',
                'collaboration_scope' => 'Dukungan Cisco Catalyst Series & Nexus DC, sertifikasi engineer CCNA/CCNP.',
                'status'              => 'Active',
                'created_by'          => $kipsriyanto ? $kipsriyanto->id : $defaultBdmId,
            ],
            [
                'partner_name'        => 'Sangfor Technologies',
                'partner_type'        => 'Principal',
                'tier_level'          => 'Gold Partner',
                'pic_name'            => 'Kevin Chandra',
                'pic_contact'         => '081766554433',
                'pic_email'           => 'kevin.c@sangfor.com',
                'collaboration_scope' => 'HCI (Hyper-Converged Infrastructure) & Sangfor NGAF security bundle discount.',
                'status'              => 'Active',
                'created_by'          => $armen ? $armen->id : $defaultBdmId,
            ],
            [
                'partner_name'        => 'Synnex Metrodata Indonesia',
                'partner_type'        => 'Distributor',
                'tier_level'          => 'Strategic Distributor',
                'pic_name'            => 'Agus Salim',
                'pic_contact'         => '081877665544',
                'pic_email'           => 'agus.salim@synnexmetrodata.com',
                'collaboration_scope' => 'Distribusi hardware jaringan, SLA pengiriman 1-2 hari Jabodetabek, fasilitas TOP 60 hari.',
                'status'              => 'Active',
                'created_by'          => $dony ? $dony->id : $defaultBdmId,
            ],
        ];

        foreach ($partnerships as $partner) {
            Partnership::updateOrCreate(
                ['partner_name' => $partner['partner_name']],
                $partner
            );
        }

        // 3. Update or Seed Opportunities with BDM & Handover Details
        $sampleOpps = [
            [
                'name'                  => 'Pengadaan Next-Gen SD-WAN & Firewall Kemenkeu Kanwil',
                'client'                => 'Kementerian Keuangan RI',
                'contract_value'        => 4500000000,
                'opportunity_source'    => 'LPSE Procurement / Market Intel',
                'business_need_summary' => 'Modernisasi router lama menjadi SD-WAN terpusat dengan enkripsi IPsec & redundant failover 4G/Fiber.',
                'stakeholders_data'     => [
                    'pic_name'  => 'Ir. Wahyu Pratama',
                    'pic_role'  => 'Kepala Bagian TI & Jaringan',
                    'pic_phone' => '081234567890',
                    'pic_email' => 'wahyu.pratama@kemenkeu.go.id',
                ],
                'initial_requirement'   => '45 unit SD-WAN router, 2 unit DC Core Firewall, Centralized Management System, 24/7 support SLA.',
                'target_timeline_type'  => 'Q3 2026',
                'competitor_analysis'   => 'Kompetitor mengajukan vendor Huawei & Cisco. Kita tawarkan Fortinet dengan harga 20% lebih kompetitif.',
                'partner_alignment'     => 'Fortinet Indonesia + Synnex Metrodata',
                'bd_assessment_score'   => 92,
                'bdm_handover_status'   => 'Handed Over to Sales',
                'bdm_handover_at'       => Carbon::now()->subDays(2),
                'sales_name'            => 'Donny Burnan',
                'bdm_id'                => $kurnijanto ? $kurnijanto->id : $defaultBdmId,
                'status'                => 'Opportunity',
                'stage'                 => 'Acquire',
                'acquire_status'        => 'Proposal Preparation',
            ],
            [
                'name'                  => 'Implementasi Zero Trust Network & NAC Bank BTPN',
                'client'                => 'Bank BTPN Syariah',
                'contract_value'        => 2100000000,
                'opportunity_source'    => 'Partner Referral (Aruba)',
                'business_need_summary' => 'Otoritas Jasa Keuangan (OJK) mewajibkan segmentasi ketat jaringan internal bank dan otentikasi 802.1X.',
                'stakeholders_data'     => [
                    'pic_name'  => 'Siti Nurhaliza',
                    'pic_role'  => 'VP IT Security & Infrastructure',
                    'pic_phone' => '081398765432',
                    'pic_email' => 'siti.nurhaliza@btpnsyariah.com',
                ],
                'initial_requirement'   => 'Aruba ClearPass 5000 Endpoints, Network Access Control, Onboarding Portal, Switch 2930F integration.',
                'target_timeline_type'  => 'Q4 2026',
                'competitor_analysis'   => 'In-house BTPN mempertimbangkan open-source, namun butuh enterprise warranty & local support.',
                'partner_alignment'     => 'Aruba Networks (HPE)',
                'bd_assessment_score'   => 88,
                'bdm_handover_status'   => 'Accepted by Sales',
                'bdm_handover_at'       => Carbon::now()->subDays(5),
                'sales_name'            => 'Hendry Wibowo',
                'bdm_id'                => $kipsriyanto ? $kipsriyanto->id : $defaultBdmId,
                'status'                => 'Opportunity',
                'stage'                 => 'Acquire',
                'acquire_status'        => 'Design Presentation',
            ],
            [
                'name'                  => 'Upgrade Backbone Switch Data Center 100G Shopee Express',
                'client'                => 'Shopee Express Hub Cakung',
                'contract_value'        => 3400000000,
                'opportunity_source'    => 'Direct Prospecting',
                'business_need_summary' => 'Lonjakan trafik sortir barang otomatis membutuhkan backbone 100G ultra low-latency di fulfillment center.',
                'stakeholders_data'     => [
                    'pic_name'  => 'Budi Hartono',
                    'pic_role'  => 'DC Facility & Network Lead',
                    'pic_phone' => '081711223344',
                    'pic_email' => 'budi.h@shopee.co.id',
                ],
                'initial_requirement'   => 'Spine-Leaf Architecture switches, 4x 100G Spine, 16x 25G Leaf Switch, fiber cabling OM4.',
                'target_timeline_type'  => 'Urgent (< 1 Bulan)',
                'competitor_analysis'   => 'Vendor eksisting menggunakan Arista dengan lead time 16 minggu. Kita sanggup suplai 4 minggu.',
                'partner_alignment'     => 'Cisco Systems Indonesia',
                'bd_assessment_score'   => 95,
                'bdm_handover_status'   => 'Handed Over to Sales',
                'bdm_handover_at'       => Carbon::now()->subDay(1),
                'sales_name'            => 'Nabylla Berlianita',
                'bdm_id'                => $novan ? $novan->id : $defaultBdmId,
                'status'                => 'Opportunity',
                'stage'                 => 'Acquire',
                'acquire_status'        => 'Proposal Preparation',
            ],
            [
                'name'                  => 'Integrasi Jaringan VPN IPsec & Wi-Fi RSUD Pasar Minggu',
                'client'                => 'RSUD Pasar Minggu Jakarta',
                'contract_value'        => 1450000000,
                'opportunity_source'    => 'Gov Procurement (LPSE)',
                'business_need_summary' => 'Integrasi sistem rekam medis elektronik SatuSehat Kemenkes dan penyediaan Wi-Fi publik & nakes.',
                'stakeholders_data'     => [
                    'pic_name'  => 'Dr. Hendra Gunawan',
                    'pic_role'  => 'Kasubag SIMRS & IT',
                    'pic_phone' => '081299881122',
                    'pic_email' => 'hendra.g@rsudpasarminggu.id',
                ],
                'initial_requirement'   => '120 Access Point Wi-Fi 6, 1x UTM Firewall, 12x PoE+ Switch 24-Port, fiber patch interconnect.',
                'target_timeline_type'  => 'Q3 2026',
                'competitor_analysis'   => 'Tender terbuka LPSE DKI Jakarta.',
                'partner_alignment'     => 'Ruijie Networks / Sangfor',
                'bd_assessment_score'   => 82,
                'bdm_handover_status'   => 'Draft',
                'bdm_handover_at'       => null,
                'sales_name'            => 'Erie',
                'bdm_id'                => $armen ? $armen->id : $defaultBdmId,
                'status'                => 'Opportunity',
                'stage'                 => 'Acquire',
                'acquire_status'        => 'Prospecting',
            ],
        ];

        foreach ($sampleOpps as $opp) {
            Project::updateOrCreate(
                ['name' => $opp['name']],
                array_merge($opp, [
                    'created_by' => $opp['bdm_id'],
                    'start_date' => now(),
                    'deadline'   => now()->addMonths(3),
                ])
            );
        }
    }
}

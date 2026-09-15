<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AdminDocument;
use App\Models\AdminDocumentChecklist;
use App\Models\AdminLogisticsDispatch;
use App\Models\AdminDispatchItem;
use App\Models\AdminSerialNumber;
use App\Models\AdminEquipmentAsset;
use App\Models\AdminProjectHandover;
use App\Models\Project;
use App\Models\Client;
use App\Models\Vendor;
use App\Models\User;
use Carbon\Carbon;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class AdminSupportSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Pastikan Role Admin Support tersedia
        Role::firstOrCreate(['name' => 'Admin Support']);
        Role::firstOrCreate(['name' => 'Admin Logistik']);
        Role::firstOrCreate(['name' => 'Admin']);

        // 2. Akun Dummy Admin Support (Rina)
        $adminUser = User::updateOrCreate(
            ['email' => 'admin.support@ipnetsolusindo.com'],
            [
                'name'        => 'Rina',
                'password'    => Hash::make('password123'),
                'phone'       => '08111000030',
                'position'    => 'Head of Admin Support & Logistics',
                'status'      => 'Active',
                'division_id' => null,
                'team_id'     => null,
                'level'       => 'Senior',
            ]
        );
        $adminUser->syncRoles(['Admin Support', 'Admin']);

        $adminUserAlias = User::updateOrCreate(
            ['email' => 'admin@ipnetsolusindo.com'],
            [
                'name'        => 'Rina',
                'password'    => Hash::make('password123'),
                'phone'       => '08111000030',
                'position'    => 'Head of Admin Support & Logistics',
                'status'      => 'Active',
                'division_id' => null,
                'team_id'     => null,
                'level'       => 'Senior',
            ]
        );
        $adminUserAlias->syncRoles(['Admin Support', 'Admin']);

        // Ambil data referensi
        $project1 = Project::first();
        $project2 = Project::skip(1)->first() ?? $project1;
        $client1 = Client::first();
        $client2 = Client::skip(1)->first() ?? $client1;
        $vendor1 = Vendor::first();

        // 3. Central Document Register
        $docsData = [
            [
                'doc_number'                => 'DOC-SPK-2026-0001',
                'title'                     => 'SPK Pengadaan & Implementasi Core SD-WAN Wilayah II',
                'doc_type'                  => 'SPK',
                'category'                  => 'Commercial',
                'project_id'                => $project1?->id,
                'client_id'                 => $client1?->id,
                'client_name'               => $client1?->name ?? 'PT Bank Mandiri (Persero) Tbk',
                'version'                   => 'v1.0',
                'status'                    => 'Verified / Complete',
                'verification_status'       => 'Approved',
                'verified_by'               => $adminUser->id,
                'verified_at'               => Carbon::now()->subDays(5),
                'effective_date'            => Carbon::now()->subMonths(1)->toDateString(),
                'expiry_date'               => Carbon::now()->addMonths(11)->toDateString(),
                'value'                     => 1450000000.00,
                'physical_archive_location' => 'Lemari Legal A - Box 01',
                'notes'                     => 'SPK Asli bertandatangan Direktur & bermaterai telah diterima dan diarsipkan.',
            ],
            [
                'doc_number'                => 'DOC-PO-CL-2026-0002',
                'title'                     => 'PO Klien: Upgrade Perangkat Router DC Surabaya',
                'doc_type'                  => 'PO Client',
                'category'                  => 'Commercial',
                'project_id'                => $project2?->id,
                'client_id'                 => $client2?->id,
                'client_name'               => $client2?->name ?? 'PT Telekomunikasi Selular (Telkomsel)',
                'version'                   => 'v1.0',
                'status'                    => 'Verified / Complete',
                'verification_status'       => 'Approved',
                'verified_by'               => $adminUser->id,
                'verified_at'               => Carbon::now()->subDays(3),
                'effective_date'            => Carbon::now()->subDays(10)->toDateString(),
                'expiry_date'               => Carbon::now()->addMonths(6)->toDateString(),
                'value'                     => 850000000.00,
                'physical_archive_location' => 'Lemari Legal A - Box 02',
                'notes'                     => 'PO resmi via SAP Telkomsel.',
            ],
            [
                'doc_number'                => 'DOC-PO-VN-2026-0003',
                'title'                     => 'PO Vendor: Pembelian 10 Unit Cisco ISR 4451 Distributor Datascrip',
                'doc_type'                  => 'PO Vendor',
                'category'                  => 'Procurement',
                'project_id'                => $project1?->id,
                'vendor_id'                 => $vendor1?->id,
                'vendor_name'               => $vendor1?->name ?? 'PT Datascrip Enterprise',
                'version'                   => 'v1.0',
                'status'                    => 'Verified / Complete',
                'verification_status'       => 'Approved',
                'verified_by'               => $adminUser->id,
                'verified_at'               => Carbon::now()->subDays(7),
                'effective_date'            => Carbon::now()->subDays(15)->toDateString(),
                'value'                     => 420000000.00,
                'physical_archive_location' => 'Lemari Vendor B - Box 01',
                'notes'                     => 'Termin pembayaran 30 hari kalender.',
            ],
            [
                'doc_number'                => 'DOC-CTR-2026-0004',
                'title'                     => 'Perjanjian Kerjasama (PKS) Managed Network Service 2026-2027',
                'doc_type'                  => 'Contract',
                'category'                  => 'Legal',
                'project_id'                => $project1?->id,
                'client_id'                 => $client1?->id,
                'client_name'               => $client1?->name ?? 'PT Bank Mandiri (Persero) Tbk',
                'version'                   => 'v1.2',
                'status'                    => 'Clarification Requested',
                'verification_status'       => 'Clarification Requested',
                'rejection_notes'           => 'Lampiran SLA Addendum B belum ada paraf dari Tim Legal Klien. Mohon dilengkapi.',
                'verified_by'               => $adminUser->id,
                'verified_at'               => Carbon::now()->subDays(1),
                'effective_date'            => Carbon::now()->toDateString(),
                'expiry_date'               => Carbon::now()->addYears(1)->toDateString(),
                'value'                     => 2400000000.00,
                'physical_archive_location' => 'Meja Pending Review',
                'notes'                     => 'Menunggu perbaikan paraf dari Account Manager.',
            ],
            [
                'doc_number'                => 'DOC-BAST-2026-0005',
                'title'                     => 'BAST Teknis & Final Migrasi Bandwidth Cabang Cikarang',
                'doc_type'                  => 'BAST',
                'category'                  => 'Project Delivery',
                'project_id'                => $project2?->id,
                'client_id'                 => $client2?->id,
                'client_name'               => $client2?->name ?? 'PT Kalbe Farma Tbk',
                'version'                   => 'v1.0',
                'status'                    => 'Under Review',
                'verification_status'       => 'Pending',
                'effective_date'            => Carbon::now()->subDays(2)->toDateString(),
                'value'                     => 180000000.00,
                'physical_archive_location' => 'Map Folder PMO 2026',
                'notes'                     => 'Sedang dicek kelengkapan lampiran hasil UAT dan foto instalasi.',
            ],
        ];

        foreach ($docsData as $d) {
            AdminDocument::updateOrCreate(
                ['doc_number' => $d['doc_number']],
                array_merge($d, ['created_by' => $adminUser->id])
            );
        }

        // 4. Project Document Checklists (Gatekeeper)
        if ($project1) {
            $checklists = [
                ['milestone' => 'Pre-Sales Handover', 'document_name' => 'Proposal Teknis & Komersial Final (Approved)', 'is_mandatory' => true, 'is_submitted' => true, 'is_verified' => true],
                ['milestone' => 'Project Kickoff', 'document_name' => 'Signed SPK / PO Asli Bermaterai', 'is_mandatory' => true, 'is_submitted' => true, 'is_verified' => true],
                ['milestone' => 'Project Kickoff', 'document_name' => 'Statement of Work (SOW) & Project Charter', 'is_mandatory' => true, 'is_submitted' => true, 'is_verified' => true],
                ['milestone' => 'Procurement', 'document_name' => 'PO Vendor & Serial Number Dispatch List', 'is_mandatory' => true, 'is_submitted' => true, 'is_verified' => true],
                ['milestone' => 'Implementation', 'document_name' => 'Surat Jalan (DO) Diterima di Site', 'is_mandatory' => true, 'is_submitted' => true, 'is_verified' => true],
                ['milestone' => 'Implementation', 'document_name' => 'As-Built Drawing (ABD) & Topologi Final', 'is_mandatory' => true, 'is_submitted' => false, 'is_verified' => false],
                ['milestone' => 'BAST Handover', 'document_name' => 'UAT Signed Report & BAST Final', 'is_mandatory' => true, 'is_submitted' => false, 'is_verified' => false],
            ];

            foreach ($checklists as $chk) {
                AdminDocumentChecklist::firstOrCreate(
                    [
                        'project_id'    => $project1->id,
                        'milestone'     => $chk['milestone'],
                        'document_name' => $chk['document_name'],
                    ],
                    [
                        'is_mandatory' => $chk['is_mandatory'],
                        'is_submitted' => $chk['is_submitted'],
                        'is_verified'  => $chk['is_verified'],
                        'verified_by'  => $chk['is_verified'] ? $adminUser->id : null,
                        'verified_at'  => $chk['is_verified'] ? Carbon::now()->subDays(4) : null,
                    ]
                );
            }
        }

        // 5. Logistics Dispatches (Surat Jalan)
        $dispatch1 = AdminLogisticsDispatch::updateOrCreate(
            ['dispatch_number' => 'SJ-2026-0001'],
            [
                'project_id'          => $project1?->id,
                'client_id'           => $client1?->id,
                'client_name'         => $client1?->name ?? 'PT Bank Mandiri (Persero) Tbk',
                'dispatch_date'       => Carbon::now()->subDays(3)->toDateString(),
                'courier_type'        => 'Internal Driver',
                'courier_name'        => 'Pak Supriyadi (Driver IP-NET)',
                'tracking_ref'        => 'B 9182 PQR (Blind Van)',
                'origin_warehouse'    => 'HQ Warehouse Jakarta Barat',
                'destination_address' => 'Plaza Mandiri Lt. 12, Jl. Jend. Gatot Subroto Kav. 36-38, Jakarta Selatan',
                'recipient_name'      => 'Bapak Hendra Gunawan (PIC IT Network)',
                'recipient_phone'     => '0812-3344-5566',
                'status'              => 'Confirmed / Signed',
                'delivery_notes'      => 'Barang telah diterima lengkap dalam kondisi segel utuh dan ditandatangani.',
                'created_by'          => $adminUser->id,
            ]
        );

        AdminDispatchItem::firstOrCreate(
            ['dispatch_id' => $dispatch1->id, 'item_name' => 'Router Cisco ISR 4451/K9'],
            [
                'category'       => 'Hardware',
                'quantity'       => 2,
                'unit'           => 'Unit',
                'serial_numbers' => 'FOC24190ABC, FOC24190ABD',
                'condition'      => 'New / Segel',
                'notes'          => 'Lengkap dengan modul power supply redundant & kabel power.',
            ]
        );

        $dispatch2 = AdminLogisticsDispatch::updateOrCreate(
            ['dispatch_number' => 'SJ-2026-0002'],
            [
                'project_id'          => $project2?->id,
                'client_id'           => $client2?->id,
                'client_name'         => $client2?->name ?? 'PT Telekomunikasi Selular (Telkomsel)',
                'dispatch_date'       => Carbon::now()->toDateString(),
                'courier_type'        => 'Ekspedisi / Kurir',
                'courier_name'        => 'JNE Trucking (JTR)',
                'tracking_ref'        => 'JTR-8829103948',
                'origin_warehouse'    => 'HQ Warehouse Jakarta Barat',
                'destination_address' => 'Telkomsel Data Center Surabaya, Jl. Ahmad Yani No. 260, Surabaya',
                'recipient_name'      => 'Bapak Arif (DC Engineer)',
                'recipient_phone'     => '0813-9988-7766',
                'status'              => 'In Transit',
                'delivery_notes'      => 'Estimasi tiba di site Surabaya dalam 2 hari kerja.',
                'created_by'          => $adminUser->id,
            ]
        );

        AdminDispatchItem::firstOrCreate(
            ['dispatch_id' => $dispatch2->id, 'item_name' => 'Switch Huawei CloudEngine S5735-L48P4X-A'],
            [
                'category'       => 'Hardware',
                'quantity'       => 4,
                'unit'           => 'Unit',
                'serial_numbers' => '2102353YAA10N0001, 2102353YAA10N0002, 2102353YAA10N0003, 2102353YAA10N0004',
                'condition'      => 'New / Segel',
                'notes'          => 'Include bracket rackmount & SFP+ 10G transceiver.',
            ]
        );

        // 6. Master Serial Numbers
        $sns = [
            [
                'serial_number'    => 'FOC24190ABC',
                'product_name'     => 'Cisco ISR 4451 Security Router',
                'brand'            => 'Cisco',
                'model'            => 'ISR4451/K9',
                'category'         => 'Router',
                'current_status'   => 'Installed at Site',
                'current_location' => 'Plaza Mandiri Data Center Rack 04',
                'project_id'       => $project1?->id,
                'client_id'        => $client1?->id,
                'client_name'      => $client1?->name ?? 'PT Bank Mandiri (Persero) Tbk',
                'dispatch_id'      => $dispatch1->id,
                'warranty_expiry'  => Carbon::now()->addYears(3)->toDateString(),
                'notes'            => 'Support Smartnet 24x7x4 aktif.',
            ],
            [
                'serial_number'    => 'FOC24190ABD',
                'product_name'     => 'Cisco ISR 4451 Security Router',
                'brand'            => 'Cisco',
                'model'            => 'ISR4451/K9',
                'category'         => 'Router',
                'current_status'   => 'Installed at Site',
                'current_location' => 'Plaza Mandiri Data Center Rack 04',
                'project_id'       => $project1?->id,
                'client_id'        => $client1?->id,
                'client_name'      => $client1?->name ?? 'PT Bank Mandiri (Persero) Tbk',
                'dispatch_id'      => $dispatch1->id,
                'warranty_expiry'  => Carbon::now()->addYears(3)->toDateString(),
                'notes'            => 'Secondary unit standby.',
            ],
            [
                'serial_number'    => '2102353YAA10N0001',
                'product_name'     => 'Huawei CloudEngine Switch 48-Port PoE',
                'brand'            => 'Huawei',
                'model'            => 'S5735-L48P4X-A',
                'category'         => 'Switch',
                'current_status'   => 'Dispatched / In Transit',
                'current_location' => 'In Transit via JNE Trucking',
                'project_id'       => $project2?->id,
                'client_id'        => $client2?->id,
                'client_name'      => $client2?->name ?? 'PT Telekomunikasi Selular (Telkomsel)',
                'dispatch_id'      => $dispatch2->id,
                'warranty_expiry'  => Carbon::now()->addYears(2)->toDateString(),
                'notes'            => 'Delivery via SJ-2026-0002.',
            ],
            [
                'serial_number'    => 'FG100FTK21008899',
                'product_name'     => 'FortiGate 100F Next-Gen Firewall',
                'brand'            => 'Fortinet',
                'model'            => 'FG-100F',
                'category'         => 'Firewall',
                'current_status'   => 'In Warehouse',
                'current_location' => 'HQ Warehouse Rak Secure B-02',
                'warranty_expiry'  => Carbon::now()->addYears(1)->toDateString(),
                'notes'            => 'Stok cadangan / buffer stock siap deploy.',
            ],
        ];

        foreach ($sns as $sn) {
            AdminSerialNumber::updateOrCreate(
                ['serial_number' => $sn['serial_number']],
                $sn
            );
        }

        // 7. Equipment & Operational Tool Assets
        $assets = [
            [
                'asset_code'           => 'AST-OTDR-001',
                'asset_name'           => 'EXFO FTB-1 OTDR Optical Tester',
                'category'             => 'Testing Tool',
                'brand_model'          => 'EXFO FTB-1v2 + FTBx-720C',
                'serial_number'        => 'EXF-9920194',
                'condition'            => 'Excellent',
                'status'               => 'Available in HQ',
                'storage_location'     => 'HQ Workshop - Lemari Alat Ukur A',
                'notes'                => 'Kalibrasi berlaku s/d Des 2026.',
            ],
            [
                'asset_code'           => 'AST-SPL-002',
                'asset_name'           => 'Fujikura 90S+ Core Alignment Fusion Splicer',
                'category'             => 'Fusion Splicer',
                'brand_model'          => 'Fujikura 90S+',
                'serial_number'        => 'FJK-8839201',
                'condition'            => 'Good',
                'status'               => 'Borrowed by Engineer',
                'current_borrower_id'  => User::where('name', '!=', 'Rina')->first()?->id,
                'borrowed_at'          => Carbon::now()->subDays(1),
                'expected_return_date' => Carbon::now()->addDays(3)->toDateString(),
                'storage_location'     => 'Dibawa Engineer Onsite Project',
                'notes'                => 'Dipinjam untuk instalasi FO site Mandiri.',
            ],
            [
                'asset_code'           => 'AST-OPM-003',
                'asset_name'           => 'Optical Power Meter & Visual Fault Locator (VFL)',
                'category'             => 'Power Meter',
                'brand_model'          => 'Yokogawa AQ2180',
                'serial_number'        => 'YKG-772910',
                'condition'            => 'Good',
                'status'               => 'Available in HQ',
                'storage_location'     => 'HQ Workshop - Rak 02',
                'notes'                => 'Tersedia lengkap dengan adapter SC/LC/FC.',
            ],
            [
                'asset_code'           => 'AST-TLS-004',
                'asset_name'           => 'Proskit Comprehensive Networking Toolkit Box',
                'category'             => 'Toolkit',
                'brand_model'          => 'Proskit PK-4022BM',
                'serial_number'        => 'PRK-55201',
                'condition'            => 'Good',
                'status'               => 'Available in HQ',
                'storage_location'     => 'HQ Workshop - Tool Rack',
                'notes'                => 'Tang crimping, stripper, punch down, cable tester.',
            ],
        ];

        foreach ($assets as $ast) {
            AdminEquipmentAsset::updateOrCreate(
                ['asset_code' => $ast['asset_code']],
                $ast
            );
        }

        // 8. Project Administration Handovers
        if ($project1) {
            AdminProjectHandover::updateOrCreate(
                ['handover_number' => 'HND-2026-0001'],
                [
                    'project_id'         => $project1->id,
                    'target_division'    => 'Client',
                    'handover_date'      => Carbon::now()->toDateString(),
                    'status'             => 'Audit Review',
                    'completeness_score' => 95,
                    'auditor_id'         => $adminUser->id,
                    'audited_at'         => Carbon::now(),
                    'audit_notes'        => 'Dokumen UAT, BAST 1, ABD, dan Warranty Certificate lengkap. Menunggu BAST Final bertandatangan kedua pihak.',
                    'archive_box_code'   => 'BOX-2026-MND-01',
                ]
            );
        }
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ManagedServiceAsset;
use App\Models\ManagedServiceTicket;
use App\Models\ManagedServiceReport;
use App\Models\Project;
use App\Models\User;
use Carbon\Carbon;

class ManagedServiceSeeder extends Seeder
{
    public function run(): void
    {
        $doris = User::where('name', 'like', '%Doris%')->first();
        $mario = User::where('name', 'like', '%Mario%')->first();
        $eris  = User::where('name', 'like', '%Eris%')->first();
        $dorisId = $doris ? $doris->id : null;
        $marioId = $mario ? $mario->id : null;
        $erisId  = $eris ? $eris->id : null;

        // Cari beberapa project untuk dihubungkan
        $projects = Project::where('client', '!=', 'Internal / Umum')->take(5)->get();

        // 1. Seed Assets (Configuration Items)
        $assets = [
            [
                'client_name'   => 'Bank Mandiri (Persero) Tbk',
                'device_name'   => 'Core DC Firewall Cluster FortiGate 600E',
                'category'      => 'Firewall',
                'brand'         => 'Fortinet',
                'model'         => 'FG-600E',
                'serial_number' => 'FG600ETK19004821',
                'ip_address'    => '10.240.1.1',
                'location_site' => 'Data Center Plaza Mandiri Lt. 8',
                'rack_position' => 'Rack DC-04 (U18-U19)',
                'status'        => 'Online',
                'warranty_expiry'=> Carbon::now()->addMonths(14),
                'notes'         => 'HA Active-Passive Mode. Primary cluster link via 10G SFP+.',
                'created_by'    => $dorisId,
            ],
            [
                'client_name'   => 'Bank Mandiri (Persero) Tbk',
                'device_name'   => 'Distribution Switch Catalyst 9500-48Y4C',
                'category'      => 'Switch',
                'brand'         => 'Cisco',
                'model'         => 'C9500-48Y4C',
                'serial_number' => 'FOC2438L0AA',
                'ip_address'    => '10.240.1.2',
                'location_site' => 'Data Center Plaza Mandiri Lt. 8',
                'rack_position' => 'Rack DC-04 (U20)',
                'status'        => 'Online',
                'warranty_expiry'=> Carbon::now()->addMonths(20),
                'notes'         => 'StackWise Virtual Active. 40G Uplink to Core Spine.',
                'created_by'    => $dorisId,
            ],
            [
                'client_name'   => 'PT Telkom Indonesia Tbk',
                'device_name'   => 'Edge BGP Border Router CCR2004',
                'category'      => 'Router',
                'brand'         => 'Mikrotik',
                'model'         => 'CCR2004-1G-12S+2XS',
                'serial_number' => 'HEE08933214',
                'ip_address'    => '10.100.0.1',
                'location_site' => 'Telkom STO Gambir',
                'rack_position' => 'Rack TELKOM-01 (U10)',
                'status'        => 'Online',
                'warranty_expiry'=> Carbon::now()->addMonths(8),
                'notes'         => 'Dual Power Supply redundant. Peering IXP & Upstream.',
                'created_by'    => $dorisId,
            ],
            [
                'client_name'   => 'PT PLN (Persero)',
                'device_name'   => 'SCADA Perimeter Gateway FortiGate 200F',
                'category'      => 'Firewall',
                'brand'         => 'Fortinet',
                'model'         => 'FG-200F',
                'serial_number' => 'FG200FTK21008741',
                'ip_address'    => '10.120.4.1',
                'location_site' => 'PLN Kantor Pusat Trunojoyo',
                'rack_position' => 'Rack SCADA-B (U12)',
                'status'        => 'Warning',
                'warranty_expiry'=> Carbon::now()->addMonths(6),
                'notes'         => 'High memory utilization alert (84%). Planned maintenance weekend.',
                'created_by'    => $dorisId,
            ],
            [
                'client_name'   => 'Universitas Indonesia',
                'device_name'   => 'High-Density Campus Gateway RG-NBS5200',
                'category'      => 'Switch',
                'brand'         => 'Ruijie',
                'model'         => 'RG-NBS5200-24GT4XS',
                'serial_number' => 'RJNBS2025091101',
                'ip_address'    => '10.50.10.1',
                'location_site' => 'Gedung Rektorat UI Depok',
                'rack_position' => 'MDF Room Lt. 2',
                'status'        => 'Online',
                'warranty_expiry'=> Carbon::now()->addMonths(18),
                'notes'         => 'Managed via Ruijie Cloud Controller.',
                'created_by'    => $dorisId,
            ],
        ];

        foreach ($assets as $idx => $a) {
            $projectId = isset($projects[$idx]) ? $projects[$idx]->id : null;
            $a['project_id'] = $projectId;
            ManagedServiceAsset::updateOrCreate(
                ['serial_number' => $a['serial_number']],
                $a
            );
        }

        $allCreatedAssets = ManagedServiceAsset::all();

        // 2. Seed Tickets (Incidents & Service Requests)
        $tickets = [
            [
                'ticket_number' => 'INC-2026-001',
                'client_name'   => 'Bank Mandiri (Persero) Tbk',
                'title'         => 'Flapping Link SFP Port 24 pada Distribution Switch',
                'description'   => 'Terjadi intermittent traffic pada link trunk server farm. Alarm NMS mendeteksi flap 14 kali dalam 1 jam.',
                'type'          => 'Incident',
                'priority'      => 'P2 - Major',
                'status'        => 'In Progress',
                'reported_by'   => 'Bpk. Hendra (IT Ops Mandiri)',
                'contact_phone' => '0812-9988-7711',
                'assigned_to'   => $marioId ?: $dorisId,
                'asset_id'      => $allCreatedAssets->where('brand', 'Cisco')->first()?->id,
                'sla_deadline'  => Carbon::now()->addHours(3),
                'sla_met'       => true,
                'resolution_notes' => 'Pemeriksaan kabel patchcord FO dan pembersihan optical interface menggunakan cleaner kit.',
                'created_by'    => $dorisId,
            ],
            [
                'ticket_number' => 'INC-2026-002',
                'client_name'   => 'PT PLN (Persero)',
                'title'         => 'High Memory Usage pada FortiGate 200F SCADA',
                'description'   => 'WAD daemon process mengalami memory leak setelah update IPS database.',
                'type'          => 'Incident',
                'priority'      => 'P2 - Major',
                'status'        => 'Open',
                'reported_by'   => 'Ibu Siska (NOC PLN)',
                'contact_phone' => '0813-1122-3344',
                'assigned_to'   => $erisId ?: $dorisId,
                'asset_id'      => $allCreatedAssets->where('brand', 'Fortinet')->skip(1)->first()?->id,
                'sla_deadline'  => Carbon::now()->addHours(4),
                'sla_met'       => true,
                'created_by'    => $dorisId,
            ],
            [
                'ticket_number' => 'REQ-2026-003',
                'client_name'   => 'PT Telkom Indonesia Tbk',
                'title'         => 'Request Penambahan VLAN 205 untuk Layanan Metro-E',
                'description'   => 'Klien meminta penambahan VLAN trunking dan alokasi subnet /28 untuk pelanggan korporat baru.',
                'type'          => 'Service Request',
                'priority'      => 'P3 - Minor',
                'status'        => 'Resolved',
                'reported_by'   => 'Bpk. Agus (Service Assurance Telkom)',
                'contact_phone' => '0811-3456-7890',
                'assigned_to'   => $marioId ?: $dorisId,
                'asset_id'      => $allCreatedAssets->where('brand', 'Mikrotik')->first()?->id,
                'sla_deadline'  => Carbon::now()->subHours(2),
                'resolved_at'   => Carbon::now()->subHours(4),
                'sla_met'       => true,
                'resolution_notes' => 'VLAN 205 berhasil di-provisioning pada interface sfp-sfpplus2 dan pengetesan ping end-to-end sukses.',
                'root_cause'    => 'Standard Service Request Delivery',
                'created_by'    => $dorisId,
            ],
            [
                'ticket_number' => 'CR-2026-004',
                'client_name'   => 'Universitas Indonesia',
                'title'         => 'Change Request: Upgrade Firmware Switch Ruijie RG-NBS5200',
                'description'   => 'Maintenance window terjadwal untuk upgrade firmware rilis stable patch security CVE-2026.',
                'type'          => 'Change Request',
                'priority'      => 'P4 - Low',
                'status'        => 'Closed',
                'reported_by'   => 'Bpk. Dimas (Pusilkom UI)',
                'contact_phone' => '0815-7766-5544',
                'assigned_to'   => $erisId ?: $dorisId,
                'asset_id'      => $allCreatedAssets->where('brand', 'Ruijie')->first()?->id,
                'sla_deadline'  => Carbon::now()->subDays(1),
                'resolved_at'   => Carbon::now()->subDays(1),
                'sla_met'       => true,
                'resolution_notes' => 'Firmware upgrade berhasil dilakukan pada maintenance window pukul 23:00 - 01:00 WIB tanpa downtime layanan perkuliahan.',
                'root_cause'    => 'Preventive Security Patching',
                'created_by'    => $dorisId,
            ],
        ];

        foreach ($tickets as $t) {
            ManagedServiceTicket::updateOrCreate(
                ['ticket_number' => $t['ticket_number']],
                $t
            );
        }

        // 3. Seed Reports (SLA, PM, Incident Post-Mortem)
        $reports = [
            [
                'client_name'   => 'Bank Mandiri (Persero) Tbk',
                'title'         => 'Laporan Preventive Maintenance & SLA Kinerja Q3 2026',
                'report_type'   => 'Preventive Maintenance',
                'period_month'  => 'September',
                'period_year'   => 2026,
                'sla_score'     => 99.98,
                'summary'       => 'Pemeriksaan fisik rak server, kebersihan filter debu, redundancy tes PSU, backup konfigurasi berkala, dan evaluasi log keamanan.',
                'status'        => 'Published',
                'created_by'    => $dorisId,
            ],
            [
                'client_name'   => 'PT Telkom Indonesia Tbk',
                'title'         => 'Laporan Bulanan SLA Availability & Incident Assurance',
                'report_type'   => 'SLA Review',
                'period_month'  => 'Agustus',
                'period_year'   => 2026,
                'sla_score'     => 100.0,
                'summary'       => 'Zero unplanned downtime tercapai. Seluruh 8 tiket Service Request terselesaikan dalam waktu < 2 jam (SLA 4 jam).',
                'status'        => 'Approved by Client',
                'created_by'    => $dorisId,
            ],
            [
                'client_name'   => 'Universitas Indonesia',
                'title'         => 'Dokumen Aktivasi Layanan (Service Activation Form) Jaringan Kampus',
                'report_type'   => 'Service Activation',
                'period_month'  => 'Juli',
                'period_year'   => 2026,
                'sla_score'     => 99.9,
                'summary'       => 'Serah terima operasional (Handover) dari tim Implementasi Network ke tim Managed Service IP-Net selesai ditandatangani.',
                'status'        => 'Published',
                'created_by'    => $dorisId,
            ],
        ];

        foreach ($reports as $r) {
            ManagedServiceReport::updateOrCreate(
                ['title' => $r['title'], 'client_name' => $r['client_name']],
                $r
            );
        }
    }
}

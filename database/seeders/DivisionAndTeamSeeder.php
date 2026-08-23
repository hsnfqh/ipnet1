<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Division;
use App\Models\Team;

class DivisionAndTeamSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Divisi Security
        $sec = Division::firstOrCreate(
            ['name' => 'Divisi Security'],
            ['code' => 'SEC', 'description' => 'Divisi Keamanan Siber dan Sistem Proteksi Jaringan']
        );

        // 2. Divisi Network
        $net = Division::firstOrCreate(
            ['name' => 'Divisi Network'],
            ['code' => 'NET', 'description' => 'Divisi Infrastruktur Jaringan, Routing, Switching & Wireless']
        );

        // 3. Tim-tim di Divisi Security
        Team::firstOrCreate(
            ['name' => 'Tim Security Operasional', 'division_id' => $sec->id],
            ['description' => 'Tim monitoring, hardening, dan incident handling security']
        );

        Team::firstOrCreate(
            ['name' => 'Tim Security Implementasi', 'division_id' => $sec->id],
            ['description' => 'Tim instalasi & deployment perangkat firewall / security']
        );

        // 4. Tim-tim di Divisi Network
        Team::firstOrCreate(
            ['name' => 'Tim Network Deployment', 'division_id' => $net->id],
            ['description' => 'Tim instalasi dan penarikan kabel/jaringan lapangan']
        );

        Team::firstOrCreate(
            ['name' => 'Tim Network Maintenance', 'division_id' => $net->id],
            ['description' => 'Tim pemeliharaan dan troubleshooting jaringan']
        );
    }
}

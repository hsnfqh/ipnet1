<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Division;
use App\Models\Team;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Pastikan Divisi & Tim tersedia
        $divNet = Division::firstOrCreate(
            ['name' => 'Divisi Network'],
            ['code' => 'NET', 'description' => 'Divisi Infrastruktur Jaringan, Routing, Switching & Fiber Optik']
        );
        $divSec = Division::firstOrCreate(
            ['name' => 'Divisi Security'],
            ['code' => 'SEC', 'description' => 'Divisi Keamanan Jaringan & Cyber Security Protection']
        );

        $teamNet = Team::firstOrCreate(
            ['name' => 'Tim Network', 'division_id' => $divNet->id],
            ['leader_id' => null]
        );
        $teamSec = Team::firstOrCreate(
            ['name' => 'Tim Security', 'division_id' => $divSec->id],
            ['leader_id' => null]
        );

        // Pastikan roles tersedia
        $rolesToEnsure = ['Team Leader Engineering', 'Team Leader', 'Lead Engineer', 'Network Engineer', 'Security Engineer', 'Engineer'];
        foreach ($rolesToEnsure as $rName) {
            Role::firstOrCreate(['name' => $rName]);
        }

        // 2. Buat / Perbarui Akun Lead Engineer Novan
        $novan = User::updateOrCreate(
            ['email' => 'novan@ipnetsolusindo.com'],
            [
                'name'        => 'Novan',
                'password'    => Hash::make('password123'),
                'phone'       => '08111000004',
                'position'    => 'Team Leader Security Engineering',
                'status'      => 'Active',
                'division_id' => $divSec->id,
                'team_id'     => $teamSec->id,
                'level'       => null,
            ]
        );
        $novan->syncRoles(['Team Leader Engineering', 'Team Leader', 'Lead Engineer']);
        $teamSec->update(['leader_id' => $novan->id]);

        // 3. Akun Baru Divisi Network (Sesuai Permintaan User: ardiansyah L1, dhimas Arda L1, Daffa Rizqullah L2, Panca Rangga A L2)
        $networkUsers = [
            [
                'name'     => 'Ardiansyah',
                'email'    => 'ardiansyah@ipnetsolusindo.com',
                'position' => 'L1 Network Engineer',
                'level'    => 'L1',
                'phone'    => '08111000021',
            ],
            [
                'name'     => 'Dhimas Arda',
                'email'    => 'dhimas@ipnetsolusindo.com',
                'position' => 'L1 Network Engineer',
                'level'    => 'L1',
                'phone'    => '08111000022',
            ],
            [
                'name'     => 'Daffa Rizqullah',
                'email'    => 'daffa@ipnetsolusindo.com',
                'position' => 'L2 Network Engineer',
                'level'    => 'L2',
                'phone'    => '08111000023',
            ],
            [
                'name'     => 'Panca Rangga A',
                'email'    => 'panca@ipnetsolusindo.com',
                'position' => 'L2 Network Engineer',
                'level'    => 'L2',
                'phone'    => '08111000024',
            ],
        ];

        foreach ($networkUsers as $nu) {
            $user = User::updateOrCreate(
                ['email' => $nu['email']],
                [
                    'name'        => $nu['name'],
                    'password'    => Hash::make('password123'),
                    'phone'       => $nu['phone'],
                    'position'    => $nu['position'],
                    'status'      => 'Active',
                    'division_id' => $divNet->id,
                    'team_id'     => $teamNet->id,
                    'level'       => $nu['level'],
                ]
            );
            $user->syncRoles(['Network Engineer', 'Engineer']);
        }

        // 4. Akun Baru Divisi Security (Sesuai Permintaan User: luhur dinda L2, Randa Multri L2, Eka L1)
        $securityUsers = [
            [
                'name'     => 'Luhur Dinda',
                'email'    => 'luhur@ipnetsolusindo.com',
                'position' => 'L2 Security Engineer',
                'level'    => 'L2',
                'phone'    => '08111000025',
            ],
            [
                'name'     => 'Randa Multri',
                'email'    => 'randa@ipnetsolusindo.com',
                'position' => 'L2 Security Engineer',
                'level'    => 'L2',
                'phone'    => '08111000026',
            ],
            [
                'name'     => 'Eka',
                'email'    => 'eka@ipnetsolusindo.com',
                'position' => 'L1 Security Engineer',
                'level'    => 'L1',
                'phone'    => '08111000027',
            ],
        ];

        foreach ($securityUsers as $su) {
            $user = User::updateOrCreate(
                ['email' => $su['email']],
                [
                    'name'        => $su['name'],
                    'password'    => Hash::make('password123'),
                    'phone'       => $su['phone'],
                    'position'    => $su['position'],
                    'status'      => 'Active',
                    'division_id' => $divSec->id,
                    'team_id'     => $teamSec->id,
                    'level'       => $su['level'],
                ]
            );
            $user->syncRoles(['Security Engineer', 'Engineer']);
        }

        // 5. Migrasikan referensi Ignatius ke Novan dan hapus akun Ignatius
        $ignatiusUsers = User::withTrashed()
            ->where(function($q) {
                $q->where('email', 'like', '%ignatius%')
                  ->orWhere('name', 'like', '%Ignatius%');
            })
            ->get();

        foreach ($ignatiusUsers as $ignatius) {
            // Reassign PM cycles
            if (Schema::hasTable('managed_service_pm_cycles')) {
                if (Schema::hasColumn('managed_service_pm_cycles', 'lead_engineer_id')) {
                    DB::table('managed_service_pm_cycles')
                        ->where('lead_engineer_id', $ignatius->id)
                        ->update(['lead_engineer_id' => $novan->id]);
                }
                if (Schema::hasColumn('managed_service_pm_cycles', 'pic_ms_id')) {
                    DB::table('managed_service_pm_cycles')
                        ->where('pic_ms_id', $ignatius->id)
                        ->update(['pic_ms_id' => $novan->id]);
                }
            }

            // Reassign tickets
            if (Schema::hasTable('managed_service_tickets')) {
                DB::table('managed_service_tickets')
                    ->where('assigned_to', $ignatius->id)
                    ->update(['assigned_to' => $novan->id]);
            }

            // Reassign teams leader
            if (Schema::hasTable('teams')) {
                DB::table('teams')
                    ->where('leader_id', $ignatius->id)
                    ->update(['leader_id' => $novan->id]);
            }

            // Hapus roles & force delete user Ignatius
            try {
                $ignatius->roles()->detach();
            } catch (\Throwable $e) {}

            try {
                $ignatius->forceDelete();
            } catch (\Throwable $e) {
                $ignatius->delete();
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No-op
    }
};

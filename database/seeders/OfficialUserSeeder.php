<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Division;
use App\Models\Team;
use Spatie\Permission\Models\Role;

class OfficialUserSeeder extends Seeder
{
    public function run(): void
    {
        // 0. Ensure cached permissions are forgotten
        if (class_exists(\Spatie\Permission\PermissionRegistrar::class)) {
            app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        }

        // 1. Ensure all Roles exist
        $roles = [
            'Director', 'Direktur', 'HD / Direktur',
            'Division Head', 'Kepala Divisi', 'Head of Division',
            'Group Leader', 'Group Leader Commercial & Solution', 'Group Leader Delivery & Operation',
            'PMO', 'Project Manager',
            'Admin', 'Admin Support', 'Admin Logistik',
            'Warehouse',
            'Sales', 'Account Manager',
            'BusDev', 'BDM', 'Business Development',
            'CRO', 'Customer Relation Officer',
            'Presales', 'Pre-Sales', 'Lead Pre Sales',
            'Solution Architect', 'Solutions Architect',
            'Tech Develop', 'Tech.Develp (R&D)', 'R&D',
            'Team Leader Engineering', 'Team Leader', 'Lead Divisi',
            'Lead Engineer', 'Lead Engineer Network', 'Lead Security',
            'Network Engineer', 'Engineer Network L1', 'Engineer Network L2',
            'Security Engineer', 'Engineer Security L1', 'Engineer Security L2',
            'Managed Service', 'Lead Maintenance', 'Maintenance',
            'Field Support (EOS)', 'Field Support',
            'Engineer', 'Engineer L1', 'Engineer L2',
        ];
        foreach ($roles as $r) {
            Role::firstOrCreate(['name' => $r]);
        }

        // 2. Ensure Divisions exist
        $divNet = Division::firstOrCreate(
            ['name' => 'Divisi Network'],
            ['code' => 'NET', 'description' => 'Divisi Infrastruktur Jaringan, Routing, Switching & Fiber Optik']
        );
        $divSec = Division::firstOrCreate(
            ['name' => 'Divisi Security'],
            ['code' => 'SEC', 'description' => 'Divisi Keamanan Jaringan & Cyber Security Protection']
        );
        $divMnt = Division::firstOrCreate(
            ['name' => 'Divisi Maintenance & Helpdesk'],
            ['code' => 'MNT', 'description' => 'Divisi Helpdesk Klien, Penanganan Tiket SLA, dan Koordinasi Pemeliharaan Rutin']
        );

        // 3. Ensure Teams exist
        $teamNet = Team::firstOrCreate(
            ['name' => 'Tim Network', 'division_id' => $divNet->id],
            ['leader_id' => null]
        );
        $teamSec = Team::firstOrCreate(
            ['name' => 'Tim Security', 'division_id' => $divSec->id],
            ['leader_id' => null]
        );
        $teamMnt = Team::firstOrCreate(
            ['name' => 'Tim Maintenance & Helpdesk', 'division_id' => $divMnt->id],
            ['leader_id' => null]
        );

        // 4. Migration Map: Map old email variations to official real emails
        // This preserves user IDs and prevents broken foreign keys in projects/tasks!
        $migrationMap = [
            'aris@ipnetsolusindo.com'                => 'asadewa@ipnetsolusindo.com',
            'akbar@ipnetsolusindo.com'               => 'akbar.eko@ipnetsolusindo.com',
            'daffa@ipnetsolusindo.com'               => 'dafa@ipnetsolusindo.com',
            'daisy@ipnetsolusindo.com'               => 'daisy.wijayanti@ipnetsolusindo.com',
            'dedy@ipnetsolusindo.com'                => 'dedy.suryana@ipnetsolusindo.com',
            'dhimas@ipnetsolusindo.com'              => 'dimas.arda@ipnetsolusindo.com',
            'donny.burnan@ipnetsolusindo.com'        => 'donny.boerman@ipnetsolusindo.com',
            'doris@ipnetsolusindo.com'               => 'doris.taneo@ipnetsolusindo.com',
            'eka@ipnetsolusindo.com'                 => 'eka.kurnia@ipnetsolusindo.com',
            'eris@ipnetsolusindo.com'                => 'eris.maryon@ipnetsolusindo.com',
            'erie@ipnetsolusindo.com'                => 'erie.putra@ipnetsolusindo.com',
            'hendry.wibowo@ipnetsolusindo.com'       => 'hendry@ipnetsolusindo.com',
            'kurnijanto.edy@ipnetsolusindo.com'      => 'edy.sanggono@ipnetsolusindo.com',
            'luhur@ipnetsolusindo.com'               => 'luhur.dinda@ipnetsolusindo.com',
            'mario@ipnetsolusindo.com'               => 'maryo@ipnetsolusindo.com',
            'nabylla.berlianita@ipnetsolusindo.com'  => 'nabylla@ipnetsolusindo.com',
            'nelvia.nataliandi@ipnetsolusindo.com'   => 'nelvia@ipnetsolusindo.com',
            'nugraha@ipnetsolusindo.com'             => 'nugraha.pratama@ipnetsolusindo.com',
            'ribka.junita@ipnetsolusindo.com'        => 'ribka@ipnetsolusindo.com',
            'susanto@ipnetsolusindo.com'             => 'susanto.djaya@ipnetsolusindo.com',
            'syaiful@ipnetsolusindo.com'             => 'syaifulamin@ipnetsolusindo.com',
        ];

        foreach ($migrationMap as $oldEmail => $newEmail) {
            $existingOld = User::where('email', $oldEmail)->first();
            $existingNew = User::where('email', $newEmail)->first();
            if ($existingOld && !$existingNew) {
                $existingOld->update(['email' => $newEmail]);
            } elseif ($existingOld && $existingNew && $existingOld->id !== $existingNew->id) {
                $existingOld->forceDelete();
            }
        }

        // 5. Official Users Dataset (38 Users from PDF + Director & Warehouse)
        $officialUsers = [
            // 1. Abdu Joned
            [
                'name'        => 'Abdu Joned',
                'email'       => 'joned@ipnetsolusindo.com',
                'phone'       => '08111000001',
                'position'    => 'Admin Support',
                'division_id' => null,
                'team_id'     => null,
                'level'       => null,
                'roles'       => ['Admin', 'Admin Support'],
            ],
            // 2. Akbar Eko Setia Budi
            [
                'name'        => 'Akbar Eko Setia Budi',
                'email'       => 'akbar.eko@ipnetsolusindo.com',
                'phone'       => '08111000002',
                'position'    => 'Lead Pre Sales',
                'division_id' => null,
                'team_id'     => null,
                'level'       => 'Lead',
                'roles'       => ['Lead Pre Sales', 'Presales', 'Pre-Sales'],
            ],
            // 3. Ardiansyah Ramadhan
            [
                'name'        => 'Ardiansyah Ramadhan',
                'email'       => 'ardiansyah@ipnetsolusindo.com',
                'phone'       => '08111000003',
                'position'    => 'Engineer Network L1',
                'division_id' => $divNet->id,
                'team_id'     => $teamNet->id,
                'level'       => 'L1',
                'roles'       => ['Network Engineer', 'Engineer', 'Engineer L1', 'Engineer Network L1'],
            ],
            // 4. Aries Sadewa
            [
                'name'        => 'Aries Sadewa',
                'email'       => 'asadewa@ipnetsolusindo.com',
                'phone'       => '08111000004',
                'position'    => 'Solution Architect',
                'division_id' => null,
                'team_id'     => null,
                'level'       => 'Senior',
                'roles'       => ['Solution Architect', 'Solutions Architect'],
            ],
            // 5. Armen Yuldi
            [
                'name'        => 'Armen Yuldi',
                'email'       => 'armen.yuldi@ipnetsolusindo.com',
                'phone'       => '08111000005',
                'position'    => 'Business Development Manager (BDM)',
                'division_id' => null,
                'team_id'     => null,
                'level'       => 'Senior',
                'roles'       => ['BDM', 'BusDev', 'Business Development'],
            ],
            // 6. Dafa Rizqullah Akbar
            [
                'name'        => 'Dafa Rizqullah Akbar',
                'email'       => 'dafa@ipnetsolusindo.com',
                'phone'       => '08111000006',
                'position'    => 'Engineer Network L2',
                'division_id' => $divNet->id,
                'team_id'     => $teamNet->id,
                'level'       => 'L2',
                'roles'       => ['Network Engineer', 'Engineer', 'Engineer L2', 'Engineer Network L2'],
            ],
            // 7. Daisy Wijayanti
            [
                'name'        => 'Daisy Wijayanti',
                'email'       => 'daisy.wijayanti@ipnetsolusindo.com',
                'phone'       => '08111000007',
                'position'    => 'Admin Support',
                'division_id' => null,
                'team_id'     => null,
                'level'       => null,
                'roles'       => ['Admin', 'Admin Support'],
            ],
            // 8. Dedy Suryana
            [
                'name'        => 'Dedy Suryana',
                'email'       => 'dedy.suryana@ipnetsolusindo.com',
                'phone'       => '08111000008',
                'position'    => 'Engineer Network L2',
                'division_id' => $divNet->id,
                'team_id'     => $teamNet->id,
                'level'       => 'L2',
                'roles'       => ['Network Engineer', 'Engineer', 'Engineer L2', 'Engineer Network L2'],
            ],
            // 9. Donny Boerman
            [
                'name'        => 'Donny Boerman',
                'email'       => 'donny.boerman@ipnetsolusindo.com',
                'phone'       => '08111000009',
                'position'    => 'Sales (Account Manager)',
                'division_id' => null,
                'team_id'     => null,
                'level'       => null,
                'roles'       => ['Sales', 'Account Manager'],
            ],
            // 10. Doris Imanuel Taneo
            [
                'name'        => 'Doris Imanuel Taneo',
                'email'       => 'doris.taneo@ipnetsolusindo.com',
                'phone'       => '08111000010',
                'position'    => 'Lead Managed Service',
                'division_id' => $divMnt->id,
                'team_id'     => $teamMnt->id,
                'level'       => 'Lead',
                'roles'       => ['Managed Service', 'Lead Maintenance', 'Team Leader'],
            ],
            // 11. Edi Hermanto
            [
                'name'        => 'Edi Hermanto',
                'email'       => 'edih@ipnetsolusindo.com',
                'phone'       => '08111000011',
                'position'    => 'Pre Sales Specialist',
                'division_id' => null,
                'team_id'     => null,
                'level'       => null,
                'roles'       => ['Presales', 'Pre-Sales'],
            ],
            // 12. Eka Kurnia Wulandari
            [
                'name'        => 'Eka Kurnia Wulandari',
                'email'       => 'eka.kurnia@ipnetsolusindo.com',
                'phone'       => '08111000012',
                'position'    => 'Engineer Security L1',
                'division_id' => $divSec->id,
                'team_id'     => $teamSec->id,
                'level'       => 'L1',
                'roles'       => ['Security Engineer', 'Engineer', 'Engineer L1', 'Engineer Security L1'],
            ],
            // 13. Eris Maryon
            [
                'name'        => 'Eris Maryon',
                'email'       => 'eris.maryon@ipnetsolusindo.com',
                'phone'       => '08111000013',
                'position'    => 'Field Support Engineer / Managed Service',
                'division_id' => $divMnt->id,
                'team_id'     => $teamMnt->id,
                'level'       => null,
                'roles'       => ['Managed Service', 'Field Support (EOS)', 'Field Support', 'Maintenance'],
            ],
            // 14. Erma Rosa Sistono
            [
                'name'        => 'Erma Rosa Sistono',
                'email'       => 'erie.putra@ipnetsolusindo.com',
                'phone'       => '08111000014',
                'position'    => 'Sales (Account Manager)',
                'division_id' => null,
                'team_id'     => null,
                'level'       => null,
                'roles'       => ['Sales', 'Account Manager'],
            ],
            // 15. Hendry Wibowo
            [
                'name'        => 'Hendry Wibowo',
                'email'       => 'hendry@ipnetsolusindo.com',
                'phone'       => '08111000015',
                'position'    => 'Sales (Account Manager)',
                'division_id' => null,
                'team_id'     => null,
                'level'       => null,
                'roles'       => ['Sales', 'Account Manager'],
            ],
            // 16. Kurnijanto Edy Sanggono
            [
                'name'        => 'Kurnijanto Edy Sanggono',
                'email'       => 'edy.sanggono@ipnetsolusindo.com',
                'phone'       => '08111000016',
                'position'    => 'Business Development Manager (BDM)',
                'division_id' => null,
                'team_id'     => null,
                'level'       => 'Senior',
                'roles'       => ['BDM', 'BusDev', 'Business Development'],
            ],
            // 17. Luhur Dinda Dinara
            [
                'name'        => 'Luhur Dinda Dinara',
                'email'       => 'luhur.dinda@ipnetsolusindo.com',
                'phone'       => '08111000017',
                'position'    => 'Engineer Security L2',
                'division_id' => $divSec->id,
                'team_id'     => $teamSec->id,
                'level'       => 'L2',
                'roles'       => ['Security Engineer', 'Engineer', 'Engineer L2', 'Engineer Security L2'],
            ],
            // 18. M. Dimas Arda Winata
            [
                'name'        => 'M. Dimas Arda Winata',
                'email'       => 'dimas.arda@ipnetsolusindo.com',
                'phone'       => '08111000018',
                'position'    => 'Engineer Network L1',
                'division_id' => $divNet->id,
                'team_id'     => $teamNet->id,
                'level'       => 'L1',
                'roles'       => ['Network Engineer', 'Engineer', 'Engineer L1', 'Engineer Network L1'],
            ],
            // 19. Maryo Alexander Bahas
            [
                'name'        => 'Maryo Alexander Bahas',
                'email'       => 'maryo@ipnetsolusindo.com',
                'phone'       => '08111000019',
                'position'    => 'Field Support Engineer / Managed Service',
                'division_id' => $divMnt->id,
                'team_id'     => $teamMnt->id,
                'level'       => null,
                'roles'       => ['Managed Service', 'Field Support (EOS)', 'Field Support', 'Maintenance'],
            ],
            // 20. Muhammad Kripsiyanto
            [
                'name'        => 'Muhammad Kripsiyanto',
                'email'       => 'kripsiyanto@ipnetsolusindo.com',
                'phone'       => '08111000020',
                'position'    => 'Business Development Manager (BDM)',
                'division_id' => null,
                'team_id'     => null,
                'level'       => 'Senior',
                'roles'       => ['BDM', 'BusDev', 'Business Development'],
            ],
            // 21. N. Anton Sebrang Setiawan
            [
                'name'        => 'N. Anton Sebrang Setiawan',
                'email'       => 'niko@ipnetsolusindo.com',
                'phone'       => '08111000021',
                'position'    => 'Admin Support',
                'division_id' => null,
                'team_id'     => null,
                'level'       => null,
                'roles'       => ['Admin', 'Admin Support'],
            ],
            // 22. Nabylla Berlianita
            [
                'name'        => 'Nabylla Berlianita',
                'email'       => 'nabylla@ipnetsolusindo.com',
                'phone'       => '08111000022',
                'position'    => 'Sales (Account Manager)',
                'division_id' => null,
                'team_id'     => null,
                'level'       => null,
                'roles'       => ['Sales', 'Account Manager'],
            ],
            // 23. Nelfia Sri Nataliandy
            [
                'name'        => 'Nelfia Sri Nataliandy',
                'email'       => 'nelvia@ipnetsolusindo.com',
                'phone'       => '08111000023',
                'position'    => 'Sales (Account Manager)',
                'division_id' => null,
                'team_id'     => null,
                'level'       => null,
                'roles'       => ['Sales', 'Account Manager'],
            ],
            // 24. Novan Pudjirachmanto (Dual Role: BDM & Lead Security)
            [
                'name'        => 'Novan Pudjirachmanto',
                'email'       => 'novan@ipnetsolusindo.com',
                'phone'       => '08111000024',
                'position'    => 'BDM & Lead Security',
                'division_id' => $divSec->id,
                'team_id'     => $teamSec->id,
                'level'       => 'Lead',
                'roles'       => ['BDM', 'BusDev', 'Lead Security', 'Team Leader Engineering', 'Team Leader', 'Lead Engineer', 'Security Engineer'],
            ],
            // 25. Novia Sutrayana Devi
            [
                'name'        => 'Novia Sutrayana Devi',
                'email'       => 'noviadevi@ipnetsolusindo.com',
                'phone'       => '08111000025',
                'position'    => 'Admin Support',
                'division_id' => null,
                'team_id'     => null,
                'level'       => null,
                'roles'       => ['Admin', 'Admin Support'],
            ],
            // 26. Nugraha Pratama
            [
                'name'        => 'Nugraha Pratama',
                'email'       => 'nugraha.pratama@ipnetsolusindo.com',
                'phone'       => '08111000026',
                'position'    => 'Lead Engineer Network',
                'division_id' => $divNet->id,
                'team_id'     => $teamNet->id,
                'level'       => 'Lead',
                'roles'       => ['Team Leader Engineering', 'Team Leader', 'Lead Engineer', 'Lead Engineer Network', 'Network Engineer'],
            ],
            // 27. Panca Pangga Ramadhan
            [
                'name'        => 'Panca Pangga Ramadhan',
                'email'       => 'panca@ipnetsolusindo.com',
                'phone'       => '08111000027',
                'position'    => 'Engineer Network L2',
                'division_id' => $divNet->id,
                'team_id'     => $teamNet->id,
                'level'       => 'L2',
                'roles'       => ['Network Engineer', 'Engineer', 'Engineer L2', 'Engineer Network L2'],
            ],
            // 28. Raihan Ghiffary Pamungkas
            [
                'name'        => 'Raihan Ghiffary Pamungkas',
                'email'       => 'raihan@ipnetsolusindo.com',
                'phone'       => '08111000028',
                'position'    => 'Engineer Network L2',
                'division_id' => $divNet->id,
                'team_id'     => $teamNet->id,
                'level'       => 'L2',
                'roles'       => ['Network Engineer', 'Engineer', 'Engineer L2', 'Engineer Network L2'],
            ],
            // 29. Raiza Maindoka
            [
                'name'        => 'Raiza Maindoka',
                'email'       => 'raiza@ipnetsolusindo.com',
                'phone'       => '08111000029',
                'position'    => 'Sales (Account Manager)',
                'division_id' => null,
                'team_id'     => null,
                'level'       => null,
                'roles'       => ['Sales', 'Account Manager'],
            ],
            // 30. Randa Multri Okta Ilham
            [
                'name'        => 'Randa Multri Okta Ilham',
                'email'       => 'randa@ipnetsolusindo.com',
                'phone'       => '08111000030',
                'position'    => 'Engineer Security L2',
                'division_id' => $divSec->id,
                'team_id'     => $teamSec->id,
                'level'       => 'L2',
                'roles'       => ['Security Engineer', 'Engineer', 'Engineer L2', 'Engineer Security L2'],
            ],
            // 31. Ribka Junita
            [
                'name'        => 'Ribka Junita',
                'email'       => 'ribka@ipnetsolusindo.com',
                'phone'       => '08111000031',
                'position'    => 'Sales (Account Manager)',
                'division_id' => null,
                'team_id'     => null,
                'level'       => null,
                'roles'       => ['Sales', 'Account Manager'],
            ],
            // 32. Rizki Toni Kusuma
            [
                'name'        => 'Rizki Toni Kusuma',
                'email'       => 'rizki@ipnetsolusindo.com',
                'phone'       => '08111000032',
                'position'    => 'Project Manager',
                'division_id' => null,
                'team_id'     => null,
                'level'       => 'Lead',
                'roles'       => ['Project Manager', 'PMO'],
            ],
            // 33. Rorik Purwanto
            [
                'name'        => 'Rorik Purwanto',
                'email'       => 'rorik@ipnetsolusindo.com',
                'phone'       => '08111000033',
                'position'    => 'Engineer Network L1',
                'division_id' => $divNet->id,
                'team_id'     => $teamNet->id,
                'level'       => 'L1',
                'roles'       => ['Network Engineer', 'Engineer', 'Engineer L1', 'Engineer Network L1'],
            ],
            // 34. Sabar Sianturi
            [
                'name'        => 'Sabar Sianturi',
                'email'       => 'sabar.sianturi@ipnetsolusindo.com',
                'phone'       => '08111000034',
                'position'    => 'Sales (Account Manager)',
                'division_id' => null,
                'team_id'     => null,
                'level'       => null,
                'roles'       => ['Sales', 'Account Manager'],
            ],
            // 35. Shiamsyah Azis
            [
                'name'        => 'Shiamsyah Azis',
                'email'       => 'shiamsyah@ipnetsolusindo.com',
                'phone'       => '08111000035',
                'position'    => 'Engineer Network L1',
                'division_id' => $divNet->id,
                'team_id'     => $teamNet->id,
                'level'       => 'L1',
                'roles'       => ['Network Engineer', 'Engineer', 'Engineer L1', 'Engineer Network L1'],
            ],
            // 36. Susanto Djaya Liong (Kepala Divisi)
            [
                'name'        => 'Susanto Djaya Liong',
                'email'       => 'susanto.djaya@ipnetsolusindo.com',
                'phone'       => '08111000036',
                'position'    => 'Kepala Divisi (Division Head)',
                'division_id' => null,
                'team_id'     => null,
                'level'       => 'Executive',
                'roles'       => ['Division Head', 'Kepala Divisi', 'Head of Division', 'Group Leader Delivery & Operation', 'Group Leader'],
            ],
            // 37. Syaiful Amin
            [
                'name'        => 'Syaiful Amin',
                'email'       => 'syaifulamin@ipnetsolusindo.com',
                'phone'       => '08111000037',
                'position'    => 'Engineer Network L2',
                'division_id' => $divNet->id,
                'team_id'     => $teamNet->id,
                'level'       => 'L2',
                'roles'       => ['Network Engineer', 'Engineer', 'Engineer L2', 'Engineer Network L2'],
            ],
            // 38. Widodo
            [
                'name'        => 'Widodo',
                'email'       => 'widodo@ipnetsolusindo.com',
                'phone'       => '08111000038',
                'position'    => 'Sales (Account Manager)',
                'division_id' => null,
                'team_id'     => null,
                'level'       => null,
                'roles'       => ['Sales', 'Account Manager'],
            ],
            // 39. Hariyadi (Director)
            [
                'name'        => 'Hariyadi',
                'email'       => 'hariyadi@ipnetsolusindo.com',
                'phone'       => '08111000099',
                'position'    => 'Director',
                'division_id' => null,
                'team_id'     => null,
                'level'       => 'Executive',
                'roles'       => ['Director', 'Direktur', 'HD / Direktur'],
            ],
            // 40. Warehouse Admin
            [
                'name'        => 'Warehouse Admin',
                'email'       => 'warehouse@ipnetsolusindo.com',
                'phone'       => '08111000040',
                'position'    => 'Warehouse Admin',
                'division_id' => null,
                'team_id'     => null,
                'level'       => null,
                'roles'       => ['Warehouse', 'Admin Logistik'],
            ],
        ];

        $officialEmails = [];

        foreach ($officialUsers as $data) {
            $officialEmails[] = $data['email'];
            $user = User::where('email', $data['email'])->first();

            if ($user) {
                $user->update([
                    'name'        => $data['name'],
                    'position'    => $data['position'],
                    'status'      => 'Active',
                    'division_id' => $data['division_id'],
                    'team_id'     => $data['team_id'],
                    'level'       => $data['level'],
                ]);
            } else {
                $user = User::create([
                    'name'        => $data['name'],
                    'email'       => $data['email'],
                    'password'    => Hash::make('password123'),
                    'phone'       => $data['phone'],
                    'position'    => $data['position'],
                    'status'      => 'Active',
                    'division_id' => $data['division_id'],
                    'team_id'     => $data['team_id'],
                    'level'       => $data['level'],
                ]);
            }

            $user->syncRoles($data['roles']);

            // Set team leaders
            if ($data['email'] === 'nugraha.pratama@ipnetsolusindo.com') {
                $teamNet->update(['leader_id' => $user->id]);
            } elseif ($data['email'] === 'novan@ipnetsolusindo.com') {
                $teamSec->update(['leader_id' => $user->id]);
            } elseif ($data['email'] === 'doris.taneo@ipnetsolusindo.com') {
                $teamMnt->update(['leader_id' => $user->id]);
            }
        }

        // 6. Safely soft delete dummy users not in official list
        User::where('email', 'like', '%@ipnetwork.co.id')
            ->orWhereNotIn('email', $officialEmails)
            ->whereNull('deleted_at')
            ->delete();
    }
}

<?php
// database/seeders/RoleSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Standard 4-Tier Roles + Legacy Roles for backward compatibility
        $roles = [
            'Direktur',
            'HD / Direktur',
            'Group Leader',
            'PMO',
            'Project Manager',
            'Lead Divisi',
            'Team Leader',
            'Lead Maintenance',
            'Lead Engineer',
            'Engineer',
            'Maintenance',
            'Engineer L1',
            'Engineer L2',
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role]);
        }
    }
}
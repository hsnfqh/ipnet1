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

        // Roles based on Organization Structure & Process Flow
        $roles = [
            // Executive & Division Head
            'Director',
            'Direktur',
            'HD / Direktur',
            'Division Head',
            'Group Leader',
            'Group Leader Commercial & Solution',
            'Group Leader Delivery & Operation',

            // Governance & Project Management
            'PMO',
            'Project Manager',
            'Admin',
            'Admin Support',
            'Admin Logistik',

            // Commercial & Solution Branch
            'Sales',
            'Account Manager',
            'BusDev',
            'BDM',
            'Business Development',
            'CRO',
            'Customer Relation Officer',
            'Presales',
            'Pre-Sales',
            'Solution Architect',
            'Solutions Architect',
            'Tech Develop',
            'Tech.Develp (R&D)',
            'R&D',

            // Delivery & Operation Branch
            'Team Leader Engineering',
            'Team Leader',
            'Lead Divisi',
            'Lead Engineer',
            'Network Engineer',
            'Security Engineer',
            'Managed Service',
            'Lead Maintenance',
            'Maintenance',
            'Field Support (EOS)',
            'Field Support',
            'Engineer',
            'Engineer L1',
            'Engineer L2',
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role]);
        }
    }
}
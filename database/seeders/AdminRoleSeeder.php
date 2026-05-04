<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class AdminRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Create roles with admin guard
        $roles = [
            'Super Admin',
            'Investment Management',
            'User Management',
            'Marketing Management',
            'Finance Management',
            'Property Management'
        ];

        foreach ($roles as $roleName) {
            Role::firstOrCreate([
                'name' => $roleName,
                'guard_name' => 'admin'
            ]);
        }

        // Ensure Super Admin role has all permissions
        $superAdminRole = Role::where('name', 'Super Admin')
            ->where('guard_name', 'admin')
            ->first();

        if ($superAdminRole) {
            $allPermissions = Permission::where('guard_name', 'admin')->get();
            $superAdminRole->syncPermissions($allPermissions);
        }
    }
}

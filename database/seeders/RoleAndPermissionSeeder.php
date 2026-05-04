<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class RoleAndPermissionSeeder extends Seeder
{
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        
        // Clear existing roles and permissions
        DB::table('role_has_permissions')->truncate();
        DB::table('model_has_roles')->truncate();
        DB::table('model_has_permissions')->truncate();
        DB::table('roles')->truncate();
        DB::table('permissions')->truncate();

        Schema::enableForeignKeyConstraints();

        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions for each module
        $permissions = [
            // Deals Management
            'deals.view',
            'deals.create',
            'deals.edit',
            'deals.delete',
            
            // Investment Management
            'investments.view',
            'investments.create',
            'investments.edit',
            'investments.delete',
            
            // Property Management
            'properties.view',
            'properties.create',
            'properties.edit',
            'properties.delete',
            
            // User Management
            'users.view',
            'users.create',
            'users.edit',
            'users.delete',
            
            // Financial Operations
            'finances.view',
            'finances.create',
            'finances.edit',
            'finances.delete',
            
            // Marketing and Content
            'marketing.view',
            'marketing.create',
            'marketing.edit',
            'marketing.delete',
            
            // Reports and Analytics
            'reports.view',
            'reports.create',
            'reports.export',
            
            // System Settings
            'settings.view',
            'settings.edit',
            
            // Staff Management
            'staff.view',
            'staff.create',
            'staff.edit',
            'staff.delete',
            'roles.view',
            'roles.create',
            'roles.edit',
            'roles.delete'
        ];

        foreach ($permissions as $permission) {
            Permission::create([
                'name' => $permission,
                'guard_name' => 'admin'
            ]);
        }

        // Create roles and assign permissions
        $roles = [
            'Super Admin' => $permissions,
            
            'Investment Management' => [
                'deals.view', 'deals.create', 'deals.edit',
                'investments.view', 'investments.create', 'investments.edit',
                'properties.view', 'properties.create', 'properties.edit',
                'reports.view', 'reports.export'
            ],
            
            'User Management' => [
                'users.view', 'users.create', 'users.edit', 'users.delete',
                'reports.view', 'reports.export'
            ],
            
            'Marketing Management' => [
                'marketing.view', 'marketing.create', 'marketing.edit', 'marketing.delete',
                'reports.view', 'reports.export'
            ],
            
            'Finance Management' => [
                'finances.view', 'finances.create', 'finances.edit',
                'investments.view',
                'reports.view', 'reports.create', 'reports.export'
            ],
            
            'Property Management' => [
                'properties.view', 'properties.edit',
                'deals.view',
                'reports.view', 'reports.export'
            ],

            'Partner' => []
        ];

        foreach ($roles as $roleName => $rolePermissions) {
            $role = Role::create(['name' => $roleName, 'guard_name' => 'admin']);
            $role->givePermissionTo($rolePermissions);
        }
    }
}
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\DB;

class PartnerRoleAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create partner-related permissions
        $partnerPermissions = [
            // Partner management permissions for admins
            'partners.view',
            'partners.create',
            'partners.edit',
            'partners.delete',
            'partners.manage',
            'partners.assign_deals',
            
            // Deal permissions for partners
            'partner.deals.view',
            'partner.deals.create',
            'partner.deals.edit',
            'partner.deals.delete',
            
            // Partner profile permissions
            'partner.profile.view',
            'partner.profile.edit',
        ];

        // Create permissions with admin guard
        foreach ($partnerPermissions as $permission) {
            Permission::findOrCreate($permission, 'admin');
        }

        // Create Partner role
        $partnerRole = Role::findOrCreate('partner', 'admin');
        
        // Assign partner-specific permissions to the partner role
        $partnerRole->syncPermissions([
            'partner.deals.view',
            'partner.deals.create',
            'partner.deals.edit',
            'partner.deals.delete',
            'partner.profile.view',
            'partner.profile.edit',
        ]);

        // Update existing admin role to include partner management permissions
        $adminRole = Role::where('name', 'Super Admin')->where('guard_name', 'admin')->first();
        if ($adminRole) {
            $adminRole->givePermissionTo([
                'partners.view',
                'partners.create',
                'partners.edit',
                'partners.delete',
                'partners.manage',
                'partners.assign_deals',
            ]);
        }
        
        // Also give these permissions to CEO role if it exists
        $ceoRole = Role::where('name', 'CEO')->where('guard_name', 'admin')->first();
        if ($ceoRole) {
            $ceoRole->givePermissionTo([
                'partners.view',
                'partners.create',
                'partners.edit',
                'partners.delete',
                'partners.manage',
                'partners.assign_deals',
            ]);
        }
        
        // Give view permissions to the Partnerships Manager
        $partnershipsMgrRole = Role::where('name', 'Partnerships Manager')->where('guard_name', 'admin')->first();
        if ($partnershipsMgrRole) {
            $partnershipsMgrRole->givePermissionTo([
                'partners.view',
                'partners.create',
                'partners.edit',
            ]);
        }
    }
}

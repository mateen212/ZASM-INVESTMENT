<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            // Core roles & permissions (this will truncate and recreate roles/permissions)
            RoleAndPermissionSeeder::class,
            AdminRoleSeeder::class,
            MemberRoleSeeder::class,
            PartnerRoleAndPermissionSeeder::class,

            // API integrations and related seeders
            ApiIntegrationsSeeder::class,
            DocumensoApiSeeder::class,

            // Misc/utility seeders
            OfferinguuidSeeder::class,

            // Default users/admins
            SimpleUserSeeder::class,
            SimpleAdminSeeder::class,

            // Gateways
            StripeAchGatewaySeeder::class,
        ]);
    }
}

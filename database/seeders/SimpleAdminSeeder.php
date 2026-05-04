<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Admin;
use Illuminate\Support\Str;

class SimpleAdminSeeder extends Seeder
{
    public function run()
    {
        if (!class_exists(Admin::class)) {
            return;
        }

        $email = 'admin@example.com';

        $admin = Admin::firstOrCreate([
            'email' => $email,
        ], [
            'name' => 'Site Admin',
            'username' => 'admin',
            'email_verified_at' => now(),
            'password' => Hash::make('AdminPass123!'),
            'remember_token' => Str::random(10),
        ]);

        // Assign `admin` role if Spatie roles are available
        try {
            if (class_exists(\Spatie\Permission\Models\Role::class) && \Illuminate\Support\Facades\Schema::hasTable('roles')) {
                // Create the roles used by layout checks
                \Spatie\Permission\Models\Role::firstOrCreate([
                    'name' => 'Super Admin',
                    'guard_name' => 'admin',
                ]);
                \Spatie\Permission\Models\Role::firstOrCreate([
                    'name' => 'admin',
                    'guard_name' => 'admin',
                ]);

                if ($admin && method_exists($admin, 'assignRole')) {
                    // Assign the high-privilege role so layout shows sidebar/topnav
                    $admin->assignRole('Super Admin');
                }
            }
        } catch (\Throwable $e) {
            // ignore role assignment errors in environments without permissions setup
        }
    }
}

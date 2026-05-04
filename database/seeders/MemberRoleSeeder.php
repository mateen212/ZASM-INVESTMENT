<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class MemberRoleSeeder extends Seeder
{
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        
        // Define the roles
        $roles = [
            'Admin Sponsor',
            'Co-Sponsor',
            'CPA-Accountant',
            'Registered Investment Advisor',
        ];

        foreach ($roles as $roleName) {
            Role::create([
                'name' => $roleName,
                'guard_name' => 'admin' 
            ]);
        }

        Schema::enableForeignKeyConstraints();
    }
}

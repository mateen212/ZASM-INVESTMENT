<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Str;

class SimpleUserSeeder extends Seeder
{
    public function run()
    {
        if (!class_exists(User::class)) {
            return;
        }

        $email = 'user@example.com';

        $attributes = [
            'name' => 'Demo User',
            'email_verified_at' => now(),
            'password' => Hash::make('Password123!'),
            'remember_token' => Str::random(10),
        ];

        // Only include columns that exist in the users table
        $filtered = [];
        foreach ($attributes as $col => $val) {
            if (\Illuminate\Support\Facades\Schema::hasColumn('users', $col)) {
                $filtered[$col] = $val;
            }
        }

        User::firstOrCreate([
            'email' => $email,
        ], $filtered);
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Offering;
use Illuminate\Support\Str;

class OfferinguuidSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $offerings = Offering::whereNull('uuid')->get();
        foreach ($offerings as $offering) {
            $offering->uuid = (string) Str::uuid();
            $offering->save();
        }
    }
}

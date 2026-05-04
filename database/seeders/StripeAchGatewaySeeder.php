<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Gateway;

class StripeAchGatewaySeeder extends Seeder
{
    public function run()
    {
        $params = [
            'secret_key' => [
                'title' => 'Secret Key',
                'type' => 'text',
                'value' => env('STRIPE_ACH_SECRET', ''),
                'global' => true,
            ],
            'publishable_key' => [
                'title' => 'Publishable Key',
                'type' => 'text',
                'value' => env('STRIPE_ACH_PUBLISHABLE', ''),
                'global' => true,
            ],
        ];

        Gateway::updateOrCreate(
            ['code' => '114'],
            [
                'name' => 'Stripe ACH',
                'gateway_parameters' => json_encode($params),
                'status' => 1,
            ]
        );
    }
}

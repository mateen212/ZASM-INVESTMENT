<?php

namespace Database\Seeders;

use App\Models\ApiIntegration;
use Illuminate\Database\Seeder;

class DocumensoApiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Define the credentials structure
        $credentials = [
            'api_key' => [
                'title' => 'API Key',
                'value' => '',
                'description' => 'Your Documenso API key (format: api_xxxxxxxxxxxxxxxx)',
                'type' => 'password'
            ],
            'api_url' => [
                'title' => 'API Base URL',
                'value' => 'https://app.documenso.com/api/v1',
                'description' => 'Base URL for Documenso API (e.g., https://your-domain.com/api/v1)',
                'type' => 'text'
            ]
        ];

        // Create or update the Documenso API integration
        ApiIntegration::updateOrCreate(
            ['code' => 'documenso'],
            [
                'name' => 'Documenso E-Signature',
                'description' => 'Integration with Documenso for electronic document signing',
                'credentials' => $credentials,
                'status' => 0
            ]
        );
    }
}

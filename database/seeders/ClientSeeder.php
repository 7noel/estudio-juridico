<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Client;

class ClientSeeder extends Seeder
{
    public function run()
    {
        $clients = [
            [
                'document_type' => '1',
                'document_number' => '12345678',
                'full_name' => 'Clientes - Varios',
                'mobile' => '987654321',
                'email' => 'juan@example.com',
                'address' => 'Av. Perú 123',
                'ubigeo_code' => '150132'
            ],
        ];

        foreach ($clients as $client) {
            Client::create($client);
        }
    }
}
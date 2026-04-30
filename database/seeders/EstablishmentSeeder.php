<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Establishment;

class EstablishmentSeeder extends Seeder
{
    public function run(): void
    {

        Establishment::create([
            'name' => 'Estudio Jurídico Principal',
            'ruc' => '10442434846',
            'address' => 'Av. Principal 123 - Oficina 201',
            'ubigeo_code' => '1275',
        ]);

        Establishment::create([
            'name' => 'Estudio Jurídico Sucursal Norte',
            'ruc' => '10442434846',
            'address' => 'Av. Los Abogados 456 - Oficina 302',
            'ubigeo_code' => '1275',
        ]);

    }
}
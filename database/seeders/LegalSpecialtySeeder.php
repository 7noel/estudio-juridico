<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\LegalSpecialty;

class LegalSpecialtySeeder extends Seeder
{
    public function run(): void
    {

        $specialties = [

            'Familia',
            'Civil',
            'Penal',
            'Laboral',
            'Notarial',
            'Administrativo',

        ];

        foreach ($specialties as $name) {

            LegalSpecialty::create([
                'name' => $name
            ]);

        }

    }
}
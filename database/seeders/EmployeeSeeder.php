<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Employee;
use Illuminate\Support\Facades\Hash;

class EmployeeSeeder extends Seeder
{
    public function run(): void
    {

        /*
        ADMIN
        */

        $adminUser = User::create([
            'name' => 'Administrador',
            'email' => 'admin@estudiojuridico.com',
            'password' => Hash::make('123456'),
        ]);

        $adminUser->assignRole('Administrador');

        Employee::create([
            'establishment_id' => 1,
            'user_id' => $adminUser->id,
            'full_name' => 'Administrador General',
            'document_type' => '1',
            'document_number' => '00000001',
            'mobile' => '999111222',
            'email' => 'admin@estudiojuridico.com',
        ]);


        /*
        RECEPCIONISTA
        */

        $recepcionUser = User::create([
            'name' => 'Recepcionista',
            'email' => 'recepcion@estudiojuridico.com',
            'password' => Hash::make('123456'),
        ]);

        $recepcionUser->assignRole('Recepcionista');

        Employee::create([
            'establishment_id' => 1,
            'user_id' => $recepcionUser->id,
            'full_name' => 'Recepcionista Principal',
            'document_type' => '1',
            'document_number' => '00000002',
            'mobile' => '999111333',
            'email' => 'recepcion@estudiojuridico.com',
        ]);


        /*
        ABOGADO
        */

        $lawyerUser = User::create([
            'name' => 'Abogado',
            'email' => 'abogado@estudiojuridico.com',
            'password' => Hash::make('123456'),
        ]);

        $lawyerUser->assignRole('Abogado');

        Employee::create([
            'establishment_id' => 1,
            'user_id' => $lawyerUser->id,
            'full_name' => 'Abogado Principal',
            'document_type' => '1',
            'document_number' => '00000003',
            'mobile' => '999111444',
            'email' => 'abogado@estudiojuridico.com',
        ]);

    }
}
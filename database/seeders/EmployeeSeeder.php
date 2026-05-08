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
            'establishment_id' => 1,
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
            'establishment_id' => 1,
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
        ABOGADOS
        */

        $lawyerUser = User::create([
            'establishment_id' => 1,
            'name' => 'Abogado 1',
            'email' => 'abogado1@estudiojuridico.com',
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

        $lawyerUser = User::create([
            'establishment_id' => 1,
            'name' => 'Abogado 2',
            'email' => 'abogado2@estudiojuridico.com',
            'password' => Hash::make('123456'),
        ]);

        $lawyerUser->assignRole('Abogado');

        $lawyerUser = User::create([
            'establishment_id' => 1,
            'name' => 'Abogado 3',
            'email' => 'abogado3@estudiojuridico.com',
            'password' => Hash::make('123456'),
        ]);

        $lawyerUser->assignRole('Abogado');

    }
}
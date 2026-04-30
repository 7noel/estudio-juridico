<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {

        $admin = User::create([
            'name' => 'Administrador',
            'email' => 'admin@estudiojuridico.com',
            'password' => Hash::make('12345678'),
            'establishment_id' => 1,
        ]);

        $admin->assignRole('Administrador');


        $recepcion = User::create([
            'name' => 'Recepcionista',
            'email' => 'recepcion@estudiojuridico.com',
            'password' => Hash::make('12345678'),
            'establishment_id' => 1,
        ]);

        $recepcion->assignRole('Recepcionista');


        $abogado = User::create([
            'name' => 'Abogado',
            'email' => 'abogado@estudiojuridico.com',
            'password' => Hash::make('12345678'),
            'establishment_id' => 1,
        ]);

        $abogado->assignRole('Abogado');

    }
}
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {

        /*
        |--------------------------------------------------------------------------
        | PERMISOS POR MODULO
        |--------------------------------------------------------------------------
        */

        $permissions = [

            // CLIENTES
            'view clients',
            'create clients',
            'edit clients',
            'delete clients',

            // CONSULTAS
            'view consultations',
            'create consultations',
            'edit consultations',
            'delete consultations',

            // CASOS
            'view cases',
            'create cases',
            'edit cases',
            'delete cases',

            // AGENDA
            'view agenda',
            'create agenda',
            'edit agenda',
            'delete agenda',

            // ROLES
            'view roles',
            'create roles',
            'edit roles',
            'delete roles',
            
            // Permisos
            'view permissions',
            'create permissions',
            'edit permissions',
            'delete permissions',

            // USUARIOS
            'view users',
            'create users',
            'edit users',
            'delete users',

            // ESTABLECIMIENTOS
            'view establishments',
            'create establishments',
            'edit establishments',
            'delete establishments',

        ];

        foreach ($permissions as $permission) {

            Permission::firstOrCreate([
                'name' => $permission
            ]);

        }

        /*
        |--------------------------------------------------------------------------
        | ROLES
        |--------------------------------------------------------------------------
        */

        $admin = Role::firstOrCreate([
            'name' => 'Administrador'
        ]);

        $recep = Role::firstOrCreate([
            'name' => 'Recepcionista'
        ]);

        $abogado = Role::firstOrCreate([
            'name' => 'Abogado'
        ]);

        /*
        |--------------------------------------------------------------------------
        | ASIGNAR PERMISOS A ROLES
        |--------------------------------------------------------------------------
        */

        // ADMIN → TODOS

        $admin->syncPermissions(
            Permission::all()
        );

        // RECEPCIONISTA

        $recep->syncPermissions([

            'view clients',
            'create clients',

            'view consultations',
            'create consultations',

            'view agenda',
            'create agenda',

        ]);

        // ABOGADO

        $abogado->syncPermissions([

            'view clients',

            'view consultations',

            'view cases',
            'edit cases',

            'view agenda',

        ]);

    }
}
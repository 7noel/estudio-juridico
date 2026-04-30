<?php

namespace App\Policies;

use App\Models\User;
use App\Models\CaseFile;

class CaseFilePolicy
{
    /*
    |--------------------------------------------------------------------------
    | Ver listado
    |--------------------------------------------------------------------------
    */

    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole([
            'Administrador',
            'Recepcionista',
            'Abogado'
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Ver caso
    |--------------------------------------------------------------------------
    */

    public function view(User $user, CaseFile $case): bool
    {
        if ($user->hasRole('Administrador')) {

            return true;

        }

        $employee = $user->employee;

        if (!$employee) {
            return false;
        }

        if ($user->hasRole('Abogado')) {

            return $case->lawyer_id == $employee->id;

        }

        if ($user->hasRole('Recepcionista')) {

            return $case->establishment_id
                == $employee->establishment_id;

        }

        return false;
    }

    /*
    |--------------------------------------------------------------------------
    | Crear caso
    |--------------------------------------------------------------------------
    */

    public function create(User $user): bool
    {
        return $user->hasAnyRole([
            'Administrador',
            'Recepcionista'
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Actualizar caso
    |--------------------------------------------------------------------------
    */

    public function update(User $user, CaseFile $case): bool
    {
        if ($user->hasRole('Administrador')) {

            return true;

        }

        $employee = $user->employee;

        if (!$employee) {
            return false;
        }

        if ($user->hasRole('Abogado')) {

            return $case->lawyer_id == $employee->id;

        }

        if ($user->hasRole('Recepcionista')) {

            return $case->establishment_id
                == $employee->establishment_id;

        }

        return false;
    }

    /*
    |--------------------------------------------------------------------------
    | Eliminar caso
    |--------------------------------------------------------------------------
    */

    public function delete(User $user, CaseFile $case): bool
    {
        return $user->hasRole('Administrador');
    }

}
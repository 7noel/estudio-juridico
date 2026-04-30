<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Consultation;

class ConsultationPolicy
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
    | Ver consulta
    |--------------------------------------------------------------------------
    */

    public function view(User $user, Consultation $consultation): bool
    {
        if ($user->hasRole('Administrador')) {

            return true;

        }

        $employee = $user->employee;

        if (!$employee) {
            return false;
        }

        if ($user->hasRole('Abogado')) {

            return $consultation->lawyer_id
                == $employee->id;

        }

        if ($user->hasRole('Recepcionista')) {

            return $consultation->establishment_id
                == $employee->establishment_id;

        }

        return false;
    }

    /*
    |--------------------------------------------------------------------------
    | Crear consulta
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
    | Actualizar consulta
    |--------------------------------------------------------------------------
    */

    public function update(User $user, Consultation $consultation): bool
    {
        if ($user->hasRole('Administrador')) {

            return true;

        }

        $employee = $user->employee;

        if (!$employee) {
            return false;
        }

        if ($user->hasRole('Recepcionista')) {

            return $consultation->establishment_id
                == $employee->establishment_id;

        }

        if ($user->hasRole('Abogado')) {

            return $consultation->lawyer_id
                == $employee->id;

        }

        return false;
    }

    /*
    |--------------------------------------------------------------------------
    | Eliminar consulta
    |--------------------------------------------------------------------------
    */

    public function delete(User $user, Consultation $consultation): bool
    {
        return $user->hasRole('Administrador');
    }

}
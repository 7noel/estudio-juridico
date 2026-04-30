<?php

namespace App\Policies;

use App\Models\LegalActivity;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class LegalActivityPolicy
{
    public function view(User $user, LegalActivity $activity): bool
    {
        if ($user->hasRole('Administrador')) {
            return true;
        }

        $employee = $user->employee;

        if ($user->hasRole('Abogado')) {

            return $activity->case->lawyer_id
                == $employee->id;

        }

        return $activity->case->establishment_id
            == $employee->establishment_id;
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole([
            'Administrador',
            'Abogado'
        ]);
    }
}

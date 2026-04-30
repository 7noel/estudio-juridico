<?php

namespace App\Policies;

use App\Models\Communication;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class CommunicationPolicy
{
    public function view(User $user, Communication $communication): bool
    {
        if ($user->hasRole('Administrador')) {
            return true;
        }

        $employee = $user->employee;

        if ($communication->case_id) {

            if ($user->hasRole('Abogado')) {

                return $communication->case->lawyer_id
                    == $employee->id;

            }

            return $communication->case->establishment_id
                == $employee->establishment_id;
        }

        return true;
    }
}

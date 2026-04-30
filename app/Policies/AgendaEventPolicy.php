<?php

namespace App\Policies;

use App\Models\AgendaEvent;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class AgendaEventPolicy
{
    public function view(User $user, AgendaEvent $event): bool
    {
        if ($user->hasRole('Administrador')) {
            return true;
        }

        $employee = $user->employee;

        if ($user->hasRole('Abogado')) {

            return $event->case->lawyer_id
                == $employee->id;

        }

        return $event->case->establishment_id
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

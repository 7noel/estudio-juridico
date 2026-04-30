<?php

namespace App\Policies;

use App\Models\Document;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class DocumentPolicy
{
    public function view(User $user, Document $document): bool
    {
        if ($user->hasRole('Administrador')) {
            return true;
        }

        $employee = $user->employee;

        if ($user->hasRole('Abogado')) {

            return $document->case->lawyer_id
                == $employee->id;

        }

        if ($user->hasRole('Recepcionista')) {

            return $document->case->establishment_id
                == $employee->establishment_id;

        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole([
            'Administrador',
            'Abogado'
        ]);
    }

    public function delete(User $user, Document $document): bool
    {
        return $user->hasRole('Administrador');
    }
}

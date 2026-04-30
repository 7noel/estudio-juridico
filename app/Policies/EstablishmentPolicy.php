<?php

namespace App\Policies;

use App\Models\Establishment;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class EstablishmentPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole('Administrador');
    }

    public function create(User $user): bool
    {
        return $user->hasRole('Administrador');
    }

    public function update(User $user): bool
    {
        return $user->hasRole('Administrador');
    }

    public function delete(User $user): bool
    {
        return $user->hasRole('Administrador');
    }
}

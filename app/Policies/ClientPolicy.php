<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Client;

class ClientPolicy
{

    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Client $client): bool
    {
        if ($user->hasRole('Administrador')) {
            return true;
        }

        return true;
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole([
            'Administrador',
            'Recepcionista'
        ]);
    }

    public function update(User $user, Client $client): bool
    {
        return $user->hasAnyRole([
            'Administrador',
            'Recepcionista'
        ]);
    }

    public function delete(User $user, Client $client): bool
    {
        return $user->hasRole('Administrador');
    }

}
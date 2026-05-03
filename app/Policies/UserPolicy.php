<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{

    public function viewAny(User $authUser): bool
    {
        return true;
    }

    public function view(User $authUser, User $user): bool
    {
        return true;
    }

    public function create(User $authUser): bool
    {
        return true;
    }

    public function update(
        User $authUser,
        User $user
    ): bool
    {

        // Puede editarse a sí mismo
        if ($authUser->id === $user->id) {
            return true;
        }

        // Administrador puede editar cualquiera
        return $authUser
            ->hasRole('Administrador');

    }

    public function delete(
        User $authUser,
        User $user
    ): bool
    {

        // No puede eliminarse a sí mismo
        if ($authUser->id === $user->id) {
            return false;
        }

        return $authUser
            ->hasRole('Administrador');

    }

}
<?php

namespace App\Policies;

use App\Models\LegalSpecialty;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class LegalSpecialtyPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny($user)
    {
        return $user->hasAnyRole(['Administrador','Recepcionista']);
    }


    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, LegalSpecialty $legalSpecialty): bool
    {
        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create($user)
    {
        return $user->hasAnyRole(['Administrador','Recepcionista']);
    }

    public function update($user)
    {
        return $user->hasAnyRole(['Administrador','Recepcionista']);
    }

    public function delete($user)
    {
        return $user->hasRole('Administrador');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, LegalSpecialty $legalSpecialty): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, LegalSpecialty $legalSpecialty): bool
    {
        return false;
    }
}

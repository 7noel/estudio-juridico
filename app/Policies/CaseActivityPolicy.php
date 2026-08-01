<?php

namespace App\Policies;

use App\Models\CaseActivity;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class CaseActivityPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, CaseActivity $caseActivity): bool
    {
        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasAnyRole(['Administrador', 'Abogado']);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, CaseActivity $activity)
    {
        return $user->hasRole('Administrador')
            || $activity->user_id === $user->id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, CaseActivity $activity)
    {
        return $user->hasRole('Administrador') ||
               $activity->case->lawyer_id == $user->id ||
               $activity->case->user_id == $user->id ||
               $activity->user_id == $user->id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, CaseActivity $caseActivity): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, CaseActivity $caseActivity): bool
    {
        return false;
    }
}

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
        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, CaseActivity $activity)
    {
        return $user->hasRole('Administrador')
            || $activity->created_by === $user->id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, CaseActivity $activity)
    {
        return $user->hasRole('Administrador') ||
               $activity->case->created_by == $user->id ||
               $activity->created_by == $user->id;
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

<?php

namespace App\Policies;

use App\Models\User;
use Spatie\Permission\Models\Role;

class RolePolicy
{
    public function viewAny(User $user)
    {
        return true;
        return $user->can('view roles');
    }

    public function view(User $user)
    {
        return true;
        return $user->can('view roles');
    }

    public function create(User $user)
    {
        return true;
        return $user->can('create roles');
    }

    public function update(User $user, Role $role)
    {
        return true;
        return $user->can('edit roles');
    }

    public function delete(User $user, Role $role)
    {
        return true;
        return $user->can('delete roles');
    }
}
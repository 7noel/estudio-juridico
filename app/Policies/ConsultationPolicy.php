<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Consultation;

class ConsultationPolicy
{
    public function viewAny(User $user)
    {
        return true;
    }

    public function view(User $user, Consultation $consultation)
    {
        return $user->hasRole('Administrador') ||
            $consultation->establishment_id === $user->establishment_id;
    }

    public function create(User $user)
    {
        return $user->hasAnyRole(['Administrador', 'Recepcionista']);
    }

    public function update(User $user, Consultation $consultation)
    {
        return $user->hasRole('Administrador') ||
            $consultation->establishment_id === $user->establishment_id;
    }

    public function delete(User $user, Consultation $consultation)
    {
        return $user->hasRole('Administrador');
    }
}
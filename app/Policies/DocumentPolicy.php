<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Document;

class DocumentPolicy
{
    public function view(User $user, Document $doc)
    {
        return $user->hasRole('Administrador') ||
            $doc->case->establishment_id == $user->employee->establishment_id;
    }

    public function create(User $user)
    {
        return $user->hasAnyRole(['Administrador', 'Abogado']);
    }

    public function update(User $user, Document $doc)
    {
        return $user->hasRole('Administrador') ||
            $doc->uploaded_by == $user->id;
    }

    public function delete(User $user, Document $doc)
    {
        return $this->update($user, $doc);
    }
}
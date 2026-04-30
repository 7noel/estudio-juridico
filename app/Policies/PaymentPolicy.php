<?php

namespace App\Policies;

use App\Models\Payment;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class PaymentPolicy
{
    public function view(User $user, Payment $payment): bool
    {
        if ($user->hasRole('Administrador')) {
            return true;
        }

        $employee = $user->employee;

        return $payment->consultation->establishment_id
            == $employee->establishment_id;
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole([
            'Administrador',
            'Recepcionista'
        ]);
    }

    public function delete(User $user): bool
    {
        return $user->hasRole('Administrador');
    }
}

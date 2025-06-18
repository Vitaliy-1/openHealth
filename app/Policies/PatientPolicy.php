<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\User;

class PatientPolicy
{
    /**
     * Determine whether the user can create an application.
     *
     * @param  User  $user
     * @return bool
     */
    public function createApplication(User $user): bool
    {
        return $user->hasAnyRole(['DOCTOR', 'RECEPTIONIST']);
    }

    /**
     * Determine whether the user can create a patient.
     *
     * @param  User  $user
     * @return bool
     */
    public function createPerson(User $user): bool
    {
        return $user->hasRole('DOCTOR');
    }
}

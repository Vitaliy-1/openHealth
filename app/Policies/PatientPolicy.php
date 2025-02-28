<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\User;

class PatientPolicy
{
    /**
     * Determine whether the user can create application.
     *
     * @param  User  $user
     * @return bool
     */
    public function createApplication(User $user): bool
    {
        return $user->hasRole(['DOCTOR', 'RECEPTIONIST']);
    }

    /**
     * Determine whether the user can create patient.
     *
     * @param  User  $user
     * @return bool
     */
    public function createPerson(User $user): bool
    {
        return $user->hasRole('DOCTOR');
    }
}

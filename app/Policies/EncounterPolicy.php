<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\User;

class EncounterPolicy
{
    /**
     * Determine whether the user can create patient.
     *
     * @param  User  $user
     * @return bool
     */
    public function store(User $user): bool
    {
        return $user->hasPermissionTo('encounter:write', 'web');
    }
}

<?php

namespace App\Repositories;

use App\Models\LegalEntity;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserRepository
{
    /**
     * @param $email
     * @param $role
     * @return User|null
     */

    public function createIfNotExist($party, $role, LegalEntity $legalEntity): User|null
    {
        if (isset($party['email']) && !empty($party['email'])) {
            // Create User if not exists
            $user = User::firstOrCreate(
                [
                    'email' => $party['email']
                ],
                [
                    'tax_id'   => $party['tax_id'] ?? '',
                    'password' => Hash::make(\Illuminate\Support\Str::random(8))
                ]
            );
            // Set Role
            $user->assignRole($role);
            $user->legalEntity()->associate($legalEntity);
            $user->save();
            return $user;
        }

        return null;
    }
}

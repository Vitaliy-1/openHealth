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
     * @return User
     */

    public function createIfNotExist($party, $role, LegalEntity $legalEntity): User
    {
        // Create User if not exists
        $user = User::firstOrCreate(
            [
                'email' => $party['email']
            ],
            [
                'tax_id' => $party['tax_id'] ?? '',
                'password' => Hash::make(\Illuminate\Support\Str::random(8))
            ]
        );
        // Set Role
        $user->assignRole($role);
        $user->legalEntity()->associate($legalEntity);
        $user->save();
        return $user;
    }
}

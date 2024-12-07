<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserRepository
{
    /**
     * @param $email
     * @param $role
     * @return User
     */

    public function createIfNotExist($email, $role): User
    {
        // Create User if not exists
        $user = User::firstOrCreate(
            [
                'email' => $email
            ],
            [
                'password' => Hash::make(\Illuminate\Support\Str::random(8))
            ]
        );

        // Set Role
        $user->assignRole($role);

        return $user;
    }
}

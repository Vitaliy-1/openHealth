<?php

namespace App\Auth\EHealth\Providers;

use App\Models\User;
use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Contracts\Auth\Authenticatable;

class EHealthUserProvider extends EloquentUserProvider
{
    /**
     * Retrieve a user by their unique identifier.
     * Here an user's uuid acts as an identifier.
     *
     * @param  mixed  $identifier
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function retrieveByid($identifier):?User
    {
        return User::where('uuid', $identifier)->first();
    }

    /**
     * Commonly, retrieve a user by their unique identifier and "remember me" token.
     * This functionality is not used here.
     *
     * @param  mixed  $identifier
     * @param  string  $token
     *
     * @return null
     */
    public function retrieveByToken($identifier, $token): null
    {
        return null;
    }

    /**
     * Commonly, update the "remember me" token for the given user in storage.
     * This functionality is not used here.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @param  string  $token
     *
     * @return void
     */
    public function updateRememberToken(Authenticatable $user, $token)
    {
        // Nothing to do here. Just override.
    }

    /**
     * Retrieve a user by the given credentials.
     * TODO: check if it really need here
     *
     * @param  array  $credentials
     *
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function retrieveByCredentials(array $credentials)
    {
        if (empty($credentials)) {
            return null;
        }

        return User::where('email', $credentials['email'] ?? null)?->first();
    }

    /**
     * Commonly, validate a user against the given credentials.
     * We returned true as user hasn been already authenticvated by ESOZ (we're thrust them!)
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @param  array  $credentials
     *
     * @return bool
     */
    public function validateCredentials(Authenticatable $user, array $credentials)
    {
        return true;
    }

}

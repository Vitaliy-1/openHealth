<?php

namespace App\Auth\EHealth\Guards;

use Illuminate\Http\Request;
use Illuminate\Auth\SessionGuard;
use App\Auth\EHealth\Services\TokenStorage;
use Illuminate\Support\Facades\Log;
use Illuminate\Contracts\Session\Session;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Contracts\Auth\Authenticatable;

class EHealthGuard extends SessionGuard
{
    /**
     * @var TokenStorage
     */
    protected TokenStorage $tokenStorage;

    public function __construct(string $name, UserProvider $provider, Session $session, Request $request, TokenStorage $tokenStorage)
    {
        parent::__construct($name, $provider, $session, $request ?? request());

        $this->tokenStorage = $tokenStorage;
    }

    /**
     * Get the currently authenticated user.
     * Depends on it's UUID
     *
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function user()
    {
        if(!empty($this->user)) {
            return $this->user;
        }

        if ($this->user && !$this->tokenStorage->hasBearerToken()) {
            $this->logout();

            return null;
        }

        $uuid = $this->session->get($this->getName());

        if ($uuid) {
            $this->user = $this->provider->retrieveById($uuid);
        }

        return $this->user;
    }

    public function isLoggedIn(): bool
    {
        return $this->tokenStorage->hasBearerToken() && $this->tokenStorage->getExpiresAt();
    }

    public function getUserUUID(Authenticatable $user): ?string
    {
        return $user->uuid;
    }

    /**
     * Log a user into the application.
     * Add additional checks for Bearer token presents
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @param  bool  $remember
     * @return void
     */
    public function login(Authenticatable $user, $remember = false)
    {
        if (! $this->tokenStorage->hasBearerToken()) {
            Log::error(__('Bearer token missing in session', [], 'en'));

            throw new \Exception('Bearer token missing in session');
        }

        $this->updateSession($this->getUserUUID($user));

        $this->fireLoginEvent($user, $remember);

        $this->setUser($user);
    }

    public function logout()
    {
        parent::logout();

        $this->tokenStorage->clear();
    }
}

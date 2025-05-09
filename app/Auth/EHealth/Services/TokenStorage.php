<?php

namespace App\Auth\EHealth\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Session;
use App\Classes\eHealth\Request as eHealthRequest;

class TokenStorage
{
    protected string $tokenKey;

    protected string $expiresAtKey = 'auth_token_expires_at';

    protected string $refreshTokenKey = 'refresh_token';

    public function __construct()
    {
        $this->tokenKey = config('ehealth.api.oauth.bearer_token');
    }

    /**
     * Save all data concerns to teh Bearer token
     *
     * @param array $tokenData
     *
     * @return void
     */
    public function store(array $tokenData): void
    {
        Session::put($this->tokenKey, $tokenData['value']);
        Session::put($this->expiresAtKey, Carbon::createFromTimestamp($tokenData['expires_at']));
        Session::put($this->refreshTokenKey, $tokenData['details']['refresh_token']);
        Session::save();
    }

    public function hasBearerToken(): bool
    {
        return Session::has($this->tokenKey);
    }

    public function getBearerToken(): ?string
    {
        return Session::get($this->tokenKey);
    }

    public function getRefreshToken(): ?string
    {
        return Session::get($this->refreshTokenKey);
    }

    public function getExpiresAt(): ?Carbon
    {
        $expiresAt = Session::get($this->expiresAtKey);

        return $expiresAt ? Carbon::parse($expiresAt) : null;
    }

    /**
     * Delete all the data concerns to the Bearer token
     *
     * @return void
     */
    public function clear(): void
    {
        Session::forget([
            $this->tokenKey,
            $this->expiresAtKey,
            $this->refreshTokenKey
        ]);

        Session::save();
    }

    public function refreshBearerToken(): ?array
    {
        $user = auth('ehealth')->user();

        if (! $user) {
            return null;
        }

        $data = [
            'token' => [
                'client_id'     => $this->user->legalEntity->client_id ?? '',
                'client_secret' => $this->user->legalEntity->client_secret ?? '',
                'grant_type'    => 'refresh_token',
                'refresh_token' => $this->getRefreshToken(),
            ]
        ];

        $request = new EhealthRequest('POST', config('ehealth.api.oauth.tokens'), $data, false)->sendRequest();

        $this->store($request);

        return $request;
    }
}

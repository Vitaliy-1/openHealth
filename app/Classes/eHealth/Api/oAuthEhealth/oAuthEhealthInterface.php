<?php

namespace App\Classes\eHealth\Api\oAuthEhealth;


interface oAuthEhealthInterface
{
    public function getToken(): string;

    public static function getUser(): array;

    public function login($user) : void;

    public function getApikey(): string;

    public function refreshAuthToken(string $refreshToken): array;
}

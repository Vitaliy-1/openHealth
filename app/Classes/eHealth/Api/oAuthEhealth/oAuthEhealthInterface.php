<?php

namespace App\Classes\eHealth\Api\oAuthEhealth;

use App\Models\User;

interface oAuthEhealthInterface
{
    public function getToken(): string;

    public static function getUser(): array;

    public function login($user) : void;

    public function getApikey(): string;
}

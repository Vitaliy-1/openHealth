<?php

namespace App\Classes\eHealth\Api\oAuthEhealth;

interface oAuthEhealthInterface
{
    public function getToken(): string;

    public function getUser(): array;

    public function login(array $email = []): void;



}
